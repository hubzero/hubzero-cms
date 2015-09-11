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
 * @author    Hubzero
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

include_once(__DIR__ . DS . 'Sku.php');

class StorefrontModelCourseOffering extends StorefrontModelSku
{
	public function __construct()
	{
		parent::__construct();

		//$this->setAllowMultiple(0);
		$this->setTrackInventory(0);
	}

	public function setCourseId($courseId)
	{
		$this->data->courseId = $courseId;
		$this->data->meta['courseId'] = $courseId;
	}

	public function setOfferingId($offeringId)
	{
		$this->data->offeringId = $offeringId;
		$this->data->meta['offeringId'] = $offeringId;
	}

	public function getCourseId()
	{
		return $this->data->meta['courseId'];
	}

	public function verify()
	{
		parent::verify();

		// Each course has to have a course ID
		if (empty($this->data->courseId))
		{
			throw new Exception(Lang::txt('No course id'));
		}
	}
}