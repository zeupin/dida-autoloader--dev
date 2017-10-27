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

                case 'namespace':
                    $result = self::matchNamespace($FQCN, $item['namespace'], $item['basedir'], $item['len']);
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
     * 登记一个PSR-4风格的namespace。
     *
     * @param string $namespace  名称空间。如：'your\\namespace\\'
     * @param string $basedir    基准目录。如：'/your/namespace/base/directory/'
     *
     * @return bool
     */
    public static function addPsr4($namespace, $basedir)
    {
        // 确保做过init动作
        self::init();

        // 检查 $basedir
        if (!file_exists($basedir) || !is_dir($basedir)) {
            return false;
        } else {
            $basedir = realpath($basedir);
        }

        // 预处理 $namepsace
        if (!is_string($namespace)) {
            return false;
        }
        $namespace = trim($namespace, "\\ \t\n\r\0\x0B"); // 所有的空白字符以及“\”

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
     * Matches a PSR-4 namespace
     */
    private static function matchPsr4($FQCN, $namespace, $basedir, $len)
    {
        // Checks if the prefix is matched.
        if (strncmp($FQCN, $namespace . '\\', $len + 1) !== 0) {
            return false;
        }

        // Strips the namespace
        $rest = substr($FQCN, $len + 1);

        // Checks if the target php file exists.
        $target = "{$basedir}/{$rest}.php";
        if (file_exists($target) && is_file($target)) {
            require $target;
            return true;
        } else {
            return false;
        }
    }


    /**
     * Adds a PSR-0 namespace
     *
     * @param string $namespace  Namespace. such as 'your\\namespace'
     * @param string $basedir    BaseDir. such as '/your/namespace/base/directory/'
     *
     * @return bool
     */
    public static function addPsr0($namespace, $basedir)
    {
        // Initialize
        self::init();

        // Checks $basedir
        if (!file_exists($basedir) || !is_dir($basedir)) {
            return false;
        } else {
            $basedir = realpath($basedir);
        }

        // Preproccesses $namepsace
        $namespace = trim($namespace, " \\\t\n\r\0\x0B");

        // Adds it to $_queue
        self::$_queue[] = [
            'type'      => 'psr0',
            'namespace' => $namespace,
            'basedir'   => $basedir,
            'len'       => strlen($namespace),
        ];

        return true;
    }


    /**
     * Matches a PSR-0 namespace
     */
    private static function matchPsr0($FQCN, $namespace, $basedir, $len)
    {
        // Checks if the prefix is matched.
        if (strncmp($FQCN, $namespace . '\\', $len + 1) !== 0) {
            return false;
        }

        // Strips the namespace
        $rest = substr($FQCN, $len + 1);

        // deal with '_' in the rest
        $rest = str_replace('_', DIRECTORY_SEPARATOR, $rest);

        // Checks if the target php file exists.
        if ($namespace === '') {
            $target = "{$basedir}/{$rest}.php";
        } else {
            $target = "{$basedir}/{$namespace}/{$rest}.php";
        }

        if (file_exists($target) && is_file($target)) {
            require $target;
            return true;
        } else {
            return false;
        }
    }


    /**
     * Adds a namespace.
     *
     * If try to match the \Namepsace\Your\Cool\Class,
     * it will check:
     *   <basedir>/Your/Cool/Class.php
     *   <basedir>/Your/Cool/Class/Class.php
     *
     * @param string $namespace  Namespace. such as 'your\\namespace'
     * @param string $basedir    BaseDir. such as '/your/namespace/base/directory/'
     *
     * @return bool
     */
    public static function addNamespace($namespace, $basedir)
    {
        // Initialize
        self::init();

        // Checks $basedir
        if (!file_exists($basedir) || !is_dir($basedir)) {
            return false;
        } else {
            $basedir = realpath($basedir);
        }

        // Preproccesses $namepsace
        $namespace = trim($namespace, " \\\t\n\r\0\x0B");

        // Adds it to $_queue
        self::$_queue[] = [
            'type'      => 'namespace',
            'namespace' => $namespace,
            'basedir'   => $basedir,
            'len'       => strlen($namespace),
        ];

        return true;
    }


    /**
     * Matches a namespace
     */
    private static function matchNamespace($FQCN, $namespace, $basedir, $len)
    {
        // Checks if the prefix is matched.
        if (strncmp($FQCN, $namespace . '\\', $len + 1) !== 0) {
            return false;
        }

        // Strips the namespace
        $rest = substr($FQCN, $len + 1);

        // Checks if the target php file exists.
        $target = "$basedir/$rest.php";
        if (file_exists($target) && is_file($target)) {
            require $target;
            return true;
        }

        // If $rest not contain '\'
        if (strpos($rest, '\\') === false) {
            $target = "{$basedir}/{$rest}/{$rest}.php";
            if (file_exists($target) && is_file($target)) {
                require $target;
                return true;
            } else {
                return false;
            }
        }

        // If $rest contains '\', split $rest to $base + $name, then checks files exist.
        $array = explode('\\', $rest);
        $name = array_pop($array);
        $base = implode('/', $array);
        $target1 = "{$basedir}/{$base}/{$name}.php";
        $target2 = "{$basedir}/{$base}/{$name}/{$name}.php";
        if (file_exists($target1) && is_file($target1)) {
            require $target1;
            return true;
        } elseif (file_exists($target2) && is_file($target2)) {
            require $target2;
            return true;
        } else {
            return false;
        }
    }


    /**
     * Adds a class map file
     *
     * @param string $mapfile   The real path of the class map file.
     * @param string $basedir  The base directory. default is the mapfile's directory.
     *
     * @return bool
     */
    public static function addClassmap($mapfile, $basedir = null)
    {
        // Initialize
        self::init();

        // Checks $mapfile
        if (!file_exists($mapfile) || !is_file($mapfile)) {
            return false;
        } else {
            $mapfile = realpath($mapfile);
        }

        // Checks $basedir
        if (is_null($basedir)) {
            $basedir = dirname($mapfile);
        } elseif (!is_string($basedir) || !file_exists($basedir) || !is_dir($basedir)) {
            return false;
        } else {
            $basedir = realpath($basedir);
        }

        // Adds it to $_queue
        self::$_queue[] = [
            'type'    => 'classmap',
            'mapfile' => $mapfile,
            'basedir' => $basedir,
            'map'     => null,
        ];

        return true;
    }


    /**
     * Matches FQCN from the map file
     */
    private static function matchClassmap($FQCN, $mapfile, $basedir, &$map)
    {
        // If first run, loads the mapfile content to $map.
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
     * 新增一个别名匹配模板
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
     * 匹配一个别名模板。
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
