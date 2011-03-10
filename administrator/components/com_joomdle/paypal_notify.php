<?php 
/**
* PayPal IPN Handler
*
* @version 
* @package Joomdle
* @subpackage core
* @copyright Copyright (C) 2004-2007 soeren - All rights reserved.
* Modifications by Antonio Duran Terres
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

$messages = Array();
function debug_msg( $msg ) {
    global $messages;
    if( PAYPAL_DEBUG == "1" ) {
        if( !defined( "_DEBUG_HEADER")  ) {
            echo "<h2>PayPal Notify.php Debug OUTPUT</h2>";
            define( "_DEBUG_HEADER", "1" );
        }
        $messages[] = "<pre>$msg</pre>";
        echo end( $messages );
    }
}

	
if ($_POST) {
	header("HTTP/1.0 200 OK");

    global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_lang, $database,
    $mosConfig_mailfrom, $mosConfig_fromname;
    
        /*** access Joomla's configuration file ***/
        $my_path = dirname(__FILE__);
        
        if( file_exists($my_path."/../../../configuration.php")) {
            $absolute_path = dirname( $my_path."/../../../configuration.php" );
            require_once($my_path."/../../../configuration.php");
        }
        elseif( file_exists($my_path."/../../configuration.php")){
            $absolute_path = dirname( $my_path."/../../configuration.php" );
            require_once($my_path."/../../configuration.php");
        }
        elseif( file_exists($my_path."/configuration.php")){
            $absolute_path = dirname( $my_path."/configuration.php" );
            require_once( $my_path."/configuration.php" );
        }
        else {
            die( "Joomla Configuration File not found!" );
        }
        
        $absolute_path = realpath( $absolute_path );
        
