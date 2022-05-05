<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Scanner Demo</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset ("img/logo.png") }}" />
</head>
<body>
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

<div style="display:none">
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
    import QrScanner from "{{ asset('js/qr-scanner.min.js') }}";

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

    function setResult(label, result) {
        console.log(result.data);
        label.textContent = result.data;
        camQrResultTimestamp.textContent = new Date().toString();
        label.style.color = 'teal';
        clearTimeout(label.highlightTimeout);
        label.highlightTimeout = setTimeout(() => label.style.color = 'inherit', 100);
    }

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

    #video-container {
      line-height: 0;
			position: fixed;
			right: 0;
			bottom: 0;
			min-width: 100%;
			min-height: 100%;
    }

		#qr-video {
			position: fixed;
			right: 0;
			bottom: 0;
			min-width: 100%;
			min-height: 100%;
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