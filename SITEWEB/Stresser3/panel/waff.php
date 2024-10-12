<?php

class xWAF {
	function __construct() {
		$this->IPHeader = "REMOTE_ADDR";
		$this->CookieCheck = false;
		$this->CookieCheckParam = 'doLogin';
		return true;
	}
	function shorten_string($string, $wordsreturned) {
		$retval = $string;
		$array = explode(" ", $string);
		if (count($array)<=$wordsreturned){
			$retval = $string;
		} else {
			array_splice($array, $wordsreturned);
			$retval = implode(" ", $array)." ...";
		}
		return $retval;
	}
	function vulnDetectedHTML($Method, $BadWord, $DisplayName, $TypeVuln) {
		header('HTTP/2.0 403 Forbidden');
		echo '<meta http-equiv="refresh" content="0;URL=index.php">';
		die(); // Block request.
	}
	function getArray($Type) {
		switch ($Type) {
			case 'SQL':
				return array(
							"'",
							'Â´',
							"' or ''='",
							"' or ''='",
							'SELECT FROM',
							'SELECT * FROM',
							"';SELECT * FROM `users`",
							"or 1-- -'",
							"or 1 or '1",
							"or 1 or",
							'or 1=1',
							'OR 1=1',
							"';SELECT * FROM",
							'union',
							"';UPDATE `users",
							"';UPDATE `users`",
							'UNION',
							"';UNION",
							'ORDER BY',
							'HAVING',
							'GROUP BY',
							"';GROUP BY",
							'-- or # ',
							"' OR 1 -- -",
							"' OR 'x'='x",
							"' or ''^'",
							"' or ''&'",
							"' or '' '",
							"' or ''-'",
							'" or ""-"',
							"')) or (('x'))=(('x",
							"admin' or '1'='1'--",
							"admin'or 1=1 or ''='",
							"1234 ' AND 1=0 UNION ALL SELECT 'admin', '81dc9bdb52d04dc20036dbd8313ed055",
							"1234 ' AND 1=0 UNION ALL SELECT 'gum4admin', '81dc9bdb52d04dc20036dbd8313ed055",
							"1234 ' AND 1=0 UNION ALL SELECT 'gum4off', '81dc9bdb52d04dc20036dbd8313ed055",
							'admin" or 1=1/*',
							'gum4admin" or 1=1/*',
							'gum4off" or 1=1/*',
							'UNION ALL SELECT NULL#',
							"' AND id IS NULL; --",
							"'LIKE'",
							" OR 1=1",
							'\\',
							'admin") or "1"="1"/*',
							'gum4off") or "1"="1"/*',
							'gum4admin") or "1"="1"/*',
							'")) or (("x"))=(("x',
							"'^'",
							"'&'",
							"' '",
							'"*"',
							'"&"',
							"' or 'x'='x",
							"1234 ' AND 1=0 UNION ALL SELECT 'admin', '81dc9bdb52d04dc20036dbd8313ed055",
							'1234 " AND 1=0 UNION ALL SELECT "admin", "81dc9bdb52d04dc20036dbd8313ed055',
							'1234 " AND 1=0 UNION ALL SELECT "gum4admin", "81dc9bdb52d04dc20036dbd8313ed055',
							'1234 " AND 1=0 UNION ALL SELECT "gum4off", "81dc9bdb52d04dc20036dbd8313ed055',
							'admin") or "1"="1"/*',
							'admin") or "1"="1"--',
							"' UNION ALL SELECT system_user(),user();",
							"'''''''''''''UNION SELECT '2",
							" UNION ALL SELECT 'INJ'||'ECT'||'XXX',2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25#",
							';DELETE FROM `',
							'doLogin',
							"';DELETE FROM `",
							';UPDATE users `',
							"';UPDATE users `users` SET",
							'UPDATE `users` SET `rank`',
							';UPDATE `users` SET `rank`',
							';UPDATE `users`; SET .`rank`',
							"' ;UPDATE `users`; SET .`rank`",
							'UPDATE users',
							'yum',
							';UPDATE users `username`',
							'`',
							'";UPDATE `users`',
							"';UPDATE `users`",
							'UPDATE users',
							'FROM',
							"';FROM",
							'delete',
							'from', 
							'*',
							';UPDATE `users',
							';UPDATE `users`',
							';SELECT FROM `',
							';SELECT FROM',
							"';UPDATE `users` SET `rank` = '1'",
							"' ;UPDATE `users` SET `rank` = '1'",
							"';UPDATE `users` SET `rank`",
							"';UPDATE `users",
							"';UPDATE",
							"';username",
							"';rank",
							'users',
							'WHERE',
							"';WHERE",
							';DELETE FROM `',
							"';DELETE FROM `",
							"';DELETE FROM `users`",
							"' ;DELETE FROM `users`",
							'DELETE',
							';DELETE',
							'DoLogin',
							';FROM',
							'" #',
							'+',
							' + ',
							'UDPATE',
							'rank',
							'membership',
							'UPDATE',
							"';DELETE",
							'UDPATE users SET',
							"';UDPATE users SET",
							"';UPDATE users SET",
							'UPDATE users SET',
							';DELETE FROM `',
							'WHERE username',
							'DROP TABLE',
							"';DROP TABLE",
							'0x50',
							'mid((select',
							'union(((((((',
							'http-equiv',
							'stressit.club',
							'pornhub.com',
							'concat(0x',
							'concat(',
							'OR boolean',
							'or HAVING',
							"OR '1", 
							'0x3c62723e3c62723e3c62723e',
							'0x3c696d67207372633d22',
							'+#1q%0AuNiOn all#qa%0A#%0AsEleCt',
							'unhex(hex(Concat(',
							'Table_schema,0x3e,',
							'0x00', // \0  [This is a zero, not the letter O]
							'0x08', // \b
							'0x09', // \t
							'0x0a', // \n
							'0x0d', // \r
							'0x1a', // \Z
							'0x22', // \"
							'0x25', // \%
							'0x27', // \'
							'0x5c', // \\
							'0x5f'  // \_
							);
				break;
			case 'XSS':
				return array('<img',
						'img>',
						'<image',
						'document.cookie',
						'onerror()',
						'script>',
						'<script',
						'alert(',
						'window.',
						'String.fromCharCode(',
						'javascript:',
						'http-equiv',
						'pornhub.com',
						'stressit.club',
						'onmouseover="',
						'<BODY onload',
						'<style',
						'svg onload');
				break;
			
			default:
				return false;
				break;
		}
	}
	function arrayFlatten(array $array) {
	    $flatten = array();
	    array_walk_recursive($array, function($value) use(&$flatten) {
	        $flatten[] = $value;
	    });
	    return $flatten;
	}
	function sqlCheck($Value, $Method, $DisplayName) {
		// For false alerts.
		$Replace = array("can't" => "cant",
						"don't" => "dont");
		foreach ($Replace as $key => $value_rep) {
			$Value = str_replace($key, $value_rep, $Value);
		}
		$BadWords = $this->getArray('SQL');
		foreach ($BadWords as $BadWord) {
			if (strpos(strtolower($Value), strtolower($BadWord)) !== false) {
				// String contains some Vuln.
				$this->vulnDetectedHTML($Method, $BadWord, $Value, 'SQL Injection');
			}
		}
	}
	function xssCheck($Value, $Method, $DisplayName) {
		// For false alerts.
		$Replace = array("<3" => ":heart:");
		foreach ($Replace as $key => $value_rep) {
			$Value = str_replace($key, $value_rep, $Value);
		}
		$BadWords = $this->getArray('XSS');

		foreach ($BadWords as $BadWord) {
			if (strpos(strtolower($Value), strtolower($BadWord)) !== false) {
			    // String contains some Vuln.

				$this->vulnDetectedHTML($Method, $BadWord, $DisplayName, 'XSS (Cross-Site-Scripting)');
			}
		}
	}
	function is_html($string) {
		return $string != strip_tags($string) ? true:false;

	}
	function santizeString($String) {
		$String = escapeshellarg($String);
		$String = htmlentities($String);
		$XSS = $this->getArray('XSS');
		foreach ($XSS as $replace) {
			$String = str_replace($replace, '', $String);
		}
		$SQL = $this->getArray('SQL');
		foreach ($SQL as $replace) {
			$String = str_replace($replace, '', $String);
		}
		return $String;
	}
	function htmlCheck($value, $Method, $DisplayName) {
		if ($this->is_html(strtolower($value)) !== false) {
			// HTML Detected!
			$this->vulnDetectedHTML($Method, "HTML CHARS", $DisplayName, 'XSS (HTML)');
		}
	}
	function arrayValues($Array) {
		return array_values($Array);
	}
	function checkGET() {
		foreach ($_GET as $key => $value) {
			if (is_array($value)) {
				$flattened = $this->arrayFlatten($value);
				foreach ($flattened as $sub_key => $sub_value) {
					$this->sqlCheck($sub_value, "_GET", $sub_key);
					$this->xssCheck($sub_value, "_GET", $sub_key);
					$this->htmlCheck($sub_value, "_GET", $sub_key);
				}
			} else {
				$this->sqlCheck($value, "_GET", $key);
				$this->xssCheck($value, "_GET", $key);
				$this->htmlCheck($value, "_GET", $key);
			}
		}
	}
	function checkPOST() {
		foreach ($_POST as $key => $value) {
			if (is_array($value)) {
				$flattened = $this->arrayFlatten($value);
				foreach ($flattened as $sub_key => $sub_value) {
					$this->sqlCheck($sub_value, "_POST", $sub_key);
					$this->xssCheck($sub_value, "_POST", $sub_key);
					$this->htmlCheck($sub_value, "_POST", $sub_key);
				}
			} else {
				$this->sqlCheck($value, "_POST", $key);
				$this->xssCheck($value, "_POST", $key);
				$this->htmlCheck($value, "_POST", $key);
			}
		}
	}
	function checkCOOKIE() {
		foreach ($_COOKIE as $key => $value) {
			if (is_array($value)) {
				$flattened = $this->arrayFlatten($value);
				foreach ($flattened as $sub_key => $sub_value) {
					$this->sqlCheck($sub_value, "_COOKIE", $sub_key);
					$this->xssCheck($sub_value, "_COOKIE", $sub_key);
					$this->htmlCheck($sub_value, "_COOKIE", $sub_key);
				}
			} else {
				$this->sqlCheck($value, "_COOKIE", $key);
				$this->xssCheck($value, "_COOKIE", $key);
				$this->htmlCheck($value, "_COOKIE", $key);
			}
		}
	}
	function gua() {
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			return $_SERVER['HTTP_USER_AGENT'];
		}
		return md5(rand());
	}
	function cutGua($string) {
		$five = substr($string, 0, 4);
		$last = substr($string, -3);
		return md5($five.$last);
	}
	function getCSRF() {
		if (isset($_SESSION['token'])) {
			$token_age = time() - $_SESSION['token_time'];
			if ($token_age <= 300){    /* Less than five minutes has passed. */
				return $_SESSION['token'];
			} else {
				$token = md5(uniqid(rand(), TRUE));
				$_SESSION['token'] = $token . "asd648" . $this->cutGua($this->gua());
				$_SESSION['token_time'] = time();
				return $_SESSION['token'];
			}
		} else {
			$token = md5(uniqid(rand(), TRUE));
			$_SESSION['token'] = $token . "asd648" . $this->cutGua($this->gua());
			$_SESSION['token_time'] = time();
			return $_SESSION['token'];
		}
	}
	function verifyCSRF($Value) {
		if (isset($_SESSION['token'])) {
			$token_age = time() - $_SESSION['token_time'];
			if ($token_age <= 300){    /* Less than five minutes has passed. */
				if ($Value == $_SESSION['token']) {
					$Explode = explode('asd648', $_SESSION['token']);
					$gua = $Explode[1];
					if ($this->cutGua($this->gua()) == $gua) {
						// Validated, Done!
						unset($_SESSION['token']);
						unset($_SESSION['token_time']);
						return true;
					}
					unset($_SESSION['token']);
					unset($_SESSION['token_time']);
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	function useCloudflare() {
		$this->IPHeader = "HTTP_CF_CONNECTING_IP";
	}
	function useBlazingfast() {
		$this->IPHeader = "X-Real-IP";
	}
	function customIPHeader($String = 'REMOTE_ADDR') {
		$this->IPHeader = $String;
	}
	function antiCookieSteal($listparams = 'doLogin') {
		$this->CookieCheck = true;
		$this->CookieCheckParam = $listparams;
	}
	function cookieCheck() {
		// Check Anti-Cookie steal trick.
		if ($this->CookieCheck == true) {
			// Check then.
			if (isset($_SESSION)) { // Session set.
				if (isset($_SESSION[$this->CookieCheckParam])) { // Logged.
					if (!(isset($_SESSION['xWAF-IP']))) {
						$_SESSION['xWAF-IP'] = $_SERVER[$this->IPHeader];
						return true;
					} else {
						if (!($_SESSION['xWAF-IP'] == $_SERVER[$this->IPHeader])) {
							// Changed IP.
							unset($_SESSION['xWAF-IP']);
							unset($_SESSION);
							@session_destroy();
							@session_start();
							return true;
						}
					}
				}
			}
		}
	}
	function start() {
		@session_start();
		@$this->checkGET();
		@$this->checkPOST();
		@$this->checkCOOKIE();
		if ($this->CookieCheck == true) {
			$this->cookieCheck();
		}
	}

}
?>