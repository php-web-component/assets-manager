<?php namespace PWC\CLI\Assets;

class PublishAssets extends \PWC\CLI
{
    private $assetsFile = [];
    private $assetDir = null;

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
    }

    public function setConfig($config = [])
    {
        $parent = parent::setConfig($config);

        $composerMap = array_merge($this->_config['composer']->getPrefixes(), $this->_config['composer']->getPrefixesPsr4(), $this->_config['composer']->getClassMap());

        $assetsDir = $composerMap['PWC\\Asset\\'] ?? [];

        $assetsFile = [];
        foreach ($assetsDir as $assetDir) {
            \PWC\Util\File::recursiveRead($assetDir, function ($file) use ($assetDir, &$assetsFile) {
                $assetFile = '\\PWC\\Asset' . str_replace('/', '\\', str_replace([
                    $assetDir, '.php'
                ], '', $file));
                $assetsFile[] = $assetFile;
            });
        }

        $this->assetsFile = $assetsFile;

        $composerFile = $this->_config['rootDir'] . 'composer.json';
        $composerJson = json_decode(file_get_contents($composerFile));
        if (isset($composerJson->extra->pwc->assets->dir)) {
            $this->assetDir = $this->_config['rootDir'] . $composerJson->extra->pwc->assets->dir;
        }

        if (!is_null($this->assetDir)) {
            @mkdir($this->assetDir, 0755, true);
        }

        return $this;
    }

    public function run(\GetOpt\GetOpt $opt)
    {
        if (count($opt->getOperands()) > 0) {
            foreach ($opt->getOperands() as $name) {
                
            }
        } else {
            foreach ($this->assetsFile as $assetFile) {
                \PWC\Util\File::recursiveRead($this->_config['rootDir'] . $assetFile::$dist, function ($file) use ($assetFile) {
                    $targetFileName = $this->assetDir . '/' . $assetFile::$package . '/' . str_replace($this->_config['rootDir'], '', str_replace("{$assetFile::$dist}/", '', $file));
                    @mkdir(dirname($targetFileName), 0755, true);
                    @copy($file, $targetFileName);
                });
            }
        }
    }
}
