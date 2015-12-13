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
   * Class LhpBrowser - Used in static context
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  class LhpBrowser {
	
    /**
     * via - Returns server variable HTTP_VIA, some devices use this like HTTP_USER_AGENT
	 *
	 * @return string
     */
	public static function getVia() {
	  return isset($_SERVER['HTTP_VIA']) ? $_SERVER['HTTP_VIA'] : '';
	}
	
    /**
     * getUserAgent - Returns name of the user agent / browser that the current request came from or
	 *  returns boolean for specific user agent $type
	 *
	 * @param string|null $type
	 *
	 * @return string|bool
     */
	public static function getUserAgent($type=null) {
	  $ua = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
	  if($type == 'facebook') {
	    return preg_match('/facebookexternalhit/i', $ua);
	  }
	  else if($type == 'googlebot') {
	    return preg_match('/googlebot/i', $ua);
	  }
	  else if($type == 'mobile') {
	    return preg_match('/(ipod|iphone|ipad|danger hiptop|blackberry|android)/i', $ua) || preg_match('/(ipod|iphone|ipad|danger hiptop|blackberry|android)/i', self::getVia());
	  }
	  else if($type == 'apple') {
	    return preg_match('/(ipod|iphone|ipad)/i', $ua) || preg_match('/(ipod|iphone|ipad)/i', self::getVia());
	  }
	  else if($type == 'android') {
	    return preg_match('/android/i', $ua) || preg_match('/android/i', self::getVia());
	  }
	  else {
	    return $ua;
	  }
	}
	
    /**
     * getRequestUri - Returns request url
	 *
	 * @return string
     */
	public static function getRequestUri() {
	  return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	}
	
    /**
     * getReferer - Returns referring url
	 *
	 * @return string
     */
	public static function getReferer() {
	  return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	}
	
    /**
     * getReferDomain - Returns domain of referring url
	 *
	 * @return string
     */
	public static function getReferDomain() {
	  return preg_replace('/^https?:\/\/([^\/]+).*$/i', "$1", self::getReferer());
	}
	
    /**
     * getIp - Returns ip of current user agent
	 *
	 * @return string
     */
	public static function getIp() {
	  return isset($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
	}
	
    /**
     * getServerName - Returns server name (ie, www.domain.com)
	 *
	 * @return string
     */
	public static function getServerName() {
      $domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
	  $domain = (empty($domain) && isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : $domain;
	  return $domain;
	}
	
    /**
     * getDomain - Returns domain of website (ie, www.domain.com)
	 *
	 * @return string
     */
	public static function getDomain() {
      $domain = self::getServerName();
	  $domain = preg_replace('/^(?:(?:.*?\.)*?)((?:[\w\-]+?)\.[a-z]+)$/', "$1", $domain);
	  return $domain;
	}
	
    /**
     * isSecure - Returns true if we are on https
	 *
	 * @return bool
     */
	public static function isSecure() {
	  return (isset($_SERVER['http']) &&  strtolower($_SERVER['http']) == 'on');
	}
	
    /**
     * getDocumentRoot - Returns document root path
	 *
	 * @return bool
     */
	public static function getDocumentRoot() {
	  return isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : './';
	}
	
    /**
     * getRequestMethod - Returns request method of user agent
     *
     * @return string|null
     */
	public static function getRequestMethod() {
	  return isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : null;
	}
	
    /**
     * redirectToUrl - redirects browser to $url with header of $type
	 *
	 * @param string $url
	 * @param int $type
	 * @param bool $nocache
	 *
     */
	public static function redirectToUrl($url='',$type='404',$nocache=false) {
	  if($nocache) {
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Pragma: no-cache');
	  }
      header("HTTP/1.1 $type " . LhpRequest::$default_response_codes[$type]);
	  $url = empty($url) ? "http://www.".self::getDomain()."/" : $url;
	  if(DEBUG) {
	    print "redirecting...<BR>\n";
	    exit;
	  }
	  header("Location: $url");
	  exit;
	}
	
  }
?>
