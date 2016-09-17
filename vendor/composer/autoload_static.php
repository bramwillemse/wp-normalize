<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcb54529bdfec61d8f6186d0ab531e6e9
{
    public static $classMap = array (
        'normalizeImages' => __DIR__ . '/../..' . '/classes/class-normalize-images.php',
        'normalizeWP' => __DIR__ . '/../..' . '/classes/class-normalize-wp.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitcb54529bdfec61d8f6186d0ab531e6e9::$classMap;

        }, null, ClassLoader::class);
    }
}