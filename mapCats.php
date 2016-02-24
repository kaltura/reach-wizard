<html>
<head>
<title>REACH configuration summary</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/all.css">
</head>
<body>
<?php
require_once('options.php');
$trigger=$_POST['trigger'];
$msg_header="<h2>Below is the required $trigger configuration for partner ID: ". $_POST['partner_id'].".</h2>";
if ($_POST['auto_transcribe']){
	$msg_header.="<h4>Entries should be auto transcribed.</h4>";
}
echo "<h4>$msg_header</h4></br>";
if ($trigger === 'category'){
	foreach($_POST as $key => $val){
		if (strstr($key,'caption')){
			$msg.=$key.' => ' .$val.'</br>';
		}
	}
}else{
	$msg=$_POST['conf_msg'];
}
echo "$msg</br><h4>An email was sent to $to and ". $_POST['user_email'].'</h4></body></html>';
mail ($to.','.$_POST['user_email'],$subject,$msg_header.$msg,$headers);
?>
