<?php

require_once('KalturaGeneratedAPIClientsPHP/KalturaClient.php');
require_once('options.php');
$partnerId = $_REQUEST['partnerId'];
// are we doing a login with email and passwd or with partner ID and session?
if (isset($_REQUEST['login_type'])){
	$login_type = $_REQUEST['login_type'];
}else{
	$login_type = 'email_passwd';
}

if ($login_type === 'email_passwd'){
	// we're in our second phase, i.e: user already logged in with email and passwd and chose a partner ID from the selectbox presented to him.
	if($partnerId != 0){
		properKS($partnerId);
	}else {
		$config = new KalturaConfiguration($partnerId);
		$config->serviceUrl = $service_url;
		$client = new KalturaClient($config);
		$loginId = $_REQUEST['email'];
		$password = $_REQUEST['password'];
		//Attempts to login with the given information
		try {
			$ks = $client->user->loginByLoginId($loginId, $password);
			$client->setKs($ks);
			$filter = null;
			$pager = null;
			$results = $client->partner->listPartnersForUser();
			//If there is only one partner on the account, log in immediately
			if($results->totalCount == 1)
				properKS($results->objects[0]->id);
			//Otherwise, display the list of partners on the account
			else {
				$ret = array();
				$ret[] = $results->totalCount;
				foreach($results->objects as $partner)
					$ret[] = array($partner->id, $partner->name);
				echo json_encode($ret);
			}
		}
		//If the login attempt fails, throw an error
		catch(Exception $ex) {
			if(strpos($ex->getMessage(), 'Unknown') === false){
				echo 'loginfail';
			}
		}	
	}
}else{
	// we already have a partner ID and secret, just use them
	// POST params are still called email and passwd but we're deciding based on the value of login_type, which is taken from a selectbox
	startSession($_POST['email'], $_POST['password']);
}

// regular 'simple' KS gen
function startSession($partnerId,$secret) 
{
	try {
		$config = new KalturaConfiguration($partnerId);
		$config->serviceUrl = 'https://www.kaltura.com';
		$client = new KalturaClient($config);
		$expiry = null;
		$privileges = null;
		$ks = $client->session->start($secret, null, KalturaSessionType::ADMIN, $partnerId, $expiry, $privileges);
		$ret = array();
		$ret[] = 1;
		$ret[] = $ks;
		$ret[] = $partnerId;
		echo json_encode($ret);
	}catch(Exception $ex) {
		if(strpos($ex->getMessage(), 'Unknown') === false){
			echo 'loginfail';
		}
	}	
}

// when doing a user and passwd login, we need two passes, this is the second one:
// Once a partner is selected, generate a Kaltura session
function properKS($partnerId) 
{
	$config = new KalturaConfiguration($partnerId);
	$config->serviceUrl = 'https://www.kaltura.com';
	$client = new KalturaClient($config);
	$loginId = $_REQUEST['email'];
	$password = $_REQUEST['password'];
	$expiry = null;
	$privileges = null;
	$ks = $client->user->loginByLoginId($loginId, $password, $partnerId, $expiry, $privileges);
	$ret = array();
	$ret[] = 1;
	$ret[] = $ks;
	$ret[] = $partnerId;
	echo json_encode($ret);
}
