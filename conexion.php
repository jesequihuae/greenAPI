<?php 

	use Illuminate\Database\Capsule\Manager as Capsule;
	$capsule = new Capsule;

	$capsule->addConnection([
		'driver' => 'mysql',
		'host' => 'localhost',
		'database' => 'ghc_bd_beta',
		'username' => 'root',
		'password' => '',
		'charset' => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix' => ''
	]);	

	$capsule->bootEloquent();
	$capsule->setAsGlobal();

?>