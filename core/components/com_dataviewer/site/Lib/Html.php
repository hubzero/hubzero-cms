<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Lib;

use Components\Dataviewer\Site\DvConfig;
use Hubzero\Facades\App;

class Html
{
    public static function dvAddScript($script, $local = true)
    {
        $document = App::get('document');

        if (!$local) {
            $document->addScript($script);
            return;
        }

        if (file_exists(DvConfig::$html_dir . DS . $script)) {
            if (is_dir(DvConfig::$html_dir . DS . $script)) {
                $path = $script;
                chdir(DvConfig::$html_dir . DS . $path);
                foreach (glob("*.js") as $file) {
                    $document->addScript(
                        DvConfig::$html_path . '/' . $path . '/' . $file . '?mt='
                        . filemtime(DvConfig::$html_dir . DS . $path . DS . $file),
                    );
                }
            } else {
                $document->addScript(
                    DvConfig::$html_path . '/' . $script . '?mt='
                    . filemtime(DvConfig::$html_dir . DS . $script),
                );
            }
        }
    }

    public static function dvAddCss($css, $local = true)
    {
        $document = App::get('document');

        if (!$local) {
            $document->addStyleSheet($css);
            return;
        }

        if (file_exists(DvConfig::$html_dir . DS . $css)) {
            if (is_dir(DvConfig::$html_dir . DS . $css)) {
                $path = $css;
                chdir(DvConfig::$html_dir . DS . $path);
                foreach (glob("*.css") as $file) {
                    $document->addStyleSheet(
                        DvConfig::$html_path . '/' . $path . '/' . $file . '?mt='
                        . filemtime(DvConfig::$html_dir . DS . $path . DS . $file),
                    );
                }
            } else {
                $document->addStyleSheet(
                    DvConfig::$html_path . '/' . $css . '?mt='
                    . filemtime(DvConfig::$html_dir . DS . $css),
                );
            }
        }
    }
}
