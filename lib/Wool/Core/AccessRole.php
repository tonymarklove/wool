<?php

require_once('Wool/Core/AccessRoleUser.php');
require_once('Wool/Core/AccessLocation.php');

class AccessRole extends WoolTable {
	// Common role types
	const ADMIN = 1;
	
	public static function define() {
	}
	
	public static function loginPageFor($id) {
		return WoolDb::fetchOne(
			"select loginUrl from access_roles where accessRoleId = ?",
			$id
		);
	}
	
	public static function deniedPageFor($id) {
		return WoolDb::fetchOne(
			"select deniedUrl from access_roles where accessRoleId = ?",
			$id
		);
	}
}
