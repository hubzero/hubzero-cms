<?php
/**
 * @version		
 * @package		Joomdle
 * @subpackage	Content
 * @copyright	Copyright (C) 2008 - 2010 Antonio Duran Terres
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.user.helper');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'parents.php');

/**
 * Content Component Query Helper
 *
 * @static
 * @package		Joomdle
 * @since 1.5
 */
class JoomdleHelperShop
{

	function is_course_on_sell ($course_id)
	{
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		if (!$shop)
			return false;

		if ($shop == 'tienda')
			$on_sell = JoomdleHelperShop::is_course_on_sell_on_tienda ($course_id);
		else
			$on_sell = JoomdleHelperShop::is_course_on_sell_on_vm ($course_id);

		return $on_sell;
	}

	function getShopCourses ($course_id)
	{
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		if ($shop == 'tienda')
			$courses = JoomdleHelperShop::getTiendaCourses ();
		else
			$courses = JoomdleHelperShop::getVirtuemartCourses ();

		return $courses;
	}

	function get_sell_url ($course_id)
	{
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		if ($shop == 'tienda')
			$url = JoomdleHelperShop::get_tienda_sell_url ($course_id);
		else
			$url = JoomdleHelperShop::get_vm_sell_url ($course_id);

		return $url;
	}

	function sell_courses ($courses)
	{
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		if ($shop == 'tienda')
			JoomdleHelperShop::sell_courses_on_tienda ($courses);
		else
			JoomdleHelperShop::sell_courses_on_vm ($courses);
	}

	function dont_sell_courses ($courses)
	{
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		if ($shop == 'tienda')
			JoomdleHelperShop::dont_sell_courses_on_tienda ($courses);
		else
			JoomdleHelperShop::dont_sell_courses_on_vm ($courses);
	}

	function reload_courses ($courses)
	{
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		if ($shop == 'tienda')
			JoomdleHelperShop::reload_courses_to_tienda ($courses);
		else
			JoomdleHelperShop::reload_courses_to_vm ($courses);
	}

	function delete_courses ($courses)
	{
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$shop = $params->get( 'shop_integration' );

		if ($shop == 'tienda')
			JoomdleHelperShop::delete_courses_from_tienda ($courses);
		else
			JoomdleHelperShop::delete_courses_from_vm ($courses);
	}

	/* Tienda  related functions */
	function getTiendaCourses ()
        {
                $cursos = JoomdleHelperContent::getCourseList (0);

                $c = array ();
                $i = 0;
		if (!is_array ($cursos))
			return $c;

                foreach ($cursos as $curso)
                {
                        $c[$i]->id = $curso['remoteid'];
                        $c[$i]->fullname = $curso['fullname'];
			$c[$i]->published = JoomdleHelperShop::is_course_on_sell_on_tienda ($curso['remoteid']);
                        $i++;
                }

                return $c;
        }

	function is_course_on_sell_on_tienda ($course_id)
	{
		$db           =& JFactory::getDBO();
		$query = 'SELECT product_sku' .
                                ' FROM #__tienda_products' .
                                ' WHERE product_sku =';
		$query .= $db->Quote($course_id) . " and product_enabled='1'";
		$db->setQuery($query);
		$products = $db->loadObjectList();
		if (count ($products))
			return 1;
		else
			return 0;

	}

	function get_tienda_sell_url ($course_id)
	{
		$db           =& JFactory::getDBO();
		$query = 'SELECT product_id' .
                                ' FROM #__tienda_products' .
                                ' WHERE product_sku =';
		$query .= $db->Quote($course_id) . " and product_enabled='1'";
		$db->setQuery($query);
		$product = $db->loadObjectList();
		$product_id = $product[0]->product_id;
		$url = "index.php?option=com_tienda&view=products&task=view&id=$product_id";

		return $url;
	}

