# ModuleManager

Mit diesem Redaxo Addon können die Redaxo Module über eine Zentrale Stelle in verschiedenen Versionen verwaltet werden.


Die "Zentrale" ist die nachfolgende index.php die unter der angegebenen URL in der Config erreichbar sein muss.
Es gibt auch noch 2 Datenbank Tabellen zur Verwaltung der einzelnen Versionen von Modulen.

Die index.php muss über eine https Verbindung erreichbar sein!


```
<?php

$hostname = 'localhost';
$username = 'root';
$passwort = '';
$database = 'modulemanager';
$prefix	  = 'rex_';


$sql = new mysqli($hostname, $username, $passwort, $database);

if($sql->connect_errno) {
	echo "Sorry, database down!";
	exit;
}


$erg = $sql->query("Select * from module order by name");

$d = [];
while($r = $erg->fetch_assoc()) {
	$erg2 = $sql->query("Select * from module_content where modulid=".$r['id']);

	$a = new stdClass();
	$a->name = utf8_encode($r['name']);
	$a->author = utf8_encode($r['author']);
	$a->shortdescription = utf8_encode($r['shortdescription']);
	$a->description = utf8_encode($r['description']);
	$a->created = utf8_encode($r['created']);
	$a->updated = utf8_encode($r['updated']);

	while($r2 = $erg2->fetch_assoc()) {
		$b = new stdClass();
		$b->version = utf8_encode($r2['version']);
		$b->description = utf8_encode($r2['description']);
		$b->created = utf8_encode($r2['created']);
		$b->updated = utf8_encode($r2['updated']);
		$b->input = utf8_encode($r2['input']);
		$b->output = utf8_encode($r2['output']);

		$a->files[$r2['revid']] = $b;
	}

	$d[utf8_encode($r['name'])] = $a;
}
echo json_encode($d);

?>
```


Erstellung der benötigten Tabellen

```

CREATE TABLE IF NOT EXISTS `module` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `author` varchar(35) DEFAULT NULL,
  `shortdescription` varchar(255) DEFAULT NULL,
  `description` text,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `module_content` (
  `revid` int(11) NOT NULL AUTO_INCREMENT,
  `modulid` int(11) NOT NULL DEFAULT '0',
  `version` char(10) NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `input` longtext NOT NULL,
  `output` longtext NOT NULL,
  PRIMARY KEY (`revid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


```


Beispiel Import von Modulen
```
INSERT INTO `module` (`id`, `name`, `author`, `shortdescription`, `description`, `created`, `updated`) VALUES
	(1, 'Testmodul 123', 'Markus Schnieder', 'Das Ultimative geile erste Modul!', 'Hier kommt die mega lange beschreibung rein, damit man weiß was dass ding so kann...', '2018-03-14 13:29:07', '2018-03-14 13:29:07'),
	(2, 'Headlines', NULL, NULL, NULL, NULL, NULL);
	
	

INSERT INTO `module_content` (`revid`, `modulid`, `version`, `description`, `created`, `updated`, `input`, `output`) VALUES
	(1, 1, '1.0.1', 'Das hat sich so alles geändert...', '2018-03-14 13:30:55', '2018-03-14 13:30:56', 'Test 123', 'HAHHA'),
	(2, 1, '1.0.2', 'Das ist neu', '2018-03-15 10:18:56', '2018-03-15 10:18:57', 'asdf', 'blub');
```