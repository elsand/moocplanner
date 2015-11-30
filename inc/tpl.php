<?php

/**
 * Simple templating engine
 */
class tpl {

	private $template_path;
	private $template_file;
	private $layout_file;
	private $template_vars = [];

	/**
	 * @param string $template_file The name of the template to render, no file extension or path
	 * @param null|string $layout_file The path to the layoutfile in which the template is placed, default 'layout.php' in $template_path. If null, no layout is rendered if ajax-request
	 * @param null|string $template_path The path to the templates, default '/templates'
	 *
	 * @throws RuntimeException
	 */
	public function __construct($template_file, $layout_file = null, $template_path = null) {
		if (!$template_path) {
			$template_path = realpath(__DIR__ . '/../templates/');
		}
		$this->template_path = $template_path;

		$template_file = $template_path . '/' . $template_file . '.php';
		if (!is_readable($template_file)) {
			throw new RuntimeException('Unable to read template file: ' . $template_file);
		}
		$this->template_file = $template_file;

		if ($layout_file == null && !is_ajax()) {
			$layout_file = $template_path . '/layout.php';
		}
		if ($layout_file) {
			if (!is_readable($layout_file)) {
				throw new RuntimeException('Unable to read layout file: ' . $layout_file);
			}
			$this->layout_file = $layout_file;
		}

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
		$rendered = ob_get_clean();

		if ($this->layout_file) {
			$layout = file_get_contents($this->layout_file);
			return str_replace('###CONTENT###', $rendered, $layout);
		}
		return $rendered;
	}
}