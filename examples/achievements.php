<?php
require_once('../src/Pebblecube.php');

$pb = new Pebblecube(array(
	'key'  => '88af624d7c8f7cc5c6ffd7c9b071355b04d26579b',
	'secret' => '354eef255ee60792c13a3c1a48b8b35d04d26579b'
));

//create a new random user
$pb->user->add(array(
	"user_email" => mt_rand()."@domain.com",
	"user_username" => "nickname",
	"user_password" => "password"
	));
	
//get user data
$user = $pb->user->get();

//request access token
$pb->user->requestToken();

//grant achievement
print_r($pb->user->grantAchievement(array("code" => "test")));

//get details
print_r($pb->user->getAchievementDetails(array("code" => "test")));

//get details
print_r($pb->user->getAchievement());

//revoke
print_r($pb->user->revokeAchievement(array("code" => "test")));
?>