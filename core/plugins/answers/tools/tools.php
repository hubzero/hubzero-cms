<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Answers plugin for tools
 */
class plgAnswersTools extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Modify or append to query filters
	 *
	 * @param   array  $filters
	 * @return  array
	 */
	public function onQuestionsPrepareFilters($filters)
	{
		if ($filters['area'] == 'assigned')
		{
			require_once Component::path('com_tools') . DS . 'tables' . DS . 'author.php';

			// What tools did this user contribute?
			$db = App::get('db');

			$TA = new Components\Tools\Tables\Author($db);
			$tools = $TA->getToolContributions(User::get('id'));
			$mytooltags = array();
			if ($tools)
			{
				foreach ($tools as $tool)
				{
					$mytooltags[] = 'tool' . $tool->toolname;
				}
			}

			$filters['tag'] .= ($filters['tag'] ? ',' : '') . implode(',', $mytooltags);

			return $filters;
		}
	}

	/**
	 * Return a list of users to be notified of changes
	 *
	 * @param   object  $row
	 * @return  array
	 */
	public function onQuestionNotify($row)
	{
		$tags = $row->tags('string');
		$tags = preg_split("/[,;]/", $tags);

		if (!count($tags))
		{
			return;
		}

		$recipients = array();

		require_once Component::path('com_tools') . DS . 'tables' . DS . 'author.php';
		require_once Component::path('com_tools') . DS . 'tables' . DS . 'version.php';

		$db = App::get('db');
		$TA = new Components\Tools\Tables\Author($db);
		$objV = new Components\Tools\Tables\Version($db);

		foreach ($tags as $tag)
		{
			if ($tag == '')
			{
				continue;
			}

			if (preg_match('/tool:/', $tag))
			{
				$toolname = preg_replace('/tool:/', '', $tag);
				if (trim($toolname))
				{
					$rev = $objV->getCurrentVersionProperty($toolname, 'revision');
					$authors = $TA->getToolAuthors('', 0, $toolname, $rev);
					if (count($authors) > 0)
					{
						foreach ($authors as $author)
						{
							$recipients[] = $author->uidNumber;
						}
					}
				}
			}
		}

		if (count($recipients))
		{
			return $recipients;
		}
	}
}
