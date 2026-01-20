<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Admin\Controllers;

use Hubzero\Component\AdminController;
use Route;
use Lang;
use App;

/**
 * Controller class for system config
 */
class Ldap extends AdminController
{
    /**
     * Default view
     *
     * @return  void
     */
    public function displayTask()
    {
        // Output the HTML
        $this->view
            ->set('config', $this->config)
            ->display();
    }

    /**
     * Delete LDAP group entries
     *
     * @return  void
     */
    public function deleteGroupsTask()
    {
        $result = \Hubzero\Utility\Ldap::deleteAllGroups();

        //Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_RESULT_UNKNOWN'));

        if (isset($result['errors']) && isset($result['fatal']) && !empty($result['fatal'][0])) {
            Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_EXPORT_FAILED', $result['fatal'][0]));
        } elseif (isset($result['errors']) && isset($result['warning']) && !empty($result['warning'][0])) {
            Notify::warning(Lang::txt('COM_SYSTEM_LDAP_WARNING_COMPLETED_WITH_ERRORS', count($result['warning'])));
        } elseif (isset($result['success'])) {
            Notify::info(Lang::txt('COM_SYSTEM_LDAP_GROUP_ENTRIES_DELETED', $result['deleted']));
        }

        App::redirect(
            Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
        );
    }

    /**
     * Delete LDAP user entries
     *
     * @return  void
     */
    public function deleteUsersTask()
    {
        $result = \Hubzero\Utility\Ldap::deleteAllUsers();

        //Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_RESULT_UNKNOWN'));

        if (isset($result['errors']) && isset($result['fatal']) && !empty($result['fatal'][0])) {
            Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_EXPORT_FAILED', $result['fatal'][0]));
        } elseif (isset($result['errors']) && isset($result['warning']) && !empty($result['warning'][0])) {
            Notify::warning(Lang::txt('COM_SYSTEM_LDAP_WARNING_COMPLETED_WITH_ERRORS', count($result['warning'])));
        } elseif (isset($result['success'])) {
            Notify::info(Lang::txt('COM_SYSTEM_LDAP_USER_ENTRIES_DELETED', $result['deleted']));
        }

        $this->cancelTask();
    }

    /**
     * Export all groups to LDAP
     *
     * @return  void
     */
    public function exportGroupsTask()
    {
        $result = \Hubzero\Utility\Ldap::syncAllGroups();

        //Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_RESULT_UNKNOWN'));

        if (isset($result['errors']) && isset($result['fatal']) && !empty($result['fatal'][0])) {
            Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_EXPORT_FAILED', $result['fatal'][0]));
        } elseif (isset($result['errors']) && isset($result['warning']) && !empty($result['warning'][0])) {
            Notify::warning(Lang::txt('COM_SYSTEM_LDAP_WARNING_COMPLETED_WITH_ERRORS', count($result['warning'])));
        } elseif (isset($result['success'])) {
            Notify::info(Lang::txt(
                'COM_SYSTEM_LDAP_GROUPS_EXPORTED',
                $result['added'],
                $result['modified'],
                $result['deleted'],
                $result['unchanged']
            ));
        }

        $this->cancelTask();
    }

    /**
     * Delete LDAP user entries
     *
     * @return  void
     */
    public function exportUsersTask()
    {
        $result = \Hubzero\Utility\Ldap::syncAllUsers();

        //Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_RESULT_UNKNOWN'));

        if (isset($result['errors']) && isset($result['fatal']) && !empty($result['fatal'][0])) {
            Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_EXPORT_FAILED', $result['fatal'][0]));
        } elseif (isset($result['errors']) && isset($result['warning']) && !empty($result['warning'][0])) {
            Notify::warning(Lang::txt('COM_SYSTEM_LDAP_WARNING_COMPLETED_WITH_ERRORS', count($result['warning'])));
        } elseif (isset($result['success'])) {
            Notify::info(Lang::txt(
                'COM_SYSTEM_LDAP_USERS_EXPORTED',
                $result['added'],
                $result['modified'],
                $result['deleted'],
                $result['unchanged']
            ));
        }

        $this->cancelTask();
    }

    /**
     * Sync users by batching them
     *
     * @return  void
     */
    public function exportUsersBatchTask()
    {
        $start = Request::getInt('start', 0);
        $limit = Request::getInt('limit', 1000);

        $response = new \stdClass();
        $response->processed = 0;
        $response->total     = 0;
        $response->start     = ($start + $limit);
        $response->success   = array();
        $response->errors    = array();

        $db = \App::get('db');
        $query = $db->getQuery()
            ->select('COUNT(id)')
            ->from('#__users');
        $db->setQuery($query->toString());

        $response->total = $db->loadResult();

        $query = $db->getQuery()
            ->select('id')
            ->from('#__users')
            ->order('id', 'desc')
            ->start($start)
            ->limit($limit);
        $db->setQuery($query->toString());

        $result = $db->loadColumn();

        if ($result) {
            foreach ($result as $row) {
                try {
                    \Hubzero\Utility\Ldap::syncUser($row);
                } catch (\Exception $e) {
                    $response->errors[] = 'User ID #' . $row . ': ' . $e->getMessage();
                }

                $response->processed++;
            }
        }

        // The following properties are currently private
        // @TODO: Make them public or add accessor methods
        //$response->errors  = \Hubzero\Utility\Ldap::$errors;
        //$response->success = \Hubzero\Utility\Ldap::$success;

        echo json_encode($response);
        exit();
    }
}
