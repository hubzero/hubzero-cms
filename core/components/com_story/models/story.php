<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Hubzero\Utility\Date;

require_once __DIR__ . DS . 'text.php';
require_once __DIR__ . DS . 'section.php';
require_once __DIR__ . DS . 'topic.php';

/**
 * A story
 *
 * The row a front page lists: everything needed to render a headline and its
 * furniture, and nothing that would need the prose loaded.
 */
class Story extends Relational
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
	public $orderBy = 'publish_up';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'alias',
		'publish_up',
		'publish_down'
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
	 * Where a story is in an editor's hands
	 *
	 * Draft and queued are both unpublished; the difference is whether an
	 * editor has finished with it. Queued means finished and waiting for its
	 * hour to come round.
	 */
	const STATE_TRASHED   = -1;
	const STATE_DRAFT     = 0;
	const STATE_QUEUED    = 1;
	const STATE_PUBLISHED = 2;
	const STATE_ARCHIVED  = 3;

	/**
	 * Generates automatic alias value
	 *
	 * @param   array   $data
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && trim($data['alias']) != '') ? $data['alias'] : (isset($data['title']) ? $data['title'] : '');
		$alias = str_replace(' ', '-', strtolower(trim($alias)));

		return preg_replace('/[^a-z0-9\-]/', '', $alias);
	}

	/**
	 * Normalises the hour a story goes out
	 *
	 * An empty value is stored as null rather than an empty string: the column
	 * is nullable and strict SQL modes will not take '' for a datetime. A story
	 * that is published without an hour set goes out now.
	 *
	 * @param   array   $data
	 * @return  mixed
	 */
	public function automaticPublishUp($data)
	{
		$up = isset($data['publish_up']) ? trim($data['publish_up']) : '';

		if ($up == '' || $up == '0000-00-00 00:00:00')
		{
			$state = isset($data['state']) ? (int) $data['state'] : self::STATE_DRAFT;

			return ($state == self::STATE_PUBLISHED) ? Date::of('now')->toSql() : null;
		}

		return $up;
	}

	/**
	 * Normalises the hour a story comes down
	 *
	 * @param   array   $data
	 * @return  mixed
	 */
	public function automaticPublishDown($data)
	{
		$down = isset($data['publish_down']) ? trim($data['publish_down']) : '';

		if ($down == '' || $down == '0000-00-00 00:00:00')
		{
			return null;
		}

		return $down;
	}

	/**
	 * Transform params into a Registry
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		if (!is_object($this->params))
		{
			$this->params = new Registry($this->get('params'));
		}

		return $this->params;
	}

	/**
	 * The story's prose, loaded on demand
	 *
	 * @return  object
	 */
	public function text()
	{
		return Text::oneByStory($this->get('id'));
	}

	/**
	 * Defines a belongs to one relationship with a section
	 *
	 * @return  object
	 */
	public function section()
	{
		return $this->belongsToOne('Components\Story\Models\Section', 'section_id');
	}

	/**
	 * Defines a belongs to one relationship with a topic
	 *
	 * @return  object
	 */
	public function topic()
	{
		return $this->belongsToOne('Components\Story\Models\Topic', 'topic_id');
	}

	/**
	 * Is this story out in the world?
	 *
	 * Published is a state *and* a moment: a story queued for tomorrow is
	 * marked published but is not yet readable, and the front page has to
	 * agree with the archive about which.
	 *
	 * @param   string  $now
	 * @return  bool
	 */
	public function isPublished($now = null)
	{
		if ((int) $this->get('state') !== self::STATE_PUBLISHED)
		{
			return false;
		}

		$now = $now ?: with(new Date('now'))->toSql();
		$up  = $this->get('publish_up');

		if ($up && $up > $now)
		{
			return false;
		}

		$down = $this->get('publish_down');

		if ($down && $down != '0000-00-00 00:00:00' && $down <= $now)
		{
			return false;
		}

		return true;
	}

	/**
	 * Everything published and readable right now
	 *
	 * @param   string  $scope
	 * @param   integer $scopeId
	 * @return  object
	 */
	public static function published($scope = 'site', $scopeId = 0)
	{
		$now = with(new Date('now'))->toSql();

		return self::all()
			->whereEquals('scope', $scope)
			->whereEquals('scope_id', (int) $scopeId)
			->whereEquals('state', self::STATE_PUBLISHED)
			->where('publish_up', '<=', $now)
			->whereRaw(
				'(`publish_down` IS NULL OR `publish_down` = ? OR `publish_down` > ?)',
				array('0000-00-00 00:00:00', $now)
			);
	}

	/**
	 * The day a story went out, for its URL
	 *
	 * @return  array  [year, month, day]
	 */
	public function publishedOn()
	{
		$when = $this->get('publish_up') ?: $this->get('created');

		if (!$when)
		{
			return array('', '', '');
		}

		return array(substr($when, 0, 4), substr($when, 5, 2), substr($when, 8, 2));
	}

	/**
	 * The story's own address
	 *
	 * Dated, because that is what this format is known for and because it
	 * makes an archive fall out of the routing rather than needing its own.
	 *
	 * @return  string
	 */
	public function link()
	{
		list($year, $month, $day) = $this->publishedOn();

		if (!$year)
		{
			return 'index.php?option=com_story&id=' . $this->get('id');
		}

		return 'index.php?option=com_story&year=' . $year . '&month=' . $month
			. '&day=' . $day . '&story=' . $this->get('alias');
	}

	/**
	 * Remove a story and the prose that belongs to it
	 *
	 * @return  bool
	 */
	public function destroy()
	{
		$text = Text::oneByStory($this->get('id'));

		if ($text->get('id'))
		{
			$text->destroy();
		}

		return parent::destroy();
	}
}
