<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Controller class for store orders
 */
class StoreControllerOrders extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$upconfig =& JComponentHelper::getParams('com_members');
		$this->banking = $upconfig->get('bankAccounts');
		ximport('Hubzero_Bank');

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

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Get paging variables
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.orders.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.orders.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['filterby'] = trim($app->getUserStateFromRequest(
			$this->_option . '.orders.filterby',
			'filterby',
			'all'
		));
		$this->view->filters['sortby']   = trim($app->getUserStateFromRequest(
			$this->_option . '.orders.sortby',
			'sortby',
			'm.id DESC'
		));

		// Get cart object
		$objOrder = new Order($this->database);

		// Get record count
		$this->view->total = $objOrder->getOrders('count', $this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		$this->view->rows = $objOrder->getOrders('', $this->view->filters);

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

				$targetuser =& JUser::getInstance($o->uid);
				$o->author = $targetuser->get('username');
			}
		}

		// Push some styles to the view
		$this->_getStyles();

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
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
		$id = JRequest::getInt('id', 0);

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
				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}

				foreach ($orderitems as $r)
				{
					$params = new $paramsClass($r->params);
					$selections = new $paramsClass($r->selections);

					// Get size selection
					$r->sizes    		= $params->get('size', '');
					$r->sizes 			= str_replace(" ","",$r->sizes);
					$r->selectedsize    = trim($selections->get('size', ''));
					$r->sizes    		= preg_split('#,#',$r->sizes);
					$r->sizeavail		= in_array($r->selectedsize, $r->sizes) ? 1 : 0;

					// Get color selection
					$r->colors    		= $params->get('color', '');
					$r->colors 			= str_replace(" ","",$r->colors);
					$r->selectedcolor   = trim($selections->get('color', ''));
					$r->colors    		= preg_split('#,#',$r->colors);
				}
			}
			else
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					'Order empty, cannot generate receipt',
					'error'
				);
				return;
			}

			$customer =& JUser::getInstance($row->uid);
		}
		else
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				'Need order ID to issue a receipt', 'error'
			);
			return;
		}
		
		// Include needed libraries
	//	require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'receipt.pdf.php');

		// Get the Joomla config
		$jconfig =& JFactory::getConfig();

		// Build the link displayed
		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option=' . $this->_option);
		if (substr($sef, 0, 1) == '/')
		{
			$sef = substr($sef, 1, strlen($sef));
		}
		$webpath = str_replace('/administrator/', '/', $juri->base() . $sef);
		$webpath = str_replace('//', '/', $webpath);
		if (isset($_SERVER['HTTPS']))
		{
			$webpath = str_replace('http:', 'https:', $webpath);
		}
		if (!strstr($webpath, '://'))
		{
			$webpath = str_replace(':/', '://', $webpath);
		}
		
		//require_once(JPATH_ROOT . DS . 'libraries/tcpdf/tcpdf.php');
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
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
		$headertext_ln2 = $this->config->get('headertext_ln2') ? $this->config->get('headertext_ln2') : $jconfig->getValue('config.sitename') ;
		$footertext     = $this->config->get('footertext')     ? $this->config->get('footertext')     : 'Thank you for contributions to our HUB!' ;
		$receipt_note   = $this->config->get('receipt_note')   ? $this->config->get('receipt_note')   : '' ;				
		
		// Get front-end template name
		$sql = "SELECT template FROM #__templates_menu WHERE client_id=0";
		$this->database->setQuery( $sql );
		$tmpl = $this->database->loadResult();

		// Use header image? tcpdf config needs to be adjusted
		/*
		if (is_file(JPATH_ROOT . DS . 'templates' . DS . $tmpl . DS . 'images' . DS . 'hub-store-logo.png'))
		{
			$logo = JPATH_ROOT . DS . 'templates' . DS . $tmpl . DS . 'images' . DS . 'hub-store-logo.png';
		}
		else
		{
			$logo =  JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_store' . DS . 'images' . DS . 'hub-store-logo.png';
		}
		*/
		
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
		$view = new JView( array('name'=>'orders', 'layout'=>'receipt' ) );
		$view->hubaddress  		= $hubaddress;
		$view->headertext_ln1 	= $headertext_ln1;
		$view->headertext_ln2 	= $headertext_ln2;
		$view->receipt_note 	= $receipt_note;
		$view->receipt_title 	= $receipt_title;
		$view->option 			= $this->_option;
		$view->url				= $webpath;
		$view->customer			= $customer;
		$view->row				= $row;
		$view->orderitems		= $orderitems;

		$html = $view->loadTemplate();

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// ---------------------------------------------------------
		
		$dir = JPATH_ROOT . DS . 'site' . DS . 'store' . DS . 'temp';
		$tempFile = $dir . DS . 'receipt_' . $id . '.pdf'; 
		
		if (!is_dir( $dir ))
		{
			if (!JFolder::create( $dir, 0755 ))
			{
				jimport('joomla.filesystem.folder');
				JError::raiseError(500, 'Failed to create folder to store receipts');
				return;
			}
		}
		
		// Close and output PDF document
		$pdf->Output($tempFile, 'F');
		
		if (is_file($tempFile))
		{
			// Get some needed libraries
			ximport('Hubzero_Content_Server');
			
			$xserver = new Hubzero_Content_Server();
			$xserver->filename($tempFile);		
			$xserver->serve_inline($tempFile);
			exit;
		}
		else
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				'There was an error creating a receipt', 'error'
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
		$id = JRequest::getInt('id', 0);

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
				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}

				foreach ($this->view->orderitems as $r)
				{
					$params = new $paramsClass($r->params);
					$selections = new $paramsClass($r->selections);

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

			$this->view->customer =& JUser::getInstance($this->view->row->uid);

			// Check available user funds		
			$BTL = new Hubzero_Bank_Teller($this->database, $this->view->row->uid);
			$balance = $BTL->summary();
			$credit  = $BTL->credit_summary();
			$this->view->funds = $balance;
		}

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
		$dateFormat = '%d %b, %Y';
		$tz = null;

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$dateFormat = 'd M, Y';
			$tz = false;
		}

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$jconfig =& JFactory::getConfig();

		$statusmsg = '';
		$email = 1; // turn emailing on/off
		$emailbody = '';
		ximport('Hubzero_Bank');

		$data = array_map('trim', $_POST);
		$action = (isset($data['action'])) ? $data['action'] : '';
		$id = ($data['id']) ? $data['id'] : 0 ;
		$cost = intval($data['total']);

		if ($id)
		{
			// initiate extended database class
			$row = new Order($this->database);
			$row->load($id);
			$row->notes = Hubzero_Filter::cleanXss($data['notes']);
			$hold = $row->total;
			$row->total = $cost;

			// get user bank account
			//$xprofile =& Hubzero_User_Profile::getInstance($row->uid);
			$xprofile =& JUser::getInstance($row->uid);
			$BTL_Q = new Hubzero_Bank_Teller($this->database, $xprofile->get('id'));

			// start email message
			$emailbody .= JText::_('THANKYOU').' '.JText::_('IN_THE').' '.$jconfig->getValue('config.sitename').' '.JText::_('STORE').'!'."\r\n\r\n";
			$emailbody .= JText::_('EMAIL_UPDATE').':'."\r\n";
			$emailbody .= '----------------------------------------------------------'."\r\n";
			$emailbody .= JText::_('ORDER').' '.JText::_('NUM').': '. $id ."\r\n";
			$emailbody .= "\t".JText::_('ORDER').' '.JText::_('TOTAL').': '. $cost ."\r\n";
			$emailbody .= "\t\t".JText::_('PLACED').': '. JHTML::_('date', $row->ordered, $dateFormat, $tz)."\r\n";
			$emailbody .= "\t\t".JText::_('STATUS').': ';

			switch ($action)
			{
				case 'complete_order':
					// adjust credit
					$credit = $BTL_Q->credit_summary();
					$adjusted = $credit - $hold;
					$BTL_Q->credit_adjustment($adjusted);

					// remove hold 
					$sql = "DELETE FROM #__users_transactions WHERE category='store' AND type='hold' AND referenceid='".$id."' AND uid=".$row->uid;
					$this->database->setQuery($sql);
					if (!$this->database->query())
					{
						JError::raiseError(500, $this->database->getErrorMsg());
						return;
					}
					// debit account
					if ($cost > 0)
					{
						$BTL_Q->withdraw($cost, JText::_('BANKING_PURCHASE').' #'.$id, 'store', $id);
					}

					// update order information
					$row->status_changed = date("Y-m-d H:i:s");
					$row->status = 1;
					$statusmsg = JText::_('ORDER') . ' #' . $id . ' ' . JText::_('HAS_BEEN') . ' ' . strtolower(JText::_('COMPLETED')) . '.';
				break;

				case 'cancel_order':
					// adjust credit
					$credit = $BTL_Q->credit_summary();
					$adjusted = $credit - $hold;
					$BTL_Q->credit_adjustment($adjusted);

					// remove hold
					$sql = "DELETE FROM #__users_transactions WHERE category='store' AND type='hold' AND referenceid='".$id."' AND uid=".$row->uid;
					$this->database->setQuery($sql);
					if (!$this->database->query())
					{
						JError::raiseError(500, $this->database->getErrorMsg());
						return;
					}
					// update order information
					$row->status_changed = date("Y-m-d H:i:s");
					$row->status = 2;

					$statusmsg = JText::_('ORDER') . ' #' . $id . ' ' . JText::_('HAS_BEEN') . ' ' . strtolower(JText::_('CANCELLED')) . '.';
				break;

				case 'message':
					$statusmsg = JText::_('MSG_SENT') . '.';
				break;

				default:
					$statusmsg = JText::_('ORDER_DETAILS_UPDATED') . '.';
				break;
			}

			// check content
			if (!$row->check())
			{
				JError::raiseError(500, $row->getError());
				return;
			}

			// store new content
			if (!$row->store())
			{
				JError::raiseError(500, $row->getError());
				return;
			}

			switch ($row->status)
			{
				case 0: ;
					$emailbody .= ' ' . JText::_('IN_PROCESS') . "\r\n";
					break;
				case 1:
					$emailbody .= ' '.strtolower(JText::_('COMPLETED')).' '.JText::_('ON').' '.JHTML::_('date', $row->status_changed, $dateFormat, $tz)."\r\n\r\n";
					$emailbody .= JText::_('EMAIL_PROCESSED').'.'."\r\n";
					break;
				case 2:
				default:
					$emailbody .= ' '.strtolower(JText::_('CANCELLED')).' '.JText::_('ON').' '.JHTML::_('date', $row->status_changed, $dateFormat, $tz)."\r\n\r\n";
					$emailbody .= JText::_('EMAIL_CANCELLED').'.'."\r\n";
					break;
			}

			if ($data['message'])
			{ // add custom message			
				$emailbody .= $data['message']."\r\n";
			}

			// send email
			if ($action || $data['message'])
			{
				if ($email)
				{
					ximport('Hubzero_Toolbox');
					$admin_email = $jconfig->getValue('config.mailfrom');
					$subject     = $jconfig->getValue('config.sitename') . ' ' . JText::_('STORE') . ': ' . JText::_('EMAIL_UPDATE_SHORT') . ' #' . $id;
					$from        = $jconfig->getValue('config.sitename') . ' ' . JText::_('STORE');
					$hub         = array('email' => $admin_email, 'name' => $from);

					Hubzero_Toolbox::send_email($hub, $row->email, $subject, $emailbody);
				}
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$statusmsg
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
