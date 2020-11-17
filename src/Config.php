<?php namespace PWC\AssetsManager;

class Config extends \PWC\Singleton\Config
{
    protected $dir = null;

    public function __setDefaultValue()
    {
        if (!is_null(\PWC\Config\RootDir::get())) {
            $composerJsonFile = \PWC\Config\RootDir::get() . '/composer.json';
            $composer = json_decode(file_get_contents($composerJsonFile));
            if (isset($composer->extra->pwc->assets->dir)) {
                self::set('dir', $composer->extra->pwc->assets->dir . '/');
            }
        }
    }
}
