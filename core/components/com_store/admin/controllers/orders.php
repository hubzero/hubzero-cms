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
use Hubzero\Config\Registry;
use Components\Store\Tables\Order;
use Components\Store\Tables\OrderItem;
use Exception;
use Component;
use Request;
use Config;
use Route;
use Lang;
use User;
use Date;

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
		$upconfig = Component::params('com_members');
		$this->banking = $upconfig->get('bankAccounts');

		parent::execute();
	}

	/**
	 * Display all orders
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Instantiate a new view
		$this->view->store_enabled = $this->config->get('store_enabled');

		// Get paging variables
		$this->view->filters = array(
			'limit' => Request::getState(
				$this->_option . '.items.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.items.limitstart',
				'limitstart',
				0,
				'int'
			),
			'filterby' => Request::getState(
				$this->_option . '.orders.filterby',
				'filterby',
				'all'
			),
			'sortby' => Request::getState(
				$this->_option . '.orders.sortby',
				'sortby',
				'm.id DESC'
			)
		);

		// Get cart object
		$objOrder = new Order($this->database);

		// Get record count
		$this->view->total = $objOrder->getOrders('count', $this->view->filters);
		$this->view->rows  = $objOrder->getOrders('', $this->view->filters);

		if ($this->view->rows)
		{
			$oi = new OrderItem($this->database);
			foreach ($this->view->rows as $o)
			{
				$items = '';

				$results = $oi->getOrderItems($o->id);

				foreach ($results as $r)
				{
					$items .= $r->title;
					$items .= ($r != end($results)) ? '; ' : '';
				}
				$o->itemtitles = $items;

				$targetuser = User::getInstance($o->uid);
				$o->author = $targetuser->get('username');
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Generate a receipt
	 *
	 * @return     void
	 */
	public function receiptTask()
	{
		// Incoming
		$id = Request::getInt('id', 0);

		// Load the order
		$row = new Order($this->database);
		$row->load($id);

		// Instantiate an OrderItem object
		$oi = new OrderItem($this->database);

		if ($id)
		{
			// Get order items
			$orderitems = $oi->getOrderItems($id);
			if ($orderitems)
			{
				foreach ($orderitems as $r)
				{
					$params = new Registry($r->params);
					$selections = new Registry($r->selections);

					// Get size selection
					$r->sizes        = $params->get('size', '');
					$r->sizes        = str_replace(' ', '', $r->sizes);
					$r->selectedsize = trim($selections->get('size', ''));
					$r->sizes        = preg_split('/,/', $r->sizes);
					$r->sizeavail    = in_array($r->selectedsize, $r->sizes) ? 1 : 0;

					// Get color selection
					$r->colors        = $params->get('color', '');
					$r->colors        = str_replace(' ', '', $r->colors);
					$r->selectedcolor = trim($selections->get('color', ''));
					$r->colors        = preg_split('/,/', $r->colors);
				}
			}
			else
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					Lang::txt('Order empty, cannot generate receipt'),
					'error'
				);
				return;
			}

			$customer = User::getInstance($row->uid);
		}
		else
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('Need order ID to issue a receipt'),
				'error'
			);
			return;
		}

		// Include needed libraries
		// require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'receipt.pdf.php');

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

		//require_once(PATH_CORE . DS . 'libraries/tcpdf/tcpdf.php');
		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$receipt_title  = $this->config->get('receipt_title')  ? $this->config->get('receipt_title')  : 'Your Order' ;
		$hubaddress = array();
		$hubaddress[] = $this->config->get('hubaddress_ln1') ? $this->config->get('hubaddress_ln1') : '' ;
		$hubaddress[] = $this->config->get('hubaddress_ln2') ? $this->config->get('hubaddress_ln2') : '' ;
		$hubaddress[] = $this->config->get('hubaddress_ln3') ? $this->config->get('hubaddress_ln3') : '' ;
		$hubaddress[] = $this->config->get('hubaddress_ln4') ? $this->config->get('hubaddress_ln4') : '' ;
		$hubaddress[] = $this->config->get('hubaddress_ln5') ? $this->config->get('hubaddress_ln5') : '' ;
		$hubaddress[] = $this->config->get('hubemail') ? $this->config->get('hubemail') : '' ;
		$hubaddress[] = $this->config->get('hubphone') ? $this->config->get('hubphone') : '' ;

		$headertext_ln1 = $this->config->get('headertext_ln1') ? $this->config->get('headertext_ln1') : '' ;
		$headertext_ln2 = $this->config->get('headertext_ln2') ? $this->config->get('headertext_ln2') : Config::get('sitename') ;
		$footertext     = $this->config->get('footertext')     ? $this->config->get('footertext')     : 'Thank you for contributions to our HUB!' ;
		$receipt_note   = $this->config->get('receipt_note')   ? $this->config->get('receipt_note')   : '' ;

		// Get front-end template name
		$sql = "SELECT template FROM `#__template_styles` WHERE `client_id`=0 AND `home`=1";
		$this->database->setQuery($sql);
		$tmpl = $this->database->loadResult();

		// set default header data
		$pdf->SetHeaderData(NULL, 0, strtoupper($receipt_title). ' - #' . $id, NULL, array(84, 94, 124), array(146, 152, 169));
		$pdf->setFooterData(array(255, 255, 255), array(255, 255, 255));

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(10);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

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
				throw new Exception(Lang::txt('Failed to create folder to store receipts'), 500);
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
		else
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('There was an error creating a receipt'),
				'error'
			);
			return;
		}

		return;
	}

	/**
	 * Display an order
	 *
	 * @return     void
	 */
	public function orderTask()
	{
		$this->view->store_enabled = $this->config->get('store_enabled');

		// Incoming
		$id = Request::getInt('id', 0);

		// Load data
		$this->view->row = new Order($this->database);
		$this->view->row->load($id);

		$oi = new OrderItem($this->database);

		$this->view->orderitems = array();
		$this->view->customer = null;
		$this->view->funds = 0;
		if ($id)
		{
			// Get order items
			$this->view->orderitems = $oi->getOrderItems($id);
			if (count($this->view->orderitems) > 0)
			{
				foreach ($this->view->orderitems as $r)
				{
					$params = new Registry($r->params);
					$selections = new Registry($r->selections);

					// Get size selection
					$r->sizes = $params->get('size', '');
					$r->sizes = str_replace(' ', '', $r->sizes);
					$r->sizes = preg_split('#,#', $r->sizes);
					$r->selectedsize = trim($selections->get('size', ''));
					$r->sizeavail = in_array($r->selectedsize, $r->sizes) ? 1 : 0;

					// Get color selection
					$r->colors = $params->get('color', '');
					$r->colors = str_replace(' ', '', $r->colors);
					$r->colors = preg_split('#,#', $r->colors);
					$r->selectedcolor = trim($selections->get('color', ''));
				}
			}

			$this->view->customer = User::getInstance($this->view->row->uid);

			// Check available user funds
			$BTL = new Teller($this->view->row->uid);
			$balance = $BTL->summary();
			$credit  = $BTL->credit_summary();
			$this->view->funds = $balance;
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Saves changes to an order
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$statusmsg = '';

		$data   = array_map('trim', $_POST);
		$action = (isset($data['action'])) ? $data['action'] : '';
		$id     = ($data['id']) ? $data['id'] : 0 ;
		$cost   = intval($data['total']);

		if ($id)
		{
			// initiate extended database class
			$row = new Order($this->database);
			$row->load($id);
			$row->notes = \Hubzero\Utility\Sanitize::clean($data['notes']);
			$hold       = $row->total;
			$row->total = $cost;

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
					$sql = "DELETE FROM `#__users_transactions` WHERE category='store' AND type='hold' AND referenceid='" . $id . "' AND uid=" . intval($row->uid);
					$this->database->setQuery($sql);
					if (!$this->database->query())
					{
						throw new Exception($this->database->getErrorMsg(), 500);
					}
					// debit account
					if ($cost > 0)
					{
						$BTL_Q->withdraw($cost, Lang::txt('COM_STORE_BANKING_PURCHASE').' #' . $id, 'store', $id);
					}

					// update order information
					$row->status_changed = Date::toSql();
					$row->status = 1;
					$statusmsg = Lang::txt('COM_STORE_ORDER') . ' #' . $id . ' ' . Lang::txt('COM_STORE_HAS_BEEN') . ' ' . strtolower(Lang::txt('COM_STORE_COMPLETED')) . '.';
				break;

				case 'cancel_order':
					// adjust credit
					$credit = $BTL_Q->credit_summary();
					$adjusted = $credit - $hold;
					$BTL_Q->credit_adjustment($adjusted);

					// remove hold
					$sql = "DELETE FROM `#__users_transactions` WHERE category='store' AND type='hold' AND referenceid='" . $id . "' AND uid=" . intval($row->uid);
					$this->database->setQuery($sql);
					if (!$this->database->query())
					{
						throw new Exception($this->database->getErrorMsg(), 500);
					}
					// update order information
					$row->status_changed = Date::toSql();
					$row->status = 2;

					$statusmsg = Lang::txt('COM_STORE_ORDER') . ' #' . $id . ' ' . Lang::txt('COM_STORE_HAS_BEEN') . ' ' . strtolower(Lang::txt('COM_STORE_CANCELLED')) . '.';
				break;

				case 'message':
					$statusmsg = Lang::txt('COM_STORE_MSG_SENT') . '.';
				break;

				default:
					$statusmsg = Lang::txt('COM_STORE_ORDER_DETAILS_UPDATED') . '.';
				break;
			}

			// check content
			if (!$row->check())
			{
				throw new Exception($row->getError(), 500);
				return;
			}

			// store new content
			if (!$row->store())
			{
				throw new Exception($row->getError(), 500);
			}

			// send email
			if ($action || $data['message'])
			{
				if (\Hubzero\Utility\Validate::email($row->email))
				{
					$message = new \Hubzero\Mail\Message();
					$message->setSubject(Config::get('sitename') . ' '
						. Lang::txt('COM_STORE_EMAIL_UPDATE_SHORT', $id));
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
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			$statusmsg
		);
	}
}
