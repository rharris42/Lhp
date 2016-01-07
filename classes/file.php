<?php
  /**
   * Copyright 2015 Last Hit Productions, Inc.
   */

  /**
   * Class LhpFile
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  class LhpFile {

    /**
     * LhpFile - Object is used in static context
     *
     * @param string $path
     */
	public function __construct($path=null) {

	}

    /**
     * info - Retrieves file info
	 *
     * @param string $path
     * @param string $key (dirname, basename, extension (if any), and filename)
	 *
	 * @return array|string
     */
	public static function info($path=null,$key=null) {
	  if(file_exists($path)) {
	    $info = pathinfo($path);
		if($key !== null && isset($info[$key])) {
		  return $info[$key];
		}
		else {
		  return $info;
		}
	  }
	  return null;
	}

    /**
     * getSize - Returns size of file
	 *
	 * @return int
     */
	public static function getSize($path=null) {
	  $size = 0;
	  if(!is_file($path)) {
	    throw new Exception("File does not exist: $path");
	  }
	  else if(!is_readable($path)) {
	    throw new Exception("File is not readable: $path");
	  }
	  else if(!$size = filesize($path)) {
        throw new Exception("Could not get size of file: $path");
	  }
	  return $size;
	}

    /**
     * get - Sets contents of file for use with $this->contents
	 *
	 * @param string $path
	 *
	 * @return object
	 *
	 * @throws Exception
     */
	public static function get($path=null) {
	  $contents = false;
	  if(!is_file($path)) {
	    throw new Exception("File does not exist: $path");
	  }
	  else if(!is_readable($path)) {
	    throw new Exception("File is not readable: $path");
	  }
	  else if(!$contents = file_get_contents($path)) {
        throw new Exception("Could not get contents of file: $path");
	  }
	  return $contents;
	}

    /**
     * put - Overwrite existing, create new file, or append to existing with contents
	 *   also uses file locking flags (have to further test and check how it works)
	 *
	 * @param string $path
	 * @param string $contents
	 * @param bool $append
	 *
	 * @return object
	 *
	 * @throws Exception
     */
	public static function put($path=null,$contents,$append=false) {
	  $append_flag = $append ? FILE_APPEND | LOCK_EX : LOCK_EX;
	  $dirname = self::info($path, 'dirname');
	  if($dirname === null && preg_match('/^(.*?)\/([^\/]+)$/', $path, $matches)) {
	    $dirname = $matches[1];
	  }
	  if(!is_dir($dirname)) {
	    throw new Exception("Directory does not exist: [ $dirname ] from path: $path");
	  }
	  else if(!is_writable($dirname)) {
		throw new Exception("You do not have permission to write to directory: $dirname");
      }
	  else if(file_exists($path) && !is_writable($path)) {
		throw new Exception("You do not have permission to write to file: $path");
	  }
	  else if(!file_put_contents($path, $contents, $append_flag)) {
	    throw new Exception("Could not write to file: $path");
      }
	}

    /**
     * grp - Change grp of file or directory
	 *
	 * @param string $path
	 * @param int $mod
	 *
	 * @throws Exception
     */
	public static function grp($path=null,$grp=null) {
	  $dirname = self::info($path, 'dirname');
	  if(!is_writable($dirname)) {
	    throw new Exception("$dirname is not writable.");
	  }
	  else if(!is_writable($path)) {
	    throw new Exception("$path is not writable.");
	  }
	  else if(!function_exists('chgrp')) {
	    throw new Exception("function chgrp() does not exist.");
	  }
	  else {
	    chgrp($path, $mod);
	  }
	}

    /**
     * mod - Change mod of file or directory
	 *
	 * @param string $path
	 * @param int $mod
	 *
	 * @throws Exception
     */
	public static function mod($path=null,$mod=0777) {
	  $dirname = self::info($path, 'dirname');
	  if(!is_writable($dirname)) {
	    throw new Exception("$dirname is not writable.");
	  }
	  else if(!is_writable($path)) {
	    throw new Exception("$path is not writable.");
	  }
	  else if(!function_exists('chmod')) {
	    throw new Exception("function chmod() does not exist.");
	  }
	  else {
	    chmod($path, $mod);
	  }
	}

    /**
     * newDirectory - Creates new directory within parent directory $dirname
	 *   if $path_to_create is a boolean, then a random 10 character directory is created instead
	 *
	 * @param string|bool $path_to_create
	 * @param int $mod
	 * @param bool $recurs
	 *
	 * @return object
	 *
	 * @throws Exception
     */
    public static function newDirectory($path=null,$path_to_create='',$mod=0755,$recurs=true) {
	  $dirname = self::info($path, 'dirname') . '/' . self::info($path, 'basename');
	  if(!is_dir($dirname)) {
	    throw new Exception("Directory does not exist: $dirname");
	  }
	  else if(!is_writable($dirname)) {
	    throw new Exception("You do not have permission to write to this directory: $dirname");
	  }
	  else {
	    if(is_bool($path_to_create)) {
		  $path_to_create = '';
          $chars = 'abcdefghijklmnopqrstuvwxyz';
	      $charlen = strlen($chars) - 1;
	      $totalCount = 1000;
          while(strlen($path_to_create) < 10 && $totalCount-- > 0) {
	        $index = mt_rand(0, $charlen);
	        $path_to_create .= $chars[$index];
	        if(strlen($path_to_create) == 10 && is_dir("$dirname/$path_to_create")) {
	          $path_to_create = '';
	        }
	      }
        }
		$directory_to_create = "$dirname/$path_to_create";
	    if(is_dir($directory_to_create) || mkdir($directory_to_create, $mod, $recurs)) {
		  return "$directory_to_create/";
		}
		else {
		  throw new Exception("Could not create diretory: $directory_to_create");
		}
	  }
	  return false;
    }

    /**
     * getHashDirectory - Gets directory structure based upon md5 hash using 2 characters breaks
	 *
	 * @param string $seed
	 *
	 * @return string
     */
	public static function getHashDirectory($seed='0') {
	  $hash = preg_match('/^[a-z0-9]+$/', $seed) ? $seed : md5($seed);
	  $pieces = array();
	  $loops = floor(strlen($hash)/2);
	  for($a=0; $a<$loops; $a++) {
	    $start = $a*2;
	    $piece = substr($hash,$start,2);
	    $pieces[] = $piece;
	  }
	  return implode('/', $pieces);
	}

  }
?>
