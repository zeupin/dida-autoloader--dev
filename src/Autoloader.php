<?php
/**
 * Dida Framework --Powered by Zeupin LLC
 * http://dida.zeupin.com
 */
class Autoloader
{
    /**
     * 版本号
     */
    const VERSION = '0.1.0';

    /**
     * @var boolean
     */
    private static $_initialized = false;

    /**
     * @var array
     */
    private static $_queue = [];


    /**
     * 初始化。
     * 把本类注册到系统的spl_autoload_register中。
     */
    public static function init()
    {
        // 以单例模式运行
        if (self::$_initialized) {
            return;
        }

        // 注册callback函数为autoload()
        spl_autoload_register([__CLASS__, 'autoload']);

        // 设置单例标志
        self::$_initialized = true;
    }


    /**
     * 依次检查当前登记的队列，如果能找到匹配FQCN，就进行自动加载。
     *
     * @param string $FQCN  FQCN(Fully Qualified Class Name)
     *
     * @return boolean 是否找到了匹配记录。
     */
    protected static function autoload($FQCN)
    {
        foreach (self::$_queue as $item) {
            switch ($item['type']) {
                case 'classmap':
                    $result = self::matchClassmap($FQCN, $item['mapfile'], $item['basedir'], $item['map']);
                    if ($result) {
                        return true;
                    }
                    break;

                case 'psr4':
                    $result = self::matchPsr4($FQCN, $item['namespace'], $item['basedir'], $item['len']);
                    if ($result) {
                        return true;
                    }
                    break;

                case 'psr0':
                    $result = self::matchPsr0($FQCN, $item['namespace'], $item['basedir'], $item['len']);
                    if ($result) {
                        return true;
                    }
                    break;

                case 'alias':
                    $result = self::matchAlias($FQCN, $item['alias'], $item['real']);
                    if ($result) {
                        return true;
                    }
                    break;
            }
        }

        // 如果队列中都没有，则返回false。
        return false;
    }


    /**
     * 新增一个PSR-4风格的加载模板。
     *
     * @param string $namespace  名称空间。如：'your\\namespace\\'
     *     如果$namespace的最前面一个字符是“\”，会自动去除。
     *     如果$namespace的最后一个字符不是“\”，会自动加上。
     * @param string $basedir    基准目录。如：'/your/namespace/base/directory/'
     *
     * @return boolean
     */
    public static function addPsr4($namespace, $basedir)
    {
        // 确保做过init动作。
        self::init();

        // 检查 $basedir
        if (!file_exists($basedir) || !is_dir($basedir)) {
            return false;
        } else {
            $basedir = realpath($basedir);
        }

        // 把$namepsace标准化。
        if (!is_string($namespace)) {
            return false;
        }
        $namespace = trim($namespace, "\\ \t\n\r\0\x0B"); // 所有的空白字符以及“\”
        $namespace = $namespace . '\\';

        // 加到查询队列中
        self::$_queue[] = [
            'type'      => 'psr4',
            'namespace' => $namespace,
            'basedir'   => $basedir,
            'len'       => strlen($namespace),
        ];

        return true;
    }


    /**
     * 匹配一个PSR-4风格的加载模板。
     */
    private static function matchPsr4($FQCN, $namespace, $basedir, $len)
    {
        // 检查FQCN是否位于这个namespace
        if (strncmp($FQCN, $namespace, $len) !== 0) {
            return false;
        }

        // 截取要匹配的部分
        $rest = substr($FQCN, $len);

        // 检查目标文件是否存在
        $target = "{$basedir}/{$rest}.php";
        if (file_exists($target) && is_file($target)) {
            require $target;
            return true;
        } else {
            return false;
        }
    }


