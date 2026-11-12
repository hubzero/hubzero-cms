<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Admin\Controllers;

use Components\Storefront\Models\Sku;
use Hubzero\Component\AdminController;
use Components\Storefront\Models\Archive;
use Components\Storefront\Models\Product;
use Components\Storefront\Models\Warehouse;
use Components\Cart\Helpers\Download;
use Hubzero\Facades\Request;
use Hubzero\Facades\Config;
use Hubzero\Facades\Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\Date;
use Hubzero\Facades\Notify;

/**
 * Controller class for knowledge base categories
 */
class Skus extends AdminController
{
    /**
     * Display a list of all categories
     *
     * @return  void
     */
    public function displayTask()
    {
        // Get product ID
        $pId = Request::getInt('id');
        if (empty($pId)) {
            $pId = Request::getArray('pId', [0]);
        }
        $this->view->set('pId', $pId);

        // Get product
        $product = new Product($pId);
        $this->view->set('product', $product);

        // Get filters
        $filters = [
            // Get sorting variables
            'sort' => Request::getState(
                $this->option . '.' . $this->controller . '.sort',
                'filter_order',
                'title',
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
        ];

        $obj = new Archive();

        // Get record count
        $this->view->set('total', $obj->skus('count', $pId, $filters));

        // Get records
        $this->view->set('rows', $obj->skus('list', $pId, $filters));
        $this->view->set('filters', $filters);

        // Set any errors
        if ($this->getError()) {
            foreach ($this->getErrors() as $error) {
                $this->view->setError($error);
            }
        }

        // Output the HTML
        $this->view->display();
    }

    /**
     * Create a new category
     *
     * @return  void
     */
    public function addTask()
    {
        $this->editTask();
    }

    /**
     * Edit a SKU
     *
     * @param   object  $row
     * @return  void
     */
    public function editTask($row = null)
    {
        Request::setVar('hidemainmenu', 1);

        $obj = new Archive();

        if (is_object($row)) {
            $id = $row->getId();
            // If this is a new SKU, set product ID
            if (!$id) {
                $pId = Request::getInt('pId');
                $row->setProductId($pId);
            }
            $this->view->set('row', $row);
            $this->view->task = 'edit';
        } else {
            // Incoming
            $id = Request::getArray('id', [0]);

            if (is_array($id) && !empty($id)) {
                $id = $id[0];
            }

            // Get correct SKU instance
            $pId = Request::getInt('pId');

            if ($id) {
                $row = Sku::getInstance($id);
            } elseif ($pId) {
                // create new SKU
                $row = Sku::newInstance($pId);
            } else {
                throw new \Exception('SKU was not found');
            }
            $this->view->set('row', $row);
        }

        // Get product's info
        $pId = $row->getProductId();
        $warehouse = new Warehouse();
        $pInfo = $warehouse->getProductInfo($pId, true);
        $this->view->set('pInfo', $pInfo);
        //print_r($pInfo); die;

        // Get available product-defined option groups and options
        $this->view->set('allOptions', $obj->getProductOptions($pId));

        // Get current SKU options
        $this->view->set('options', $row->getOptions());

        // Get number of downloads
        $downloaded = Download::countSkuDownloads($id);
        $this->view->set('downloaded', $downloaded);

        // Set any errors
        foreach ($this->getErrors() as $error) {
            $this->view->setError($error);
        }

        // Output the HTML
        $this->view
            ->setLayout('edit')
            ->display();
    }

    /**
     * Save a category and come back to the edit form
     *
     * @return  void
     */
    public function applyTask()
    {
        $this->saveTask(false);
    }

    /**
     * Save a product
     *
     * @param   boolean  $redirect  Redirect the page after saving
     * @return  void
     */
    public function saveTask($redirect = true)
    {
        // Check for request forgeries
        Request::checkToken();

        // Incoming
        $fields = Request::getArray('fields', [], 'post');

        if (isset($fields['publish_up']) && $fields['publish_up'] != '') {
            $fields['publish_up'] = Date::of($fields['publish_up'], Config::get('offset'))->toSql();
        }
        if (isset($fields['publish_down']) && $fields['publish_down'] != '') {
            $fields['publish_down'] = Date::of($fields['publish_down'], Config::get('offset'))->toSql();
        }

        $pId = Request::getInt('pId');

        // Get the proper SKU
        if ($fields['sId']) {
            $sku = Sku::getInstance($fields['sId']);
        } elseif ($pId) {
            // create new SKU
            $sku = Sku::newInstance($pId);
        } else {
            Notify::error(Lang::txt('COM_STOREFRONT_NO_ID'));
            $this->cancelTask();
            return;
        }

        // Save SKU
        $obj = new Archive();
        try {
            $sku = $obj->updateSku($sku, $fields);
        } catch (\Exception $e) {
            Notify::error($e->getMessage());
            $this->editTask($sku);
            return;
        }

        if ($redirect) {
            // Redirect
            $url = 'index.php?option=' . $this->option . '&controller=' . $this->controller
                . '&task=display&id=' . Request::getInt('pId', 0);
            App::redirect(
                Route::url($url, false),
                Lang::txt('COM_STOREFRONT_SKU_SAVED'),
            );
        }
        Notify::success(Lang::txt('COM_STOREFRONT_SKU_SAVED'));

        $this->editTask($sku);
    }

    /**
     * Remove an entry
     *
     * @return  void
     */
    public function removeTask()
    {
        // Incoming
        $step = Request::getInt('step', 1);
        $step = (!$step) ? 1 : $step;

        $pId = Request::getInt('pId');

        // What step are we on?
        switch ($step) {
            case 1:
                Request::setVar('hidemainmenu', 1);

                // Incoming
                $id = Request::getArray('id', [0]);
                $this->view->set('sId', $id);

                $this->view->set('pId', $pId);

                // Set any errors
                if ($this->getError()) {
                    $this->view->setError($this->getError());
                }

                // Output the HTML
                $this->view->display();
                break;

            case 2:
                // Check for request forgeries
                Request::checkToken();

                // Incoming
                $sIds = array_filter(array_map('intval', Request::getArray('sId', [])));

                // Make sure we have an ID to work with
                if (empty($sIds)) {
                    App::redirect(
                        Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller, false),
                        Lang::txt('COM_STOREFRONT_NO_ID'),
                        'error',
                    );
                }

                $delete = Request::getInt('delete', 0);

                $msg = "Delete canceled";
                $type = 'error';
                if ($delete) {
                    // Do the delete
                    $obj = new Archive();

                    foreach ($sIds as $sId) {
                        // Delete SKU
                        try {
                            $sku = Sku::getInstance($sId);
                            $sku->delete();
                        } catch (\Exception $e) {
                            $url = 'index.php?option=' . $this->option . '&controller='
                                . $this->controller . '&task=display&id=' . $pId;
                            App::redirect(
                                Route::url($url, false),
                                $e->getMessage(),
                                $type,
                            );
                        }
                    }

                    $msg = "SKU(s) deleted";
                    $type = 'message';
                }

                // Set the redirect
                $url = 'index.php?option=' . $this->option . '&controller=' . $this->controller
                    . '&task=display&id=' . $pId;
                App::redirect(
                    Route::url($url, false),
                    $msg,
                    $type,
                );
        }
    }

