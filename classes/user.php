<?php
  /**
   * Copyright 2015 Last Hit Productions, Inc.
   *
   * You are hereby granted a non-exclusive, worldwide, royalty-free license to
   * use, copy, modify, and distribute this software in source code or binary
   * form for use in connection with the web services and APIs provided by
   * Last Hit Producions (LHP).
   *
   * As with any software that integrates with the LHP platform, your use
   * of this software is subject to the LHP Developer Principles and
   * Policies [http://developers.lhpdigital.com/policy/]. This copyright notice
   * shall be included in all copies or substantial portions of the software.
   *
   * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
   * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
   * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
   * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
   * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
   * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
   * DEALINGS IN THE SOFTWARE.
   *
   */
   
  /**
   * Class LhpUser
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  class LhpUser {
  
    /**
     * @var string - Sessions name
     */
	private $session_name = 'SESSIONID';
  
    /**
     * @var int - Default role of current user
     */
	private $default_role = 0;
  
    /**
     * @var int - session id of current user
     */
	private $default_id = 1;
  
    /**
     * @var bool - by pass cookie validation for logged in user
     */
	private $bypass_validation = false;
  
    /**
     * @var int - flag to determine when to update mysql with session data
     */
	public $update = false;
  
    /**
     * @var object - Pointer to cookie class
     */
	private $cookie = null;
  
    /**
     * @var object - Pointer to mysql class
     */
	private $mysql = null;
  
    /**
     * @var object - session data for a particular file
     */
	private $session_data = null;
  
    /**
     * @var array - These are the mysql keys that we do not want to store in session data
     */
	private $no_save_keys = array('password', 'user_id');
	
    /**
     * LhpUser - Initiate user object and create unique session and store in mysql
     */
	public function __construct(&$cookie, &$mysql) {
	  $this->cookie = $cookie;
	  $this->mysql = $mysql;
      $this->reload();
	}
	
    /**
     * reload - Generate session id and open session
     */
	public function reload($force=false) {
	  $expiry = $this->logged_in() ? SESSION_EXPIRY : GUEST_SESSION_EXPIRY;
      $session_id = ($this->cookie->get($this->session_name) !== null && !$force) ? $this->cookie->get($this->session_name) : str_random(32);
	  session_name($this->session_name);
	  session_set_cookie_params($expiry, '/', '.' . DOMAIN);
	  session_id($session_id);
      session_start();
	  
	  //if($this->cookie->get($this->session_name) === null) {
	  //  $this->cookie->set($this->session_name, $session_id, $expiry);
	  //}

	}

    /**
     * getSessionDataVal - get value of session var from mysql
     */
	public function getSessionDataVal($key, $user_id) {
	  $val = null;
	  if($this->session_data === null) {
	    $row = $this->mysql->fetch("select `session_data` from `users` where `id`='$user_id' limit 1");
		$this->mysql->free();
		if($this->mysql->affected_rows === 1) {
		  $this->session_data = unserialize($row['session_data']);
		}
	  }
	  if($this->session_data !== null && isset($this->session_data[SESSION_PREFIX . $key])) {
	    $val = $this->session_data[SESSION_PREFIX . $key];
	  }
	  return $val;
	}
	
    /**
     * get - Retreive user field from $_SESSION
     */
	public function get($key) {
	  if(isset($_SESSION[SESSION_PREFIX . $key])) {
	    return $_SESSION[SESSION_PREFIX . $key];
	  }
	  else {
	    return null;
	  }
	}
	
    /**
     * remove - Set user field into $_SESSION
     */
	public function remove($key) {
	  if(isset($_SESSION[SESSION_PREFIX . $key])) {
	    unset($_SESSION[SESSION_PREFIX . $key]);
	  }
	  return $this;
	}
	
    /**
     * set - Set user field into $_SESSION
     */
	public function set($key,$val) {
	  $_SESSION[SESSION_PREFIX . $key] = $val;
	  return $this;
	}
	
    /**
     * set_push - Add value to user field array into $_SESSION
     */
	public function set_push($key,$val) {
	  if(!(isset($_SESSION[SESSION_PREFIX . $key]) && is_array($_SESSION[SESSION_PREFIX . $key]))) {
	    $_SESSION[SESSION_PREFIX . $key] = array();
	  }
	  $_SESSION[SESSION_PREFIX . $key][] = $val;
	  return $this;
	}
	
    /**
     * set_add - Math function to add value to existing user data value
     */
	public function set_add($key,$val) {
	  if(isset($_SESSION[SESSION_PREFIX . $key])) {
	    $_SESSION[SESSION_PREFIX . $key] += $val;
	  }
	  return $this;
	}
	
    /**
     * set_sub - Math function to subtract value to existing user data value
     */
	public function set_sub($key,$val) {
	  if(isset($_SESSION[SESSION_PREFIX . $key])) {
	    $_SESSION[SESSION_PREFIX . $key] -= $val;
	  }
	  return $this;
	}
	
    /**
     * set_default - Set user field into $_SESSION
     */
	public function set_default($key,$val) {
	  if($this->get($key) === null) {
	    $this->set($key,$val);
	  }
	  return $this;
	}
	
    /**
     * generate_password_hash - Generate SHA-512 hash of given plaintext password
     */
	public static function generate_password_hash($pass) {
      $password_hash = crypt($pass, '$6$rounds=5000$' . PASSWORD_SALT . '$');
	  $password_hash = substr($password_hash, 32, strlen($password_hash));
	  $runs = 10;
	  while($runs--) {
        $password_hash = crypt($password_hash, '$6$rounds=5000$' . $password_hash . '$');
		$password_hash = substr($password_hash, 32, strlen($password_hash));
	  }
	  return $password_hash;
	}
	
    /**
     * update_session - Update session values from database
     */
	public function update_session($sql) {
	  $this->reset_session();
	  $this->cookie->set('lsr', '1', SESSION_REFRESH_EXPIRY);
	  $row = $this->mysql->fetch($sql);
	  $this->mysql->free();
	  if($this->mysql->affected_rows === 1 && $row['status'] > 0) {
		foreach($row as $key=>$val) {
		  $this->set($key,$val);
		}
	    return true;
	  }
	  return false;
	}
	
    /**
     * login - Login user with given mysql $query and set $_SESSION data
     */
	public function login($sql,$pass=null) {
	  $row = $this->mysql->fetch($sql);
	  $this->mysql->free();
	  if($this->mysql->affected_rows === 1 && $row['status'] == 1 && ((isset($row['password']) && $row['password'] == $this->generate_password_hash($pass)) || !isset($row['password']))) {
	  
	    /* destroy current session and start new session if we are logging in for the first time */
	    if(isset($row['password'])) {
	      /* destroy current session */
		  $this->destroy_session();
	      /* start new session */
	      $this->reload(true);
		}
		
		/* set user variable key / value definitions from mysql statement */
		foreach($row as $key=>$val) {  
		  if(!in_array($key, $this->no_save_keys)) { $this->set($key,$val); }
		}
		
		/* set cookie and session key to signify logged in */
		$random = str_random(64);
		$login_hash = md5(AUTH_KEY . $random . LhpBrowser::getUserAgent());
		$this->cookie->set('lsr', '1', SESSION_REFRESH_EXPIRY);
		$this->cookie->set('ls', $random);
		$this->set("session_id", $login_hash);
		
		/* add to login history table */ 
		$this->mysql->insert('user_logins', array(
		  'date' => 'NOW()'
		), array(
		  'user_agent' => LhpBrowser::getUserAgent(),
		  'user_id' => $this->get('id'),
		  'ip' => LhpBrowser::getIp()
		),true);
		
		/* update session id */ 
		$this->mysql->update('users', "id=" . $this->get('id'), array(
		  'modified_date' => 'NOW()'
		), array(
		  'session_id' => $login_hash,
		  'modified_ip' => LhpBrowser::getIp()
		),true);
		
		/* good login */
	    return true;
		
	  }
	  
	  /* bad login */
	  return false;
	}
	
    /**
     * logout - Logout user and clear all $_SESSION data
     */
	public function logout() {
      /* update session id */ 
	  $this->mysql->update('users', "id=" . $this->get('id'), array(
	    'modified_date' => 'NOW()',
		'session_id' => 'NULL'
	  ), array(
		'modified_ip' => LhpBrowser::getIp()
      ),true);
	  /* destroy all cookies */
	  $this->cookie->delete($this->session_name, 'ls', 'lsr');
	  /* destroy session */
	  $this->destroy_session();
	}
	
    /**
     * destroy_session - Destroy current session
     */
	public function destroy_session() {
	  /* remove all session variables */ 
	  foreach($_SESSION as $key=>$val) {
	    unset($_SESSION[$key]);
	  }
	  /* destroy session */
	  session_destroy();
	}
	
    /**
     * reset_session - Remove all session variables but the ones required to be logged in, so we can properly refresh user session data from database
     */
	public function reset_session() {
	  foreach($_SESSION as $key=>$val) {
	    if(preg_match('/^' . SESSION_PREFIX . '/', $key) && $key != SESSION_PREFIX . 'id' && $key != SESSION_PREFIX . 'role' && $key != SESSION_PREFIX . 'session_id') {
	      unset($_SESSION[$key]);
		}
	  }
	}
	
    /**
     * setBypassValidation - Set flag to bypass validation check
     */
	public function setBypassValidation($bypass=false) {
	  $this->bypass_validation = $bypass;
	  return $this;
	}
	
    /**
     * getBypass - return bypass flag
     */
	public function getBypass() {
	  return $this->bypass_validation;
	}	
	
    /**
     * getSessionHash - return the login session id md5 hash
     */
	public function getSessionHash() {
	  return md5(AUTH_KEY . $this->cookie->get('ls') . LhpBrowser::getUserAgent());
	}
	
    /**
     * validate - Check to see whether or not we have a valid session_id based on AUTH_KEY and current session
	 * add mysql check like every 10 minutes to make sure that we have a valid user account
     */
	public function validate() {
	  return (($this->logged_in() && $this->get('role') > 0 && $this->get("session_id") === $this->getSessionHash()) || $this->bypass_validation);
	}
	
    /**
     * logged_in - Check to see whether or not we have a valid login hash
     */
	public function logged_in() {
	  return ($this->cookie->get('ls') !== null);
	}
	
    /**
     * need_to_refresh - check to see if we need to reload and set user session data
     */
	public function need_to_refresh() {
	  return ($this->cookie->get('lsr') === null);
	}
	
    /**
     * is_null - check to see if a user data key is null
     */
	public function is_null($key) {
	  return ($this->get($key) === null || $this->get($key) == '');
	}
	
  }
?>
