<?php
require_once('options.php');
?>
<html>  
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- set the viewport width and initial-scale on mobile devices -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kaltura REACH Wizard</title>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/all.css">
<script src="js/modernizr.custom.84803.js"></script>
<link href="lib/chosen/chosen.css" rel="stylesheet" />
<link rel="stylesheet" href="lib/colorbox/example4/colorbox.css" />
<link href="lib/jQueryUI/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css" />
<!-- Script Includes -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="lib/chosen/chosen.jquery.js"></script>
<script src="lib/jquery.json-2.3.min.js"></script>
<script src="lib/colorbox/colorbox/jquery.colorbox.js"></script>
<script src="lib/jquery.numeric.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>	
<script>
	var kalturaSession = "";
	var partnerId = 0;
	
	function partnerLogin(response) {
		$('#email').attr("readonly", "readonly");
		$('#password').attr("readonly", "readonly");
		$('#loginButton').hide();
		$('#loginFooter').hide();
		$.ajax({
			type: "POST",
			url: "partnerSelect.php",
			data: {response: response}
		}).done(function(msg) {
			$('#description').text("Choose the partner ID you want to use.");
			$('#partnerLogin').html(msg);
			$('#loginForm').animate({height: "700px", marginTop: "-216px"}, 400, function() {
				$('#partnerLogin').slideDown();
			});
			$("#submitButton").attr("onclick", "partnerSubmit()");
			$('#email').keyup(function(event) {
				if(event.which == 13)
					partnerSubmit();
			});
			$('#password').keyup(function(event) {
				if(event.which == 13)
					partnerSubmit();
			});
			jQuery('.czntags').chosen({search_contains: true});
		});
	}
	function partnerSubmit() {
		$('#sumbitPartner').hide();
		$('#partnerLoader').show();
		var login_type=document.getElementById("login_type").value;
		$.ajax({
			type: "POST",
			url: "getSession.php",
			data: {email: $('#email').val(), partnerId: $('#partnerChoice').val(), password: $('#password').val(), login_type: login_type}
		}).done(function(msg) {
			$('#partnerLoader').hide();
			if(msg === "loginfail") {
				document.getElementById("note").innerHTML="Invalid username or password.";
				$('#loginButton').show();
			}
			else if(msg === 'idfail') {
				document.getElementById("note").innerHTML="Invalid partner ID.";
				$('#loginButton').show();
			}else {
				response = $.evalJSON(msg);
				kalturaSession = response[1];
				document.getElementById("ks").value = kalturaSession;
				document.getElementById("partner_id").value = response[2];
				partnerId = $('#partnerChoice').val();
				$('#mainf').show();

				$('#userLogin').hide();
				$('#loginButton').hide();
				$('#loginFooter').hide();
				$('#loginForm').animate({width: "1000px", marginTop: "-130px"}, 400);
				$('#page').slideDown();
			}
		});
	}
	function loginSubmit() {
		$('#loginButton').hide();
		$('#loginLoader').show();
		var login_type=document.getElementById("login_type").value;
		if (login_type == 'email_passwd'){
			var partnerId=0;
		}else{
			var partnerId=$('#email').val();
		}
		$.ajax({
			type: "POST",
			url: "getSession.php",
			data: {email: $('#email').val(), partnerId: partnerId, password: $('#password').val(), login_type: login_type}
		}).done(function(msg) {
			$('#loginLoader').hide();
			if(msg == "loginfail") {
				alert(login_type);
				document.getElementById("note").innerHTML="Invalid username or password.";
				$('#loginButton').show();
			}
			else {
				response = $.evalJSON(msg);
				// if we only have one partner
				if(response[0] == 1) {
					kalturaSession = response[1];
					partnerId = response[2];
					$('#userLogin').hide();
					$('#loginButton').hide();
					$('#loginFooter').hide();
					$('#loginForm').animate({height: "500px", marginTop: "-130px"}, 400);
					document.getElementById("ks").value = kalturaSession;
					document.getElementById("partner_id").value = response[2];
					$('#mainf').show();
					$('#page').slideDown();
				}else{
					partnerLogin(response);
				}
			}
		});
	}
	$(document).ready(function(){
	    $('#login_type').change(
		function () {
		    var login_type = $('option:selected', this).val();
		    if (login_type == "email_passwd") {
			$('#username').text("Email Address: *");
			$('#password_label').text("Password: *");
		    } else if (login_type == "partner_id_secret") {
			$('#username').html("Partner ID: *");
			$('#password_label').html("Admin Secret: *");
		    }
		});
	 });  
