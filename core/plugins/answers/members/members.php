<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
