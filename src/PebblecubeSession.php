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
 * Game session class
 *
 */
class PebblecubeSession
{
	/**
	 * session unique id
	 *
	 * @var int
	 */
	var $id = NULL;
	
	/**
	 * timestamp session started
	 *
	 * @var 
	 */
	var $startedAt = NULL;
	
	/**
	 * elapsed time between stop and start
	 *
	 * @var string
	 */
	var $elapsedTime = NULL;
	
	/**
	 * time that session stopped
	 *
	 * @var string
	 */
	var $stoppedAt = NULL;
	
	/**
	 * starts a new game session
	 *
	 * @param Array $params optional parameters user_token, version, time
	 * @return void
	 * @throws PebblecubeException
	 */
	public function start($params = NULL) {
		$res = PebblecubeApi::executeCall("/sessions/start", "GET", $params);
		if($res != null) {
			$this->id = $res["k"];
			$this->startedAt = $res["t"];
		}
		else
			throw new PebblecubeException("invalid session");
	}
	
	/**
	 * stops current session
	 *
	 * @return void
	 * @throws PebblecubeException
	 */
	public function stop() {
		if($this->id != null) {
			$res = PebblecubeApi::executeCall("/sessions/stop", "GET", array("session_key" => $this->id));
			$this->elapsedTime = $res["t"];
			$this->stoppedAt = $this->startedAt + $this->elapsedTime;
		}
		else
			throw new PebblecubeException("session not started");
	}
	
	/**
	 * send a new game event
	 *
	 * @param Array $events events array list
	 * @param string $user_token optional user token
	 * @return int server timestamp
	 */
	public function sendEvent($events, $user_token = NULL) {
		if($this->id != null) {
		
			$params = array("session_key" => $this->id, "events" => json_encode($events));
			if($user_token != null)
				$params['user_token'] = $user_token;
				
			$res = PebblecubeApi::executeCall("/events/send", "POST", $params);
			if($res == null)
				throw new PebblecubeException("invalid event");
			else
				return $res["t"];
		}
		else
			throw new PebblecubeException("session not started");
	}
	
	/**
	 * tracks a new segment on a specified map
	 *
	 * @param string $map map code
	 * @param Array $segment list of coordinates
	 * @param int $start optional start timestamp
	 * @param int $stop optional stop timestamp
	 * @return string segment id
	 */
	public function trackSegment($map, $segment, $start = time(), $stop = time()) {
		if($this->id != null) {
			if(empty($map)) {
				throw new PebblecubeException("invalid map code");
			}			
			if(empty($segment)) {
				throw new PebblecubeException("invalid segment");
			}
			$params = array("session_key" => $this->id, "map" => $map, "start" => $start, "stop" => $stop, "segment" => json_encode($segment));
			$res = PebblecubeApi::executeCall("/maps/track", "POST", $params);
			if($res == null) {
				throw new PebblecubeException("invalid segment");
			}
			else {
				return $res["s"];
			}
		}
		else
			throw new PebblecubeException("session not started");
	}
}
?>