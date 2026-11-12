<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Storefront\Models\Archive;
use Hubzero\Facades\App;
use Hubzero\Facades\Config;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

/**
 * Controller class for knowledge base categories
 */
class Meta extends AdminController
{
    /**
     * Display a list of all categories
     *
     * @return  void
     */
    public function displayTask()
    {
        // Get filters
        $filters = [
            'access' => -1,
        ];
        $filters['sort'] = trim(Request::getState(
            $this->option . '.' . $this->controller . '.sort',
            'filter_order',
            'title',
        ));
        $filters['sort_Dir'] = trim(Request::getState(
            $this->option . '.' . $this->controller . '.sortdir',
            'filter_order_Dir',
            'ASC',
        ));

        // Get paging variables
        $filters['limit'] = Request::getState(
            $this->option . '.' . $this->controller . '.limit',
            'limit',
            Config::get('list_limit'),
            'int',
        );
        $filters['start'] = Request::getState(
            $this->option . '.' . $this->controller . '.limitstart',
            'limitstart',
            0,
            'int',
        );

        $obj = new Archive();

        // Get record count
        $this->view->set('total', $obj->products('count', $filters));

        // Get records
        $this->view->set('rows', $obj->products('list', $filters));
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
     * Edit a category
     *
     * @return  void
     */
    public function editTask($row = null)
    {
        Request::setVar('hidemainmenu', 1);

        $obj = new Archive();

        if (is_object($row)) {
            $this->view->set('row', $row);
            $this->view->task = 'edit';

            $id = $row->getId();
        } else {
            // Incoming
            $id = Request::getArray('id', 0);

            if (is_array($id) && !empty($id)) {
                $id = $id[0];
            }

            // Load product
            $this->view->set('row', $obj->product($id));
        }

        // Load meta
        $this->view->set('meta', $this->view->get('row')->getMeta());

        // Set any errors
        foreach ($this->getErrors() as $error) {
            $this->view->setError($error);
        }

        // See what layout to load
        if ($this->view->get('row')->getTypeInfo()->name == 'Software Download') {
            // Software
            $layout = 'edit-software';
        } else {
            App::abort(404, Lang::txt('No meta for this product'));
        }

        // Output the HTML
        $this->view
            ->setLayout($layout)
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

        $obj = new Archive();
        // Get the product
        $product = $obj->product($fields['pId']);

        $meta = $fields;
        unset($meta['pId']);

        // Save product meta
        $product->setMeta($meta);

        if ($redirect) {
            // Redirect
            App::redirect(
                Route::url(
                    'index.php?option='
                        . $this->option
                        . '&controller=products&task=edit&id='
                        . Request::getInt('id', 0),
                ),
                Lang::txt('COM_STOREFRONT_PRODUCT_SAVED'),
            );
        }
        Notify::success(Lang::txt('COM_STOREFRONT_PRODUCT_SAVED'));

        $this->editTask($product);
    }

    /**
     * Cancel a task (redirects to default task)
     *
     * @return     void
     */
    public function cancelTask()
    {
        // Set the redirect
        App::redirect(
            Route::url(
                'index.php?option=' . $this->option . '&controller=products&task=edit&id=' . Request::getInt('id', 0),
            ),
        );
    }
}
