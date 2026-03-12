<?php

namespace Plugins\EditorsXtd\Readmore;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\Document;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Editor Readmore buton
 *
 */
class Readmore extends Plugin
{
    /**
     * Constructor
     *
     * @param       object  $subject The object to observe
     * @param       array   $config  An array that holds the plugin configuration
     * @since       1.5
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        $this->loadLanguage();
    }

    /**
     * readmore button
     *
     * @param  string  $name  Value of name
     * @return  array  A two element array of (imageName, textToInsert)
     */
    public function onDisplay($name)
    {
        $template = App::get('template')->template;

        // button is not active in specific content components
        $getContent = $this->_subject->getContent($name);
        $present = Lang::txt('PLG_READMORE_ALREADY_EXISTS', true);
        $js = "
			function insertReadmore(editor) {
				var content = $getContent
				if (content.match(/<hr\s+id=(\"|')system-readmore(\"|')\s*\/*>/i)) {
					alert('$present');
					return false;
				} else {
					jInsertEditorText('<hr id=\"system-readmore\" />', editor);
				}
			}
			";

        $isDaisyUi = Document::getCssFramework() === 'daisyui';

        $button = new \Hubzero\Base\Obj();
        $button->set('modal', false);
        $button->set('text', Lang::txt('PLG_READMORE_BUTTON_READMORE'));
        $button->set('name', 'readmore');
        $button->set('link', '#');

        if ($isDaisyUi) {
            // Blade mode: admin.js handles via data-action delegation
            $button->set('onclick', '');
            $button->set('data-action', 'insertReadmore');
            $button->set('data-alert-exists', Lang::txt('PLG_READMORE_ALREADY_EXISTS'));
        } else {
            // Legacy mode: inline script + onclick handler
            Document::addScriptDeclaration($js);
            $button->set('onclick', 'insertReadmore(\'' . $name . '\');return false;');
        }

        return $button;
    }
}
