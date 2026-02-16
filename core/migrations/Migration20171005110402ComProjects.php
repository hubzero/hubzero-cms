<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for moving projects activity to the global activity tables
 *
 */
class Migration20171005110402ComProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $newids = array();
        $blgids = array();
        $tdoids = array();

        if (
            $schema->tableExists('#__activity_logs')
            && $schema->tableExists('#__activity_recipients')
            && $schema->tableExists('#__project_activity')
        ) {
            $activities = $this->db->getQuery(true)
                ->select('*')
                ->from('#__project_activity')
                ->order('id', 'ASC')
                ->loadObjectList();

            foreach ($activities as $activity) {
                $action = 'created';
                $scope = 'project';
                $scope_id = $activity->projectid;
                $parent = 0;
                $blog = false;
                $todo = false;

                switch ($activity->activity) {
                    case 'started the project':
                        $action = 'created';
                        $scope = 'project';
                        $scope_id = $activity->projectid;
                        break;

                    case 'deleted project':
                        $action = 'deleted';
                        $scope = 'project';
                        $scope_id = $activity->projectid;
                        break;

                    case 'joined the project':
                        $action = 'joined';
                        $scope = 'project';
                        $scope_id = $activity->projectid;
                        break;

                    case 'left the project':
                        $action = 'cancelled';
                        $scope = 'project';
                        $scope_id = $activity->projectid;
                        break;

                    case 'posted a to-do item':
                    case 'posted a to do item':
                        $action = 'created';
                        $scope = 'project.todo';
                        $scope_id = $activity->referenceid;
                        $todo = true;
                        break;

                    case 'checked off a to-do item':
                    case 'checked off a to do item':
                        $action = 'updated';
                        $scope = 'project.todo';
                        $scope_id = $activity->referenceid;
                        $todo = true;
                        break;

                    case 'said':
                        $action = 'created';
                        $scope = 'project.comment';
                        $scope_id = $activity->projectid;
                        $blog = true;
                        if ($schema->tableExists('#__project_microblog')) {
                            $comment = $this->db->getQuery(true)
                                ->select('*')
                                ->from('#__project_microblog')
                                ->where('id', '=', $activity->referenceid)
                                ->first();
                            if ($comment) {
                                $activity->activity = $comment->blogentry;
                            }
                        }
                        break;

                    case 'commented on a to do item':
                    case 'commented on a to-do item':
                        $action = 'created';
                        $scope = 'project.comment';
                        $scope_id = $activity->referenceid;

                        if ($schema->tableExists('#__project_comments')) {
                            $comment = $this->db->getQuery(true)
                                ->select('*')
                                ->from('#__project_comments')
                                ->where('id', '=', $activity->referenceid)
                                ->first();
                            if ($comment) {
                                $parent = (isset($tdoids[$comment->itemid]) ? $tdoids[$comment->itemid] : 0);
                                $activity->activity = $comment->comment;
                            }
                        }
                        break;

                    case 'commented on a blog post':
                        $action = 'created';
                        $scope = 'project.comment';
                        $scope_id = $activity->projectid;

                        if ($schema->tableExists('#__project_comments')) {
                            $comment = $this->db->getQuery(true)
                                ->select('*')
                                ->from('#__project_comments')
                                ->where('id', '=', $activity->referenceid)
                                ->first();
                            if ($comment) {
                                $parent = (isset($blgids[$comment->itemid]) ? $blgids[$comment->itemid] : 0);
                                $activity->activity = $comment->comment;
                            }
                        }
                        break;

                    case 'commented on an activity':
                        $action = 'created';
                        $scope = 'project.comment';
                        $scope_id = $activity->projectid;

                        if ($schema->tableExists('#__project_comments')) {
                            $comment = $this->db->getQuery(true)
                                ->select('*')
                                ->from('#__project_comments')
                                ->where('id', '=', $activity->referenceid)
                                ->first();
                            if ($comment) {
                                $parent = (isset($newids[$comment->itemid]) ? $newids[$comment->itemid] : 0);
                                $activity->activity = $comment->comment;
                            }
                        }
                        break;

                    case 'added a new page in project notes':
                        $action = 'created';
                        $scope = 'project.note';
                        $scope_id = $activity->referenceid;
                        break;

                    case 'changed the project settings':
                    case 'edited project information':
                    case 'replaced project picture':
                        $action = 'updated';
                        $scope = 'project';
                        $scope_id = $activity->projectid;
                        break;

                    default:
                        if (substr($activity->activity, 0, strlen('uploaded')) == 'uploaded') {
                            $action = 'uploaded';
                            $scope = 'project.file';
                            $scope_id = $activity->projectid;
                        }
                        if (substr($activity->activity, 0, strlen('updated file')) == 'updated file') {
                            $action = 'updated';
                            $scope = 'project.file';
                            $scope_id = $activity->projectid;
                        }
                        $restoredStr = 'restored deleted file';
                        if (substr($activity->activity, 0, strlen($restoredStr)) == $restoredStr) {
                            $action = 'updated';
                            $scope = 'project.file';
                            $scope_id = $activity->projectid;
                        }
                        if (substr($activity->activity, 0, strlen('created database')) == 'created database') {
                            $action = 'created';
                            $scope = 'project.database';
                            $scope_id = $activity->referenceid;
                        }
                        if (substr($activity->activity, 0, strlen('removed database')) == 'removed database') {
                            $action = 'deleted';
                            $scope = 'project.database';
                            $scope_id = $activity->referenceid;
                        }
                        if (substr($activity->activity, 0, strlen('updated database')) == 'updated database') {
                            $action = 'updated';
                            $scope = 'project.database';
                            $scope_id = $activity->referenceid;
                        }
                        $pubStr = 'started a new publication';
                        $draftStr = 'started draft';
                        if (
                            substr($activity->activity, 0, strlen($pubStr)) == $pubStr
                            || substr($activity->activity, 0, strlen($draftStr)) == $draftStr
                        ) {
                            $action = 'created';
                            $scope = 'publication';
                            $scope_id = $activity->referenceid;
                        }
                        if (substr($activity->activity, 0, strlen('started new version')) == 'started new version') {
                            $action = 'created';
                            $scope = 'publication';
                            $scope_id = $activity->referenceid;
                        }
                        if (
                            substr($activity->activity, 0, strlen('published version')) == 'published version'
                            || substr($activity->activity, 0, strlen('re-published version')) == 're-published version'
                        ) {
                            $action = 'published';
                            $scope = 'publication';
                            $scope_id = $activity->referenceid;
                        }
                        if (
                            substr($activity->activity, 0, strlen('submitted draft')) == 'submitted draft'
                            || substr($activity->activity, 0, strlen('re-submitted draft')) == 're-submitted draft'
                        ) {
                            $action = 'submitted';
                            $scope = 'publication';
                            $scope_id = $activity->referenceid;
                        }
                        if (substr($activity->activity, 0, strlen('deleted draft')) == 'deleted draft') {
                            $action = 'deleted';
                            $scope = 'publication';
                            $scope_id = $activity->referenceid;
                        }
                        if (substr($activity->activity, 0, strlen('reviewed')) == 'reviewed') {
                            $action = 'reviewed';
                            $scope = 'publication';
                            $scope_id = $activity->referenceid;
                        }
                        if (substr($activity->activity, 0, strlen('approved')) == 'approved') {
                            $action = 'approved';
                            $scope = 'publication';
                            $scope_id = $activity->referenceid;
                        }
                        if (substr($activity->activity, 0, strlen('reverted to draft')) == 'reverted to draft') {
                            $action = 'reverted';
                            $scope = 'publication';
                            $scope_id = $activity->referenceid;
                        }
                        if (substr($activity->activity, 0, strlen('unpublished')) == 'unpublished') {
                            $action = 'unpublished';
                            $scope = 'publication';
                        }
                        if (substr($activity->activity, 0, strlen('edited page')) == 'edited page') {
                            $action = 'updated';
                            $scope = 'project.note';
                            $scope_id = $activity->referenceid;
                        }
                        break;
                }

                $this->db->getQuery(true)
                    ->insert('#__activity_logs')
                    ->set([
                        'created'     => $activity->recorded,
                        'created_by'  => $activity->userid,
                        'description' => $activity->activity,
                        'action'      => $action,
                        'scope'       => $scope,
                        'scope_id'    => $scope_id,
                        'details'     => json_encode($activity),
                        'anonymous'   => 0,
                        'parent'      => $parent
                    ])
                    ->execute();

                $newids[$activity->id] = $this->db->insertid();
                if ($blog) {
                    $blgids[$activity->referenceid] = $newids[$activity->id];
                }
                if ($todo) {
                    $tdoids[$activity->referenceid] = $newids[$activity->id];
                }

                // Add to the project's feed
                $this->db->getQuery(true)
                    ->insert('#__activity_recipients')
                    ->set([
                        'log_id'   => $newids[$activity->id],
                        'scope'    => 'project',
                        'scope_id' => $activity->projectid,
                        'created'  => $activity->recorded,
                        'viewed'   => $activity->recorded,
                        'state'    => ($activity->state == 2 ? $activity->state : 1),
                        'starred'  => 0
                    ])
                    ->execute();

                // We have a child comment
                // So, we want to force the parent to show up more recent in the list
                // to reflect the new comment.
                if ($parent && $activity->state != 2) {
                    // Unset the parent's recipient record
                    $this->db->getQuery(true)
                        ->update('#__activity_recipients')
                        ->set(['state' => 0])
                        ->where('state', '=', 1)
                        ->where('log_id', '=', $parent)
                        ->where('scope', '=', 'project')
                        ->where('scope_id', '=', $activity->projectid)
                        ->execute();

                    // And add a new recipient record with an updated timestamp
                    $this->db->getQuery(true)
                        ->insert('#__activity_recipients')
                        ->set([
                            'log_id'   => $parent,
                            'scope'    => 'project',
                            'scope_id' => $activity->projectid,
                            'created'  => $activity->recorded,
                            'viewed'   => $activity->recorded,
                            'state'    => 1,
                            'starred'  => 0
                        ])
                        ->execute();
                }
            }

            //$this->db->schema()->dropTable('#__project_activity');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // Transfer articles
        if ($schema->tableExists('#__activity_logs')) {
            if (!$schema->tableExists('#__project_activity')) {
                $schema->createTable('#__project_activity')
                    ->id()
                    ->integer('projectid')->default(0)
                    ->integer('userid')->default(0)
                    ->string('referenceid', 255)->default('0')
                    ->tinyInteger('managers_only')->default(0)
                    ->tinyInteger('admin')->default(0)
                    ->tinyInteger('commentable')->default(0)
                    ->tinyInteger('state')->default(0)
                    ->datetime('recorded')->default('0000-00-00 00:00:00')
                    ->string('activity', 255)->default('')
                    ->string('highlighted', 100)->default('')
                    ->string('url', 255)->nullable()
                    ->string('class', 150)->nullable()
                    ->mediumText('preview')->nullable()
                    ->index('idx_projectid', 'projectid')
                    ->index('idx_state', 'state')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }

            $activities = $this->db->getQuery(true)
                ->select('*')
                ->from('#__activity_logs')
                ->whereIn('scope', [
                    'project',
                    'project.note',
                    'project.todo',
                    'publication',
                    'project.comment',
                    'project.file',
                    'project.todo.comment',
                    'project.database',
                ])
                ->loadObjectList();

            foreach ($activities as $activity) {
                if ($activity->details) {
                    $details = json_decode($activity->details);
                }

                if (isset($details)) {
                    $this->db->getQuery(true)
                        ->insert('#__project_activity')
                        ->set([
                            'projectid'     => $details->projectid,
                            'userid'        => $details->userid,
                            'referenceid'   => $details->referenceid,
                            'managers_only' => $details->managers_only,
                            'admin'         => $details->admin,
                            'commentable'   => $details->commentable,
                            'state'         => $details->state,
                            'recorded'      => $details->recorded,
                            'activity'      => $details->activity,
                            'highlighted'   => $details->highlighted,
                            'url'           => $details->url,
                            'class'         => $details->class,
                            'preview'       => $details->preview
                        ])
                        ->execute();
                }
            }

            if ($schema->tableExists('#__activity_recipients')) {
                $this->db->getQuery(true)
                    ->delete('#__activity_recipients')
                    ->whereIn('scope', ['project', 'project_managers'])
                    ->execute();
            }
        }
    }
}
