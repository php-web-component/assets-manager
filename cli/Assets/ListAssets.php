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

        \PWC\Config::register(\PWC\AssetsManager\Config::class);
    }

    public function run(\GetOpt\GetOpt $opt)
    {
        echo "Name\t\t\tPackage\t\t\t\t\tVersion\t\tDist" . PHP_EOL;
        foreach ((array_merge(\PWC\CLI\Config::get('composerAutoload')->getPrefixes(), \PWC\CLI\Config::get('composerAutoload')->getPrefixesPsr4(), \PWC\CLI\Config::get('composerAutoload')->getClassMap())['PWC\\Asset\\'] ?? []) as $assetDir) {

            \PWC\Util\File::recursiveRead($assetDir, function ($file) use ($assetDir) {
                $assetFile = '\\PWC\\Asset' . str_replace('/', '\\', str_replace([
                    $assetDir, '.php'
                ], '', $file));

                if (is_subclass_of($assetFile, \PWC\Asset::class)) {
                    echo $assetFile::$name . "\t\t" . $assetFile::$package . "\t\t" . $assetFile::$version . "\t\t" . $assetFile::$dist . PHP_EOL;
                }
            });
        }
    }
}
