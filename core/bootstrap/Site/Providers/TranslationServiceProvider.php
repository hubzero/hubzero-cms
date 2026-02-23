<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

use Hubzero\Base\ServiceProvider;
use Hubzero\Language\Translator;

/**
 * Language translation service provider
 */
class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return  void
     */
    public function register()
    {
        $this->app['language'] = function ($app) {
            $locale = $app['config']->get('locale', 'en-GB');
            $debug  = $app['config']->get('debug_lang', false);

            return new Translator($locale, $debug, ucfirst($app['client']->name));
        };

        $this->app['language.filter'] = false;
    }

    /**
     * Add the plugin loader to the event dispatcher.
     *
     * @return  void
     */
    public function boot()
    {
        $translator = $this->app['language'];

        $language = null;
        $appLangDir = PATH_APP . DS . 'language' . DS . strtolower($this->app['client']->name);

        // If a language was specified it has priority
        if (!$language && $this->app->has('request')) {
            $lang = $this->app['request']->getString('language', null);

            if ($lang && is_dir($appLangDir . DS . $lang)) {
                $language = $lang;
            }
        }

        // Detect cookie language
        if (!$language) { // && $this->app->has('language.filter'))
            $lang = $this->app['request']->getString($this->app->hash('language'), null, 'cookie');

            if ($lang && is_dir($appLangDir . DS . $lang)) {
                $language = $lang;
            }
        }

        // Detect user specified language
        if (!$language && $this->app->has('user')) {
            $lang = \User::getParam($this->app['client']->alias . '_language');

            if ($lang && is_dir($appLangDir . DS . $lang)) {
                $language = $lang;
            }
        }

        // Detect browser language
        if (!$language && $this->app->has('browser')) {
            $lang = $translator->detectLanguage();

            if ($lang && is_dir($appLangDir . DS . $lang)) {
                $language = $lang;
            }
        }

        // Detect default language
        if (!$language && $this->app->has('component')) {
            $params = $this->app['component']->params('com_languages');

            $language = $params->get(
                $this->app['client']->name,
                $this->app['config']->get('language', 'en-GB')
            );
        }

        // One last check to make sure we have something
        if (!$language || !is_dir($appLangDir . DS . $language)) {
            $lang = $this->app['config']->get('language', 'en-GB');

            if (is_dir($appLangDir . DS . $lang)) {
                $language = $lang;
            }
        }

        if ($language) {
            $translator->setLanguage($language);
        }
    }
}
