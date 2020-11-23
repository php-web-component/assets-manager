<?php namespace PWC\CLI\Assets;

use GetOpt\GetOpt;
use GetOpt\Operand;
use jc21\CliTable;
use PWC\Asset;
use PWC\AssetsManager\Config as AssetsManagerConfig;
use PWC\CLI;
use PWC\CLI\Config as CLIConfig;
use PWC\Config;
use PWC\Config\RootDir;
use PWC\Util\File;

class PublishAssets extends CLI
{
    public function __construct()
    {
        parent::__construct('assets:publish', [
            $this, 'run'
        ]);

        $this->setShortDescription('Publish Assets');
        $this->setDescription('Public Assets');
        $this->addOperands([
            Operand::create('name', Operand::MULTIPLE)
                ->setDescription('Asset name')
        ]);

        Config::register(AssetsManagerConfig::class);
    }

    public function run(GetOpt $opt)
    {
        if (!is_null(AssetsManagerConfig::get('dir'))) {
            @mkdir(RootDir::get() . AssetsManagerConfig::get('dir'), 0755, true);
        }

        if (count($opt->getOperands()) > 0) {
            foreach ($opt->getOperands() as $name) {
                
            }
        } else {
            $table = new CliTable();
            $table->addField('Package', 'package');
            $table->addField('File', 'file');
            $table->addField('Status', 'status');

            $data = [];
            foreach ((array_merge(CLIConfig::get('composerAutoload')->getPrefixes(), CLIConfig::get('composerAutoload')->getPrefixesPsr4(), CLIConfig::get('composerAutoload')->getClassMap())['PWC\\Asset\\'] ?? []) as $assetDir) {

                File::recursiveRead($assetDir, function ($file) use ($assetDir, &$data) {
                    $assetFile = '\\PWC\\Asset' . str_replace('/', '\\', str_replace([
                        $assetDir, '.php'
                    ], '', $file));

                    if (is_subclass_of($assetFile, Asset::class)) {
                        File::recursiveRead(RootDir::get() . $assetFile::$dist, function ($file2) use ($assetFile, &$data) {
                            $targetFileName = RootDir::get() . AssetsManagerConfig::get('dir') . $assetFile::$package . '/' . str_replace(RootDir::get(), '', str_replace("{$assetFile::$dist}/", '', $file2));
                            @mkdir(dirname($targetFileName), 0755, true);
                            $copy = @copy($file2, $targetFileName);

                            $data[] = [
                                'package' => $assetFile::$package,
                                'file' => str_replace($assetFile::$dist . '/', '', str_replace(RootDir::get(), '', $file2)),
                                'status' => $copy ? 'OK' : 'NOK'
                            ];
                        });
                    }
                });
            }

            $table->injectData($data);
            $table->display();
        }
    }
}
