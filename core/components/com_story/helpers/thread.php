<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Helpers;

use Components\Story\Models\Comment;
use Components\Story\Models\Preference;

/**
 * Puts a discussion in the order one reader asked for
 *
 * Both the article page and the standalone discussion page need this, and
 * neither is a sensible place for the other to reach into, so it lives here.
 *
 * Ordering only. What a comment is *worth* — the score, the thresholds, the
 * modifier stack — is phase 8, and arrives as a filter applied to what this
 * returns rather than as a change to it.
 */
class Thread
{
	/**
	 * The comments of a discussion, ordered for a reader
	 *
	 * Tree order comes out of the database; `path` exists precisely so that
	 * this is one indexed scan rather than a recursive assembly in PHP. Flat
	 * mode is the only one that reorders, and it does so by id, which for an
	 * append-only table is chronological.
	 *
	 * @param   integer  $discussionId
	 * @param   object   $preference
	 * @return  array
	 */
	public static function build($discussionId, $preference)
	{
		$mode = $preference->get('mode', Preference::MODE_THREADED);

		if ($mode == Preference::MODE_NONE)
		{
			return array();
		}

		if ($mode == Preference::MODE_FLAT)
		{
			// Built from scratch rather than layered onto inDiscussion():
			// adding a second ordering would leave the path ordering in
			// charge and flat mode would silently stay in tree order.
			$query = Comment::all()
				->whereEquals('discussion_id', (int) $discussionId)
				->order('id', $preference->get('sort') == Preference::SORT_NEWEST ? 'desc' : 'asc');
		}
		else
		{
			$query = Comment::inDiscussion($discussionId);
		}

		$rows = $query->rows();

		$out = array();

		foreach ($rows as $row)
		{
			// Only threaded mode indents. Nested and flat render every comment
			// at the margin — nested keeping tree order, flat abandoning it.
			$out[] = (object) array(
				'comment' => $row,
				'indent'  => ($mode == Preference::MODE_THREADED) ? (int) $row->get('depth') : 0
			);
		}

		return $out;
	}

	/**
	 * How deep the renderer is willing to indent
	 *
	 * Past this the indent stops growing while the tree carries on. A reply
	 * forty levels down is still in the right place; it just stops walking
	 * off the right-hand edge of the page.
	 *
	 * @var  integer
	 */
	const MAX_INDENT = 10;

	/**
	 * The indent to actually apply
	 *
	 * @param   integer  $depth
	 * @return  integer
	 */
	public static function indent($depth)
	{
		return min((int) $depth, self::MAX_INDENT);
	}
}
