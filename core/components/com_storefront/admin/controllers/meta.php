<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Storefront\Models\Archive;
use Hubzero\Facades\App;
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
     * Meta is edited per product, from the product's edit screen; there is no
     * list of it, so the default task goes to the products list
     *
     * @return  void
     */
    public function displayTask()
    {
        App::redirect(
            Route::url('index.php?option=' . $this->option . '&controller=products', false),
        );
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
     * @param   mixed  $row
     * @return  void
     */
    public function editTask($row = null)
    {
        Request::setVar('hidemainmenu', 1);

        $obj = new Archive();

        if (is_object($row) && method_exists($row, 'getId')) {
            $this->view->set('row', $row);
            $this->view->task = 'edit';

            $id = $row->getId();
        } else {
            // Incoming
            $id = Request::getArray('id', 0);

            if (!empty($id)) {
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
