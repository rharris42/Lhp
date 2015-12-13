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
   * Lhp function library
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  
  /**
   * set_form_token - Set md5 hash token for form validation
   */
  function set_form_token(&$template) {
    $hash = str_random(32);
	$template->token_hash = $hash;
    $template->token = md5(LhpBrowser::getUserAgent() . FORM_KEY . session_id() . $hash);
  }
  
  /**
   * check_form_token - Check md5 hash token for form validation
   */
  function check_form_token(&$form, &$cookie, &$user) {
	return (LhpBrowser::getRequestMethod() == 'post' && (($cookie->get('SESSIONID') !== null && $form->get('token') == md5(LhpBrowser::getUserAgent() . FORM_KEY . $cookie->get('SESSIONID') . $form->get('token_hash'))) || $user->getBypass()));
  }
    
  /**
   * str_random - Returns randomly generated string based on $char list
   *   having a strlen() of $size. max $size is 128
   *
   * @param int $size
   * @param int $chars
   *
   * @return string 
   */
  function str_random($size=10,$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
	$str = '';
	$charlen = strlen($chars) - 1;
	if($size > 128) { $size = 128; }
	while(strlen($str) < $size) {
	  $index = mt_rand(0, $charlen);
	  $str .= $chars[$index];
	}
	return $str;
  }
   
  /**
   * str_is_md5 - Returns double occurences of $char
   *
   * @param string $str
   *
   * @return bool
   */
  function str_is_md5($str) {
    return preg_match('/^[a-f0-9]{32}$/', $str);
  }
   
  /**
   * str_remove_double - Returns double occurences of $char
   *
   * @param string $str
   * @param string $char
   *
   * @return string
   */
  function str_remove_double($str,$char=' ') {
    while(preg_match("/$char$char/", $str)) {
	  $str = preg_replace("/$char{2,}/", $char, $str);
	}
    return $str;
  }
   
  /**
   * str_strip_tags - Returns randomly generated string based on $char list
   *   having a strlen() of $size
   *
   * @param string $str
   *
   * @return string
   */
  function str_strip_tags($str) {
    $str = preg_replace_callback('/<(script|style).*?>(.*?)<\/\1>/si', function($m){
      return " ";
    }, $str);
    $str = preg_replace('/<.+?>/i', ' ', $str);
	$str = str_remove_double($str);
    $str = trim($str);
    return $str;
  }
   
  /**
   * str_strip_non_standard - Returns string stripped of non-standard characters based on $type
   *
   * @param string $str
   * @param int $type
   *
   * @return string
   */
  function str_strip_non_standard($str, $type=1) {
    if($type === 1) {
      $str = preg_replace('/[^a-zA-Z0-9\!@#\$%\^&\*\(\)_\-\+\=\{\}\[\];\:\'",\.\? \r\n\t]/', '', $str);
	}
    else if($type === 2) {
      $str = preg_replace('/[^a-zA-Z0-9\-\'\. ]/', '', $str);
	}
    return $str;
  }
    
  /**
   * str_urlencode - Provides one way encoding of a string of words for a nicely formatted url 
   *
   * @param string $str
   *
   * @return string
   */
  function str_urlencode($str) {
    $str = urlencode($str);
    $str = preg_replace('/\+/', '%20', $str);
	return $str;
  }
   
  /**
   * convert_non_ascii - Convert non ascii characters into ascii characters 
   *
   * @param string $str
   *
   * @return string
   */
  function convert_non_ascii($str) {
	$str = preg_replace('/[‘’]/', "'", $str);
	$str = preg_replace('/[“”]/', '"', $str);
	$str = preg_replace('/…/', '...', $str);
	$str = preg_replace('/–/', '-', $str);
    $str = preg_replace('/(?:â€˜|â€™)/', "'", $str);
    $str = preg_replace('/(?:â€“)/', "-", $str);
    $str = preg_replace('/(?:â€¦)/', "...", $str);
    $str = preg_replace('/(?:â€œ|â€)/', '"', $str);
	$str = preg_replace('/[ÀÁÂÃÄÅ]/', 'A', $str);
	$str = preg_replace('/[ÈÉÊË]/', 'E', $str);
	$str = preg_replace('/[ÌÍÎÏ]/', 'I', $str);
	$str = preg_replace('/Ñ/', 'N', $str);
	$str = preg_replace('/[ÒÓÔÕÖ]/', 'O', $str);
	$str = preg_replace('/[ÙÚÛÜ]/', 'U', $str);
	$str = preg_replace('/Ý/', 'Y', $str);
	$str = preg_replace('/[àáâãäå]/', 'a', $str);
	$str = preg_replace('/[èéêë]/', 'e', $str);
	$str = preg_replace('/[ìíîï]/', 'i', $str);
	$str = preg_replace('/ñ/', 'n', $str);
	$str = preg_replace('/[òóôõöø]/', 'o', $str);
	$str = preg_replace('/[ùúûü]/', 'u', $str);
	$str = preg_replace('/[ýÿ]/', 'y', $str);
	$newstr = '';
	for($x=0, $y=strlen($str); $x<$y; $x++) {
	  if(ord($str[$x]) >= 32 && ord($str[$x]) <= 126 || (ord($str[$x]) == 9 || ord($str[$x]) == 10 || ord($str[$x]) == 13)) {
	    $newstr .= $str[$x];
	  }
	}
	$str = $newstr;
	return $str;
  }
  
  /**
   * is_number - Returns true or false if $val is a string or number containing ONLY digits /^\d+$/ 
   *
   * @param string $val
   *
   * @return bool
   */
  function is_number($val) {
    return preg_match('/^\d+$/', $val);
  }
  
?>
