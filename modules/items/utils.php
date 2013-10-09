<?php

/**
 * třída pomocných funkcí utils
 */
class utils {

	private static $menuIns = array();
	private static $urlIns = array();
	private static $addJs = array();
	private static $addCss = array();

	/**
	 * funkce pro porovnani casu zda momentalne se nachazi cas mezi nima
	 * 
	 * @param type $timeFrom
	 * @param type $timeTo
	 * @param type $format
	 * @return boolean
	 */
	public static function chechTimeIterval($timeFrom, $timeTo, $format = "unix") {
		if (empty($timeFrom) || empty($timeTo)) {
			return false;
		}
		$now = time();
		if ($format != "unix") {
			$timeFrom = self::_timeTransformToUnix($timeFrom, $format);
			$timeTo = self::_timeTransformToUnix($timeTo, $format);
		}
		if ($timeFrom < $now && $now < $timeTo) {
			$ret = true;
		} else {
			$ret = false;
		}
		return $ret;
	}

	/**
	 * pomocna funkce pro prevod casu
	 * 
	 * @param type $time
	 * @param type $format
	 * @return type
	 */
	public static function _timeTransformToUnix($time, $format) {
		$datetime = new DateTime($time);
		$datetime->format($format);
		return $datetime->getTimestamp();
	}

	/**
	 * pomocna fce pro odstraneni diakritiky
	 * 
	 * @param type $txt
	 * @return type
	 */
	public static function fOutDia($txt, $clear = true) {
		// TODO::zkontrolovat zda je UTF 8 pokud ne tak predelat do utf8
		$utf8table = array(
			"\xc3\xa1" => "a",
			"\xc3\xa4" => "a",
			"\xc4\x8d" => "c",
			"\xc4\x8f" => "d",
			"\xc3\xa9" => "e",
			"\xc4\x9b" => "e",
			"\xc3\xad" => "i",
			"\xc4\xbe" => "l",
			"\xc4\xba" => "l",
			"\xc5\x88" => "n",
			"\xc3\xb3" => "o",
			"\xc3\xb6" => "o",
			"\xc5\x91" => "o",
			"\xc3\xb4" => "o",
			"\xc5\x99" => "r",
			"\xc5\x95" => "r",
			"\xc5\xa1" => "s",
			"\xc5\xa5" => "t",
			"\xc3\xba" => "u",
			"\xc5\xaf" => "u",
			"\xc3\xbc" => "u",
			"\xc5\xb1" => "u",
			"\xc3\xbd" => "y",
			"\xc5\xbe" => "z",
			"\xc3\x81" => "A",
			"\xc3\x84" => "A",
			"\xc4\x8c" => "C",
			"\xc4\x8e" => "D",
			"\xc3\x89" => "E",
			"\xc4\x9a" => "E",
			"\xc3\x8d" => "I",
			"\xc4\xbd" => "L",
			"\xc4\xb9" => "L",
			"\xc5\x87" => "N",
			"\xc3\x93" => "O",
			"\xc3\x96" => "O",
			"\xc5\x90" => "O",
			"\xc3\x94" => "O",
			"\xc5\x98" => "R",
			"\xc5\x94" => "R",
			"\xc5\xa0" => "S",
			"\xc5\xa4" => "T",
			"\xc3\x9a" => "U",
			"\xc5\xae" => "U",
			"\xc3\x9c" => "U",
			"\xc5\xb0" => "U",
			"\xc3\x9d" => "Y",
			"\xc5\xbd" => "Z");
		$text = strtr($txt, $utf8table);
		if ($clear) {
			$text = Str_Replace(Array(" ", "_", "+", "/", "*", ";", ",", "[", "]", "|"), "-", $text); //nahradí mezery a podtržítka pomlčkami
			$text = Str_Replace(Array('--'), "-", $text);
			$text = Str_Replace(Array('--'), "-", $text);
			$text = Str_Replace(Array("+", "/", "*", "?" ,"&", "(", ")", ".", ":"), "", $text);
			$text = Str_Replace(Array('"'), "", $text);
			$text = Str_Replace(Array("'"), "", $text);
			$text = Str_Replace(Array("˙"), "", $text);
		}
		return $text;
	}

	/**
	 * funkce pro nahrazeni specialnich znaku v textu
	 * 
	 * @param type $txt
	 * @return type
	 */
	public static function replaceToEntites($txt) {

		$table = array(
			"²" => "&sup2;",
			"³" => "&sup3;",
			"µ" => "&micro;",
			"¼" => "&franc14;",
			"½" => "&franc12;",
			"¾" => "&franc34;",
			"⁄" => "&frasl;",
			"/" => "&frasl;",
			'"' => "&quot;");
		$text = strtr($txt, $table);
		$text = Str_Replace(Array("+", "/", "*", "?"), "", $text);
		return $text;
	}

