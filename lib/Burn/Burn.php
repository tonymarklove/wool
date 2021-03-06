<?php

require_once('Burn/JSMin.php');
require_once('Burn/CssCompressor.php');

define('SLASH', DIRECTORY_SEPARATOR);

class Burn {
	private static $vars = array();
	private static $varsModified = 0;
	
	// Takes a file and returns all files that are referenced within, or the file
	// itself if it is the end of the line.
	private static function expandFileList($filePath) {
		$info = pathinfo($filePath);
		$path = $info['dirname'];
		$filename = $info['filename'];
		$extension = $info['extension'];

		$sourcePath = $GLOBALS['BASE_PATH'] . SLASH . 'public' . SLASH . $path . SLASH . $filename;

		$sourceFile = "{$sourcePath}.{$extension}";
		$confFile = "{$sourcePath}.conf";

		$files = array('conf'=>array($confFile), 'source'=>array(), 'uri'=>array());
		
		// Load files directly that have no config.
		if (!file_exists($confFile)) {
			if (!file_exists($sourceFile)) {
				if (DEVELOPER) {
					trigger_error("Burn attempting to load non-existent file: " . $sourceFile, E_USER_NOTICE);
				}
				return array();
			}
			
			$files['source'][] = $sourceFile;
			$files['uri'][] = $filePath;
			$files['path'][] = $path;
		} else {
			$conf = file_get_contents($confFile);
			$conf = json_decode($conf, true);

			foreach ($conf['files'] as $file) {
				$files = array_merge_recursive($files, self::expandFileList($path . '/' . $file));
			}
		}
		
		return $files;
	}
	
	public static function expandDebugFileList($filePath) {
		if (DEVELOPER) {
			$files = self::expandFileList($filePath);
			return $files['uri'];
		} else {
		 return array($filePath);
		}
	}

	public static function start() {
		$path = $_GET['path'];
		$filename = $_GET['file'];
		$extension = $_GET['ext'];
		
		$cachedPath = $GLOBALS['BASE_PATH'] . SLASH . 'var' . SLASH . 'burn' . SLASH . $path . SLASH . $filename;
		$debugFile = "{$cachedPath}.{$extension}";
		$minFile = "{$cachedPath}.min.{$extension}";
		
		$files = self::expandFileList($path . '/' . $filename . '.' . $extension);
		if (!$files) {
			header("HTTP/1.1 404 Not Found", true, 404);
			exit;
		}
		
		// Work out relative paths.
		$currentPath = $files["path"][0];
		
		foreach ($files["path"] as &$path) {
			$path = str_replace($currentPath . "/", "", $path);
			$path = str_replace($currentPath, "", $path);
		}
		
		self::loadVariables();
		
		$lastModifiedSource = lastModifiedTime(array_merge($files['conf'], $files['source']));
		$lastModifiedSource = max($lastModifiedSource, self::$varsModified);

		// Output
		if ($extension == 'css') {
			header("Content-type: text/css");
		} else {
			header("Content-type: application/x-javascript");
		}
		
		$modifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : -1;
		$noneMatch = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) : "";
		$etag = md5($lastModifiedSource.$filename);
		
		header("Cache-Control: public");
		header("ETag: {$etag}");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastModifiedSource) . " GMT");
		
		if ($lastModifiedSource <= $modifiedSince || $noneMatch == $etag) {
			header("HTTP/1.1 304 Not Modified", true, 304);
			exit;
		}
		header("Expires: " . gmdate("D, d M Y H:i:s", time()+86400) . " GMT");
		
		if (DEVELOPER) {
			$output = self::updateDebug($debugFile, $lastModifiedSource, $files['source'], $extension, $files['path']);
		} else {
			$output = self::updateMinified($minFile, $lastModifiedSource, $files['source'], $extension, $files['path']);
		}
		
		echo $output;
	}
	
	private static function convertUrls($contents, $file, $relPath) {
		$regex = "/url\([\"\']?([^\"\'\)]+)[\"\']?\)/";
		return $relPath ? preg_replace($regex, "url('{$relPath}/$1')", $contents) : $contents;
	}
	
	private static function loadVariables() {
		$file = $GLOBALS['BASE_PATH'] . '/var/burn/variables.php';
		
		if (!file_exists($file)) {
			return;
		}
		
		self::$vars = require($file);
		self::$varsModified = lastModifiedTime($file);
	}
	
	private static function insertVariables($contents) {
		if (!self::$vars) {
			return $contents;
		}
		
		$regex = "/\[([^\s]+):([^\s]+)\]/";
		return preg_replace_callback($regex, array("self", "insertVariableCallback"), $contents);
	}
	
	public static function insertVariableCallback($matches) {
		return isset(self::$vars[$matches[1]]) ? self::$vars[$matches[1]] : $matches[2];
	}
	
	private static function minify($files, $extension, $relPath) {
		$min = array();
		foreach ($files as $num=>$file) {
			if ($extension == 'css') {
				$contents = file_get_contents($file);
				$contents = self::convertUrls($contents, $file, $relPath[$num]);
				$contents = self::insertVariables($contents);
				$min[] = CssCompressor::process($contents);
			} else {
				$min[] = JSMin::minify(file_get_contents($file));
			}
		}
		return join("\r\n", $min);
	}

	private static function joinDebugFiles($files, $extension, $relPath) {
		$debug = array();

		foreach ($files as $num=>$file) {
			$contents = file_get_contents($file);
			
			if ($extension == "css") {
				$contents = self::convertUrls($contents, $file, $relPath[$num]);
				$contents = self::insertVariables($contents);
			}

			$debug[] = $contents;
			$debug[] = '';
		}

		return join("\r\n", $debug);
	}

	private static function updateMinified($minFile, $lastModifiedSource, $files, $extension, $relPath) {
		$min = '';
		if (lastModifiedTime($minFile) < $lastModifiedSource) {
			// We need to recache.
			$min = self::minify($files, $extension, $relPath);
			if (!$min) {
				$min = self::joinDebugFiles($files, $extension, $relPath);
			}
			file_put_contents_mkdir($minFile, $min);
		} else {
			$min = file_get_contents($minFile);
		}
		return $min;
	}

	private static function updateDebug($debugFile, $lastModifiedSource, $files, $extension, $relPath) {
		$debug = '';
		if (lastModifiedTime($debugFile) < $lastModifiedSource) {
			// We need to recreate debug cache.
			$debug = self::joinDebugFiles($files, $extension, $relPath);
			file_put_contents_mkdir($debugFile, $debug);
		} else {
			$debug = file_get_contents($debugFile);
		}
		return $debug;
	}
}
