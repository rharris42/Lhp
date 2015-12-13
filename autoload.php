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
   * Lhp library auto loader
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  
  /**
   * Class Lhp - System control class
   */
  class Lhp {
  
    /** 
     *  @var array - List of required classes 
     */ 
    private static $required_classes = array(
      'LhpBrowser',
      'LhpCookie',
      'LhpDate',
      'LhpFile',
      'LhpForm',
	  'LhpMail',
      'LhpMysql',
      'LhpRequest',
      'LhpRoute',
      'LhpTemplate',
	  'LhpUser'
	);
  
    /** 
     *  load - Require *.php library files in given path 
	 *
	 * @throws exception
     */ 
	public static function Load($path) {
	  // print "library dir = $path<BR>";
	  if(is_readable($path)) {
        foreach(glob("$path/*.php") as $file) {
          if(!is_readable($file)) {
	        throw new Exception("Unable to load library file: $file - File is not readable or does not exist.");
	      }
	      require $file;
        }
      }
      else {
        throw new Exception("Unable to load library directory: $path - Directory is not readable or does not exist.");
      }
	}
	
    /** 
     *  CheckConfig - Checks lhp configuration files and make sure we have loaded all necessary classes
	 *
	 * @throws exception 
     */ 
	public static function CheckConfig() {
	  foreach(static::$required_classes as $class) {
        if(!class_exists($class, false)) {
		  $file = strtolower(preg_replace('/^Lhp/', '', $class));
		  throw new Exception("Failed to load required class: $class - File missing: " . __DIR__ . "/system/classes/$file.php");
		}
	  }
	  if(!defined('DOMAIN_PATH')) {
	    throw new Exception("Constant DOMAIN_PATH is not defined in  " . __DIR__ . "/system/lib/config.php");
	  }
	  if(!is_readable(DOMAIN_PATH)) {
	    throw new Exception("DOMAIN_PATH does not exist or is not readable: " . DOMAIN_PATH);
	  }
	  if(!defined('TEMP_DIRECTORY')) {
	    throw new Exception("Constant TEMP_DIRECTORY is not defined in  " . __DIR__ . "/system/lib/config.php");
	  }
	  if(!is_readable(TEMP_DIRECTORY)) {
	    throw new Exception("TEMP_DIRECTORY does not exist or is not readable: " . TEMP_DIRECTORY);
	  }
	}
	
    /** 
     *  CheckDomainConfig - Checks user domain configuration files and make sure we have loaded all necessary classes
	 *
	 * @throws exception 
     */ 
	public static function CheckDomainConfig() {
      if(!defined('MYSQL_USER') || !defined('MYSQL_PASS') || !defined('MYSQL_DB') || !defined('MYSQL_HOST') || !defined('MYSQL_PORT')) {
        throw new Exception("Please check the configuration file and make sure you have set the MYSQL_ constant definitions: " . DOMAIN_PATH . "/lib/config.php");
      }
	  if(!defined('TEMPLATE_DIR')) {
	    throw new Exception("Constant TEMPLATE_DIR is not defined in " . DOMAIN_PATH . "/lib/config.php");
	  }
	  if(!is_readable(TEMPLATE_DIR)) {
	    throw new Exception("TEMPLATE_DIR does not exist or is not readable: " . TEMPLATE_DIR);
	  }
	}
  
    /**
     * error - prints exception error
     */
	public static function Debug($msg) {
	  if(DEBUG) {
	    print $msg;
	    print CMD_LINE ? "\n" : "<BR>";
	  }
	}
  
    /**
     * error - prints exception error
     */
	public static function ExceptionError($e) {
	  if(DEBUG) {
	    print "\n\nError: " . $e->getMessage() . "\n\n";
	    exit;
	  }
	  else if(!CMD_LINE) {
	    LhpBrowser::redirectToUrl('http://www.' . DOMAIN);
	  }
	}
  }
  
  /**
   * We only use one main try block to capture all exceptions for a centralized error control handler
   */
  try {
   
    /**
     * Load lhp classes
     */
    Lhp::Load(__DIR__ . "/system/classes");
  
    /**
     * Load lhp libraries
     */
    Lhp::Load(__DIR__ . "/system/lib");
	
    /**
     * Check lhp configuration
     */
    Lhp::CheckConfig();
  
    /**
     * Load domain specific classes
     */
    Lhp::Load(DOMAIN_PATH . "/classes");
   
    /**
     * Load domain specific libraries
     */
    Lhp::Load(DOMAIN_PATH . "/lib");
	
    /**
     * Check domain specific configuration
     */
    Lhp::CheckDomainConfig();
	
    /**
     * Check to make sure we are on www 
     */
    (LhpBrowser::getServerName() == 'www.' . DOMAIN) or
      LhpBrowser::redirectToUrl('http://www.' . DOMAIN . LhpBrowser::getRequestUri(), 404);
	
    /**
     * Initiate LhpForm with filtered form data (true)
     * Set default form values
     */
    $form = new LhpForm(true);
    
    /**
     * Initiate LhpCookie for use with current domain and all sub domains (ie '.domain.com')
     */
    $cookie = new LhpCookie('.' . DOMAIN);
    
    /**
     * Initiate LhpMysql - To save resources, we connect to the mysql server 
     * only when a query is executed, not on every request
     */
    $mysql = new LhpMysql(MYSQL_USER, MYSQL_PASS, MYSQL_DB, MYSQL_HOST, MYSQL_PORT);
    
    /**
     * Load user data using sessions
	 * Set default session data
     */
    $user = new LhpUser($cookie, $mysql);
    $user->set_default('id', 1);
    $user->set_default('role', 0);
	
    /**
     * Initiate template object.
     * Set default template placeholder values
     */
    $template = new LhpTemplate();
	
    /**
     * Set URI variable for routing
     */
    $uri = LhpBrowser::getRequestUri();
	$uri = preg_replace('/\?.+$/', '', $uri);
	
    /**
     * check to see if we have logged in cookie hash but no session
     * then auto log in the user if possible  
     * 
     */
    if($user->logged_in()) {	  
      /* auto login user if we have valid session id */
      if(!$user->validate()) {
        $session_id = $user->getSessionHash();
        if($user->login(UserRoute::$mysql_relogin_query . "users.session_id='" . $mysql->real_escape($session_id) . "' limit 1")) {
	      $user->setBypassValidation(true);
	    }
	    else {
	      $uri = 'logout';
	    }
	  }
	  /* refresh user data as needed */
	  else if($user->validate() && $user->need_to_refresh() && !$user->update_session(UserRoute::$mysql_relogin_query . "users.id=" . $user->get('id') . " limit 1")) {
	    $uri = 'logout';
	  }
    }
	
    /**
     * Set or check form tokens depending on form action
     */
	if($form->get('action') == 'save' && !$user->getBypass() && !check_form_token($form, $cookie, $user)) {
	  throw new Exception("Form hash tokens do not match!");
	}
	else {
	  set_form_token($template);
	}
   
    /**
     * Load domain specific executables
	 * Files are loaded in alphabetical order
     */
    foreach(glob(DOMAIN_PATH . "/*.php") as $file) {
      if(!is_readable($file)) {
	    throw new Exception("Unable to load library file: $file - File is not readable.");
	  }
	  require $file;
    }
    
    /** 
     * Route is based upon current request uri
     * Routing is executed by UserRoute::$route()
	 * Uri is stripped of all characters except for letters and numbers to perform php function call
	 * example: /some-page would execute the php function UserRoute::somepage()
     */
    $routing = $template->findRoute($uri);
    if($routing !== false && is_callable($routing)) {
      $routing($template,$mysql,$user,$cookie,$form);
    }
	else {
	  Lhp::Debug("NO ROUTING FUNCTION FOUND");
	}
	
    /**
     * Redirect to URI if applicable
     */
	if($template->getRedirect() !== null) {
	  $mysql->disconnect();
	  LhpBrowser::redirectToUrl($template->getRedirect(), 302);
	}
	
    /**
     * Further debugging information
	 * Cookies, session cookie values
     */
	if(DEBUG) {
	  //foreach($_COOKIE as $key=>$val) {
	  //  print "$key = $val<BR>";
	  //}
	  //$info = session_get_cookie_params();
	  //foreach($info as $key=>$val) {
	  //  print "$key = $val<BR>";
	  //}
	}
    
    /**
     * Load template file, parse template placeholders and print to screen
     * Template file is based upon current request uri (route) (/somepage.html or /somepage = somepage.tpl.htm)
     */
	if(!$template->getAjax()) {
      $template->loadTemplateFile($user);
      $template->parseBlocks();
      $template->parseAll();
      $template->show();
	}
    
    /**
     * Disconnect from mysql as needed
     */
    $mysql->disconnect();
	
  }
  catch (Exception $e) {
    Lhp::ExceptionError($e);
  }
?>
