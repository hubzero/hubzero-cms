<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * System plugin for content events
 */

namespace Plugins\System\Content;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Event;

class Content extends Plugin
{
    /**
     * Hook for after parsing route
     *
     * @param   object  $table
     * @param   object  $model
     * @return  void
     */
    public function onContentSave($table, $model)
    {
        //@TODO: Add check for isIndexable
        Event::trigger('search.onAddIndex', [$table, $model]);
    }

    /**
     * Hook for after parsing route
     *
     * @param   object  $table
     * @param   \Hubzero\Database\Relational  $model
     * @return  void
     */
    public function onContentDestroy($table, $model)
    {
        //@TODO: Add check for isIndexable
        Event::trigger('search.onRemoveIndex', [$table, $model]);
    }
}
