<?php
/**
 * Curse Inc.
 * Redis Session
 * Redis Session Language Strings
 *
 * @author		Timothy Aldrdige
 * @copyright	(c) 2013 Curse Inc.
 * @license		GPL v3.0
 * @package		Redis Session
 * @link		https://github.com/HydraWiki/RedisSessions
 *
**/

class RedisSessionHandler implements SessionHandlerInterface {
	/**
	 * Session Expiration in Seconds
	 *
	 * @var		integer
	 */
	private $sessionExpiration = 15552000;

	/**
	 * Session Key for Redis
	 *
	 * @var		string
	 */
	private $sessionKeyPrefix = ':session:';

	/**
	 * Session Data from initial read.  [$id => $data] - Session ID is use as the key since it is possible to read session data from another user during the a different session while in maintenance scripts.
	 *
	 * @var		array
	 */
	private $sessionData;

	/**
	 * Setup basic Redis handler.
	 *
	 * @access	public
	 * @param	integer	[Optional] Number of seconds for sessions to stay active.  Defaults to 180 days.
	 * @return	void
	 */
	public function __construct($sessionExpiration = 15552000) {
		$this->sessionExpiration = $sessionExpiration;

		$this->redis = \RedisCache::getClient('session');
	}

	/**
	 * Do any procedures required to open a session.
	 *
	 * @access	public
	 * @param	string	[Not Used] File path to save sessions.
	 * @param	string	Session Name
	 * @return	boolean	True
	 */
	public function open($savePath = null, $sessionName) {
		if (!empty($sessionName)) {
			$this->sessionKeyPrefix .= $sessionName.':';
		}

		return true;
	}

	/**
	 * Close out a session
	 *
	 * @access	public
	 * @return	boolean	True
	 */
	public function close() {
		return true;
	}

	/**
	 * Read Session Data
	 *
	 * @access	public
	 * @param	string	Session ID
	 * @return	boolean	True
	 */
	public function read($id) {
		if ($this->redis !== false) {
			try {
				$data = $this->redis->get($this->sessionKeyPrefix.$id);
			} catch (RedisException $e) {
				$this->redis = false;
			}
		}

		if (!$data) {
			$data = '';
		}

		$this->sessionData[$id] = $data;

		return $data;
	}

	/**
	 * Write Session Data
	 *
	 * @access	public
	 * @param	string	Session ID
	 * @param	string	Session Data
	 * @return	boolean	True
	 */
	public function write($id, $data) {
		if ($this->redis !== false) {
			try {
				// If the data to write is exactly the same as the existing data
				// as a pure string to string comparison from the initial read
				// just update the TTL to keep the session active and return.
				// This helps with some cases of page request ordering causing
				// session data being destroyed.
				if ($data !== $this->sessionData[$id]) {
					$this->redis->set($this->sessionKeyPrefix.$id, $data);
				}
				$this->redis->expire($this->sessionKeyPrefix.$id, $this->sessionExpiration);
			} catch (RedisException $e) {
				$this->redis = false;
			}
		}

		return true;
	}

	/**
	 * Destroy a Session
	 * Crush.  Kill.  Destroy.  Swag.
	 *
	 * @access	public
	 * @param	string	Session ID
	 * @param	string	Session Data
	 * @return	boolean	True
	 */
	public function destroy($id) {
		if ($this->redis !== false) {
			try {
				$this->redis->del($this->sessionKeyPrefix.$id);
			} catch (RedisException $e) {
				$this->redis = false;
			}
		}

		return true;
	}

	/**
	 * Session Garbage Collector - This is handled automatically by Redis Expire/TTL.
	 *
	 * @access	public
	 * @param	integer	Epoch Timestamp
	 * @return	boolean	True
	 */
	public function gc($maxlifetime) {
		return true;
	}
}
