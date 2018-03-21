<?php

/**
 * @package redaxo\modulemanager
 *
 * @internal
 */
abstract class rex_api_install_module_download extends rex_api_function
{
	protected $modulkey;
	protected $fileId;
	protected $file;
	protected $archive;

	public function execute()
	{
		if (!rex::getUser()->isAdmin()) {
			throw new rex_api_exception('You do not have the permission!');
		}
		$this->modulkey = rex_request('modulkey', 'string');
		$modules = rex_module::get();
		$this->fileId = rex_request('file', 'int');
		if (isset($modules[$this->modulkey]['updateuser']) && $modules[$this->modulkey]['updateuser'] == 'modulemanager') {
			print_r($modules);
//			throw new rex_api_exception('The requested module version can not be loaded, maybe your module is manual changed.');
		}

		rex_module::install($this->modulkey);

//		return new rex_api_result($success, $message);
	}
}
