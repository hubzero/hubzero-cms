<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for standardizing table and column names for wiki
**/
class Migration20160508203200ComWiki extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__wiki_page') && !$schema->tableExists('#__wiki_pages')) {
            $schema->renameTable('#__wiki_page', '#__wiki_pages');

            if (!$schema->hasColumn('#__wiki_pages', 'scope_id')) {
                $schema->addColumn('#__wiki_pages', 'scope_id')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            if (!$schema->hasColumn('#__wiki_pages', 'path')) {
                $schema->addColumn('#__wiki_pages', 'path')->string(255)->notNull()->execute();
            }

            if (!$schema->hasColumn('#__wiki_pages', 'namespace')) {
                $schema->addColumn('#__wiki_pages', 'namespace')->string(255)->notNull()->execute();

                $this->db->getQuery(true)
                    ->update('#__wiki_pages')
                    ->set(['namespace' => $this->db->quote('Help')])
                    ->where('pagename', 'LIKE', 'Help:%')
                    ->execute();

                $this->db->getQuery(true)
                    ->update('#__wiki_pages')
                    ->set(['namespace' => $this->db->quote('Template')])
                    ->where('pagename', 'LIKE', 'Template:%')
                    ->execute();
            }

            if (!$schema->hasColumn('#__wiki_pages', 'protected')) {
                $schema->addColumn('#__wiki_pages', 'protected')
                    ->tinyInteger()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            if (!$schema->hasColumn('#__wiki_pages', 'parent')) {
                $schema->addColumn('#__wiki_pages', 'parent')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            // Move state=1 (locked) to proected column
            $this->db->getQuery(true)
                ->update('#__wiki_pages')
                ->set(['protected' => 1])
                ->where('state', '=', 1)
                ->execute();

            // Mark items as published (state=1)
            $this->db->getQuery(true)
                ->update('#__wiki_pages')
                ->set(['state' => 1])
                ->where('state', '=', 0)
                ->execute();

            if ($schema->hasColumn('#__wiki_pages', 'group_cn')) {
                // Convert group pages
                $rows = $this->db->getQuery(true)
                    ->select('w.id, w.scope, w.group_cn, g.gidNumber')
                    ->from('#__wiki_pages', 'w')
                    ->innerJoin('#__xgroups AS g', 'w.group_cn', 'g.cn')
                    ->loadObjectList();
                foreach ($rows as $row) {
                    $row->scope = substr($row->scope, strlen($row->group_cn . '/wiki'));
                    $row->scope = ltrim($row->scope, '/');

                    $this->db->getQuery(true)
                        ->update('#__wiki_pages')
                        ->set(['scope' => 'group'])
                        ->set(['scope_id' => $row->gidNumber])
                        ->set(['path' => $row->scope])
                        ->where('id', '=', $row->id)
                        ->execute();
                }

                // Convert projects
                $rows = $this->db->getQuery(true)
                    ->select('w.id, w.scope, w.group_cn')
                    ->from('#__wiki_pages', 'w')
                    ->where('w.group_cn', 'LIKE', 'pr-%')
                    ->loadObjectList();
                foreach ($rows as $row) {
                    $row->scope = substr($row->scope, strlen($row->group_cn . '/wiki'));
                    $row->scope = ltrim($row->scope, '/');
                    $row->group_cn = substr($row->group_cn, strlen('pr-'));

                    $row->pidNumber = $this->db->getQuery(true)
                        ->select('id')
                        ->from('#__projects')
                        ->where('alias', '=', $row->group_cn)
                        ->value('id');

                    $this->db->getQuery(true)
                        ->update('#__wiki_pages')
                        ->set(['scope' => 'project'])
                        ->set(['scope_id' => $row->pidNumber])
                        ->set(['path' => $row->scope])
                        ->where('id', '=', $row->id)
                        ->execute();
                }

                $this->db->getQuery(true)
                    ->update('#__wiki_pages')
                    ->set(['scope' => $this->db->quote('site')])
                    ->where('scope_id', '=', 0)
                    ->execute();

                // Drop column
                $schema->dropColumn('#__wiki_pages', 'group_cn');
            }

            if ($schema->hasColumn('#__wiki_pages', 'authors')) {
                $schema->dropColumn('#__wiki_pages', 'authors');
            }
        }

        if ($schema->tableExists('#__wiki_attachments')) {
            if (
                $schema->hasColumn('#__wiki_attachments', 'pageid')
                && !$schema->hasColumn('#__wiki_attachments', 'page_id')
            ) {
                $schema->renameColumn('#__wiki_attachments', 'pageid', 'page_id')
                    ->integer()
                    ->default(0)
                    ->execute();
            }
        }

        if ($schema->tableExists('#__wiki_page_author') && !$schema->tableExists('#__wiki_authors')) {
            $schema->renameTable('#__wiki_page_author', '#__wiki_authors');
        }

        if ($schema->tableExists('#__wiki_math') && !$schema->tableExists('#__wiki_formulas')) {
            $schema->renameTable('#__wiki_math', '#__wiki_formulas');
        }

        if ($schema->tableExists('#__wiki_page_links') && !$schema->tableExists('#__wiki_links')) {
            $schema->renameTable('#__wiki_page_links', '#__wiki_links');
        }

        if ($schema->tableExists('#__wiki_comments')) {
            if ($schema->hasColumn('#__wiki_comments', 'pageid')) {
                $schema->renameColumn('#__wiki_comments', 'pageid', 'page_id')
                    ->integer()
                    ->default(0)
                    ->execute();
            }

            if ($schema->hasColumn('#__wiki_comments', 'status')) {
                $schema->renameColumn('#__wiki_comments', 'status', 'state')
                    ->tinyInteger()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }
        }

        if ($schema->tableExists('#__wiki_log')) {
            if ($schema->hasColumn('#__wiki_log', 'pid')) {
                $schema->renameColumn('#__wiki_log', 'pid', 'page_id')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            if ($schema->hasColumn('#__wiki_log', 'uid')) {
                $schema->renameColumn('#__wiki_log', 'uid', 'user_id')
                    ->integer()
                    ->default(0)
                    ->execute();
            }

            if (!$schema->tableExists('#__wiki_logs')) {
                $schema->renameTable('#__wiki_log', '#__wiki_logs');
            }
        }

        if ($schema->tableExists('#__wiki_page_metrics')) {
            if ($schema->hasColumn('#__wiki_page_metrics', 'pageid')) {
                $schema->renameColumn('#__wiki_page_metrics', 'pageid', 'page_id')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            if (!$schema->tableExists('#__wiki_metrics')) {
                $schema->renameTable('#__wiki_page_metrics', '#__wiki_metrics');
            }
        }

        if ($schema->tableExists('#__wiki_version')) {
            if ($schema->hasColumn('#__wiki_version', 'pageid')) {
                $schema->renameColumn('#__wiki_version', 'pageid', 'page_id')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            if (!$schema->tableExists('#__wiki_versions')) {
                $schema->renameTable('#__wiki_version', '#__wiki_versions');
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__wiki_page') && !$schema->tableExists('#__wiki_pages')) {
            if ($schema->hasColumn('#__wiki_pages', 'scope_id')) {
                $schema->dropColumn('#__wiki_pages', 'scope_id');
            }

            if ($schema->hasColumn('#__wiki_pages', 'path')) {
                $schema->dropColumn('#__wiki_pages', 'path');
            }

            if ($schema->hasColumn('#__wiki_pages', 'namespace')) {
                $schema->dropColumn('#__wiki_pages', 'namespace');
            }

            // Mark items as published (state=1)
            $this->db->getQuery(true)
                ->update('#__wiki_pages')
                ->set(['state' => 0])
                ->where('state', '=', 1)
                ->execute();

            if ($schema->hasColumn('#__wiki_pages', 'protected')) {
                // Mark items as published (state=1)
                $this->db->getQuery(true)
                    ->update('#__wiki_pages')
                    ->set(['state' => 1])
                    ->where('protected', '=', 1)
                    ->execute();

                $schema->dropColumn('#__wiki_pages', 'protected');
            }

            if ($schema->hasColumn('#__wiki_pages', 'parent')) {
                $schema->dropColumn('#__wiki_pages', 'parent');
            }

            if ($schema->hasColumn('#__wiki_pages', 'scope')) {
                if (!$schema->hasColumn('#__wiki_pages', 'group_cn')) {
                    $schema->addColumn('#__wiki_pages', 'group_cn')->string(255)->execute();
                }

                // Convert group pages
                $rows = $this->db->getQuery(true)
                    ->select('w.id, w.path, w.scope, w.scope_id, g.cn')
                    ->from('#__wiki_pages', 'w')
                    ->joinOn('#__xgroups AS g', [
                        ['w.scope_id', '=', 'g.gidNumber'],
                        ['left' => 'w.scope', 'operator' => '=', 'value' => 'group'],
                    ], 'inner')
                    ->loadObjectList();
                foreach ($rows as $row) {
                    $this->db->getQuery(true)
                        ->update('#__wiki_pages')
                        ->set(['group_cn' => $row->cn])
                        ->set(['scope' => $row->cn . '/wiki' . ($row->path ? '/' . $row->path : '')])
                        ->where('id', '=', $row->id)
                        ->execute();
                }

                // Convert projects
                $rows = $this->db->getQuery(true)
                    ->select('w.id, w.path, w.scope, w.scope_id, p.alias')
                    ->from('#__wiki_pages', 'w')
                    ->joinOn('#__projects AS p', [
                        ['w.scope_id', '=', 'p.id'],
                        ['left' => 'w.scope', 'operator' => '=', 'value' => 'project'],
                    ], 'inner')
                    ->loadObjectList();
                foreach ($rows as $row) {
                    $scopePath = $row->alias . '/wiki' . ($row->path ? '/' . $row->path : '');
                    $this->db->getQuery(true)
                        ->update('#__wiki_pages')
                        ->set(['group_cn' => 'pre-' . $row->alias])
                        ->set(['scope' => $scopePath])
                        ->where('id', '=', $row->id)
                        ->execute();
                }

                $this->db->getQuery(true)
                    ->update('#__wiki_pages')
                    ->set(['scope' => Expression::column('path')])
                    ->where('scope', '=', 'site')
                    ->where('scope_id', '=', 0)
                    ->execute();

                // Drop column
                $schema->dropColumn('#__wiki_pages', 'scope_id');
            }

            if (!$schema->tableExists('#__wiki_page')) {
                $schema->renameTable('#__wiki_pages', '#__wiki_page');
            }
        }

        if ($schema->tableExists('#__wiki_attachments')) {
            if ($schema->hasColumn('#__wiki_attachments', 'page_id')) {
                $schema->renameColumn('#__wiki_attachments', 'page_id', 'pageid')
                    ->integer()
                    ->default(0)
                    ->execute();
            }
        }

        if ($schema->tableExists('#__wiki_authors') && !$schema->tableExists('#__wiki_page_author')) {
            $schema->renameTable('#__wiki_authors', '#__wiki_page_author');
        }

        if ($schema->tableExists('#__wiki_formulas') && !$schema->tableExists('#__wiki_math')) {
            $schema->renameTable('#__wiki_formulas', '#__wiki_math');
        }

        if ($schema->tableExists('#__wiki_links') && !$schema->tableExists('#__wiki_page_links')) {
            $schema->renameTable('#__wiki_links', '#__wiki_page_links');
        }

        if ($schema->tableExists('#__wiki_comments')) {
            if ($schema->hasColumn('#__wiki_comments', 'page_id')) {
                $schema->renameColumn('#__wiki_comments', 'page_id', 'pageid')
                    ->integer()
                    ->default(0)
                    ->execute();
            }

            if ($schema->hasColumn('#__wiki_comments', 'state')) {
                $schema->renameColumn('#__wiki_comments', 'state', 'status')
                    ->tinyInteger()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }
        }

        if ($schema->tableExists('#__wiki_logs')) {
            if ($schema->hasColumn('#__wiki_logs', 'page_id')) {
                $schema->renameColumn('#__wiki_logs', 'page_id', 'pid')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            if ($schema->hasColumn('#__wiki_logs', 'user_id')) {
                $schema->renameColumn('#__wiki_logs', 'user_id', 'uid')
                    ->integer()
                    ->default(0)
                    ->execute();
            }

            if (!$schema->tableExists('#__wiki_log')) {
                $schema->renameTable('#__wiki_logs', '#__wiki_log');
            }
        }

        if ($schema->tableExists('#__wiki_metrics')) {
            if ($schema->hasColumn('#__wiki_metrics', 'page_id')) {
                $schema->renameColumn('#__wiki_metrics', 'page_id', 'pageid')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            if (!$schema->tableExists('#__wiki_page_metrics')) {
                $schema->renameTable('#__wiki_metrics', '#__wiki_page_metrics');
            }
        }

        if ($schema->tableExists('#__wiki_versions')) {
            if ($schema->hasColumn('#__wiki_versions', 'page_id')) {
                $schema->renameColumn('#__wiki_versions', 'page_id', 'pageid')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }

            if (!$schema->tableExists('#__wiki_version')) {
                $schema->renameTable('#__wiki_versions', '#__wiki_version');
            }
        }
    }
}
