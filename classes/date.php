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
   * Class LhpDate
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  class LhpDate {
	
    /**
     * @var array Key values based on date() are associated with strftime() format values
     */
	private static $formats = array(
	  'lt' => '%r',       // local time 08:42:17 AM
	  'ld' => '%x',       // local date 09/12/14
	  'hh' => '%l',       // hour 1 - 12 without leading zero
	  'dd' => 'd',        // 2 digit day without leading zero
	  'mm' => 'm',        // 2 digit month without leading zero
	  'ii' => 'i',        // 2 digit minute without leading zero
	  'MM' => '%b',       // month abbreviated
	  'W' => '%A',        // weekday full name
	  'w' => '%a',        // weekday abbreviated
	  'M' => '%B',        // full month
	  'Y' => '%Y',        // 4 digit year
	  'y' => '%y',        // 2 digit year
	  'm' => '%m',        // 2 digit month with leading zero
	  'd' => '%d',        // 2 digit day with leading zero
	  'H' => '%H',        // 0-23 2 digit hour
	  'h' => '%I',        // 1-12 2 digit hour
	  'i' => '%M',        // 2 digit minute with leading zero
	  's' => '%S',        // 2 digit seconds with leading zero
	  'e' => '%p'         // AM or PM
	);
	
    /**
     * LhpDate - Returns date object to handle date/time formatting
	 * 
     * @param string|null $format
     * @param string|null $timestamp
     */
	public function __construct() {
	
	}
	
    /**
     * check - Returns true if $datetime based on $format using date() formatting
	 * 
     * @param string $datetime
     * @param string $format
	 *
	 * @return bool
     */
    public static function check($datetime, $format='Y-m-d H:i:s') {
	  return (date($format, strtotime($datetime)) == $datetime);
    }
	
    /**
     * format - Returns formatted string based on timestamp or datetime string
	 *  if $timestamp is null, then current time() is used
	 * 
     * @param string $format
     * @param string $datetime
	 *
	 * @return string
     */
	public static function format($format='Y-m-d h:i:s', $timestamp=null) {
	  if($timestamp === null) {
	    $timestamp = time();
      }
	  if(!preg_match('/^\d+$/', $timestamp)) {
	    $timestamp = strtotime($timestamp);
	  }
	  foreach(static::$formats as $key => $val) {
	    if(!preg_match('/%/', $val)) {
		  $val2 = date($val, $timestamp);
		  if($key == "$val$val") {
		    $val2 = ltrim($val2, '0');
	      }
		  $val = $val2;
		}
		$format = preg_replace('/'.$key.'/', $val, $format);
	  }
	  $format = strftime($format, $timestamp);
	  $format = preg_replace('/\s{2,}/', ' ', $format);
	  return $format;
	}
	
    /**
     * getSeconds - Returns number of seconds from time format: \d:\d\d (ie. 2:10)
	 *  if $timestamp is null, then current time() is used
	 * 
     * @param string $time
	 *
	 * @return int
     */
	public static function getSeconds($time) {
      preg_match('/^(\d+):(\d+)$/', $time, $matches);
	  $mins = isset($matches[1]) ? intval($matches[1]) : 0;
	  $secs = isset($matches[2]) ? intval($matches[2]) : 0;
	  if($mins && $secs) { $time = ($mins * 60) + $secs; }
	  else if($secs) { $time = $secs; }
	  return $time;
	}
	
  }
?>
