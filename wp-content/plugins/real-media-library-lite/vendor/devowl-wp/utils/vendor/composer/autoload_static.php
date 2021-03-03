<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd931f738e4b6285b4c23d103ffcccbb1 {
    public static $prefixLengthsPsr4 = [
        'M' => [
            'MatthiasWeb\\Utils\\Test\\' => 23,
            'MatthiasWeb\\Utils\\' => 18
        ]
    ];

    public static $prefixDirsPsr4 = [
        'MatthiasWeb\\Utils\\Test\\' => [
            0 => __DIR__ . '/../..' . '/test/phpunit'
        ],
        'MatthiasWeb\\Utils\\' => [
            0 => __DIR__ . '/../..' . '/src'
        ]
    ];

    public static $classMap = [
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php'
    ];

    public static function getInitializer(ClassLoader $loader) {
        return \Closure::bind(
            function () use ($loader) {
                $loader->prefixLengthsPsr4 = ComposerStaticInitd931f738e4b6285b4c23d103ffcccbb1::$prefixLengthsPsr4;
                $loader->prefixDirsPsr4 = ComposerStaticInitd931f738e4b6285b4c23d103ffcccbb1::$prefixDirsPsr4;
                $loader->classMap = ComposerStaticInitd931f738e4b6285b4c23d103ffcccbb1::$classMap;
            },
            null,
            ClassLoader::class
        );
    }
}
