<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Storefront\Models\Sku;
use Components\Storefront\Helpers\Serials as SerialsHelper;
use Hubzero\Facades\Request;
use Hubzero\Facades\Config;
use Hubzero\Facades\Route;
use Hubzero\Facades\App;

/**
 * Controller class for knowledge base categories
 */
class Serials extends AdminController
{
    /**
     * Display a list of all users
     *
     * @return  void
     */
    public function displayTask()
    {
        // Get SKU ID
        $sId = Request::getInt('sId');
        $this->view->set('sId', $sId);

        // Get SKU
        $sku = Sku::getInstance($sId);
        $this->view->set('sku', $sku);

        // Get filters
        $this->view->set('filters', [
            // Get sorting variables
            'sort' => Request::getState(
                $this->option . '.' . $this->controller . '.sort',
                'filter_order',
                'srId',
            ),
            'sort_Dir' => Request::getState(
                $this->option . '.' . $this->controller . '.sortdir',
                'filter_order_Dir',
                'ASC',
            ),
            // Get paging variables
            'limit' => Request::getState(
                $this->option . '.' . $this->controller . '.limit',
                'limit',
                Config::get('list_limit'),
                'int',
            ),
            'start' => Request::getState(
                $this->option . '.' . $this->controller . '.limitstart',
                'limitstart',
                0,
                'int',
            ),
        ]);
        //print_r($this->view->filters); die;

        // Get record count
        $viewFilters = $this->view->get('filters');
        $viewFilters['return'] = 'count';
        $this->view->set('filters', $viewFilters);
        $this->view->set('total', SerialsHelper::getSkuSerials($this->view->get('filters'), $sId));

        // Get records
        $viewFilters = $this->view->get('filters');
        $viewFilters['return'] = 'list';
        $this->view->set('filters', $viewFilters);
        $this->view->set('rows', SerialsHelper::getSkuSerials($this->view->filters, $sId));

        // Output the HTML
        $this->view->display();
    }

    /**
     * Cancel a task (redirects to default task)
     *
     * @return     void
     */
    public function cancelTask()
    {
        // Set the redirect
        $url = 'index.php?option=' . $this->option
            . '&controller=skus&task=edit&id=' . Request::getInt('sId', 0);
        App::redirect(
            Route::url($url, false),
        );
    }

    /**
     * Remove an entry
     *
     * @return  void
     */
    public function removeTask()
    {
        // Check for request forgeries
        Request::checkToken();

        // Incoming (the list view posts srId[] checkboxes)
        $ids = array_map('intval', (array) Request::getArray('srId', []));
        $sId = Request::getInt('sId');

        $msg  = '';
        $type = 'message';

        $deletedMessage = SerialsHelper::delete($ids);

        if ($deletedMessage) {
            $msg = $deletedMessage->message;
            $type = $deletedMessage->type;
        }

        $url = 'index.php?option=' . $this->option . '&controller=' . $this->controller
            . '&task=display&sId=' . $sId;
        App::redirect(
            Route::url($url, false),
            $msg,
            $type,
        );
    }

    /**
     * Create an entry
     *
     * @return  void
     */
    public function newTask()
    {
        $sId = Request::getInt('sId', 0);
        $this->view->set('sId', $sId);

        // Set any errors
        foreach ($this->getErrors() as $error) {
            $this->view->setError($error);
        }

        // Output the HTML
        $this->view->display();
    }

    /**
     * Add serials
     *
     * @return  void
     */
    public function addserialsTask()
    {
        // Check for request forgeries
        Request::checkToken();

        $sId = Request::getInt('sId', '');
        $serials = Request::getString('serials', '');

        $serials = explode(',', $serials);
        foreach ($serials as $serial) {
            \Components\Storefront\Helpers\Serials::add($serial, $sId);
        }
    }

    /**
     * Upload
     *
     * @return  void
     */
    public function uploadTask()
    {
        $sId = Request::getInt('sId', 0);
        $this->view->set('sId', $sId);

        // Output the HTML
        $this->view->display();
    }

    /**
     * Upload a CSV file
     *
     * @return  void
     */
    public function uploadcsvTask()
    {
        // Check for request forgeries
        Request::checkToken();

        // See if we have a file
        $csvFile = Request::getArray('csvFile', false, 'files');

        $sId = Request::getInt('sId', 0);

        if (isset($csvFile['name']) && $csvFile['name'] && $csvFile['type'] == 'text/csv') {
            if (($handle = fopen($csvFile['tmp_name'], "r")) !== false) {
                $inserted = 0;
                $skipped = [];
                $ignored = [];

                while (($line = fgetcsv($handle, 1000, ",")) !== false) {
                    if (!empty($line[0])) {
                        $serial = trim($line[0]);

                        $res = SerialsHelper::add($serial, $sId);
                        if ($res) {
                            $inserted++;
                        } else {
                            $skipped[] = $serial;
                        }
                    }
                }
                fclose($handle);

                $this->view->set('inserted', $inserted);
                $this->view->set('skipped', $skipped);
                $this->view->set('ignored', $ignored);
            } else {
                $this->view->setError('Could not read the file.');
            }
        } else {
            $this->view->setError(
                'No file or bad file was uploaded. Please make sure you upload the CSV formated file.',
            );
        }

        // Output the HTML
        $this->view->set('sId', $sId);
        $this->view->display();
    }
}