    /**
     * 新增一个PSR-0风格的加载模板
     *
     * @param string $namespace  名称空间。如：'your\\namespace\\'
     *     如果$namespace的最前面一个字符是“\”，会自动去除。
     *     如果$namespace的最后一个字符不是“\”，会自动加上。
     * @param string $basedir    基准目录。如：'/your/namespace/base/directory/'
     *
     * @return boolean
     */
    public static function addPsr0($namespace, $basedir)
    {
        // 确保做过init动作
        self::init();

        // 检查 $basedir
        if (!file_exists($basedir) || !is_dir($basedir)) {
            return false;
        } else {
            $basedir = realpath($basedir);
        }

        // 把$namepsace标准化。
        if (!is_string($namespace)) {
            return false;
        }
        $namespace = trim($namespace, "\\ \t\n\r\0\x0B"); // 所有的空白字符以及“\”
        $namespace = $namespace . '\\';

        // 加到队列中
        self::$_queue[] = [
            'type'      => 'psr0',
            'namespace' => $namespace,
            'basedir'   => $basedir,
            'len'       => strlen($namespace),
        ];

        return true;
    }


    /**
     * 匹配一个PSR-0风格的加载模板。
     */
    private static function matchPsr0($FQCN, $namespace, $basedir, $len)
    {
        // 检查是否位于这个namespace下
        if (strncmp($FQCN, $namespace, $len) !== 0) {
            return false;
        }

        // 截取要匹配的部分
        $rest = substr($FQCN, $len);

        // 处理PSR-0中对于“_”的定义
        $rest = str_replace('_', DIRECTORY_SEPARATOR, $rest);

        // 检查目标文件是否存在
        if ($namespace === '') {
            $target = "{$basedir}/{$rest}.php";
        } else {
            $target = "{$basedir}/{$namespace}/{$rest}.php";
        }

        // 如果目标文件存在，则require进来
        if (file_exists($target) && is_file($target)) {
            require $target;
            return true;
        } else {
            return false;
        }
    }


    /**
     * 新增一个classmap风格的加载模板。
     *
     * @param string $mapfile  mapfile文件，详见README.md中的说明。
     * @param string $basedir  mapfile中定义的类文件所在的基准目录。
     *
     * @return boolean
     */
    public static function addClassmap($mapfile, $basedir = null)
    {
        // 确保做过init动作
        self::init();

        // 检查mapfile
        if (!file_exists($mapfile) || !is_file($mapfile)) {
            return false;
        } else {
            $mapfile = realpath($mapfile);
        }

        // 检查$basedir
        if (is_null($basedir)) {
            $basedir = dirname($mapfile);
        } elseif (!is_string($basedir) || !file_exists($basedir) || !is_dir($basedir)) {
            return false;
        } else {
            $basedir = realpath($basedir);
        }

        // 加到查询队列中
        self::$_queue[] = [
            'type'    => 'classmap',
            'mapfile' => $mapfile,
            'basedir' => $basedir,
            'map'     => null,
        ];

        return true;
    }


    /**
     * 匹配一个mapfile风格的加载模板
     */
    private static function matchClassmap($FQCN, $mapfile, $basedir, &$map)
    {
        // 如果是首次运行，先把mapfile的内容做下缓存
        if (is_null($map)) {
            $map = require($mapfile);

            // Checks $map, sets it to [] if invalid.
            if (!is_array($map)) {
                $map = [];
                return false;
            }
        }

        // Checks if $map is empty.
        if (empty($map)) {
            return false;
        }

        // Checks if FQCN exists.
        if (!array_key_exists($FQCN, $map)) {
            return false;
        }

        // Loads the target file.
        $target = $basedir . '/' . $map[$FQCN];
        if (file_exists($target) && is_file($target)) {
            require $target;
            return true;
        } else {
            return false;
        }
    }


    /**
     * 新增一个alias风格的加载模板。
     *
     * @param string $alias  别名。如：Your\Class\Alias，注意：最前面没有“\”。
     * @param string $real   对应的实际类名。如：\Its\Real\FQCN，注意，最前面有“\”。
     *
     * @return boolean
     */
    public static function addAlias($alias, $real)
    {
        // Initialize
        self::init();

        // Adds it to $_queue
        self::$_queue[] = [
            'type'  => 'alias',
            'alias' => $alias,
            'real'  => $real,
        ];

        return true;
    }


    /**
     * 匹配一个alias风格的加载模板。
     */
    private static function matchAlias($FQCN, $alias, $real)
    {
        if ($FQCN === $alias) {
            return class_alias($real, $alias);
        } else {
            return false;
        }
    }
}
