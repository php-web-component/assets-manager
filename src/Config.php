<?php namespace PWC\AssetsManager;

use PWC\Config\BaseURL;
use PWC\Config\RootDir;
use PWC\Singleton\Config as SingletonConfig;

class Config extends SingletonConfig
{
    protected $dir = null;

    public function __setDefaultValue()
    {
        if (!is_null(RootDir::get())) {
            $composerJsonFile = RootDir::get() . '/composer.json';
            $composer = json_decode(file_get_contents($composerJsonFile));
            if (isset($composer->extra->pwc->assets->dir)) {
                self::set('dir', BaseURL::get() .  '/' . $composer->extra->pwc->assets->dir . '/');
            }
        }
    }
}
