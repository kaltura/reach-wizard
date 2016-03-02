<?php
require_once('options.php');
require_once('headers.inc');
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
			//$('#description').text("Choose the partner ID you want to use.");
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
		var login_type=document.getElementById("login_type").value;
		$.ajax({
			type: "POST",
			url: "getSession.php",
			data: {email: $('#email').val(), partnerId: $('#partnerChoice').val(), password: $('#password').val(), login_type: login_type}
		}).done(function(msg) {
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
				$('#mainf').focus();
				$('#description').hide();
				$('#main').hide();
				$('#userLogin').hide();
				$('#loginButton').hide();
				$('#loginFooter').hide();
				//$('#loginForm').animate({width: "1000px", marginTop: "-130px"}, 400);
				$('#loginForm').hide();
				//$('#page').slideDown();
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
					//$('#loginForm').animate({height: "500px", marginTop: "-130px"}, 400);
					$('#loginForm').hide();
					document.getElementById("ks").value = kalturaSession;
					document.getElementById("partner_id").value = response[2];
					$('#mainf').show();
					//$('#page').slideDown();
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
	<div id="wrapper">
		<div class="w1">
			<?php echo HTML_HEADER;?>
			<div class="content">
				<div class="row">
					<div class="col-md-12">
						<div id="mainf" style="display: none;">
							<form method="post" action="configure.php">
								<input type="hidden" id="ks" name="ks">
								<input type="hidden" id="partner_id" name="partner_id">
								<h1>Kaltura REACH - KMC Workflow setup</h1>
								<p>This tool helps setup up and configure a new REACH account for KMC based workflow.</p>
								<p>Setup should be according to what was purchased in the contract, any different setting will result in monthly overages. <br />
								For questions, please contact your Kaltura representative or email reach@kaltura.com</p>

								<h4>Choose the desired Transcription turn-around-time</h4> 
								<p>(Turn-Around-Time is the time from when the transcription job starts until captions are available in KMC)</p>
								<?php
									foreach ($captions_turn_around_time as $key=>$val){
									   echo "<input type=\"checkbox\" id=\"cboxtat_$key\" name=\"ctat[]\" value=\"$key\"><label for=\"cboxtat_$key\">$val</label><br />";
									}
								?>
								
								<h4>Choose the languages spoken in the videos</h4>
								<p>Please note the supported services next to each language</p>
								<?php
									foreach ($captions_lang as $key=>$val){
									   echo "<input type=\"checkbox\" id=\"cboxlang_$key\" name=\"lang[]\" value=\"$key\"><label for=\"cboxlang_$key\">".ucfirst($key). ' - ' . implode(" and ", $val).'</label><br />';
									}
								?>
								
								<h4>Choose the trigger that will execute transcription of entries:</h4>
								<p>It is highly recommended to use tags as triggers</p>
								<?php
									foreach ($triggers as $key=>$val){
									   echo "  <input type=\"radio\" id=\"radtrig_$key\" name=\"trigger\" value=\"$key\"><label for=\"radtrig_$key\">$val</label><br />";
									}
								?>
								<br />
								<h4>Should entries be <strong>machine</strong>-transcribed automatically upon upload?</h4>
								<p>If Yes is chosen here, every new entry that will be status=READY will be automatically transcribed using English machine speech to text.<br />
								Note: Automatic transcription is currently only available for English speaking entries.</p>
								<input type="radio" id="radauto_1" name="auto_transcribe" value="1"><label for="radauto_1">Yes</label><br />
								<input type="radio" id="radauto_0" name="auto_transcribe" value="0"><label for="radauto_0">No</label><br />
								<input type="submit" id=finButton class="btn btn-default" value="Next" >
							</form>
						</div>
					</div>
				</div>
			</div>
			<div id="login">
				<main id="main">
					<div class="container">
						<div class="row">
							<div class="col-md-12">
								<div role="tabrole">
									<form method="post" id="loginForm" action="javascript:loginSubmit();" >
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
												<img src="lib/loginLoader.gif" id="loginLoader" style="display: none;">
												<div class="btn-holder">
													<input type="submit" id=submitButton class="btn btn-default" value="LOGIN">
												</div>
											</fieldset>	
										</div>
									</form>
								<!--div id="page" class="boxBody" style="display: none;"></div-->
							</div>
						</div>

					</div>
				</div>
			</main>
			</div>
			</div>
<!--?php echo HTML_FOOTER;?-->
				</div>

</body>
</html>
