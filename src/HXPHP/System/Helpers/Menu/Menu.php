<?php

namespace HXPHP\System\Helpers\Menu;

use HXPHP\System\Storage as Storage;
use HXPHP\System\Tools as Tools;

class Menu
{
	private $realLink = null;
	private $checkActive = null;

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

		$this->realLink = new RealLink($configs->site->url, $configs->baseURI);
		$this->checkActive = new CheckActive($this->realLink, $this->current_URL);
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
	 * Renderiza o menu em HTML
	 */
	private function render($role = 'default')
	{
		$menus = $this->configs->menu->itens[$role];
		$menu_configs = $this->configs->menu->configs;

		if (empty($menus) || !is_array($menus))
			return false;

		$itens = '';

		$i = 0;

		foreach ($menus as $key => $value) {
			$i++;
			$menu_data = MenuData::get($key);
			$real_link = $this->realLink->get($value);

			// Dropdown
			if (is_array($value) && !empty($value)) { 
				$dropdown_itens = '';

				foreach ($value as $dropdown_key => $dropdown_value) {
					$submenu_data = MenuData::get($dropdown_key);
					$submenu_real_link = $this->realLink->get($dropdown_value);

					$submenu_link_active = $this->checkActive->link($submenu_real_link) === true ? $menu_configs['link_active_class'] : '';

					$link = Elements::get('link', array(
						$submenu_real_link,
						$menu_configs['link_class'],
						$submenu_link_active,
						$submenu_data->title,
						$submenu_data->icon,
						$menu_configs['link_before'],
						$submenu_data->title,
						$menu_configs['link_after']
					));

					$submenu_active = $this->checkActive->link($submenu_real_link) === true ? $menu_configs['dropdown_item_active_class'] : '';

					$dropdown_itens.= Elements::get('dropdown_item', array(
						$menu_configs['dropdown_item_class'],
						$submenu_active,
						$link
					));
				}

				$dropdown = Elements::get('dropdown', array(
					$i,
					$menu_configs['dropdown_class'],
					$dropdown_itens
				));

				$attrs = Attrs::render($menu_configs['link_dropdown_attrs']);
				$active = $this->checkActive->dropdown($value) === true ? $menu_configs['link_active_class'] : '';

				$link = Elements::get('link_with_dropdown', array(
					$i,
					$menu_configs['link_dropdown_class'],
					$active,
					$attrs,
					$menu_data->title,
					$menu_data->icon,
					$menu_configs['link_before'],
					$menu_data->title,
					$menu_configs['link_after'],
					$dropdown
				));

				$active = $this->checkActive->dropdown($value) === true ? $menu_configs['menu_item_active_class'] : '';

				$itens.= Elements::get('menu_item', array(
					$menu_configs['menu_item_dropdown_class'],
					$active,
					$link
				));		
			}
			else {
				$link_active = $this->checkActive->link($real_link) === true ? $menu_configs['link_active_class'] : '';

				$link = Elements::get('link', array(
					$real_link,
					$menu_configs['link_class'],
					$link_active,
					$menu_data->title,
					$menu_data->icon,
					$menu_configs['link_before'],
					$menu_data->title,
					$menu_configs['link_after']
				));

				$active = $this->checkActive->link($real_link) === true ? $menu_configs['menu_item_active_class'] : '';

				$itens.= Elements::get('menu_item', array(
					$menu_configs['menu_item_class'],
					$active,
					$link
				));
			}	
		}

		$menu = Elements::get('menu', array(
			$menu_configs['menu_class'],
			$menu_configs['menu_id'],
			$itens
		));

		if ($menu_configs['container'] !== false) {
			$this->html = Elements::get('container', array(
				$menu_configs['container'],
				$menu_configs['container_id'],
				$menu_configs['container_class'],
				$menu,
				$menu_configs['container']
			));
		}
		else {
			$this->html = $menu;
		}

		return $this;
	}

	/**
	 * Exibe o HTML com o menu renderizado
	 * @return string
	 */
	public function getMenu()
	{
		$this->render($this->role);

		return $this->html;
	}

	public function __toString()
	{
		return $this->getMenu();
	}
}