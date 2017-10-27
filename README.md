# Autoloader

`Autoloader` 是一个强大的类载入器。它是 [Dida框架](https://github.com/zeupin/dida) 的一部分。

## 特点

* 轻量级，仅有一个独立的PHP文件，方便随处调用。
* 支持五种加载模式： PSR-4, PSR-0, Namespace, Classmap, Alias.
* 可以方便灵活地直接加载一些尚处于开发状态或者测试状态的类。这些类因为还没有正式发布到packagist，如果用composer模式处理很繁琐。
* MIT版权协议，免费商用。

## 安装

### 方法 1: 下载最新的 Autoloader.php 文件，然后直接在需要的地方引入它。推荐采用这种方式，比较简单直接。

下载网址如下：

```
https://github.com/zeupin/dida-autoloader/blob/master/src/Autoloader.php
```

然后在你的项目中直接include进去，举例如下：

```php
// 第一步，include本类。
include '/path/to/Autoloader.php';  // 你下载的Autoload.php的位置

// 第二步，执行初始化，把本类注册到系统中。
Autoloadeer::init(); // 调用初始化方法，注册到系统中。

// 第三步，然后再把Composer的autoload注册到系统中。
include '/path/to/composer/vendor/autoload.php';
```

### 方法 2: 用 Composer 安装。

```
composer require dida/autoloader
```

> 这种方式是PHP包的常规安装方式，但是用这种方法会导致一个问题：
> 
> 当碰到未知类时，是先在Composer中查，查不到才会到本类中查。而我们实际项目中，往往是希望是先到本类中查，查不到再去Composer中查。
> 
> 如果你是用这个方式调用本类的话，尤其需要注意查找未知类的匹配优先级问题。


## API

### 初始化

```php
public static function init();
```

### 五种匹配模式

```php
public static function addPsr4($namespace, $basedir);
public static function addPsr0($namespace, $basedir);
public static function addNamespace($namespace, $basedir);
public static function addClassmap($mapfile, $basedir = null);
public static function addAlias($alias, $real);
```

## 用法

```php
require '/path/to/Autoloader.php';

Autoloader::init();
Autoloader::addClassmap(__DIR__ . 'FooMap.php', '/your/real/base/path');
Autoloader::addNamespace('Foo\\Bar', __DIR__ . '/Your/Path');
```

### 如果是采用Classmap匹配模式时，相应的mapfile文件的示例如下：

```php
<?php
return [
    'Dida\\Application' => 'Application/Application.php',
    'Dida\\Config'      => 'Config/Config.php',
    'Dida\\Container'   => 'Container/Container.php',
    'Dida\\Controller'  => 'Controller/Controller.php',
];
```

## 作者

* [Macc Liu](https://github.com/maccliu)

## 鸣谢

* [宙品公司，Zeupin LLC](http://zeupin.com) , 尤其是 [Dida框架团队](http://dida.zeupin.com)

## 版权

Copyright (c) 2017 Zeupin LLC. [MIT 版权协议](LICENSE).