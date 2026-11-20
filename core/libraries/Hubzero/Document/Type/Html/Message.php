<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type\Html;

use Hubzero\Document\Renderer;
use Hubzero\Facades\App;

/**
 * System message renderer
 *
 * Inspired by Joomla's JDocumentRendererMessage class
 */
class Message extends Renderer
{
    /**
     * Renders the error stack and returns the results as a string
     *
     * @param   string  $name     Not used.
     * @param   array<mixed>   $params   Associative array of values
     * @param   string|null  $content  Not used.
     * @return  string  The output of the script
     */
    public function render($name, $params = [], $content = null)
    {
        // Initialise variables.
        $buffer = [];
        $lists  = [];

        // Get the message queue
        $messages = App::get('notification')->messages();

        // Build the sorted message list
        if (is_array($messages) && !empty($messages)) {
            foreach ($messages as $msg) {
                if (isset($msg['type'], $msg['message'])) {
                    $lists[$msg['type']][] = $msg['message'];
                }
            }
        }

        $lnEnd = $this->doc->getLineEnd();
        $tab   = $this->doc->getTab();

        // Build the return string
        $buffer[] = '<div id="system-message-container">';

        // If messages exist render them
        if (!empty($lists)) {
            $buffer[] = $tab . '<dl id="system-message">';
            foreach ($lists as $type => $msgs) {
                $buffer[] = $tab
                    . $tab
                    . '<dt class="'
                    . strtolower($type)
                    . '">'
                    . App::get('language')->txt($type)
                    . '</dt>';
                $buffer[] = $tab . $tab . '<dd class="' . strtolower($type) . ' message">';
                $buffer[] = $tab . $tab . $tab . '<ul>';
                foreach ($msgs as $msg) {
                    $buffer[] = $tab . $tab . $tab . $tab . '<li>' . $msg . '</li>';
                }
                $buffer[] = $tab . $tab . $tab . '</ul>';
                $buffer[] = $tab . $tab . '</dd>';
            }
            $buffer[] = $tab . '</dl>';
        }

        $buffer[] = '</div>';

        return implode($lnEnd, $buffer);
    }
}
