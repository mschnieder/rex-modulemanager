<?php

/**
 * @package redaxo\modulemanager
 *
 * @internal
 */
class rex_api_install_module_add extends rex_api_install_module_download
{
	const GET_MODULE_FUNCTION = 'getAddModule';
	const VERB = 'downloaded';
	const SHOW_LINK = true;

	protected function checkPreConditions()
	{
		if (rex_module::exists($this->modulkey)) {
			throw new rex_api_exception(sprintf('Module "%s" already exist!', $this->modulkey));
		}
	}

	protected function doAction()
	{
		if (($msg = $this->extractArchiveTo(rex_path::addon($this->modulkey))) !== true) {
			return $msg;
		}
		rex_package_manager::synchronizeWithFileSystem();
		rex_install_packages::addedPackage($this->modulkey);
	}
}
