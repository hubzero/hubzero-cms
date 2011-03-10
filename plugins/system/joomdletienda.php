<?php
/**
* @version		
* @package		Joomdle
* @copyright		Antonio Duran Terres
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class  plgSystemJoomdletienda extends JPlugin
{


	function plgSystemCache(& $subject, $config)
	{
		parent::__construct($subject, $config);

	}

	function doCompletedOrderTasks ($order_id)
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'shop.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'parents.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tienda'.DS.'helpers'.DS.'product.php');
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tienda'.DS.'tables' );
                JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tienda'.DS.'models' );

		/* Get Joomdle courses category */
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$courses_category = $params->get( 'courses_category' );
		$buy_for_children = $params->get( 'buy_for_children' );

                $productsModel = JModel::getInstance( 'Products', 'TiendaModel' );
		$model = JModel::getInstance( 'Orders', 'TiendaModel' );
		$model->setId( $order_id );
		$order = $model->getItem();
		if ($order->orderitems)
		{
			foreach ($order->orderitems as $orderitem)
			{
				//print_r ($orderitem);
				$cats = TiendaHelperProduct::getCategories ($orderitem->product_id);

				/* Only process products in the Joomdle courses category */
				if (in_array ($courses_category, $cats))
				{
					$user = & JFactory::getUser($orderitem->user_id);

					/* Update user info in Moodle with Tienda info */
					JoomdleHelperContent::call_method ("create_joomdle_user", $user->username);

					/* Enrol the user / update purchased courses */
					if ($buy_for_children)
					{
						JoomdleHelperParents::purchase_course ($user->username, $orderitem->product_sku, $orderitem->orderitem_quantity);
					}
					else 
					{
						JoomdleHelperContent::enrolUser ($user->username, (int) $orderitem->product_sku);
						/* Send confirmation email */
						JoomdleHelperShop::send_confirmation_email ($user->email, $orderitem->product_sku);
					}

				}
			}

		}
	}

}
