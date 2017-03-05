<?php
// this class takes the url, extracts the controller, method and parameters and launches it.
class App
{
	// define and set the default controller, method and parameters
	protected $controller = 'home';
	protected $method = 'index';
	protected $params = [];

	public function __construct()
	{
		$url = $this->parseUrl();

		// launch the controller
		if(file_exists('../app/controllers/' . $url[0] . '.php'))
		{
			$this->controller = $url[0];
			unset($url[0]);
		}
		require_once '../app/controllers/' . $this->controller . '.php';
		$this->controller = new $this->controller;

		// feed the method to the controller
		if(isset($url[1]))
		{
			if(method_exists($this->controller, $url[1]))
			{
				$this->method = $url[1];
				unset($url[1]);
			}
		}

		// feed the parameters to the controller
		if($url)
		{
			$this->params = array_values($url);
		}
		call_user_func_array([$this->controller, $this->method], $this->params);
	}

	public function parseUrl()
	{
		// this extracts the data (controller/method/parameters) from the url
		if(isset($_GET['url']))
		{
			return $url = explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
		}
	}
}
