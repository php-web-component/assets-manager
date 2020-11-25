<?php namespace PWC\CLI\Assets;

use GetOpt\GetOpt;
use jc21\CliTable;
use PWC\Asset;
use PWC\CLI;
use PWC\CLI\Config as CLIConfig;
use PWC\Config;
use PWC\Util\File;

class ListAssets extends CLI
{
    public function __construct()
    {
        parent::__construct('assets', [
            $this, 'run'
        ]);

        $this->setShortDescription('List Available Assets');
        $this->setDescription('List Available Assets');
    }

    public function run(GetOpt $opt)
    {
        $table = new CliTable();
        $table->addField('Name', 'name');
        $table->addField('Package', 'package');
        $table->addField('Package', 'package');
        $table->addField('Version', 'version');
        $table->addField('Dist', 'dist');

        $data = [];
        foreach ((array_merge(CLIConfig::get('composerAutoload')->getPrefixes(), CLIConfig::get('composerAutoload')->getPrefixesPsr4(), CLIConfig::get('composerAutoload')->getClassMap())['PWC\\Asset\\'] ?? []) as $assetDir) {

            File::recursiveRead($assetDir, function ($file) use ($assetDir, &$data) {
                $assetFile = '\\PWC\\Asset' . str_replace('/', '\\', str_replace([
                    $assetDir, '.php'
                ], '', $file));

                if (is_subclass_of($assetFile, Asset::class)) {
                    $data[] = [
                        'name' => $assetFile::$name,
                        'package' => $assetFile::$package,
                        'version' => $assetFile::$version,
                        'dist' => $assetFile::$dist
                    ];
                }
            });
        }

        $table->injectData($data);
        $table->display();
    }
}
