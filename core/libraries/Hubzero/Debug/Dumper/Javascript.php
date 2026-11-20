<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug\Dumper;

/**
 * Javascript renderer
 */
class Javascript extends AbstractRenderer
{
    /**
     * Returns renderer name
     *
     * @return  string
     */
    public function getName()
    {
        return 'javascript';
    }

    /**
     * Render a list of messages
     *
     * @param   array<mixed>|null  $messages
     * @return  string
     */
    public function render($messages = null)
    {
        if ($messages) {
            $this->setMessages($messages);
        }

        $messages = $this->getMessages();

        $output = [];
        $output[] = '<script type="text/javascript">';
        $output[] = 'if (!window.console) console = {};';
        $output[] = 'console.log   = console.log   || function(){};';
        $output[] = 'console.warn  = console.warn  || function(){};';
        $output[] = 'console.error = console.error || function(){};';
        $output[] = 'console.info  = console.info  || function(){};';
        $output[] = 'console.debug = console.debug || function(){};';
        foreach ($messages as $item) {
            // A list stays a list after deflating; print it rather than fail on it
            $text = $this->deflate($item['message']);
            $text = is_string($text) ? $text : print_r($text, true);
            $output[] = 'console.' . $item['label'] . '("' . addslashes($text) . '");';
        }
        $output[] = '</script>';
        return implode("\n", $output);
    }
}
