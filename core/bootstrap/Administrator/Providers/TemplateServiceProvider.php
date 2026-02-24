<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

use Hubzero\Base\ServiceProvider;
use Hubzero\Config\FileWriter;
use Hubzero\Template\Loader;

/**
 * Template loader service provider
 */
class TemplateServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return  void
     */
    public function register()
    {
        $this->app['template.loader'] = function ($app) {
            $options = [
                'path_app'  => PATH_APP . DS . 'templates',
                'path_core' => PATH_CORE . DS . 'templates',
                'style'     => 0,
                'lang'      => ''
            ];

            $options['style'] = \User::getParam('admin_style', $options['style']);

            return new Loader($app, $options);
        };

        $this->app['template'] = function ($app) {
            $loader = $app['template.loader'];

            if ($style = $app['request']->getVar('templateStyle', 0)) {
                $loader->setStyle($style);
            }

            $template = $loader->load();

            // Keep config in sync with the database default
            $configName = $app['config']->get('administrator_template');
            if ($template->home && $configName !== $template->template) {
                $configPath = PATH_APP . '/config';
                $configFile = $configPath . '/app.php';
                if (file_exists($configFile)) {
                    $config = include $configFile;
                    if (is_array($config)) {
                        $config['administrator_template'] = $template->template;
                        $writer = new FileWriter('php', $configPath);
                        $writer->write($config, 'app');
                    }
                }
            }

            return $template;
        };
    }
}
