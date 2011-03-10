<?php
/**
 * Joomla! 1.5 component Joomdle
 *
 * @version 
 * @author Antonio Durán Terrés
 * @package Joomdle
 * @license GNU/GPL
 *
 * Shows information about Moodle courses
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'content.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'shop.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'mappings.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'parents.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'profiletypes.php' );

/**
 * Joomdle Controller
 *
 * @package Joomla
 * @subpackage Joomdle
 */
class JoomdleController extends JController {
    /**
     * Constructor
     * @access private
     * @subpackage Joomdle
     */
    function __construct() {
        //Get View
        if(JRequest::getCmd('view') == '') {
            JRequest::setVar('view', 'default');
        }
        $this->item_type = 'Default';
        parent::__construct();
    }

    	/* Shop actions */
        function publish()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' ); 

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to publish' ) );
                }
		JoomdleHelperShop::sell_courses ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=shop' );

	}

        function unpublish()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
                }
		JoomdleHelperShop::dont_sell_courses ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=shop' );

	}

	function reload ()
        {
                JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
                }
                JoomdleHelperShop::reload_courses ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=shop' );
        }

	function delete_courses_from_shop ()
        {
                JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to delete' ) );
                }
                JoomdleHelperShop::delete_courses ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=shop' );
        }

    	/* Users actions */
	function add_to_moodle ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
                }
		JoomdleHelperContent::add_moodle_users ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
	}

	function migrate_to_joomdle ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
                }
		JoomdleHelperContent::migrate_users_to_joomdle ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
	}

	function sync_profile_to_moodle ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
                }
		JoomdleHelperContent::sync_moodle_profiles ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
	}

	function add_to_joomla ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
                }
		JoomdleHelperContent::create_joomla_users ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
	}

	function sync_parents_from_moodle ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
                }
		JoomdleHelperParents::sync_parents_from_moodle ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
	}

    	/* Config actions */
	function save_configuration_options ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
                $component = JRequest::getCmd( 'option' );

                $table =& JTable::getInstance('component');
                if (!$table->loadByOption( $component ))
                {
                        JError::raiseWarning( 500, 'Not a valid component' );
                        return false;
                }

		/* General config */
		$config_array['MOODLE_URL'] = JRequest::getVar('MOODLE_URL', 0, 'post', 'string');
		$config_array['moodle_version'] = JRequest::getVar('moodle_version', 19, 'post', 'string');
		$config_array['auth_token'] = JRequest::getVar('auth_token', '', 'post', 'string');
		$config_array['connection_method'] = JRequest::getVar('connection_method', 0, 'post', 'string');
		$config_array['auto_create_users'] = JRequest::getVar('auto_create_users', 0, 'post', 'int');
		$config_array['auto_delete_users'] = JRequest::getVar('auto_delete_users', 0, 'post', 'int');
		$config_array['auto_login_users'] = JRequest::getVar('auto_login_users', 0, 'post', 'int');

		/* Links target */
		$config_array['linkstarget'] = JRequest::getVar('linkstarget', 0, 'post', 'string');

		/* Wrapper config */
		$config_array['scrolling'] = JRequest::getVar('scrolling', 0, 'post', 'string');
		$config_array['width'] = JRequest::getVar('width', 0, 'post', 'string');
		$config_array['height'] = JRequest::getVar('height', 0, 'post', 'string');
		$config_array['autoheight'] = JRequest::getVar('autoheight', 1, 'post', 'int');
		$config_array['transparency'] = JRequest::getVar('transparency', 1, 'post', 'int');
		$config_array['default_itemid'] = JRequest::getVar('default_itemid', 0, 'post', 'string');

		/* Detail view */
		$config_array['show_topìcs_link'] = JRequest::getVar('show_topìcs_link', 0, 'post', 'int');
		$config_array['show_grading_system_link'] = JRequest::getVar('show_grading_system_link', 0, 'post', 'int');
		$config_array['show_teachers_link'] = JRequest::getVar('show_teachers_link', 0, 'post', 'int');
		$config_array['show_enrol_link'] = JRequest::getVar('show_enrol_link', 0, 'post', 'int');
		$config_array['show_paypal_button'] = JRequest::getVar('show_paypal_button', 0, 'post', 'int');

		/* Shop */
		$config_array['shop_integration'] = JRequest::getVar('shop_integration', 0, 'post', 'string');
		$config_array['courses_category'] = JRequest::getVar('courses_category', 0, 'post', 'int');
		$config_array['buy_for_children'] = JRequest::getVar('buy_for_children', 0, 'post', 'int');
		$config_array['enrol_email_subject'] = JRequest::getVar('enrol_email_subject', 0, 'post', 'string');
		$config_array['enrol_email_text'] = JRequest::getVar('enrol_email_text', 0, 'post', 'string');
		
		/* Additional data source */
		$config_array['additional_data_source'] = JRequest::getVar('additional_data_source', 0, 'post', 'string');
		/* XIPT integration */
		$config_array['use_xipt_integration'] = JRequest::getVar('use_xipt_integration', 0, 'post', 'string');

		$post['params'] = $config_array;
                $post['option'] = $component;
                $table->bind( $post );

		// pre-save checks
                if (!$table->check()) {
                        JError::raiseWarning( 500, $table->getError() );
                        return false;
                }

                // save the changes
                if (!$table->store()) {
                        JError::raiseWarning( 500, $table->getError() );
                        return false;
                }



	}

	function save_config ()
	{
		$this->save_configuration_options ();
		$this->setRedirect( 'index.php?option=com_joomdle&view=default' );
	}

	function apply_config ()
	{
		$this->save_configuration_options ();
		$this->setRedirect( 'index.php?option=com_joomdle&view=config' );
	}

    	/* Mappings actions */
	function new_mapping ()
	{
		$this->setRedirect( 'index.php?option=com_joomdle&view=mappings&task=edit' );
	}

	function save_mapping ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		/* General config */
	//	$mapping['joomla_app'] = JRequest::getVar('additional_data_source', 0, 'post', 'string');
		$mapping['joomla_app'] = JRequest::getVar('joomla_app', 0, 'post', 'string');
		$mapping['joomla_field'] = JRequest::getVar('joomla_field', 0, 'post', 'string');
		$mapping['moodle_field'] = JRequest::getVar('moodle_field', 0, 'post', 'string');
		$mapping['id'] = JRequest::getVar('mapping_id', 0, 'post', 'string');

		if ($mapping['id'])
			$m->id =  $mapping['id'];

		$m->joomla_app =  $mapping['joomla_app'];
		$m->joomla_field =  $mapping['joomla_field'];
		$m->moodle_field =  $mapping['moodle_field'];

		$db           =& JFactory::getDBO();

		/* Update record */
		if ($mapping['id'])
			$db->updateObject ('#__joomdle_field_mappings', $m, 'id');
		else
		{
			/* Insert new record */
			$db->insertObject ('#__joomdle_field_mappings', $m);
		}


		$this->setRedirect( 'index.php?option=com_joomdle&view=mappings' );
	}

        function delete_mappings()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to delete' ) );
                }
		JoomdleHelperMappings::delete_mappings ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=mappings' );

	}

	function create_profiletype_on_moodle ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to delete' ) );
                }
		JoomdleHelperProfiletypes::create_on_moodle ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=customprofiletypes' );
	}

	function dont_create_profiletype_on_moodle ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
                JArrayHelper::toInteger($cid);

                if (count( $cid ) < 1) {
                        JError::raiseError(500, JText::_( 'Select an item to delete' ) );
                }
		JoomdleHelperProfiletypes::dont_create_on_moodle ($cid);

                $this->setRedirect( 'index.php?option=com_joomdle&view=customprofiletypes' );
	}

}
?>
