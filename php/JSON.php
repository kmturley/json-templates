<?php

class JSON {

    public function init() {
        require_once 'php/Twig/Autoloader.php';
		Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('html');
        $this->twig = new Twig_Environment($loader);
    }

	public function render() {
        $sections = $this->getSections();
        if ($sections[0]) {
            $config = $this->loadJSON('json/'.$sections[0].'.json');
            $model = array();
            foreach ($config['model'] as $key => $value) {
                $model[$key] = $this->loadJSON('json/'.$value);
            }
            return $this->twig->render($config['view'], array('model' => $model));
        } else {
            return 'Try a page url e.g. <a href="./home">home</a>';
        }
	}

    public function loadJSON($url) {
        return json_decode(file_get_contents($url), true);
    }
    
    public function getSections() {
        $self = explode('/', $_SERVER['PHP_SELF']);
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        return array_splice($uri, count($self)-1, 1);
    }
}
?>