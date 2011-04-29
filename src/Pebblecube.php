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
			"sig" => NULL
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
		Pebblecube::$config["sig"] = md5($params['key'].$params['secret']);
		$this->session = new PebblecubeSession();
		$this->user = new PebblecubeUser();
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
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
				break;
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		$json = json_decode($result, TRUE);
		
		//check status code or response for errors
		if($httpcode >= 400 || array_key_exists("e", $json)) {
			throw new PebblecubeException(sprintf("%s", $json["e"]));
		}
		return json_decode($result, TRUE);
	}
}

/**
 * custom pebblecube exception
 *
 */
class PebblecubeException extends Exception { }
?>