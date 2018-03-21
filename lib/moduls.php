<?php

/**
 * @package redaxo\modulemanager
 *
 * @internal
 */
class rex_install_moduls
{
	private static $updateModuls;
	private static $addModuls;
	private static $myModuls;

	public static function getUpdateModuls()
	{
//		if (is_array(self::$addModuls)) {
//			return self::$addModuls;
//		}

		self::$updateModuls = self::getModuls('index.php');

		foreach (self::$updateModuls as $key => $modul) {
			if (rex_module::exists($key) && isset($modul['files'])) {
				self::unsetOlderVersions($key, rex_module::get($key)[$key]['revision']);
			} else {
				unset(self::$updateModuls[$key]);
			}
		}
		return self::$updateModuls;
	}

	public static function updatedPackage($package, $fileId)
	{
		self::unsetOlderVersions($package, self::$updateModuls[$package]['files'][$fileId]['version']);
	}

	private static function unsetOlderVersions($modul, $version)
	{
		foreach (self::$updateModuls[$modul]['files'] as $fileId => $file) {
			if (empty($version)  || rex_string::versionCompare($fileId, $version, '<=')) {
				unset(self::$updateModuls[$modul]['files'][$fileId]);
			}
		}
		if (empty(self::$updateModuls[$modul]['files'])) {
			unset(self::$updateModuls[$modul]);
		}
	}

	public static function getAddModuls()
	{
		if (is_array(self::$addModuls)) {
			return self::$addModuls;
		}

		self::$addModuls = self::getModuls('index.php');
		return self::$addModuls;
	}

	public static function addedPackage($package)
	{
		self::$myModuls = null;
	}

	public static function getMyModuls()
	{
		if (is_array(self::$myModuls)) {
			return self::$myModuls;
		}

		self::$myModuls = self::getModuls('index.php');
		foreach (self::$myModuls as $key => $modul) {
//			if (!::exists($key)) {
//				unset(self::$myModuls[$key]);
//			}
		}
		return self::$myModuls;
	}

	public static function getPath($path = '')
	{
		return '/' . $path;
	}

	private static function getModuls($path = 'index.php')
	{
		return rex_install_webservices::getJson($path);

	}

	public static function deleteCache()
	{
		self::$updateModuls = null;
		self::$addModuls = null;
		self::$myModuls = null;
		rex_install_webservices::deleteCache('packages/');
	}
}
