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
   * Configuration constants
   * @author Robert Harris <robert.t.harris@gmail.com>
   * 
   * The following constants you are going to want to make changes to match your server's configuration
   * TEMP_DIRECTORY, GRP, MOD, DOMAIN_PATH
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
  define('DOMAIN_PATH', '/web/doc/path');
   
  /**
   * @const string - Full path to temp directory (used for uploading files, plugin installations and more)
   */ 
  define('TEMP_DIRECTORY', '/your/temp/directory/');

?>
