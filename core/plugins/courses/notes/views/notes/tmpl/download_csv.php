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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$results = array();

$entries = \Plugins\Courses\Notes\Models\Note::all()
	->whereEquals('section_id', $this->offering->section()->get('id'))
	->whereEquals('created_by', User::get('id'))
	->whereEquals('state', 1);

if ($this->filters['search'])
{
	$entries->whereLike('content', $this->filters['search']);
}
$notes = $entries->rows();

if ($notes->count())
{
	foreach ($notes as $note)
	{
		$ky = $note->get('scope_id');
		if (!isset($results[$ky]))
		{
			$results[$ky] = array();
		}
		$results[$ky][] = $note;
	}
}

$base = $this->offering->link();

if (count($results))
{
	foreach ($results as $id => $notes)
	{
		$lecture = new \Components\Courses\Models\Assetgroup($id);
		$unit = \Components\Courses\Models\Unit::getInstance($lecture->get('unit_id'));

		echo $this->escape(stripslashes($lecture->get('title'))) . "\n";
		echo '--------------------------------------------------' . "\n";

		foreach ($notes as $note)
		{
			echo '#' . $note->get('id');

			if ($note->get('timestamp') != '00:00:00')
			{
				echo ' video time: ' . $this->escape($note->get('timestamp'));
			}
			echo "\n";
			echo $this->escape(stripslashes($note->get('content')));
			echo "\n";
		}

		echo "\n";
	}
}
