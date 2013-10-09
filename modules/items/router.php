<?php

/**
 * rozdelovac cest
 */
class router {

	/**
	 * type cesty (product, grid ...
	 */
	public $type;

	/**
	 * rozparsovany url
	 */
	public $routa;

	/**
	 * metadata
	 */
	public $metaData = array();

	/**
	 * process router
	 */
	public function process() {
		$this->routa = $this->_getRouta();
		// first test on homepage, ajax, basket
		if (empty($this->routa)) {
			$this->type = "node";
		} elseif ($this->routa[0] == "ajax") {
			$this->type = "ajax";
		} elseif ($this->routa[0] == page::$config->url["basket"]) {
			$this->type = "basket";
		}

		//second test on product,grid a node	
		if (empty($this->type)) {
			if ($this->_typeTest("product")) {
				$this->type = "product";
			} elseif ($this->_typeTest("grid")) {
				$this->type = "grid";
			} elseif ($this->_typeTest("node")) {
				$this->type = "node";
			}
		}

		// third test on redirect
		if (empty($this->type)) {
			// TODO: check for old products, menu, node or noactive
			$this->type = "404";
		}

		return $this->_getOutput();
	}

	/**
	 * routa maker
	 */
	private function _getRouta() {
		$routa = array();
		if (isset($_REQUEST["pos"])) {
			$routa = explode("/", $_REQUEST["pos"]);
			$lastRout = end($routa);
			reset($routa);
			if (empty($lastRout)) {
				array_pop($routa);
			}
		}
		return $routa;
	}

	/**
	 * test function for routing type
	 */
	private function _typeTest($type) {
		$routa = $this->routa;
		$ret = false;
		switch ($type) {
			case "product":
				$result = dibi::query("SELECT id FROM products WHERE e_name=%s", end($routa))->fetchSingle();
				if (!empty($result)) {
					$this->metaData["id_pro"] = $result;
					$ret = true;
				}
				break;
			case "grid":
				$result = dibi::query("SELECT id, id_par FROM menu WHERE e_name=%s", end($routa))->fetchAll();
				if ($count = count($result)) {
					if ($count != 1) {
						array_slice($routa, 1);
						if (count($routa)) {
							foreach ($result AS $row) {
								$parentRes = dibi::query("SELECT id FROM menu WHERE e_name=%s AND id=%i", end($routa), $row->id_par)->fetchAll();
								if (count($parentRes)) {
									$this->metaData["id_grid"] = $row->id;
									$ret = true;
								}
							}
						}
					} else {
						$this->metaData["id_grid"] = $result[0]->id;
						$ret = true;
					}
				}
			case "node":
				$result = dibi::query("SELECT id FROM node WHERE e_name=%s", $routa[0]);
				if (count($result)) {
					$ret = true;
				}
				break;
		}
		return $ret;
	}

	/**
	 * check redirecting
	 */
	private function _checkRedirection() {
		// TODO::all redirection
		$base = utils::getBase();
		// look on table for redirecting
	}

	/**
	 *  getting output value for page class
	 */
	private function _getOutput() {
		$ret = new stdClass();
		$ret->routa = $this->routa;
		$ret->type = $this->type;
		$ret->metaData = $this->metaData;
		return $ret;
	}

}

?>
