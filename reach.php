<?php
// accepts a KS and, if not already in existance, creates the cielo24 role and user
if (!isset($argv[1])){
    echo __FILE__ . 'Missing KS!';
    exit (1);
}
require_once('KalturaGeneratedAPIClientsPHP/KalturaClient.php');

$config = new KalturaConfiguration($admin_partner_id);
$config->serviceUrl = 'https://www.kaltura.com';
$client = new KalturaClient($config);
$client->setKs($argv[1]);
$filter = new KalturaUserRoleFilter();
$filter->nameEqual = 'cielo24';
$pager = null;
$result = $client->userRole->listAction($filter, $pager);
if ($result->totalCount === 0){

	$userRole = new KalturaUserRole();
	$userRole->name = 'cielo24';
	$userRole->systemName = 'cielo24';
	$userRole->description = 'Kaltura REACH Admin';
	$userRole->tags='kmc';
	$userRole->status = KalturaUserRoleStatus::ACTIVE;
	$userRole->permissionNames = 'KMC_ACCESS,KMC_READ_ONLY,BASE_USER_SESSION_PERMISSION,WIDGET_SESSION_PERMISSION,CONTENT_MANAGE_BASE,CONTENT_MANAGE_METADATA,CONTENT_MANAGE_ASSIGN_CATEGORIES,CONTENT_MANAGE_EDIT_CATEGORIES,CONTENT_MANAGE_CATEGORY_USERS,CAPTION_MODIFY,ATTACHMENT_MODIFY';
	$result = $client->userRole->add($userRole);
	$role_id=$result->id;
	$user = new KalturaUser();
	$user->id = 'kaltura@cielo24.com';
	$user->type = KalturaUserType::USER;
	$user->screenName = 'kaltura@cielo24.com';
	$user->fullName = 'REACH cielo24';
	$user->email = 'kaltura@cielo24.com';
	$user->status = KalturaUserStatus::ACTIVE;
	$user->roleIds = $role_id;
	$result = $client->user->add($user);
}
?>
