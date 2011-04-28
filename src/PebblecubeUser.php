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

/**
 * Pebblecube user class
 *
 */
class PebblecubeUser
{
	/**
	 * user id
	 *
	 * @var string
	 */
	var $id = NULL;
	
	/**
	 * user authentication token
	 *
	 * @var string
	 */
	var $auth_token = NULL;
	
	/**
	 * user access token
	 *
	 * @var string
	 */
	var $user_token = NULL;
	
	/**
	 * Inizialize a pebblecube user
	 *
	 * config:
	 * - auth_token: md5(user_email + md5(password)) or token released by /users/addalien method
	 *
	 * @param Array $params the user configuration
	 */
	public function __construct($params = NULL) {
		if(is_array($params))
			$this->auth_token = isset($params["auth_token"]) ? $params["auth_token"] : NULL;
	}
	
	/**
	 * retrieves the data of the current user
	 * 
	 * @return Array user data
	 * @throws PebblecubeException
	 */
	public function get() {
		if($this->auth_token) {
			return PebblecubeApi::executeCall("/users/get", "GET", array("auth_token" => $this->auth_token));			
		}
		else
			throw new PebblecubeException("auth_token not specified");
	}
	
	/**
	 * request a new access token for the current user
	 *
	 * @return void
	 * @throws PebblecubeException
	 */
	public function requestToken() {
		if($this->auth_token) {
			$res = PebblecubeApi::executeCall("/users/get", "GET", array("auth_token" => $this->auth_token));
			if(isset($res['t'])) {
				$this->$user_token = $res['t'];
			}
		}
		else
			throw new PebblecubeException("auth_token not specified");
	}
	
	/**
	 * creates a new user for the current project
	 *
	 * params:
	 * - user_email: user email - must be unique
	 * - user_username: user nickname
	 * - user_password: password
	 *
	 * @param Array $params user data
	 * @return void
	 * @throws PebblecubeException
	 */
	public function add($params)
	{
		$res = PebblecubeApi::executeCall("/users/add", "POST", $params);
		if($res) {
			$this->id = $res["id"];
			$this->auth_token = md5($params['user_email'].md5($params['user_password']));
		}
		else
			throw new PebblecubeException("error in creating user");
	}
	
	/**
	 * creates a new "alien" user for the current project
	 *
	 * params:
	 * - alien_auth_service: name of the authentication service
	 * - alien_id: user id
	 * - alien_token: eventual authentication token
	 * - alien_secret: optional secret
	 * - user_username: user nickname
	 *
	 * @param Array $params user data
	 * @return void
	 * @throws PebblecubeException
	 */
	public function addAlien($params = NULL) {
		$res = PebblecubeApi::executeCall("/users/add", "POST", $params);
		if($res) {
			$this->id = $res["id"];
			$this->auth_token = $res["auth_token"];
		}
		else
			throw new PebblecubeException("error in creating user");
	}
	
	/**
	 * creates a new "alien" user for the current project
	 *
	 * params:
	 * - board: scoreboard code
	 * - value: value of the score
	 * - time: timestamp score
	 * - session_key: unique session key
	 *
	 * @param Array $params score data
	 * @return int server time
	 * @throws PebblecubeException
	 */
	public function saveScore($params) {
		if($this->auth_token) {
			$params["user_token"] = $this->user_token;			
			$res = PebblecubeApi::executeCall("/games/savescore", "POST", $params);
			return $res['t'];
		}
		else
			throw new PebblecubeException("auth_token not specified");
	}
	
	public function saveGame() {
		
	}
}
?>