//	echo "XX";
        // Set up the appropriate CMS framework
        if( class_exists( 'jconfig' ) ) {
			define( '_JEXEC', 1 );
			define( 'JPATH_BASE', $absolute_path );
			define( 'DS', DIRECTORY_SEPARATOR );
			
			// Load the framework
			require_once ( JPATH_BASE . DS . 'includes' . DS . 'defines.php' );
			require_once ( JPATH_BASE . DS . 'includes' . DS . 'framework.php' );

			// create the mainframe object
			$mainframe = & JFactory::getApplication( 'site' );
			
			// Initialize the framework
			$mainframe->initialise();
			
			// load system plugin group
			JPluginHelper::importPlugin( 'system' );
			
			// trigger the onBeforeStart events
			$mainframe->triggerEvent( 'onBeforeStart' );
			$lang =& JFactory::getLanguage();
			$mosConfig_lang = $GLOBALS['mosConfig_lang']          = strtolower( $lang->getBackwardLang() );
			// Adjust the live site path
			$mosConfig_live_site = str_replace('/administrator/components/com_virtuemart', '', JURI::base());
			$mosConfig_absolute_path = JPATH_BASE;

			/* Include Joomdle helpers functions once the framework is set up */
			require_once ('./helpers'.DIRECTORY_SEPARATOR.'content.php');
			require_once ('./helpers'.DIRECTORY_SEPARATOR.'shop.php');
        } else {
        	define('_VALID_MOS', '1');
        	require_once($mosConfig_absolute_path. '/includes/joomla.php');
        	require_once($mosConfig_absolute_path. '/includes/database.php');
        	$database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );
        	$mainframe = new mosMainFrame($database, 'com_virtuemart', $mosConfig_absolute_path );
        }

        // load Joomla Language File
        if (file_exists( $mosConfig_absolute_path. '/language/'.$mosConfig_lang.'.php' )) {
            require_once( $mosConfig_absolute_path. '/language/'.$mosConfig_lang.'.php' );
        }
        elseif (file_exists( $mosConfig_absolute_path. '/language/english.php' )) {
            require_once( $mosConfig_absolute_path. '/language/english.php' );
        }
    /*** END of Joomla config ***/
    
    
    /*** VirtueMart part ***/        
        require_once($mosConfig_absolute_path.'/administrator/components/com_virtuemart/virtuemart.cfg.php');
        include_once( ADMINPATH.'/compat.joomla1.5.php' );
        require_once( ADMINPATH. 'global.php' );
        require_once( CLASSPATH. 'ps_main.php' );
        
        /* @MWM1: Logging enhancements (file logging & composite logger). */
        $vmLogIdentifier = "notify.php";
        require_once(CLASSPATH."Log/LogInit.php");
              
        /* Load the PayPal Configuration File */ 
        require_once( CLASSPATH. 'payment/ps_paypal.cfg.php' );
        
		if( PAYPAL_DEBUG == "1" ) {
			$debug_email_address = $mosConfig_mailfrom;
		}
		else {
			$debug_email_address = PAYPAL_EMAIL;
		}
	    // restart session
	    // Constructor initializes the session!
	    $sess = new ps_session();                        
	    
    /*** END VirtueMart part ***/
    
    debug_msg( "1. Finished Initialization of the notify.php script" );

    $post_msg = "";
    foreach ($_POST as $ipnkey => $ipnval) {
        $post_msg .= "$ipnkey=$ipnval&amp;";
    }
    debug_msg( "2. Received this POST: $post_msg" );

    $post_msg = "";
    /**
    * Read post from PayPal system and create reply
    * starting with: 'cmd=_notify-validate'...
    * then repeating all values sent: that's our VALIDATION.
    **/
    $workstring = 'cmd=_notify-validate'; // Notify validate
    $i = 1;
    foreach ($_POST as $ipnkey => $ipnval) {
        if (get_magic_quotes_gpc())
            // Fix issue with magic quotes
            $ipnval = stripslashes ($ipnval);
            
        if (!eregi("^[_0-9a-z-]{1,30}$",$ipnkey)  || !strcasecmp ($ipnkey, 'cmd'))  { 
            // ^ Antidote to potential variable injection and poisoning
            unset ($ipnkey); 
            unset ($ipnval); 
        } 
        // Eliminate the above
        // Remove empty keys (not values)
        if (@$ipnkey != '') { 
          //unset ($_POST); // Destroy the original ipn post array, sniff...
          $workstring.='&'.@$ipnkey.'='.urlencode(@$ipnval); 
        }
        $post_msg .= "key ".$i++.": $ipnkey, value: $ipnval<br />";
    } // Notify string
    
    
    $paypal_receiver_email = PAYPAL_EMAIL;
    $business = trim(stripslashes($_POST['business'])); 
    $item_name = trim(stripslashes($_POST['item_name']));
    $item_number = trim(stripslashes(@$_POST['item_number']));
    $payment_status = trim(stripslashes($_POST['payment_status']));
    
    // The order total amount including taxes, shipping and discounts
    $mc_gross = trim(stripslashes($_POST['mc_gross']));
    
    // Can be USD, GBP, EUR, CAD, JPY
    $currency_code =  trim(stripslashes($_POST['mc_currency']));
    
    $txn_id = trim(stripslashes($_POST['txn_id']));
    $receiver_email = trim(stripslashes($_POST['receiver_email']));
    $payer_email = trim(stripslashes($_POST['payer_email']));
    $payment_date = trim(stripslashes($_POST['payment_date']));
    
    // The Order Number (not order_id !)
    $invoice =  trim(stripslashes($_POST['invoice']));
    
    $amount =  trim(stripslashes(@$_POST['amount']));
    
    $quantity = trim(stripslashes($_POST['quantity']));
    $pending_reason = trim(stripslashes(@$_POST['pending_reason']));
    $payment_method = trim(stripslashes(@$_POST['payment_method'])); // deprecated
    $payment_type = trim(stripslashes(@$_POST['payment_type']));
    
    // Billto
    $first_name = trim(stripslashes($_POST['first_name']));
    $last_name = trim(stripslashes($_POST['last_name']));
    $address_street = trim(stripslashes(@$_POST['address_street']));
    $address_city = trim(stripslashes(@$_POST['address_city']));
    $address_state = trim(stripslashes(@$_POST['address_state']));
    $address_zipcode = trim(stripslashes(@$_POST['address_zip']));
    $address_country = trim(stripslashes(@$_POST['address_country']));
    $residence_country = trim(stripslashes(@$_POST['residence_country']));
    
    $address_status = trim(stripslashes(@$_POST['address_status']));
    
    $payer_status = trim(stripslashes($_POST['payer_status']));
    $notify_version = trim(stripslashes($_POST['notify_version'])); 
    $verify_sign = trim(stripslashes($_POST['verify_sign'])); 
    $custom = trim(stripslashes(@$_POST['custom'])); 
    $txn_type = trim(stripslashes($_POST['txn_type'])); 
    
    /*
    if($paypal_receiver_email != "$receiver_email"){
        $error_message .= "Error code 501. Possible fraud. Error with receiver_email. receiver_email = $receiver_email\n";
        $error++;
    }  
    */
    if( PAYPAL_DEBUG != "1" ) {
    	// Get the list of IP addresses for www.paypal.com and notify.paypal.com
        $paypal_iplist = gethostbynamel('www.paypal.com');
		$paypal_iplist2 = gethostbynamel('notify.paypal.com');
        $paypal_iplist = array_merge( $paypal_iplist, $paypal_iplist2 );

        $paypal_sandbox_hostname = 'ipn.sandbox.paypal.com';
        $remote_hostname = gethostbyaddr( $_SERVER['REMOTE_ADDR'] );
        
        $valid_ip = false;
        
        if( $paypal_sandbox_hostname == $remote_hostname ) {
            $valid_ip = true;
            $hostname = 'www.sandbox.paypal.com';
        }
        else {
            $ips = "";
            // Loop through all allowed IPs and test if the remote IP connected here
            // is a valid IP address
            foreach( $paypal_iplist as $ip ) {
                $ips .= "$ip,\n";
                $parts = explode( ".", $ip );
                $first_three = $parts[0].".".$parts[1].".".$parts[2];
                if( preg_match("/^$first_three/", $_SERVER['REMOTE_ADDR']) ) {
                    $valid_ip = true;
                }
            }
            $hostname = 'www.paypal.com';
        }
        
        if( !$valid_ip ) {
            debug_msg( "Error code 506. Possible fraud. Error with REMOTE IP ADDRESS = ".$_SERVER['REMOTE_ADDR'].". 
                        The remote address of the script posting to this notify script does not match a valid PayPal ip address\n" );
            
            $mailsubject = "PayPal IPN Transaction on your site: Possible fraud";
            $mailbody = "Error code 506. Possible fraud. Error with REMOTE IP ADDRESS = ".$_SERVER['REMOTE_ADDR'].". 
                        The remote address of the script posting to this notify script does not match a valid PayPal ip address\n
            These are the valid IP Addresses: $ips
            
            The Order ID received was: $invoice";
            vmMail( $mosConfig_mailfrom, $mosConfig_fromname, $debug_email_address, $mailsubject, $mailbody );
            
            exit();
        }
    }
    /**--------------------------------------------
    * Create message to post back to PayPal...
    * Open a socket to the PayPal server...
    *--------------------------------------------*/
    // To Debug this script, just visit www.sandbox.paypal.com/cgi-bin/webscr
    if(@PAYPAL_DEBUG=="1") {
        $uri = "/cgi-bin/webscr";
        $hostname = "www.sandbox.paypal.com";
        
    }
    // regular mode: Post to paypal.com
    else {
        $uri = "/cgi-bin/webscr";
    }   
    $header = "POST $uri HTTP/1.0\r\n";
    $header.= "User-Agent: PHP/".phpversion()."\r\n";
    $header.= "Referer: ".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].@$_SERVER['QUERY_STRING']."\r\n";
    $header.= "Server: ".$_SERVER['SERVER_SOFTWARE']."\r\n";
    $header.= "Host: ".$hostname.":80\r\n";
    $header.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header.= "Content-Length: ".strlen($workstring)."\r\n";
    $header.= "Accept: */*\r\n\r\n";
    
    $fp = fsockopen ( $hostname, 80, $errno, $errstr, 30);
    
    debug_msg( "3. Connecting to: $hostname"."$uri
                Using these http Headers: 
                
                $header
                
                and this String:
                
                $workstring");
    //----------------------------------------------------------------------
    // Check HTTP connection made to PayPal OK, If not, print an error msg
    //----------------------------------------------------------------------
    if (!$fp) {
        $error_description = "$errstr ($errno)
        Status: FAILED";
        
        debug_msg( "4. Connection failed: $error_description" );
        
        $res = "FAILED";
        
        $mailsubject = "PayPal IPN Fatal Error on your Site";
        $mailbody = "Hello,
        A fatal error occured while processing a paypal transaction.
        ----------------------------------
        Hostname: $hostname
        URI: $uri
        $error_description";
        vmMail($mosConfig_mailfrom, $mosConfig_fromname, $debug_email_address, $mailsubject, $mailbody );
        
    }
    
    //--------------------------------------------------------
    // If connected OK, write the posted values back, then...
    //--------------------------------------------------------
    else {
      
      debug_msg( "4. Connection successful. Now posting to $hostname"."$uri" );
      
      fwrite($fp, $header . $workstring);
      $res = '';
      while (!feof($fp)) {
           $res .= fgets ($fp, 1024);
      }
      fclose ($fp);
    
      $error_description = "Response from $hostname: " . $res."\n";
      
      debug_msg( "5. $error_description ");
      
      // Get the Order Details from the database      
      $qv = "SELECT `order_id`, `order_number`, `user_id`, `order_subtotal`,
                    `order_total`, `order_currency`, `order_tax`, 
                    `order_shipping_tax`, `coupon_discount`, `order_discount`
                FROM `#__{vm}_orders` 
                WHERE `order_number`='".$invoice."'";
      $db = new ps_DB;
      $db->query($qv);
      $db->next_record();
      $order_id = $db->f("order_id");
     
      $d['order_id'] = $order_id;
      $d['notify_customer'] = "Y";

      // remove post headers if present.
      $res = preg_replace("'Content-type: text/plain'si","",$res);
      
      //-------------------------------------------
      // ...read the results of the verification...
      // If VERIFIED = continue to process the TX...
      //-------------------------------------------
        if (eregi ( "VERIFIED", $res) || @PAYPAL_VERIFIED_ONLY == '0' ) {
            //----------------------------------------------------------------------
            // If the payment_status is Completed... Get the password for the product
            // from the DB and email it to the customer.
            //----------------------------------------------------------------------
            if (eregi ("Completed", $payment_status) || eregi ("Pending", $payment_status)) {
                 
                if (empty($order_id)) {
                    $mailsubject = "PayPal IPN Transaction on your site: Order ID not found";
                    $mailbody = "The right order_id wasn't found during a PayPal transaction on your website.
                    The Order ID received was: $invoice";
                    vmMail($mosConfig_mailfrom, $mosConfig_fromname, $debug_email_address, $mailsubject, $mailbody );
                    exit();
                }
                
                // AMOUNT and CURRENCY CODE CHECK
				$amount_check = round($db->f("order_total"), 2 );
                                
                if( $mc_gross != $amount_check 
                   || $currency_code != $db->f('order_currency') ) {
                    $mailsubject = "PayPal IPN Error: Order Total/Currency Check failed";
                    $mailbody = "During a paypal transaction on your site the received amount didn't match the order total.
                    Order ID: ".$db->f('order_id').".
                    Order Number: $invoice.
                    The amount received was: $mc_gross $currency_code.
                    It should be: $amount_check ".$db->f("order_currency").".";
                    
                    vmMail($mosConfig_mailfrom, $mosConfig_fromname, $debug_email_address, $mailsubject, $mailbody );
                    exit();
                }
                
                // UPDATE THE ORDER STATUS to 'Completed'
                if(eregi ("Completed", $payment_status)) {
                    $d['order_status'] = PAYPAL_VERIFIED_STATUS;                    //Note: PAYPAL_VERIFIED_STATUS = C   // Es una forma diferente de decir completado

		    /* Get product type ID for Courses */
			$query = "SELECT product_type_id from #__vm_product_type where product_type_name='Course'";
			$db->setQuery($query);
			$items = $db->loadObjectList();

			if ($db->getErrorNum()) {
				JError::raiseWarning( 500, $db->stderr() );
			}
			$type_id = $items[0]->product_type_id;


		    /* Check all the order items:
		       If there are any courses in the order:
			       Courses are processed here by Joomdle
			       Other product types are processed by VM
			Else we leave VM work as usual
		       */
			$query = "SELECT * from #__vm_order_item where order_id='$order_id'";
			$db->setQuery($query);
			$items = $db->loadObjectList();

			if ($db->getErrorNum()) {
				JError::raiseWarning( 500, $db->stderr() );
			}

                        $query = "SELECT * from #__vm_order_item where order_id='$order_id'";
			$db->setQuery($query);
			$items_copy = $db->loadObjectList();

			if (count ($items) != 0)
			{
				//$items_copy = array ($items);
				$courses_in_order = 0;
				foreach ($items as $item)
				{
					$product_id = $item->product_id;
					$query = "SELECT product_id from #__vm_product_product_type_xref where product_id='$product_id' and product_type_id='$type_id'";
					$db->setQuery($query);
					$p_ids = $db->loadObjectList();

					if ($db->getErrorNum()) {
						JError::raiseWarning( 500, $db->stderr() );
					}
					/* If it is a course */
					if (count ($p_ids))
					{
						$courses_in_order = 1;
						break;
					}
				}



				$order_processed = 0;
				if ($courses_in_order)
				{
					foreach ($items_copy as $item)
					{	
						$product_id = $item->product_id;
						$query = "SELECT product_id from #__vm_product_type_$type_id where product_id='$product_id'";
						$db->setQuery($query);
						$p_ids = $db->loadObjectList();

						if ($db->getErrorNum()) {
							JError::raiseWarning( 500, $db->stderr() );
						} 
						/* If it is a course */
						if (count ($p_ids))
						{
						    /* add_user_enrol processes the whole order, so we exit here */
							if ($order_processed)
								continue;
						    /* Do the enrolment */
						    $user_id = $db->f("user_id");
						    /* add_order_enrols enrols the user and set order status */
						    JoomdleHelperShop::add_order_enrols ($order_id, $user_id);
						    $order_processed = 1;
						}
						else {
							require_once( CLASSPATH. 'ps_product.php' );
						     require_once ( CLASSPATH . 'ps_order.php' );
						     $ps_order= new ps_order;
						     $d['order_item_id'] = $item->order_item_id;
						     $ps_order->order_status_update($d);
						}  
				           }
				  
			        }
				else
				{
				     require_once ( CLASSPATH . 'ps_order.php' );
				     $ps_order= new ps_order;
				     $ps_order->order_status_update($d);

				}  
			}



                }
                // UPDATE THE ORDER STATUS to 'Pending'
                elseif(eregi ("Pending", $payment_status)) {
			$d['order_status'] = PAYPAL_PENDING_STATUS;
			require_once ( CLASSPATH . 'ps_order.php' );
			$ps_order= new ps_order;
			$ps_order->order_status_update($d);
                }
     	/* If it is a normal product */
              //  require_once ( CLASSPATH . 'ps_order.php' );
              //  $ps_order= new ps_order;
              //  $ps_order->order_status_update($d);
                $mailsubject = "PayPal IPN txn on your site";
                $mailbody = "Hello,\n\n";
                $mailbody .= "a PayPal transaction for you has been made on your website!\n";
                $mailbody .= "-----------------------------------------------------------\n";
                $mailbody .= "Transaction ID: $txn_id\n";
                $mailbody .= "Payer Email: $payer_email\n";
                $mailbody .= "Order ID: $order_id\n";
                $mailbody .= "Payment Status returned by PayPal: $payment_status\n";
                $mailbody .= "Order Status Code: ".$d['order_status'];
                vmMail($mosConfig_mailfrom, $mosConfig_fromname, $debug_email_address, $mailsubject, $mailbody );
            }
            else { 
                //----------------------------------------------------------------------
                // If the payment_status is not Completed... do nothing but mail
                //----------------------------------------------------------------------
                // UPDATE THE ORDER STATUS to 'INVALID'
                $d['order_status'] = PAYPAL_INVALID_STATUS;
                
                require_once ( CLASSPATH . 'ps_order.php' );
                $ps_order= new ps_order;
                $ps_order->order_status_update($d);
                
                $mailsubject = "PayPal IPN Transaction on your site";
                $mailbody = "Hello,
                a Failed PayPal Transaction on $mosConfig_live_site requires your attention.
                -----------------------------------------------------------
                Order ID: ".$d['order_id']."
                User ID: ".$db->f("user_id")."
                Payment Status returned by PayPal: $payment_status 
                
                $error_description";
                vmMail($mosConfig_mailfrom, $mosConfig_fromname, $debug_email_address, $mailsubject, $mailbody );
                
            }
        }
        //----------------------------------------------------------------
        // ..If UNVerified - It's 'Suspicious' and needs investigating!
        // Send an email to yourself so you investigate it.
        //----------------------------------------------------------------
        elseif (eregi ("INVALID", $res)) {
                $mailsubject = "Invalid PayPal IPN Transaction on your site";
                $mailbody = "Hello,\n\n";
                $mailbody .= "An Invalid PayPal Transaction requires your attention.\n";
                $mailbody .= "-----------------------------------------------------------\n";
                $mailbody .= "REMOTE IP ADDRESS: ".$_SERVER['REMOTE_ADDR']."\n";
                $mailbody .= "REMOTE HOST NAME: $remote_hostname\n";
                $mailbody .= "Order ID: ".$d['order_id']."\n";
                $mailbody .= "User ID: ".$db->f("user_id")."\n";
                $mailbody .= $error_description;
                vmMail($mosConfig_mailfrom, $mosConfig_fromname, $debug_email_address, $mailsubject, $mailbody );

        }
        else {
                $mailsubject = "PayPal IPN Transaction on your Site";
                $mailbody = "Hello,
                An error occured while processing a paypal transaction.
                ----------------------------------
                    $error_description";
                vmMail($mosConfig_mailfrom, $mosConfig_fromname, $debug_email_address, $mailsubject, $mailbody );
        }
    }
}
?>
