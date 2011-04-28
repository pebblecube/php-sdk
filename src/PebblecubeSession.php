<?php
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
	 * starts a new session
	 *
	 * @return void
	 */
	public function start() {
		$res = PebblecubeApi::executeCall("/sessions/start", "GET", NULL);
		$this->id = $res["k"];
		$this->startedAt = $res["t"];
	}
	
	/**
	 * stops current session
	 *
	 * @return void
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
}
?>