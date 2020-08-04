<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Html\Editor as Wysiwyg;
use App;

/**
 * An editarea field for content creation
 */
class Editor extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'Editor';

	/**
	 * The Editor object.
	 *
	 * @var  object
	 */
	protected $editor;

	/**
	 * Method to get the field input markup for the editor area
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$rows = (int) $this->element['rows'];
		$cols = (int) $this->element['cols'];
		$height = ((string) $this->element['height']) ? (string) $this->element['height'] : '250';
		$width = ((string) $this->element['width']) ? (string) $this->element['width'] : '100%';
		$assetField = $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';
		$authorField = $this->element['created_by_field'] ? (string) $this->element['created_by_field'] : 'created_by';
		$asset = $this->form->getValue($assetField) ? $this->form->getValue($assetField) : (string) $this->element['asset_id'];

		// Build the buttons array.
		$buttons = (string) $this->element['buttons'];

		if ($buttons == 'true' || $buttons == 'yes' || $buttons == '1')
		{
			$buttons = true;
		}
		elseif ($buttons == 'false' || $buttons == 'no' || $buttons == '0')
		{
			$buttons = false;
		}
		else
		{
			$buttons = explode(',', $buttons);
		}

		$hide = ((string) $this->element['hide']) ? explode(',', (string) $this->element['hide']) : array();

		// Get an editor object.
		$editor = $this->getEditor();

		return $editor->display(
			$this->name, htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'), $width, $height, $cols, $rows,
			$buttons ? (is_array($buttons) ? array_merge($buttons, $hide) : $hide) : false, $this->id, $asset,
			$this->form->getValue($authorField)
		);
	}

	/**
	 * Method to get a Editor object based on the form field.
	 *
	 * @return  object  The Editor object.
	 */
	protected function getEditor()
	{
		// Only create the editor if it is not already created.
		if (empty($this->editor))
		{
			// Initialize variables.
			$editor = null;

			// Get the editor type attribute. Can be in the form of: editor="desired|alternative".
			$type = trim((string) $this->element['editor']);

			if ($type)
			{
				// Get the list of editor types.
				$types = explode('|', $type);

				// Get the database object.
				$db = App::get('db');

				// Iterate over teh types looking for an existing editor.
				foreach ($types as $element)
				{
					// Build the query.
					$query = $db->getQuery()
						->select('element')
						->from('#__extensions')
						->whereEquals('element', $element)
						->whereEquals('folder', 'editors')
						->whereEquals('enabled', '1')
						->limit(1)
						->start(0);

					// Check of the editor exists.
					$db->setQuery($query->toString());
					$editor = $db->loadResult();

					// If an editor was found stop looking.
					if ($editor)
					{
						break;
					}
				}
			}

			// Create the Editor instance based on the given editor.
			$this->editor = $editor ? Wysiwyg::getInstance($editor) : App::get('editor');
		}

		return $this->editor;
	}

	/**
	 * Method to get the Editor output for an onSave event.
	 *
	 * @return  string  The Editor object output.
	 */
	public function save()
	{
		return $this->getEditor()->save($this->id);
	}
}
