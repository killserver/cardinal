<?php

class Router {

	public static function all($name = "", $uri_callback = "", $regex  = array(), $callback = "") {
		return Route::method("GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD", $uri_callback, $name, $regex, $callback);
	}

	public static function get($name = "", $uri_callback = "", $regex  = array(), $callback = "") {
		return Route::method("GET", $uri_callback, $name, $regex, $callback);
	}

	public static function post($name = "", $uri_callback = "", $regex  = array(), $callback = "") {
		return Route::method("POST", $uri_callback, $name, $regex, $callback);
	}

	public static function put($name = "", $uri_callback = "", $regex  = array(), $callback = "") {
		return Route::method("PUT", $uri_callback, $name, $regex, $callback);
	}

	public static function delete($name = "", $uri_callback = "", $regex  = array(), $callback = "") {
		return Route::method("DELETE", $uri_callback, $name, $regex, $callback);
	}

	public static function options($name = "", $uri_callback = "", $regex  = array(), $callback = "") {
		return Route::method("OPTIONS", $uri_callback, $name, $regex, $callback);
	}

	public static function patch($name = "", $uri_callback = "", $regex  = array(), $callback = "") {
		return Route::method("PATCH", $uri_callback, $name, $regex, $callback);
	}

	public static function head($name = "", $uri_callback = "", $regex  = array(), $callback = "") {
		return Route::method("HEAD", $uri_callback, $name, $regex, $callback);
	}

	private static $routesList = array();
	private static $allowedCollectMethods = array('get', 'post', 'put', 'delete', 'head', 'options', 'patch');
	public static function collect($firstPart, $class) {
		$method = get_class_methods($class);
		for($i=0;$i<sizeof($method);$i++) {
			$info = explode('/', preg_replace('/([a-z]*)(([A-Z]+)([a-zA-Z0-9_]*))/', '$1/$2', $method[$i]));
			if(isset($info[1])) {
				$httpMethod  = $info[0];
				$uri = preg_replace('/([a-z])([A-Z])/', '$1-$2', $info[1]);
				$uri = strtolower($uri);
			} else {
				$uri = $info[0];
				$httpMethod = "GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD";
			}
			$reflectionMethod = new \ReflectionMethod($class, $method[$i]);
            if($reflectionMethod->isPrivate()) {
            	continue;
            }
            if(!in_array($httpMethod, self::$allowedCollectMethods)) {
            	continue;
            }
            $regex = array();
            $paramList = array();
            $params = $reflectionMethod->getParameters();
            $defaults = array();
            for($z=0;$z<sizeof($params);$z++) {
            	try {
	            	$typeParams = $params[$z]->getType();
	            } catch(Exception $ex) {
	            	$typeParams = "";
	            }
            	try {
	            	$default = $params[$z]->getDefaultValue();
	            } catch(Exception $ex) {
	            	$default = false;
	            }
            	if(!empty($typeParams)) {
            		$paramList[$params[$z]->getName()] = $typeParams->getName();
            	} else {
            		$paramList[$params[$z]->getName()] = "any";
            	}
            	$defaults[$params[$z]->getName()] = $default;
            }
            $defaults = array_filter($defaults);
            $regex = self::compileRegEx($paramList);
            $paramsLink = self::getParamsLink($paramList, $defaults);
            $phpDocs = self::getMethodMetaData($reflectionMethod);
            //var_dump($phpDocs);die();
            if(isset($phpDocs['url'])) {
                if(isset($phpDocs['url']['url'])) {
                    //only one route
	                self::$routesList[] = array(
	                	"uri" => $phpDocs['url']['url'],
	                	"method" => $httpMethod,
	                	"defaults" => array(
	                		"class" => $class,
	                		"method" => $method[$i],
            				"routerGetParams" => $paramList,
            				"defaults" => $defaults,
	                	),
            			"regex" => $regex,
	                );
                } else {
                    foreach($phpDocs['url'] as $urlAnnotation) {
		                self::$routesList[] = array(
		                	"uri" => (!empty($firstPart) ? $firstPart."/" : "").$urlAnnotation['url'],
		                	"method" => $httpMethod,
		                	"defaults" => array(
		                		"class" => $class,
		                		"method" => $method[$i],
                				"routerGetParams" => $paramList,
                				"defaults" => $defaults,
		                	),
                			"regex" => $regex,
		                );
                    }
                }
            } else {
                self::$routesList[] = array(
                	"uri" => (!empty($firstPart) ? $firstPart."/" : "").$uri.(!empty($paramsLink) ? implode("", $paramsLink) : ""),
                	"method" => $httpMethod,
                	"defaults" => array(
                		"class" => $class,
                		"method" => $method[$i],
                		"routerGetParams" => $paramList,
        				"defaults" => $defaults,
                	),
                	"regex" => $regex,
                );
            }
		}
		// var_dump(self::$routesList, $defaults);
		for($i=0;$i<sizeof(self::$routesList);$i++) {
			Route::method(
				strtoupper(self::$routesList[$i]['method']),
				self::$routesList[$i]['uri'],
				md5(self::$routesList[$i]['uri'].microtime()),
				self::$routesList[$i]['regex']
			)->defaults(self::$routesList[$i]['defaults']);
		}
	}

	private static function getParamsLink($paramList, $defaults) {
		$params = array();
		foreach($paramList as $k => $v) {
			if(array_key_exists($k, $defaults)) {
				$params[] = "(/<".$k.">)";
			} else {
				$params[] = "/<".$k.">";
			}
		}
		return $params;
	}

	private static function compileRegEx($paramList) {
		foreach($paramList as $k => &$v) {
			if($v=="int") {
				$v = "[0-9]+";
			} else {
				$v = ".+?";
			}
		}
		$paramList = array_filter($paramList);
		return $paramList;
	}

