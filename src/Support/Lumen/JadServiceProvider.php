<?php declare(strict_types=1);

namespace Jad\Support\Lumen;

use Illuminate\Support\ServiceProvider;
use Jad\Configure;

/**
 * Class JadServiceProvider
 * @package Jad\Support\Lumen
 */
class JadServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $config = Configure::getInstance();
        $config->setConfig('debug', config()['jad']['debug']);
        $config->setConfig('cors', config()['jad']['cors']);
        $config->setConfig('max_page_size', config()['jad']['max_page_size']);
        $config->setConfig('strict', config()['jad']['strict']);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            $this->getConfigPath(), 'jad'
        );

        $this->app->configure('jad');
    }

    /**
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__ . '/jad.php';
    }
}