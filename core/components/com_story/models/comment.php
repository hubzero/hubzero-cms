<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\Date;
use Lang;
use User;

require_once __DIR__ . DS . 'discussion.php';

/**
 * One comment in a discussion
 *
 * `parent` is the truth. `path` and `depth` are derived from it, written on
 * insert and rebuildable at any time by rebuildPaths(). Nothing reads them as
 * authority: `path` exists so a discussion can be fetched in tree order in one
 * index scan, and `depth` so the renderer does not have to parse the path.
 */
class Comment extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'story';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'path';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'comment'       => 'notempty',
		'discussion_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'length'
	);

	/**
	 * Where a comment stands
	 *
	 * A deleted comment with replies under it keeps its row as a tombstone.
	 * Removing it outright would orphan the conversation that followed it,
	 * and the thread would read as a non-sequitur.
	 */
	const STATE_DELETED     = -1;
	const STATE_UNPUBLISHED = 0;
	const STATE_PUBLISHED   = 1;

	/**
	 * Digits per ancestor id in a path
	 *
	 * Six gives just under a million comments in one discussion and lets a
	 * varchar(255) path carry thirty-six levels of reply, which is deeper
	 * than any conversation stays readable.
	 *
	 * @var  integer
	 */
	const PATH_WIDTH = 6;

	/**
	 * Render one id as it appears in a path
	 *
	 * Fixed width is what makes a plain string sort match tree order: without
	 * it '10' would sort before '9'.
	 *
	 * @param   integer  $id
	 * @return  string
	 */
	public static function segment($id)
	{
		return str_pad((int) $id, self::PATH_WIDTH, '0', STR_PAD_LEFT);
	}

	/**
	 * The path a comment should have, given its parent's
	 *
	 * @param   string   $parentPath
	 * @param   integer  $id
	 * @return  string
	 */
	public static function pathUnder($parentPath, $id)
	{
		return ($parentPath ? $parentPath . '.' : '') . self::segment($id);
	}

	/**
	 * Body length, kept for the reader length bonuses
	 *
	 * @param   array    $data
	 * @return  integer
	 */
	public function automaticLength($data)
	{
		return isset($data['comment']) ? strlen($data['comment']) : 0;
	}

	/**
	 * Save, then derive the path from the parent it was given
	 *
	 * A new comment's path contains its own id, which does not exist until the
	 * row does, so the path is written immediately afterwards. That is one
	 * extra single-row UPDATE against an indexed primary key, and it shifts
	 * nothing else in the table — which is the point of the whole arrangement.
	 *
	 * @return  boolean
	 */
	public function save()
	{
		$isNew      = $this->isNew();
		$parentPath = '';

		if ($isNew)
		{
			if ($parent = (int) $this->get('parent'))
			{
				$row = self::oneOrNew($parent);

				if (!$row->get('id') || $row->get('discussion_id') != $this->get('discussion_id'))
				{
					$this->addError(Lang::txt('COM_STORY_COMMENT_PARENT_NOT_FOUND'));

					return false;
				}

				$parentPath = $row->get('path');
				$this->set('depth', (int) $row->get('depth') + 1);
			}
			else
			{
				$this->set('depth', 0);
			}

			// A placeholder so the column is never briefly empty for a reader
			// mid-transaction; the real value lands below.
			$this->set('path', self::pathUnder($parentPath, 0));
		}

		if (!parent::save())
		{
			return false;
		}

		if ($isNew)
		{
			$path = self::pathUnder($parentPath, $this->get('id'));

			$query = $this->getQuery()
				->update($this->getTableName())
				->set(array('path' => $path))
				->whereEquals('id', $this->get('id'));

			if (!$query->execute())
			{
				$this->addError($query->getError());

				return false;
			}

			$this->set('path', $path);
			$this->purgeCache();
		}

		return true;
	}

	/**
	 * The rules to hold this row to
	 *
	 * A withdrawn comment legitimately has no body, so the content rule is
	 * dropped once the row is a tombstone. Validating it as if it were still
	 * a comment would make it impossible to withdraw one.
	 *
	 * @return  array
	 */
	public function getRules()
	{
		$rules = parent::getRules();

		if ($this->get('state') == self::STATE_DELETED)
		{
			unset($rules['comment']);
		}

		return $rules;
	}

	/**
	 * Remove a comment without breaking what was said under it
	 *
	 * A leaf goes. Anything with replies is emptied and kept, so its
	 * descendants keep an ancestor to hang from and the paths below it stay
	 * valid. Either way the tree is still exactly what `parent` says it is.
	 *
	 * @return  boolean
	 */
	public function destroy()
	{
		if ($this->descendants()->count())
		{
			$this->set('state', self::STATE_DELETED);
			$this->set('comment', '');
			$this->set('subject', '');
			$this->set('signature', '');
			$this->set('modified', Date::of('now')->toSql());
			$this->set('modified_by', (int) User::get('id'));

			return (bool) parent::save();
		}

		return (bool) parent::destroy();
	}

	/**
	 * Everything filed under this comment
	 *
	 * The trailing dot matters: without it '000012.%' would also match a
	 * sibling whose id happened to start with the same digits.
	 *
	 * @return  object
	 */
	public function descendants()
	{
		return self::all()
			->whereEquals('discussion_id', $this->get('discussion_id'))
			->where('path', 'LIKE', $this->get('path') . '.%');
	}

	/**
	 * Its replies, one level down
	 *
	 * @return  object
	 */
	public function replies()
	{
		return $this->oneToMany(__CLASS__, 'parent');
	}

	/**
	 * Whether it has been emptied rather than removed
	 *
	 * @return  boolean
	 */
	public function isDeleted()
	{
		return ($this->get('state') == self::STATE_DELETED);
	}

	/**
	 * Who to show as the author
	 *
	 * `created_by` is recorded either way — anonymity is a display decision,
	 * not an absence of a record. What it does mean is that the comment sits
	 * outside the karma economy in both directions.
	 *
	 * @return  string
	 */
	public function authorName()
	{
		if ($this->get('anonymous'))
		{
			return Lang::txt('COM_STORY_ANONYMOUS');
		}

		if (!$this->get('created_by'))
		{
			return Lang::txt('COM_STORY_ANONYMOUS');
		}

		$user = User::getInstance($this->get('created_by'));

		return $user && $user->get('id') ? $user->get('name') : Lang::txt('COM_STORY_ANONYMOUS');
	}

	/**
	 * What a comment is worth to one particular reader
	 *
	 * Two readers looking at the same comment can honestly see different
	 * numbers, because half the stack is their own preferences. The breakdown
	 * comes back with the total for exactly that reason: a score a reader
	 * cannot account for reads as arbitrary, and this one is not.
	 *
	 * @param   object  $preference
	 * @param   object  $context
	 * @return  object  the total, the base it started from, and every term
	 */
	public function displayScore($preference, $context)
	{
		// What moderation has made of it, kept apart from what the reader
		// makes of it. Both halves are clamped, so the delta is the movement
		// that actually survived the bounds rather than the raw arithmetic.
		$born  = $context->clamp((float) $this->get('score_original') + (float) $this->get('tweak_original'));
		$now   = $context->clamp((float) $this->get('score') + (float) $this->get('tweak'));
		$base  = $context->clamp($born + ($now - $born));

		$modifiers = array();

		if ((float) $preference->get('bonus_long')
		 && (int) $this->get('length') > (int) $preference->get('length_long'))
		{
			$modifiers['long'] = (float) $preference->get('bonus_long');
		}

		if ((float) $preference->get('bonus_short')
		 && (int) $this->get('length') < (int) $preference->get('length_short'))
		{
			$modifiers['short'] = (float) $preference->get('bonus_short');
		}

		if ($this->get('anonymous') && (float) $preference->get('bonus_anonymous'))
		{
			$modifiers['anonymous'] = (float) $preference->get('bonus_anonymous');
		}

		if ((float) $preference->get('bonus_new_user') && $context->isNewMember($this))
		{
			$modifiers['new_member'] = (float) $preference->get('bonus_new_user');
		}

		if ($this->get('reason_id'))
		{
			$adjustment = (float) $preference->reasonAdjustments->get((string) $this->get('reason_id'), $context->reasonDelta($this->get('reason_id')));

			if ($adjustment)
			{
				$modifiers['reason'] = $adjustment;
			}
		}

		if ($this->get('karma_bonus') && (float) $preference->get('bonus_karma'))
		{
			$modifiers['karma'] = (float) $preference->get('bonus_karma');
		}

		return (object) array(
			'score'     => $context->clamp($base + array_sum($modifiers)),
			'base'      => $base,
			'modifiers' => $modifiers
		);
	}

	/**
	 * What a comment is worth the moment it is written
	 *
	 * Standing earns a comment the benefit of the doubt and the want of it
	 * costs the same. An anonymous comment gets neither: it carries no
	 * identity for standing to attach to, which is the whole reason it sits
	 * outside the karma economy.
	 *
	 * Sets `karma_bonus` as well as the score, because a reader may choose to
	 * adjust for the bonus separately and needs to know it was given.
	 *
	 * @param   object  $config
	 * @param   float   $karma   the author's standing, or null to look it up
	 * @return  $this
	 */
	public function setBirthScore($config, $karma = null)
	{
		if ($this->get('anonymous') || !$this->get('created_by'))
		{
			$score = (float) $config->get('anonymous_default_score', 0);

			$this->set('karma_bonus', 0);
		}
		else
		{
			$score = (float) $config->get('comment_default_score', 1);

			if ($karma === null)
			{
				$karma = self::karmaOf($this->get('created_by'));
			}

			if ($karma !== null && $karma >= (float) $config->get('karma_good', 10))
			{
				$score++;
				$this->set('karma_bonus', 1);
			}
			elseif ($karma !== null && $karma <= (float) $config->get('karma_bad', -5))
			{
				$score--;
				$this->set('karma_bonus', 0);
			}
			else
			{
				$this->set('karma_bonus', 0);
			}
		}

		$floor   = (float) $config->get('comment_min_score', -1);
		$ceiling = (float) $config->get('comment_max_score', 5);
		$score   = max($floor, min($ceiling, $score));

		$this->set('score', $score);
		$this->set('score_original', $score);
		$this->set('score_max', $score);

		return $this;
	}

	/**
	 * An author's standing, or null if karma is not installed
	 *
	 * The component works without the library. A hub that has not turned
	 * karma on simply gets no bonus and no penalty, rather than an error.
	 *
	 * @param   integer  $userId
	 * @return  mixed
	 */
	public static function karmaOf($userId)
	{
		if (!class_exists('\\Hubzero\\Karma\\Karma'))
		{
			return null;
		}

		return (float) \Hubzero\Karma\Karma::of((int) $userId);
	}

	/**
	 * Comments in a discussion, in tree order
	 *
	 * @param   integer  $discussionId
	 * @return  object
	 */
	public static function inDiscussion($discussionId)
	{
		return self::all()
			->whereEquals('discussion_id', (int) $discussionId)
			->order('path', 'asc');
	}

	/**
	 * Put `path` and `depth` back in step with `parent`
	 *
	 * This is the repair that makes storing a derived ordering defensible.
	 * It walks outward from the roots, so a child is always numbered after
	 * its parent, and writes only the rows that were actually wrong. Safe to
	 * run at any time, against any state of the two derived columns.
	 *
	 * A comment whose parent has gone missing is treated as a root rather
	 * than dropped: leaving it unreachable would hide what somebody wrote.
	 *
	 * @param   integer  $discussionId
	 * @return  integer  how many rows had to be corrected
	 */
	public static function rebuildPaths($discussionId)
	{
		$rows = self::all()
			->whereEquals('discussion_id', (int) $discussionId)
			->order('id', 'asc')
			->rows();

		$children = array();
		$known    = array();

		foreach ($rows as $row)
		{
			$known[(int) $row->get('id')] = $row;
		}

		foreach ($rows as $row)
		{
			$parent = (int) $row->get('parent');

			// An orphan hangs from the root rather than disappearing.
			if ($parent && !isset($known[$parent]))
			{
				$parent = 0;
			}

			$children[$parent][] = (int) $row->get('id');
		}

		$corrected = 0;
		$stack     = array();

		if (isset($children[0]))
		{
			foreach (array_reverse($children[0]) as $id)
			{
				$stack[] = array($id, '', 0);
			}
		}

		while ($stack)
		{
			list($id, $parentPath, $depth) = array_pop($stack);

			$row  = $known[$id];
			$path = self::pathUnder($parentPath, $id);

			if ($row->get('path') !== $path || (int) $row->get('depth') !== $depth)
			{
				$row->set('path', $path);
				$row->set('depth', $depth);
				$row->save();

				$corrected++;
			}

			if (isset($children[$id]))
			{
				foreach (array_reverse($children[$id]) as $child)
				{
					$stack[] = array($child, $path, $depth + 1);
				}
			}
		}

		return $corrected;
	}
}
