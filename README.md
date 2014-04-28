Slim Config - YAML
==================

Parses YAML files and adds them into Slim's config singleton.
Uses Symfony's YAML Component to parse files (http://github.com/symfony/Yaml).
Allows other YAML files to be imported and parameters to be set and used.

Installation
============

Composer
--------

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

Manual Install
--------------

Download and extract src/ directory into your project directory and ```require``` it in your
application's ```index.php``` file.
```php
<?php
require 'Yaml.php';
\\BurningDiode\Slim\Config\Yaml::getInstance()->addFile('/path/to/some/file');
```
