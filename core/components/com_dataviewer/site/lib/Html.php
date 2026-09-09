<?php

/**
 * Asset loading helper for the Dataviewer component.
 *
 * Adds JS and CSS files to the document, resolving paths relative
 * to the component's assets/ directory.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Lib;

class Html
{
    /**
     * Base filesystem path for assets.
     *
     * @var  string
     */
    private static $basePath;

    /**
     * Base URL path for assets.
     *
     * @var  string
     */
    private static $baseUrl;

    /**
     * Get the filesystem base path for assets.
     *
     * @return  string
     */
    private static function getBasePath(): string
    {
        if (!self::$basePath) {
            self::$basePath = dirname(__DIR__) . '/assets';
        }
        return self::$basePath;
    }

    /**
     * Get the URL base path for assets.
     *
     * @return  string
     */
    private static function getBaseUrl(): string
    {
        if (!self::$baseUrl) {
            self::$baseUrl = str_replace(
                PATH_ROOT,
                '',
                dirname(__DIR__)
            ) . '/assets';
        }
        return self::$baseUrl;
    }

    /**
     * Add a JavaScript file to the document.
     *
     * If $script is a directory, all .js files in it are added.
     * File modification time is appended as a cache buster.
     *
     * @param   string  $script  Relative path under assets/
     * @param   bool    $local   Whether the path is local (true)
     *                           or an external URL (false)
     * @return  void
     */
    public static function dvAddScript($script, $local = true)
    {
        $document = \Hubzero\Facades\App::get('document');

        if (!$local) {
            $document->addScript($script);
            return;
        }

        $basePath = self::getBasePath();
        $baseUrl = self::getBaseUrl();
        $fullPath = $basePath . '/' . ltrim($script, '/');

        if (file_exists($fullPath)) {
            if (is_dir($fullPath)) {
                $files = glob($fullPath . '/*.js');
                if ($files) {
                    sort($files);
                    foreach ($files as $file) {
                        $relPath = ltrim($script, '/') . '/'
                            . basename($file);
                        $document->addScript(
                            $baseUrl . '/' . $relPath
                                . '?mt=' . filemtime($file)
                        );
                    }
                }
            } else {
                $document->addScript(
                    $baseUrl . '/' . ltrim($script, '/')
                        . '?mt=' . filemtime($fullPath)
                );
            }
        }
    }

    /**
     * Add a CSS stylesheet to the document.
     *
     * If $css is a directory, all .css files in it are added.
     *
     * @param   string  $css    Relative path under assets/
     * @param   bool    $local  Whether the path is local
     * @return  void
     */
    public static function dvAddCss($css, $local = true)
    {
        $document = \Hubzero\Facades\App::get('document');

        if (!$local) {
            $document->addStyleSheet($css);
            return;
        }

        $basePath = self::getBasePath();
        $baseUrl = self::getBaseUrl();
        $fullPath = $basePath . '/' . ltrim($css, '/');

        if (file_exists($fullPath)) {
            if (is_dir($fullPath)) {
                $files = glob($fullPath . '/*.css');
                if ($files) {
                    sort($files);
                    foreach ($files as $file) {
                        $relPath = ltrim($css, '/') . '/'
                            . basename($file);
                        $document->addStyleSheet(
                            $baseUrl . '/' . $relPath
                                . '?mt=' . filemtime($file)
                        );
                    }
                }
            } else {
                $document->addStyleSheet(
                    $baseUrl . '/' . ltrim($css, '/')
                        . '?mt=' . filemtime($fullPath)
                );
            }
        }
    }
}
