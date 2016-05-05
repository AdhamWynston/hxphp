<?php

namespace HXPHP\System\Helpers\Menu;

use HXPHP\System\Storage as Storage;
use HXPHP\System\Tools as Tools;

class Menu
{
	private $render = null;

	/**
	 * Dados do módulo de configuração
	 * @var array
	 */
	private $configs = array();

	/**
	 * URL ATUAL
	 * @var string
	 */
	private $current_URL = null;

	private $role;

	/**
	 * Conteúdo HTML do menu renderizado
	 * @var string
	 */
	private $html;


	/**
	 * @param \HXPHP\System\Http\Request   $request Objeto Request
	 * @param \HXPHP\System\Configs\Config $configs Configurações do framework
	 * @param string                       $role    Nível de acesso
	 */
	public function __construct(
		\HXPHP\System\Http\Request $request,
		\HXPHP\System\Configs\Config $configs,
		$role = 'default'
	)
	{

		$this->role = $role;

		$this->setConfigs($configs)
				->setCurrentURL($request, $configs);

		$realLink = new RealLink($configs->site->url, $configs->baseURI);
		$checkActive = new CheckActive($realLink, $this->current_URL);

		$this->render = new Render(
			$realLink,
			$checkActive,
			$this->configs->menu->itens,
			$this->configs->menu->configs
		);
	}

	/**
	 * Dados do módulo de configuração do MenuHelper
	 * @param array $configs
	 */
	private function setConfigs($configs)
	{
		$this->configs = $configs;

		return $this;
	}

	/**
	 * Define a URL atual
	 */
	private function setCurrentURL($request, $configs)
	{
		$parseURL = parse_url($request->server('REQUEST_URI'));

		$this->current_URL = $configs->site->url . $parseURL['path'];

		return $this;
	}
	

	/**
	 * Exibe o HTML com o menu renderizado
	 * @return string
	 */
	public function getMenu()
	{
		$this->render->getHTML($this->role);

		return $this->html;
	}

	public function __toString()
	{
		return $this->getMenu();
	}
}