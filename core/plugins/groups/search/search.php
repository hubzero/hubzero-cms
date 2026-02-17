<?php

namespace Plugins\Groups\Search;

use Hubzero\Plugin\Plugin;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Groups Plugin class for usage
 *
 */
class Search extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     * @phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore
     */
    protected $_autoloadLanguage = true;

    /**
     * Trigger onAddIndex to update search index.
     *
     * @param   object  $before  group object before any changes
     * @param   object  $group   group object including changes made during most recent save.
     * @return  void
     */
    public function onGroupAfterSave($before, $group)
    {
        $groupId = $group->gidNumber;

        include_once Component::path('com_groups') . '/models/orm/group.php';

        $ormGroup = Components\Groups\Models\Orm\Group::one($groupId);
        $attributes = $ormGroup->getAttributes();

        if ($ormGroup) {
            $table = $ormGroup->getTableName();
            Event::trigger('search.onAddIndex', array($table, $ormGroup));
        }
    }
}
