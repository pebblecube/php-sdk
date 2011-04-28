<?php
require_once('../src/Pebblecube.php');

$pb = new Pebblecube(array(
  'key'  => '88af624d7c8f7cc5c6ffd7c9b071355b04d26579b',
  'secret' => '354eef255ee60792c13a3c1a48b8b35d04d26579b'
));

$pb->session->start();
print_r($pb->session);

$pb->session->stop();
print_r($pb->session);
?>