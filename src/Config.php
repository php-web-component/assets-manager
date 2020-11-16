<?php namespace PWC\AssetsManager;

class Config extends \PWC\Singleton\Config
{
    protected $dir = '';

    public function __setDefaultValue()
    {
        if (!is_null(\PWC\Config\RootDir::instance()->get())) {
            $composerJsonFile = \PWC\Config\RootDir::instance()->get() . '/composer.json';
            $composer = json_decode(file_get_contents($composerJsonFile));
            if (isset($composer->extra->pwc->assets->dir)) {
                $this->set('dir', $composer->extra->pwc->assets->dir . '/');
            }
        }
    }
}
