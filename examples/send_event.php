<?php
require_once('../src/Pebblecube.php');

$pb = new Pebblecube(array(
  'key'  => 'YOUR_KEY',
  'secret' => 'YOUR_SECRET'
));

//start session
$pb->session->start();
print_r($pb->session);

//send a list of events
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
				));

echo("\n".$t."\n");
//stop game session
$pb->session->stop();
print_r($pb->session);
?>