<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Models;

use Hubzero\Item\Comment as ItemComment;

/**
 * Wishlist class for a wish comment model
 */
class Comment extends ItemComment
{
	/**
	 * Database state constants
	 */
	const STATE_FLAGGED = 3;

	/**
	 * Get the attachments on the wish
	 *
	 * @return  object
	 */
	public function attachments()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Attachment', 'comment_id');
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string $type The type of link to return
	 * @return  string
	 */
	public function link($type='')
	{
		if (!isset($this->_base))
		{
			$this->_base = 'index.php?option=com_wishlist&task=wish&category=' . $this->get('listcategory') . '&rid=' . $this->get('listreference') . '&wishid=' . $this->get('item_id');
		}
		$link = $this->_base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= 'index.php?option=com_wishlist&task=deletereply&replyid=' . $this->get('id');
			break;

			case 'delete':
				$link .= 'index.php?option=com_wishlist&task=deletereply&replyid=' . $this->get('id');
			break;

			case 'reply':
				$link .= 'index.php?option=com_wishlist&task=reply&cat=' . $this->get('listcategory') . '&id=' . $this->get('listid') . '&refid=' . $this->get('item_id') . '&wishid=' . $this->get('item_id');
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=itemcomment&id=' . $this->get('id') . '&parent=' . $this->get('item_id');
			break;

			case 'permalink':
			default:
				$link .= '#c' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Parses content string as directed
	 *
	 * @param   string  $field  The field to parse
	 * @param   string  $as     The format to return state in
	 * @return  string
	 * @since   2.0.0
	 **/
	public function parse($field, $as = 'parsed')
	{
		switch (strtolower($as))
		{
			case 'parsed':
				$property = "_{$field}Parsed";

				if (!isset($this->$property))
				{
					$this->$property = Html::content('prepare', $this->get($field, ''));
				}

				if ($field == 'content')
				{
					require_once __DIR__ . '/attachment.php';

					if (preg_match('/{attachment#([0-9]*)}/sU', $this->$property, $matches))
					{
						foreach ($matches as $i => $match)
						{
							if ($i == 0)
							{
								continue;
							}

							$this->$property = str_replace('{attachment#' . $match . '}', '', $this->$property);

							$id = intval($match);

							$model = Attachment::oneOrNew($id);

							if (!$model->get('id'))
							{
								continue;
							}

							if (!$model->get('comment_id'))
							{
								$model->set('comment_id', $this->get('id'));
								$model->save();
							}
						}
					}
				}

				return $this->$property;
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get($field, ''));
				return preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}
	}

	/**
	 * Validates the set data attributes against the model rules
	 *
	 * @return  bool
	 **/
	public function validate()
	{
		$valid = parent::validate();

		if ($valid)
		{
			$results = \Event::trigger('content.onContentBeforeSave', array(
				'com_wishlist.comment.content',
				&$this,
				$this->isNew()
			));

			foreach ($results as $result)
			{
				if ($result === false)
				{
					$this->addError(Lang::txt('Content failed validation.'));
					$valid = false;
				}
			}
		}

		return $valid;
	}
}
