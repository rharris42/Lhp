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
   * Class LhpTemplate
   * @author Robert Harris <robert.t.harris@gmail.com>
   */
  class LhpTemplate {
	
    /**
     * @var array - List of string formatting modifiers
     */
	private $modifiers = array(
      'br' => 'str_replace(array("\n","\r"), array("<br>",""), %s)',
	  'encs' => 'str_urlencode(%s)',
      'enc' => 'urlencode(%s)',
	  'dec' => 'urldecode(%s)',
      'ent' => 'htmlentities(%s, ENT_QUOTES)',
      'urlenc' => 'str_urlenc(%s)',
      'lc' =>  'strtolower(%s)',
      'uc' =>  'strtoupper(%s)',
      'ucw' => 'ucwords(%s)',
	  'ucf' => 'ucfirst(%s)',
	  'lcf' => 'lcfirst(%s)',
	  'trim' => 'trim(%s)',
      'md5' => 'md5(%s)',
	  'sha1' => 'sha1(%s)',
      'num' => 'number_format(%s)',
	  'nobad' => 'str_replace_bad_words(%s)',
	  'smysql' => '$mysql->real_escape(%s)',
	  'fext' => 'file_get_ext(%s)'
	);
  
    /**
     * @var array - Data array of overloaded property values
	 * Examples of valid placeholders:
	 * {first_name}
	 * {first_name:ucw}
	 * {myvar1}
	 * {date.full_date}
	 * {form.field}
	 * {form.field.array_index}
	 * {cookie.value}
	 * {user_defined_object.property}
     */
    private $placeholder_regexp = '([a-zA-Z](?:[a-zA-Z0-9_]*))((?:\.[a-zA-Z0-9](?:[a-zA-Z0-9_]*))*)((?::[a-zA-Z0-9_]+)*)';
  
    /**
     * @var array - Data array of overloaded property values
     */
	private $data = array();
  
    /**
     * @var string - Additional HTTP headers to be used on current request
     */
	private $http_header = null;
  
    /**
     * @var string - HTML content of template file
     */
	private $html = '';
  
    /**
     * @var string - Path of template file
     */
	private $path = '';
	
    /**
     * @var string - Route to template
     */
	private $template_route = '';
	
    /**
     * @var string - Route to execute
     */
	private $route = '';
	
    /**
     * @var string - Flag determining whether or not we are performing an ajax request
     */
	private $ajax = false;
	
    /**
     * @var string - URL to redirect to
     */
	private $redirect = null;
  
    /**
     * @var array -  List of improperly formatted template vars
     */
	private $bad_vars = array();
	
    /**
     * LhpTemplate - Initiate template object and set route based on request uri
     */
	public function __construct() {

	}
	
    /**
     * loadTemplateFile - Loads template file based on route or given $path 
     *  also performs first parse of template commands (ie. including other templates) {-t template.tpl.htm}
	 * @param string $path
	 *
	 * @throws exception
     */
	public function loadTemplateFile($user,$path=null) {
      $this->path = ($path === null) ? TEMPLATE_DIR . "/".$user->get('role')."/" . $this->template_route . TEMPLATE_EXT : $path;
	  $this->html = LhpFile::get($this->path);
	  preg_match_all('/\{\-([a-z]\s+.*?)\}/', $this->html, $matches);
	  $temp = isset($matches[1]) ? $matches[1] : array();
	  $vars = array();
	  foreach($temp as $placeholder) {
	    $replace_value = '';
	    list($command,$param) = explode(' ', $placeholder);
		if($command === 't') {
		  $replace_value = LhpFile::get(dirname($this->path) . "/" . $param . TEMPLATE_EXT);
		}
		$this->html = preg_replace('/\{\-' . preg_quote($placeholder, '/') . '\}/', $replace_value, $this->html);
	  }
	  Lhp::Debug("template path = $this->path");
	}
	
    /**
     * addModifier - Adds placeholder modifiers to the default list of $this->modifiers
	 *   modifier $key values must only contain the following characters [a-zA-Z0-9] (case sensitive) 
	 * 
	 * @param string $key - the name of the placeholder modifier that you would use within your html templates ( ex: {myvar:mynewmodifier} )
	 * @param string $val - the name of the function to execute for the specified modifier ( ex: 'strtolower(%s)' )
	 *
	 * @throws exception
     */
	public function addModifier($key,$val) {
	  $this->modifiers[$key] = $val;
	}
	
    /**
     * setHeader - Set http header
     */
	public function setHeader($header) {
	  $this->http_header[] = $header;
	}
	
    /**
     * add - Add placeholders
     */
	public function add($row) {
	  foreach($row as $key=>$val) {
		$this->data[$key] = $val;
	  }
	}
	
    /**
     * __set - Magic function to set the placeholder values
     */
	public function __set($name,$value) {
	  $this->data[$name] = $value;
	  return true;
	}
	
    /**
     * __get - Magic function to get the placeholder value
     */
	public function __get($name) {
	  if(isset($this->data[$name])) {
	    return $this->data[$name];
	  }
	  else {
	    return null;
	  }
	}
	
    /**
     * getRedirect - Returns redirect url 
     *
     */
	public function getRedirect() {
	  return $this->redirect;
	}
	
    /**
     * setRedirect - Sets redirect url
     *
     */
	public function setRedirect($uri) {
      $this->redirect = $uri;
	}
	
    /**
     * getRoute - Returns route 
     *
     */
	public function getRoute() {
	  return $this->route;
	}
	
    /**
     * setTemplateRoute - Sets template route 
     *
     */
	public function setTemplateRoute($route) {
	  $this->template_route = $route;
	}
	
    /**
     * getAjax - Returns ajax 
     *
     */
	public function getAjax() {
	  return $this->ajax;
	}
	
    /**
     * setAjax - Sets ajax flag
     *
     */
	public function setAjax($ajax) {
      $this->ajax = $ajax;
	}
	
    /**
     * findRoute - Set template route and get UserRoute based on uri
     *
     */
	public function findRoute($uri=null) {
      $route = preg_replace('/\?.*$/', '', $uri);
	  $route = preg_replace('/\..*$/', '', $route);
      if($route === DEFAULT_URI) {
        $route = DEFAULT_ROUTE;
      }
	  $route = preg_replace('/^\//', '', $route);
	  $route = preg_replace('/[^-a-zA-Z0-9_]/', '', $route);
	  $this->template_route = $route;
	  $route = preg_replace('/-/', '', $route);
	  $this->route = $route;
	  return LhpRoute::$route();
	}
	
    /**
     * getTemplateVars - add objects / arrays to data container to be used when parsing template
     *
	 * @return array
     */
	private function getTemplateVars($html) {
	//print "getTemplateVars_html = $html<BR>";
	  // preg_match_all('/\{(.*?)\}/', $html, $matches);
	  preg_match_all('/\{(' . $this->placeholder_regexp . ')\}/', $html, $matches);
	  $temp = isset($matches[1]) ? $matches[1] : array();
	  $vars = array();
	  foreach($temp as $var) {
	   //print "var = $var<BR>";
	    if(preg_match('/^' . $this->placeholder_regexp . '$/', $var, $matches)) {
		  $obj = $matches[1];
		  $keys = (isset($matches[2]) && !empty($matches[2])) ? $matches[2] : null;
		  $mods = (isset($matches[3]) && !empty($matches[3])) ? $matches[3] : null;
		  if($keys === null) {
		    $keys = $obj;
		    $obj = 'this';
		  }
		  $keys = preg_replace('/^\./', '', $keys);
		  $mods = preg_replace('/^:/', '', $mods);
		  if(!isset($vars[$var])) {
		    $vars[$var] = array('obj'=>$obj, 'keys'=>$keys, 'mods'=>$mods);
		  }
		}
		else if(!in_array($var, $this->bad_vars)) {
		  $this->bad_vars[] = $var;
		}
	  }
	  //print "<BR>";
	  return $vars;
	}
	
    /**
     * parse - parse template variables with object / associative array data
     *
	 * @throws exception
     */
	private function parse($html, $data=null) {
	  global $form,$cookie,$mysql,$user;
	  $vars = $this->getTemplateVars($html);
	  if(count($vars) > 0) {
	    foreach($vars as $placeholder=>$params) {
		  $replace_value = "";
		  $obj = $params['obj'];
		  $keys = $params['keys'];
		  $mods = $params['mods'];
		  if(is_array($data)) {
		    $replace_value = (isset($data[$keys])) ? $data[$keys] : $replace_value;
		  }
		  else {
			if($params['obj'] == 'form') {
			  $pieces = explode('.', $keys);
			  if(count($pieces) === 2) {
			    $replace_value = $form->get($pieces[0], $pieces[1]);
			  }
			  else if(count($pieces) === 1) {
			    $replace_value = $form->get($keys);
			  }
			}
		    else if($params['obj'] == 'session' || $params['obj'] == 'user') { 
		      $replace_value = $user->get($keys);
		    }
		    else if($params['obj'] == 'cookie') {
		      $replace_value = $cookie->get($keys);
		    }
			else if($params['obj'] == 'this' && isset($this->data[$keys])) {
			  $replace_value = $this->data[$keys];
			}
	        else if(DEBUG) {
			  $replace_value = "[ placeholder not found: {$placeholder} ]";
		    }
		  }
		  if($replace_value !== '' && $mods !== null) {
			foreach(explode(':', $mods) as $mod) {
		      if(isset($this->modifiers[$mod])) {
			    eval("\$replace_value = " . sprintf($this->modifiers[$mod], "\$replace_value") . ";");
			  }
			}
		  }
		  $html = preg_replace('/\{' . preg_quote($placeholder, '/') . '\}/', $replace_value, $html);
	    }
	  }
	  return $html;
	}
	
    /**
     * parseBlocks - parse template blocks <lhp>...</lhp> with object / associative array data
	 *  We must parse blocks first, as parsing the whole template first will overwrite the data within the blocks
     *
	 * @return array
     *
	 * @throws exception
     */
	public function parseBlocks() {
	  global $mysql,$form;
	  $pagelink_info = array();
	  /** go through each <lhp>...</lhp> block and parse placeholders */
	  preg_match_all('/<lhp((?:\s+(?:\w+)="(?:.*?)")+?)\s*>(.*?)<\/lhp>/si', $this->html, $matches);
      for($a=0, $b=count($matches[1]); $a<$b; $a++) {
	  
	    /** Set block paramaters */
	    $params_str = $matches[1][$a];
	    $params = array(
		  'type' => null,
		  'query' => null,
		  'cols' => 0,
		  'pagelinks' => null,
		  'pages' => 10,
		  'name' => null,
		  'class_on' => null,
		  'class_off' => null,
		  'class_next' => null,
		  'class_prev' => null,
		  'func' => null,
		  'functype' => null
		);
		preg_match_all('/\s+(\w+)="(.*?)"/', $params_str, $pmatches);
		for($c=0, $d=count($pmatches[1]); $c<$d; $c++) {
		  if(isset($pmatches[1][$c])) {
		    $key = strtolower($pmatches[1][$c]);
		    $params[$key] = $this->parse($pmatches[2][$c]);
			//print "$key = " . $params[$key] . "<BR>";
		    if($params[$key] === 'true') { $params[$key] = true; }
		    else if($params[$key] === 'false') { $params[$key] = false; }
		  } 
		}
		unset($pmatches);
		
		/** Set active and else blocks */
		$block = $matches[2][$a];
		$block_else = null;
		if(preg_match('/(<lhp:else>)/i', $block, $imatches)) {
		  list($block, $block_else) = explode($imatches[1], $block);
		}
		
	    //print "<BR><BR>outer = " . htmlentities($matches[0][$a]) . "<BR>\n";
		//print "params = <BR>\n";
		//foreach($params as $key=>$val) {
		//  print "&nbsp;&nbsp;$key = $val<BR>\n";
		//}
		//print "inner = " . htmlentities($block) . "<BR>\n";
		//print "else = " . htmlentities($block_else) . "<BR>\n";
		
		/** build html block by specified type */
		$complete_html = '';
		$counter = 1;
		if($params['type'] === 'mysql') {
		  //print "query = " . $params['query'] . "<BR>";
		  $offset = $form->get('offset');
		  $start = 0;
		  $increment = 0;
		  $rows_found = 0;
		  if(preg_match('/(?:(\d+|%d),)?\s*(\d+)$/', $params['query'], $smatches)) {
		    $start = isset($smatches[1]) ? $smatches[1] : $start;
			$increment = isset($smatches[2]) ? $smatches[2] : $increment; 
		  }
		  else if(preg_match('/%d,\s*%d$/', $params['query'], $smatches)) {
		    $start = $form->get('start_limit');
		    $params['query'] = sprintf($params['query'], $form->get('start_limit'), $form->get('end_limit')); 
		  }
		  if($start === '%d') {
		    $start = $offset * $increment;
		    $params['query'] = sprintf($params['query'], $start); 
		  }
		  $counter = $counter + $start;
		  while($row = $mysql->fetch($params['query'])) {
		    /** execute optional function parameter to add more template variables */
		    if($params['func'] !== null && $params['functype'] !== null && is_callable($params['func'])) {
		      $params['func']($row, $params['functype']); 
		    }
		    if($params['cols'] > 0 && (($counter-1) % $params['cols'] === 0)) {
			  $complete_html .= "\n<tr>\n";
			}
			$row['counter'] = $counter;
		    $complete_html .= $this->parse($block, $row);
			if($params['cols'] > 0 && $counter % $params['cols'] === 0) {
			  $complete_html .= "\n</tr>\n";
			}
			$counter++;
		  }
		  /** get total number of rows if SQL_CALC_FOUND_ROWS found in query */
		  /** mainly used for generating pagelinks on content listing / search listing pages */
		  if(preg_match('/SQL_CALC_FOUND_ROWS/', $params['query'])) {
		    $rows_found = $mysql->getFoundRows();
			$total_pages = floor($rows_found / $increment) + (($rows_found % $increment > 0) ? 1 : 0);
			$this->data['pages_found'] = $total_pages;
			$this->data['rows_found'] = $rows_found;
			if($params['pagelinks'] !== null) {
			  $pagelink_info[$params['pagelinks']] = $total_pages;
			}
			Lhp::Debug("rows_found = $rows_found");
			Lhp::Debug("pages_found = $total_pages");
		  }
		}
		else if($params['type'] === 'pagelinks' && isset($pagelink_info[$params['name']])) {
		  $total_pages = $pagelink_info[$params['name']];
		  // print "total_pages = $total_pages<BR>";
		  $pagelinks = "";
	      $start_page = 0;
		  $end_page = $total_pages;
	      if($total_pages > $params['pages']) {
			if($offset > floor($params['pages'] / 2)) {
			  $start_page = $offset - floor($params['pages'] / 2);
			}
			$end_page = $start_page + $params['pages'];
			if($end_page > $total_pages) {
			  $end_page = $total_pages;
			  $start_page -= $params['pages'] - ($end_page - $start_page);
			}
		  }
		  if($total_pages > 1) {
		    $pagelinks .= '<a href="?offset=' . (($offset === 0) ? ($total_pages - 1) : $offset - 1) . '" class="' . $params['class_prev'] . '">&laquo;&nbsp;PREV</a>';
	      }
		  for($c=$start_page, $d=$end_page; $c<$d; $c++) {
			$page_number = $c + 1;
			if($c == $offset) {
			  $pagelinks .= '<a href="?offset=' . $c . '" class="' . $params['class_on'] . '">' . $page_number . '</a>';
			}
			else {
			  $pagelinks .= '<a href="?offset=' . $c . '" class="' . $params['class_off'] . '">' . $page_number . '</a>';
			}
	      }
		  if($total_pages > 1) {
			$pagelinks .= '<a href="?offset=' . (($offset === $total_pages - 1) ? 0 : $offset + 1) . '" class="' . $params['class_next'] . '">NEXT&nbsp;&raquo;</a>';
		  }
		  $complete_html = $this->parse($block, array('pagelinks' => $pagelinks));
		}
		
		/** for empty results for lists that have an <lhp:else> block */
		if(empty($complete_html) && $block_else !== null) {
		  $complete_html = $block_else;
		}
		
		/** update block placeholder with parsed html */
		$this->html = preg_replace('/' . preg_quote($matches[0][$a], '/') . '/', $complete_html, $this->html);
	  }
	}
	
	public function parseAll() {
	  $this->html = $this->parse($this->html);
	}
	
    /**
     * show - prints template to browser
	 *  has option to compress output to minize the amount of data actually sent
     *
     */
	public function show() {
	  /** Reduce file size by optimizing code */
	  //$this->html = preg_replace('/(^\s+|\s+$)/m', '', $this->html);
	  //$this->html = preg_replace('/[\r\n]/', '', $this->html);
	  
	  /** Print HTTP header if applicable */
	  if(count($this->http_header) > 0) {
	    foreach($this->http_header as $val) {
	      header($val);
		}
	  }
	  
	  /** Print compiled template */
	  print $this->html;
	}
	
  }
?>
