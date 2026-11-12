<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Cart\Helpers\Orders as OrdersHelper;
use Components\Cart\Models\Cart;
use Components\Storefront\Models\Warehouse;
use Hubzero\Facades\Request;
use Hubzero\Facades\Config;
use Hubzero\Facades\Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;
use Hubzero\Facades\App;

/**
 * Controller class for knowledge base categories
 */
class Orders extends AdminController
{
    /**
     * Display a list of all orders
     *
     * @return  void
     */
    public function displayTask()
    {
        // Get filters
        $this->view->set('filters', [
            'search' => Request::getState(
                $this->option . '.' . $this->controller . '.search',
                'search',
                '',
            ),
            'uidNumber' => Request::getState(
                $this->option . '.' . $this->controller . '.uidNumber',
                'uidNumber',
                0,
                'int',
            ),
            // Get sorting variables
            'sort' => Request::getState(
                $this->option . '.' . $this->controller . '.sort',
                'filter_order',
                'tLastUpdated',
            ),
            'sort_Dir' => Request::getState(
                $this->option . '.' . $this->controller . '.sortdir',
                'filter_order_Dir',
                'DESC',
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
            'report-notes' => Request::getState(
                $this->option . '.' . $this->controller . '.report-notes',
                'report-notes',
                0,
                'int',
            ),
            'report-from' => Request::getState(
                $this->option . '.' . $this->controller . '.report-from',
                'report-from',
                gmdate('m/d/Y', strtotime('-1 month')),
            ),
            'report-to' => Request::getState(
                $this->option . '.' . $this->controller . '.report-to',
                'report-to',
                gmdate('m/d/Y'),
            ),
        ]);

        // Get record count
        $this->view->filters['count'] = true;
        $this->view->filters['userInfo'] = true;
        $this->view->set('total', Cart::getAllTransactions($this->view->filters));

        // Get records
        $this->view->filters['count'] = false;
        $this->view->set('rows', Cart::getAllTransactions($this->view->filters));

        if (!$this->view->rows) {
            $this->view->rows = [];
        }

        // update with total and items ordered
        foreach ($this->view->rows as $r) {
            $tInfo = Cart::getTransactionInfo($r->tId);
            $r->tiTotal = $tInfo->tiTotal;
            $tiItemsQty = count(unserialize($tInfo->tiItems));
            $r->tiItemsQty = $tiItemsQty;
        }

        // Output the HTML
        $this->view->display();
    }

    /**
     * View the order
     *
     * @return  void
     */
    public function viewTask()
    {
        // Incoming
        $id = Request::getInt('id', 0);

        // Get transaction info
        $transactionItems = Cart::getTransactionItems($id, false);
        $transactionInfo = Cart::getTransactionInfo($id);

        //print_r($transactionItems); die;

        $tInfo = $transactionInfo;

        foreach ($transactionItems as $sId => $item) {
            // Check if the product is still available
            $warehouse = new Warehouse();
            $skuInfo = $warehouse->getSkuInfo($sId);
            if (!$skuInfo) {
                // product no longer available
                // Make an attempt at not throwing an error in the view
                $item['info']->available = false;
                $item['info']->pId = 0;
                $item['info']->pName = 'N/A';
                $item['info']->sId = $sId;
                $item['info']->sSku = 0;
            } else {
                $item['info'] = $skuInfo['info'];
                $item['info']->available = true;
            }
            $transactionItems[$sId] = $item;
        }

        $tInfo->tiItems = $transactionItems;

        // Get user info
        $userId = Cart::getCartUser($tInfo->crtId);
        $user = User::getInstance($userId);

        // Get the log of changes
        $changesLog = OrdersHelper::getOrderChangesLog($id);

        // Build log messages
        if (!empty($changesLog)) {
            foreach ($changesLog as $log) {
                // Get user info
                $profile = User::getInstance($log->created_by);
                $userName = $profile->get('name');
                $log->user = $userName;

                $log->details = json_decode($log->details);

                foreach ($log->details as $item) {
                    $msg = '';

                    if ($item->object == 'cart_transaction_item') {
                        // find the item's (SKU) info
                        $skuInfo = $transactionItems[$item->sId];
                        $msg = $skuInfo['info']->sSku;

                        if (
                            is_object($item->key)
                            && !empty($item->key->tiMeta)
                            && $item->key->tiMeta == 'checkoutNotes'
                        ) {
                            $msg .= ' <strong>notes</strong>';
                        } elseif ($item->key == 'tiQty') {
                            $msg .= ' <strong>quantity</strong>';
                        } elseif ($item->key == 'tiPrice') {
                            $msg .= ' <strong>price</strong>';
                        }

                        $msg .= ' value was updated';
                    } elseif ($item->object == 'cart_transaction_info') {
                        $msg = 'Order';

                        if ($item->key == 'tiNotes') {
                            $msg .= ' <strong>notes</strong>';
                        } elseif ($item->key == 'tiPaymentDetails') {
                            $msg .= ' <strong>payment details</strong>';
                        }

                        $msg .= ' value was updated';
                    }

                    $item->message = $msg;
                }
            }

            $this->view->set('log', $changesLog);
        }

        $this->view->set('user', $user);
        $this->view->set('tInfo', $tInfo);
        $this->view->set('items', $transactionItems);
        $this->view->set('tId', $id);

        $this->view
            ->setLayout('view')
            ->display();
    }

    /**
     * Edit the order info
     *
     * @return  void
     */
    public function editTask()
    {
        // Incoming
        $id = Request::getInt('id', 0);

        // Get transaction info
        $transactionItems = Cart::getTransactionItems($id, false);
        $transactionInfo = Cart::getTransactionInfo($id);

        //print_r($transactionItems); die;

        $tInfo = $transactionInfo;

        foreach ($transactionItems as $item) {
            // Check if the product is still available
            $warehouse = new Warehouse();
            $skuInfo = $warehouse->getSkuInfo($item['info']->sId);
            if (!$skuInfo) {
                // product no longer available
                $item['info']->available = false;
            } else {
                $item['info']->available = true;
            }
        }

        $tInfo->tiItems = $transactionItems;

        // Get user info
        $userId = Cart::getCartUser($tInfo->crtId);
        $user = User::getInstance($userId);

        $this->view->set('user', $user);
        $this->view->set('tInfo', $tInfo);
        $this->view->set('items', $transactionItems);
        $this->view->set('tId', $id);

        $this->view
            ->setLayout('edit')
            ->display();
    }

    public function applyTask()
    {
        $this->saveTask(false);
    }

    /**
     * Save the order info
     *
     * @return  void
     */
    public function saveTask($redirect = true)
    {
        // Incoming
        $id = Request::getInt('id', '');

        // get the transaction items' QTYs
        $tiQty = Request::getArray('tiQty', []);

        // get the transaction items' prices
        $tiPrice = Request::getArray('tiPrice', []);

        // get the transaction items' checkoutNotes
        $tiCheckoutNotes = Request::getArray('checkoutNotes', []);

        // get the transaction notes
        $tiNotes = Request::getString('tiNotes', '');
        $transactionInfo = ['tiNotes' => $tiNotes];

        // get the payment details
        $tiPaymentDetails = Request::getString('tiPaymentDetails', '');
        $transactionInfo['tiPaymentDetails'] = $tiPaymentDetails;

        //print_r($tiCheckoutNotes); die;

        // Create transaction items' info object
        $tiInfo = new \stdClass();

        // populate the QTYs
        foreach ($tiQty as $sId => $qty) {
            if (empty($tiInfo->$sId)) {
                $tiInfo->$sId = new \stdClass();
            }
            $tiInfo->$sId->tiQty = $qty;
        }

        // populate the prices
        foreach ($tiPrice as $sId => $price) {
            if (empty($tiInfo->$sId)) {
                $tiInfo->$sId = new \stdClass();
            }
            $tiInfo->$sId->tiPrice = $price;
        }

        // populate the notes
        foreach ($tiCheckoutNotes as $sId => $notes) {
            if (empty($tiInfo->$sId)) {
                $tiInfo->$sId = new \stdClass();
            }
            if (empty($tiInfo->$sId->meta)) {
                $tiInfo->$sId->meta = new \stdClass();
            }
            $tiInfo->$sId->meta->checkoutNotes = $notes;
        }

        //print_r($transactionInfo); die;

        // Log the changes
        $itemsChanges = Cart::updateTransactionItems($id, $tiInfo);
        $transactionChanges = Cart::updateTransactionInfo($id, $transactionInfo);
        //print_r($itemsChanges);
        //print_r($transactionChanges); //die;
        $orderChanges = array_merge($itemsChanges, $transactionChanges);
        //print_r($orderChanges); die;

        if (!empty($orderChanges)) {
            OrdersHelper::logOrderChanges($id, $orderChanges);
        }

        if ($redirect) {
            App::redirect(
                Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=' . $this->controller . '&task=view&id=' . $id,
                    false,
                ),
                Lang::txt('Order info saved'),
            );
        }

        $this->editTask();
    }

