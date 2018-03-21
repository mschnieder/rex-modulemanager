<?php
/**
 * @package redaxo\modulemanager
 *
 * @internal
 */
class rex_module
{


	public static function exists($modulname) {
		$sql = rex_sql::factory();
		$q = $sql->prepareQuery("Select count(*) as anzahl from ".rex::getTablePrefix()."module where name= :name");
		$sql->execute(['name' => $modulname]);

		if($sql->getValue('anzahl') == '0') return false;

		return true;
	}


	public static function get($modulename = null) {
		if($modulename == null) {
			$sql = rex_sql::factory()->setQuery("Select * from ".rex::getTablePrefix()."module order by name");
		} else {
			$sql = rex_sql::factory()->setQuery("Select * from ".rex::getTablePrefix()."module where name='".rex_escape($modulename)."'");
		}

		$d = [];
		foreach($sql as $r) {
			$d[$r->getValue('name')] = ['updateuser' => $r->getValue('updateuser'),
										'revision' => $r->getValue('revision')];
		}
		return $d;
	}


	public static function install($key, $fileversion) {
		try {
			$moduls = rex_install_moduls::getAddModuls();
			$input  = $moduls[$key]['files'][$fileversion]['input'];
			$output = $moduls[$key]['files'][$fileversion]['output'];
			$name = $key;

			$sql = rex_sql::factory();

			$q = $sql->prepareQuery("Select id from ".rex::getTablePrefix()."module where name = :name");
			$q->execute(['name' => $key]);

			if($q->rowCount() > 0) {
				$id = $sql->getValue('id');
				$q = $sql->prepareQuery("Update ".rex::getTablePrefix()."module set output= :output, input = :input, updateuser = :updateuser, updatedate = :updatedate, revision = :revision where id = :id");
				$q->execute(['output' 		=> $output,
							 'input'		=> $input,
							 'updateuser' 	=> 'modulemanager',
							 'updatedate' 	=> date('Y-m-d H:i:s', time()),
							 'revision'		=> $fileversion,
							 'id'			=> $id]);
			} else {
				$q = $sql->prepareQuery("Insert into ".rex::getTablePrefix()."module (`name`, `output`, `input`, `createuser`, `updateuser`, `createdate`, `updatedate`, `attributes`, `revision`) values 
																				(:modulename, :moduloutput, :modulinput, :createuser, :updateuser, :createdate, :updatedate, :attributes, :revision)");


				$q->execute(['modulename'	=> $name,
							 'moduloutput' 	=> $output,
							 'modulinput' 	=> $input,
							 'createuser'	=> 'modulemanager',
							 'updateuser'	=> 'modulemanager',
							 'createdate'	=> date('Y-m-d H:i:s', time()),
							 'updatedate'   => date('Y-m-d H:i:s', time()),
							 'attributes'	=> '',
							 'revision'		=> 1,
							 ]);
			}


		} catch (rex_functional_exception $e) {
			echo rex_view::warning($e->getMessage());
			$modulkey = '';
		}
	}

}