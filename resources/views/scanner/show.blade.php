<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Guest Book</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{env('APP_URL')}}/img/logo.png" />
    <link href="{{env('APP_URL')}}/css/scanner.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>

<div class="fill">
    <img src="{{ $scanner->backgroundImageLink() }}" alt="bg-img" />
    <div class="welcoming-message show" id="default-message">
        <h1 class="wed-h1">Hagai & Putri</h1>
        <h3 class="wed-h3">Bali, 12.14.2024</h2>
    </div>
    <div class="welcoming-message" id="welcome-message">
        <h1 class="wed-h1">Welcome, <br><span id="name">Hagai Raja Sinulingga!</span></h1>
        <h3 class="wed-h3"><span id="position">Keluarga Pria</span> | <strong>Seat <span id="seat">C16</span> </strong> | <span id="rsvp_count">2</span> pax</h2>
    </div>
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
<script>
    var last_check = "";

    function switchDefault() {
        $('#welcome-message').removeClass("show");
        $('#default-message').addClass("show");
    }

    function checkQR() {
        $.ajax({
            url: "{{env('APP_URL')}}/guests/check",
        }).done(function(data) {
            data = JSON.parse(data);
            var startDate = new Date(data.attended_at + "+08")
            var endDate   = new Date();
            var seconds = (endDate.getTime() - startDate.getTime()) / 1000;
            if (seconds <= 5) {
                console.log(seconds);
                $('#name').html(data.name);
                $('#position').html(data.position);
                $('#rsvp_count').html(data.rsvp_count);
                $('#seat').html(data.seat);

                $('#default-message').removeClass("show");
                $('#welcome-message').addClass("show");
                setTimeout(function () {
                    switchDefault();
                }, 5000)
                setTimeout(function () {
                    checkQR();
                }, 5000)
            }
            else {
                setTimeout(function () {
                    checkQR();
                }, 500)
            }
        });
    }
    checkQR();
</script>

</body>
</html>