    /**
     * Display a list of all orders
     *
     * @return  void
     */
    public function itemsTask()
    {
        // Do some filter cleaning
        $setPId = Request::getInt('pId', 0);
        $setSId = Request::getInt('sId', 0);

        if ($setPId) {
            Request::setVar('sId', 0);
        } elseif ($setSId) {
            Request::setVar('pId', 0);
        }

        // Get filters
        $this->view->set('filters', [
            'search' => Request::getState(
                $this->option . '.' . $this->controller . $this->task . '.search',
                'search',
                '',
            ),
            // Get sorting variables
            'sort' => Request::getState(
                $this->option . '.' . $this->controller . $this->task . '.sort',
                'filter_order',
                'tLastUpdated',
            ),
            'sort_Dir' => Request::getState(
                $this->option . '.' . $this->controller . $this->task . '.sortdir',
                'filter_order_Dir',
                'ASC',
            ),
            // Get paging variables
            'limit' => Request::getState(
                $this->option . '.' . $this->controller . $this->task . '.limit',
                'limit',
                Config::get('list_limit'),
                'int',
            ),
            'start' => Request::getState(
                $this->option . '.' . $this->controller . $this->task . '.limitstart',
                'limitstart',
                0,
                'int',
            ),
            'report-from' => Request::getState(
                $this->option . '.' . $this->controller . $this->task . '.report-from',
                'report-from',
                gmdate('m/d/Y', strtotime('-1 month')),
            ),
            'report-to' => Request::getState(
                $this->option . '.' . $this->controller . $this->task . '.report-to',
                'report-to',
                gmdate('m/d/Y'),
            ),
            'order' => Request::getState(
                $this->option . '.' . $this->controller . $this->task . '.order',
                'order',
                0,
                'int',
            ),
            'pId' => Request::getState(
                $this->option . '.' . $this->controller . '.pId',
                'pId',
                0,
                'int',
            ),
            'sId' => Request::getState(
                $this->option . '.' . $this->controller . '.sId',
                'sId',
                0,
                'int',
            ),
        ]);

        if ($this->view->filters['order']) {
            $this->view->filters['report-from'] = '';
            $this->view->filters['report-to'] = '';
        }

        // Get orders count
        $this->view->set('total', OrdersHelper::getItemsOrdered('count', $this->view->filters));

        // Get orders
        $orders = OrdersHelper::getItemsOrdered('list', $this->view->filters);

        foreach ($orders as $order) {
            $orderItems = unserialize(Cart::getTransactionInfo($order->tId)->tiItems);
            $order->itemInfo = $orderItems[$order->sId];
        }

        $this->view->set('rows', $orders);

        // Output the HTML
        $this->view->display();
    }

