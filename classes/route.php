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
   * Class LhpRoute - Used in static context; Handles all magically called routes
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  class LhpRoute {
	
    /**
     * __callStatic - Magic function that handles calls to all undeclared static methods 
     *
     */
    public static function __callStatic($route, $arguments) {
	  if(class_exists('UserRoute', false)) {
	    $methods = get_class_methods('UserRoute');
		if(in_array($route, $methods)) {
		  return (function(&$template, &$mysql, &$user, &$cookie, &$form){
		    $route = $template->getRoute();
			UserRoute::$route($template, $mysql, $user, $cookie, $form);
	      });
		}
	  }
      return false;
    }
	
  }
?>
