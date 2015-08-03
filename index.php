<?php
	ob_start();

	ini_set('display_errors', 1); 
	date_default_timezone_set('America/Sao_Paulo');

	//Inclusão dos arquivos de configuração
	require_once("src/HXPHP/System/Bootstrap/config.php");

	/**
	 * Verifica se o autoload do Composer está configurado
	 */
	$composer_autoload = 'vendor' . DS . 'autoload.php';

	if ( ! file_exists($composer_autoload)) {
		die('Execute o comando: composer install');
	}

	require_once($composer_autoload);

	//Start da sessão
	HXPHP\System\Services\StartSession::sec_session_start();

	//Configurações
	$configs = new HXPHP\System\Configs\Config;

	$configs->env->add('production');
	$configs->env->production->database->setConnectionData(array(
		'host' => 'localhost',
		'user' => 'root',
		'password' => '',
		'dbname' => 'teste'
	));

	$configs->define->setDefaultEnv('production');

	var_dump($configs->database);

	$app = new HXPHP\System\App($configs);
	$app->ActiveRecord();
	$app->run();