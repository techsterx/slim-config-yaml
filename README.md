# Slim Config - YAML

[![Build Status](https://travis-ci.org/techsterx/slim-config-yaml.svg?branch=master)](https://travis-ci.org/techsterx/slim-config-yaml)
[![Total Downloads](https://poser.pugx.org/techsterx/slim-config-yaml/d/total.svg)](https://packagist.org/packages/techsterx/slim-config-yaml)
[![Latest Stable Version](https://poser.pugx.org/techsterx/slim-config-yaml/v/stable.svg)](https://packagist.org/packages/techsterx/slim-config-yaml)
[![License](https://poser.pugx.org/techsterx/slim-config-yaml/license.svg)](https://raw.githubusercontent.com/techsterx/slim-config-yaml/master/LICENSE)

Parses YAML files and adds them into Slim's config singleton.
Uses Symfony's YAML Component to parse files (http://github.com/symfony/Yaml).
Allows other YAML files to be imported and parameters to be set and used.

## Getting Started

### Installation

#### Composer

Install composer in your project.
```
curl -s https://getcomposer.org/installer | php
```
Create a ```composer.json``` file in your project root:
```
{
	"require": {
		"techsterx/slim-config-yaml": "1.*"
	}
}
```
Install via composer:
```
php composer.phar install
```
Add this line to your application's ```index.php``` file:
```php
<?php
require 'vendor/autoload.php';
```

#### Manual Install

Download and extract src/ directory into your project directory and ```require``` it in your
application's ```index.php``` file.
```php
<?php
require 'Slim\Slim.php';
require 'Yaml.php';

$app = new \Slim\Slim();

\BurningDiode\Slim\Config\Yaml::getInstance()->addFile('/path/to/some/file');
```

### Methods

*Slim Config - YAML* uses a static method to get the currenct instance.
If an instance doesn't exist, a new one will be created.
Use the ```getInstance()``` method to get the current instance.

```php
$slimYaml = \BurningDiode\Slim\Config\Yaml::getInstance();
```

```_()``` is the shorthand equivalent of ```getInstance()```.

```php
$slimYaml = \BurningDiode\Slim\Config\Yaml::_();
```

To add a single file, use the ```addFile()``` method.
```php
\BurningDiode\Slim\Config\Yaml::getInstance()->addFile('/path/to/some/file.yaml');
```

You can also chain multiple ```addFile()``` methods togethor.
```php
\BurningDiode\Slim\Config\Yaml::getInstance()
    ->addFile('/path/to/some/file.yaml')
    ->addFile('/path/to/another/file.yaml');
```

You can import a whole directory of YAML files.
```php
\BurningDiode\Slim\Config\Yaml::getInstance()->addDirectory('/path/to/directory');
```

You can chain with the ```addDirectory()``` method as well.
```php
\BurningDiode\Slim\Config\Yaml::getInstance()
    ->addDirectory('/path/to/directory')
    ->addFile('/path/to/some/file.yaml');
```

Specify some global parameters to be used by all YAML files processed.
```php
\BurningDiode\Slim\Config\Yaml::_()
    ->addParameters(array('app.root' => dirname(__FILE__)))
    ->addDirectory('/path/to/config/directory')
    ->addFile('/path/to/file/outside/of/config/directory.yml');
```

### Using Parameters

You can specify parameters in YAML files that will be replaced using keywords. Parameters are only available to the resource currently being processed.

config.yaml
```yaml
parameters:
    key1: value1
    key2: value2
    
application:
    keya: %key1%
    keyb: %key2%
```

app.php
```php
\BurningDiode\Slim\Config\Yaml::_()->addFile('config.yml');

$config = $app->config('application');

print_r($config);
```

Output:
```
Array
(
    [key1] => value1
    [key2] => value2
)
```

### Importing Files

You can import other YAML files which can be useful to keep all your common parameters in one file and used in others.

parameters.yml
```yaml
parameters:
    db_host:  localhost
    db_user:  username
    db_pass:  password
    db_dbase: database
```

database.yml
```yaml
imports:
    - { resource: parameters.yml }
    
database:
    hostname: %db_host%
    username: %db_user%
    password: %db_pass%
    database: %db_dbase%
```

app.php
```php
\BurningDiode\Slim\Config\Yaml::_()->addFile('database.yml');

$db_config = $app->config('database');

print_r($db_config);
```

Output:
```
Array
(
    [hostname] => localhost
    [username] => username
    [password] => password
    [database] => database
)
```
    
## License

Slim Config - YAML is released under the [MIT public license] (https://raw.githubusercontent.com/techsterx/slim-config-yaml/master/LICENSE).
