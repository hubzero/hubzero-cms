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

/**
 * Content Component Query Helper
 *
 * @static
 * @package		Joomdle
 * @since 1.5
 */
class JoomdleHelperMappings
{

	function get_app_mappings ($app)
	{
                $db           =& JFactory::getDBO();
		$query = 'SELECT *' .
			' FROM #__joomdle_field_mappings' .
			" WHERE joomla_app = " . $db->Quote($app);
                $db->setQuery($query);
                $mappings = $db->loadObjectList();

		if (!$mappings)
			return NULL;

		return $mappings;
	}

	function getMappings ($filter_type, $limitstart, $limit, $filter_order, $filter_order_Dir, $search)
	{
                $db           =& JFactory::getDBO();

		$wheres = array ();
		if ($filter_type)
			$wheres[] = "joomla_app = ". $db->Quote($filter_type);

		if ($search)
		{
			$wheres_search[] = "joomla_field = ". $db->Quote($search);
			$wheres_search[] = "moodle_field = ". $db->Quote($search);
			$wheres[] = "(joomla_field LIKE  ". $search ." OR moodle_field LIKE ".$search.")";
		}

		$query = 'SELECT *' .
			' FROM #__joomdle_field_mappings';

		if(! empty($wheres)){
                   $query .= " WHERE ".implode(' AND ', $wheres);
                }

		$query .= " ORDER BY ".  $filter_order  ." ". $filter_order_Dir;

		if(! empty($limit)){
                   $query .= " LIMIT $limitstart, $limit";
                }

		$db->setQuery($query);
                $mappings = $db->loadAssocList();


		if (!$mappings)
			return NULL;

		foreach ($mappings as $mapping)
		{
			$mapping['joomla_field'] =  $mapping['joomla_field'];
			$mapping['joomla_field_name'] = JoomdleHelperMappings::get_field_name ( $mapping['joomla_app'], $mapping['joomla_field'] );
			$m[] = $mapping;
		}

		return $m;
	}

	function getMapping ($id)
	{
                $db           =& JFactory::getDBO();
		$query = 'SELECT *' .
			' FROM #__joomdle_field_mappings' .
                              " WHERE id = " . $db->Quote($id);
                $db->setQuery($query);
                $mapping = $db->loadObject();

		return $mapping;
	}

	function delete_mappings ($cid)
	{
                $db           =& JFactory::getDBO();
		foreach ($cid as $id)
		{
			$query = 'DELETE ' .
				' FROM #__joomdle_field_mappings' .
				      " WHERE id = " . $db->Quote($id);
			$db->setQuery($query);
			$db->query();
		}
	}

	function get_user_info ($username)
	{
		$comp_params = &JComponentHelper::getParams( 'com_joomdle' );
		$app = $comp_params->get( 'additional_data_source' );

		$id = JUserHelper::getUserId($username);
                $user =& JFactory::getUser($id);


                $user_info['email'] = $user->email;

                /* Language */
                $user_info['lang']   = JoomdleHelperMappings::get_moodle_lang ($user->getParam( 'language' ));

                /* Timezone */
                $user_info['timezone']   = $user->getParam( 'timezone' );

		switch ($app)
		{
			case 'jomsocial':
				$more_info = JoomdleHelperMappings::get_user_info_jomsocial ($username);
				break;
			case 'virtuemart':
				$more_info = JoomdleHelperMappings::get_user_info_virtuemart ($username);
				break;
			case 'tienda':
				$more_info = JoomdleHelperMappings::get_user_info_tienda ($username);
				break;
			case 'cb':
				$more_info = JoomdleHelperMappings::get_user_info_cb ($username);
				break;
			default:
				$more_info = JoomdleHelperMappings::get_user_info_joomla ($username);
				break;
		}

		return array_merge ($user_info, $more_info);

		return $more_info;
	}

	function get_field_name ($app, $field)
	{
		switch ($app)
		{
			case 'jomsocial':
				$name = JoomdleHelperMappings::get_field_name_jomsocial ($field);
				break;
			case 'virtuemart':
				$name = JoomdleHelperMappings::get_field_name_virtuemart ($field);
				break;
			case 'tienda':
				$name = JoomdleHelperMappings::get_field_name_tienda ($field);
				break;
			case 'cb':
				$name = JoomdleHelperMappings::get_field_name_cb ($field);
				break;
			default:
				$name = 'UNKNOWN APP';
				break;
		}

		return $name;
	}

