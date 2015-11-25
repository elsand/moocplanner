<?php

class tpl {

	private $template_path;
	private $template_file;
	private $template_vars = [];

	public function __construct($template_file, $template_path = null) {
		if (!$template_path) {
			$template_path = realpath(__DIR__ . '/../templates/');
		}
		$this->template_path = $template_path;

		$template_file = $template_path . '/' . $template_file . '.php';
		if (!is_readable($template_file)) {
			throw new RuntimeException('Unable to read template file: ' . $template_file);
		}

		$this->template_file = $template_file;
	}

	public function set($field, $val) {
		$this->template_vars[$field] = $val;
	}

	public function render() {
		foreach ($this->template_vars as $field => $val) {
			$$field = $val;
		}
		ob_start();
		/** @noinspection PhpIncludeInspection */
		require $this->template_file;
		return ob_get_clean();
	}
}