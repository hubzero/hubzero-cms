<?php
/**
* @version		
* @package		Joomdle
* @copyright	Copyright (C) 2008 - 2010 Antonio Duran Terres
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');

/*
Process: 
In VM we sell courses as downloads
When a order is confirmed in VM, the download is made avaiable.

Each time the Joomdle module list users courses, it checks first to see
if there are new enrolments to do before showing the list.
For that, we check all his orders and process the confirmed ones.
For each order, all its downloads are checked.
If there are downloads with donwload_max > 0, the user in enroled in the new course
Also, the download_max is set to 0 so the process is not re-done again

XXX NOT USED ANYMORE, DISCARD
*/

function update_user_enrols ()
{
	$user = & JFactory::getUser();
	$id = $user->get('id');
	$username = $user->get('username');


	$db           =& JFactory::getDBO();
	$query = 'SELECT order_id from #__vm_orders where user_id=';
	$query .= "'$id'".'';
	$db->setQuery($query);
	$orders = $db->loadObjectList();

	if ($db->getErrorNum()) {
		JError::raiseWarning( 500, $db->stderr() );
	}

	if (count($orders))
	foreach ($orders as $order)
	{
		$order_id = $order->order_id;
		$query = "SELECT order_status_code from #__vm_order_history where order_id = $order_id order by order_status_history_id DESC limit 1";
		$db->setQuery($query);
		$order_status = $db->loadObjectList();
		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}
		$os_code = $order_status[0]->order_status_code; //array es? xXX

		/* We only check confirmed orders */
		if ($os_code != 'C') // XXX deberia ser configurable
			continue;

		/* Order is confirmed, check if it has been processed */

		/* Check all downloads with this order_id to see it download_max > 0, 
		   which means we have to enrol the user
		   */
		$query = 'SELECT download_max,order_id,product_id' .
			' FROM #__vm_product_download' .
			' WHERE (download_max > 0) and (order_id =';
		$query .= "'$order_id')";

		$db->setQuery($query);
		$downloads = $db->loadObjectList();

		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}

		/* All courses in this order already processed */
		if (count ($downloads) == 0)
			continue;

		foreach ($downloads as $download)
		{
			$product_id = $download->product_id;
			$query = 'SELECT product_sku' .
				' FROM #__vm_product' .
				' WHERE product_id =';
			$query .= "'$product_id'";
			$db->setQuery($query);
			$products = $db->loadObjectList();
			if (count($products))
			foreach ($products as $product)
				$sku = $product->product_sku;

			if ($db->getErrorNum()) {
				JError::raiseWarning( 500, $db->stderr() );
			}
			JoomdleHelperContent::enrolUser ($username, $sku);
			//echo "You have been enrroled in the course";
			$query = "UPDATE #__vm_product_download set download_max=0 where product_id='$product_id'";
			$db->setQuery($query);
			if (!$db->query()) {
				return JError::raiseWarning( 500, $db->getError() );
			 }

		}

	}
}

?>
