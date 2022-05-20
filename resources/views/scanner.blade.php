<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Scanner Demo</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{env('APP_URL')}}/img/logo.png" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <h1 style="color: #FF0000">THIS IS JUST FOR READ TEST</h1>
<div id="video-container" class="example-style-2">
	<video id="qr-video"></video>
	{{-- <div class="scan-region-highlight" style="position: absolute; pointer-events: none; transform: scaleX(-1); width: 424px; height: 424px; top: 106px; left: 161.5px;">
		<svg class="scan-region-highlight-svg" viewBox="0 0 238 238" 
					preserveAspectRatio="none" style="position:absolute;width:100%;height:100%;left:0;top:0;fill:none;stroke:#e9b213;stroke-width:4;stroke-linecap:round;stroke-linejoin:round">
					<path d="M31 2H10a8 8 0 0 0-8 8v21M207 2h21a8 8 0 0 1 8 8v21m0 176v21a8 8 0 0 1-8 8h-21m-176 0H10a8 8 0 0 1-8-8v-21">
						</path>
		</svg>
		<svg class="code-outline-highlight" preserveAspectRatio="none" style="width: 100%; height: 100%; fill: none; stroke: rgb(233, 178, 19); stroke-width: 5; stroke-dasharray: 25; stroke-linecap: round; stroke-linejoin: round; display: none;" viewBox="320 96 384 384">
			<polygon points="363.2454716011722,376.0824213131417 360.57384500928003,371.25682390930376 640.8926115838651,444.98156387900923 667.2474983482225,442.6679746153717">
			</polygon>
		</svg>
	</div> --}}
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


    function beep_true() {
        var snd = new Audio("data:audio/mpeg;base64,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");  
        snd.play();
    }

    var last_check = "";

    function setResult(label, result) {
        last_check = result.data;
        label.textContent = result.data;
        camQrResultTimestamp.textContent = new Date().toString();
        label.style.color = 'teal';
        clearTimeout(label.highlightTimeout);
        label.highlightTimeout = setTimeout(() => label.style.color = 'inherit', 100);
    }

    function checkQR() {
        if (last_check != "") {
            beep_true();
            setTimeout(function () {
                last_check = "";
                checkQR();
            }, 2000)
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
    div {
        margin-bottom: 16px;
    }

    #video-container {
        line-height: 0;
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