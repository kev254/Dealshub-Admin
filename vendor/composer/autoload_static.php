<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf2cde18a3f866594e0714637b3c3f7d8
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf2cde18a3f866594e0714637b3c3f7d8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf2cde18a3f866594e0714637b3c3f7d8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf2cde18a3f866594e0714637b3c3f7d8::$classMap;

        }, null, ClassLoader::class);
    }
}
