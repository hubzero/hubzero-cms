<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tags\Helpers;

use stdClass;

class ActivityLogPresenter
{

	public function parse($log)
	{
		$action = $log->get('action');
		$comments = $log->get('comments');
		$timestamp = $log->get('timestamp');
		$activityDescription = null;
		$class = null;

		$actor = stripslashes($log->actor->get('name'));
		$actor = $actor ?: Lang::txt('JUNKNOWN');

		if ($comments)
		{
			$decodedComments = json_decode($comments);

			if (!is_object($decodedComments))
			{
				$decodedComments = new stdClass;
			}

			if (!isset($decodedComments->entries))
			{
				$decodedComments->entries = 0;
			}

			$entriesCount = count($decodedComments->entries);
			$objectId = isset($decodedComments->objectid) ? $decodedComments->objectid : 0;
			$oldId =  isset($decodedComments->old_id) ? $decodedComments->old_id : 0;
			$rawTag = isset($decodedComments->raw_tag) ? $decodedComments->raw_tag : '';
			$table = isset($decodedComments->tbl) ? $decodedComments->tbl : '';
			$tagId = isset($decodedComments->tagid) ? $decodedComments->tagid : 0;

			switch ($action)
			{
				case 'substitute_created':
					$class = 'created';
					$activityDescription = Lang::txt('COM_TAGS_LOG_ALIAS_CREATED', $rawTag, $timestamp, $actor);
				break;

				case 'substitute_edited':
					$class = 'edited';
					$activityDescription = Lang::txt('COM_TAGS_LOG_ALIAS_EDITED', $rawTag, $timestamp, $actor);
				break;

				case 'substitute_deleted':
					$class = 'deleted';
					$activityDescription = Lang::txt('COM_TAGS_LOG_ALIAS_DELETED', $rawTag, $timestamp, $actor);
				break;

				case 'substitute_moved':
					$class = 'moved';
					$activityDescription = Lang::txt('COM_TAGS_LOG_ALIAS_MOVED', $entriesCount, $oldId, $timestamp, $actor);
				break;

				case 'tags_removed':
					$class = 'deleted';
					$activityDescription = Lang::txt('COM_TAGS_LOG_ASSOC_DELETED', $entriesCount, $table, $objectId, $timestamp, $actor);
				break;

				case 'objects_copied':
					$class = 'copied';
					$activityDescription = Lang::txt('COM_TAGS_LOG_ASSOC_COPIED', $entriesCount, $oldId, $timestamp, $actor);
				break;

				case 'objects_moved':
					$class = 'moved';
					$activityDescription = Lang::txt('COM_TAGS_LOG_ASSOC_MOVED', $entriesCount, $oldId, $timestamp, $actor);
				break;

				case 'objects_removed':
					$class = 'deleted';
					if ($objectId || $table)
					{
						$activityDescription = Lang::txt('COM_TAGS_LOG_OBJ_DELETED', $entriesCount, $table, $objectId, $timestamp, $actor);
					}
					else
					{
						$activityDescription = Lang::txt('COM_TAGS_LOG_OBJ_REMOVED', $entriesCount, $tagId, $timestamp, $actor);
					}
				break;

				default:
					$class = 'edited';
					$activityDescription = Lang::txt('COM_TAGS_LOG_TAG_EDITED', str_replace('_', ' ', $action), $timestamp, $actor);
				break;
			}
		}
		else
		{
			$class = 'edited';
			$activityDescription = Lang::txt('COM_TAGS_LOG_TAG_EDITED', str_replace('_', ' ', $action), $timestamp, $actor);
		}

		$parsedLog = new stdClass;
		$parsedLog->class = $class;
		$parsedLog->activityDescription = $activityDescription;

		return $parsedLog;
	}

}
