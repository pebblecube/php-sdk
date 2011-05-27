<?php
require_once('../src/Pebblecube.php');

$pb = new Pebblecube(array(
  'key'  => 'YOUR_KEY',
  'secret' => 'YOUR_SECRET'
));

print_r($pb->getConstant("const_1"));


print_r($pb->executeFunction("test", array(
										"x" => 1,
										"y" => 2
									)
							)
		);
?>