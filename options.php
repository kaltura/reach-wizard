<?php
$service_url='https://www.kaltura.com';
 //2. Check boxes: "Chose your desired Transcription turn-around-time (the time from start until captions available in KMC):”
$captions_turn_around_time=array(
'asr'=>'Machine Speech to Text (up to 30 minutes turn around time)',
'3hour' => 'Professional Human Transcription - 3 Hours',
'6hour' => 'Professional Human Transcription - 6 Hours',
'24hour' => 'Professional Human Transcription - 24 Hours',
'48hour' => 'Professional Human Transcription - 48 Hours',
'7day' => 'Professional Human Transcription - 7 Days',
); 

// 3. Check boxes: "Chose the languages spoken in your videos (note the supported services next to each language):”
$captions_lang = array(
'arabic'=>array('Human'),
'english'=>array('Human','Machine'),
'french'=>array('Human','Machine'),
'german'=>array('Human','Machine'),
'hebrew'=>array('Human'),
'hindi'=>array('Human'),
'italian'=>array('Human','Machine'),
'japanese'=>array('Human'),
'korean' => array('Human'),
'mandarin'=> array('Human'),
'portugue'=> array('Human'),
'russian'=> array('Human'),
'spanish'=> array('Human','Machine'),
'chinese'=> array('Human'),
'turkish'=> array('Human'),
'dutch' => array('Human','Machine'),
);

$triggers = array (
'tag'=>'Tag based',
'category'=>'Category based',
);

// email

$subject='Kaltura REACH: New KMC Account Activation Request';
$to='reach@kaltura.com';
//$to='reach@kaltura.com,kaltura@cielo24.com';
//$to='jess.portnoy@kaltura.com';
$headers = "From: Kaltura REACH <community@kaltura.com>\r\n";
$headers .= "Reply-To: reach@kaltura.com,kaltura@cielo24.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
