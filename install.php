<?php
/**
 * Created by PhpStorm.
 * User: Markus
 * Date: 08.03.18
 * Time: 15:47
 */
$dataDir = $this->getPath('data');
if(is_dir($dataDir) && !is_dir(rex_path::addonData('modulemanager'))) {
	if(!rex_dir::copy($dataDir,rex_path::addonData('modulemanager'))) {
		throw new rex_functional_exception($this->i18n('install_cant_copy_files'));
	}
}