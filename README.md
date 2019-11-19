# Autoloader

`Autoloader` 是一个强大的类载入器。它是 [Dida 框架](https://github.com/zeupin/dida) 的一部分。

## 特点

- 轻量级，仅有一个独立的 PHP 文件，方便随处调用。
- 支持四种加载模式： PSR-4, PSR-0, Classmap, Alias.
- 可以方便灵活地直接加载一些尚处于开发状态或者测试状态的类。这些类因为还没有正式发布到 packagist，如果用 composer 模式处理很繁琐。
- MIT 版权协议，免费商用。

## 安装

### 方法 1: （推荐）下载最新的 Autoloader.php 文件，然后直接在需要的地方引入它。

下载网址如下：

```
https://github.com/zeupin/dida-autoloader/blob/master/src/Autoloader.php
```

然后在你的项目中直接 include 进去，举例如下：

```php
// 第一步，include本类。
include '/path/to/Autoloader.php';  // 你下载的Autoload.php的位置

// 第二步，执行初始化，把本类注册到系统中。
Autoloadeer::init(); // 调用初始化方法，注册到系统中。如果忘记写这句，AutoLoader等于没用哦！

// 第三步，Load需要自动载入的本地类。
\Dida\Autoloader::addPsr4('Your\\Namespace', '/your/package/path/');
\Dida\Autoloader::addClassmap(__DIR__ . 'FooMap.php', '/your/real/base/path');

// 第四步，然后再把Composer的autoload注册到系统中。
include '/path/to/composer/vendor/autoload.php';
```

推荐采用这种方式，AutoLoad 的使用流程清晰明了。

### 方法 2: 用 Composer 安装。

```
composer require dida/autoloader
```

如果你是用这个方式调用本类的话，尤其需要注意查找未知类的匹配优先顺序问题。

### 推荐方法 1 而不是方法 2 的原因

方法 1 和方法 2 的主要区别在于**搜索的优先顺序**：

- 用方法 1，先搜 `AutoLoader` 的定义，找不到再去 `composer.json` 的定义里面搜。
- 用方法 2，先搜 `composer.json` 的定义里面搜，找不到再去 `AutoLoader` 的定义里面搜。

在实际项目中，想使用 `AutoLoader` 的目的，绝大多数都是要使用自定义的类，而不是 Composer 的 vendor 里面的库，所以用方法 1 能更好达成此目的。

## API

### 初始化

```php
public static function init();
```

### 四种匹配模式

```php
public static function addPsr4($namespace, $basedir);
public static function addPsr0($namespace, $basedir);
public static function addClassmap($mapfile, $basedir = null);
public static function addAlias($alias, $real);
```

## 用法

```php
require '/path/to/Autoloader.php';

\Dida\Autoloader::init();
\Dida\Autoloader::addPsr4('Dida\\', '/your/root/path/');
\Dida\Autoloader::addClassmap(__DIR__ . 'FooMap.php', '/your/real/base/path');
```

### 如果是采用 Classmap 匹配模式时，相应的 mapfile 文件的示例如下：

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

- [Macc Liu](https://github.com/maccliu)

## 鸣谢

- [宙品公司，Zeupin LLC](http://zeupin.com) , 尤其是 [Dida 框架团队](http://dida.zeupin.com)

## 版权声明

版权所有 (c) 2017-2019 上海宙品信息科技有限公司。<br>Copyright (c) 2017-2019 Zeupin LLC. <http://zeupin.com>

源代码采用 MIT 授权协议。<br>Licensed under The MIT License.

如需在您的项目中使用，必须保留本源代码中的完整版权声明。<br>Redistributions of files MUST retain the above copyright notice.
