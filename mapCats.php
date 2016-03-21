<html>
<head>
<title>Kaltura REACH KMC Workflow configuration summary</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/all.css">
</head>
<body>
<?php
require_once('options.php');
require_once('headers.inc');
echo HTML_HEADER;
?>
<div class="content">
<div class="col-md-12">
<div class="row">
<?php
$trigger=$_POST['trigger'];
$msg_header="<h2>Kaltura REACH KMC Workflow configuration summary</h2>";
$msg_header.="<h3>For Kaltura Account partnerId: ". $_POST['partner_id'].".</h3>";
$msg_header.="<h4>Chosen transcription triggers:</h4><ul>";
$msg_header.="<li>".($trigger==="category"?"categories":'Start Tags')." based workflow</li>";
if ($_POST['auto_transcribe']){
	$msg_header.="<li>All newly uploaded entries will be automatically machine transcribed.</li>";
}
$msg_header.="</ul>";
echo "<h3>$msg_header</h3>";
if ($trigger === 'category'){
	$msg.="<h4>Chosen transcription categories:</h4><ul>";
	foreach($_POST as $key => $val){
		if (strstr($key,'caption')){
			$keyarr = explode("_", $key);
			$captionkey = ucfirst($keyarr[1]).' '.($keyarr[2]=='asr'?'machine':$keyarr[2]);
			$msg.='<li><strong>'.$captionkey.'</strong> by adding entries to category: <strong>'.$val.'</strong></li>';
		}
	}
	$msg.="</ul>";
}else{
	$msg=$_POST['conf_msg'].'</br></br><span style="color:red;font-weight:bold;">Within 24 hours, your account will be set up. You can then input the "Start Tags" listed above to request transcriptions for your content.</span>';
}
if(isset($_POST['user_email'])){
	$to.=','.$_POST['user_email'];
}
echo "$msg</br><h4>An email was sent to $to </h4></body></html>";
mail ($to,$subject,$msg_header.$msg,$headers);
?>
</div>
</div>
</div>
</body>
</html>
