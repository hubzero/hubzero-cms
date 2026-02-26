<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2020 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Installer\Admin\Models;

use Hubzero\Database\Relational;
use Filesystem;
use Hubzero\Form\Form;
use Hubzero\Facades\Lang;
use Hubzero\Config\Registry;
use Hubzero\Facades\User;
use Hubzero\Facades\Date;
use Hubzero\Facades\Component;

/**
 * Extension model
 */
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
class Custom_extensions extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     */
    protected $namespace = '';

    /**
     * The table primary key name
     *
     * It defaults to 'id', but can be overwritten by a subclass.
     *
     * @var  string
     **/
    protected $pk = 'extension_id';

    /**
     * The table name, non-standard naming
     *
     * @var  string
     */
    protected $table = '#__custom_extensions';

    /**
     * Default order by for model
     *
     * @var  string
     */
    public $orderBy = 'extension_id';

    /**
     * Default order direction for select queries
     *
     * @var  string
     */
    public $orderDir = 'asc';

    /**
     * Configuration registry
     *
     * @var  object
     */
    protected $paramsRegistry = null;

    /**
     * XML manifest
     *
     * @var  object
     */
    protected $manifest = null;

    /**
     * Fields and their validation criteria
     *
     * @var  array
     */
    protected $rules = array(
        'name'    => 'notempty',
        'alias'  => 'notempty',
        'type' => 'notempty'
    );

    /**
     * Sets up additional custom rules
     *
     * The alias and folder become part of the extension's path, which the
     * repository commands then work in, so only allow plain names (the form's
     * own check is client-side only)
     *
     * @return  void
     */
    public function setup()
    {
        foreach (array('alias', 'folder') as $key) {
            $this->addRule($key, function ($data) use ($key) {
                if (!empty($data[$key]) && !preg_match('/^[A-Za-z0-9][A-Za-z0-9_]*$/', $data[$key])) {
                    return $key . ' may only contain letters, numbers and underscores, and cannot start with an underscore';
                }
                return false;
            });
        }
    }

    /**
     * Automatically fillable fields
     *
     * @var  array
     **/
    public $always = array(
        'path',
        'modified',
        'modified_by',
        'created',
        'created_by'
    );

    /**
     * Delete the existing/current model
     *
     * @return  bool
     */
    public function destroy()
    {
        return parent::destroy();
    }

    /**
     * Publsh an entry
     *
     * @return  bool
     */
    public function publish()
    {
        $this->set('enabled', self::STATE_PUBLISHED);

        return $this->save();
    }

    /**
     * Unpublish an entry
     *
     * @return  bool
     */
    public function unpublish()
    {
        if ($this->get('type') == 'template') {
            if (is_file(Component::path('com_templates') . '/models/style.php')) {
                $style = \Components\Templates\Models\Style::all()
                    ->whereEquals('template', $this->get('element'))
                    ->whereEquals('client_id', $this->get('client_id'))
                    ->whereEquals('home', 1)
                    ->row();

                if ($style && $style->get('id')) {
                    $this->addError(Lang::txt('COM_INSTALLER_ERROR_DISABLE_DEFAULT_TEMPLATE_NOT_PERMITTED'));
                    return false;
                }
            }
        }

        $this->set('enabled', self::STATE_UNPUBLISHED);

        return $this->save();
    }

    /**
     * Get params as a Registry object
     *
     * @return  object
     */
    public function transformParams()
    {
        if (!($this->paramsRegistry instanceof Registry)) {
            $this->paramsRegistry = new Registry($this->get('params'));
        }
        return $this->paramsRegistry;
    }

    /**
     * Generates automatic modified field value
     *
     * @param   array   $data  the data being saved
     * @return  string
     */
    public function automaticModified($data)
    {
        // A loaded row always carries its last modified date, so stamp every
        // save rather than only filling in a missing value
        return Date::of('now')->toSql();
    }

    /**
     * Generates automatic modified by field value
     *
     * @return  int
     */
    public function automaticModifiedBy($data)
    {
        return User::get('id');
    }

    /**
     * Generates automatic modified by field value
     *
     * @return  int
     */
    public function automaticPath($data)
    {
        if (!isset($data['path']) || !$data['path']) {
            $data['path'] = 'Unknown';
        }
        return $data['path'];
    }
}
