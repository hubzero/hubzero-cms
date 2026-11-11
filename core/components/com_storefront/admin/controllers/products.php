<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Storefront\Models\Archive;
use Components\Storefront\Models\Warehouse;
use Components\Storefront\Models\Product;
use Hubzero\Html\Builder\Access;
use Components\Cart\Helpers\Download;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\Config;
use Hubzero\Facades\Date;
use Hubzero\Facades\Notify;

/**
 * Controller class for knowledge base categories
 */
class Products extends AdminController
{
    /**
     * Display a list of all categories
     *
     * @return  void
     */
    public function displayTask()
    {
        // Get filters
        $this->view->filters = [
            // Get sorting variables
            'search' => Request::getState(
                $this->option . '.' . $this->controller . '.search',
                'search',
                '',
            ),
            'type' => Request::getState(
                $this->option . '.' . $this->controller . '.type',
                'type',
                -1,
                'int',
            ),
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
        $this->view->total = $obj->products('count', $this->view->filters);

        // Get records
        $this->view->rows = $obj->products('list', $this->view->filters);

        // For all records here get SKUs
        $skus = new \stdClass();
        $warehouse = new Warehouse();
        foreach ($this->view->rows as $r) {
            $key = $r->pId;
            $allSkus = $warehouse->getProductSkus($r->pId, 'all', false);

            // Count how many active and how many inactive SKUs there are
            $skuCounter = new \stdClass();
            $skuCounter->active = 0;
            $skuCounter->inactive = 0;
            foreach ($allSkus as $skuInfo) {
                if ($skuInfo->sActive) {
                    $skuCounter->active++;
                } else {
                    $skuCounter->inactive++;
                }
            }
            $skus->$key = $skuCounter;
        }

        $this->view->skus = $skus;

        // access groups
        $accessGroups = [];
        if ($this->config->get('productAccess')) {
            $ag = \Hubzero\Access\Group::all()->rows();
            $accessGroups[0] = 'None';
            foreach ($ag as $obj) {
                $accessGroups[$obj->get('id')] = $obj->get('title');
            }
        } else {
            $ag = Access::assetgroups();
            $accessGroups[0] = 'All';
            foreach ($ag as $obj) {
                $accessGroups[$obj->value] = $obj->text;
            }
        }
        $this->view->ag = $accessGroups;

        $db = App::get('db');
        $db->setQuery("SELECT * FROM `#__storefront_product_types`");
        $this->view->types = $db->loadObjectList();

        // Output the HTML
        $this->view
            ->set('config', $this->config)
            ->display();
    }

    /**
     * Create a new entry
     *
     * @return  void
     */
    public function addTask()
    {
        $this->editTask();
    }

    /**
     * Edit an entry
     *
     * @param   object  $row
     * @return  void
     */
    public function editTask($row = null)
    {
        Request::setVar('hidemainmenu', 1);

        $obj = new Archive();
        // Get types
        $this->view->types = $obj->getProductTypes();

        // Get collections
        $this->view->collections = $obj->collections('list', ['sort' => 'cType']);

        // Get all option groups
        $this->view->optionGroups = $obj->optionGroups('list', ['sort' => 'ogName']);

        if (is_object($row)) {
            $id = $row->getId();
            $this->view->row = $row;
            $this->view->task = 'edit';
        } else {
            // Incoming
            $id = Request::getArray('id', [0]);

            if (is_array($id) && !empty($id)) {
                $id = $id[0];
            }

            // Load product
            $this->view->row = $obj->product($id);
        }

        // Get product option groups
        $this->view->productOptionGroups = $this->view->row->getOptionGroups();
        $this->view->config = $this->config;

        // Check if meta is needed for this product
        $pType = null;
        if ($this->view->row->getTypeInfo()) {
            $pType = $this->view->row->getTypeInfo()->name;
        }
        $this->view->metaNeeded = false;
        // Only software needs meta
        if ($pType == 'Software Download') {
            $this->view->metaNeeded = true;

            // Get number of downloads
            $downloaded = Download::countProductDownloads($id);
            $this->view->downloaded = $downloaded;
        }

        // Get meta
        $meta = $this->view->row->getMeta();
        $this->view->meta = $meta;

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
     * Save an entry and return to the edit form
     *
     * @return  void
     */
    public function applyTask()
    {
        $this->saveTask(false);
    }

    /**
     * Save an entry
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

        $obj = new Archive();

        // Save product (if loading it fails, the edit form reloads it from the request)
        $product = null;
        try {
            $product = new Product($fields['pId']);

            if (isset($fields['pName'])) {
                $product->setName($fields['pName']);
            }
            if (isset($fields['pAlias']) && $fields['pAlias']) {
                $product->setAlias($fields['pAlias']);
            }
            if (isset($fields['pDescription'])) {
                $product->setDescription($fields['pDescription']);
            }
            if (isset($fields['pFeatures'])) {
                $product->setFeatures($fields['pFeatures']);
            }
            if (isset($fields['pTagline']) && $fields['pTagline']) {
                $product->setTagline($fields['pTagline']);
            }
            if (isset($fields['access'])) {
                $product->setAccessLevel($fields['access']);
            }
            if (isset($fields['state'])) {
                $product->setActiveStatus($fields['state']);
            }
            if (isset($fields['ptId'])) {
                $product->setType($fields['ptId']);
            }
            if (isset($fields['pAllowMultiple'])) {
                $product->setAllowMultiple($fields['pAllowMultiple']);
            }

            if (!isset($fields['collections'])) {
                $fields['collections'] = [];
            }
            $product->setCollections($fields['collections']);

            if (!isset($fields['optionGroups'])) {
                $fields['optionGroups'] = [];
            }
            $product->setOptionGroups($fields['optionGroups']);
            $product->setPublishTime($fields['publish_up'], $fields['publish_down']);
            $product->save();

            // Meta is written straight to its table keyed by pId, so it has to
            // wait until save() has given a new product its id (before, every
            // new product failed: pId '' is not an integer on strict SQL)
            if (isset($fields['pQtyTxt'])) {
                $product->setMeta(['qtyTxt' => $fields['pQtyTxt']]);
            }

            $accessgroups = Request::getArray('accessgroupsyes', [], 'post');
            $product->setAccessGroups($accessgroups, 'include');

            $accessgroups = Request::getArray('accessgroupsno', [], 'post');
            $product->setAccessGroups($accessgroups, 'exclude');
        } catch (\Exception $e) {
            Notify::error($e->getMessage());
            // Get the product
            //$product = $obj->product($fields['pId']);
            $this->editTask($product);
            return;
        }

        $warnings = $product->getMessages();

        if ($warnings && !$redirect) {
            foreach ($warnings as $warning) {
                Notify::warning($warning);
            }
        }

        if ($redirect) {
            App::redirect(
                Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller),
                Lang::txt('COM_STOREFRONT_PRODUCT_SAVED'),
            );
        }
        Notify::success(Lang::txt('COM_STOREFRONT_PRODUCT_SAVED'));

        $this->editTask($product);
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

        // What step are we on?
        switch ($step) {
            case 1:
                Request::setVar('hidemainmenu', 1);

                // Incoming
                $id = Request::getArray('id', []);
                $this->view->pIds = $id;

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
                $pIds = Request::getArray('pIds', 0);

                // Make sure we have IDs to work with
                if (empty($pIds)) {
                    App::redirect(
                        Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller),
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

                    foreach ($pIds as $pId) {
                        // Delete SKU
                        try {
                            $product = $obj->product($pId);
                            $product->delete();
                        } catch (\Exception $e) {
                            $url = 'index.php?option=' . $this->option . '&controller='
                                . $this->controller . '&task=display&id=' . $pId;
                            App::redirect(
                                Route::url($url),
                                $e->getMessage(),
                                $type,
                            );
                        }
                    }

                    $msg = "Product(s) deleted";
                    $type = 'message';
                }

                // Set the redirect
                $url = 'index.php?option=' . $this->option . '&controller='
                    . $this->controller . '&task=display';
                App::redirect(
                    Route::url($url),
                    $msg,
                    $type,
                );
        }
    }

    /**
     * Set the access level of an article to 'public'
     *
     * @return  void
     */
    public function accesspublicTask()
    {
        $this->accessTask(0);
    }

    /**
     * Set the access level of an article to 'registered'
     *
     * @return  void
     */
    public function accessregisteredTask()
    {
        $this->accessTask(1);
    }

    /**
     * Set the access level of an article to 'special'
     *
     * @return  void
     */
    public function accessspecialTask()
    {
        $this->accessTask(2);
    }

    /**
     * Set the access level of an article
     *
     * @param   integer  $access  Access level to set
     */
    public function accessTask($access = 0)
    {
        // Incoming
        $id = Request::getInt('id', 0);

        // Make sure we have an ID to work with
        if (!$id) {
            App::redirect(
                Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller),
                Lang::txt('COM_STOREFRONT_NO_ID'),
                'error',
            );
        }

        // Load the article
        $row = new \Components\Storefront\Models\Collection($id);
        $row->set('access', $access);

        // Check and store the changes
        if (!$row->store(true)) {
            App::redirect(
                Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller),
                $row->getError(),
                'error',
            );
        }

        App::redirect(
            Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller),
        );
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
        $ids = (!is_array($ids) ? [$ids] : $ids);

        // Check for an ID
        if (count($ids) < 1) {
            $msg = ($state == 1
                ? Lang::txt('COM_STOREFRONT_SELECT_PUBLISH')
                : Lang::txt('COM_STOREFRONT_SELECT_UNPUBLISH'));
            App::redirect(
                Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller),
                $msg,
                'error',
            );
        }

        // Update record(s)
        foreach ($ids as $pId) {
            // Save product
            try {
                $product = new Product($pId);
                $product->setActiveStatus($state);
                $product->save();
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

            $message = 'Product could not be ' . $action;
            if (count($ids) > 1) {
                $message = 'Some products could not be ' . $action;
            }
            $type = 'error';
        }

        // Redirect
        App::redirect(
            Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller),
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
        App::redirect(
            Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller),
        );
    }
}
