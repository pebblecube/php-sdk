<?php
require_once('../src/Pebblecube.php');

$pb = new Pebblecube(array(
  'key'  => 'YOUR_KEY',
  'secret' => 'YOUR_SECRET'
));

$map_code = "YOUR MAP CODE";

//start session
$pb->session->start();
print_r($pb->session);

//build random segment
$segment = array();
for($i = 0; $i <= 10; $i++) {
	$segment[] = array("x" => mt_rand(), "y" => mt_rand(), "z" => mt_rand());
}

//send segment
$s = $pb->session->trackSegment($map_code, $segment);

echo("\n".$s."\n");

//stop game session
$pb->session->stop();
print_r($pb->session);
?>