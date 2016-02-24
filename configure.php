<html>
<head>
<title>REACH configuration summary</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/all.css">
</head>
<body>
<?php
/*echo "<pre>";
var_dump($_POST);
echo "</pre>";*/
require_once('options.php');
if(isset($_POST['trigger'])){
	$trigger=$_POST['trigger'];
}else{
	$trigger='tag';
}
if(isset($_POST['auto_transcribe'])){
	$auto_transcribe=$_POST['auto_transcribe'];
}else{
	$auto_transcribe=false;
}

exec("php reach.php ". $_POST['ks'],$ret,$rc);
if($rc !== 0){
	echo "$ret</br>";
}

$form='
  <div class="content">
<h1>CONFIGURATION</h1>

<form id="cat_form" action="mapCats.php" method="post">
<input type="hidden" id="partner_id" name="partner_id" value="'.$_POST['partner_id'].'">
<input type="hidden" id="auto_transcribe" name="auto_transcribe" value="'.$auto_transcribe.'">
<input type="hidden" id="trigger" name="trigger" value="'.$trigger.'">
<div class="row">
<div class="col-sm-6">
';
$msg="<h4>Requested captions are:</h4>";
if ($trigger === 'category'){
	require_once('KalturaGeneratedAPIClientsPHP/KalturaClient.php');
	require_once('options.php');
	$config = new KalturaConfiguration();
	$config->serviceUrl = $service_url;
	$client = new KalturaClient($config);
	$client->setKs($_POST['ks']);
	$filter = new KalturaCategoryFilter();
	$pager = null;
	$result = $client->category->listAction($filter, $pager);
	$selectbox_values='';
	foreach($result->objects as $cat){
		$selectbox_values.=" <option value=\"$cat->name\">$cat->name</option>";
	}
}
$error_langs=array();
if(!empty($_POST['ctat'])){
	foreach($_POST['ctat'] as $ctat){
		foreach($_POST['lang'] as $lang){
			if ($lang === 'english'){
				$trans_id="caption$ctat";
			}else{
				$trans_id="caption$lang$ctat";
			}
			if (!stristr($ctat,'asr')){
				$trans_type='human';
			}else{
				if(in_array('Machine',$captions_lang[$lang])){
					$trans_type='machine';
				}else{
					$error_langs[] = ucfirst($lang);
					continue;
				}
			}
			if ($trigger === 'tag'){
				$msg.= "$trans_id - Will execute $trans_type transcription for ".ucfirst($lang)." spoken video</br>";
			}else{
				$form.="<div class=\"form-group valid-row\">
				<label for=\"$trans_id\">Choose a category for $trans_type $ctat transcription for ".ucfirst($lang)." spoken video:<span class=\"required\"> *</span></label>

				<select id=\"$trans_id\" name=\"$trans_id\" class=\"form-control select\">$selectbox_values</select>
				</div>
				";

			}
		}
	}
}

$uniq_errors=array_unique($error_langs);
$err_msg="<font color=\"red\"><b>The following languages are only available for Human transcription. Machine tags for these languages are not available:</br>";
foreach ($uniq_errors as $err){
	$err_msg.="$err</br>";
}
$err_msg.='</font></b></br>';


if ($trigger === 'tag'){
	echo '<div id="wrapper">
	<div class="w1">
	<main id="main">
		<div class="container">
		<div role="tabrole">
			<form id="cat_form" action="mapCats.php" method="post">
			<div class="row">
			<div class="col-sm-6">
			<input type="hidden" id="partner_id" name="partner_id" value="'.$_POST['partner_id'].'">
			<input type="hidden" id="auto_transcribe" name="auto_transcribe" value="'.$auto_transcribe.'">
			<input type="hidden" id="trigger" name="trigger" value="'.$trigger.'">
			<input type="hidden" id="conf_msg" name="conf_msg" value="'.$msg.'">
			<h4>'.$msg.'</h4></br>'.$err_msg.'
			<div class="form-group valid-row">
				<label for="email">E-mail address: <span class="required">*</span></label>
				<input id="user_email" name="user_email"  type="text" class="form-control required">
			</div>
			<input id="submit" name="submit" type="submit" class="btn btn-default" value="SUBMIT">
			</form>
		</div>
		</div>
	</main>
	</div>
	</div>
';
}else{
	echo $form.'
	<div class="btn-holder">';
	echo $err_msg.'
	<div class="form-group valid-row">
		<label for="email">E-mail address: <span class="required">*</span></label>
		<input id="user_email" name="user_email"  type="text" class="form-control required">
	</div>
	<input id="submit" name="submit" type="submit" class="btn btn-default" value="SUBMIT">
	</div>

	</div></div></form>';
}
echo "</body></html>";

