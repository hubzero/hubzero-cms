<?php

/**
 * Spreadsheet view controller for the Dataviewer component.
 *
 * Renders the interactive DataTables spreadsheet view with
 * charts, maps, and customizer panels.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Controllers;

use Components\Dataviewer\Site\DvConfig;
use Components\Dataviewer\Site\Helpers\ConfigFactory;
use Components\Dataviewer\Site\Helpers\DataQuery;
use Components\Dataviewer\Site\Helpers\ModeInterface;
use Hubzero\Component\SiteController;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;
use Hubzero\Facades\Pathway;

class Spreadsheet extends SiteController
{
    /**
     * View engines this controller accepts.
     *
     * @var  array
     */
    protected $viewEngines = ['blade', 'php'];

    /**
     * CSS frameworks this controller accepts.
     *
     * @var  array
     */
    protected $cssFrameworks = ['daisyui', 'classic'];

    /**
     * Database identifier parsed from request.
     *
     * @var  array
     */
    protected $dbId = [];

    /**
     * Mode instance.
     *
     * @var  ModeInterface
     */
    protected $mode;

    /**
     * Component config array.
     *
     * @var  array
     */
    protected $dvConfig = [];

    /**
     * Execute the controller.
     *
     * @return  void
     */
    public function execute()
    {
        // Map legacy 'view' task to the default display task
        $this->registerTask('view', 'display');

        // Build config and resolve mode
        $this->dvConfig = ConfigFactory::build();
        $this->dbId = ConfigFactory::parseDbParam(
            Request::getString('db', '')
        );
        $this->dvConfig['settings']['db_id'] = $this->dbId;
        $this->mode = ConfigFactory::resolveMode($this->dbId);
        $this->mode->getConfig($this->dbId, $this->dvConfig);

        // Sync to DvConfig for backward compat with legacy code
        DvConfig::$dv_conf = $this->dvConfig;

        parent::execute();
    }

    /**
     * Display the spreadsheet view (default task).
     *
     * @return  void
     */
    public function displayTask()
    {
        $dd = $this->mode->getDataDefinition(
            $this->dbId,
            $this->dvConfig
        );

        // Sync config back after mode may have modified it
        DvConfig::$dv_conf = $this->dvConfig;

        if (!$dd) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('COM_DATAVIEWER_ERROR_INVALID_DATAVIEW'));
            return;
        }

        if (!$this->authorize($dd)) {
            return;
        }

        // Set breadcrumbs
        $this->mode->setPathway($dd);

        // Build settings for the JS layer
        $comName = $this->dvConfig['com_name'];
        $settings = $this->dvConfig['settings'];
        $settings['view']['id'] = $dd['dv_id'];
        $settings['view']['type'] = 'spreadsheet';
        $settings['data_url'] = 'index.php?option=com_' . $comName
            . '&task=data&db=' . $dd['db_id']['id'] . '&dv=' . $dd['dv_id'];
        $settings['view_url'] = '/dataviewer/view/' . $dd['db_id']['id']
            . '/' . $dd['dv_id'] . '/';

        // Record IDs
        $rec_ids = Request::getString('id', '');
        if ($rec_ids != '') {
            $settings['data_url'] .= '&id=' . htmlentities($rec_ids);
            $settings['view_url'] .= '?id=' . htmlentities($rec_ids);
        } else {
            $settings['view_url'] .= '?dv_first=1';
        }

        // Version
        $version = isset($dd['version']) ? '&v=' . $dd['version'] : '';
        $settings['data_url'] .= $version;
        $settings['view_url'] .= $version;

        // Filter options
        $settings['show_filter_options'] = isset($dd['filter_options'])
            ? $dd['filter_options']
            : true;

        // Custom field URL
        $custom_field = Request::getString('custom_field', false);
        $custom_field_url = $custom_field ? '&custom_field=' . $custom_field : '';

        // Custom view URL
        $custom_view = Request::getString('custom_view', false);
        $custom_view_url = $custom_view ? '&custom_view=' . $custom_view : '';

        // Overrides from data definition
        $settings['limit'] = isset($dd['display_limit'])
            ? $dd['display_limit']
            : $settings['limit'];
        $settings['hide_data'] = isset($dd['hide_data']);
        $settings['serverside'] = isset($dd['serverside']) && $dd['serverside'];

        // Customizer
        if (!isset($dd['customizer']) && isset($this->dvConfig['customizer'])) {
            $dd['customizer'] = $this->dvConfig['customizer'];
        }
        if (isset($dd['customizer']) && $dd['customizer'] === false) {
            unset($dd['customizer']);
        }

        // Build query and get results
        $dbConfig = isset($dd['db']) ? $dd['db'] : $this->dvConfig['db'];
        $driver = DataQuery::createDriver($dbConfig);
        $query = new DataQuery($driver, $settings['limit']);
        $sql = $query->buildQuery($dd);
        $res = $query->execute($sql, $dd);

        // Get JSON-filtered data
        $f_data = \Components\Dataviewer\Site\Filter\Json::filter($res, $dd);
        $d_arr = json_decode($f_data, true);

        // Customizer state
        $show_customizer = true;
        $hide_str = '';
        $group_by = '';

        if (isset($dd['customizer'])) {
            if (isset($dd['customizer']['show_table']) && !$dd['customizer']['show_table']) {
                $hide_str = 'display: none;';
            }
            if (isset($dd['customizer']['show_customizer']) && !$dd['customizer']['show_customizer']) {
                $show_customizer = false;
            }
            if (isset($dd['group_by'])) {
                $arr = explode(',', $dd['group_by']);
                foreach ($arr as $a) {
                    $a = trim($a);
                    $lbl = isset($dd['cols'][$a]['label']) ? $dd['cols'][$a]['label'] : $a;
                    $group_by .= '<div class="dv_customizer_group_by_item_div"'
                        . ' style="padding: 3px; margin: 5px; border: 1px #EEE solid;">'
                        . '<input type="checkbox" checked="checked"'
                        . ' class="dv_customizer_group_by_item" value="' . e($a) . '" />'
                        . ' &nbsp;<label style="cursor: pointer;">'
                        . str_replace('<br />', ' ', $lbl) . '</label></div>';
                }
            }
        }

        // Help file
        $help_file = false;
        $name = $dd['dv_id'];
        if (isset($this->dvConfig['help_file_base_path'])) {
            $candidate = $this->dvConfig['help_file_base_path'] . $name . '/' . $name . '-help.html';
            if (file_exists(PATH_ROOT . $candidate)) {
                $help_file = $candidate;
            }
        }

        // Return link
        $return = '';
        if (isset($dd['return']) && isset($dd['return']['raw'])) {
            $return = $dd['return']['raw'];
        } elseif (isset($dd['return'])) {
            $return = '<span id="dv_return_link"'
                . ' style="font-size: 1.1em; margin-left: 10px; padding-top: 12px;">'
                . '<a href="' . e($dd['return']['url']) . '"><strong>'
                . e($dd['return']['label']) . '</strong></a></span>';
        }

        // Filtered views
        $filter = Request::getVar('filter', false);
        $filtered_view = [];
        if ($filter !== false) {
            $settings['data_url'] .= '&filter=' . $filter;
            $settings['filters']['fv_vals'] = $filter;
            $ff = explode('||', $filter);
            foreach ($ff as $f) {
                $parts = explode('|', $f);
                $filtered_view[$parts[0]] = $parts[1];
            }
        }

        // Filter dialog visibility
        $dv_show_filters = false;
        $path = explode('/', Request::path());
        if (isset($path[5]) && $path[5] == 'filter_dialog') {
            $dv_show_filters = true;
        }
        if (Request::getString('show_filters', 'false') === 'true') {
            $dv_show_filters = true;
        }

        // Store settings back for backward compat with legacy code
        $this->dvConfig['settings'] = $settings;
        DvConfig::$dv_conf = $this->dvConfig;

        // Show maps flag
        $showMaps = isset($dd['show_maps']);

        // Load Google Maps API if needed
        if ($showMaps) {
            $document = \Hubzero\Facades\App::get('document');
            $document->addScript('//maps.google.com/maps/api/js?sensor=false');
        }

        // Build customizer column lists
        $full_list = '';
        $selected_list = '';
        if (isset($dd['customizer'])) {
            foreach ($dd['cols'] as $id => $prop) {
                if (!isset($prop['hide']) || (isset($prop['hide']) && $prop['hide'] != 'hide')) {
                    $col_label = isset($prop['label']) ? $prop['label'] : $id;
                    $col_label = str_replace('<br />', ' ', $col_label);
                    $col_label = str_replace('<hr />', '&nbsp/&nbsp', $col_label);

                    if (isset($prop['units']) && $prop['units'] != '') {
                        $col_label .= ' <small>[' . $prop['units'] . ']</small>';
                    } elseif (isset($prop['unit']) && $prop['unit'] != '') {
                        $col_label .= ' <small>[' . $prop['unit'] . ']</small>';
                    }

                    if (
                        isset($dd['customizer']['selected'])
                        && in_array($id, $dd['customizer']['selected'])
                    ) {
                        $selected_list .= '<li data-dv-id="' . e($id) . '">' . $col_label . '</li>';
                    } else {
                        $full_list .= '<li data-dv-id="' . e($id) . '">' . $col_label . '</li>';
                    }
                }
            }
        }

        // Pass all data to the view
        $this->view
            ->set('dd', $dd)
            ->set('settings', $settings)
            ->set('f_data', $f_data)
            ->set('d_arr', $d_arr)
            ->set('show_customizer', $show_customizer)
            ->set('hide_str', $hide_str)
            ->set('group_by', $group_by)
            ->set('help_file', $help_file)
            ->set('return_link', $return)
            ->set('filtered_view', $filtered_view)
            ->set('dv_show_filters', $dv_show_filters)
            ->set('showMaps', $showMaps)
            ->set('showCharts', isset($dd['custom_charts']) || isset($dd['charts_list']))
            ->set('full_list', $full_list)
            ->set('selected_list', $selected_list)
            ->set('custom_field_url', $custom_field_url)
            ->set('custom_view_url', $custom_view_url)
            ->set('htmlPath', $this->dvConfig['html_path'])
            ->display();
    }

    /**
     * Check user authorization for a data definition.
     *
     * @param   array  $dd  Data definition with ACL config
     * @return  bool
     */
    protected function authorize($dd)
    {
        $allowedUsers = $dd['acl']['allowed_users'] ?? null;
        $isValid = is_array($allowedUsers)
            || $allowedUsers === false
            || $allowedUsers == 'registered';
        if (isset($allowedUsers) && $isValid) {
            $this->dvConfig['acl']['allowed_users'] = $allowedUsers;
        }

        $allowedGroups = $dd['acl']['allowed_groups'] ?? null;
        if (
            isset($allowedGroups)
            && (is_array($allowedGroups) || $allowedGroups === false)
        ) {
            $this->dvConfig['acl']['allowed_groups'] = $allowedGroups;
        }

        $usersAllowed = $this->dvConfig['acl']['allowed_users'];
        $groupsAllowed = $this->dvConfig['acl']['allowed_groups'];

        // Public access
        if ($usersAllowed === false && $groupsAllowed === false || isset($dd['acl']['public'])) {
            return true;
        }

        // Guest — redirect to login
        if (User::isGuest()) {
            $return = base64_encode($_SERVER['REQUEST_URI']);
            $this->setRedirect(
                \Hubzero\Facades\Route::url('index.php?option=com_login&return=' . $return),
                '',
                'warning'
            );
            return false;
        }

        // Registered users
        if (isset($dd['acl']['registered'])) {
            return true;
        }

        if ($usersAllowed == 'registered') {
            return true;
        }

        // Specific users
        if (is_array($usersAllowed)) {
            if (in_array(User::get('username'), $usersAllowed)) {
                return true;
            }
        }

        // Specific groups
        if ($groupsAllowed !== false && is_array($groupsAllowed)) {
            $groups = \Hubzero\User\Helper::getGroups(User::get('id'));
            if ($groups && count($groups)) {
                foreach ($groups as $g) {
                    if (in_array($g->cn, $groupsAllowed)) {
                        return true;
                    }
                }
            }
        }

        $this->view
            ->set('authorized', false)
            ->setLayout('unauthorized');

        return false;
    }
}