	/**
	 * funkce pro zjisteni base parametru
	 * 
	 * @return string
	 */
	public static function getBase() {
		if (page::$base) {
			$base = self::$base;
		} else {
			if ($_SERVER['SERVER_NAME'] == '127.0.0.1') {
				$base = 'http://' . $_SERVER['HTTP_HOST'] . '/opp/';
			} else {
				$base = 'http://' . $_SERVER['HTTP_HOST'] . '/';
			}
			page::$base = $base;
		}
		return $base;
	}

	/**
	 * function for deleting cache
	 */
	public static function delCache() {
		cache::deleteCache();
		$cache = new cacheDb();
		$cache->ini("def");
		$cache->clear();
	}

	/**
	 * multybytovy replace
	 * 
	 * @param type $search
	 * @param type $replace
	 * @param type $subject
	 * @param type $count
	 * @return boolean
	 */
	public static function mb_replace($search, $replace, $subject, &$count = 0) {
		if (!is_array($search) && is_array($replace)) {
			return false;
		}
		if (is_array($subject)) {
			// call mb_replace for each single string in $subject
			foreach ($subject as &$string) {
				$string = &mb_replace($search, $replace, $string, $c);
				$count += $c;
			}
		} elseif (is_array($search)) {
			if (!is_array($replace)) {
				foreach ($search as &$string) {
					$subject = mb_replace($string, $replace, $subject, $c);
					$count += $c;
				}
			} else {
				$n = max(count($search), count($replace));
				while ($n--) {
					$subject = mb_replace(current($search), current($replace), $subject, $c);
					$count += $c;
					next($search);
					next($replace);
				}
			}
		} else {
			$parts = mb_split(preg_quote($search), $subject);
			$count = count($parts) - 1;
			$subject = implode($replace, $parts);
		}
		return $subject;
	}

	/**
	 * fuknce pro logování 
	 * @param type $text
	 * @param type $prefixName
	 * @return boolean 
	 */
	public static function log($text, $prefixName = "log") {
		$date = new DateTime();
		$text = $date->format("d-m-y H:i:s") . " " . $text . "\r\n";
		$path = "./logs/" . $prefixName . "-" . $date->format('dmY') . ".log";
		try {
			$old = umask(0);
			umask(0);
			$handle = fopen($path, "a");
			chmod($path, 0755);
			umask($old);
			fwrite($handle, $text);
			fclose($handle);
		} catch (Exception $e) {
			echo 'Chyba při ukládání logu: ', $e->getMessage(), "\n";
		}
	}

	/**
	 * method for creating ident from params
	 * Exampl:         
	  $identArray = array("product" => $id, "whitoutEname" => $whitoutEname, "absolute" => $absolute);
	  $identify = utils::getIdent($identArray);
	 * 
	 * @param type $arr
	 * @return type 
	 */
	public static function getIdent($arr) {
		$ident = "";
		foreach ($arr as $key => $value) {
			if (!empty($value)) {
				$ident .= $key . "-" . $value . "-";
			}
		}
		if (empty($ident)) {
			$ident = false;
		} else {
			$ident = substr($ident, 0, -1);
		}
		return $ident;
	}

	/**
	 * method for creating %search% %text% - obsolete
	 * 
	 * @param type $text
	 * @return string
	 */
	public function makeSearchText($text) {
		// %slovo% %slovo%
		$seachText = "";
		$text = trim(mb_strtoupper($text));
		$text = Str_Replace(Array(" ", "_", "+", "/", "*", "?", "%"), "", $text);
		$text = Str_Replace("-", "", $text);
		$words = explode(" ");
		$len = count($words);
		foreach ($words AS $word) {
			$i++;
			if ($i == $len) {
				$end = "";
			} else {
				$end = " ";
			}
			$seachText .= "%" . $word . "%" . $end;
		}
		return $seachText;
	}

	/**
	 *  method for getting url instance (singleton +/-)
	 * 
	 * @param type $type
	 * @return \url
	 */
	public static function getUrlIns($type = "relative") {
		$keyArr = array("url" => "ins", "type" => $type);
		$key = utils::getIdent($keyArr);
		if (!empty(self::$urlIns[$key])) {
			$urlIns = self::$urlIns[$key];
		} else {
			$urlIns = new url($type);
			self::$urlIns[$key] = $urlIns;
		}
		return $urlIns;
	}

	/**
	 *  method for getting menu instance (singleton +/-)
	 * 
	 * @param type $simple
	 * @param type $isItemsVisible
	 * @return \menu
	 */
	public static function getMenuIns($simple = false, $isItemsVisible = true) {
		$keyArr = array("menu" => "ins", "simple" => $simple, "isItemsVisible" => $isItemsVisible);
		$key = utils::getIdent($keyArr);
		if (!empty(self::$menuIns[$key])) {
			$menuIns = self::$menuIns[$key];
		} else {
			$menuIns = new menu();
			$menuIns->init($simple, $isItemsVisible);
			self::$menuIns[$key] = $menuIns;
		}
		return $menuIns;
	}

