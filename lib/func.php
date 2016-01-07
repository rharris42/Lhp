<?php
  /**
   * Copyright 2015 Last Hit Productions, Inc.
   */

  /**
   * Lhp function library
   * @author Robert Harris <robert.t.harris@gmail.com>
   */

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

?>
