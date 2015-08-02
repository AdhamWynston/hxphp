<?php 

class Error404Controller extends \HXPHP\System\Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load('Auth');
	}
	public function indexAction()
	{
		$this->view->setTitle('Oops! Nada encontrado!');
		$this->render('404');
	}
}