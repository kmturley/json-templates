<?php

require_once 'Twig/Autoloader.php';
require_once 'Twig/ExtensionInterface.php';
require_once 'Twig/Extension.php';
require_once 'Twig_JSON.php';

class Main {

    public function init() {
		Twig_Autoloader::register();
        $this->twig = new Twig_Environment(new Twig_Loader_Filesystem('../html'));
        $this->twig->addExtension(new Twig_JSON());
    }

	public function render() {
        $self = explode('/', $_SERVER['PHP_SELF']);
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        $sections = array_splice($uri, count($self)-2, 1);
        if ($sections[0]) {
            return $this->twig->render($sections[0].'.html');
        } else {
            return 'Try a page url e.g. <a href="./home">home</a>';
        }
	}
}

$main = new Main();
$main->init();
echo $main->render();
?>