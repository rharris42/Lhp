<?php
  /**
   * Copyright 2015 Last Hit Productions, Inc.
   */

  /**
   * Class LhpForm
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  class LhpForm {

    /**
     * @var array - List of allowed request methods
     */
	private $valid_request_methods = array('post', 'get', 'head');

    /**
     * @var array - Array of uploaded files
     */
	private $files = array();

    /**
     * @var array - Key/Value pairs of form post data
     */
	private $form = array();

    /**
     * LhpForm - Returns a form object containing all form data including uploaded files
	 *  by default all html, javascript, and <.*?> tags are removed.
     *
     * @param bool $notags
     */
	public function __construct($notags=true) {
	  $method = LhpBrowser::getRequestMethod();
	  if(in_array($method, $this->valid_request_methods)) {
	    $in = ($method == 'post') ? $_POST : $_GET;
        foreach($in as $key=>$val) {
	      if(is_string($val)) {
            $this->parse($val,$notags)->set($key,$val);
          }
		  else if(is_array($val)) {
		    foreach($val as $key2=>$val2) {
              $this->parse($val2,$notags)->set($key,$val2,$key2);
	        }
          }
        }
	    if(!isset($this->form['offset']) || !is_int($this->form['offset'])) {
	      $this->set('offset', 0);
	    }
	    if(isset($_FILES)) {
	      foreach($_FILES as $key=>$val) {
		    $this->files[$key] = $val;
		    if(!is_uploaded_file($this->files[$key]['tmp_name'])) {
		      $this->files[$key] = null;
			}
		  }
	    }
	  }
	}

    /**
     * fields - Returns all form data keys
     *
     * @return array
     */
	public function fields() {
	  return array_keys($this->form);
	}

    /**
     * get - Returns form field value
     *
     * @param string $key
	 * @param string|null $key2
     *
     * @return mixed string|float|int|array
     */
	public function get($key,$key2=null) {
	  $val = null;
	  if(isset($this->form[$key])) {
	    if($key2 !== null && is_array($this->form[$key]) && isset($this->form[$key][$key2])) {
	      $val = $this->form[$key][$key2];
	    }
	    else {
	      $val = $this->form[$key];
		}
	  }
	  return $val;
	}

    /**
     * set - Sets form field value
     *
     * @param string $key
	 * @param string|null $key2
     *
     * @return object
     */
	public function set($key,$val,$key2=null) {
	  if($key2 !== null) {
	    if(isset($this->form[$key]) && is_array($this->form[$key])) {
		  $this->form[$key][$key2] = $val;
		}
		else {
		  $this->form[$key] = array($key2 => $val);
		}
	  }
	  else {
	    $this->form[$key] = $val;
	  }
	  return $this;
	}

    /**
     * getFile - Returns array containing information about the uploaded file or
	 *  returns the specific key value if specified
     *
     * @param string $key
	 * @param string|null $fkey
     *
     * @return mixed string|array|null
     */
	public function getFile($key,$fkey=null) {
	  if(isset($this->files[$key])) {
	    if($fkey !== null && isset($this->files[$key])) {
		  if(isset($this->files[$key][$fkey])) {
		    return $this->files[$key][$fkey];
		  }
		}
		else {
	      return $this->files[$key];
		}
	  }
	  return null;
	}

    /**
     * parse - Filters (optional param), stripcslashes (if needed) and converts
	 *   integer and float strings to actually int and float types
	 *   removes all unicode (ie. \xe2\x80\x8e)
     *
     * @param var &$val
	 * @param bool $notags
     *
     * @return object
     */
	private function parse(&$val,$notags=true) {
      if(ini_get('magic_quotes_runtime') == 1) {
        $val = stripcslashes($val);
      }
	  if($notags) {
	    $val = str_strip_tags($val);
	  }
	  // $val = convert_non_ascii($val);
	  $val = preg_replace('/\\\x[0-9a-f]{2,}/', '', $val);	 // removes perl string literals
	  while(preg_match('/\\\/', $val)) {
	    $val = preg_replace('/\\\/', '', $val);
	  }
      if(preg_match('/^\d+\.\d+$/', $val)) {
        $val = floatval($val);
      }
      else if(preg_match('/^\d+$/',$val)) {
        $val = intval($val);
      }
	  return $this;
	}

    /**
     * set_tokens - Set md5 hash token for form validation
     */
    public function set_tokens(&$template) {
      $hash = str_random(32);
	  $template->token_hash = $hash;
      $template->token = md5(LhpBrowser::getUserAgent() . FORM_KEY . session_id() . $hash);
    }

    /**
     * check_tokens - Check md5 hash token for form validation
     */
    public function check_tokens(&$cookie, &$user) {
	  if($this->get('action') == 'save' && !$user->getBypass()) {
	    return (LhpBrowser::getRequestMethod() == 'post' && (($cookie->get('SESSIONID') !== null && $this->get('token') == md5(LhpBrowser::getUserAgent() . FORM_KEY . $cookie->get('SESSIONID') . $this->get('token_hash'))) || $user->getBypass()));
	  }
	  else {
	    return true;
	  }
    }

  }
?>
