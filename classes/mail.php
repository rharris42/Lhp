<?php
  /**
   * Copyright 2015 Last Hit Productions, Inc.
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
