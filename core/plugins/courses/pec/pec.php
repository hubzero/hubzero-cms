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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Courses Plugin class for PEC
 */
class plgCoursesPec extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function onOfferingEdit()
	{
		$area = array(
			'name'  => $this->_name,
			'title' => Lang::txt('PLG_COURSES_' . strtoupper($this->_name))
		);
		return $area;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      array   $areas     Active area(s)
	 * @param      string  $rtrn      Data to be returned
	 * @return     array
	 */
	public function onCourseEnrolled($course, $offering, $section)
	{
		if (!$course->exists() || !$offering->exists())
		{
			return;
		}

		$offering->section($section->get('alias'));
		$url = $offering->link() . '&task=enroll';

		if ($offering->params('pec_register', 0) && $offering->params('pec_course', 0))
		{
			$url = str_replace('{{course}}', $offering->params('pec_course', 0), $offering->params('url', 'https://www.distance.purdue.edu/{{course}}'));
		}

		return $url;
	}

	/**
	 * Actions to perform after saving an offering
	 *
	 * @param      object  $model \Components\Courses\Models\Section
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onSectionSave($model, $isNew=false)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after deleting an offering
	 *
	 * @param      object  $model \Components\Courses\Models\Section
	 * @return     void
	 */
	public function onSectionDelete($model)
	{
		if (!$model->exists())
		{
			return;
		}
	}
}