    /**
     * Download CSV report (default)
     *
     * @return  void
     */
    public function downloadTask()
    {
        // Get filters
        $filters = [
            'search' => Request::getState(
                $this->option . '.' . $this->controller . '.search',
                'search',
                '',
            ),
            // Get sorting variables
            'sort' => Request::getState(
                $this->option . '.' . $this->controller . '.sort',
                'filter_order',
                'dDownloaded',
            ),
            'sort_Dir' => Request::getState(
                $this->option . '.' . $this->controller . '.sortdir',
                'filter_order_Dir',
                'ASC',
            ),
            'report-notes' => Request::getState(
                $this->option . '.' . $this->controller . '.report-notes',
                'report-notes',
                0,
                'int',
            ),
            'report-from' => Request::getState(
                $this->option . '.' . $this->controller . '.report-from',
                'report-from',
                gmdate('m/d/Y', strtotime('-1 month')),
            ),
            'report-to' => Request::getState(
                $this->option . '.' . $this->controller . '.report-to',
                'report-to',
                gmdate('m/d/Y'),
            ),
            'uidNumber' => Request::getState(
                $this->option . '.' . $this->controller . '.uidNumber',
                'uidNumber',
                0,
                'int',
            ),
        ];

        // request array to be returned
        $filters['returnFormat'] = 'array';
        $filters['userInfo'] = true;
        $rowsRaw = Cart::getAllTransactions($filters);

        $rows = [];

        foreach ($rowsRaw as $row) {
            // Get the notes, both SKU-specific and other
            $transactionItems = Cart::getTransactionItems($row['tId'], false);

            //continue;

            $transactionInfo = Cart::getTransactionInfo($row['tId']);
            $transactionInfoItems = unserialize($transactionInfo->tiItems);

            $notes = [];
            foreach ($transactionItems as $sId => $item) {
                $meta = $item['transactionInfo']->tiMeta;
                if (!empty($meta->checkoutNotes)) {
                    $notes[] = [
                        //'label' => $item['info']->pName . ', ' . $item['info']->sSku,
                        'label' => $transactionInfoItems[$sId]['info']->pName
                            . ', ' . $transactionInfoItems[$sId]['info']->sSku,
                        'notes' => $meta->checkoutNotes,
                    ];
                }
            }

            $genericNotesLabel = '';
            if (!empty($notes)) {
                $genericNotesLabel = 'Other notes/comments';
            }

            if ($transactionInfo->tiNotes) {
                $notes[] = [
                    'label' => $genericNotesLabel,
                    'notes' => $transactionInfo->tiNotes,
                ];
            }

            $notesValue = '';
            if (!empty($notes)) {
                $notesCount = 0;
                foreach ($notes as $note) {
                    if ($notesCount) {
                        $notesValue .= ' *** ';
                    }
                    $notesValue .= $note['label'];
                    if ($note['label']) {
                        $notesValue .= ': ';
                    }
                    $notesValue .= $note['notes'];
                    $notesCount++;
                }
            };

            $rows[] = [$row['tId'], $row['tLastUpdated'], $row['name'], $row['uidNumber'], $notesValue];
        }

        $dateFrom = gmdate('dMY', strtotime($filters['report-from']));
        $dateTo = gmdate('dMY', strtotime($filters['report-to']));
        $date = gmdate('d-m-Y');

        header("Content-Type: text/csv");
        $filename = "cart-orders-" . $date . "(" . $dateFrom . '-' . $dateTo . ").csv";
        header("Content-Disposition: attachment; filename=" . $filename);
        // Disable caching
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
        header("Pragma: no-cache"); // HTTP 1.0
        header("Expires: 0"); // Proxies

        $output = fopen("php://output", "w");
        $row = ['Order ID', 'Order Placed', 'Purchased By', 'Purchased By (userId)', 'Order Notes'];
        fputcsv($output, $row);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        die();
    }

