<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating publication categories table (if it doesn't exist)
  *
**/
class Migration20130828201526ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__publication_categories')) {
            $schema
                ->createTable('#__publication_categories')
                ->integer('id', ['autoIncrement' => true])
                ->string('name', 200)->default('')
                ->string('dc_type', 200)->default('Dataset')
                ->string('alias', 200)->default('')
                ->string('url_alias', 200)->default('')
                ->tinyText('description')->nullable()
                ->integer('contributable')->default(1)
                ->tinyInteger('state')->default(1)
                ->text('customFields')->nullable()
                ->text('params')->nullable()
                ->primaryKey('id')
                ->uniqueIndex('type', 'name')
                ->uniqueIndex('alias', 'alias')
                ->uniqueIndex('url_alias', 'url_alias')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $customFields = "bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0"
                . "\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0"
                . "\npublications=Publications=textarea=0";
            $customFieldsTool = "poweredby=Powered by=textarea=0\nbio=Bio=textarea=0"
                . "\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0"
                . "\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0"
                . "\npublications=Publications=textarea=0";
            $params = "plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1";
            $paramsExt = "plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1"
                . "\nplg_wishlist=1\nplg_citations=1\nplg_usage = 1";

            $categories = [
                [
                    '1',
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
                    '2',
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
                    '3',
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
                    '4',
                    'Learning Modules',
                    'InteractiveResource',
                    'learning module',
                    'learningmodules',
                    'A combination of presentations, tools, assignments, etc. geared toward teaching a '
                        . 'specific concept.',
                    '0',
                    '0',
                    $customFields,
                    $params,
                ],
                [
                    '5',
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
                    '6',
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
                    '7',
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
                    '9',
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
                    '10',
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
                    '11',
                    'Series',
                    'Collection',
                    'series',
                    'series',
                    'Series are collections of other resources, typically online presentations, that cover a '
                        . 'specific topic.',
                    '0',
                    '0',
                    $customFields,
                    $params,
                ],
                [
                    '12',
                    'Teaching Materials',
                    'Text',
                    'teaching material',
                    'teachingmaterials',
                    'Supplementary materials (study notes, guides, etc.) that don\'t quite fit into any of the '
                        . 'other categories.',
                    '0',
                    '0',
                    $customFields,
                    $params,
                ],
            ];

            foreach ($categories as $category) {
                $this->db
                    ->getQuery(true)
                    ->insert('#__publication_categories')
                    ->set([
                        'id'            => $category[0],
                        'name'          => $category[1],
                        'dc_type'       => $category[2],
                        'alias'         => $category[3],
                        'url_alias'     => $category[4],
                        'description'   => $category[5],
                        'contributable' => $category[6],
                        'state'         => $category[7],
                        'customFields'  => $category[8],
                        'params'        => $category[9],
                    ])
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__publication_categories');
    }
}
