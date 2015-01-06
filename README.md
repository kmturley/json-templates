json-templates
==============

Example project showing how to load json into templates using a filter

    PHP `http://www.php.org/`
    Twig template engine `http://twig.sensiolabs.org/`

## Installation

First off you need to install PHP on your server. Many will come with it automatically installed, if not head over to http://www.php.org to download and install php locally. Once installed you should be able to view a php page on your server without errors e.g.

    <?php
        phpinfo();
    ?>

Next we need to create a site folder and install TWIG template library. This will allow us to use a nice format to write templates, and keep a clean separation between controller code in php and views code in TWIG syntax. Download TWIG from http://twig.sensiolabs.org/ and put the folder at:

    /php/Twig/

## Development

You will need to use the custom filter to load JSON files inside the templates. Add the file at /php/Twig_JSON.php which contains the json loading functions:

    <?php
    class Twig_JSON extends Twig_Extension {
    
      public function getName() {
          return 'Twig_JSON';
      }
    
      public function getFunctions() {
          return array(
              'json'  => new Twig_Function_Method($this, 'json'),
          );
      }
      
      public function json($url) {
          return json_decode(file_get_contents($url), true);
      }
    }
    ?>

Also ensure you have a /php/Main.php file which will load the TWIG template library, the custom TWIG filter, and map browser urls to specific template files:

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

I’ve also added an optional index.php in the root to direct the request to the default template: home. This is because root url doesn’t have a section, so I don’t have a template which will be loaded. By redirecting the root to point to home, i’m telling it to render the home template:

    <?php
        header("Location: home");
        exit;
    ?>

As part of my configuration i’ve also decided to add an .htaccess file at the root of the site to ensure all directory and folder requests hit our /php/Main.php file instead of real folders. This is optional depending on your setup but I think works well for mapping urls to templates:

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule . php/Main.php [L]

Now you can edit the data! Find the file at /json/navigation.json and update the data:

    [
        {
            "title": "Home",
            "url": "./home"
        },
        {
            "title": "Videos",
            "url": "./videos"
        }
    ]

We can now load the json file using a TWIG filter and output it into the template. The example at /templates/home.html shows how this can now work using the custom filter:

    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Json templates</title>
    </head>
    <body>
        <h2>navigation</h2>
        <ul>
        {% set items = json('../json/navigation.json') %}
        {% for item in items %}
            <li><a href="{{ item.url }}">{{ item.title }}</a></li>
        {% endfor %}
        </ul>
    </body>
    </html>

The json() filter allows you to pass through the url to the json file, which loads and returns it back to the template. This can then be counted/looped through and outputted. A very easy way to keep separation between view templates and your data sets.
