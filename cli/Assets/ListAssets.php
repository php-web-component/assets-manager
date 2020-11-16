<?php namespace PWC\CLI\Assets;

class ListAssets extends \PWC\CLI
{
    public function __construct()
    {
        parent::__construct('assets', [
            $this, 'run'
        ]);

        $this->setShortDescription('List Available Assets');
        $this->setDescription('List Available Assets');
    }

    public function run(\GetOpt\GetOpt $opt)
    {
        $composerMap = array_merge($this->_config['composer']->getPrefixes(), $this->_config['composer']->getPrefixesPsr4(), $this->_config['composer']->getClassMap());

        $assetsDir = $composerMap['PWC\\Asset\\'] ?? [];

        echo "Name\t\t\tPackage\t\t\t\t\tVersion\t\tDist" . PHP_EOL;
        foreach ($assetsDir as $assetDir) {
            \PWC\Util\File::recursiveRead($assetDir, function ($file) use ($assetDir) {
                $assetFile = '\\PWC\\Asset' . str_replace('/', '\\', str_replace([
                    $assetDir, '.php'
                ], '', $file));
                echo $assetFile::$name . "\t\t" . $assetFile::$package . "\t\t" . $assetFile::$version . "\t\t" . $assetFile::$dist . PHP_EOL;
            });
        }
    }
}
