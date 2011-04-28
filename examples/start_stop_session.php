<?php
require_once('../src/Pebblecube.php');

$pb = new Pebblecube(array(
  'key'  => 'your key',
  'secret' => 'your secret'
));

$pb->session->start();
print_r($pb->session);

$pb->session->stop();
print_r($pb->session);
?>