<?php
/**
 *
 * Copyright 2011 Pebblecube.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require_once("PebblecubeSession.php");
require_once("PebblecubeUser.php");

//check if curl installed
if (!function_exists('curl_init'))
	throw new PebblecubeException("lib curl missing");

/**
 * Provides access to the Pebblecube apis
 *
 */
class Pebblecube
{
	/**
	 * configuration array 
	 *
	 * @var Array
	 */
	public static $config = array(
			"server" => "https://api.pebblecube.com",
			"key" => NULL,
			"secret" => NULL,
			"sig" => NULL,
			"cipher" => NULL
		);
	
	/**
	 * session object
	 *
	 * @var PebblecubeSession
	 */
	var $session;
	
	/**
	 * user object
	 *
	 * @var PebblecubeUser
	 */
	var $user;
		
	/**
	 * Inizialize a pebblecube project
	 *
	 * config:
	 * - key: project api key
	 * - secret: project secret key
	 *
	 * @param Array $config the project configuration
	 * @throws PebblecubeException
	 */
	public function __construct($params) {
		
		if(empty($params['key']) || empty($params['secret']))
			throw new PebblecubeException("invalid construct parameters");
			
		Pebblecube::$config["key"] = $params['key'];
	    Pebblecube::$config["secret"] = $params['secret'];
	    Pebblecube::$config["cipher"] = $params['cipher'];
		Pebblecube::$config["sig"] = md5($params['key'].$params['secret']);
		$this->session = new PebblecubeSession();
		$this->user = new PebblecubeUser();
	}
	
	
	/**
	 * returns all the scores in a scoreboard
	 *
	 * config:
	 * - board: scoreboard code
	 * - index (optional): page index, default 1
	 * - size (optional): page size, max and default 100
	 * - from (optional): time stamp from
	 * - to (optional): time stamp to
	 * - user_token (optional): token released by /auth/request_token method
	 *
	 * @param Array $params 
	 * @return Array scores from a specific scoreboard
	 * @throws PebblecubeException
	 */
	public function getScoreBoard($params) {
		return PebblecubeApi::executeCall("/games/scoreboard", "GET", $params);
	}
	
	/**
	 * returns list of all users with a specified achievement
	 *
	 * config:
	 * - code: achievement code
	 * - index (optional): page index, default 1
	 * - size (optional): page size, max and default 100
	 * - from (optional): time stamp from
	 * - to (optional): time stamp to
	 *
	 * @param Array $params 
	 * @return Array achievements
	 * @throws PebblecubeException
	 */
	public function getAchievementsBoard($params) {
		return PebblecubeApi::executeCall("/achievements/board", "GET", $params);
	}
	
	/**
	 * returns the value of a selected constant
	 *
	 * @param string $code constant code 
	 * @return Array
	 */
	public function getConstant($code) {
		return PebblecubeApi::executeCall("/functions/constant", "GET", array("code" => $code));
	}
	
	/**
	 * returns the result of a selected function
	 *
	 * config:
	 * - code: function code
	 * - vars: json string with values for each of the variables specified in the function script
	 *
	 * @param string $code constant code 
	 * @return Array
	 */
	public function executeFunction($code, $vars) {
		$params = array("code" => $code, "vars" => json_encode($vars));
		return PebblecubeApi::executeCall("/functions/execute", "GET", $params);
	}
}

class PebblecubeApi 
{
	/**
	 * executes an api call
	 *
	 * @param string $url api method url
	 * @param string $method HTTP method
	 * @param Array $params method parameters
	 * @return Array JSON result encoded into array
	 * @throws PebblecubeException
	 */
	public static function executeCall($url, $method, $params) {
		
		if(Pebblecube::$config["cipher"] && sizeof($params) > 0) {
			$enc_data = PebblecubeApi::encrypt(http_build_query($params, '', '&'));
			$params = array();
			$params["data"] = $enc_data;
		}
		
		//default call parameters
		$params["api_sig"] = Pebblecube::$config["sig"];
		$params["api_key"] = Pebblecube::$config["key"];
		
		$ch = curl_init();
		switch($method) {
			case "GET":
				curl_setopt($ch, CURLOPT_URL, Pebblecube::$config["server"].$url."?".http_build_query($params, '', '&'));
				break;
			case "POST":
				curl_setopt($ch, CURLOPT_URL, Pebblecube::$config["server"].$url);
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
				break;
			case "FILE":
				$postParams = array();
				foreach ($params as $fieldName => $fieldValue) {
					$postParams[$fieldName] = $fieldValue;
				}
				$postParams["file"] = "@".$postParams["file"];
				curl_setopt($ch, CURLOPT_URL, Pebblecube::$config["server"].$url);
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
		}

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		//check status code or response for errors
		if($httpcode >= 400) {
			$json = json_decode($result, TRUE);
			throw new PebblecubeException(sprintf("%s", $json["e"]));
		}
		else {
			return json_decode(PebblecubeApi::decrypt($result), TRUE);
		}
	}
	
	public static function encrypt($text) {
		$const_cipher = NULL;
		switch (Pebblecube::$config["cipher"]) {
			case '256':
				$const_cipher = MCRYPT_RIJNDAEL_256;
				$key = substr(Pebblecube::$config["secret"], 0, 32);
				break;
			case '192':
				$const_cipher = MCRYPT_RIJNDAEL_192;
				$key = substr(Pebblecube::$config["secret"], 0, 24);
				break;
			case '128':
				$const_cipher = MCRYPT_RIJNDAEL_128;
				$key = substr(Pebblecube::$config["secret"], 0, 16);
				break;
		}
		if($const_cipher != NULL) {
			$iv_size = mcrypt_get_iv_size($const_cipher, MCRYPT_MODE_ECB);
	   		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	   		return mcrypt_encrypt($const_cipher, $key, $text, MCRYPT_MODE_ECB, $iv);
	 	}
		else
			return $text;
	}

	public static function decrypt($text) {
		$const_cipher = NULL;
		switch (Pebblecube::$config["cipher"]) {
			case '256':
				$const_cipher = MCRYPT_RIJNDAEL_256;
				$key = substr(Pebblecube::$config["secret"], 0, 32);
				break;
			case '192':
				$const_cipher = MCRYPT_RIJNDAEL_192;
				$key = substr(Pebblecube::$config["secret"], 0, 24);
				break;
			case '128':
				$const_cipher = MCRYPT_RIJNDAEL_128;
				$key = substr(Pebblecube::$config["secret"], 0, 16);
				break;
		}
		if($const_cipher != NULL) {
			$iv_size = mcrypt_get_iv_size($const_cipher, MCRYPT_MODE_ECB);
	   		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	   		return trim(mcrypt_decrypt($const_cipher, $key, $text, MCRYPT_MODE_ECB, $iv));
		}
		else
			return $text;
	}
}

/**
 * custom pebblecube exception
 *
 */
class PebblecubeException extends Exception { }
?>