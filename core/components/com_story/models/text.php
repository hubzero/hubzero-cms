<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Models;

use Hubzero\Database\Relational;

/**
 * A story's prose
 *
 * Kept apart from the story itself because a front page lists thirty stories
 * and needs none of their bodies, and the body is the largest column here.
 */
class Text extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'story';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'story_id' => 'positive|nonzero'
	);

	/**
	 * Load the text for a story, or a blank one
	 *
	 * @param   integer  $storyId
	 * @return  object
	 */
	public static function oneByStory($storyId)
	{
		$text = self::all()->whereEquals('story_id', (int) $storyId)->row();

		if (!$text->get('id'))
		{
			$text->set('story_id', (int) $storyId);
		}

		return $text;
	}

	/**
	 * Keep the derived counts honest on the way in
	 *
	 * They exist so that a listing can say how long a piece is without
	 * loading it, which is the whole reason this table is separate.
	 *
	 * @return  bool
	 */
	public function save()
	{
		$body = (string) $this->get('intro') . ' ' . (string) $this->get('body');

		$this->set('word_count', str_word_count(strip_tags($body)));
		$this->set('body_length', strlen((string) $this->get('body')));

		return parent::save();
	}

	/**
	 * Defines a belongs to one relationship with a story
	 *
	 * @return  object
	 */
	public function story()
	{
		return $this->belongsToOne('Components\Story\Models\Story', 'story_id');
	}
}
