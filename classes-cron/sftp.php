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
   * Class LhpSftp
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  class LhpSftp {
  
    /**
     * @var resource - SSH2 connection id
     */
	private $connection = null;
	
    /**
     * @var resource - SFTP sub module of SSH2 connection
     */
	private $sftp = null;
	
    /**
     * LhpSftp - Returns ssh2_sftp object 
     *
	 * @param string $user
	 * @param string $pass
	 * @param string $host
	 * @param string $pubkey
	 * @param int $port
	 *
	 * @throws exception
     */
	public function __construct($user,$pass,$host,$pubkey=null,$port=22) {
	
      /**
       * Try to connect to $host
       */
      if(!($this->connection = ssh2_connect($host, $port))) {
	    throw new Exception("Could not connect to $host on port $port.");
	  }
	  
      /**
       * Check fingerprint from server against our stored pubkey
       */
	  if($pubkey !== null) {
        $fingerprint = ssh2_fingerprint($this->connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX);
        if($fingerprint != $pubkey) {
	      throw new Exception("Fingerprint [ $fingerprint ] does not match your pubkey [ $pubkey ] ");
	    }
	  }
	  
      /**
       * Send username and password
       */
      if(!ssh2_auth_password($this->connection, $user, $pass)) {
        throw new Exception("Username and/or password was not accepted.");
	  }
	  
	}
	
    /**
     * scp_send - Send local file to remote path using scp method 
     *
	 * @param string $local
	 * @param string $remote
	 * @param int $mod
	 *
	 * @return bool
     */
	public function scp_send($local,$remote,$mod=0644) {
      return ssh2_scp_send($this->connection, $local, $remote, $mod); 
	}
	
    /**
     * open - Request the SFTP subsystem from an already connected SSH2 server.
	 *   This method sets $this->sftp an SSH2 SFTP resource for use with all other ssh2_sftp_*() methods and the ssh2.sftp:// fopen wrapper. 
     *
	 * @param string $local
	 * @param string $remote
	 * @param int $mod
	 *
	 * @throws exception
     */
	public function open() {
	  if(!($this->sftp = ssh2_sftp($this->connection))) {
	    throw new Exception("Could not load sftp module");
	  }
	}
	
    /**
     * send - Send file using the SFTP subsystem from an already connected SSH2 server. 
     *
	 * @param string $local
	 * @param string $remote
	 * @param int $mod
	 *
	 * @throws exception
     */
	public function send($local,$remote) {
      $stream = fopen("ssh2.sftp://" . $this->sftp . $remote, 'w');
	  if($stream) {
	    $data_to_send = file_get_contents($local);
        if(fwrite($stream, $data_to_send)) {
	      unset($data_to_send);
		  fclose($stream);
		}
	  }
	  else {
	    throw new Exception("Could not send $local to $remote");
	  }
	}
	
    /**
     * newDirectory - Create new directory using the SFTP subsystem from an already connected SSH2 server. 
     *
	 * @param string $local
	 * @param string $remote
	 * @param int $mod
	 *
	 * @throws exception
     */
	public function newDirectory($remote) {
	  $statinfo = @ssh2_sftp_stat($this->sftp, $remote);
	  if(empty($statinfo['size'])) {
	    if(!ssh2_sftp_mkdir($this->sftp, $remote)) {
	      throw new Exception("Could not create directory $remote");
	    }
	  }
	}
	
  }
?>