    /**
     * Calls stateTask to publish entries
     *
     * @return  void
     */
    public function publishTask()
    {
        $this->stateTask(1);
    }

    /**
     * Calls stateTask to unpublish entries
     *
     * @return  void
     */
    public function unpublishTask()
    {
        $this->stateTask(0);
    }

    /**
     * Set the state of an entry
     *
     * @param   integer  $state  State to set
     * @return  void
     */
    public function stateTask($state = 0)
    {
        $ids = Request::getArray('id', []);

        $pId = Request::getInt('pId', 0);

        // Check for an ID
        if (count($ids) < 1) {
            $msg = ($state == 1
                ? Lang::txt('COM_STOREFRONT_SELECT_PUBLISH')
                : Lang::txt('COM_STOREFRONT_SELECT_UNPUBLISH'));
            App::redirect(
                Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller, false),
                $msg,
                'error',
            );
        }

        // Update record(s)
        $obj = new Archive();

        foreach ($ids as $sId) {
            // Save SKU
            try {
                $sku = Sku::getInstance($sId);
                $obj->updateSku($sku, ['state' => $state]);
            } catch (\Exception $e) {
                $error = true;
            }
        }

        // Set message
        switch ($state) {
            case '-1':
                $message = Lang::txt('COM_STOREFRONT_ARCHIVED', count($ids));
                break;
            case '1':
                $message = Lang::txt('COM_STOREFRONT_ITEMS_PUBLISHED', count($ids));
                break;
            default:
                // stateTask() is only given 1 or 0
                $message = Lang::txt('COM_STOREFRONT_ITEMS_UNPUBLISHED', count($ids));
                break;
        }

        $type = 'message';

        if (isset($error) && $error) {
            switch ($state) {
                case '1':
                    $action = 'published';
                    break;
                default:
                    $action = 'unpublished';
                    break;
            }

            $message = 'SKU could not be ' . $action;
            if (count($ids) > 1) {
                $message = 'Some SKUs could not be ' . $action;
            }
            $type = 'error';
        }

        // Redirect
        $url = 'index.php?option=' . $this->option . '&controller=' . $this->controller
            . '&task=display&id=' . $pId;
        App::redirect(
            Route::url($url, false),
            $message,
            $type,
        );
    }

    /**
     * Cancel a task (redirects to default task)
     *
     * @return  void
     */
    public function cancelTask()
    {
        // Set the redirect
        $url = 'index.php?option=' . $this->option . '&controller=' . $this->controller
            . '&task=display&id=' . Request::getInt('pId', 0);
        App::redirect(
            Route::url($url, false),
        );
    }
}
