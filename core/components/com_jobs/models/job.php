<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Models;

use Components\Members\Models\Member;
use Hubzero\Base\Model;
use Hubzero\Utility\Str;

require_once dirname(__DIR__). DS . 'tables' . DS . 'job.php';
require_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';

/**
 * Courses model class for a forum
 */
class Job extends Model
{
	/**
	 * Table name
	 *
	 * @var  string
	 */
	protected $_tbl_name = '\\Components\\Jobs\\Tables\\Job';

	/**
	 * Model context
	 *
	 * @var  string
	 */
	protected $_context = 'com_jobs.job.description';

	/**
	 * User
	 *
	 * @var  object
	 */
	private $_creator = null;

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire user object
	 *
	 * @param   string  $property
	 * @return  mixed
	 */
	public function creator($property=null)
	{
		if (!($this->_creator instanceof Member))
		{
			$this->_creator = Member::oneOrNew($this->get('addedBy'));
		}
		if ($property)
		{
			$property = ($property == 'uidNumber') ? 'id' : $property;
			if ($property == 'picture')
			{
				return $this->_creator->picture();
			}
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get('added'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('added'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('added');
			break;
		}
	}

	/**
	 * Get the content of the entry
	 *
	 * @param   string   $as       Format to return state in [text, number]
	 * @param   integer  $shorten  Number of characters to shorten text to
	 * @return  string
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('description.parsed', null);

				if ($content == null)
				{
					$config = array(
						'option'   => 'com_jobs',
						'scope'    => 'job' . DS . $this->get('code'),
						'pagename' => 'jobs',
						'pageid'   => $this->get('code'),
						'filepath' => '',
						'domain'   => ''
					);

					$content = stripslashes($this->get('description'));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('description.parsed', $this->get('description'));
					$this->set('description', $content);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('description'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = Str::truncate($content, $shorten, $options);
		}

		return $content;
	}
}
