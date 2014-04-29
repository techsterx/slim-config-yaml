# Slim Config - YAML

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
require 'Yaml.php';
\\BurningDiode\Slim\Config\Yaml::getInstance()->addFile('/path/to/some/file');
```

### Examples

To add a single file, use the ```addFile()``` method.
```php
\\BurningDiode\Slim\Config\Yaml::getInstance()->addFile('/path/to/some/file.yaml');
```

You can also chain multiple ```addFile()``` methods togethor.
```php
\\BurningDiode\Slim\Config\Yaml::getInstance()
    ->addFile('/path/to/some/file.yaml')
    ->addFile('/path/to/another/file.yaml');
```

You can import a whole directory of YAML files as well.
```php
\\BurningDiode\Slim\Config\Yaml::getInstance()->addDirectory('/path/to/directory');
```

You can chain with the ```addDirectory()``` method as well.
```php
\\BurningDiode\Slim\Config\Yaml::getInstance()
    ->addDirectory('/path/to/directory')
    ->addFile('/path/to/some/file.yaml');
```

## License

The Slim Framework is released under the MIT public license.
