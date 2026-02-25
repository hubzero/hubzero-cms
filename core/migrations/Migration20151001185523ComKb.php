<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

// Restricted access
/**
 * Migration script for moving KB categories to #__categories
 *
*/
class Migration20151001185523ComKb extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__faq_categories') && $schema->tableExists('#__categories')) {
            if ($schema->hasColumn('#__faq', 'section')) {
                $query = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__faq_categories');
                $categories = $query->loadObjectList();

                $sub = array();
                $par = array();

                foreach ($categories as $category) {
                    if ($category->section) {
                        $sub[] = $category;
                        continue;
                    }

                    $category->section = 1;
                    $category->level   = 1;
                    $category->path    = $category->alias;

                    $par[$category->id] = $this->category($category);
                }

                foreach ($sub as $category) {
                    $parent = (isset($par[$category->section]) ? $par[$category->section] : 1);

                    $category->path    = $category->alias;
                    foreach ($categories as $c) {
                        if ($c->id == $category->section) {
                            $category->path = $c->alias . '/' . $category->alias;
                            break;
                        }
                    }
                    $category->section = $parent;
                    $category->level   = 2;

                    $par[$category->id] = $this->category($category);
                }

                $query = $this->db->getQuery(true)
                    ->select('MAX(rgt)')
                    ->from('#__categories');
                $max = $query->value('MAX(rgt)');

                if ($max) {
                    $max = intval($max);
                    $this->db->getQuery(true)
                        ->update('#__categories')
                        ->set(['rgt' => $max + 1])
                        ->where('extension', '=', 'system')
                        ->where('title', '=', 'ROOT')
                        ->execute();
                }

                $this->db->getQuery(true)
                    ->update('#__categories')
                    ->set(['parent_id' => 1])
                    ->where('extension', '=', 'com_kb')
                    ->where('parent_id', '=', 0)
                    ->execute();

                $query = $this->db->getQuery(true)
                    ->select(['id', 'section', 'category'])
                    ->from('#__faq');
                $articles = $query->loadObjectList();

                foreach ($articles as $article) {
                    $key = ($article->category ? $article->category : $article->section);

                    $article->category = (isset($par[$key]) ? $par[$key] : 0);

                    $this->db->getQuery(true)
                        ->update('#__faq')
                        ->set(['category' => $article->category])
                        ->where('id', '=', (int)$article->id)
                        ->execute();
                }

                $schema->dropIndex('#__faq', 'idx_section');

                $this->db->schema()->dropColumn('#__faq', 'section');
            }

            if ($schema->tableExists('#__faq_categories')) {
                $this->db->schema()->dropTable('#__faq_categories');
            }

            if ($schema->tableExists('#__faq') && !$schema->tableExists('#__kb_articles')) {
                $schema->renameTable('#__faq', '#__kb_articles');
            }

            if ($schema->tableExists('#__faq_comments') && !$schema->tableExists('#__kb_comments')) {
                $schema->renameTable('#__faq_comments', '#__kb_comments');
            }

            if ($schema->tableExists('#__faq_helpful_log') && !$schema->tableExists('#__kb_votes')) {
                $schema->renameTable('#__faq_helpful_log', '#__kb_votes');

                $this->db->getQuery(true)
                    ->update('#__kb_votes')
                    ->set(['type' => 'article'])
                    ->where('type', '=', 'entry')
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

        if (!$schema->tableExists('#__faq_categories') && $schema->tableExists('#__categories')) {
            if (!$schema->tableExists('#__faq_categories')) {
                $schema->createTable('#__faq_categories')
                    ->integer('id', ['autoIncrement' => true])
                    ->string('title', 200)->nullable()
                    ->string('alias', 200)->nullable()
                    ->string('description', 255)->default('')
                    ->integer('section')->default(0)
                    ->tinyInteger('state')->default(0)
                    ->tinyInteger('access')->default(0)
                    ->integer('asset_id')->default(0)
                    ->primaryKey('id')
                    ->index('idx_alias', 'alias')
                    ->index('idx_section', 'section')
                    ->index('idx_state', 'state')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            }

            if (!$schema->tableExists('#__faq') && $schema->tableExists('#__kb_articles')) {
                $schema->renameTable('#__kb_articles', '#__faq');
            }

            if (!$schema->tableExists('#__faq_comments') && $schema->tableExists('#__kb_comments')) {
                $schema->renameTable('#__kb_comments', '#__faq_comments');
            }

            if (!$schema->tableExists('#__faq_votes') && $schema->tableExists('#__kb_votes')) {
                $schema->renameTable('#__kb_votes', '#__faq_helpful_log');

                $this->db->getQuery(true)
                    ->update('#__faq_helpful_log')
                    ->set(['type' => 'entry'])
                    ->where('type', '=', 'article')
                    ->execute();
            }

            if (!$schema->hasColumn('#__faq', 'section')) {
                $schema->addColumn('section')->integer()->notNull()->default(0);
            }

            $schema->addIndex('#__faq', 'idx_section', 'section');

            $query = $this->db->getQuery(true)
                ->select('*')
                ->from('#__categories')
                ->where('extension', '=', 'com_kb');
            $categories = $query->loadObjectList();

            $sub = array();
            $par = array();

            foreach ($categories as $category) {
                if ($category->parent_id) {
                    $sub[] = $category;
                    continue;
                }

                $par[$category->id] = $this->section($category);
            }

            foreach ($sub as $category) {
                //$parent = (isset($par[$category->parent_id]) ? $par[$category->parent_id] : 0);
                //$category->section = $parent;

                $par[$category->id] = $this->section($category);
            }

            $query = $this->db->getQuery(true)
                ->select(['id', 'category'])
                ->from('#__faq');
            $articles = $query->loadObjectList();

            foreach ($articles as $article) {
                $article->section = (isset($par[$article->category]) ? $par[$article->category] : 0);

                $this->db->getQuery(true)
                    ->update('#__faq')
                    ->set([
                        'section'  => $article->section,
                        'category' => 0
                    ])
                    ->where('id', '=', (int)$article->id)
                    ->execute();
            }
        }
    }

    /**
     * Make a #__categories entry
     *
     * @param   object  $category
     * @return  integer
     */
    public function category($category)
    {
        $id = 0;

        if (is_file(\Hubzero\Facades\Component::path('com_categories') . DS . 'models' . DS . 'category.php')) {
            include_once \Hubzero\Facades\Component::path('com_categories') . DS . 'models' . DS . 'category.php';

            // NOTE: We're using a model to do this as creating an entry involves
            // multiple queries due to the 'nested set' structure of the table
            $tbl = \Components\Categories\Models\Category::blank();
            $tbl->set('title', $category->title);
            $tbl->set('alias', $category->alias);
            $tbl->set('description', $category->description);
            $tbl->set('extension', 'com_kb');
            $tbl->set('published', $category->state);
            $tbl->set('access', $category->access);
            $tbl->set('parent_id', ($category->section ? $category->section : 1));
            $tbl->set('language', '*');
            $tbl->set('level', $category->level);
            $tbl->set('path', $category->path);
            $tbl->set('note', '');
            $tbl->set('metakey', '');
            $tbl->set('metadesc', '');
            $tbl->set('metadata', '');
            $tbl->set('params', '');

            $tbl->assetRules = new \Hubzero\Access\Rules(array());
            $tbl->setNameSpace('com_kb');

            $tbl->save();

            $id = $tbl->get('id');
        }

        return $id;
    }

    /**
     * Make a #__faq_categories entry
     *
     * @param   object  $tbl
     * @return  integer
     */
    public function section($tbl)
    {
        $id = 0;

        if ($this->db->schema()->tableExists('#__faq_categories')) {
            $this->db->getQuery(true)
                ->insert('#__faq_categories')
                ->values([
                    'id'          => null,
                    'title'       => $tbl->title,
                    'alias'       => $tbl->alias,
                    'description' => $tbl->description,
                    'section'     => $tbl->parent_id,
                    'state'       => $tbl->published,
                    'access'      => $tbl->access,
                    'asset_id'    => null
                ])
                ->execute();

            $query = $this->db->getQuery(true)
                ->select('id')
                ->from('#__faq_categories')
                ->where('title', '=', $tbl->title)
                ->where('alias', '=', $tbl->alias)
                ->where('section', '=', (int)$tbl->parent_id);
            $id = $query->value('id');
        }

        return $id;
    }
}
