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
			$res = PebblecubeApi::executeCall("/auth/request_token", "GET", array("auth_token" => $this->auth_token));
			if(isset($res['t'])) {
				$this->user_token = $res['t'];
			}
			else
				throw new PebblecubeException("error getting user token");
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
	
	/**
	 * uploads a saved game file
	 *
	 * params:
	 * - file: path to the file
	 * - name: filename
	 * - time: timestamp created
	 * - session_key: unique session key
	 *
	 * @param Array $params game data
	 * @return string ticket to retrieve the file
	 * @throws PebblecubeException
	 */
	public function saveGame($params) {
		if($this->user_token) {
			$params["user_token"] = $this->user_token;
			$res = PebblecubeApi::executeCall("/games/save", "FILE", $params);
			if($res)
				return $res['k'];
			else
				throw new PebblecubeException("error saving file");
		}
		else
			throw new PebblecubeException("user_token not valid");
	}
	
	/**
	 * updates a saved game file
	 *
	 * params:
	 * - k: file identifier
	 * - file: saved game file 
	 * - name (optional): filename
	 * - time (optional): timestamp created
	 * - session_key (optional): unique session key
	 *
	 * @param Array $params game data
	 * @return int server time
	 * @throws PebblecubeException
	 */
	public function updateGame($params) {
		$res = PebblecubeApi::executeCall("/games/update", "FILE", $params);
		return $res['t'];
	}
	
	/**
	 * gets the details of a saved game
	 *
	 * params:
	 * - k: file identifier
	 *
	 * @param Array $params game data
	 * @return Array file details
	 * @throws PebblecubeException
	 */
	public function getSavedGame($params) {
		return PebblecubeApi::executeCall("/games/get", "GET", $params);			
	}
	
	/**
	 * delete a saved game
	 *
	 * params:
	 * - k: file identifier
	 *
	 * @param Array $params game data
	 * @return int server time
	 * @throws PebblecubeException
	 */
	public function deleteSavedGame($params) {
		$res = PebblecubeApi::executeCall("/games/delete", "GET", $params);
		return $res['t'];
	}	
	
	/**
	 * returns user saved games
	 *
	 * config:
	 * - index (optional): page index
	 * - size (optional): page size, max 100
	 *
	 * @param Array $params 
	 * @return Array list of saved games
	 * @throws PebblecubeException
	 */
	public function getSavedGames($params = NULL) {
		if($this->user_token) {
			if($params == null)
				$params = array();
				
			$params["user_token"] = $this->user_token;
			return PebblecubeApi::executeCall("/games/list", "GET", $params);
		}
		else
			throw new PebblecubeException("user_token not valid");
	}
	
	/**
	 * grants an achievement to a user
	 *
	 * config:
	 * - code: achievement code
	 * - time (optional): timestamp score
	 * - session_key (optional): unique session key
	 *
	 * @param Array $params 
	 * @return int server time
	 * @throws PebblecubeException
	 */
	public function grantAchievement($params) {
		if($this->user_token) {
			$params["user_token"] = $this->user_token;
			$res = PebblecubeApi::executeCall("/achievements/grant", "POST", $params);
			return $res['t'];
		}
		else
			throw new PebblecubeException("user_token not valid");
	}
	
	/**
	 * gets achievement details
	 *
	 * config:
	 * - code: achievement code
	 *
	 * @param Array $params 
	 * @return Array achievement details
	 * @throws PebblecubeException
	 */
	public function getAchievementDetails($params) {
		if($this->user_token) {
			$params["user_token"] = $this->user_token;
			return PebblecubeApi::executeCall("/achievements/details", "GET", $params);
		}
		else
			throw new PebblecubeException("user_token not valid");
	}
	
	/**
	 * revoke an achievement
	 *
	 * config:
	 * - code: achievement code
	 *
	 * @param Array $params 
	 * @return int server time
	 * @throws PebblecubeException
	 */
	public function revokeAchievement($params) {
		if($this->user_token) {
			$params["user_token"] = $this->user_token;
			$res = PebblecubeApi::executeCall("/achievements/revoke", "POST", $params);
			return $res['t'];
		}
		else
			throw new PebblecubeException("user_token not valid");
	}
	
	public function getAchievement() {
		if($this->user_token) {
			return PebblecubeApi::executeCall("/achievements/user", "GET", array("user_token" => $this->user_token));
		}
		else
			throw new PebblecubeException("user_token not valid");
	}
}
?>