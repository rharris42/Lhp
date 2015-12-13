<?php
  /**
   * Copyright 2014 Last Hit Productions, Inc.
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
  require __DIR__ . '/lhp/lhp.php';
  
  /**
   * We only use one main try block to capture all exceptions for a centralized error control handler
   */
  try {
   
    /**
     * Load lhp classes
     */
    Lhp::Load(__DIR__ . "/lhp/classes");
  
    /**
     * Load lhp libraries
     */
    Lhp::Load(__DIR__ . "/lhp/lib");
	
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
      /* debugging */
      Lhp::Debug("user is logged in");
	  
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
