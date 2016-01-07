<?php
  /**
   * Copyright 2015 Last Hit Productions, Inc.
   */

  /**
   * Class LhpRequest
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  class LhpRequest {

    /**
     * @var array - Standard request headers
     */
	private $default_request_headers = array(
      'Accept',               // Content-Types that are acceptable for the response [ Accept: text/plain ]
      'Accept-Charset',       // Character sets that are acceptable [ Accept-Charset: utf-8 ]
      'Accept-Encoding',      // List of acceptable encodings. [ Accept-Encoding: gzip, deflate ]
      'Accept-Language',      // List of acceptable human languages for response [ Accept-Language: en-US ]
      'Accept-Datetime',      // Acceptable version in time [ Accept-Datetime: Thu, 31 May 2007 20:35:00 GMT ]
      'Authorization',        // Authentication credentials for HTTP authentication [ Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ== ]
      'Cache-Control',        // Used to specify directives that must be obeyed by all caching mechanisms along the request-response chain [ Cache-Control: no-cache ]
      'Connection',           // What type of connection the user-agent would prefer [ Connection: keep-alive ]
      'Cookie',               // An HTTP cookie previously sent by the server with Set-Cookie (below) [ Cookie: $Version=1; Skin=new; ]
      'Content-Length',       // The length of the request body in octets (8-bit bytes) [ Content-Length: 348 ]
      'Content-MD5',          // A Base64-encoded binary MD5 sum of the content of the request body [ Content-MD5: Q2hlY2sgSW50ZWdyaXR5IQ== ]
      'Content-Type',         // The MIME type of the body of the request (used with POST and PUT requests) [ Content-Type: application/x-www-form-urlencoded ]
      'Date',                 // The date and time that the message was sent (in "HTTP-date" format as defined by RFC 7231) [ Date: Tue, 15 Nov 1994 08:12:31 GMT ]
      'Expect',               // Indicates that particular server behaviors are required by the client [  Expect: 100-continue ]
      'From',                 // The email address of the user making the request [ From: user@example.com ]
      'Host',                 // The domain name of the server (for virtual hosting), and the TCP port number on which the server is listening. The port number may be omitted if the port is the standard port for the service requested. Mandatory since HTTP/1.1. [ Host: en.wikipedia.org:80, Host: en.wikipedia.org ]
      'If-Match',             // Only perform the action if the client supplied entity matches the same entity on the server. This is mainly for methods like PUT to only update a resource if it has not been modified since the user last updated it. [ If-Match: "737060cd8c284d8af7ad3082f209582d" ]
      'If-Modified-Since',    // Allows a 304 Not Modified to be returned if content is unchanged [ If-Modified-Since: Sat, 29 Oct 1994 19:43:31 GMT ]
      'If-None-Match',        // Allows a 304 Not Modified to be returned if content is unchanged, see HTTP ETag [ If-None-Match: "737060cd8c284d8af7ad3082f209582d" ]
      'If-Range',             // If the entity is unchanged, send me the part(s) that I am missing; otherwise, send me the entire new entity [ If-Range: "737060cd8c284d8af7ad3082f209582d" ]
      'If-Unmodified-Since',  // Only send the response if the entity has not been modified since a specific time. [ If-Unmodified-Since: Sat, 29 Oct 1994 19:43:31 GMT ]
      'Max-Forwards',         // Limit the number of times the message can be forwarded through proxies or gateways. [ Max-Forwards: 10 ]
      'Origin',               // Initiates a request for cross-origin resource sharing (asks server for an 'Access-Control-Allow-Origin' response field) [ Origin: http://www.example-social-network.com ]
      'Pragma',               // Implementation-specific fields that may have various effects anywhere along the request-response chain [ Pragma: no-cache ]
      'Proxy-Authorization',  // Authorization credentials for connecting to a proxy. [ Proxy-Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ== ]
      'Range',                // Request only part of an entity. Bytes are numbered from 0. See Byte serving. [ Range: bytes=500-999 ]
      'Referer',              // This is the address of the previous web page from which a link to the currently requested page was followed. (The word “referrer” has been misspelled in the RFC as well as in most implementations to the point that it has become standard usage and is considered correct terminology) [ Referer: http://en.wikipedia.org/wiki/Main_Page ]
      'TE',                   // The transfer encodings the user agent is willing to accept: the same values as for the response header field Transfer-Encoding can be used, plus the "trailers" value (related to the "chunked" transfer method) to notify the server it expects to receive additional fields in the trailer after the last, zero-sized, chunk. [ TE: trailers, deflate ]
      'User-Agent',           // The user agent string of the user agent. [ User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:12.0) Gecko/20100101 Firefox/21.0 ]
      'Upgrade',              // Ask the server to upgrade to another protocol. [ Upgrade: HTTP/2.0, SHTTP/1.3, IRC/6.9, RTA/x11 ]
      'Via',                  // Informs the server of proxies through which the request was sent. [ Via: 1.0 fred, 1.1 example.com (Apache/1.1) ]
      'Warning'               // A general warning about possible problems with the entity body. [ Warning: 199 Miscellaneous warning ]
	);

    /**
     * @var array - Standard response headers
     */
	private $default_response_headers = array(

	);

    /**
     * @var array - Standard response headers
     */
	public static $default_response_codes = array(
	  '100' => 'Continue',
	  '101' => 'Switching Protocols',
	  '102' => 'Processing',
	  '200' => 'OK',
	  '201' => 'Created',
	  '202' => 'Accepted',
	  '203' => 'Non-Authoritative Information',
	  '204' => 'No Content',
	  '205' => 'Reset Content',
	  '206' => 'Partial Content',
	  '207' => 'Multi-Status',
	  '208' => 'Already Reported',
	  '226' => 'IM Used',
	  '300' => 'Multiple Choices',
	  '301' => 'Moved Permanently',
	  '302' => 'Found',
	  '303' => 'See Other',
	  '304' => 'Not Modified',
	  '305' => 'Use Proxy',
	  '306' => 'Switch Proxy',
	  '307' => 'Temporary Redirect',
	  '308' => 'Permanent Redirect',
	  '400' => 'Bad Request',
	  '401' => 'Unauthorized',
	  '402' => 'Payment Required',
	  '403' => 'Forbidden',
	  '404' => 'Not Found',
	  '405' => 'Method Not Allowed',
	  '406' => 'Not Acceptable',
	  '407' => 'Proxy Authentication Required',
	  '408' => 'Request Timeout',
	  '409' => 'Conflict',
	  '410' => 'Gone',
	  '411' => 'Length Required',
	  '412' => 'Precondition Failed',
	  '413' => 'Request Entity Too Large',
	  '414' => 'Request URI Too Long',
	  '415' => 'Unsupported Media type',
	  '416' => 'Requested Range Not Satisfiable',
	  '417' => 'Expectation Failed',
	  '418' => 'I\'m a teapot',
	  '419' => 'Authentication Timeout',
	  '422' => 'Unprocessable Entity',
	  '423' => 'Locked',
	  '424' => 'Failed Dependency',
	  '426' => 'Upgrade Required',
	  '428' => 'Precondition Required',
	  '429' => 'Too Many Requests',
	  '431' => 'Request Header Fields Too Large',
	  '440' => 'Login Timeout (Microsoft)',
	  '444' => 'No Response (Nginx)',
	  '449' => 'Retry Wtih (Microsoft)',
	  '450' => 'Blocked by Windows Parental Controls (Microsoft)',
	  '451' => 'Unavailable For Legal Reasons (Internet Draft)',
	  '494' => 'Request Header Too Large (Nginx)',
	  '495' => 'Cert Error (Nginx)',
	  '496' => 'No Cert (Nginx)',
	  '497' => 'HTTP to HTTPS (Nginx)',
	  '498' => 'Token Expired/Invalid (Esri)',
	  '499' => 'Token Required (Esri)',
	  '500' => 'Internal Server Error',
	  '501' => 'Not Implemented',
	  '502' => 'Bad Gateway',
	  '503' => 'Service Unavailable',
	  '504' => 'Gateway Timeout',
	  '505' => 'HTTP Version Not Supported',
	  '506' => 'Variant Also Negotiates',
	  '507' => 'Insufficient Storage',
	  '508' => 'Loop Detected',
	  '509' => 'Bandwidth Limit Exceeded',
	  '510' => 'Not Extended',
	  '511' => 'Network Authentication Required',
	  '520' => 'Origin Error (CloudFlare)',
	  '521' => 'Web Server is Down (CloudFlare)',
	  '522' => 'Connect Time Out (CloudFlare)',
	  '523' => 'Proxy Declined Request (CloudFlare)',
	  '524' => 'A Timeout Occurred (CloudFlare)',
	  '598' => 'Network Read Timeout Error (Unknown)',
	  '599' => 'Network Connect Timeout Error (Unknown)',
	);

    /**
     * @var array - List of standard request methods
     */
	private $default_request_methods = array(
	  'POST' => 'Requests that the server accept the entity enclosed in the request as a new subordinate of the web resource identified by the URI. The data POSTed might be, as examples, an annotation for existing resources; a message for a bulletin board, newsgroup, mailing list, or comment thread; a block of data that is the result of submitting a web form to a data-handling process; or an item to add to a database.',
	  'GET' => 'Requests a representation of the specified resource. Requests using GET should only retrieve data and should have no other effect. (This is also true of some other HTTP methods.) The W3C has published guidance principles on this distinction, saying, Web application design should be informed by the above principles, but also by the relevant limitations.',
	  'HEAD' => 'Asks for the response identical to the one that would correspond to a GET request, but without the response body. This is useful for retrieving meta-information written in response headers, without having to transport the entire content.',
	  'PUT' => 'Requests that the enclosed entity be stored under the supplied URI. If the URI refers to an already existing resource, it is modified; if the URI does not point to an existing resource, then the server can create the resource with that URI.',
	  'DELETE' => 'Deletes the specified resource.',
	  'TRACE' => 'Echoes back the received request so that a client can see what (if any) changes or additions have been made by intermediate servers.',
	  'OPTIONS' => 'Returns the HTTP methods that the server supports for the specified URL. This can be used to check the functionality of a web server by requesting \'*\' instead of a specific resource.',
	  'CONNECT' => 'Converts the request connection to a transparent TCP/IP tunnel, usually to facilitate SSL-encrypted communication (HTTPS) through an unencrypted.',
	  'PATCH' => 'Is used to apply partial modifications to a resource'
	);

    /**
     * @const string - Http version
     */
	const HTTP_VERSION = 'HTTP/1.1';

    /**
     * @var string - Response of an http request
     */
	private $response = '';

    /**
     * @var string - Raw response header of an http request
     */
	private $raw_response_header = '';

    /**
     * @var array - Key/value pairs of response headers
     */
	private $response_header = array();

    /**
     * @var int - Response code of an http request
     */
	private $response_code = 0;

    /**
     * @var array - Request headers
     */
	private $request_header = array();

    /**
     * @var string - First line of request header
     */
	private $top_header = null;

    /**
     * @var int - Request timeout in seconds
     */
	private $timeout = 60;

    /**
     * LhpRequest - Http request handling object
     *
	 * @param string $domain
	 * @param string $page
	 * @param string $method
     */
	public function __construct($domain=null, $page='/', $method='GET') {
	  $this->top_header = "$method $page " . self::HTTP_VERSION . "\r\n";
	  $this->addRequestHeader('User-Agent', 'lhp-spider-v2');
	  $this->addRequestHeader('Host', $domain);
	  $this->addRequestHeader('Accept', 'text/html; utf-8');
	  $this->addRequestHeader('Connection', 'close');
	}

    /**
     * getRespnse - Returns html response after send() request
     *
     * @return string
     */
	public function getResponse() {
	  return $this->response;
	}

    /**
     * getRawResponseHeader - Returns html response header after send() request
     *
     * @return string
     */
	public function getRawResponseHeader() {
	  return $this->raw_response_header;
	}

    /**
     * getResponseHeader - Returns html response header after send() request
     *
     * @return string|array
     */
	public function getResponseHeader($key) {
	  $val = null;
	  if(isset($this->response_header[$key])) {
	    $val = $this->response_header[$key];
	  }
	  return $val;
	}

    /**
     * getResponseHeaderFields - Return all response header key values
     *
     * @return array
     */
	public function getResponseHeaderFields() {
	  return array_keys($this->response_header);
	}

    /**
     * getResponseCode - Returns html response code after send() request
     *
     * @return int
     */
	public function getResponseCode() {
	  return $this->response_code;
	}

    /**
     * setTimeout - Sets request timeout in seconds
     *
     * @return object
     */
	public function setTimeout($t=60) {
	  $this->timeout = $t;
	  return $this;
	}

    /**
     * addRequestHeader - Add to http request header
     *
     * @return object
     */
	public function addRequestHeader($key,$val) {
	  if(in_array($key, $this->default_request_headers)) {
	    $this->request_header[$key] = $val;
	  }
	  return $this;
	}

    /**
     * getRequestHeader - Get http request header $key
     *
     * @return string
     */
	public function getRequestHeader($key) {
	  $val = null;
	  if(isset($this->request_header[$key])) {
	    $val = $this->request_header[$key];
	  }
	  return $val;
	}

    /**
     * getRequestHeaderFields - Return all request header key/value pairs
     *
     * @return array
     */
	public function getRequestHeaderFields() {
	  return array_keys($this->request_header);
	}

    /**
     * buildRequestHeader - Build complete http request header
     *
     * @return string
     */
	private function buildRequestHeader() {
	  $header = $this->top_header;
	  foreach($this->request_header as $key=>$val) {
	    $header .= "$key: $val\r\n";
	  }
	  $header .= "\r\n";
	  return $header;
	}

    /**
     * send - Send http request or https request if $secure is set to true
	 *
	 * @param array|null $args
	 * @param bool $secure
     *
     * @return object
     */
	public function send($args=null,$secure=false) {
	  $this->response = '';
	  $this->raw_response_header = '';
	  $this->response_code = 0;
	  $req = '';
	  if($args !== null && is_array($args)) {
	    $temp = array();
	    foreach($args as $field => $value) {
	      $treq[] = urlencode($field) . '=' . urlencode($value);
	    }
	    $req = join('&', $temp);
		if(strlen($req) > 0) {
		  $this->addRequestHeader("Content-Length", strlen($req));
		  $this->addRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		}
	  }
	  $domain = $this->getRequestHeader('Host');
	  $fp = $secure ? fsockopen("ssl://$domain", 443, $this->errno, $this->errstr, $this->timeout) : fsockopen($domain, 80, $this->errno, $this->errstr, $this->timeout);
	  if($fp) {
        fputs ($fp, $this->buildRequestHeader(). $req);
        $headerdone = false;
        while(!feof($fp)) {
          $line = fgets($fp, 1024);
		  if(!$headerdone) { $this->raw_response_header .= $line; }
          if(strcmp($line, "\r\n") === 0) {
            $headerdone = true;
          }
          else if($headerdone) {
            $this->response .= $line;
          }
        }
	    fclose($fp);
	    $this->raw_response_header = trim($this->raw_response_header);
	    $this->response = trim($this->response);
	    $lines = explode("\n", $this->raw_response_header);
	    for($x=0, $y=count($lines); $x<$y; $x++) {
	      if($x === 0 && preg_match('/^'.preg_quote(self::HTTP_VERSION, '/').'\s+?(\d+)\s+?(.*)$/i', $lines[$x], $matches) && isset($matches[1])) {
		    $this->response_code = trim($matches[1]);
		  }
		  else if(preg_match('/^(.*?):(.*?)$/i', $lines[$x], $matches) && isset($matches[1]) && isset($matches[2])) {
		    if(isset($this->response_header[$matches[1]])) {
		      if(is_array($this->response_header[$matches[1]])) {
		        $this->response_header[$matches[1]][] = trim($matches[2]);
			  }
			  else {
			    $this->response_header[$matches[1]] = array($this->response_header[$matches[1]], trim($matches[2]));
			  }
		    }
		    else {
		      $this->response_header[$matches[1]] = trim($matches[2]);
		    }
	 	  }
		}
	  }
	  return $this;
	}

  }
?>
