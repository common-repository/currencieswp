<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit55e55624a5fd102ce2ab4a51a6074921
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Dnolbon\\Wordpress\\' => 18,
            'Dnolbon\\Twig\\' => 13,
            'Dnolbon\\Currencies\\' => 19,
            'Dnolbon\\CurrenciesWp\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Dnolbon\\Wordpress\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Dnolbon/Wordpress',
        ),
        'Dnolbon\\Twig\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Dnolbon/Twig',
        ),
        'Dnolbon\\Currencies\\' => 
        array (
            0 => __DIR__ . '/..' . '/dnolbon/currencies/src/Dnolbon/Currencies',
        ),
        'Dnolbon\\CurrenciesWp\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Dnolbon/CurrenciesWp',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit55e55624a5fd102ce2ab4a51a6074921::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit55e55624a5fd102ce2ab4a51a6074921::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit55e55624a5fd102ce2ab4a51a6074921::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
