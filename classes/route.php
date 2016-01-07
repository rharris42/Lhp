<?php
  /**
   * Copyright 2015 Last Hit Productions, Inc.
   */

  /**
   * Class LhpRoute - Used in static context; Handles all magically called routes
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  class LhpRoute {

    /**
     * __callStatic - Magic function that handles calls to user defined routes
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
