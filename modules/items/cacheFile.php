<?php

/**
 * Class for cache stored in file 
 */
class cacheFile {

	private $key;
	private $path;
	private $default_path = "./cache/";
	private $expirationTime = 86400;

	/**
	 * Method for geting cache from the store, if cache is expired or doesn exist return false
	 * 
	 * @param type $ident
	 * @param type $category
	 * @return mixed 
	 */
	public function getCache($ident, $category = NULL) {
		$key = $this->getKey($ident);
		$path = $this->getFilePath($key, $category);
		if ($this->timeVerification($path)) {
			return $this->getFileContens($path);
		} else {
			return false;
		}
	}

	/**
	 * method for saving data to the file (ident and category are from datainstanc on cache manager)
	 * 
	 * @param type $data 
	 */
	public function saveCache($data) {
		$data = serialize($data);
		try {
			$old = umask(0);
			umask(0);
			file_put_contents($this->path, $data);
			chmod($this->path, 0755);
			umask($old);
		} catch (Exception $e) {
			utils::log('Err on saving data to file "' . $this->path . '": ' . $e->getMessage(), "cache-err");
		}
	}

	/**
	 * method for deleting cache for whole category
	 * 
	 * @param type $category 
	 */
	public function deleteCache($category = NULL) {
		$dir = $this->getDirPath($category);
		try {
			if ($category == NULL) {
				$items = glob($dir . '*', GLOB_BRACE);
			} else {
				$items = glob($dir . '*.cch', GLOB_BRACE);
			}
			
			if (is_array($items)) {
				foreach ($items as $item) {
					if (is_dir($item)) {
						// recursion
						$cat = substr($item, strrpos($item, "/"));
						$this->deleteCache($cat);
					} else {
						unlink($item);
					}
				}
			}
		
			if ($category != NULL) {
				rmdir($dir);
			}
		} catch (Exception $e) {
			utils::log('Err on deleting cache on dir "' . $dir . '": ' . $e->getMessage(), "cache-err");
		}
	}

	/**
	 * Method for cleaning cache for specific one ident
	 * 
	 * @param type $ident
	 * @param type $category 
	 */
	public function clearCache($ident, $category = NULL) {
		$key = $this->getKey($ident);
		$path = $this->getFilePath($key, $category);
		try {
			unlink($path);
		} catch (Exception $e) {
			utils::log('Err on cleaning cache on file "' . $path . '": ' . $e->getMessage(), "cache-err");
		}
	}

	private function getKey($ident) {
		$key = $ident . "-" . md5($ident);
		$this->key = $key;
		return $key;
	}

	private function timeVerification($path) {
		if (isset($_GET["nocache"]) AND $_GET["nocache"] == 1) {
			return false;
		} else {
			try {
				if (file_exists($path)) {
					if ((time() - $this->expirationTime) < filemtime($path)) {
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			} catch (Exception $e) {
				utils::log('Err on verification on file "' . $path . '": ' . $e->getMessage(), "cache-err");
			}
		}
	}

	private function getFileContens($path) {
		try {
			$content = file_get_contents($path);
		} catch (Exception $e) {
			utils::log('Err on getting data from file "' . $path . '": ' . $e->getMessage(), "cache-err");
		}
		$data = unserialize($content);
		return $data;
	}

	private function getFilePath($key, $category) {
		if ($category == NULL) {
			$path = $this->default_path . $key . ".cch";
		} else {
			$dir = $this->default_path . $category . "/";
			if (!is_dir($dir)) {
				try {
					mkdir($dir);
					chmod($dir, 0755);
				} catch (Exception $e) {
					utils::log('Err on creatin directory "' . $dir . '": ' . $e->getMessage(), "cache-err");
				}
			}
			$path = $dir . $key . ".cch";
		}
		$this->path = $path;
		return $path;
	}

	private function getDirPath($category) {
		if ($category == NULL) {
			$dir = $this->default_path;
		} else {
			$dir = $this->default_path . $category . "/";
		}
		return $dir;
	}

}

?>