</script>
</head>
<body class="page">
			<div id="mainf" style="display: none;">
			<form method="post" action="configure.php">
			<input type="hidden" id="ks" name="ks">
			<input type="hidden" id="partner_id" name="partner_id">
			<h4>This tool helps setup up and configure a new REACH account for KMC based workflow.</h4> 
			<h4>Setup should be according to what was purchased in the contract, any different setting will result in monthly overages.</h4>
			<h4 >For questions, please contact your Kaltura representative or email reach@kaltura.com</a></h4>
			</br>

			<h4 >Chose your desired Transcription turn-around-time (the time from start until captions are available in KMC):</h4>
			</br>
			</br>
			<?php
				foreach ($captions_turn_around_time as $key=>$val){
				   echo "<input type=\"checkbox\" name=\"ctat[]\" value=\"$key\">$val <br>";
				}
			?>
			</br>
				<h4 >Chose the languages spoken in your videos (note the supported services next to each language):</h4>
			</br>
			<?php
				foreach ($captions_lang as $key=>$val){
				   echo "<input type=\"checkbox\" name=\"lang[]\" value=\"$key\">".ucfirst($key). ' - ' . implode(" and ", $val).' <br>';
				}
			?>
			</br>
			<label><h4 >Chose the trigger that will execute transcription of entries:</h4>
			</br>
			<?php
				foreach ($triggers as $key=>$val){
				   echo "  <input type=\"radio\" name=\"trigger\" value=\"$key\" > $val <br>";
				}
			?>
			</br>
			</br>
			<h4 >Should entries be machine-transcribed automatically upon upload?</h4>
			</br>
			<input type="radio" name="auto_transcribe" value="1">Yes <br>
			<input type="radio" name="auto_transcribe" value="0">No <br>
			</br>
			<input type="submit" id=finButton class="btn btn-default" value="Next" >
			</form>
			</div>
		        <div id="wrapper">
                        <div class="w1">
			<main id="main">
				<div class="container">
				<div role="tabrole">
				<form method="post" id="loginForm" action="javascript:loginSubmit();" >
				<div class="row">
				<div class="col-sm-6">

					<h1>REACH Configuration Wizard</h1>
					<p id="description" style="padding-bottom:10px;">Login to your Kaltura account to proceed</p>
				<div id="userLogin">
				<fieldset>
				<div class="form-group valid-row">
				<label for="login_type">login type:<span class="required"> *</span></label>

				<select id="login_type" name="email_passwd" class="form-control select">
					 <option value="email_passwd">Email and Password</option>
					 <option value="partner_id_secret">Partner ID and Admin Secret</option>
				</select>
				</div>
					<div class="form-group valid-row">
						<label for="username" id="username">Email Address: <span class="required">*</span></label>
						<input id="email" name="email"  type="text" class="form-control required">
					</div>
					<div class="form-group valid-row">
						<label for="password" id="password_label">Password: <span class="required">*</span></label>
						<input type="password" id="password" name="password" class="form-control required">
					</div>
					<div id="partnerLogin" style="display: none;"></div>
					<span class="error note" id="note"></span>
					<div class="btn-holder">
					<input type="submit" id=submitButton class="btn btn-default" value="LOGIN">
					</div>
				</fieldset>	
				</div>
				</div>
				<div id="page" class="boxBody" style="display: none;"></div>
				</div>

				</form>
				</div>
				</div>
			</main>
		</div>
		</div>
</body>
</html>
