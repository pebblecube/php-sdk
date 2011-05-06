<?php
require_once('../src/Pebblecube.php');

$pb = new Pebblecube(array(
  'key'  => 'YOUR_KEY',
  'secret' => 'YOUR_SECRET'
));

$pb->session->start();
print_r($pb->session);

$pb->session->stop();
print_r($pb->session);
?>