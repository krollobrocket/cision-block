<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit62164a894c2d9ee047ffd7611979b8c1
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'CisionBlock\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'CisionBlock\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit62164a894c2d9ee047ffd7611979b8c1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit62164a894c2d9ee047ffd7611979b8c1::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit62164a894c2d9ee047ffd7611979b8c1::$classMap;

        }, null, ClassLoader::class);
    }
}
