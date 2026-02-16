<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding publication tables
 *
*/
class Migration20130829211856ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__publications')) {
            $schema->createTable('#__publications')
                ->id()
                ->integer('category')->default(0)
                ->integer('master_type')->default(1)
                ->integer('project_id')->default(0)
                ->integer('access')->default(0)
                ->integer('checked_out')->default(0)
                ->integer('created_by')->default(0)
                ->datetime('checked_out_time')->default('0000-00-00 00:00:00')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->decimal('rating', 2, 1)->default(0.0)
                ->integer('times_rated')->default(0)
                ->string('alias', 100)->default('')
                ->float('ranking')->default(0)
                ->execute();
        }

        if (!$schema->tableExists('#__publication_access')) {
            $schema->createTable('#__publication_access')
                ->id()
                ->integer('publication_version_id')->default(0)
                ->integer('group_id')->default(0)
                ->execute();
        }

        if (!$schema->tableExists('#__publication_attachments')) {
            $schema->createTable('#__publication_attachments')
                ->id()
                ->integer('publication_version_id')->default(0)
                ->integer('publication_id')->default(0)
                ->string('title', 255)->nullable()
                ->datetime('created')
                ->datetime('modified')->nullable()
                ->integer('created_by')->default(0)
                ->integer('modified_by')->default(0)
                ->integer('object_id')->default(0)
                ->string('object_name', 64)->default('0')
                ->integer('object_instance')->default(0)
                ->integer('object_revision')->default(0)
                ->tinyInteger('role')->default(0)
                ->string('path', 255)
                ->string('vcs_hash', 255)->nullable()
                ->string('vcs_revision', 255)->nullable()
                ->string('type', 30)->default('file')
                ->text('params')->nullable()
                ->text('attribs')->nullable()
                ->integer('ordering')->default(0)
                ->string('content_hash', 255)->nullable()
                ->execute();
        }

        if (!$schema->tableExists('#__publication_audience')) {
            $schema->createTable('#__publication_audience')
                ->id()
                ->integer('publication_id')->default(0)
                ->integer('publication_version_id')->default(0)
                ->tinyInteger('level0')->default(0)
                ->tinyInteger('level1')->default(0)
                ->tinyInteger('level2')->default(0)
                ->tinyInteger('level3')->default(0)
                ->tinyInteger('level4')->default(0)
                ->tinyInteger('level5')->default(0)
                ->string('comments', 255)->default('')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->execute();
        }

        if (!$schema->tableExists('#__publication_audience_levels')) {
            $schema->createTable('#__publication_audience_levels')
                ->id()
                ->string('label', 11)->default('0')
                ->string('title', 100)->default('')
                ->string('description', 255)->default('')
                ->execute();

            // Set audience level defaults
            // Set audience level defaults
            $levels = [
                ['level0', 'K12', 'Middle/High School'],
                ['level1', 'Easy', 'Freshmen/Sophomores'],
                ['level2', 'Intermediate', 'Juniors/Seniors'],
                ['level3', 'Advanced', 'Graduate Students'],
                ['level4', 'Expert', 'PhD Experts'],
                ['level5', 'Professional', 'Beyond PhD']
            ];

            foreach ($levels as $level) {
                $this->db->getQuery(true)
                    ->insert('#__publication_audience_levels')
                    ->set([
                        'label'       => $level[0],
                        'title'       => $level[1],
                        'description' => $level[2]
                    ])
                    ->execute();
            }
        }

        if (!$schema->tableExists('#__publication_authors')) {
            $schema->createTable('#__publication_authors')
                ->id()
                ->integer('publication_version_id')->default(0)
                ->integer('user_id')->default(0)
                ->integer('project_owner_id')->default(0)
                ->integer('ordering')->nullable()
                ->string('role', 50)->nullable()
                ->string('name', 255)
                ->string('firstName', 255)->nullable()
                ->string('lastName', 255)->nullable()
                ->string('organization', 255)->nullable()
                ->string('credit', 255)->nullable()
                ->datetime('created')
                ->datetime('modified')->nullable()
                ->integer('created_by')->default(0)
                ->integer('modified_by')->default(0)
                ->tinyInteger('status')->default(1)
                ->execute();
        }

        if (!$schema->tableExists('#__publication_categories')) {
            $schema->createTable('#__publication_categories')
                ->id()
                ->string('name', 200)->default('')
                ->string('dc_type', 200)->default('Dataset')
                ->string('alias', 200)->default('')
                ->string('url_alias', 200)->default('')
                ->tinyText('description')->nullable()
                ->integer('contributable')->default(1)
                ->tinyInteger('state')->default(1)
                ->text('customFields')->nullable()
                ->text('params')->nullable()
                ->uniqueIndex('type', 'name')
                ->uniqueIndex('alias', 'alias')
                ->uniqueIndex('url_alias', 'url_alias')
                ->execute();

            $customFields = 'bio=Bio=textarea=0\ncredits=Credits=textarea=0'
                . '\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0'
                . '\nreferences=References=textarea=0\npublications=Publications=textarea=0';
            $customFieldsTool = 'poweredby=Powered by=textarea=0\nbio=Bio=textarea=0'
                . '\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0'
                . '\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0'
                . '\npublications=Publications=textarea=0';
            $params = 'plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1';
            $paramsExt = 'plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1'
                . '\nplg_wishlist=1\nplg_citations=1\nplg_usage = 1';

            $categories = [
                [
                    'Datasets',
                    'Dataset',
                    'dataset',
                    'datasets',
                    'A collection of research data',
                    '1',
                    '1',
                    $customFields,
                    $paramsExt,
                ],
                [
                    'Workshops',
                    'Event',
                    'workshop',
                    'workshops',
                    'A collection of lectures, seminars, and materials that were presented at a workshop.',
                    '0',
                    '0',
                    $customFields,
                    $params,
                ],
                [
                    'Publications',
                    'Dataset',
                    'publication',
                    'publications',
                    'A publication is a paper relevant to the community that has been published in some manner.',
                    '0',
                    '0',
                    $customFields,
                    $params,
                ],
                [
                    'Learning Modules',
                    'InteractiveResource',
                    'learning module',
                    'learningmodules',
                    'A combination of presentations, tools, assignments, etc. geared toward teaching '
                        . 'a specific concept.',
                    '0',
                    '0',
                    $customFields,
                    $params,
                ],
                [
                    'Animations',
                    'MovingImage',
                    'animation',
                    'animations',
                    'An animation is a Flash-based demo or short movie that illustrates some concept.',
                    '0',
                    '0',
                    $customFields,
                    $params,
                ],
                [
                    'Courses',
                    'Collection',
                    'course',
                    'courses',
                    'University courses that make videos of lectures and associated teaching materials available.',
                    '0',
                    '0',
                    $customFields,
                    $params,
                ],
                [
                    'Tools',
                    'Software',
                    'tool',
                    'tools',
                    'A simulation tool is software that allows users to run a specific type of calculation.',
                    '0',
                    '1',
                    $customFieldsTool,
                    $params,
                ],
                [
                    'Downloads',
                    'PhysicalObject',
                    'download',
                    'downloads',
                    'A download is a type of resource that users can download and use on their own computer.',
                    '0',
                    '0',
                    $customFields,
                    $params,
                ],
                [
                    'Notes',
                    'Text',
                    'note',
                    'notes',
                    'Notes are typically a category for any resource that might not fit any of the other categories.',
                    '0',
                    '0',
                    $customFields,
                    $params,
                ],
                [
                    'Series',
                    'Collection',
                    'series',
                    'series',
                    'Series are collections of other resources, typically online presentations, that '
                        . 'cover a specific topic.',
                    '0',
                    '0',
                    $customFields,
                    $params,
                ],
                [
                    'Teaching Materials',
                    'Text',
                    'teaching material',
                    'teachingmaterials',
                    'Supplementary materials (study notes, guides, etc.) that don\'t quite fit into '
                        . 'any of the other categories.',
                    '0',
                    '0',
                    $customFields,
                    $params
                ]
            ];

            foreach ($categories as $category) {
                $this->db->getQuery(true)
                    ->insert('#__publication_categories')
                    ->set([
                        'name'          => $category[0],
                        'dc_type'       => $category[1],
                        'alias'         => $category[2],
                        'url_alias'     => $category[3],
                        'description'   => $category[4],
                        'contributable' => $category[5],
                        'state'         => $category[6],
                        'customFields'  => $category[7],
                        'params'        => $category[8]
                    ])
                    ->execute();
            }
        }

        if (!$schema->tableExists('#__publication_master_types')) {
            $schema->createTable('#__publication_master_types')
                ->id()
                ->string('type', 200)->default('')
                ->string('alias', 200)->default('')
                ->tinyText('description')->nullable()
                ->integer('contributable')->default(0)
                ->integer('supporting')->default(0)
                ->integer('ordering')->default(0)
                ->text('params')->nullable()
                ->uniqueIndex('alias', 'alias')
                ->execute();

            $types = [
                ['File(s)', 'files', 'uploaded material', '1', '1', '1', 'peer_review=1'],
                ['Link', 'links', 'external content', '0', '0', '3', ''],
                ['Wiki', 'notes', 'from project notes', '0', '0', '5', ''],
                ['Application', 'apps', 'simulation tool', '0', '0', '4', ''],
                ['Series', 'series', 'publication collection', '0', '0', '6', ''],
                ['Gallery', 'gallery', 'image/photo gallery', '0', '0', '7', ''],
                ['Databases', 'databases', 'project database', '0', '0', '2', '']
            ];

            foreach ($types as $type) {
                $this->db->getQuery(true)
                    ->insert('#__publication_master_types')
                    ->set([
                        'type'          => $type[0],
                        'alias'         => $type[1],
                        'description'   => $type[2],
                        'contributable' => $type[3],
                        'supporting'    => $type[4],
                        'ordering'      => $type[5],
                        'params'        => $type[6]
                    ])
                    ->execute();
            }
        }

        if (!$schema->tableExists('#__publication_ratings')) {
            $schema->createTable('#__publication_ratings')
                ->id()
                ->integer('publication_id')->default(0)
                ->integer('publication_version_id')->default(0)
                ->integer('created_by')->default(0)
                ->decimal('rating', 2, 1)->default(0.0)
                ->text('comment')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->tinyInteger('anonymous')->default(0)
                ->execute();
        }

        if (!$schema->tableExists('#__publication_screenshots')) {
            $schema->createTable('#__publication_screenshots')
                ->id()
                ->integer('publication_version_id')->default(0)
                ->integer('publication_id')->default(0)
                ->string('title', 127)->default('')
                ->integer('ordering')->default(0)
                ->string('filename', 100)
                ->string('srcfile', 100)
                ->datetime('created')->nullable()
                ->datetime('modified')->nullable()
                ->string('created_by', 127)->nullable()
                ->string('modified_by', 127)->nullable()
                ->execute();
        }

        if (!$schema->tableExists('#__publication_stats')) {
            $schema->createTable('#__publication_stats')
                ->id('id', 'bigIncrements')
                ->bigInteger('publication_id')
                ->tinyInteger('publication_version')->nullable()
                ->bigInteger('users')->nullable()
                ->bigInteger('downloads')->nullable()
                ->datetime('datetime')->default('0000-00-00 00:00:00')
                ->tinyInteger('period')->default(-1)
                ->timestamp('processed_on')
                ->uniqueIndex('pub_stats', ['publication_id', 'datetime', 'period'])
                ->execute();
        }

        if (!$schema->tableExists('#__publication_versions')) {
            $schema->createTable('#__publication_versions')
                ->id()
                ->integer('publication_id')->default(0)
                ->integer('main')->default(0)
                ->string('doi', 255)->default('')
                ->string('ark', 255)->default('')
                ->integer('state')->default(0)
                ->string('title', 255)->default('')
                ->text('description')
                ->text('abstract')
                ->text('metadata')->nullable()
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->datetime('published_up')->default('0000-00-00 00:00:00')
                ->datetime('published_down')->nullable()
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->datetime('accepted')->default('0000-00-00 00:00:00')
                ->datetime('submitted')->default('0000-00-00 00:00:00')
                ->integer('modified_by')->default(0)
                ->string('version_label', 100)->default('1.0')
                ->string('secret', 10)->default('')
                ->integer('version_number')->default(0)
                ->text('params')->nullable()
                ->text('release_notes')->nullable()
                ->text('license_text')->nullable()
                ->integer('license_type')->nullable()
                ->integer('access')->default(0)
                ->decimal('rating', 2, 1)->default(0.0)
                ->integer('times_rated')->default(0)
                ->float('ranking')->default(0)
                ->execute();
        }

        if (!$schema->tableExists('#__publication_licenses')) {
            $schema->createTable('#__publication_licenses')
                ->id()
                ->string('name', 100)
                ->text('text')->nullable()
                ->string('title', 100)->nullable()
                ->string('url', 250)->nullable()
                ->text('info')->nullable()
                ->integer('ordering')->nullable()
                ->integer('active')->default(0)
                ->integer('apps_only')->default(0)
                ->integer('main')->default(0)
                ->integer('agreement')->default(0)
                ->integer('customizable')->default(0)
                ->string('icon', 250)->nullable()
                ->execute();

            $licText1 = '[ONE LINE DESCRIPTION]\r\nCopyright (C) [YEAR] [OWNER]';
            $licIcon1 = '/components/com_publications/assets/img/logos/license.gif';

            $ccInfo = 'CC0 enables scientists, educators, artists and other creators and owners of '
                . 'copyright- or database-protected content to waive those interests in their works '
                . 'and thereby place them as completely as possible in the public domain, so that '
                . 'others may freely build upon, enhance and reuse the works for any purposes '
                . 'without restriction under copyright or database law.';
            $licIcon2 = '/components/com_publications/assets/img/logos/cc.gif';

            $licIcon3 = '/components/com_publications/images/logos/license.gif';

            $licenses = [
                [
                    'custom',
                    $licText1,
                    'Custom',
                    'http://creativecommons.org/about/cc0',
                    'Custom license',
                    '3',
                    '1',
                    '0',
                    '0',
                    '0',
                    '1',
                    $licIcon1
                ],
                [
                    'cc',
                    '',
                    'CC0 - Creative Commons',
                    'http://creativecommons.org/about/cc0',
                    $ccInfo,
                    '2',
                    '1',
                    '0',
                    '1',
                    '1',
                    '0',
                    $licIcon2
                ],
                [
                    'standard',
                    'All rights reserved.',
                    'Standard HUB License',
                    'http://nanohub.org',
                    'Standard HUB license.',
                    '1',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    $licIcon3
                ]
            ];

            foreach ($licenses as $lic) {
                $this->db->getQuery(true)
                    ->insert('#__publication_licenses')
                    ->set([
                        'name'         => $lic[0],
                        'text'         => $lic[1],
                        'title'        => $lic[2],
                        'url'          => $lic[3],
                        'info'         => $lic[4],
                        'ordering'     => $lic[5],
                        'active'       => $lic[6],
                        'apps_only'    => $lic[7],
                        'main'         => $lic[8],
                        'agreement'    => $lic[9],
                        'customizable' => $lic[10],
                        'icon'         => $lic[11]
                    ])
                    ->execute();
            }
        }

        $params = array(
            "enabled" => "1",
            "autoapprove" => "1",
            "autoapproved_users" => "",
            "email" => "0",
            "default_category" => "dataset",
            "defaultpic" => "/core/components/com_publications/site/assets/img/resource_thumb.gif",
            "toolpic" => "/core/components/com_publications/site/assets/img/tool_thumb.gif",
            "video_thumb" => "/core/components/com_publications/site/assets/img/video_thumb.gif",
            "gallery_thumb" => "/core/components/com_publications/site/assets/img/gallery_thumb.gif",
            "webpath" => "/site/publications",
            "aboutdoi" => "",
            "doi_shoulder" => "",
            "doi_prefix" => "",
            "doi_service" => "",
            "doi_userpw" => "",
            "doi_xmlschema" => "",
            "doi_publisher" => "",
            "doi_resolve" => "https://doi.org/",
            "doi_verify" => "http://n2t.net/ezid/id/",
            "supportedtag" => "",
            "supportedlink" => "",
            "google_id" => "",
            "show_authors" => "1",
            "show_ranking" => "1",
            "show_rating" => "1",
            "show_date" => "3",
            "show_citation" => "1",
            "panels" => "content, description, authors, audience, gallery, tags, access, license, notes",
            "suggest_licence" => "0",
            "show_tags" => "1",
            "show_metadata" => "1",
            "show_notes" => "1",
            "show_license" => "1",
            "show_access" => "0",
            "show_gallery" => "1",
            "show_audience" => "0",
            "audiencelink" => "",
            "documentation" => "/kb/publications",
            "deposit_terms" => "/legal/termsofdeposit",
            "dbcheck" => "0",
            "repository" => "0",
            "aip_path" => "/srv/AIP"
        );

        $this->addComponentEntry('Publications', 'com_publications', 1, $params);

        $this->addPluginEntry('publications', 'related');
        $this->addPluginEntry('publications', 'recommendations');
        $this->addPluginEntry('publications', 'supportingdocs');
        $this->addPluginEntry('publications', 'versions');
        $this->addPluginEntry('publications', 'questions');
        $this->addPluginEntry('publications', 'citations');
        $this->addPluginEntry('publications', 'usage');
        $this->addPluginEntry('publications', 'share');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__publications');
        $schema->dropTable('#__publication_access');
        $schema->dropTable('#__publication_attachments');
        $schema->dropTable('#__publication_audience');
        $schema->dropTable('#__publication_audience_levels');
        $schema->dropTable('#__publication_authors');
        $schema->dropTable('#__publication_categories');
        $schema->dropTable('#__publication_master_types');
        $schema->dropTable('#__publication_ratings');
        $schema->dropTable('#__publication_screenshots');
        $schema->dropTable('#__publication_stats');
        $schema->dropTable('#__publication_versions');
        $schema->dropTable('#__publication_licenses');

        $this->deleteComponentEntry('Publications');

        $this->deletePluginEntry('publications');
    }
}