	function get_fields ($app)
	{
		switch ($app)
		{
			case 'jomsocial':
				$fields = JoomdleHelperMappings::get_fields_jomsocial ();
				break;
			case 'virtuemart':
				$fields = JoomdleHelperMappings::get_fields_virtuemart ();
				break;
			case 'tienda':
				$fields = JoomdleHelperMappings::get_fields_tienda ();
				break;
			case 'cb':
				$fields = JoomdleHelperMappings::get_fields_cb ();
				break;
			default:
				$fields = 'UNKNOWN APP';
				break;
		}

		return $fields;
	}


	function get_user_info_joomla ($username)
	{

		$id = JUserHelper::getUserId($username);
		$user =& JFactory::getUser($id);

		$user_info['firstname'] = JoomdleHelperMappings::get_firstname ($user->name);
		$user_info['lastname'] = JoomdleHelperMappings::get_lastname ($user->name);

		return $user_info;
	}

	/* Jomsocial fns */

	function get_user_info_jomsocial ($username)
	{
		$db = &JFactory::getDBO();

		$id = JUserHelper::getUserId($username);
		$user =& JFactory::getUser($id);

		$user_info['firstname'] = JoomdleHelperMappings::get_firstname ($user->name);
		$user_info['lastname'] = JoomdleHelperMappings::get_lastname ($user->name);

		/* User pic */
		$query = 'SELECT avatar' .
				' FROM #__community_users' .
				" WHERE userid = '$id'";
		$db->setQuery( $query );
		$user_row = $db->loadAssoc();
		$user_info['pic_url'] =  $user_row['avatar'];

		$mappings = JoomdleHelperMappings::get_app_mappings ('jomsocial');


		foreach ($mappings as $mapping)
		{
			$user_info[$mapping->moodle_field] = JoomdleHelperMappings::get_field_value_jomsocial ($mapping->joomla_field, $user->id);
		}

		return $user_info;

	}

	function get_field_name_jomsocial ($field)
	{
                $db           =& JFactory::getDBO();
		$query = 'SELECT name ' .
			' FROM #__community_fields' .
                              " WHERE id = " . $db->Quote($field);
                $db->setQuery($query);
                $field = $db->loadObject();

		return $field->name;
	}

	function get_field_type_jomsocial ($field)
	{
                $db           =& JFactory::getDBO();
		$query = 'SELECT type ' .
			' FROM #__community_fields' .
                              " WHERE id = " . $db->Quote($field);
                $db->setQuery($query);
                $field = $db->loadObject();

		return $field->type;
	}

	function get_field_value_jomsocial ($field, $user_id)
	{
                $db           =& JFactory::getDBO();
		$query = 'SELECT value ' .
			' FROM #__community_fields_values' .
                              " WHERE field_id = " . $db->Quote($field) . " AND user_id = " . $db->Quote($user_id);
                $db->setQuery($query);
                $field_obj = $db->loadObject();
		
		if (!$field_obj)
			return "";

		/* Check if data needs transformation */
		$type = JoomdleHelperMappings::get_field_type_jomsocial ($field);
		switch ($type)
		{
			case 'country':
				$field_obj->value = JoomdleHelperMappings::get_moodle_country ($field_obj->value);
				break;
			default:
				break;
		}

		return $field_obj->value;
	}

	function get_fields_jomsocial ()
	{
                $db           =& JFactory::getDBO();
		$query = 'SELECT id, name ' .
			' FROM #__community_fields';
                $db->setQuery($query);
                $fields = $db->loadObjectList();

		return $fields;
	}


	/* Virtuemart fns */

	function get_user_info_virtuemart ($username)
	{
		$db = &JFactory::getDBO();

		$id = JUserHelper::getUserId($username);
		$user =& JFactory::getUser($id);

		$mappings = JoomdleHelperMappings::get_app_mappings ('virtuemart');


		foreach ($mappings as $mapping)
		{
			$user_info[$mapping->moodle_field] = JoomdleHelperMappings::get_field_value_virtuemart ($mapping->joomla_field, $user->id);
		}

		if ((!array_key_exists ('firstname', $user_info)) || ($user_info['firstname'] == ''))
			return JoomdleHelperMappings::get_user_info_joomla ($username);

		return $user_info;

	}

