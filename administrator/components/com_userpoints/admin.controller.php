<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------

class UserpointsController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	
	//-----------
	
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
		$this->_option = 'com_'.$this->_name;
	}
	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	//-----------
	
	private function getTask()
	{
		$task = JRequest::getVar( 'task', '' );
		$this->_task = $task;
		return $task;
	}
		
	//-----------
	
	function execute( )
	{
		
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		switch ( $this->getTask() ) 
		{
			case 'config':     			$this->config();    	break;
			case 'saveconfig': 			$this->save_config(); 	break;
			
			case 'user':       			$this->edit();      	break;
			case 'save':       			$this->save();      	break;
			case 'edit':       			$this->edit();      	break;
			case 'cancel':     			$this->cancel();    	break;
			case 'batch':      			$this->batch();     	break;
			case 'process_batch':      	$this->process_batch(); break;
			case 'royalty':      		$this->royalty(); 		break;
			
			default: $this->summary(); break;
		}
	}

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	public function summary( )
	{
		$database =& JFactory::getDBO();
		
		// Get top earners
		$database->setQuery( "SELECT * FROM #__users_points ORDER BY earnings DESC, balance DESC LIMIT 15" );
		$users = $database->loadObjectList();
		
		$stats = array();
		$BT  = new BankTransaction( $database);
	
		
		$thismonth = date( 'Y-m');
		$lastmonth = date('Y-m', time() - (32 * 24 * 60 * 60));
		
		// Get overall earnings
		$stats[]= array(
			'memo'=>'Earnings - Total', 
			'class'=>'earntotal',
			'alltimepts'=>$BT->getTotals( '', 'deposit', '', 0, '', '', 1, '' ),
			'thismonthpts'=>$BT->getTotals( '', 'deposit', '', 0, '', '', 1, $thismonth ),
			'lastmonthpts'=>$BT->getTotals( '', 'deposit', '', 0, '', '', 1, $lastmonth ),
			'alltimetran'=>$BT->getTotals( '', 'deposit', '', 0, '', '', 1, '', $calc=2 ),
			'thismonthtran'=>$BT->getTotals( '', 'deposit', '', 0, '', '', 1, $thismonth, $calc=2 ),
			'lastmonthtran'=>$BT->getTotals( '', 'deposit', '', 0, '', '', 1, $lastmonth, $calc=2 ),
			'avg'=>round($BT->getTotals( '', 'deposit', '', 0, '', '', 1, '', $calc=1 )));
			
			
		// Get overall earnings on Answers
		$stats[]= array(
			'memo'=>'Earnings: Answers',
			'class'=>'earn', 
			'alltimepts'=>$BT->getTotals( 'answers', 'deposit', '', 0, '', '', 1, '' ),
			'thismonthpts'=>$BT->getTotals( 'answers', 'deposit', '', 0, '', '', 1, $thismonth ),
			'lastmonthpts'=>$BT->getTotals( 'answers', 'deposit', '', 0, '', '', 1, $lastmonth ),
			'alltimetran'=>$BT->getTotals( 'answers', 'deposit', '', 0, '', '', 1, '', $calc=2 ),
			'thismonthtran'=>$BT->getTotals( 'answers', 'deposit', '', 0, '', '', 1, $thismonth, $calc=2 ),
			'lastmonthtran'=>$BT->getTotals( 'answers', 'deposit', '', 0, '', '', 1, $lastmonth, $calc=2 ),
			'avg'=>round($BT->getTotals( 'answers', 'deposit', '', 0, '', '', 1, '', $calc=1 )));
	
			
		// Get overall earnings on Wishes
		$stats[]= array(
			'memo'=>'Earnings: Wish List',
			'class'=>'earn', 
			'alltimepts'=>$BT->getTotals( 'wish', 'deposit', '', 0, '', '', 1, '' ),
			'thismonthpts'=>$BT->getTotals( 'wish', 'deposit', '', 0, '', '', 1, $thismonth ),
			'lastmonthpts'=>$BT->getTotals( 'wish', 'deposit', '', 0, '', '', 1, $lastmonth ),
			'alltimetran'=>$BT->getTotals( 'wish', 'deposit', '', 0, '', '', 1, '', $calc=2 ),
			'thismonthtran'=>$BT->getTotals( 'wish', 'deposit', '', 0, '', '', 1, $thismonth, $calc=2 ),
			'lastmonthtran'=>$BT->getTotals( 'wish', 'deposit', '', 0, '', '', 1, $lastmonth, $calc=2 ),
			'avg'=>round($BT->getTotals( 'wish', 'deposit', '', 0, '', '', 1, '', $calc=1 )));
	
		
		// Get overall spending
		$stats[]= array(
			'memo'=>'Spending - Total',
			'class'=>'spendtotal', 
			'alltimepts'=>$BT->getTotals( '', 'withdraw', '', 0, '', '', 1, '' ),
			'thismonthpts'=>$BT->getTotals( '', 'withdraw', '', 0, '', '', 1, $thismonth ),
			'lastmonthpts'=>$BT->getTotals( '', 'withdraw', '', 0, '', '', 1, $lastmonth ),
			'alltimetran'=>$BT->getTotals( '', 'withdraw', '', 0, '', '', 1, '', $calc=2 ),
			'thismonthtran'=>$BT->getTotals( '', 'withdraw', '', 0, '', '', 1, $thismonth, $calc=2 ),
			'lastmonthtran'=>$BT->getTotals( '', 'withdraw', '', 0, '', '', 1, $lastmonth, $calc=2 ),
			'avg'=>round($BT->getTotals( '', 'withdraw', '', 0, '', '', 1, '', $calc=1 )));
		
		// Get overall spending in Store
		$stats[]= array(
			'memo'=>'Spending: Store', 
			'class'=>'spend', 
			'alltimepts'=>$BT->getTotals( 'store', 'withdraw', '', 0, '', '', 1, '' ),
			'thismonthpts'=>$BT->getTotals( 'store', 'withdraw', '', 0, '', '', 1, $thismonth ),
			'lastmonthpts'=>$BT->getTotals( 'store', 'withdraw', '', 0, '', '', 1, $lastmonth ),
			'alltimetran'=>$BT->getTotals( 'store', 'withdraw', '', 0, '', '', 1, '', $calc=2 ),
			'thismonthtran'=>$BT->getTotals( 'store', 'withdraw', '', 0, '', '', 1, $thismonth, $calc=2 ),
			'lastmonthtran'=>$BT->getTotals( 'store', 'withdraw', '', 0, '', '', 1, $lastmonth, $calc=2 ),
			'avg'=>round($BT->getTotals( 'store', 'withdraw', '', 0, '', '', 1, '', $calc=1 )));
		
		// Get overall spending on Answers
		$stats[]= array(
			'memo'=>'Spending: Answers', 
			'class'=>'spend',
			'alltimepts'=>$BT->getTotals( 'answers', 'withdraw', '', 0, '', '', 1, '' ),
			'thismonthpts'=>$BT->getTotals( 'answers', 'withdraw', '', 0, '', '', 1, $thismonth ),
			'lastmonthpts'=>$BT->getTotals( 'answers', 'withdraw', '', 0, '', '', 1, $lastmonth ),
			'alltimetran'=>$BT->getTotals( 'answers', 'withdraw', '', 0, '', '', 1, '', $calc=2 ),
			'thismonthtran'=>$BT->getTotals( 'answers', 'withdraw', '', 0, '', '', 1, $thismonth, $calc=2 ),
			'lastmonthtran'=>$BT->getTotals( 'answers', 'withdraw', '', 0, '', '', 1, $lastmonth, $calc=2 ),
			'avg'=>round($BT->getTotals( 'answers', 'withdraw', '', 0, '', '', 1, '', $calc=1 )));
		
		// Get overall spending on Wishes
		$stats[]= array(
			'memo'=>'Spending: Wish List', 
			'class'=>'spend',
			'alltimepts'=>$BT->getTotals( 'wish', 'withdraw', '', 0, '', '', 1, '' ),
			'thismonthpts'=>$BT->getTotals( 'wish', 'withdraw', '', 0, '', '', 1, $thismonth ),
			'lastmonthpts'=>$BT->getTotals( 'wish', 'withdraw', '', 0, '', '', 1, $lastmonth ),
			'alltimetran'=>$BT->getTotals( 'wish', 'withdraw', '', 0, '', '', 1, '', $calc=2 ),
			'thismonthtran'=>$BT->getTotals( 'wish', 'withdraw', '', 0, '', '', 1, $thismonth, $calc=2 ),
			'lastmonthtran'=>$BT->getTotals( 'wish', 'withdraw', '', 0, '', '', 1, $lastmonth, $calc=2 ),
			'avg'=>round($BT->getTotals( 'wish', 'withdraw', '', 0, '', '', 1, '', $calc=1 )));
		
		// Get royalties
		$stats[]= array(
			'memo'=>'Royalties - Total', 
			'class'=>'royaltytotal',
			'alltimepts'=>$BT->getTotals( '', 'deposit', '', $royalty=1, '', '', 1, '' ),
			'thismonthpts'=>$BT->getTotals( '', 'deposit', '', $royalty=1, '', '', 1, $thismonth ),
			'lastmonthpts'=>$BT->getTotals( '', 'deposit', '', $royalty=1, '', '', 1, $lastmonth ),
			'alltimetran'=>$BT->getTotals( '', 'deposit', '', $royalty=1, '', '', 1, '', $calc=2 ),
			'thismonthtran'=>$BT->getTotals( '', 'deposit', '', $royalty=1, '', '', 1, $thismonth, $calc=2 ),
			'lastmonthtran'=>$BT->getTotals( '', 'deposit', '', $royalty=1, '', '', 1, $lastmonth, $calc=2 ),
			'avg'=>round($BT->getTotals( '', 'deposit', '', $royalty=1, '', '', 1, '', $calc=1 )));
		
		// Get royalties on answers
		$stats[]= array(
			'memo'=>'Royalties: Answers',
			'class'=>'royalty', 
			'alltimepts'=>$BT->getTotals( 'answers', 'deposit', '', $royalty=1, '', '', 1, '' ),
			'thismonthpts'=>$BT->getTotals( 'answers', 'deposit', '', $royalty=1, '', '', 1, $thismonth ),
			'lastmonthpts'=>$BT->getTotals( 'answers', 'deposit', '', $royalty=1, '', '', 1, $lastmonth ),
			'alltimetran'=>$BT->getTotals( 'answers', 'deposit', '', $royalty=1, '', '', 1, '', $calc=2 ),
			'thismonthtran'=>$BT->getTotals( 'answers', 'deposit', '', $royalty=1, '', '', 1, $thismonth, $calc=2 ),
			'lastmonthtran'=>$BT->getTotals( 'answers', 'deposit', '', $royalty=1, '', '', 1, $lastmonth, $calc=2 ),
			'avg'=>round($BT->getTotals( 'answers', 'deposit', '', $royalty=1, '', '', 1, '', $calc=1 )));
		
		// Get royalties on reviews
		$stats[]= array(
			'memo'=>'Royalties: Reviews', 
			'class'=>'royalty',
			'alltimepts'=>$BT->getTotals( 'review', 'deposit', '', $royalty=1, '', '', 1, '' ),
			'thismonthpts'=>$BT->getTotals( 'review', 'deposit', '', $royalty=1, '', '', 1, $thismonth ),
			'lastmonthpts'=>$BT->getTotals( 'review', 'deposit', '', $royalty=1, '', '', 1, $lastmonth ),
			'alltimetran'=>$BT->getTotals( 'review', 'deposit', '', $royalty=1, '', '', 1, '', $calc=2 ),
			'thismonthtran'=>$BT->getTotals( 'review', 'deposit', '', $royalty=1, '', '', 1, $thismonth, $calc=2 ),
			'lastmonthtran'=>$BT->getTotals( 'review', 'deposit', '', $royalty=1, '', '', 1, $lastmonth, $calc=2 ),
			'avg'=>round($BT->getTotals( 'review', 'deposit', '', $royalty=1, '', '', 1, '', $calc=1 )));
			
		// Get royalties on resource contributions
		$stats[]= array(
			'memo'=>'Royalties: Resources',
			'class'=>'royalty', 
			'alltimepts'=>$BT->getTotals( 'resource', 'deposit', '', $royalty=1, '', '', 1, '' ),
			'thismonthpts'=>$BT->getTotals( 'resource', 'deposit', '', $royalty=1, '', '', 1, $thismonth ),
			'lastmonthpts'=>$BT->getTotals( 'resource', 'deposit', '', $royalty=1, '', '', 1, $lastmonth ),
			'alltimetran'=>$BT->getTotals( 'resource', 'deposit', '', $royalty=1, '', '', 1, '', $calc=2 ),
			'thismonthtran'=>$BT->getTotals( 'resource', 'deposit', '', $royalty=1, '', '', 1, $thismonth, $calc=2 ),
			'lastmonthtran'=>$BT->getTotals( 'resource', 'deposit', '', $royalty=1, '', '', 1, $lastmonth, $calc=2 ),
			'avg'=>round($BT->getTotals( 'resource', 'deposit', '', $royalty=1, '', '', 1, '', $calc=1 )));
		
		
		
		
		UserpointsHTML::users( $users, $this->_option, $stats);		
		
	}

	//-----------

	public function edit( )
	{
		$database =& JFactory::getDBO();
	
		$uid = JRequest::getInt ('uid', 0 );
		
		if($uid) {
			$row = new BankAccount( $database );
			$row->load_uid( $uid );

			if(!$row->balance) {
				$row->uid = $uid;
				$row->balance = 0;
				$row->earnings = 0;
			}
			
			$database->setQuery( "SELECT * FROM #__users_transactions WHERE uid=".$uid." ORDER BY created DESC, id DESC" );
			$history = $database->loadObjectList();
		
			UserpointsHTML::edit( $database, $row, $this->_option, $history );
		} else {
			UserpointsHTML::find( $this->_option );
		}
	}

	//-----------

	public function cancel( )
	{

		$url  = 'index.php?option='.$this->_option;
		$this->_redirect = $url;
		
	}

	//-----------

	public function save( )
	{
		$database =& JFactory::getDBO();

		$id = JRequest::getInt( 'id', 0 );

		$row = new BankAccount( $database );
		if (!$row->bind( $_POST )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		$row->uid = intval($row->uid);
		$row->balance = intval($row->balance);
		$row->earnings = intval($row->earnings);

		if(isset($_POST['amount']) && intval($_POST['amount'])>0 && intval($_POST['amount'])) {
			$data = array();
			$data['uid'] = $row->uid;
			$data['type'] = JRequest::getVar( 'type', '' );
			$data['category'] = JRequest::getVar( 'category', 'general', 'post' );
			$data['amount'] = JRequest::getInt('amount', 0 );
			$data['description'] = JRequest::getVar( 'description', 'Reason unspecified', 'post' );
			$data['created'] = date( 'Y-m-d H:i:s', time() );

			switch($data['type'])
			{
				case 'withdraw':
					$row->balance  -= $data['amount'];
					break;
				case 'deposit':
					$row->balance  += $data['amount'];
					$row->earnings += $data['amount'];
					break;
				case 'creation':
					$row->balance  = $data['amount'];
					$row->earnings = $data['amount'];
					break;
			}

			$data['balance'] = $row->balance;
		
			$BT = new BankTransaction( $database );
			if($data['description']=='') { $data['description'] = 'Reason unspecified'; }
			if($data['category']=='') { $data['category'] = 'general'; }
			
			if (!$BT->bind( $data )) {
				echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}
			if (!$BT->check()) {
				echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}
			if (!$BT->store()) {
				echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
				exit();
			}
		}

		if (!$row->check()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		$this->_redirect = 'index2.php?option='.$this->_option.'&task=edit&uid='.$row->uid ;
		$this->_message = 'User info saved';
	}

	//-----------

	public function config( )
	{
		$database =& JFactory::getDBO();
		
		$database->setQuery( "SELECT * FROM #__users_points_config" );
		$params = $database->loadObjectList();
		
		UserpointsHTML::config( $params, $this->_option );
	}
	
	//-----------
	
	public function save_config( ) 
	{
		$database =& JFactory::getDBO();

		$points = JRequest::getVar( 'points', array() );
		$descriptions = JRequest::getVar( 'description', array() );
		$aliases = JRequest::getVar( 'alias', array() );

		$query = 'DELETE FROM #__users_points_config';
		$database->setQuery( $query );
		$database->query();
		

		for ($i=0; $i < count($points); $i++)
		{
	    	$point = intval($points[$i]);
	    	$description = $descriptions[$i];
			$alias = $aliases[$i];
			if ($point != '') {
			    $id = intval( $i);
			    $query = "INSERT INTO #__users_points_config VALUES ($id,'$description','$alias', '$point')";
				$database->setQuery( $query );
				$database->query();
				
			}
		}

		$this->_redirect = 'index.php?option='.$this->_option.'&task=config';
		$this->_message = JText::_('Config Saved');
		
	}
	//------------
	
	public function batch()
	{		
		UserpointsHTML::batch( $this->_option );
	}
	
	//------------
	
	public function process_batch()
	{
		$database 		=& JFactory::getDBO();
		$msg 			= '';
		$duplicate 		= 0;
		$xprofile 			=& XFactory::getProfile();
		
		$ref 				= JRequest::getInt ('ref', 0, 'post');
		$category 			= JRequest::getVar('com', 'general','post') ? JRequest::getVar('com') : 'general';
		$action 		    = JRequest::getVar ('action', 'batch','post') ?  JRequest::getVar ('action') : 'batch';
		$users 				= JRequest::getVar ( 'users', '' );
		$data['type'] 		= JRequest::getVar( 'type', '' );
		$data['amount'] 	= JRequest::getInt( 'amount', 0 );
		$data['description'] = JRequest::getVar( 'description', '' );
		$when				= date( 'Y-m-d H:i:s', time() );

			
		// make sure this function was not already run
		$MH = new MarketHistory( $database );
		$duplicate = $MH->getRecord($ref, $action, $category, '', $data['description']);
		
		if($data['amount'] && $data['description'] && $users) {
					
			if(!$duplicate) { // run only once
			
				
				// get array of affected users
				$users 		= str_replace(" ",",",$users);		
				$users 		= split(',',$users);
				$users 		= array_unique($users); // get rid of duplicates
				foreach($users as $user) {
							
					$validuser = XProfile::getInstance($user);
			
					if($user && $validuser) {
						$BTL = new BankTeller( $database, $user );
						switch($data['type'])
						{
							case 'withdraw':
								$BTL->withdraw($data['amount'], $data['description'], $category, $ref);
							break;
							case 'deposit':
								$BTL->deposit($data['amount'], $data['description'], $category, $ref);
							break;
						}
					}				
				}
					
				// Save log
				$MH = new MarketHistory( $database );
				$data['itemid']       = $ref;
				$data['date']         = date("Y-m-d H:i:s");
				$data['market_value'] = $data['amount'];
				$data['category']     = $category ? $category : 'general';	
				$data['action']       = $action ? $action : 'batch';
				$data['log']          = $data['description'];
				
				if (!$MH->bind( $data )) {
					$err = $MH->getError();
				}
				
				if (!$MH->store()) {
					$err = $MH->getError();
				}
				
				$msg = 'Batch transaction was processed successfully.';
			
			}
			else {
			$msg = 'This batch transaction was already processed earlier. Use a different identifier if you need to run it again.';
			}		
		}
		else {
			$msg = 'Could not process. Some required fields are missing.';
		}
	
		
		// show output if run manually						
		$this->_redirect = 'index.php?option='.$this->_option.'&task=batch';
		$this->_message = JText::_($msg);
			
	}
	
	//--------------------------------------------------------
	// Process Royalties
	//--------------------------------------------------------
	
	public function royalty()
	{
		$database 	=& JFactory::getDBO();
		$auto  		= JRequest::getInt ('auto', 1);
		$msg 		= '';
		$action 	= 'royalty';
			
		if(!$auto) { 
			$juser 	=& JFactory::getUser();
			$who 	= $juser->get('id');
		}
		else {
			$who = 0;
		}
		
		// What month/year is it now?
		$curmonth = date("F");
		$curyear = date("Y-m");
		$ref = 	strtotime($curyear);
		$msg = 'Royalties on Answers for '.$curyear.' were distributed successfully.';
		$rmsg = 'Royalties on Reviews for '.$curyear.' were distributed successfully.';
		$resmsg = 'Royalties on Resources for '.$curyear.' were distributed successfully.';

				
		// Make sure we distribute royalties only once/ month
		$MH = new MarketHistory( $database );
		$royaltyAnswers = $MH->getRecord('', $action, 'answers', $curyear, $msg);
		$royaltyReviews = $MH->getRecord('', $action, 'reviews', $curyear, $rmsg);
		$royaltyResources = $MH->getRecord('', $action, 'resources', $curyear, $resmsg);
		
		$AE = new AnswersEconomy( $database );
		$accumulated = 0;
		
		// Get Royalties on Answers		
		if(!$royaltyAnswers) { 
			$rows = $AE->getQuestions();
			
			if($rows) {
				foreach($rows as $r) {			
					$AE->distribute_points ($r->id, $r->q_owner, $r->a_owner, $action);
					$accumulated = $accumulated + $AE->calculate_marketvalue($r->id, $action);
				}
				
				
				// make a record of royalty payment
				if(intval($accumulated) > 0) {
					$MH = new MarketHistory( $database  );
					$data['itemid']       = $ref;
					$data['date']         = date("Y-m-d H:i:s");
					$data['market_value'] = $accumulated;
					$data['category']     = 'answers';	
					$data['action']       = $action;
					$data['log']          = $msg;
					
					if (!$MH->bind( $data )) {
						$err = $MH->getError();
					}
					
					if (!$MH->store()) {
						$err = $MH->getError();
					}
				}
		
			}
			else {
				$msg = 'There were no questions eligible for royalty payment. ';
			}
			
			
		}
		else {
			$msg = 'Royalties on Answers for '.$curyear.' were previously distributed. ';
			//if($auto) { return 0; }
		}
		
		// Get Royalties on Resource Reviews
		if(!$royaltyReviews) { 
			
			// get eligible 
			$RE = new ReviewsEconomy( $database );
			$reviews = $RE->getReviews();
			
			// do we have ratings on reviews enabled?
			$param = JPluginHelper::getPlugin( 'resources', 'reviews' );
			$plparam = new JParameter( $param->params );
			$voting = $plparam->get('voting');
				
			$accumulated = 0;		
			if($reviews && $voting) {
				
				foreach($reviews as $r) {			
						$RE->distribute_points ($r, $action);
						$accumulated = $accumulated + $RE->calculate_marketvalue($r, $action);
				}
				
				$msg .= $rmsg;
			}
			else {
				$msg .= 'There were no reviews eligible for royalty payment. ';
			}
			
			// make a record of royalty payment
			if(intval($accumulated) > 0) {
				
					$MH = new MarketHistory( $database  );
					$data['itemid']       = $ref;
					$data['date']         = date("Y-m-d H:i:s");
					$data['market_value'] = $accumulated;
					$data['category']     = 'reviews';	
					$data['action']       = $action;
					$data['log']          = $rmsg;
					
					if (!$MH->bind( $data )) {
						$err = $MH->getError();
					}
					
					if (!$MH->store()) {
						$err = $MH->getError();
					}
					
					
			}
			
		}
		else {
			$msg .= 'Royalties on Reviews for '.$curyear.' were previously distributed. ';
			//if($auto) { return 0; }
		}
		
		// Get Royalties on Resources
		if(!$royaltyResources) { 
			// get eligible 
			$ResE = new ResourcesEconomy( $database );
			$cons = $ResE->getCons();
				
			$accumulated = 0;		
			if($cons) {
				
				foreach($cons as $con) {			
						$ResE->distribute_points ($con, $action);
						$accumulated = $accumulated + $con->ranking;
				}
				
				$msg .= $resmsg;
			}
			else {
				$msg .= 'There were no resources eligible for royalty payment. ';
			}
			
			// make a record of royalty payment
			if(intval($accumulated) > 0) {
				
					$MH = new MarketHistory( $database  );
					$data['itemid']       = $ref;
					$data['date']         = date("Y-m-d H:i:s");
					$data['market_value'] = $accumulated;
					$data['category']     = 'resources';	
					$data['action']       = $action;
					$data['log']          = $resmsg;
					
					if (!$MH->bind( $data )) {
						$err = $MH->getError();
					}
					
					if (!$MH->store()) {
						$err = $MH->getError();
					}
					
					
			}
			
			
		}
		else {
			$msg .= 'Royalties on Resources for '.$curyear.' were previously distributed. ';
			//if($auto) { return 0; }
		}
		
		
		if(!$auto) {
			// show output if run manually						
			$this->_redirect = 'index.php?option='.$this->_option;
			$this->_message = JText::_($msg);
		}
		
				
	}
	//------------
	
}
?>
