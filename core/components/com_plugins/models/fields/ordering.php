<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;

/**
 * Supports an HTML select list of plugins
 */
class Ordering extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'Ordering';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $element = $this->fieldElement();

        // Initialize variables.
        $html = [];
        $attr = '';

        // Initialize some field attributes.
        $attr .= $element['class'] ? ' class="' . (string) $element['class'] . '"' : '';
        $attr .= ((string) $element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        $attr .= $element['size'] ? ' size="' . (int) $element['size'] . '"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $element['onchange'] ? ' onchange="' . (string) $element['onchange'] . '"' : '';

        // Get some field values from the form.
        $pluginId = (int) $this->form->getValue('extension_id');
        $folder = $this->form->getValue('folder');

        $db = App::get('db');

        // Build the query for the ordering list.
        $query = 'SELECT ordering AS value, name AS text, type AS type, folder AS folder, extension_id AS extension_id
				FROM `#__extensions`
				WHERE (type =' . $db->Quote('plugin') . ' AND folder=' . $db->Quote($folder) . ')
				ORDER BY ordering';

        // Create a read-only list (no name) with a hidden input to store the value.
        if ((string) $element['readonly'] == 'true') {
            $html[] = self::ordering('', $query, trim($attr), $this->value, $pluginId ? 0 : 1);
            $html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '" />';
        } else {
            // Create a regular list.
            $html[] = self::ordering($this->name, $query, trim($attr), $this->value, $pluginId ? 0 : 1);
        }

        return implode($html);
    }

    /**
     * Returns an array of options
     *
     * @param   string   $sql   SQL with 'ordering' AS value and 'name field' AS text
     * @param   int|string $chop  The length of the truncated headline
     * @return  array<int, mixed>    An array of objects formatted for JHtml list processing
     */
    public static function genericordering($sql, $chop = '30')
    {
        $db = App::get('db');
        $options = [];
        $db->setQuery($sql);

        $items = $db->loadObjectList();

        // Check for a database error.

        if (empty($items)) {
            $options[] = Html::select('option', 1, Lang::txt('JOPTION_ORDER_FIRST'));
            return $options;
        }

        $options[] = Html::select('option', 0, '0 ' . Lang::txt('JOPTION_ORDER_FIRST'));
        for ($i = 0, $n = count($items); $i < $n; $i++) {
            $items[$i]->text = Lang::txt($items[$i]->text);
            if (strlen($items[$i]->text) > $chop) {
                $text = substr($items[$i]->text, 0, (int) $chop) . '...';
            } else {
                $text = $items[$i]->text;
            }

            $options[] = Html::select('option', $items[$i]->value, $items[$i]->value . '. ' . $text);
        }
        $lastValue = $items[$i - 1]->value + 1;
        $lastText = $lastValue . ' ' . Lang::txt('JOPTION_ORDER_LAST');
        $options[] = Html::select('option', $lastValue, $lastText);

        return $options;
    }

    /**
     * Build the select list for Ordering derived from a query
     *
     * @param   string   $name      The name of the select list
     * @param   string   $query     The query
     * @param   string|null   $attribs   HTML tag attributes
     * @param   string|null   $selected  The selected item
     * @param   int|null      $neworder  1 if new and first, -1 if new and last, 0  or null if existing item
     * @param   string|null   $chop      The length of the truncated headline
     * @return  string   Html for the select list
     */
    public static function ordering($name, $query, $attribs = null, $selected = null, $neworder = null, $chop = null)
    {
        if (empty($attribs)) {
            $attribs = 'class="inputbox" size="1"';
        }

        if (empty($neworder)) {
            $orders = self::genericordering($query);
            $opts = ['list.attr' => $attribs, 'list.select' => (int) $selected];
            $html = Html::select('genericlist', $orders, $name, $opts);
        } else {
            if ($neworder > 0) {
                $text = Lang::txt('JGLOBAL_NEWITEMSLAST_DESC');
            } else {
                $text = Lang::txt('JGLOBAL_NEWITEMSFIRST_DESC');
            }
            $html = '<input type="hidden" name="' . $name . '" value="' . (int) $selected . '" />'
                . '<span class="readonly">' . $text . '</span>';
        }
        return $html;
    }
}
