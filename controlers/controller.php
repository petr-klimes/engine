<?php
/*
 * hlavni controller stranek
 * pripoji k db
 * rozhodne jaky je typ stranky a jaky controlorovy deticky zavolat
 * aby se o pozadavek hezky postaraly
 */
class controller {

	public static function init() {
		//nactu config
		self::_loadConfig();
		
		// zpracuji debugovani
		self::_debug();
		
		// pripojik db
		self::_dbConnect();
		
		// vytvorim routu
		self::_createRoute();
		
		// na zaklade routy zavolam detatko, aby se o to postaralo
		return self::_callController();
	}

	/**
	 * nacte configuracni tridu v zavislosti na serveru (devel,production)
	 */
	private static function _loadConfig(){
		
	}
	
	/**
	 * Privatni metoda, obstaravajici debugovani
	 */
	private static function _debug() {
		
		
	}
	
	/**
	 * pripojeni k databazi
	 */
	private static function _dbConnect(){
		
	}
	
	/**
	 * vytvori routu z query pozadavku
	 */
	private static function _createRoute(){
		
	}
	
	/**
	 * zavola na yaklade routy prislusny controller
	 */
	private static function _callController(){
		
	}
}

?>
