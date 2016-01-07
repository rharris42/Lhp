<?php
  /**
   * Copyright 2015 Last Hit Productions, Inc.
   */

  /**
   * Configuration constants
   * @author Robert Harris <robert.t.harris@gmail.com>
   *
   * The following constants you are going to want to make changes to match your server's configuration
   * TEMP_DIRECTORY, MOD, DOMAIN_PATH
   */

  /**
   * @const string - System version
   */
  define('LHP_VERSION', '2.0');

  /**
   * @const bool - Are we using command line?
   */
  defined('CMD_LINE') or
    define('CMD_LINE', false);

  /**
   * @const int - File permission to be used when user uploads file
   */
  define('MOD', 0775);

  /**
   * @const string - Current domain without www. (ie domain.com)
   */
  defined('DOMAIN') or
    define('DOMAIN', LhpBrowser::getDomain());

  /**
   * @const string - Directory path of domain files without trailing /
   */
  define('DOMAIN_PATH', dirname(dirname(__FILE__)) . '/domains/' . DOMAIN);

  /**
   * @const string - Full path to temp directory (used for uploading files, plugin installations and more)
   */
  define('TEMP_DIRECTORY', dirname(dirname(__FILE__)) . '/domains/' . DOMAIN . '/temp/');

?>
