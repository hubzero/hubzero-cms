<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Search;

use Hubzero\Module\Module;
use Hubzero\Facades\Document;
use Hubzero\Facades\Request;
use Hubzero\Facades\Config;
use Hubzero\Facades\Route;
use Hubzero\Facades\Lang;

/**
 * Module class for displaying a search form
 */
class Search extends Module
{
    /**
     * Number of instances of the module
     *
     * @var  integer
     */
    public static $instances = 0;

    /**
     * Display the search form
     *
     * @return  void
     */
    public function display()
    {
        self::$instances++;

        if ($this->params->get('opensearch', 0)) {
            $defaultTitle = Lang::txt('MOD_SEARCH_SEARCHBUTTON_TEXT') . ' ' . Config::get('sitename');
            $ostitle = $this->params->get('opensearch_title', $defaultTitle);

            Document::addHeadLink(
                Request::base() . Route::url('&option=com_search&format=opensearch'),
                'search',
                'rel',
                array('title' => htmlspecialchars($ostitle), 'type' => 'application/opensearchdescription+xml')
            );
        }

        //$upper_limit = Lang::getUpperLimitSearchWord();
        //$maxlength   = $upper_limit;

        $params          = $this->params;
        $button          = $this->params->get('button', '');
        $button_pos      = $this->params->get('button_pos', 'right');
        $defaultBtnText = Lang::txt('MOD_SEARCH_SEARCHBUTTON_TEXT');
        $button_text = htmlspecialchars($this->params->get('button_text', $defaultBtnText));
        $width = intval($this->params->get('width', 20));
        $defaultText = Lang::txt('MOD_SEARCH_SEARCHBOX_TEXT');
        $text = htmlspecialchars($this->params->get('text', $defaultText));
        $label           = htmlspecialchars($this->params->get('label', Lang::txt('MOD_SEARCH_LABEL_TEXT')));
        $moduleclass_sfx = htmlspecialchars($this->params->get('moduleclass_sfx', ''));

        require $this->getLayoutPath($this->params->get('layout', 'default'));
    }
}