	public function getMethodMetaData(\ReflectionFunctionAbstract $pMethod, $pRegMatches = null) {
        $file = $pMethod->getFileName();
        $startLine = $pMethod->getStartLine();

        $fh = fopen($file, 'r');
        if (!$fh) return false;

        $lineNr = 1;
        $lines = array();
        while (($buffer = fgets($fh)) !== false) {
            if ($lineNr == $startLine) break;
            $lines[$lineNr] = $buffer;
            $lineNr++;
        }
        fclose($fh);

        $phpDoc = '';
        $blockStarted = false;
        while ($line = array_pop($lines)) {

            if ($blockStarted) {
                $phpDoc = $line.$phpDoc;

                //if start comment block: /*
                if (preg_match('/\s*\t*\/\*/', $line)) {
                    break;
                }
                continue;
            } else {
                //we are not in a comment block.
                //if class def, array def or close bracked from fn comes above
                //then we dont have phpdoc
                if (preg_match('/^\s*\t*[a-zA-Z_&\s]*(\$|{|})/', $line)) {
                    break;
                }
            }

            $trimmed = trim($line);
            if ($trimmed == '') continue;

            //if end comment block: */
            if (preg_match('/\*\//', $line)) {
                $phpDoc = $line.$phpDoc;
                $blockStarted = true;
                //one line php doc?
                if (preg_match('/\s*\t*\/\*/', $line)) {
                    break;
                }
            }
        }

        $phpDoc = self::parsePhpDoc($phpDoc);

        $refParams = $pMethod->getParameters();
        $params = array();

        $fillPhpDocParam = !isset($phpDoc['param']);

        foreach ($refParams as $param) {
            $params[$param->getName()] = $param;
            if ($fillPhpDocParam) {
                $phpDoc['param'][] = array(
                    'name' => $param->getName(),
                    'type' => $param->isArray()?'array':'mixed'
                );
            }
        }

        $parameters = array();

        if (isset($phpDoc['param'])) {
            if (is_array($phpDoc['param']) && is_string(key($phpDoc['param'])))
                $phpDoc['param'] = array($phpDoc['param']);

            $c = 0;
            foreach ($phpDoc['param'] as $phpDocParam) {

                $param = $params[$phpDocParam['name']];
                if (!$param) continue;
                $parameter = array(
                    'type' => $phpDocParam['type']
                );

                if ($pRegMatches && is_array($pRegMatches) && $pRegMatches[$c]) {
                    $parameter['fromRegex'] = '$'.($c+1);
                }

                $parameter['required'] = !$param->isOptional();

                if ($param->isDefaultValueAvailable()) {
                    $parameter['default'] = str_replace(array("\n", ' '), '', var_export($param->getDefaultValue(), true));
                }
                $parameters[self::argumentName($phpDocParam['name'])] = $parameter;
                $c++;
            }
        }

        if (!isset($phpDoc['return']))
            $phpDoc['return'] = array('type' => 'mixed');

        $result = array(
            'parameters' => $parameters,
            'return' => $phpDoc['return']
        );

        if (isset($phpDoc['description']))
            $result['description'] = $phpDoc['description'];

        if (isset($phpDoc['url']))
            $result['url'] = $phpDoc['url'];

        return $result;
    }

    /**
     * If the name is a camelcased one whereas the first char is lowercased,
     * then we remove the first char and set first char to lower case.
     *
     * @param  string $pName
     * @return string
     */
    public function argumentName($pName)
    {
        if (ctype_lower(substr($pName, 0, 1)) && ctype_upper(substr($pName, 1, 1))) {
            return strtolower(substr($pName, 1, 1)).substr($pName, 2);
        } return $pName;
    }

    /**
     * Parse phpDoc string and returns an array.
     *
     * @param  string $pString
     * @return array
     */
    public function parsePhpDoc($pString) {
        preg_match('#^/\*\*(.*)\*/#s', trim($pString), $comment);

        if (0 === count($comment)) return array();

        $comment = trim($comment[1]);

        preg_match_all('/^\s*\*(.*)/m', $comment, $lines);
        $lines = $lines[1];

        $tags = array();
        $currentTag = '';
        $currentData = '';

        foreach ($lines as $line) {
            $line = trim($line);

            if (substr($line, 0, 1) == '@') {

                if ($currentTag)
                    $tags[$currentTag][] = $currentData;
                else
                    $tags['description'] = $currentData;

                $currentData = '';
                preg_match('/@([a-zA-Z_]*)/', $line, $match);
                $currentTag = $match[1];
            }

            $currentData = trim($currentData.' '.$line);

        }
        if ($currentTag)
            $tags[$currentTag][] = $currentData;
        else
            $tags['description'] = $currentData;

        //parse tags
        $regex = array(
            'param' => array('/^@param\s*\t*([a-zA-Z_\\\[\]]*)\s*\t*\$([a-zA-Z_]*)\s*\t*(.*)/', array('type', 'name', 'description')),
            'url' => array('/^@url\s*\t*(.+)/', array('url')),
            'return' => array('/^@return\s*\t*([a-zA-Z_\\\[\]]*)\s*\t*(.*)/', array('type', 'description')),
        );
        foreach ($tags as $tag => &$data) {
            if ($tag == 'description') continue;
            foreach ($data as &$item) {
                if (isset($regex[$tag])) {
                    preg_match($regex[$tag][0], $item, $match);
                    $item = array();
                    $c = count($match);
                    for ($i =1; $i < $c; $i++) {
                        if (isset($regex[$tag][1][$i-1])) {
                            $item[$regex[$tag][1][$i-1]] = $match[$i];
                        }
                    }
                }
            }
            if (count($data) == 1)
                $data = $data[0];
        }

        return $tags;
    }

}