	function reload_courses_to_tienda ($courses)
	{
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tienda'.DS.'tables' );
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$courses_category = $params->get( 'courses_category' );
		$db           =& JFactory::getDBO();
		foreach ($courses as $sku)
		{
			$query = "SELECT product_id FROM #__tienda_products WHERE product_sku = ". $db->Quote($sku);
			$db->setQuery($query);
			$products = $db->loadObjectList();
			if (count ($products))
			{
				//XXX quizas mejor intentarlo con el model, por el tema precios...
				$product_id = $products[0]->product_id;

				$course_info = JoomdleHelperContent::getCourseInfo ($sku);
				$name = $course_info['fullname'];
				$desc = $course_info['summary'];
				$cost = $course_info['cost'];
				$currency = $course_info['currency'];

				$product = JTable::getInstance('Products', 'TiendaTable');
				$product->load ($product_id);
				$product->product_name = $name;
				$product->product_description = $desc;
				$product->product_description_short = $desc;
				$product->product_sku = $sku;
				$product->product_enabled = 1;
				$product->product_check_inventory = 0; // XXX Esto no va en joomdle.info... differente version?
				$product->product_ships = 0;


				$product->save();

				/* Set price */
				$price = JTable::getInstance('ProductPrices', 'TiendaTable');
				$price->load (array ('product_id' => $product_id));

				$price->product_id = $product->product_id;
				$price->product_price = $cost;

				$price->save();

				/* Set category */
				$category = JTable::getInstance( 'Productcategories', 'TiendaTable' );
				$category->product_id = $product->id;
				$category->category_id = $courses_category;
				if (!$category->save())
				{
					$this->messagetype      = 'notice';
					$this->message .= " :: ".$category->getError();
				}
			}
			else JoomdleHelperShop::sell_courses_on_tienda (array($sku));
		}
	}

	function delete_courses_from_tienda ($courses)
	{
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tienda'.DS.'tables' );
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$courses_category = $params->get( 'courses_category' );

		$db           =& JFactory::getDBO();

		foreach ($courses as $sku)
		{
			$query = 'SELECT product_id' .
					' FROM #__tienda_products' .
					' WHERE product_sku =';
			$query .= $db->Quote($sku);
			$db->setQuery($query);
			$products = $db->loadObjectList();
			/* Product not on Tienda, nothing to do */
			if (!count ($products))
				continue;

			$query = "DELETE FROM  #__tienda_products  where product_sku = " . $db->Quote($sku);
			$db->setQuery($query);
			if (!$db->query()) {
				return JError::raiseWarning( 500, $db->getError() );
			 }
		}
	}

	function sell_courses_on_tienda ($courses)
	{
		require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tienda'.DS.'defines.php' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tienda'.DS.'tables' );
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$courses_category = $params->get( 'courses_category' );

		$db           =& JFactory::getDBO();