	function get_field_name_virtuemart ($field)
	{
		return $field;
	}

	function get_field_value_virtuemart ($field, $user_id)
	{
                $db           =& JFactory::getDBO();
		$query = "SELECT $field " .
			' FROM #__vm_user_info' .
                              " WHERE  user_id = " . $db->Quote($user_id);
                $db->setQuery($query);
                $field_object = $db->loadObject();
		
		if (!$field_object)
			return "";

		return $field_object->$field;
	}

	function get_fields_virtuemart ()
	{
		$fields = array ();

		$field = new JObject ();
		$field->name = 'first_name';
		$field->id = 'first_name';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'last_name';
		$field->id = 'last_name';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'last_name';
		$field->id = 'last_name';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'middle_name';
		$field->id = 'middle_name';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'company';
		$field->id = 'company';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'title';
		$field->id = 'title';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'phone_1';
		$field->id = 'phone_1';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'phone_2';
		$field->id = 'phone_2';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'fax';
		$field->id = 'fax';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'address_1';
		$field->id = 'address_1';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'address_2';
		$field->id = 'address_2';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'city';
		$field->id = 'city';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'state';
		$field->id = 'state';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'country';
		$field->id = 'country';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'zip';
		$field->id = 'zip';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'user_email';
		$field->id = 'user_email';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'middle_name';
		$field->id = 'middle_name';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'company';
		$field->id = 'company';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'title';
		$field->id = 'title';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'phone_1';
		$field->id = 'phone_1';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'phone_2';
		$field->id = 'phone_2';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'fax';
		$field->id = 'fax';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'address_1';
		$field->id = 'address_1';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'address_2';
		$field->id = 'address_2';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'city';
		$field->id = 'city';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'zip';
		$field->id = 'zip';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'country'; //XXX special cases
		$field->id = 'country';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'state';
		$field->id = 'state';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'user_email';
		$field->id = 'user_email';
		$fields[] = $field;


		//XXX meter el resto de user_info


		//XXX Echar un ojo a los campos personalizaos


		return $fields;
	}

	/* Tienda fns */

	function get_user_info_tienda ($username)
	{
		$db = &JFactory::getDBO();

		$id = JUserHelper::getUserId($username);
		$user =& JFactory::getUser($id);

		$mappings = JoomdleHelperMappings::get_app_mappings ('tienda');

		foreach ($mappings as $mapping)
		{
			$user_info[$mapping->moodle_field] = JoomdleHelperMappings::get_field_value_tienda ($mapping->joomla_field, $user->id);
		}

		if ((!array_key_exists ('firstname', $user_info)) || ($user_info['firstname'] == ''))
			return JoomdleHelperMappings::get_user_info_joomla ($username);

		return $user_info;

	}

	function get_field_name_tienda ($field)
	{
		return $field;
	}

	function get_field_value_tienda ($field, $user_id)
	{
                $db           =& JFactory::getDBO();
		$query = "SELECT $field " .
			' FROM #__tienda_addresses' .
                              " WHERE  user_id = " . $db->Quote($user_id);
                $db->setQuery($query);
                $field_obj = $db->loadObject();
		
		if (!$field_obj)
			return "";

		/* Check if data needs transformation */
		switch ($field)
		{
			case 'country_id':
				$field_obj->$field = JoomdleHelperMappings::get_tienda_country ($field_obj->$field);
				break;
			default:
				break;
		}

		return $field_obj->$field;
	}

	function get_fields_tienda ()
	{
		$fields = array ();

		$field = new JObject ();
		$field->name = 'first_name';
		$field->id = 'first_name';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'last_name';
		$field->id = 'last_name';
		$fields[] = $field;


		$field = new JObject ();
		$field->name = 'middle_name';
		$field->id = 'middle_name';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'company';
		$field->id = 'company';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'title';
		$field->id = 'title';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'phone_1';
		$field->id = 'phone_1';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'phone_2';
		$field->id = 'phone_2';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'fax';
		$field->id = 'fax';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'address_1';
		$field->id = 'address_1';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'address_2';
		$field->id = 'address_2';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'city';
		$field->id = 'city';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'postal_code';
		$field->id = 'postal_code';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'country_id'; //XXX special cases
		$field->id = 'country_id';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'zone_id';
		$field->id = 'zone_id';
		$fields[] = $field;

		$field = new JObject ();
		$field->name = 'user_email';
		$field->id = 'user_email';
		$fields[] = $field;


		//XXX meter el resto de user_info


		//XXX Echar un ojo a los campos personalizaos


		return $fields;
	}

