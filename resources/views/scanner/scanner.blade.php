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