		foreach ($courses as $sku)
		{
			$query = 'SELECT product_sku' .
					' FROM #__tienda_products' .
					' WHERE product_sku =';
			$query .= $db->Quote($sku);
			$db->setQuery($query);
			$products = $db->loadObjectList();
			if (count ($products))
			{
				/* Product already on Tienda, just publish it */
				$query = "UPDATE  #__tienda_products SET product_enabled = '1' where product_sku = ". $db->Quote($sku);
				$db->setQuery($query);
				if (!$db->query()) {
					return JError::raiseWarning( 500, $db->getError() );
				 }
				continue;
			}

			/* New product to add to Tienda */
			$course_info = JoomdleHelperContent::getCourseInfo ($sku);
			$name = $course_info['fullname'];
			$desc = $course_info['summary'];
			$cost = $course_info['cost'];
			$currency = $course_info['currency'];

			$product = JTable::getInstance('Products', 'TiendaTable');
			$product->product_name = $name;
			$product->product_description = $desc;
			$product->product_description_short = $desc;
			$product->product_sku = $sku;
			$product->product_enabled = 1;
			$product->product_check_inventory = 0;
			$product->product_ships = 0;

			$product->save();

			/* Set price */
			$price = JTable::getInstance('ProductPrices', 'TiendaTable');
			$price->product_id = $product->product_id;
			$price->product_price = $cost;

			$price->save();

			/* Set category */
			$category = JTable::getInstance( 'Productcategories', 'TiendaTable' );
			$category->product_id = $product->id;
			$category->category_id = $courses_category;
			if (!$category->save())
			{
				$this->messagetype      = 'notice';
				$this->message .= " :: ".$category->getError();
			}
		}
	}

	function dont_sell_courses_on_tienda ($courses)
	{
		$db           =& JFactory::getDBO();

		foreach ($courses as $sku)
		{
			$query = "UPDATE  #__tienda_products SET product_enabled = '0' where product_sku = " . $db->Quote($sku);
			$db->setQuery($query);
			if (!$db->query()) {
				return JError::raiseWarning( 500, $db->getError() );
			 }
		}
	}

	/* Virtuemart related functions */

	function getVirtuemartCourses ()
        {
                $cursos = JoomdleHelperContent::getCourseList (0);

                $c = array ();
                $i = 0;
		if (!is_array ($cursos))
			return $c;

                foreach ($cursos as $curso)
                {
                        $c[$i]->id = $curso['remoteid'];
                        $c[$i]->fullname = $curso['fullname'];
			$c[$i]->published = JoomdleHelperShop::is_course_on_sell ($curso['remoteid']);
                        $i++;
                }

                return $c;
        }

	function is_course_on_sell_on_vm ($course_id)
	{
		$db           =& JFactory::getDBO();
		$query = 'SELECT product_sku' .
                                ' FROM #__vm_product' .
                                ' WHERE product_sku =';
		$query .= $db->Quote($course_id) . " and product_publish='Y'";
		$db->setQuery($query);
		$products = $db->loadObjectList();
		if (count ($products))
			return 1;
		else
			return 0;

	}

	function get_vm_sell_url ($course_id)
	{
		$db           =& JFactory::getDBO();
		$query = 'SELECT product_id' .
                                ' FROM #__vm_product' .
                                ' WHERE product_sku =';
		$query .= $db->Quote($course_id) . " and product_publish='Y'";
		$db->setQuery($query);
		$product = $db->loadObjectList();
		$product_id = $product[0]->product_id;
		$url = "index.php?page=shop.product_details&flypage=flypage.tpl&product_id=$product_id&option=com_virtuemart";

		return $url;
	}

	/* Reload data from Moodle */
	function reload_courses_to_vm ($courses)
	{
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$courses_category = $params->get( 'courses_category' );
		$db           =& JFactory::getDBO();
		foreach ($courses as $sku)
		{
			$query = "SELECT product_id FROM #__vm_product WHERE product_sku = " . $db->Quote($sku);
			$db->setQuery($query);
			$products = $db->loadObjectList();
			if (count ($products))
			{
				$product_id = $products[0]->product_id;
				$course_info = JoomdleHelperContent::getCourseInfo ($sku);
				$name = $db->getEscaped($course_info['fullname']);
				$desc = $db->getEscaped($course_info['summary']);
				$price = $db->getEscaped($course_info['cost']);
				$currency = $db->getEscaped($course_info['currency']);

				$query = "UPDATE  #__vm_product SET product_publish = 'Y', product_name = '$name', product_desc = '$desc', product_s_desc = '$desc'  where product_sku = '$sku'";
				$db->setQuery($query);
				if (!$db->query()) {
					return JError::raiseWarning( 500, $db->getError() );
				 }
				/* Price */
				$query = "UPDATE  #__vm_product_price SET product_price='$price', product_currency = '$currency' where product_id = '$product_id'";
				$db->setQuery($query);
				if (!$db->query()) {
					return JError::raiseWarning( 500, $db->getError() );
				 }
			}
			else JoomdleHelperShop::sell_courses_on_vm (array($sku));
		}
	}

	function sell_courses_on_vm ($courses)
	{
		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$courses_category = $params->get( 'courses_category' );
		$db           =& JFactory::getDBO();
		foreach ($courses as $sku)
		{
			/* If course already exists, only publish it */
			$query = "SELECT product_id FROM #__vm_product WHERE product_sku = '$sku'";
			$db->setQuery($query);
			$products = $db->loadObjectList();
			if (count ($products))
			{
				$query = "UPDATE  #__vm_product SET product_publish = 'Y' where product_sku = '$sku'";
				$db->setQuery($query);
				if (!$db->query()) {
					return JError::raiseWarning( 500, $db->getError() );
				 }
				continue;
			}
			
			/* New course to insert in VM */
			
			$course_info = JoomdleHelperContent::getCourseInfo ($sku);
			$name = $db->getEscaped($course_info['fullname']);
			$desc = $db->getEscaped($course_info['summary']);
			$price = $db->getEscaped($course_info['cost']);
			$currency = $db->getEscaped($course_info['currency']);
			/* Add new product to Virtuemart */
			$query = "INSERT into #__vm_product (vendor_id, product_parent_id, product_sku, product_name, product_s_desc, product_desc, product_publish, child_options, quantity_options)
				  VALUES ('1', '0', '$sku', '$name', '$desc', '$desc', 'Y', 'N,N,N,N,N,N,20%,10%,', 'hide,0,0,1');";
			$db->setQuery($query);
                        if (!$db->query()) {
                                return JError::raiseWarning( 500, $db->getError() );
                         }

			/* Get product id */
			$product_id = $db->insertid();

			/* Get product type ID for Courses */
			$query = "SELECT product_type_id from #__vm_product_type where product_type_name='Course'";
			$db->setQuery($query);
			$items_aux = $db->loadObjectList();

			if ($db->getErrorNum()) {
				JError::raiseWarning( 500, $db->stderr() );
			}

			if (count ($items_aux) == 0)
			{
				/* Insert into product types if not done yet . We cannot do it  at install because VM may not be installed */
				$query = "INSERT into #__vm_product_type (product_type_name, product_type_description, product_type_publish) VALUES  ('Course', 'Joomdle Course', 'N')";
				$db->setQuery($query);
				if (!$db->query()) {
					return JError::raiseWarning( 500, $db->getError() );
				 }
				/* Get product type ID for Courses  once is inserted */
				$query = "SELECT product_type_id from #__vm_product_type where product_type_name='Course'";
				$db->setQuery($query);
				$items_aux = $db->loadObjectList();

				if ($db->getErrorNum()) {
					JError::raiseWarning( 500, $db->stderr() );
				}
			}
			$type_id = $items_aux[0]->product_type_id;

			/* Create table if it no exists yet. We cannot create at install time due to variable name :( */
			$query = "CREATE TABLE IF NOT EXISTS #__vm_product_type_$type_id (product_id int primary key);";
			$db->setQuery($query);

			if (!$db->query()) {
				return JError::raiseWarning( 500, $db->getError() );
			 }
			/* Insert into product_type_$type_id table */
			$query = "INSERT INTO #__vm_product_type_$type_id VALUES ('$product_id')";
			$db->setQuery($query);
                        if (!$db->query()) {
                                return JError::raiseWarning( 500, $db->getError() );
                         }
			/* Insert into product_type_xref table */
			$query = "INSERT INTO #__vm_product_product_type_xref  (product_id, product_type_id) VALUES ('$product_id', '$type_id')";
			$db->setQuery($query);
                        if (!$db->query()) {
                                return JError::raiseWarning( 500, $db->getError() );
                         }

			/* Add to category */
			//XXX configurar que categoria se ponen a los cursos por defecto, luego se podra cambiar en VM... XXX Do correspndoncia
			// category_id es el primer parametro del values : configurar en la pantalla del virtuemart
			$query = "INSERT into #__vm_product_category_xref 
				VALUES ('$courses_category', '$product_id', 1);";
			$db->setQuery($query);
                        if (!$db->query()) {
                                return JError::raiseWarning( 500, $db->getError() );
                         }
			// XXX falta meterlo en la tabla de manufactures
			//   INSERT INTO jos_vm_product_mf_xref VALUES ('7', '1')
			/* Add price */
			$query = "INSERT into #__vm_product_price (product_id, shopper_group_id, product_price, product_currency) 
				VALUES ('$product_id', 5, '$price', '$currency');";
			$db->setQuery($query);
                        if (!$db->query()) {
                                return JError::raiseWarning( 500, $db->getError() );
                         }

			/* Add download for new product */
			$query = "INSERT into #__vm_product_attribute (product_id, attribute_name, attribute_value) 
				VALUES ('$product_id', 'download', 'file.html');";
			$db->setQuery($query);
                        if (!$db->query()) {
                                return JError::raiseWarning( 500, $db->getError() );
                         }

			/* Add file */
			$filename = JPATH_COMPONENT.DS.'views'.DS.'virtuemart'.DS.'downloads'.DS.'file.html';
			$query  = "INSERT into #__vm_product_files (file_product_id, file_name, file_published)
				VALUES ('$product_id', '$filename', 0);";
			$db->setQuery($query);
                        if (!$db->query()) {
                                return JError::raiseWarning( 500, $db->getError() );
                         }
		}
	}

	function delete_courses_from_vm ($courses)
	{
		$db           =& JFactory::getDBO();
		foreach ($courses as $sku)
		{
			$course_info = JoomdleHelperContent::getCourseInfo ($sku);
			$name = $course_info['fullname'];
			$price = $course_info['cost'];
			$currency = $course_info['currency'];

			/* Get product id */
			$query = "SELECT product_id FROM #__vm_product WHERE product_sku = '$sku'";
			$db->setQuery($query);
			$products = $db->loadObjectList();
			$product_id = $products[0]->product_id;

			$query = "DELETE from #__vm_product where product_sku = '$sku'";
			$db->setQuery($query);
                        if (!$db->query()) {
                                return JError::raiseWarning( 500, $db->getError() );
                         }

			$query = "DELETE from #__vm_product_category_xref
				WHERE product_id = '$product_id'";
			$db->setQuery($query);
                        if (!$db->query()) {
                                return JError::raiseWarning( 500, $db->getError() );
                         }
			$query = "DELETE from #__vm_product_price
				WHERE product_id = '$product_id'";
			$db->setQuery($query);
                        if (!$db->query()) {
                                return JError::raiseWarning( 500, $db->getError() );
                         }

			$query = "DELETE from #__vm_product_attribute
				WHERE product_id = '$product_id'";
			$db->setQuery($query);
                        if (!$db->query()) {
                                return JError::raiseWarning( 500, $db->getError() );
                         }

			$query = "DELETE from #__vm_product_files
				WHERE file_product_id = '$product_id';";
			$db->setQuery($query);
                        if (!$db->query()) {
                                return JError::raiseWarning( 500, $db->getError() );
                         }
		}
	}

	/* Unpublish courses in VM */
	function dont_sell_courses_on_vm ($courses)
	{
		$db           =& JFactory::getDBO();
		foreach ($courses as $sku)
		{
			$query = "UPDATE  #__vm_product SET product_publish = 'N' where product_sku = '$sku'";
			$db->setQuery($query);
                        if (!$db->query()) {
                                return JError::raiseWarning( 500, $db->getError() );
                         }
		}
	}

	function add_order_enrols ($order_id, $user_id)
	{
		$db           =& JFactory::getDBO();

		$user =& JFactory::getUser($user_id);
		$username=  $user->username;
		$email =  $user->email;

		/* Update user profile in Moodle  with VM data, if necessary */
		JoomdleHelperContent::call_method ('create_joomdle_user', $username);

		/* Get product type ID for Courses */
		$query = "SELECT product_type_id from #__vm_product_type where product_type_name='Course'";
		$db->setQuery($query);
		$items_aux = $db->loadObjectList();

		if ($db->getErrorNum()) {
			JError::raiseWarning( 500, $db->stderr() );
		}
		$type_id = $items_aux[0]->product_type_id;

		$order_id = $db->Quote ($order_id);
                $query = 'SELECT *' .
                        ' FROM #__vm_order_item' .
                        ' WHERE order_id =';
                $query .= "$order_id";

                $db->setQuery($query);
                $items = $db->loadObjectList();

                if ($db->getErrorNum()) {
                        JError::raiseWarning( 500, $db->stderr() );
                }

                /* No items in this order */
                if (count ($items) == 0)
                        return;

		$params = &JComponentHelper::getParams( 'com_joomdle' );
		$buy_for_children = $params->get( 'buy_for_children' );


                foreach ($items as $item)
                {
			/* Only process product of type Course */
			$product_id = $item->product_id;
			$order_item_id = $item->order_item_id;
			$query = "SELECT product_id from #__vm_product_type_$type_id where product_id='$product_id'";
			$db->setQuery($query);
			$p_ids = $db->loadObjectList();

			if ($db->getErrorNum()) {
				JError::raiseWarning( 500, $db->stderr() );
			}
			/* If it is a course */
			if (count ($p_ids))
			{
				$sku = $item->order_item_sku;
				if ($buy_for_children)
					JoomdleHelperParents::purchase_course ($username, $sku, $item->product_quantity);
				else
				{
					JoomdleHelperContent::enrolUser ($username, $sku);
					/* Send confirmation email */
					JoomdleHelperShop::send_confirmation_email ($email, $sku);
				}

				/* Update item status */
				$query = "update #__vm_order_item set order_status='C' where order_item_id='$order_item_id'";
				$db->setQuery($query);
				if (!$db->query()) {

					return JError::raiseWarning( 500, $db->getError() );
				 }
			}
                }

		$timestamp = time ();
		$mysqlDatetime = date("Y-m-d G:i:s", $timestamp + ($mosConfig_offset*60*60));  //Custom

                /* Mark order as Procesed (Enroled) */ ///XXX Quizas mejor dejarlo en C, menos lio
                //$query = "UPDATE #__vm_orders set order_status = 'E', mdate = '$timestamp' where order_id='$order_id'";  ///XXX Cambiado a C
                $query = "UPDATE #__vm_orders set order_status = 'C', mdate = '$timestamp' where order_id=$order_id"; 
                $db->setQuery($query);
                if (!$db->query()) {
                        return JError::raiseWarning( 500, $db->getError() );
                 }

		/* Update order status history */
                $query = "INSERT INTO #__vm_order_history (order_id, order_status_code, date_added) VALUES ('$order_id', 'C', '$mysqlDatetime')";
                $db->setQuery($query);
                if (!$db->query()) {
                        return JError::raiseWarning( 500, $db->getError() );
                 }

		/* Update items status */
 /*               $query = "update #__vm_order_item set order_status='C' where order_id='$order_id'";
                $db->setQuery($query);
                if (!$db->query()) {
                        return JError::raiseWarning( 500, $db->getError() );
                 }
*/
		/* XXX Update sales and stock level XXX*/
              /*  $query = "update #__vm_product set order_status='C' where order_id='$order_id'";
                $db->setQuery($query);
                if (!$db->query()) {
                        return JError::raiseWarning( 500, $db->getError() );
                 } */
	}

	/* General functions */
	function send_confirmation_email ($email, $course_id)
	{
		global $mainframe;

		$config =& JFactory::getConfig();
		$comp_params = &JComponentHelper::getParams( 'com_joomdle' );
		$linkstarget = $comp_params->get( 'linkstarget' );
		$moodle_url = $comp_params->get( 'MOODLE_URL' );
		$email_subject = $comp_params->get( 'enrol_email_subject' );
		$email_text = $comp_params->get( 'enrol_email_text' );


		if ($linkstarget == 'wrapper')
		{
			/* XXX After and hour tryng and searching I could not find the GOOD way
			   to do this, so I do this kludge and it seems to work ;) 
			   */
			$url            = JURI::base();
			$pos =  strpos ($url, '/administrator/');
			if ($pos)
				$url = substr ($url, 0, $pos);
			$url            = $url.'/index.php?option=com_joomdle&view=wrapper&moodle_page_type=course&id='.$course_id;
		} else {
			$url = $moodle_url.'/course/view.php?id='.$course_id;
		}

		$course_info = JoomdleHelperContent::getCourseInfo ((int) $course_id);
		$name = $course_info['fullname'];

		$email_text = str_replace ('COURSE_NAME', $name, $email_text);
		$email_text = str_replace ('COURSE_URL', $url, $email_text);
		$email_subject = str_replace ('COURSE_NAME', $name, $email_subject);
		$email_subject = str_replace ('COURSE_URL', $url, $email_subject);

	//	$lang = JFactory::getLanguage();
	//	$lang->load('com_joomdle');

		// And then use this instead of JText::_()
                // Set the e-mail parameters
                $from           = $config->getValue('mailfrom');
                $fromname       = $config->getValue('fromname');
 //               $subject           = JText::sprintf(JText::_('CJ_ENROL_MESSAGE_SUBJECT'), $name);
   //             $body           = JText::sprintf(JText::_('CJ_ENROL_MESSAGE_BODY'), $name, $url);
     //           $body           = JText::sprintf($lang->_('CJ_ENROL_MESSAGE_BODY'), $name, $url);

                // Send the e-mail
                if (!JUtility::sendMail($from, $fromname, $email, $email_subject, $email_text))
                {
                        $this->setError('ERROR_SENDING_CONFIRMATION_EMAIL');
                        return false;
                }

                return true;
	}
}
