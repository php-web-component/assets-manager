<?php namespace PWC\CLI\Assets;

class PublishAssets extends \PWC\CLI
{
    public function __construct()
    {
        parent::__construct('assets:publish', [
            $this, 'run'
        ]);

        $this->setShortDescription('Publish Assets');
        $this->setDescription('Public Assets');
        $this->addOperands([
            \GetOpt\Operand::create('name', \GetOpt\Operand::MULTIPLE)
                ->setDescription('Asset name')
        ]);

        \PWC\Config::register(\PWC\AssetsManager\Config::class);
    }

    public function run(\GetOpt\GetOpt $opt)
    {
        if (!is_null(\PWC\AssetsManager\Config::get('dir'))) {
            @mkdir(\PWC\Config\RootDir::get() . \PWC\AssetsManager\Config::get('dir'), 0755, true);
        }

        if (count($opt->getOperands()) > 0) {
            foreach ($opt->getOperands() as $name) {
                
            }
        } else {
            foreach ((array_merge(\PWC\CLI\Config::get('composerAutoload')->getPrefixes(), \PWC\CLI\Config::get('composerAutoload')->getPrefixesPsr4(), \PWC\CLI\Config::get('composerAutoload')->getClassMap())['PWC\\Asset\\'] ?? []) as $assetDir) {

                \PWC\Util\File::recursiveRead($assetDir, function ($file) use ($assetDir) {
                    $assetFile = '\\PWC\\Asset' . str_replace('/', '\\', str_replace([
                        $assetDir, '.php'
                    ], '', $file));

                    if (is_subclass_of($assetFile, \PWC\Asset::class)) {
                        \PWC\Util\File::recursiveRead(\PWC\Config\RootDir::get() . $assetFile::$dist, function ($file2) use ($assetFile) {
                            $targetFileName = \PWC\Config\RootDir::get() . \PWC\AssetsManager\Config::get('dir') . $assetFile::$package . '/' . str_replace(\PWC\Config\RootDir::get(), '', str_replace("{$assetFile::$dist}/", '', $file2));
                            @mkdir(dirname($targetFileName), 0755, true);
                            @copy($file2, $targetFileName);
                            echo realpath($file2) . ' copied to ' . realpath($targetFileName) . PHP_EOL;
                        });
                    }
                });
            }
        }
    }
}
