<?php

namespace HXPHP\System;

use HXPHP\System\Http as Http;

class App
{
	/**
	 * Injeção das configurações
	 * @var object
	 */
	public $configs;

	/**
	 * Injeção do Request
	 * @var object
	 */
	public $request;

	/**
	 * Injeção do Response
	 * @var object
	 */
	public $response;
	
	/**
	 * Método Construtor
	 */
	public function __construct(Configs\Config $configs)
	{
		$this->configs  = $configs;
		$this->request  = new Http\Request($configs->baseURI);
		$this->response = new Http\Response;
		
		return $this;
	}

	/**
	 * Configuração do ORM
	 */
	public function ActiveRecord()
	{
		$cfg = \ActiveRecord\Config::instance();
		$cfg->set_model_directory($this->configs->models->directory);
		$cfg->set_connections(
			array(
				'development' => 'mysql://'.$this->configs->database->user
									.':'.$this->configs->database->password
									.'@'.$this->configs->database->host
									.'/'.$this->configs->database->dbname
			)
		);
	}
	
	/**
	 * Executa a aplicação
	 */
	public function run()
	{

		/**
		 * Variáveis
		 */
		$controller = $this->request->controller;
		$action = $this->request->action;
		$controllersDir = $this->configs->controllers->directory;
		$notFoundController = $this->configs->controllers->notFound;

		/**
		 * Caminho do controller
		 * @var string
		 */
		$controllerFile = $controllersDir . $controller . '.php';

		if ( ! file_exists($controllerFile))
			$controllerFile = $controllersDir . $notFoundController . '.php';
		
		//Inclusão do Controller
		require_once($controllerFile);

		//Verifica se a classe correspondente ao Controller existe
		if ( ! class_exists($controller)) {
			$controller = $notFoundController;
		}

		//Instância do Controller
		$app = new $controller();
		
		//Verifica se a Action requisitada não existe
		if ( ! method_exists($app, $action))
			$action = 'indexAction';

		/**
		 * Injeta as dependências no Controller
		 */
		$app->start($this->configs);

		/**
		 * Adiciona a View ao Controller
		 */
		$app->setView(new View( $this->configs,
							    $controller,
							    $action ));


		/**
		 * Atribuição de parâmetros
		 */
		call_user_func_array(array(&$app, $action), $this->request->params);

		/**
		 * Renderização da VIEW
		 */
		$app->view->flush();

	}
}