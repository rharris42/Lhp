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
   * Class LhpCookie
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  class LhpCookie {
  
    /**
     * @var string - Path of cookie
     */
	private $path;
	
    /**
     * @var string - Domain of cookie
     */
	private $domain;
	
    /**
     * LhpCookie - Returns cookie object to handle reading, writing and deleting cookies
     *  cookies can be created upon object creating using $cookies array consisting of name, value, expiration
	 * 
     * @param string|null $path
     * @param string $path
     * @param array $cookies
     */
	public function __construct($domain=null,$path='/',$cookies=array()) {
	  $this->domain = $domain;
	  $this->path = $path;
	  foreach($cookies as $cookie) {
	    $days = isset($cookie[2]) ? $cookie[2] : COOKIE_EXPIRY;
	    $this->set($cookie[0],$cookie[1],$days);
	  }
    }
	
    /**
     * fields - Returns array of all keys found in $_COOKIE
	 *
	 * @return array
     */
	public function fields() {
	  return array_keys($_COOKIE);
    }
	
    /**
     * get - Returns cookie value of given $name if available
	 *
	 * @param string $name
	 *
	 * @return string:null
     */
	public function get($name) {
      return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null; 
    }
	
    /**
     * set - Sets cookie $name based on value $value with an expiration of time() + $days
	 *
	 * @param string $name
	 * @param string $value
	 * @param int $days
	 *
	 * @return object
     */
	public function set($name,$value,$expires=COOKIE_EXPIRY) {
	  if($expires > 0) {
        setcookie($name, $value, time()+$expires, $this->path, $this->domain);
	  }
	  else {
	    setcookie($name, $value, 0, $this->path, $this->domain);
	  }
	  return $this;
    }
	
    /**
     * delete - Sets cookie for deletion using given arguments 
	 *
	 * @param string $name
	 * @param string $name2
	 * @param string $name3
	 * @param etc..
	 *
	 * @return object
     */
	public function delete() {
	  foreach(func_get_args() as $name) {
        setcookie($name, '', 0, $this->path, $this->domain);
	  }
	  return $this;
    }
	
  }
?>
