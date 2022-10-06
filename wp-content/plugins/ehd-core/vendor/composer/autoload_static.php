<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6cef821f1336fb207d3ff45c9f00b4e9
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        'a4a119a56e50fbb293281d9a48007e0e' => __DIR__ . '/..' . '/symfony/polyfill-php80/bootstrap.php',
        '841f98c5d948ce534a6f87abe5b50614' => __DIR__ . '/..' . '/roots/wp-password-bcrypt/wp-password-bcrypt.php',
        '667aeda72477189d0494fecd327c3641' => __DIR__ . '/..' . '/symfony/var-dumper/Resources/functions/dump.php',
        'ed8e8b1c5434502ce047f777d92c74a3' => __DIR__ . '/../..' . '/inc/Plugin.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Php80\\' => 23,
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Component\\VarDumper\\' => 28,
        ),
        'E' => 
        array (
            'EHD\\Plugins\\Woocommerce\\' => 24,
            'EHD\\Plugins\\Widgets\\' => 20,
            'EHD\\Plugins\\Themes\\' => 19,
            'EHD\\Plugins\\Elementor\\' => 22,
            'EHD\\Plugins\\Core\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Php80\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php80',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Component\\VarDumper\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/var-dumper',
        ),
        'EHD\\Plugins\\Woocommerce\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc/Woocommerce',
        ),
        'EHD\\Plugins\\Widgets\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc/Widgets',
        ),
        'EHD\\Plugins\\Themes\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc/Themes',
        ),
        'EHD\\Plugins\\Elementor\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc/Elementor',
        ),
        'EHD\\Plugins\\Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc/Core',
        ),
    );

    public static $classMap = array (
        'Attribute' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Attribute.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'PhpToken' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/PhpToken.php',
        'Stringable' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Stringable.php',
        'UnhandledMatchError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
        'ValueError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/ValueError.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6cef821f1336fb207d3ff45c9f00b4e9::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6cef821f1336fb207d3ff45c9f00b4e9::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6cef821f1336fb207d3ff45c9f00b4e9::$classMap;

        }, null, ClassLoader::class);
    }
}
