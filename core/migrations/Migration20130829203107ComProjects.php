<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting up projects
 *
*/
class Migration20130829203107ComProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__projects')) {
            $schema->createTable('#__projects')
                ->integer('id', ['autoIncrement' => true])
                ->string('alias', 30)->default('')
                ->string('title', 255)->default('')
                ->string('picture', 255)->default('')
                ->text('about')->nullable()
                ->integer('state')->default(0)
                ->integer('type')->default(1)
                ->integer('provisioned')->default(0)
                ->integer('private')->default(1)
                ->datetime('created')
                ->datetime('modified')->nullable()
                ->integer('owned_by_user')->default(0)
                ->integer('created_by_user')
                ->integer('owned_by_group')->default(0)
                ->integer('modified_by')->default(0)
                ->integer('setup_stage')->default(0)
                ->text('params')->nullable()
                ->text('admin_notes')->nullable()
                ->primaryKey('id')
                ->uniqueIndex('alias', 'alias')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            // Make entries to enable HUB messaging
            $this->db->getQuery(true)
                ->insert('#__xmessage_component')
                ->values([
                    'component' => 'com_projects',
                    'action'    => 'projects_member_added',
                    'title'     => 'You were added or invited to a project'
                ])
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__xmessage_component')
                ->values([
                    'component' => 'com_projects',
                    'action'    => 'projects_new_project_admin',
                    'title'     => 'Receive notifications about project(s) you monitor as an admin or reviewer'
                ])
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__xmessage_component')
                ->values([
                    'component' => 'com_projects',
                    'action'    => 'projects_admin_message',
                    'title'     => 'Receive administrative messages about your project(s)'
                ])
                ->execute();
        }

        if (!$schema->tableExists('#__project_activity')) {
            $schema->createTable('#__project_activity')
                ->integer('id', ['autoIncrement' => true])
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
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__project_comments')) {
            $schema->createTable('#__project_comments')
                ->integer('id', ['autoIncrement' => true])
                ->integer('itemid')->default(0)
                ->text('comment')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->integer('activityid')->default(0)
                ->tinyInteger('state')->default(0)
                ->integer('parent_activity')->default(0)
                ->tinyInteger('anonymous')->default(0)
                ->tinyInteger('admin')->default(0)
                ->string('tbl', 50)->default('blog')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__project_microblog')) {
            $schema->createTable('#__project_microblog')
                ->integer('id', ['autoIncrement' => true])
                ->string('blogentry', 255)->nullable()
                ->datetime('posted')->default('0000-00-00 00:00:00')
                ->integer('posted_by')->default(0)
                ->tinyInteger('state')->default(0)
                ->tinyText('params')->nullable()
                ->integer('projectid')->default(0)
                ->integer('activityid')->default(0)
                ->tinyInteger('managers_only')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__project_owners')) {
            $schema->createTable('#__project_owners')
                ->integer('id', ['autoIncrement' => true])
                ->integer('projectid')->default(0)
                ->integer('userid')->default(0)
                ->integer('groupid')->default(0)
                ->string('invited_name', 100)->nullable()
                ->string('invited_email', 100)->nullable()
                ->string('invited_code', 10)->nullable()
                ->datetime('added')
                ->datetime('lastvisit')->nullable()
                ->datetime('prev_visit')->nullable()
                ->integer('status')->default(0)
                ->integer('num_visits')->default(0)
                ->integer('role')->default(0)
                ->integer('native')->default(0)
                ->text('params')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__project_todo')) {
            $schema->createTable('#__project_todo')
                ->integer('id', ['autoIncrement' => true])
                ->integer('projectid')->default(0)
                ->string('todolist', 255)->nullable()
                ->datetime('created')
                ->datetime('duedate')->nullable()
                ->datetime('closed')->nullable()
                ->integer('created_by')->default(0)
                ->integer('assigned_to')->default(0)
                ->integer('closed_by')->default(0)
                ->integer('priority')->default(0)
                ->integer('activityid')->default(0)
                ->tinyInteger('state')->default(0)
                ->tinyInteger('milestone')->default(0)
                ->tinyInteger('private')->default(0)
                ->text('details')->nullable()
                ->string('content', 255)
                ->string('color', 20)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__project_types')) {
            $schema->createTable('#__project_types')
                ->integer('id', ['autoIncrement' => true])
                ->string('type', 150)->default('')
                ->string('description', 255)->default('')
                ->text('params')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $params1 = 'apps_dev=0\npublications_public=1\nteam_public=1\nallow_invite=0';
            $params2 = 'apps_dev=1\npublications_public=1\nteam_public=1\nallow_invite=0';

            if (
                $this->db->getQuery(true)
                    ->select('type')
                    ->from('#__project_types')
                    ->where('type', '=', 'General')
                    ->doesntExist()
            ) {
                $this->db->getQuery(true)
                    ->insertOrIgnore('#__project_types')
                    ->values([
                        'type'        => 'General',
                        'description' => 'Individual or collaborative projects of general nature',
                        'params'      => $params1
                    ])
                    ->execute();
            }

            $desc2 = 'Projects created with the purpose to publish data as a resource '
                . 'or a collection of related resources';
            if (
                $this->db->getQuery(true)
                    ->select('type')
                    ->from('#__project_types')
                    ->where('type', '=', 'Content publication')
                    ->doesntExist()
            ) {
                $this->db->getQuery(true)
                    ->insertOrIgnore('#__project_types')
                    ->values([
                        'type'        => 'Content publication',
                        'description' => $desc2,
                        'params'      => $params1
                    ])
                    ->execute();
            }

            $desc3 = 'Projects created with the purpose to develop and publish a simulation tool or a code library';
            if (
                $this->db->getQuery(true)
                    ->select('type')
                    ->from('#__project_types')
                    ->where('type', '=', 'Application development')
                    ->doesntExist()
            ) {
                $this->db->getQuery(true)
                    ->insertOrIgnore('#__project_types')
                    ->values([
                        'type'        => 'Application development',
                        'description' => $desc3,
                        'params'      => $params2
                    ])
                    ->execute();
            }
        }

        if (!$schema->tableExists('#__project_logs')) {
            $schema->createTable('#__project_logs')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('projectid')->default(0)
                ->integer('userid')->default(0)
                ->tinyInteger('ajax')->default(0)
                ->unsignedInteger('owner')->default(0)
                ->string('ip', 15)->default('0')
                ->string('section', 100)->default('general')
                ->string('layout', 100)->default('')
                ->string('action', 100)->default('')
                ->datetime('time')->default('0000-00-00 00:00:00')
                ->tinyText('request_uri')->nullable()
                ->primaryKey('id')
                ->index('projectid', 'projectid')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__project_stats')) {
            $schema->createTable('#__project_stats')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('month')->nullable()
                ->integer('year')->nullable()
                ->integer('week')->nullable()
                ->datetime('processed')->default('0000-00-00 00:00:00')
                ->text('stats')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__project_public_stamps')) {
            $schema->createTable('#__project_public_stamps')
                ->integer('id', ['autoIncrement' => true])
                ->string('stamp', 30)->default('0')
                ->integer('projectid')->default(0)
                ->tinyInteger('listed')->default(0)
                ->string('type', 50)->default('files')
                ->text('reference')
                ->datetime('expires')->nullable()
                ->datetime('created')->nullable()
                ->integer('created_by')->nullable()
                ->primaryKey('id')
                ->uniqueIndex('stamp', 'stamp')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__project_remote_files')) {
            $schema->createTable('#__project_remote_files')
                ->integer('id', ['autoIncrement' => true])
                ->integer('projectid')->default(0)
                ->integer('created_by')->default(0)
                ->integer('modified_by')->default(0)
                ->integer('paired')->default(0)
                ->datetime('created')->nullable()
                ->datetime('modified')->nullable()
                ->datetime('synced')->nullable()
                ->string('local_path', 255)
                ->string('original_path', 255)
                ->string('original_format', 200)
                ->string('local_dirpath', 255)->default('')
                ->string('local_format', 200)->nullable()
                ->string('local_md5', 32)->nullable()
                ->string('service', 50)
                ->string('type', 25)->default('file')
                ->tinyInteger('remote_editing')->default(0)
                ->string('remote_id', 100)
                ->string('original_id', 100)
                ->string('remote_parent', 100)->nullable()
                ->string('remote_title', 140)->nullable()
                ->string('remote_md5', 32)->nullable()
                ->string('remote_format', 200)->nullable()
                ->string('remote_author', 100)->nullable()
                ->datetime('remote_modified')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // Add FULLTEXT index for project_microblog (using helper for SQLite compatibility)
        $schema->addFulltextIndex('#__project_microblog', 'title', 'blogentry');

        $componentParams = array(
            "component_on" => "0",
            "grantinfo" => "0",
            "confirm_step" => "0",
            "edit_settings" => "1",
            "restricted_data" => "0",
            "restricted_upfront" => "0",
            "approve_restricted" => "0",
            "privacylink" => "/legal/privacy",
            "HIPAAlink" => "/legal/privacy",
            "FERPAlink" => "/legal/privacy",
            "creatorgroup" => "",
            "admingroup" => "projectsadmin",
            "sdata_group" => "hipaa_reviewers",
            "ginfo_group" => "sps_reviewers",
            "min_name_length" => "6",
            "max_name_length" => "25",
            "reserved_names" => "clone, temp, test",
            "webpath" => "/srv/projects",
            "offroot" => "1",
            "gitpath" => "/usr/bin/git",
            "gitclone" => "/site/projects/clone/.git",
            "maxUpload" => "104857600",
            "defaultQuota" => "1",
            "premiumQuota" => "1",
            "approachingQuota" => "90",
            "pubQuota" => "1",
            "premiumPubQuota" => "1",
            "imagepath" => "/site/projects",
            "defaultpic" => "/components/com_projects/assets/img/project.png",
            "img_maxAllowed" => "5242880",
            "img_file_ext" => "jpg,jpeg,jpe,bmp,tif,tiff,png,gif",
            "logging" => "0",
            "messaging" => "1",
            "privacy" => "1",
            "limit" => "25",
            "sidebox_limit" => "3",
            "group_prefix" => "pr-",
            "use_alias" => "1",
            "documentation" => "/projects/features",
            "dbcheck" => "1"
        );
        $filesParams = array(
            "maxUpload" => "104857600",
            "maxDownload" => "1048576",
            "reservedNames" => "google , dropbox, shared, temp",
            "connectedProjects" => "",
            "enable_google" => "0",
            "google_clientId" => "",
            "google_clientSecret" => "",
            "google_appKey" => "",
            "google_folder" => "Google",
            "sync_lock" => "0",
            "auto_sync" => "1",
            "latex" => "1",
            "texpath" => "/usr/bin/",
            "gspath" => "/usr/bin/"
        );

        $this->addComponentEntry('Projects', 'com_projects', 1, $componentParams);

        $this->addPluginEntry('projects', 'blog');
        $this->addPluginEntry('projects', 'team');
        $this->addPluginEntry('projects', 'files', 1, $filesParams);
        $this->addPluginEntry('projects', 'todo');
        $this->addPluginEntry('projects', 'notes');
        $this->addPluginEntry('members', 'projects');
        $this->addPluginEntry('groups', 'projects');

        $this->addModuleEntry('mod_myprojects');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__projects');
        $schema->dropTable('#__project_activity');
        $schema->dropTable('#__project_comments');
        $schema->dropTable('#__project_microblog');
        $schema->dropTable('#__project_owners');
        $schema->dropTable('#__project_todo');
        $schema->dropTable('#__project_types');
        $schema->dropTable('#__project_logs');
        $schema->dropTable('#__project_stats');
        $schema->dropTable('#__project_public_stamps');
        $schema->dropTable('#__project_remote_files');

        $this->db->getQuery(true)
            ->delete('#__xmessage_component')
            ->where('component', '=', 'com_projects')
            ->execute();

        $this->deleteComponentEntry('Projects');

        $this->deletePluginEntry('projects');
        $this->deletePluginEntry('members', 'projects');
        $this->deletePluginEntry('groups', 'projects');

        $this->deleteModuleEntry('mod_myprojects');
    }
}
