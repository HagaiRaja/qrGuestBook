<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Scanner Demo</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{env('APP_URL')}}/img/logo.png" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
<div id="video-container" class="example-style-2">
	<video id="qr-video"></video>
</div>

<div style="z-index=100">
	<div>
        <label>
                Highlight Style
                <select id="scan-region-highlight-style-select">
                        <option value="default-style">Default style</option>
                        <option value="example-style-1">Example custom style 1</option>
                        <option selected value="example-style-2">Example custom style 2</option>
                </select>
        </label>
        <label>
                <input id="show-scan-region" type="checkbox">
                Show scan region canvas
        </label>
	</div>
    <button type="button">Beep!</button>
	<div>
			<select id="inversion-mode-select">
					<option value="original">Scan original (dark QR code on bright background)</option>
					<option value="invert">Scan with inverted colors (bright QR code on dark background)</option>
					<option value="both">Scan both</option>
			</select>
			<br>
	</div>
	<b>Device has camera: </b>
	<span id="cam-has-camera"></span>
	<br>
	<div>
			<b>Preferred camera:</b>
			<select id="cam-list">
					<option value="environment" selected>Environment Facing (default)</option>
					<option value="user">User Facing</option>
			</select>
	</div>
	<b>Camera has flash: </b>
	<span id="cam-has-flash"></span>
	<div>
			<button id="flash-toggle">📸 Flash: <span id="flash-state">off</span></button>
	</div>
	<br>
	<b>Detected QR code: </b>
	<span id="cam-qr-result">None</span>
	<br>
	<b>Last detected at: </b>
	<span id="cam-qr-result-timestamp"></span>
	<br>
	<button id="start-button">Start</button>
	<button id="stop-button">Stop</button>
	<hr>

	<h1>Scan from File:</h1>
	<input type="file" id="file-selector">
	<b>Detected QR code: </b>
	<span id="file-qr-result">None</span>