	function get_tienda_country ($country_id)
        {
                $db = &JFactory::getDBO();
                $query = 'SELECT *' .
                                ' FROM #__tienda_countries' .
                                " WHERE country_id = " . $db->Quote($country_id);
                $db->setQuery( $query );
                $country = $db->loadAssoc();

                return $country['country_isocode_2'];
        }


	/* Community Builder fns */

	function get_user_info_cb ($username)
	{
		$db = &JFactory::getDBO();

		$id = JUserHelper::getUserId($username);
		$user =& JFactory::getUser($id);

		$user_info['firstname'] = JoomdleHelperMappings::get_firstname ($user->name); //XXX remove
		$user_info['lastname'] = JoomdleHelperMappings::get_lastname ($user->name);

		$mappings = JoomdleHelperMappings::get_app_mappings ('cb');


		foreach ($mappings as $mapping)
		{
			$user_info[$mapping->moodle_field] = JoomdleHelperMappings::get_field_value_cb ($mapping->joomla_field, $user->id);
		}

		if ((!array_key_exists ('firstname', $user_info)) || ($user_info['firstname'] == ''))
			return JoomdleHelperMappings::get_user_info_joomla ($username);

		return $user_info;

	}

	function get_field_name_cb ($field)
	{
		return $field;
	}

	function get_field_value_cb ($field, $user_id)
	{
                $db           =& JFactory::getDBO();
		$query = "SELECT $field " .
			' FROM #__comprofiler' .
                              " WHERE  user_id = " . $db->Quote($user_id);
                $db->setQuery($query);
                $field_object = $db->loadObject();
		
		if (!$field_object)
			return "";

		return $field_object->$field;
	}

	function get_fields_cb ()
	{
		$fields = array ();

                $db           =& JFactory::getDBO();
		$query = "DESC ".
			' #__comprofiler' ;

                $db->setQuery($query);
                $field_objects = $db->loadObjectList();

		$fields = array ();
		$i = 0;
		foreach ($field_objects as $fo)
		{
			$fields[$i]->name =  $fo->Field;
			$fields[$i]->id =  $fo->Field;
			$i++;
		}


		return $fields;


		//XXX special cases

		return $fields;
	}

	/* General helper fns */

	function get_firstname ($name)
        {
                $parts = explode (' ', $name);

                return  $parts[0];
        }

        function get_lastname ($name)
        {
                $parts = explode (' ', $name);

                $lastname = '';
                $n = count ($parts);
                for ($i = 1; $i < $n; $i++)
                {
                        if ($i != 1)
                                $lastname .= ' ';
                        $lastname .= $parts[$i];
                }

                return $lastname;
        }

