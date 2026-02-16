<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding newletter features
  *
**/
class Migration20130716202127ComNewsletter extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // create component entry
        $this->addComponentEntry('Newsletters', 'com_newsletter');

        if (!$schema->tableExists('#__newsletters')) {
            $schema->createTable('#__newsletters')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('alias', 150)->nullable()
                ->string('name', 150)->nullable()
                ->integer('issue')->nullable()
                ->string('type', 50)->default('html')
                ->integer('template')->nullable()
                ->integer('published')->default(1)
                ->integer('sent')->default(0)
                ->text('content')->nullable()
                ->integer('tracking')->default(1)
                ->datetime('created')->nullable()
                ->integer('created_by')->nullable()
                ->datetime('modified')->nullable()
                ->integer('modified_by')->nullable()
                ->integer('deleted')->default(0)
                ->text('params')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__newsletter_templates')) {
            $schema->createTable('#__newsletter_templates')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('editable')->default(1)
                ->string('name', 100)->nullable()
                ->text('template')->nullable()
                ->string('primary_title_color', 100)->nullable()
                ->string('primary_text_color', 100)->nullable()
                ->string('secondary_title_color', 100)->nullable()
                ->string('secondary_text_color', 100)->nullable()
                ->integer('deleted')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            // insert default templates
            $htmlTemplate = '<html>\n'
                . '	<head>\n'
                . '		<title>{{TITLE}}</title>\n'
                . '	</head>\n'
                . '	<body>\n'
                . '		<table width="100%" border="0" cellspacing="0">\n'
                . '			<tr>\n'
                . '				<td align="center">\n'
                . '					\n'
                . '					<table width="700" border="0" cellpadding="20" cellspacing="0">\n'
                . '						<tr class="display-browser">\n'
                . '							<td colspan="2" style="font-size:10px;padding:0 0 5px 0;" align="center">\n'
                . '								Email not displaying correctly? '
                . '<a href="{{LINK}}">View in a Web Browser</a>\n'
                . '							</td>\n'
                . '						</tr>\n'
                . '						<tr>\n'
                . '							<td colspan="2" style="background:#000000;">\n'
                . '								<h1 style="color:#FFFFFF;">HUB Campaign Template</h1>\n'
                . '								<h3 style="color:#888888;">{{TITLE}}</h3>\n'
                . '							</td>\n'
                . '						<tr>\n'
                . '							<td width="500" valign="top" '
                . 'style="font-size:14px;color:#222222;border-left:1px solid #000000;">\n'
                . '								<span style="display:block;color:#CCCCCC;margin-bottom:20px;">'
                . 'Issue {{ISSUE}}</span>\n'
                . '								{{PRIMARY_STORIES}}\n'
                . '							</td>\n'
                . '							<td width="200" valign="top" '
                . 'style="font-size:12px;color:#555555;border-left:1px solid #AAAAAA;'
                . 'border-right:1px solid #000000;">\n'
                . '								{{SECONDARY_STORIES}}\n'
                . '							</td>\n'
                . '						</tr>\n'
                . '						<tr>\n'
                . '							<td colspan="2" align="center" style="background:#000000;color:#FFFFFF;">\n'
                . '								Copyright &copy; {{COPYRIGHT}} HUB. All Rights reserved.\n'
                . '							</td>\n'
                . '						</tr>\n'
                . '					</table>\n'
                . '				\n'
                . '				</td>\n'
                . '			</tr>\n'
                . '		</table>\n'
                . '	</body>\n'
                . '</html>	';

            $this->db->getQuery(true)
                ->insert('#__newsletter_templates')
                ->columns([
                    'editable',
                    'name',
                    'template',
                    'primary_title_color',
                    'primary_text_color',
                    'secondary_title_color',
                    'secondary_text_color',
                    'deleted',
                ])
                ->values([0, 'Default HTML Email Template', $htmlTemplate, '', '', '', '', 0])
                ->execute();

            $plainTemplate = 'View In Browser - {{LINK}}\n'
                . '=====================================\n'
                . '{{TITLE}} - {{ISSUE}}\n'
                . '=====================================\n'
                . '\n'
                . '{{PRIMARY_STORIES}}\n'
                . '\n'
                . '--------------------------------------------------\n'
                . '\n'
                . '{{SECONDARY_STORIES}}\n'
                . '\n'
                . '--------------------------------------------------\n'
                . '\n'
                . 'Unsubscribe - {{UNSUBSCRIBE_LINK}}\n'
                . 'Copyright - {{COPYRIGHT}}';

            $this->db->getQuery(true)
                ->insert('#__newsletter_templates')
                ->columns([
                    'editable',
                    'name',
                    'template',
                    'primary_title_color',
                    'primary_text_color',
                    'secondary_title_color',
                    'secondary_text_color',
                    'deleted',
                ])
                ->values([0, 'Default Plain Text Email Template', $plainTemplate, null, null, null, null, 0])
                ->execute();

            // add newsletter cron jobs
            $params1 = 'newsletter_queue_limit=2\n'
                . 'support_ticketreminder_severity=all\n'
                . 'support_ticketreminder_group=\n\n';

            $query = $this->db->getQuery(true);
            $query->select('title')
                ->from('#__cron_jobs')
                ->where('title', '=', 'Process Newsletter Mailings');
            if ($query->doesntExist()) {
                $this->db->getQuery(true)
                    ->insertOrIgnore('#__cron_jobs')
                    ->columns([
                        'title',
                        'state',
                        'plugin',
                        'event',
                        'last_run',
                        'next_run',
                        'recurrence',
                        'created',
                        'created_by',
                        'modified',
                        'modified_by',
                        'active',
                        'ordering',
                        'params',
                    ])
                    ->values([
                        'Process Newsletter Mailings',
                        0,
                        'newsletter',
                        'processMailings',
                        '0000-00-00 00:00:00',
                        '0000-00-00 00:00:00',
                        '*/5 * * * *',
                        '2013-06-25 08:23:04',
                        1001,
                        '2013-07-16 17:15:01',
                        0,
                        0,
                        0,
                        $params1,
                    ])
                    ->execute();
            }

            $query = $this->db->getQuery(true);
            $query->select('title')
                ->from('#__cron_jobs')
                ->where('title', '=', 'Process Newsletter Opens & Click IP Addresses');
            if ($query->doesntExist()) {
                $this->db->getQuery(true)
                    ->insertOrIgnore('#__cron_jobs')
                    ->columns([
                        'title',
                        'state',
                        'plugin',
                        'event',
                        'last_run',
                        'next_run',
                        'recurrence',
                        'created',
                        'created_by',
                        'modified',
                        'modified_by',
                        'active',
                        'ordering',
                        'params',
                    ])
                    ->values([
                        'Process Newsletter Opens & Click IP Addresses',
                        0,
                        'newsletter',
                        'processIps',
                        '0000-00-00 00:00:00',
                        '0000-00-00 00:00:00',
                        '*/5 * * * *',
                        '2013-06-25 08:23:04',
                        1001,
                        '2013-07-16 17:15:01',
                        0,
                        0,
                        0,
                        '',
                    ])
                    ->execute();
            }
        }

        if (!$schema->tableExists('#__newsletter_primary_story')) {
            $schema->createTable('#__newsletter_primary_story')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('nid')
                ->string('title', 150)->nullable()
                ->text('story')->nullable()
                ->string('readmore_title', 100)->nullable()
                ->string('readmore_link', 200)->nullable()
                ->integer('order')->nullable()
                ->integer('deleted')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__newsletter_secondary_story')) {
            $schema->createTable('#__newsletter_secondary_story')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('nid')
                ->string('title', 150)->nullable()
                ->text('story')->nullable()
                ->string('readmore_title', 100)->nullable()
                ->string('readmore_link', 200)->nullable()
                ->integer('order')->nullable()
                ->integer('deleted')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__newsletter_mailings')) {
            $schema->createTable('#__newsletter_mailings')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('nid')->nullable()
                ->integer('lid')->nullable()
                ->string('subject', 250)->nullable()
                ->longText('body')->nullable()
                ->text('headers')->nullable()
                ->text('args')->nullable()
                ->integer('tracking')->default(1)
                ->datetime('date')->nullable()
                ->integer('deleted')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__newsletter_mailinglists')) {
            $schema->createTable('#__newsletter_mailinglists')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('name', 150)->nullable()
                ->text('description')->nullable()
                ->integer('private')->nullable()
                ->integer('deleted')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__newsletter_mailinglist_unsubscribes')) {
            $schema->createTable('#__newsletter_mailinglist_unsubscribes')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('mid')->nullable()
                ->string('email', 150)->nullable()
                ->text('reason')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__newsletter_mailinglist_emails')) {
            $schema->createTable('#__newsletter_mailinglist_emails')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('mid')->nullable()
                ->string('email', 150)->nullable()
                ->string('status', 100)->nullable()
                ->integer('confirmed')->default(0)
                ->datetime('date_added')->nullable()
                ->datetime('date_confirmed')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__newsletter_mailing_recipients')) {
            $schema->createTable('#__newsletter_mailing_recipients')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('mid')->nullable()
                ->string('email', 150)->nullable()
                ->string('status', 100)->nullable()
                ->datetime('date_added')->nullable()
                ->datetime('date_sent')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__newsletter_mailing_recipient_actions')) {
            $schema->createTable('#__newsletter_mailing_recipient_actions')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('mailingid')->nullable()
                ->string('action', 100)->nullable()
                ->text('action_vars')->nullable()
                ->string('email', 255)->nullable()
                ->string('ip', 100)->nullable()
                ->string('user_agent', 255)->nullable()
                ->datetime('date')->nullable()
                ->char('countrySHORT', 2)->nullable()
                ->string('countryLONG', 64)->nullable()
                ->string('ipREGION', 128)->nullable()
                ->string('ipCITY', 128)->nullable()
                ->double('ipLATITUDE')->nullable()
                ->double('ipLONGITUDE')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        $this->addPluginEntry('cron', 'newsletter');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $this->deleteComponentEntry('Newsletters');

        // remove all newsletter tables
        $schema->dropTable('#__newsletters');
        $schema->dropTable('#__newsletter_templates');
        $schema->dropTable('#__newsletter_primary_story');
        $schema->dropTable('#__newsletter_secondary_story');
        $schema->dropTable('#__newsletter_mailings');
        $schema->dropTable('#__newsletter_mailinglists');
        $schema->dropTable('#__newsletter_mailinglist_unsubscribes');
        $schema->dropTable('#__newsletter_mailinglist_emails');
        $schema->dropTable('#__newsletter_mailing_recipients');
        $schema->dropTable('#__newsletter_mailing_recipient_actions');

        //remove newsletter cron jobs
        $query = $this->db->getQuery(true);
        $query->delete('#__cron_jobs')
            ->where('title', '=', 'Process Newsletter Mailings');
        $query->execute();

        $query = $this->db->getQuery(true);
        $query->delete('#__cron_jobs')
            ->where('title', '=', 'Process Newsletter Opens & Click IP Addresses');
        $query->execute();

        $this->deletePluginEntry('cron', 'newsletter');
    }
}
