<?php

namespace Plugins\Content\Xhubtags;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Config;
use Hubzero\Facades\Document;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Content Plugin class for {xhub} tags
 *
 */
class Xhubtags extends Plugin
{
    /**
     * Plugin that loads module positions within content
     *
     * @param   string   $context  The context of the content being passed to the plugin.
     * @param   object   $article  The article object.  Note $article->text is also available
     * @param   object   $params   The article params
     * @param   integer  $page     The 'page' number
     * @return  void
     */
    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        if (($article instanceof \Hubzero\Base\Obj) || !in_array($context, ['com_content.article', 'text'])) {
            return;
        }

        // Fix asset paths
        $text = $article->text == null ? '' : $article->text;
        $article->text = str_replace('src="/media/system/', 'src="/core/assets/', $text);
        $appPath = substr(PATH_APP, strlen(PATH_ROOT));
        $article->text = str_replace('src="/site', 'src="' . $appPath . '/site', $article->text);
        $article->text = str_replace("src='/site", "src='" . $appPath . "/site", $article->text);

        // simple performance check to determine whether bot should process further
        if (strpos($article->text, '{xhub') === false) {
            return true;
        }

        // expression to search for
        $regex = "/\{xhub:\s*[^\}]*\}/i";

        // find all instances of plugin and put in $matches
        preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

        if ($matches) {
            foreach ($matches as $match) {
                $regex = "/\{xhub:\s*([^\s]+)\s*(.*)/i";
                if (preg_match($regex, $match[0], $tag)) {
                    switch (strtolower(trim($tag[1]))) {
                        case 'include':
                            $text = $this->xhubInclude($tag[2]);
                            break;

                        case 'image':
                            $text = $this->image($tag[2]);
                            break;

                        case 'module':
                            $text = $this->modules($tag[2]);
                            break;

                        case 'templatedir':
                            $text = $this->templateDir($tag[2]);
                            break;

                        case 'getcfg':
                            $text = $this->getCfg($tag[2]);
                            break;

                        default:
                            $text = '';
                            break;
                    }

                    $article->text = str_replace($match[0], $text, $article->text);
                }
            }
        }
    }

    /**
     * {xhub:module position="position" style="style"}
     * Renders a module from an {xhub} tag
     *
     * @param   string  $options  Tag options (e.g. 'component="support"')
     * @return  string
     */
    private function modules($options)
    {
        $regex = "/position\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

        if (!preg_match($regex, $options, $position)) {
            return '';
        }

        $attribs = array('style' => $this->params->get('style', 'xhtml'));

        $regex = "/style\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

        if (preg_match($regex, $options, $style)) {
            $attribs['style'] = $style[2];

            if ($attribs['style'] == -1 || $attribs['style'] == '-1') {
                $attribs['style'] = 'none';
            }
            if ($attribs['style'] == -2 || $attribs['style'] == '-2') {
                $attribs['style'] = 'xhtml';
            }
        }

        $regex = "/params\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

        if (preg_match($regex, $options, $params)) {
            $attribs['params'] = $params[2];
        }

        return \Hubzero\Facades\Module::position($position[2], $attribs);
    }

    /**
     * {xhub:templatedir}
     *
     * @return  string  Template path
     */
    private function templateDir()
    {
        return substr(App::get('template')->path, strlen(PATH_ROOT));
    }

    /**
     * {xhub:include type="script" component="component" filename="filename"}
     * {xhub:include type="stylesheet" component="component" filename="filename"}
     *
     * @param   string  $options  Tag options (e.g. 'component="support"')
     * @return  string
     */
    private function xhubInclude($options)
    {
        $regex = "/type\s*=\s*(\"|&quot;)(script|stylesheet)(\"|&quot;)/i";

        if (!preg_match($regex, $options, $type)) {
            return '';
        }

        $regex = "/filename\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

        if (!preg_match($regex, $options, $file)) {
            return '';
        }

        $regex = "/component\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

        $template = App::get('template')->template;

        if (
            substr($file[2], 0, strlen('http')) == 'http'
            || substr($file[2], 0, strlen('://')) == '://'
            || substr($file[2], 0, strlen('//')) == '//'
        ) {
            if ($type[2] == 'script') {
                Document::addScript($file[2]);
            } elseif ($type[2] == 'stylesheet') {
                Document::addStyleSheet($file[2], 'text/css', 'screen');
            }

            return '';
        }

        if ($file[2][0] == '/') {
            $filename = $file[2];
        } elseif (preg_match($regex, $options, $component)) {
            $filename = $this->templateDir() . '/html/' . $component[2] . '/' . $file[2]; //'templates/' . $template
            if (!file_exists(PATH_ROOT . $filename)) {
                $filename = substr(Component::path($component[2]), strlen(PATH_ROOT)) . '/' . $file[2];
            }
        } else {
            $filename = $this->templateDir() . '/'; //"/templates/$template/";
            if ($type[2] == 'script') {
                $filename .= 'js/';
            } else {
                $filename .= 'css/';
            }
            $filename .= $file[2];
        }

        if (!file_exists(PATH_ROOT . $filename)) {
            return '';
        }

        $assetUrl = Request::base(true) . '/' . ltrim($filename, '/');
        $assetUrl .= '?v=' . filemtime(PATH_ROOT . $filename);

        if ($type[2] == 'script') {
            Document::addScript($assetUrl);
        } elseif ($type[2] == 'stylesheet') {
            Document::addStyleSheet($assetUrl, 'text/css', 'screen');
        }

        return '';
    }

    /**
     * {xhub:image component="component" filename="filename"}
     *
     * @param   string  $options  Tag options (e.g. 'component="support"')
     * @return  string
     */
    private function image($options)
    {
        $regex = "/filename\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

        if (!preg_match($regex, $options, $file)) {
            return '';
        }

        $regex = "/component\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

        if (!preg_match($regex, $options, $component)) {
            $regex = "/module\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

            preg_match($regex, $options, $module);
        }

        if (empty($component) && empty($module)) {
            return ''; //substr(\Hubzero\Document\Assets::getHubImage($file[2]), 1);
        } elseif (!empty($component)) {
            return substr(\Hubzero\Document\Assets::getComponentImage($component[2], $file[2]), 1);
        } elseif (!empty($module)) {
            return substr(\Hubzero\Document\Assets::getModuleImage($module[2], $file[2]), 1);
        }

        return '';
    }

    /**
     * {xhub:getcfg variable}
     *
     * @param   string  $options  Variable name
     * @return  string
     */
    private function getCfg($options)
    {
        $options = trim($options, " \n\t\r}");

        $sitename = Config::get('sitename');
        $live_site = rtrim(Request::base(), '/');

        if ($options == 'hubShortName') {
            return $sitename;
        } elseif ($options == 'hubShortURL') {
            return $live_site;
        } elseif ($options == 'hubHostname') {
            return Request::getHost();
        }

        return '';
    }
}
