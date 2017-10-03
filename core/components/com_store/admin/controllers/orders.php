<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Store\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Content\Server;
use Hubzero\Bank\Teller;
use Components\Store\Models\Order;
use Component;
use Request;
use Notify;
use Route;
use Lang;
use User;
use App;

/**
 * Controller class for store orders
 */
class Orders extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->banking = Component::params('com_members')->get('bankAccounts');

		parent::execute();
	}

	/**
	 * Display all orders
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get paging variables
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'status' => Request::getState(
				$this->_option . '.' . $this->_controller . '.status',
				'status',
				-1,
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'ordered'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			)
		);

		$query = Order::all();

		if ($filters['status'] >= 0)
		{
			$query->whereEquals('status', (int)$filters['status']);
		}

		// Get records
		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Generate a receipt
	 *
	 * @return  void
	 */
	public function receiptTask()
	{
		// Incoming
		$id = Request::getInt('id', 0);

		// Load the order
		$row = Order::oneOrFail($id);

		// Get order items
		$orderitems = $row->items;

		if ($orderitems->count() <= 0)
		{
			Notify::warning(Lang::txt('Order empty, cannot generate receipt'));
			return $this->cancelTask();
		}

		$customer = User::getInstance($row->uid);

		// Build the link displayed
		$sef = Route::url('index.php?option=' . $this->_option);
		if (substr($sef, 0, 1) == '/')
		{
			$sef = substr($sef, 1, strlen($sef));
		}
		$webpath = str_replace('/administrator/', '/', Request::base() . $sef);
		$webpath = str_replace('//', '/', $webpath);
		if (isset($_SERVER['HTTPS']))
		{
			$webpath = str_replace('http:', 'https:', $webpath);
		}
		if (!strstr($webpath, '://'))
		{
			$webpath = str_replace(':/', '://', $webpath);
		}

		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$receipt_title  = $this->config->get('receipt_title') ? $this->config->get('receipt_title') : 'Your Order';
		$hubaddress = array();
		$hubaddress[] = $this->config->get('hubaddress_ln1') ? $this->config->get('hubaddress_ln1') : '';
		$hubaddress[] = $this->config->get('hubaddress_ln2') ? $this->config->get('hubaddress_ln2') : '';
		$hubaddress[] = $this->config->get('hubaddress_ln3') ? $this->config->get('hubaddress_ln3') : '';
		$hubaddress[] = $this->config->get('hubaddress_ln4') ? $this->config->get('hubaddress_ln4') : '';
		$hubaddress[] = $this->config->get('hubaddress_ln5') ? $this->config->get('hubaddress_ln5') : '';
		$hubaddress[] = $this->config->get('hubemail') ? $this->config->get('hubemail') : '';
		$hubaddress[] = $this->config->get('hubphone') ? $this->config->get('hubphone') : '';

		$headertext_ln1 = $this->config->get('headertext_ln1') ? $this->config->get('headertext_ln1') : '';
		$headertext_ln2 = $this->config->get('headertext_ln2') ? $this->config->get('headertext_ln2') : Config::get('sitename');
		$footertext     = $this->config->get('footertext')     ? $this->config->get('footertext')     : 'Thank you for contributions to our HUB!';
		$receipt_note   = $this->config->get('receipt_note')   ? $this->config->get('receipt_note')   : '';

		// Get front-end template name
		$sql = "SELECT template FROM `#__template_styles` WHERE `client_id`=0 AND `home`=1";
		$this->database->setQuery($sql);
		$tmpl = $this->database->loadResult();

		// set default header data
		$pdf->SetHeaderData(null, 0, strtoupper($receipt_title). ' - #' . $id, null, array(84, 94, 124), array(146, 152, 169));
		$pdf->setFooterData(array(255, 255, 255), array(255, 255, 255));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(10);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// Set font
		$pdf->SetFont('dejavusans', '', 11, '', true);

		$pdf->AddPage();

		// HTML content
		$this->view->setLayout('receipt');
		$this->view->hubaddress     = $hubaddress;
		$this->view->headertext_ln1 = $headertext_ln1;
		$this->view->headertext_ln2 = $headertext_ln2;
		$this->view->receipt_note   = $receipt_note;
		$this->view->receipt_title  = $receipt_title;
		$this->view->option         = $this->_option;
		$this->view->url            = $webpath;
		$this->view->customer       = $customer;
		$this->view->row            = $row;
		$this->view->orderitems     = $orderitems;

		$html = $this->view->loadTemplate();

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// ---------------------------------------------------------

		$dir = PATH_APP . DS . 'site' . DS . 'store' . DS . 'temp';
		$tempFile = $dir . DS . 'receipt_' . $id . '.pdf';

		if (!is_dir($dir))
		{
			if (!\Filesystem::makeDirectory($dir))
			{
				App::abort(500, Lang::txt('Failed to create folder to store receipts'));
			}
		}

		// Close and output PDF document
		$pdf->Output($tempFile, 'F');

		if (is_file($tempFile))
		{
			$xserver = new Server();
			$xserver->filename($tempFile);
			$xserver->serve_inline($tempFile);
			exit;
		}

		Notify::error(Lang::txt('There was an error creating a receipt'));

		$this->cancelTask();
	}

	/**
	 * Display an order
	 *
	 * @return  void
	 */
	public function orderTask()
	{
		// Incoming
		$id = Request::getInt('id', 0);

		// Load data
		$row = Order::oneOrFail($id);

		// Get order items
		$orderitems = $row->items;

		// Get customer
		$customer = User::getInstance($row->get('uid'));

		// Check available user funds
		$BTL = new Teller($row->get('uid'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance;

		// Output the HTML
		$this->view
			->set('row', $row)
			->set('orderitems', $orderitems)
			->set('funds', $funds)
			->set('customer', $customer)
			->display();
	}

	/**
	 * Saves changes to an order
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$statusmsg = '';

		$data   = array(
			'action' => Request::getCmd('action'),
			'notes'  => Request::getVar('notes'),
			'total'  => Request::getInt('total', 0)
		);
		$id   = Request::getInt('id', 0);
		$cost = intval($data['total']);

		// initiate extended database class
		$row = Order::oneOrFail($id);
		$row->set('notes', \Hubzero\Utility\Sanitize::clean($data['notes']));
		$hold = $row->total;
		$row->set('total', $cost);

		// get user bank account
		$xprofile = User::getInstance($row->uid);
		$BTL_Q = new Teller($xprofile->get('id'));

		switch ($action)
		{
			case 'complete_order':
				// adjust credit
				$credit = $BTL_Q->credit_summary();
				$adjusted = $credit - $hold;
				$BTL_Q->credit_adjustment($adjusted);

				// remove hold
				$database = App::get('db');
				$sql = "DELETE FROM `#__users_transactions` WHERE category='store' AND type='hold' AND referenceid='" . $id . "' AND uid=" . intval($row->uid);
				$database->setQuery($sql);
				$database->query();

				// debit account
				if ($cost > 0)
				{
					$BTL_Q->withdraw($cost, Lang::txt('COM_STORE_BANKING_PURCHASE').' #' . $id, 'store', $id);
				}

				// update order information
				$row->set('status_changed', Date::toSql());
				$row->set('status', 1);

				$statusmsg = Lang::txt('COM_STORE_ORDER') . ' #' . $id . ' ' . Lang::txt('COM_STORE_HAS_BEEN') . ' ' . strtolower(Lang::txt('COM_STORE_COMPLETED')) . '.';
			break;

			case 'cancel_order':
				// adjust credit
				$credit = $BTL_Q->credit_summary();
				$adjusted = $credit - $hold;
				$BTL_Q->credit_adjustment($adjusted);

				// remove hold
				$database = App::get('db');
				$sql = "DELETE FROM `#__users_transactions` WHERE category='store' AND type='hold' AND referenceid='" . $id . "' AND uid=" . intval($row->uid);
				$database->setQuery($sql);
				$database->query();

				// update order information
				$row->set('status_changed', Date::toSql());
				$row->set('status', 2);

				$statusmsg = Lang::txt('COM_STORE_ORDER') . ' #' . $id . ' ' . Lang::txt('COM_STORE_HAS_BEEN') . ' ' . strtolower(Lang::txt('COM_STORE_CANCELLED')) . '.';
			break;

			case 'message':
				$statusmsg = Lang::txt('COM_STORE_MSG_SENT') . '.';
			break;

			default:
				$statusmsg = Lang::txt('COM_STORE_ORDER_DETAILS_UPDATED') . '.';
			break;
		}

		// store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->cancelTask();
		}

		// send email
		if ($action || $data['message'])
		{
			if (\Hubzero\Utility\Validate::email($row->email))
			{
				$message = new \Hubzero\Mail\Message();
				$message->setSubject(Config::get('sitename') . ' ' . Lang::txt('COM_STORE_EMAIL_UPDATE_SHORT', $id));
				$message->addFrom(
					Config::get('mailfrom'),
					Config::get('sitename') . ' ' . Lang::txt('COM_STORE_STORE')
				);

				// Plain text email
				$eview = new \Hubzero\Mail\View(array(
					'name'   => 'emails',
					'layout' => '_plain'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->orderid    = $id;
				$eview->cost       = $cost;
				$eview->row        = $row;
				$eview->action     = $action;
				$eview->message    = \Hubzero\Utility\Sanitize::stripAll($data['message']);

				$plain = $eview->loadTemplate(false);
				$plain = str_replace("\n", "\r\n", $plain);

				$message->addPart($plain, 'text/plain');

				// HTML email
				$eview->setLayout('_html');

				$html = $eview->loadTemplate();
				$html = str_replace("\n", "\r\n", $html);

				$message->addPart($html, 'text/html');

				// Send e-mail
				$message->setTo(array($row->email));
				$message->send();
			}
		}

		Notify::success($statusmsg);

		$this->cancelTask();
	}
}
