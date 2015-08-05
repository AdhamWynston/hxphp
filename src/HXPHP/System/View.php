<?php

namespace HXPHP\System;

class View
{
	/**
	 * Título da página
	 * @var string
	 */
	public $title;

	/**
	 * Injeção das Configurações
	 * @var object
	 */
	private $configs;

	/**
	 * Parâmetros de configuração da VIEW
	 * @var string
	 */
	protected $path;
	protected $template;
	protected $header;
	protected $file;
	protected $footer;
	protected $vars = array();
	protected $assets = array();

	public function __construct(Configs\Config $configs, $controller, $action)
	{
		/**
		 * Injeção das Configurações
		 * @var object
		 */
		$this->configs = $configs;

		/**
		 * Tratamento das variáveis
		 */
		$controller = strtolower(str_replace('Controller', '', $controller));
		$action = ($controller == $configs->controllers->notFound
					 ? 'indexAction' : $action);
		$action = str_replace('Action', '', $action);

		/**
		 * Definindo estrutura padrão
		 */
		$this->setPath($controller);
		$this->setTemplate(true);
		$this->setHeader('Header');
		$this->setFile($action);
		$this->setFooter('Footer');

		/**
		 * Definindo dados 
		 */
		$this->setTitle('HXPHP Framework');
	}

	/**
	 * Define o título da página
	 * @param string  $title  Título da página
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Define a pasta da view
	 * @param string  $path  Caminho da View
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * Define se o arquivo é miolo (Inclusão de Cabeçalho e Rodapé) ou único
	 * @param bool  $template  Template ON/OFF
	 */
	public function setTemplate($template)
	{
		$this->template = $template;
		return $this;
	}


	/**
	 * Define o cabeçalho da view
	 * @param string  $header  Cabeçalho da View
	 */
	public function setHeader($header)
	{
		$this->header = $header;
		return $this;
	}

	/**
	 * Define o arquivo da view
	 * @param string  $file  Arquivo da View
	 */
	public function setFile($file)
	{
		$this->file = $file;
		return $this;
	}

	/**
	 * Define o rodapé da view
	 * @param string  $footer  Rodapé da View
	 */
	public function setFooter($footer)
	{
		$this->footer = $footer;
		return $this;
	}

	/**
	 * Define um conjunto de variáveis para a VIEW
	 * @param array  $vars  Array com variáveis
	 */
	public function setVars(array $vars)
	{
		$this->vars = array_push($this->vars, $vars);
		return $this;
	}

	/**
	 * Define uma variável única para a VIEW
	 * @param string  $name  Nome do índice
	 * @param string  $value  Valor
	 */
	public function setVar($name, $value)
	{
		$this->vars[$name] = $value;
		return $this;
	}

	/**
	 * Define os arquivos customizáveis que serão utilizados
	 * @param string  $type  Tipo do arquivo
	 * @param string|array  $assets  Arquivo Único | Array com os arquivos
	 */
	public function setAssets($type, $assets)
	{
		if (is_array($assets)) {
			$this->assets[$type] = array_merge($this->assets[$type], $assets);
		} 
		else {
			$this->assets[$type] = array_push($this->assets[$type], $assets);
		}
		 
		return $this;
	}

	/**
	 * Inclui os arquivos customizados
	 * @param  string $type          Tipo de arquivo incluso, como: css ou js
	 * @param  array  $custom_assets Links dos arquivos que serão incluídos
	 * @return string                HTML formatado de acordo com o tipo de arquivo
	 */
	public function assets($type, array $custom_assets = array())
	{
		$add_assets = '';

		switch ($type) {
			case 'css':
				$tag = '<link type="text/css" rel="stylesheet" href="%s">'."\n\r";
				break;

			case 'js':
				$tag = '<script type="text/javascript" src="%s"></script>'."\n\r";
				break;
		}
		
		if (count($custom_assets) > 0)
			foreach ($custom_assets as $file)
				$add_assets .= sprintf($tag,$file);

		return $add_assets;
	}
}