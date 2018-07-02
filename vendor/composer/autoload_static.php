<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3fb9943afcee1f33e30c20ba45a37cd8
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TVShowsAPI\\' => 11,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'M' => 
        array (
            'Monolog\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TVShowsAPI\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/monolog/monolog/src/Monolog',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3fb9943afcee1f33e30c20ba45a37cd8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3fb9943afcee1f33e30c20ba45a37cd8::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}