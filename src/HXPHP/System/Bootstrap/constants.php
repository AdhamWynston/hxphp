<?php
	
	/**
	 * Define a BASE da URL para ambiente de desenvolvimento e produção, respectivamente
	 */
	if ($_SERVER['SERVER_NAME'] === 'localhost'){
		define('BASE','/hxphp/');
	}
	else{
		define('BASE','/');
	}

	/**
	 * Constantes de diretórios
	 */
	define('DS', DIRECTORY_SEPARATOR);
	define('ROOT_PATH', str_replace('/', DS, BASE));
	define('APP_PATH', 'app' . DS);
	define('PUBLIC_PATH', ROOT_PATH . 'public');

	//Constantes que definem os diretórios secundários do HXPHP
	$secondary_directories = array(
		'ASSETS' =>  PUBLIC_PATH . DS.'assets' . DS,
		'IMG' =>  PUBLIC_PATH . DS . 'img' . DS,
		'CSS' =>  PUBLIC_PATH . DS . 'css' . DS,
		'JS' =>  PUBLIC_PATH . DS . 'js' . DS
	);

	//Constante que define o título prefixado da aplicação
	$title_config = array(
		'TITLE' => 'HXPHP Framework - O seu primeiro framework'
	);

	//Declaração das constantes
	$constants = array_merge($secondary_directories, $title_config);
	foreach($constants as $key => $value)
		define($key,$value);
