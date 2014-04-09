<?php

class Twig_JSON extends Twig_Extension {

    public function getName() {
        return 'Twig_JSON';
    }

    public function getFunctions() {
        return array(
            'loadJSON'  => new Twig_Function_Method($this, 'loadJSON'),
        );
    }
    
    public function loadJSON($url) {
        return json_decode(file_get_contents($url), true);
    }
}

?>