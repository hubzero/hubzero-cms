<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Poll\Elements;

// No direct access
defined('_HZEXEC_') or die();

/**
 * JElementPoll class
 */
class JElementPoll extends \JElement
{
    /**
     * Element name
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_name = 'Poll';

    /**
     * Retrieve element
     *
     * @param  string   $name
     * @param  unknown  $value
     * @param  unknown  &$node
     * @param  string   $control_name
     */
    public function fetchElement($name, $value, &$node, $control_name)
    {
        require_once dirname(__DIR__) . DS . 'models' . DS . 'poll.php';

        $options = \Components\Poll\Models\Poll::all()
            ->whereEquals('published', 1)
            ->rows()
            ->raw();

        array_unshift(
            $options,
            \Html::select('option', '0', '- ' . \Lang::txt('Select Poll') . ' -', 'id', 'title')
        );

        $fieldName = $control_name . '[' . $name . ']';
        return \Html::select(
            'genericlist',
            $options,
            $fieldName,
            'class="inputbox"',
            'id',
            'title',
            $value,
            $control_name . $name
        );
    }
}
