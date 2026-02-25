<?php

namespace Plugins\Blog\Opengraph;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Route;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;
use Hubzero\Facades\Document;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Blog Plugin class for adding Open Graph metadata to the document
 *
 */
class Opengraph extends Plugin
{
    /**
     * Return data on a resource view (this will be some form of HTML)
     *
     * @param   object  $model   Current model
     * @return  void
     */
    public function onBlogView($model)
    {
        if (!App::isSite()) {
            return;
        }

        if (Request::getWord('tmpl') || Request::getWord('format') || Request::getInt('no_html')) {
            return;
        }

        $view = $this->view();

        $title = $view->escape(\Hubzero\Utility\Str::truncate(strip_tags($model->title), 40));
        Document::addCustomTag('<meta property="og:title" content="' . $title . '" />');

        $content = \Hubzero\Utility\Str::truncate(strip_tags($model->content), 300);
        $content = str_replace(array("\n", "\t", "\r"), ' ', $content);
        $content = trim($content);

        Document::addCustomTag('<meta property="og:description" content="' . $view->escape($content) . '" />');

        Document::addCustomTag('<meta property="og:type" content="article" />');

        $url = Route::url($model->link());
        $url = rtrim(Request::root(), '/') . '/' . trim($url, '/');

        Document::addCustomTag('<meta property="og:url" content="' . $url . '" />');
    }
}