</div>
<!--<script src="../qr-scanner.umd.min.js"></script>-->
<!--<script src="../qr-scanner.legacy.min.js"></script>-->
<script type="module">
    import QrScanner from "{{env('APP_URL')}}/js/qr-scanner.min.js";

    const video = document.getElementById('qr-video');
    const videoContainer = document.getElementById('video-container');
    const camHasCamera = document.getElementById('cam-has-camera');
    const camList = document.getElementById('cam-list');
    const camHasFlash = document.getElementById('cam-has-flash');
    const flashToggle = document.getElementById('flash-toggle');
    const flashState = document.getElementById('flash-state');
    const camQrResult = document.getElementById('cam-qr-result');
    const camQrResultTimestamp = document.getElementById('cam-qr-result-timestamp');
    const fileSelector = document.getElementById('file-selector');
    const fileQrResult = document.getElementById('file-qr-result');

    var last_check = "";

    function setResult(label, result) {
        last_check = result.data;
        label.textContent = result.data;
        camQrResultTimestamp.textContent = new Date().toString();
        label.style.color = 'teal';
        clearTimeout(label.highlightTimeout);
        label.highlightTimeout = setTimeout(() => label.style.color = 'inherit', 100);
    }

    function switchDefault() {
        $('#welcome-message').removeClass("show");
        $('#default-message').addClass("show");
    }

    function checkQR() {
        if (last_check != "") {
            $.ajax({
                url: "{{env('APP_URL')}}/scanners/check/" + last_check,
            }).done(function(data) {
                data = JSON.parse(data);
                if (data.length == 1) {
                    console.log(data, data.length);
                    beep_true();
                    data = data[0];
                    $('#name').html(data.name);
                    $('#position').html(data.position);
                    $('#rsvp_count').html(data.rsvp_count);
                    $('#seat').html(data.seat);

                    $('#default-message').removeClass("show");
                    $('#welcome-message').addClass("show");
                    $('#video-container.example-style-2 .code-outline-highlight')
                        .attr('style', `
                            width: 100%; 
                            height: 100%; 
                            fill: none; 
                            stroke-linecap: round; 
                            stroke-linejoin: round; 
                            display: none;
                            stroke: rgba(63, 255, 24, 0.5) !important;
                            stroke-width: 15 !important;
                            stroke-dasharray: none !important;
                        `);
                    setTimeout(function () {
                        switchDefault();
                    }, 5000)
                }
                else {
                    $('#video-container.example-style-2 .code-outline-highlight')
                        .attr('style', `
                            width: 100%; 
                            height: 100%; 
                            fill: none; 
                            stroke-linecap: round; 
                            stroke-linejoin: round; 
                            display: none;
                            stroke: rgba(255, 63, 24, 0.5) !important;
                            stroke-width: 15 !important;
                            stroke-dasharray: none !important;
                        `);
                    beep_false();
                }
            });
            setTimeout(function () {
                last_check = "";
                checkQR();
            }, 3000)
        }
        else {
            setTimeout(function () {
                checkQR();
            }, 500)
        }
    }
    checkQR();

    // ####### Web Cam Scanning #######

    const scanner = new QrScanner(video, result => setResult(camQrResult, result), {
        onDecodeError: error => {
            camQrResult.textContent = error;
            camQrResult.style.color = 'inherit';
        },
				// overlay: document.getElementById('scan-region-highlight'),
        highlightScanRegion: true,
        highlightCodeOutline: true,
    });

    const updateFlashAvailability = () => {
        scanner.hasFlash().then(hasFlash => {
            camHasFlash.textContent = hasFlash;
            flashToggle.style.display = hasFlash ? 'inline-block' : 'none';
        });
    };

    scanner.start().then(() => {
        updateFlashAvailability();
        // List cameras after the scanner started to avoid listCamera's stream and the scanner's stream being requested
        // at the same time which can result in listCamera's unconstrained stream also being offered to the scanner.
        // Note that we can also start the scanner after listCameras, we just have it this way around in the demo to
        // start the scanner earlier.
        QrScanner.listCameras(true).then(cameras => cameras.forEach(camera => {
            const option = document.createElement('option');
            option.value = camera.id;
            option.text = camera.label;
            camList.add(option);
        }));
    });

    QrScanner.hasCamera().then(hasCamera => camHasCamera.textContent = hasCamera);

    // for debugging
    window.scanner = scanner;

    document.getElementById('scan-region-highlight-style-select').addEventListener('change', (e) => {
        videoContainer.className = e.target.value;
        scanner._updateOverlay(); // reposition the highlight because style 2 sets position: relative
    });

    document.getElementById('show-scan-region').addEventListener('change', (e) => {
        const input = e.target;
        const label = input.parentNode;
        label.parentNode.insertBefore(scanner.$canvas, label.nextSibling);
        scanner.$canvas.style.display = input.checked ? 'block' : 'none';
    });

    document.getElementById('inversion-mode-select').addEventListener('change', event => {
        scanner.setInversionMode(event.target.value);
    });

    camList.addEventListener('change', event => {
        scanner.setCamera(event.target.value).then(updateFlashAvailability);
    });

    flashToggle.addEventListener('click', () => {
        scanner.toggleFlash().then(() => flashState.textContent = scanner.isFlashOn() ? 'on' : 'off');
    });

    // document.getElementById('start-button').addEventListener('click', () => {
    //     scanner.start();
    // });
		scanner.start();

    document.getElementById('stop-button').addEventListener('click', () => {
        scanner.stop();
    });

    // ####### File Scanning #######

    fileSelector.addEventListener('change', event => {
        const file = fileSelector.files[0];
        if (!file) {
            return;
        }
        QrScanner.scanImage(file, { returnDetailedScanResult: true })
            .then(result => setResult(fileQrResult, result))
            .catch(e => setResult(fileQrResult, { data: e || 'No QR code found.' }));
    });

    function beep_true() {
        var snd = new Audio("data:audio/mpeg;base64,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");  
        snd.play();
    }

    function beep_false() {
        var snd = new Audio("data:audio/mpeg;base64,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");  
        snd.play();
    }
</script>

<style>
    body, html {
        height: 100%;
    }
    body {
        margin: 0;
        font-family: "Source Sans Pro", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        text-align: left;
        background-color: #ffffff;
    }

    #video-container.example-style-1 .scan-region-highlight-svg,
    #video-container.example-style-1 .code-outline-highlight {
        stroke: #64a2f3 !important;
    }

    #video-container.example-style-2 {
        position: relative;
        width: max-content;
        height: max-content;
        overflow: hidden;
    }
    #video-container.example-style-2 .scan-region-highlight {
        border-radius: 30px;
        outline: rgba(0, 0, 0, .5) solid 50vmax;
    }
    #video-container.example-style-2 .scan-region-highlight-svg {
        display: none;
    }
    #video-container.example-style-2 .code-outline-highlight {
        stroke: rgba(63, 255, 24, 0.5) !important;
        stroke-width: 15 !important;
        stroke-dasharray: none !important;
    }

    #flash-toggle {
        display: none;
    }

    hr {
        margin-top: 32px;
    }
    input[type="file"] {
        display: block;
        margin-bottom: 16px;
    }
</style>
</body>
</html>