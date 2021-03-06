<?php

require_once('Wool/Framework/Error.php');
require_once('Wool/Framework/ValidatorsStd.php');
require_once('Wool/Framework/ValidatorsTypes.php');
require_once('Wool/Framework/ValidatorsCustom.php');

class WoolValidation {
	private static $validators = array();
	private static $regValidators = array();

	public static function add($group, $field, $validationType, $params=array()) {
		self::$validators[$group][$field][$validationType] = $params;
	}

	public static function registerValidator($name, $class) {
		self::$regValidators[$name] = $class;
	}
	
	public static function classByName($name) {
		return isset(self::$regValidators[$name]) ? self::$regValidators[$name] : null;
	}
	
	public static function liveValidation($name, $params) {
		$cls = self::classByName($name);
		if (!$cls || !method_exists($cls, "liveValidation")) {
			return array();
		}
		
		return call_user_func(array($cls, 'liveValidation'), $params);
	}
	
	public static function validate($group, $field, $obj, $value, $pretty=null) {
		if (!isset(self::$validators[$group][$field])) { return true; }
		$id = spl_object_hash($obj);
		$pretty = coal($pretty, $field);
		$valid = true;
		
		foreach (self::$validators[$group][$field] as $type=>$valParams) {
			$params = array_merge(array('table' => $group, 'column' => $field), $valParams);
			
			if (!isset(self::$regValidators[$type])) {
				trigger_error("Unrecognised validator '{$type}'", E_USER_ERROR);
			}
			
			$valCls = self::$regValidators[$type];
			if (!call_user_func(array($valCls, 'validate'), $value, $params)) {
				if (isset($params['msg'])) {
					// Custom message.
					WoolErrors::add($id, $field, call_user_func(
						array($valCls, 'formatErrorMessage'), $params['msg'], $field,
						$pretty, $value
					));
				} else {
					// Standard validator message.
					WoolErrors::add($id, $field, call_user_func(
						array($valCls, 'errorMessage'), $field, $pretty, $value, $params
					));
				}
				
				$valid = false;
			}
		}
		
		return $valid;
	}
	
	public static function getFor($group, $field) {
		return coal(self::$validators[$group][$field], array());
	}
}