	/**
	 * addJs to page
	 * 
	 * @param type $key
	 * @param type $value
	 */
	public static function addJs($key, $value, $head = true) {
		if (!array_key_exists($key, self::$addJs)) {
			self::$addJs[$key] = $value;
		}
	}

	/**
	 * addCss to page
	 * 
	 * @param type $key
	 * @param type $value
	 */
	public static function addCss($key, $value) {
		if (!array_key_exists($key, self::$addCss)) {
			self::$addCss[$key] = $value;
		}
	}

	/**
	 * send mail
	 * 
	 * @param type $to
	 * @param type $subject
	 * @param type $message
	 * @param type $header
	 * @param type $from
	 */
	public static function sendMail($to, $subject = '(No subject)', $message = '', $header = '', $from = 'anvil-obchod@anvil.cz') {
		// na lokale maily neodesilam
		try {
			if ($_SERVER['SERVER_NAME'] != '127.0.0.1') {
				$header_ = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=UTF-8' . "\r\n" . 'From: ' . $from . "\r\n";
				mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, $header_ . $header);
			}
		} catch (ErrorException $ex) {
			utils::log('Chyba při odesílání emailu !: "' . $ex->getMessage, "order-err");
			return "Chyba: " . $ex->getMessage;
		}
	}

	/**
	 * format nuber for price
	 * 
	 * @param type $number
	 * @return string
	 */
	public static function priceNumberFormat($number) {
		return number_format($number, 0, ',', ' ');
	}

	/**
	 * funkce pro zkraceni textu (mb) + ...
	 * 
	 * @param type $text
	 * @param type $lenght
	 * @return string
	 */
	public static function fCutTo($text, $lenght) {
		if (mb_strlen($text) > $lenght) {
			$text = mb_substr($text, 0, $lenght);
			$text = mb_substr($text, 0, StrRPos($text, " ")) . " ...";
		};
		return $text;
	}

	/**
	 * funkce pro vytváření obrázků
	 * TODO: presunout do img class
	 * 
	 * @param type $sorce
	 * @param type $dest
	 * @param type $type
	 * @return boolean
	 */
	public static function imageCreate($sorce, $dest, $type) {
		if (file_exists($sorce)) {
			// zjistim slozku a nastavim ji 0755
			$dir = substr($dest, 0, strrpos($dest, "/") + 1);
			$old = umask(0);
			umask(0);
			if(is_dir($dir)){
				chmod($dir, 0777);
			}else{
				mkdir($dir, 0777);
			}
			switch ($type) {
				// big
				case 'b':
					$image = new simpleImage();
					$image->load($sorce);
					$image->best_fit(1024, 768);
					$image->save($dest);
					break;
				// grid
				case 'm':
					$image = new simpleImage();
					$image->load($sorce);
					$image->best_fit(230, 150);
					$image->save($dest);
					break;
				// detail
				case 's':
					$image = new simpleImage();
					$image->load($sorce);
					$image->best_fit(370, 240);
					$image->save($dest);
					break;
				// thumb
				case 't':
					$image = new simpleImage();
					$image->load($sorce);
					$image->fit_to_height(50);
					$image->save($dest);
					break;
			}
			umask($old);
			return true;
		} else {
			return false;
		}
	}

	public static function allImagesCreate($origName, $newName, $dir, $file, $productId, $prior = 0) {
		// todo:: predelat do imgManu ->zde volat imagemana -> mit vytvorenou instanci
		$ext = strtolower(substr($origName, strrpos($origName, ".")));
		$allowArray = array(".jpg", ".jpeg", ".png", ".gif");
		if (in_array($ext, $allowArray)) {
			$dirPath = $dir . "/";
			$imgTypes = array("b", "m", "s", "t");
			foreach ($imgTypes AS $imgType) {
				$nameImg = $dirPath . $newName . "-" . $imgType . $ext;
				utils::imageCreate($file, $nameImg, $imgType);
			}
			$arr = array(
				'name' => $newName,
				'ext' => $ext,
				'src' => $dirPath,
				'prior' => -1,
				'alt' => $newName,
				'title' => $newName,
				'active' => 1,
				'id_pro' => $productId,
				'prior' => $prior
			);
			// zeptam se zda uz nahodou takovej neni
			$dupl = dibi::query("SELECT * FROM img WHERE name=%s AND id_pro=%i", $newName, $productId)->fetchAll();
			if (count($dupl)) {
				foreach ($dupl as $dup) {
					dibi::query("DELETE FROM img WHERE id=%i", $dup->id);
				}
			}
			dibi::query('INSERT INTO img', $arr);
			return true;
		} else {
			return false;
		}
	}

}

?>
