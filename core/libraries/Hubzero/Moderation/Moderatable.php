<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation;

/**
 * Something that can be moderated
 *
 * The library knows nothing about comments, posts or stories. A component
 * registers an item type and supplies an adapter answering these six
 * questions, and gets the whole economy for free.
 */
interface Moderatable
{
	/**
	 * The item type this adapter speaks for, as in com_forum.post
	 *
	 * @return  string
	 */
	public function itemType();

	/**
	 * The item's own id
	 *
	 * @return  integer
	 */
	public function itemId();

	/**
	 * The item's score as it currently stands
	 *
	 * @return  float
	 */
	public function currentScore();

	/**
	 * The lowest and highest score this item may hold
	 *
	 * @return  array  [min, max]
	 */
	public function scoreBounds();

	/**
	 * Move the item's score
	 *
	 * Called inside the moderation transaction. Returning false aborts the
	 * whole act, including the credit spend.
	 *
	 * @param   float    $delta     Signed amount
	 * @param   integer  $reasonId  Which reason was given
	 * @return  bool
	 */
	public function applyScore($delta, $reasonId);

	/**
	 * Who wrote it, or zero where that should not earn or lose karma
	 *
	 * An adapter returns zero for anonymous content: anonymity is a promise
	 * about display, and karma is visible standing.
	 *
	 * @return  integer
	 */
	public function authorId();

	/**
	 * What the item sits inside — a thread, a discussion, a question
	 *
	 * Moderating inside a container bars you from contributing to it, and
	 * contributing undoes your moderations there. That rule needs a notion
	 * of "here", and this is it.
	 *
	 * @return  integer
	 */
	public function containerId();
}
