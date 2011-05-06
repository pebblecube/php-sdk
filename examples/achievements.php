<?php
require_once('../src/Pebblecube.php');

$pb = new Pebblecube(array(
	'key'  => 'YOUR_KEY',
	'secret' => 'YOUR_SECRET'
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