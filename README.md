# Autoloader

Autoloader is a powerful class loader. It's a part of Dida Framework.

* Five modes: PSR-4, PSR-0, Namespace, Classmap, Alias.
* Friendly MIT License.
* Just a single file.

## Installation

### Method 1: Download the latest Autoloader.php, and include it directly. (Recommended)

Download from this url:

```
https://github.com/zeupin/dida-autoloader/blob/master/src/Autoloader.php
```

Include it in your project:

```php
// step 1
include '/path/to/Autoloader.php';
Autoloadeer::init();

// step 2
include '/path/to/composer/vendor/autoload.php';
```

### Method 2: Install via Composer

```
composer require dida/autoloader
```

**Attention:** If you use this method, PHP will give priority to `the composer's autoload`, if not found, then match the items in `your Autoloader`.Please pay attention to the priority.

## API

### Initialization:

```php
public static function init();
```

### Five loader types:

```php
public static function addPsr4($namespace, $basedir);
public static function addPsr0($namespace, $basedir);
public static function addNamespace($namespace, $basedir);
public static function addClassmap($mapfile, $basedir = null);
public static function addAlias($alias, $real);
```

## Usage

```php
require FOO_PATH . 'Autoloader.php';

Autoloader::init();
Autoloader::addClassmap(__DIR__ . 'FooMap.php', '/your/real/base/path');
Autoloader::addNamespace('Foo\\Bar', __DIR__ . '/Your/Path');
```

### Classmap File

If you use the `addClassmap($mapfile, $basedir)` function, here is a mapfile sample:

```php
<?php
return [
    'Dida\\Application' => 'Application/Application.php',
    'Dida\\Config'      => 'Config/Config.php',
    'Dida\\Container'   => 'Container/Container.php',
    'Dida\\Controller'  => 'Controller/Controller.php',
];
```

## Authors

* [Macc Liu](https://github.com/maccliu)

## Credits

* [Zeupin LLC](http://zeupin.com) , especially [Dida Team](http://dida.zeupin.com)

## License

Copyright (c) 2017 Zeupin LLC. Released under the [MIT license](LICENSE).