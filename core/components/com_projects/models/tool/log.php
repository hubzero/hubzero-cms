<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Tool;

use Hubzero\Base\Model;
use Components\Projects\Tables;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\User;

/**
 * Project Tool Log model
 */
class Log extends Model
{
    /**
     * @var  \Hubzero\User\User|null
     */
    protected $actor = null;
    /**
     * @var  mixed
     */
    protected $creator;

    /**
     * Table class name
     *
     * @var  class-string<\Components\Projects\Tables\ToolLog>
     */
    protected $tableClass = '\\Components\\Projects\\Tables\\ToolLog';

    /**
     * Table, an instance of $tableClass
     *
     * @var \Components\Projects\Tables\ToolLog
     */
    protected $table;

    /**
     * Registry
     *
     * @var  object
     */
    public $config;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $this->db = App::get('db');

        $this->table = new Tables\ToolLog($this->db);
    }

    /**
     * Returns a reference to a log model
     *
     * @param   int|string|\stdClass|array<string,mixed>|null  $oid  object ID
     * @return  self
     */
    public static function &getInstance($oid = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        if ($oid instanceof \stdClass) {
            $key = $oid->id;
        } elseif (is_array($oid)) {
            $key = $oid['id'];
        } else {
            $key = $oid;
        }

        if (!isset($instances[$key])) {
            $instances[$key] = new self();
        }

        return $instances[$key];
    }

    /**
     * Update Parent Name
     *
     * @param   integer|null  $id
     * @param   string|null   $name
     * @return  boolean
     */
    public function updateParentName($id = null, $name = null)
    {
        if (!$id || !$name) {
            return false;
        }
        return $this->table->updateParentName($id, $name);
    }

    /**
     * getLastUpdate
     *
     * @param   boolean  $statusChange
     * @return  \stdClass|null
     */
    public function getLastUpdate($statusChange = false)
    {
        return $this->table->getLastUpdate(
            $this->get('instance_id'),
            $this->get('parent_name'),
            $this->get('parent_id'),
            $statusChange,
        );
    }

    /**
     * Get item history
     *
     *
     * @param   array<mixed>   $filters
     * @return  array<mixed>|false|null  The history rows
     */
    public function getHistory($filters = [])
    {
        return $this->table->getHistory(
            $this->get('parent_name'),
            $this->get('parent_id'),
            $this->get('instance_id'),
            $filters,
        );
    }

    /**
     * Determine changes
     *
     * @param   \Components\Projects\Models\Tool  $old  Tool model
     * @param   \Components\Projects\Models\Tool  $new  Tool model
     * @return  string
     */
    public function whatChanged($old, $new)
    {
        $log = '';

        // Name change
        if ($old->get('name') != $new->get('name')) {
            $log .= strtolower(Lang::txt('PLG_PROJECTS_TOOLS_NAME'));
            $log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_CHANGED');
            $log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_FROM');
            $log .= ' &ldquo;' . $old->get('name') . '&rdquo; ';
            $log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_TO');
            $log .= ' &ldquo;' . $new->get('name') . '&rdquo; ';
            $log .= "\n";
        }

        // Title change
        if ($old->get('title') != $new->get('title')) {
            $log .= strtolower(Lang::txt('PLG_PROJECTS_TOOLS_TITLE'));
            $log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_CHANGED');
            $log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_FROM');
            $log .= ' &ldquo;' . $old->get('title') . '&rdquo; ';
            $log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_TO');
            $log .= ' &ldquo;' . $new->get('title') . '&rdquo; ';
            $log .= "\n";
        }

        // Dev area access change
        if ($old->get('opendev') != $new->get('opendev')) {
            $log .= strtolower(Lang::txt('PLG_PROJECTS_TOOLS_DEV_AREA_ACCESS'));
            $log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_CHANGED');
            $log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_FROM') . ' ';
            $log .= $old->get('opendev')
                ? Lang::txt('PLG_PROJECTS_TOOLS_ACCESS_SHARED')
                : Lang::txt('PLG_PROJECTS_TOOLS_ACCESS_RESTRICTED');
            $log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_TO') . ' ';
            $log .= $new->get('opendev') == 1
                ? Lang::txt('PLG_PROJECTS_TOOLS_ACCESS_SHARED')
                : Lang::txt('PLG_PROJECTS_TOOLS_ACCESS_RESTRICTED');
            $log .= "\n";
        }

        return $log;
    }

    /**
     * Get the actor
     *
     * Accepts an optional property name. If provided
     * it will return that property value. Otherwise,
     * it returns the entire User object
     *
     * @param   string|null  $property
     * @return  mixed
     */
    public function actor($property = null)
    {
        if (!isset($this->actor) || !($this->creator instanceof \Hubzero\User\User)) {
            $this->actor = User::getInstance($this->get('actor'));
        }
        if ($property) {
            return $this->actor->get($property);
        }
        return $this->actor;
    }
}
