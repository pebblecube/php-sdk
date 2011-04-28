Pebblecube PHP SDK
================

Pebblecube is videogame development tools, includes: analytics, achievements, scores and much more.

This repository contains the open source PHP SDK for the Pebblecube APIs.

Usage
-----

    <?php

	require_once('../Pebblecube.php');
	
	$pb = new Pebblecube(array(
		'key'  => 'your key',
		'secret' => 'your secret'
	));

    try {
		$pb->session->start();
    } catch (PebblecubeException $e) {
		error_log($e);
    }
	
	?>
	
Feedback
--------

Use the [GitHub issues tracker][issues] for feedback. Send bugs or other issues [here][issues].

[issues]: https://github.com/pebblecube/php-sdk/issues

Tests
-----

Not ready yet ;)