<?php

class AlertHelper
{
	
	/**
	 * Injeção do controle de sessão
	 * @var object
	 */
	private $storage;

	/**
	 * Parâmetro alterado mediante o tipo da mensagem: string|array
	 * @var boolean
	 */
	private $list_messages = false;

	/**
	 * Método construtor
	 */
	public function __construct()
	{
		//Instância dos objetos injetados
		$this->storage = new Session;
		return $this;
	}

	/**
	 * Método resposnável pela obtenção do conteúdo do template
	 * @return html
	 */
	private function getTemplate()
	{
		$file = $this->list_messages ? '-list' : '';
		$template = dirname(__FILE__) . DS . 'templates' . DS . 'Alert' . DS . 'alert' . $file . '.html';

		if ( ! file_exists($template)) {
			throw new \Exception("O template para a mensagem nao foi localizado: $template", 1);
		}

		return file_get_contents($template);
	}

	/**
	 * Define um alerta
	 * @param array $alert ['Classe CSS', 'Título do alerta', 'Mensagem do alerta']
	 */
	public function setAlert(array $alert)
	{
		if (count($alert) == 1)
			return null;

		$alert[2] = ! isset($alert[2]) ? '' : $alert[2];

		list($style, $title, $message) = $alert;

		/**
		 * Rederiza a mensagem
		 * @var string
		 */
		$render = $this->render($message);

		/**
		 * Recupera o template html ara a mensagem
		 * @var html
		 */
		$template = $this->getTemplate();

		/**
		 * Aplica a mensagem no template
		 * @var html
		 */
		$content = sprintf($template, $style, $title, $render);

		$this->storage->set('message', $content);
	}

	/**
	 * Renderiza as mensagens
	 * @param  array|string $messages Mensagem pode ser um array com a seguinte estrutura ['Erro' => 'Justicativa' , 'Erro 2' => 'Justificativa 2'] ou uma string
	 * @return mixed
	 */
	private function render($messages)
	{
		if ( ! is_array($messages)) {
			return $messages;
		}

		$this->list_messages = true;

		$html = '';

		foreach ($messages as $key => $message) {
			$html .= '<li>' . $message . '</li>';
		}

		return $html;
	}

	/**
	 * Retorna os alertas da aplicação
	 * @return html
	 */
	public function show()
	{
		$message = $this->storage->get('message');
		$this->storage->clear('message');

		return $message; 
	}
}