<?php
date_default_timezone_set('Asia/Shanghai');

Flight::set('dbconfig', [
	// required
	'database_type' => 'mysql',
	'database_name' => 'spytalk',
	'server' => 'localhost',
	'username' => 'username',
	'password' => 'password',
	'charset' => 'utf8',
 
	// optional
	'port' => 3306,
	// driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
	'option' => [
		PDO::ATTR_CASE => PDO::CASE_NATURAL
	]
]);

Flight::set('reCaptchaSecretkey', 'reCaptchaSecretkey');
Flight::set('reCaptchaSitekey', 'reCaptchaSitekey');
?>