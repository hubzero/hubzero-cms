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
 * Puts a discussion in the order one reader asked for, and decides how much
 * of each comment they see
 *
 * Both the article page and the standalone discussion page need this, and
 * neither is a sensible place for the other to reach into, so it lives here.
 *
 * None of what comes out of here can be cached as HTML across readers, since
 * half of every score is the reader's own preferences. The rows underneath it
 * can be: one query per discussion, keyed on its last activity.
 */
class Thread
{
	/**
	 * How the reader sees a comment
	 *
	 * A comment below the threshold is never dropped, only folded to a line.
	 * That is deliberate — a filter a reader can see working reads as a
	 * filter; one that silently removes things reads as censorship.
	 */
	const SHOW_FULL      = 'full';
	const SHOW_TRUNCATED = 'truncated';
	const SHOW_STUB      = 'stub';

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
	 * The comments of a discussion, ordered and scored for one reader
	 *
	 * Tree order comes out of the database; `path` exists precisely so that
	 * this is one indexed scan rather than a recursive assembly in PHP. Flat
	 * mode is the only one that reorders, and it does so by id, which for an
	 * append-only table is chronological.
	 *
	 * @param   integer  $discussionId
	 * @param   object   $preference
	 * @param   object   $context
	 * @param   integer  $anchor        the comment the address points at, shown whole
	 * @return  array
	 */
	public static function build($discussionId, $preference, $context = null, $anchor = 0)
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

		$context = $context ?: new Context();

		$threshold = (float) $preference->get('threshold', 0);
		$highlight = (float) $preference->get('highlight_threshold', 4);
		$limit     = (int) $preference->get('comment_limit', 100);
		$spill     = (int) $preference->get('comment_spill', 50);
		$longest   = (int) $preference->get('max_comment_size', 4096);

		// Past the reader's limit, everything folds to a line. The spill is
		// how much further the page is willing to go before it stops being a
		// page at all.
		$ceiling = ($limit > 0) ? $limit + max(0, $spill) : 0;

		$out   = array();
		$shown = 0;

		foreach ($query->rows() as $row)
		{
			$scoring = $row->displayScore($preference, $context);

			$show = self::SHOW_FULL;

			if ($row->isDeleted())
			{
				// A tombstone is already a line. Nothing below applies to it,
				// and it does not spend any of the reader's budget.
				$show = self::SHOW_FULL;
			}
			elseif ($scoring->score < $threshold)
			{
				$show = self::SHOW_STUB;
			}
			elseif ($limit > 0 && $shown >= $limit && (int) $row->get('id') != $anchor)
			{
				$show = self::SHOW_STUB;
			}
			elseif ($longest > 0
				&& strlen($row->get('comment')) > $longest
				&& (int) $row->get('id') != $anchor)
			{
				$show = self::SHOW_TRUNCATED;
				$shown++;
			}
			else
			{
				$shown++;
			}

			$out[] = (object) array(
				'comment'     => $row,
				'indent'      => ($mode == Preference::MODE_THREADED) ? (int) $row->get('depth') : 0,
				'score'       => $scoring->score,
				'base'        => $scoring->base,
				'modifiers'   => $scoring->modifiers,
				'show'        => $show,
				'highlighted' => ($show != self::SHOW_STUB && $scoring->score >= $highlight)
			);

			if ($ceiling > 0 && count($out) >= $ceiling)
			{
				break;
			}
		}

		return $out;
	}

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

	/**
	 * A comment cut to length, on a word boundary
	 *
	 * @param   string   $body
	 * @param   integer  $longest
	 * @return  string
	 */
	public static function shorten($body, $longest)
	{
		if ($longest <= 0 || strlen($body) <= $longest)
		{
			return $body;
		}

		$cut = substr($body, 0, $longest);

		// Back up to whitespace so the cut does not land mid-word.
		if (($space = strrpos($cut, ' ')) !== false && $space > ($longest / 2))
		{
			$cut = substr($cut, 0, $space);
		}

		return rtrim($cut);
	}

	/**
	 * The breakdown, as a reader would read it
	 *
	 * A score somebody cannot account for reads as arbitrary. This is what
	 * turns the number into an explanation.
	 *
	 * @param   object  $entry
	 * @return  string
	 */
	public static function explain($entry)
	{
		// The base is a value, not a movement, so it is shown unsigned; only
		// the modifiers carry a sign.
		$parts = array(\Lang::txt('COM_STORY_SCORE_BASE', self::number($entry->base, false)));

		foreach ($entry->modifiers as $name => $value)
		{
			$parts[] = \Lang::txt('COM_STORY_SCORE_MODIFIER_' . strtoupper($name), self::number($value));
		}

		return implode(', ', $parts);
	}

	/**
	 * A score as a reader should see it, signed and without noise
	 *
	 * @param   float    $value
	 * @param   boolean  $signed  whether a positive value carries a plus
	 * @return  string
	 */
	public static function number($value, $signed = true)
	{
		$value = round((float) $value, 2);

		$out = (floor($value) == $value) ? (string) (int) $value : rtrim(rtrim(number_format($value, 2), '0'), '.');

		return ($signed && $value > 0) ? '+' . $out : $out;
	}
}
