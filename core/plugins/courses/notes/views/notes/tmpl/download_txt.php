<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
			echo "\n\n";
		}

		echo "\n";
	}
}