    /**
     * Download CSV report (default)
     *
     * @return  void
     */
    public function downloadOrdersTask()
    {
        // Get filters
        $filters = [
            'search' => Request::getState(
                $this->option . '.' . $this->controller . 'items.search',
                'search',
                '',
            ),
            // Get sorting variables
            'sort' => Request::getState(
                $this->option . '.' . $this->controller . 'items.sort',
                'filter_order',
                'dDownloaded',
            ),
            'sort_Dir' => Request::getState(
                $this->option . '.' . $this->controller . 'items.sortdir',
                'filter_order_Dir',
                'ASC',
            ),
            'report-from' => Request::getState(
                $this->option . '.' . $this->controller . 'items.report-from',
                'report-from',
                gmdate('m/d/Y', strtotime('-1 month')),
            ),
            'report-to' => Request::getState(
                $this->option . '.' . $this->controller . 'items.report-to',
                'report-to',
                gmdate('m/d/Y'),
            ),
            'order' => Request::getState(
                $this->option . '.' . $this->controller . 'items.order',
                'order',
                0,
                'int',
            ),
        ];

        // Get orders, request array to be returned
        $orders = OrdersHelper::getItemsOrdered('list', $filters);

        foreach ($orders as $order) {
            $orderItems = unserialize(Cart::getTransactionInfo($order->tId)->tiItems);
            $order->itemInfo = $orderItems[$order->sId];
        }
        $rowsRaw = $orders;

        $rows = [];

        foreach ($rowsRaw as $row) {
            $itemInfo = $row->itemInfo['info'];
            $rows[] = [
                $row->sId,
                $itemInfo->pName . ', ' . $itemInfo->sSku,
                $row->tiQty,
                $row->tiPrice,
                $row->tId,
                $row->tLastUpdated,
                $row->name,
                $row->uidNumber,
            ];
        }

        $dateFrom = gmdate('dMY', strtotime($filters['report-from']));
        $dateTo = gmdate('dMY', strtotime($filters['report-to']));
        $date = gmdate('d-m-Y');

        header("Content-Type: text/csv");
        $filename = "cart-items-ordered-" . $date . "(" . $dateFrom . '-' . $dateTo . ").csv";
        header("Content-Disposition: attachment; filename=" . $filename);
        // Disable caching
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
        header("Pragma: no-cache"); // HTTP 1.0
        header("Expires: 0"); // Proxies

        $output = fopen("php://output", "w");
        $row = [
            'SKU ID',
            'Product',
            'QTY',
            'Price',
            'Order ID',
            'Order Placed',
            'Purchased By',
            'Purchased By (userId)',
        ];
        fputcsv($output, $row);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        die();
    }

    /**
     * Cancel a task (redirects to default task)
     *
     * @return  void
     */
    public function cancelTask()
    {
        // Incoming
        $id = Request::getString('id', '');
        $from = Request::getString('from', '');

        $attr = '';
        if ($from && $from == 'edit') {
            $attr = '&task=view&id=' . $id;
        }

        // Set the redirect
        App::redirect(
            Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . $attr, false),
        );
    }
}
