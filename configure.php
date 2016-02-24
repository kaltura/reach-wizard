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
	echo "$ret<br />";
}

$form='
  <div class="content">
<div class="row">
<div class="col-md-12">
<h1>CONFIGURATION</h1>

<form id="cat_form" action="mapCats.php" method="post">
<input type="hidden" id="partner_id" name="partner_id" value="'.$_POST['partner_id'].'">
<input type="hidden" id="auto_transcribe" name="auto_transcribe" value="'.$auto_transcribe.'">
<input type="hidden" id="trigger" name="trigger" value="'.$trigger.'">
';
$msg="<h3>The transcription triggers that will be configured:</h3>";
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
		//categories selectbox options
		$selectbox_values.=" <option value=\"$cat->fullName ($cat->id)\">$cat->fullName ($cat->id)</option>";
	}
}
if ($trigger === 'tag'){
	$msg.= "<ul>";
}
$error_langs=array();
if(!empty($_POST['ctat'])){
	foreach($_POST['ctat'] as $ctat){
		foreach($_POST['lang'] as $lang){
			if ($lang === 'english'){
				$trans_id="caption$ctat";
				$trans_id_cat='caption_'.$lang."_".$ctat;
			}else{
				$trans_id="caption$lang$ctat";
				$trans_id_cat='caption_'.$lang."_".$ctat;
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
				$msg.= "<li><strong>$trans_id</strong> - Will execute <strong>$trans_type $ctat</strong> transcription for <strong>".ucfirst($lang)."</strong> spoken video</li>";
			}else{
				$form.="<div class=\"form-group valid-row\">
				<label for=\"$trans_id_cat\">Choose a category for $trans_type $ctat transcription for ".ucfirst($lang)." spoken video:<span class=\"required\"> *</span></label>
				<select id=\"$trans_id_cat\" name=\"$trans_id_cat\" class=\"form-control select\">$selectbox_values</select>
				</div>";

			}
		}
	}
}
if ($trigger === 'tag'){
	$msg.= "</ul>";
}
$err_msg='';
if(count($error_langs)){
	$uniq_errors=array_unique($error_langs);
	$err_msg="<span style=\"color:red;font-weight:bold;\"><b>The following languages are only available for Human transcription.<br />Machine-transcription tags for these languages are not available:</span><ul>";
	foreach ($uniq_errors as $err){
		$err_msg.="<li>$err</li>";
	}
	$err_msg.='</ul><br />';
}

if ($trigger === 'tag'){
	echo '<div id="wrapper">
	<div class="w1">
	<main id="main">
		<div class="container">
		<div role="tabrole">
			<form id="cat_form" action="mapCats.php" method="post">
			<div class="row">
			<div class="col-md-12">
			<input type="hidden" id="partner_id" name="partner_id" value="'.$_POST['partner_id'].'">
			<input type="hidden" id="auto_transcribe" name="auto_transcribe" value="'.$auto_transcribe.'">
			<input type="hidden" id="trigger" name="trigger" value="'.$trigger.'">
			<input type="hidden" id="conf_msg" name="conf_msg" value="'.$msg.'">
			<h4>'.$msg.'</h4><br />'.$err_msg.'
			<div class="form-group valid-row">
				<label for="email">Your E-mail address (you will be CC on the activation email): <span class="required">*</span></label>
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
		<label for="email">Your E-mail address (you will be CC on the activation email): <span class="required">*</span></label>
		<input id="user_email" name="user_email"  type="text" class="form-control required">
	</div>
	<input id="submit" name="submit" type="submit" class="btn btn-default" value="SUBMIT">

	</div></div></form>';
}
?>
</body>
</html>