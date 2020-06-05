<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Answers plugin for members
 */
class plgAnswersMembers extends \Hubzero\Plugin\Plugin
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
		if ($filters['area'] == 'interest')
		{
			require_once Component::path('com_members') . DS . 'models' . DS . 'tags.php';

			// Get tags of interest
			$mt = new Components\Members\Models\Tags(User::get('id'));

			$filters['tag'] .= ($filters['tag'] ? ',' : '') . $mt->render('string');

			return $filters;
		}
	}

	/**
	 * Return a list of filters that can be applied
	 *
	 * @return  array
	 */
	public function onQuestionsFilters()
	{
		return array(
			'name'  => 'area',
			'value' => 'interest',
			'label' => Lang::txt('COM_ANSWERS_QUESTIONS_TAGGED_WITH_MY_INTERESTS')
		);
	}
}
