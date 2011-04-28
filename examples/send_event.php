<?php
require_once('../src/Pebblecube.php');

$pb = new Pebblecube(array(
  'key'  => '88af624d7c8f7cc5c6ffd7c9b071355b04d26579b',
  'secret' => '354eef255ee60792c13a3c1a48b8b35d04d26579b'
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