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
   * Class LhpMail
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  class LhpMail {
	
    /**
     * validate - Returns true if given email is valid, false otherwise
     */
    public static function validate($email) {
      return preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w+)+$/', $email) ? true : false;
    }
	
    /**
     * send - Sends email
     */
    public static function send($from=null,$from_name=null,$to=null,$subject=null,$ebody=null) {
	  if(self::validate($to) && $from && $from_name && $subject) {
	    $seed = str_random();
        $header = 'From: ' . $from_name . ' <' . $from . '>';
	    $header .= "\r\nReply-To: " . $from;
	    $header .= "\r\nContent-Type: multipart/alternative; boundary=\"_$seed"."_\"";
        $body = "\r\n\r\n";
        $body .= "\r\n";
        $body .= "--_$seed"."_\r\n";
        $body .= "Content-Type: text/plain\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n";
        $body .= "Content-Disposition: inline\r\n\r\n";
	    $body .= "$ebody\r\n";
	    $body .= "--_$seed"."_--\r\n";
	    return mail($to, $subject, $body, $header);
	  }
	  else {
	    return false;
	  }
    }
  }
?>
