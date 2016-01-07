<?php
  /**
   * Copyright 2015 Last Hit Productions, Inc.
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
     * @var string - Default re-login query
     */
	private static $mysql_relogin_query = "select users.id,users.email,users.status,users.confirmed,users.api_id,users.create_date,users.role,user_settings.* from users inner join user_settings on users.id=user_settings.user_id where ";

    /**
     * @var string - Default login query
     */
	private static $mysql_login_query = "select users.id,users.email,users.status,users.confirmed,users.api_id,users.role,users.create_date,users.password,user_settings.* from users inner join user_settings on users.id=user_settings.user_id where ";

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
      $session_id = ($this->cookie->get($this->session_name) !== null && !$force) ? $this->cookie->get($this->session_name) : str_random(64);
	  session_name($this->session_name);
	  session_set_cookie_params($expiry, '/', '.' . DOMAIN);
	  session_id($session_id);
      session_start();
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
	public function update_session($id) {
	  $this->reset_session();
	  $this->cookie->set('lsr', '1', SESSION_REFRESH_EXPIRY);
	  $sql = static::$mysql_relogin_query . "users.id=$id limit 1";
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
	public function login($data) {
	  $pass = null;
	  $sql = null;
	  /* login using email and password, usually intial login to site */
	  if(isset($data['email']) && isset($data['password']) && LhpMail::validate($data['email']) && strlen($data['password']) >= 6 && strlen($data['password']) <= 32) {
	    $sql = static::$mysql_login_query . "users.email='" . $this->mysql->real_escape($data['email']) . "' limit 1";
		$pass = $data['password'];
	  }
	  /* login using user id, usually used when a user confirms their email address */
	  else if(isset($data['id']) && is_int($data['id'])) {
	    $sql = static::$mysql_relogin_query . "users.id=" . $data['id'] . " limit 1";
	  }
	  /* relogin user when php session has expired, but we have a session cookie */
	  else if(isset($data['session_id'])) {
	    $sql = static::$mysql_relogin_query . "users.session_id='" . $this->mysql->real_escape($data['session_id']) . "' limit 1";
	  }
	  if($sql !== null) {
	    $row = $this->mysql->fetch($sql);
	    $this->mysql->free();
	    if($this->mysql->affected_rows === 1 && $row['status'] == 1 && (($pass !== null && $row['password'] == $this->generate_password_hash($pass)) || $pass === null)) {

	      /* destroy current session and start new session if we are logging in for the first time */
	      if($pass !== null) {
	        /* destroy current session */
		    $this->destroy_session();
	        /* start new session */
	        $this->reload(true);
		  }

		  /* set user variable key / value definitions from mysql statement */
		  foreach($row as $key=>$val) {
		    if(!in_array($key, $this->no_save_keys)) { $this->set($key,$val); }
		  }

		  /* set session id cookie and to signify logged in */
		  $login_hash = $this->setSessionHash();

		  /* set session refresh flag cookie */
		  $this->cookie->set('lsr', '1', SESSION_REFRESH_EXPIRY);

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

		  /* set bypass user validation if we are refreshing session */
	      if(isset($data['session_id'])) {
	        $this->setBypassValidation(true);
		  }

		  /* good login */
	      return true;

		}
	  }

	  /* add to user bad logins table */
	  if(isset($data['email']) && isset($data['password'])) {
        $mysql->insert('user_bad_logins',array(
		  'date' => 'NOW()'
	    ), array(
		  'email' => $form->get('email'),
		  'password' => $form->get('password'),
		  'ip' => LhpBrowser::getIp(),
		  'user_agent' => LhpBrowser::getUserAgent()
		),true);
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
     * generate_confirm_email_link - generate link to confirm user's email address
     *   user must already be logged in for this to work
	 *
	 * @param object $form
	 *
     */
	public function generate_confirm_email_link() {
	  $row = $this->mysql->fetch("select create_date,email from users where id=" . $this->get('id') . " limit 1");
	  $this->mysql->free();
	  if($this->mysql->affected_rows === 1) {
		return array("/confirm?id=" . urlencode($this->generate_password_hash($row['email'] . $row['create_date'])) . "&email=" . urlencode($row['email']), $row['email']);
	  }
	  return array(null,null);
	}

    /**
     * confirm_email - confirm users email address
     *
	 * @param object $template
	 *
     */
	public function confirm_email($email,$id) {
	  if(LhpMail::validate($email) && $id !== null) {
        $row = $this->mysql->fetch("select confirmed,id,create_date from users where email='" . $this->mysql->real_escape($email) . "' limit 1");
		$this->mysql->free();
		if($this->mysql->affected_rows === 1 && !$row['confirmed'] && $this->generate_password_hash($email . $row['create_date']) == $id) {
		  $this->mysql->update('users', "id=" . $row['id'], array(), array(
		    'confirmed' => '1'
		  ),true);
		  if($this->validate()) {
		    $this->set('confirmed', 1);
		  }
		  else {
			$this->login(array('id'=>$row['id']));
		  }
		  return true;
		}
	  }
	  return false;
	}

    /**
     * create - create new user account with given email and password
     *
	 * @param object $template
	 *
     */
	public function create($email,$pass) {
	  $last_insert_id = null;
	  if(LhpMail::validate($email) && strlen($pass) >= 6 && strlen($pass) <= 32) {
        $this->mysql->insert('users',
		  array(
			'create_date' => 'NOW()'
		  ),
		  array(
            'status' => 1,
			'role' => 2,
			'email' => $email,
			'password' => $this->generate_password_hash($pass),
			'api_id' => $this->generate_password_hash($email.time()),
            'create_ip' => LhpBrowser::getIp()
		  ),
		  true
		);
		/* get new user id if successfull */
		if($this->mysql->affected_rows === 1) {
		  $last_insert_id = $this->mysql->getLastId();
		}
	  }
	  return $last_insert_id;
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
     * getSessionHash - return the login session id
     */
	public function getSessionHash() {
	  return $this->generate_password_hash(AUTH_KEY . $this->cookie->get('ls') . LhpBrowser::getUserAgent());
	}

    /**
     * setSessionHash - set session hash
     */
	public function setSessionHash() {
      $random = str_random(64);
	  $this->cookie->set('ls', $random);
	  $login_hash = $this->generate_password_hash(AUTH_KEY . $random . LhpBrowser::getUserAgent());
	  $this->set("session_id", $login_hash);
	  return $login_hash;
	}

    /**
     * validate - Check to see whether or not we have a valid user logged in based on AUTH_KEY, user agent and session cookies
     */
	public function validate() {
	  return (($this->logged_in() && $this->get('role') > 0 && $this->get("session_id") === $this->getSessionHash()) || $this->bypass_validation);
	}

    /**
     * logged_in - Check to see whether or not we have a valid login cookie
     */
	public function logged_in() {
	  return ($this->cookie->get('ls') !== null);
	}

    /**
     * need_to_refresh - check to see if we need to reload and reset user session data
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
