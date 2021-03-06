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
print_r($user);

//request access token
$pb->user->requestToken();

//start game session
$pb->session->start(array(
	"user_token" => $pb->user->user_token
	));
print_r($pb->session);

//send events
$t = $pb->session->sendEvent(array(
					array(
						"code" => "test_string",
						"value" => mt_rand()."",
						"time" => time()
						),
					array(
						"code" => "test_int",
					    "value" => mt_rand(),
					    "time" => time()
						),
					array(
						"code" => "test_float",
					    "value" => mt_rand(),
					    "time" => time()
						),
					array(
						"code" => "test_bool",
				        "value" => true,
				        "time" => time()
						),
					array(
						"code" => "test_array",
				        "time" => $time,
						"value" => array(
								"val_string" => mt_rand()."",
								"val_numeric" => mt_rand()
							)
						)
				), $pb->user->user_token);
				
echo("\n".$t."\n");

//stop game session
$pb->session->stop();
print_r($pb->session);
?>