	function get_moodle_country ($country)
        {
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'countries.php');
                if ($country == 'selectcountry')
                        return '';
                return $countries[$country];
        }

	function get_joomla_country ($country)
        {
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'countries_joomla.php');
                if ($country == 'selectcountry')
                        return '';
                return $countries[$country];
        }

	function get_moodle_lang ($lang)
        {
                if (!$lang)
                        return '';

			$comp_params = &JComponentHelper::getParams( 'com_joomdle' );
			$moodle_version = $comp_params->get( 'moodle_version' );
			if ($moodle_version == 20)
			{
				return substr ($lang, 0, 2);
			}
			else
			{
                switch ($lang)
                {
                        case 'en-GB':
                                return 'en_utf8';
                        case 'es-ES':
                                return 'es_utf8';
                        default:
                                return '';
                }
			}
        }


	function save_user_info ($user_info)
	{
		$comp_params = &JComponentHelper::getParams( 'com_joomdle' );
		$app = $comp_params->get( 'additional_data_source' );

		$username = $user_info['username'];
		$id = JUserHelper::getUserId($username);
		$user =& JFactory::getUser($id);


		/* Save info to joomla user table */
		$user->email = $user_info['email'];
		$user->name = $user_info['firstname'] . " " . $user_info['lastname'];
		//XXX more info ...

                /* Language */
            //    $user_info['lang']   = JoomdleHelperMappings::get_moodle_lang ($user->getParam( 'language' ));

                /* Timezone */
              //  $user_info['timezone']   = $user->getParam( 'timezone' );

		switch ($app)
		{
			case 'jomsocial':
				$more_info = JoomdleHelperMappings::save_user_info_jomsocial ($user_info);
				break;
			case 'virtuemart':
				$more_info = JoomdleHelperMappings::save_user_info_virtuemart ($user_info);
				break;
			case 'tienda':
				$more_info = JoomdleHelperMappings::save_user_info_tienda ($user_info);
				break;
			case 'cb':
				$more_info = JoomdleHelperMappings::save_user_info_cb ($user_info);
				break;
			default:
			//	$more_info = JoomdleHelperMappings::save_user_info_joomla ($username);
				break;
		}

		$user->save ();

		return array_merge ($user_info, $more_info);

		return $more_info;
	}


	function save_user_info_joomla ($user_info)
	{
		$username = $user_info['username'];
		$id = JUserHelper::getUserId($username);
		$user =& JFactory::getUser($id);
	}

	function save_avatar_jomsocial ($userid, $pic_url)
	{
		$pic = JoomdleHelperContent::get_file ($pic_url);

		if (!$pic)
			return;

		require_once(JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php');

                CFactory::load( 'helpers' , 'image' );

		$config                 = CFactory::getConfig();

		$imageMaxWidth  = 160;

		$extension = '.jpg';  // Moodle stores JPG always

		$jconfig = JFactory::getConfig();
		$tmp_file = $jconfig->getValue('tmp_path'). DS .'tmp_pic'.time();


		file_put_contents ($tmp_file, $pic);
		// Get a hash for the file name.
		$fileName               = JUtility::getHash( $pic_url . time() );
		$hashFileName   = JString::substr( $fileName , 0 , 24 );

		$storage                        = JPATH_ROOT . DS . $config->getString('imagefolder') . DS . 'avatar';
		$storageImage           = $storage . DS . $hashFileName . $extension;
		$storageThumbnail       = $storage . DS . 'thumb_' . $hashFileName . $extension ;
		$image                          = $config->getString('imagefolder') . '/avatar/' . $hashFileName . $extension ;
		$thumbnail                      = $config->getString('imagefolder') . '/avatar/' . 'thumb_' . $hashFileName . $extension;

		$userModel                      =& CFactory::getModel( 'user' );

		// Only resize when the width exceeds the max.
		list($currentWidth, $currentHeight) = getimagesize( $tmp_file );
		if ($currentWidth < $imageMaxWidth)
			$imageMaxWidth = $currentWidth;
		if( !CImageHelper::resizeProportional( $tmp_file , $storageImage , 'image/jpeg' , $imageMaxWidth ) ) //Moodle always stores jpg
		{
			$mainframe->enqueueMessage(JText::sprintf('CC ERROR MOVING UPLOADED FILE' , $storageImage), 'error');

			if(isset($url)){
				$mainframe->redirect($url);
			}
		}

		// Generate thumbnail
		if(!CImageHelper::createThumb( $tmp_file , $storageThumbnail , 'image/jpeg' )) //Moodle always stores jpg
		{
			$mainframe->enqueueMessage(JText::sprintf('CC ERROR MOVING UPLOADED FILE' , $storageThumbnail), 'error');

			if(isset($url)){
				$mainframe->redirect($url);
			}
		}

		$userModel->setImage( $userid , $image , 'avatar' );
		$userModel->setImage( $userid , $thumbnail , 'thumb' );

	}

	function save_user_info_jomsocial ($user_info)
	{
		$db = &JFactory::getDBO();

		$username = $user_info['username'];
		$id = JUserHelper::getUserId($username);
		$user =& JFactory::getUser($id);

		$mappings = JoomdleHelperMappings::get_app_mappings ('jomsocial');


		foreach ($mappings as $mapping)
		{
			$additional_info[$mapping->joomla_field] = $user_info[$mapping->moodle_field];
			JoomdleHelperMappings::set_field_value_jomsocial ($mapping->joomla_field, $user_info[$mapping->moodle_field], $id);
		}

		if ($user_info['pic_url'])
			JoomdleHelperMappings::save_avatar_jomsocial ($id, $user_info['pic_url']);

		return $additional_info;

	}

	function set_field_value_jomsocial ($field, $value, $user_id)
	{
                $db           =& JFactory::getDBO();

		/* Check if data needs transformation */
		$type = JoomdleHelperMappings::get_field_type_jomsocial ($field);
		switch ($type)
		{
			case 'country':
				$value = JoomdleHelperMappings::get_joomla_country ($value);
				break;
			default:
				break;
		}

		$query = 
			' SELECT count(*) from  #__community_fields_values' .
                              " WHERE field_id = " . $db->Quote($field) . " AND user_id = " . $db->Quote($user_id);

                $db->setQuery($query);
		$exists = $db->loadResult();

		if ($exists)
			$query = 
				' UPDATE #__community_fields_values' .
				' SET value='. $db->Quote($value) .
				      " WHERE field_id = " . $db->Quote($field) . " AND user_id = " . $db->Quote($user_id);
		else
			$query = 
				' INSERT INTO #__community_fields_values' .
				' (field_id, user_id, value) VALUES ('. $db->Quote($field) . ','.  $db->Quote($user_id) . ',' . $db->Quote($value) . ')';

                $db->setQuery($query);
                $db->query();
		
		return true;
	}

	/* Community Builder */
	function save_user_info_cb ($user_info)
	{
		$db = &JFactory::getDBO();

		$username = $user_info['username'];
		$id = JUserHelper::getUserId($username);
		$user =& JFactory::getUser($id);

		$mappings = JoomdleHelperMappings::get_app_mappings ('cb');


		foreach ($mappings as $mapping)
		{
			$additional_info[$mapping->joomla_field] = $user_info[$mapping->moodle_field];
			JoomdleHelperMappings::set_field_value_cb ($mapping->joomla_field, $user_info[$mapping->moodle_field], $id);
		}

	//	if ($user_info['pic_url'])
	//		JoomdleHelperMappings::save_avatar_cb ($id, $user_info['pic_url']);

		return $additional_info;

	}

	function set_field_value_cb ($field, $value, $user_id)
	{
		$db           =& JFactory::getDBO();

		$query = 
			' UPDATE #__comprofiler' .
			' SET '. $field.'='. $db->Quote($value) .
				  " WHERE user_id = " . $db->Quote($user_id);

		$db->setQuery($query);
		$db->query();
		
		return true;
	}

	/* Virtuemart */
	function save_user_info_virtuemart ($user_info)
	{
		$db = &JFactory::getDBO();

		$username = $user_info['username'];
		$id = JUserHelper::getUserId($username);
		$user =& JFactory::getUser($id);

		$mappings = JoomdleHelperMappings::get_app_mappings ('virtuemart');


		foreach ($mappings as $mapping)
		{
			$additional_info[$mapping->joomla_field] = $user_info[$mapping->moodle_field];
			JoomdleHelperMappings::set_field_value_virtuemart ($mapping->joomla_field, $user_info[$mapping->moodle_field], $id);
		}

		return $additional_info;
	}

	function set_field_value_virtuemart ($field, $value, $user_id)
	{
		$db           =& JFactory::getDBO();
		$query = 
			' UPDATE #__vm_user_info' .
			' SET '. $field.'='. $db->Quote($value) .
				  " WHERE user_id = " . $db->Quote($user_id);
		$db->setQuery($query);
		$field_object = $db->loadObject();
		
		$db->setQuery($query);
		$db->query();
		
		return true;
	}

	/* Tienda */
	function save_user_info_tienda ($user_info)
	{
		$db = &JFactory::getDBO();

		$username = $user_info['username'];
		$id = JUserHelper::getUserId($username);
		$user =& JFactory::getUser($id);

		$mappings = JoomdleHelperMappings::get_app_mappings ('tienda');


		foreach ($mappings as $mapping)
		{
			$additional_info[$mapping->joomla_field] = $user_info[$mapping->moodle_field];
			JoomdleHelperMappings::set_field_value_tienda ($mapping->joomla_field, $user_info[$mapping->moodle_field], $id);
		}

		return $additional_info;
	}

	function set_field_value_tienda ($field, $value, $user_id)
	{
		$db           =& JFactory::getDBO();
		$query = 
			' UPDATE  #__tienda_addresses' .
			' SET '. $field.'='. $db->Quote($value) .
				  " WHERE user_id = " . $db->Quote($user_id);
		$db->setQuery($query);
		$field_object = $db->loadObject();
		
		$db->setQuery($query);
		$db->query();
		
		return true;
	}
}
