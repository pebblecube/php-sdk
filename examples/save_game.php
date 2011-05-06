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

//start game session
$pb->session->start(array(
	"user_token" => $pb->user->user_token
	));

$text_string = mt_rand()."";
//create text file
$file_name = dirname(__FILE__). DIRECTORY_SEPARATOR."test.txt";
$fh = fopen($file_name, 'w') or die("can't open file");
fwrite($fh, $text_string);		
fclose($fh);

//upload file
$tiket = $pb->user->saveGame(array("file" => $file_name));
print_r($tiket);

//get file
$game_data = $pb->user->getSavedGame(array("k" => $tiket));
print_r($game_data);
$file_contents = file_get_contents($game_data['url']);
echo "\n"."content: ".$file_contents;


//save score
echo "\n".$pb->user->saveScore(array("board" => "test", "value" => 123));

//get board
print_r($pb->getScoreBoard(array("board" => "test")));

//get games
print_r($pb->user->getSavedGames());

//delete game
echo "\n".$pb->user->deleteSavedGame(array("k" => $tiket));
?>