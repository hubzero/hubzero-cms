<?php
/**
 * Joomla! 1.5 component Joomdle
 *
 * @version $Id: view.html.php 2009-04-17 03:54:05 svn $
 * @author Antonio Durán Terrés
 * @package Joomla
 * @subpackage Joomdle
 * @license GNU/GPL
 *
 * Shows information about Moodle courses
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
// Import Joomla! libraries
jimport( 'joomla.application.component.view');
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'content.php' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'mappings.php' );

class JoomdleViewMappings extends JView {
    function display($tpl = null) {
	    global $mainframe, $option;

	$id = JRequest::getVar( 'mapping_id' );
	$task = JRequest::getVar( 'task' );
	$comp_params = &JComponentHelper::getParams( 'com_joomdle' );
	$app = $comp_params->get( 'additional_data_source' );

	if ($app == 'none')
        {
                echo JText::_('CJ NO ADDITIONAL DATA SOURCE SELECTED');
                return;
        }


	/* Edit a mapping */
	if ($task == 'edit')
	{
		if ($id)
		{
			/* Edit */
			$this->mapping_id = $id;
			$this->mapping = JoomdleHelperMappings::getMapping ($id);
			$this->joomla_app = $this->mapping->joomla_app;
		}
		else
		{
			/* New */

			$this->mapping_id = "";
			$this->mapping->joomla_app = $app;
			$this->joomla_app = $app;
			$this->mapping->joomla_field = '';
			$this->mapping->moodle_field = '';
		}

		//XXX Si falla el coger el mapeo redirigir  a la laista

		$additional_data_source = array (
			JHTML::_('select.option', 'jomsocial', JText::_('Jomsocial')),
			JHTML::_('select.option', 'cb', JText::_('Community Builder')),
			JHTML::_('select.option', 'tienda', JText::_('Tienda')),
			JHTML::_('select.option', 'virtuemart', JText::_('Virtuemart')));
		$this->lists['additional_data_source'] = JHTML::_('select.genericlist',  $additional_data_source, 'additional_data_source', 'class="inputbox" size="1" disabled', 'value',
                        'text', $this->mapping->joomla_app);

		$joomla_fields = JoomdleHelperMappings::get_fields ($this->mapping->joomla_app);
		foreach ($joomla_fields as $jf)
		{
			$joomla_field[] = JHTML::_('select.option', $jf->id, $jf->name);
		}
		$this->lists['joomla_field'] = JHTML::_('select.genericlist',  $joomla_field, 'joomla_field', 'class="inputbox" size="1"', 'value',
                        'text', $this->mapping->joomla_field);

		/*
			JHTML::_('select.option', 'firstname', JText::_('CJM FIRSTNAME')),
			JHTML::_('select.option', 'lastname', JText::_('CJM LASTNAME')),
			JHTML::_('select.option', 'email', JText::_('CJM EMAIL')),
			JHTML::_('select.option', 'icq', JText::_('CJM ICQ')),
			JHTML::_('select.option', 'skype', JText::_('CJM SKYPE')),
			JHTML::_('select.option', 'yahoo', JText::_('CJM YAHOO')),
			JHTML::_('select.option', 'aim', JText::_('CJM AIM')),
			JHTML::_('select.option', 'msn', JText::_('CJM MSN')),
			JHTML::_('select.option', 'phone1', JText::_('CJM PHONE1')),
			JHTML::_('select.option', 'phone2', JText::_('CJM PHONE2')),
			JHTML::_('select.option', 'institution', JText::_('CJM INSTITUTION')),
			JHTML::_('select.option', 'department', JText::_('CJM DEPARTMENT')),
			JHTML::_('select.option', 'address', JText::_('CJM ADDRESS')),
			JHTML::_('select.option', 'city', JText::_('CJM CITY')),
			JHTML::_('select.option', 'country', JText::_('CJM COUNTRY')),
			JHTML::_('select.option', 'lang', JText::_('CJM LANG')),
			JHTML::_('select.option', 'timezone', JText::_('CJM TIMEZONE')),
			JHTML::_('select.option', 'description', JText::_('CJM DESCRIPTION'))
			*/
		$moodle_fields = array (
			JHTML::_('select.option', 'firstname', 'firstname'),
			JHTML::_('select.option', 'lastname', 'lastname'),
			JHTML::_('select.option', 'email', 'email'),
			JHTML::_('select.option', 'icq', 'icq'),
			JHTML::_('select.option', 'skype','skype'),
			JHTML::_('select.option', 'yahoo','yahoo'),
			JHTML::_('select.option', 'aim',  'aim'),
			JHTML::_('select.option', 'msn', 'msn'),
			JHTML::_('select.option', 'phone1', 'phone1'),
			JHTML::_('select.option', 'phone2',  'phone2'),
			JHTML::_('select.option', 'institution', 'institution'),
			JHTML::_('select.option', 'department', 'department'),
			JHTML::_('select.option', 'address', 'address'),
			JHTML::_('select.option', 'city', 'city'),
			JHTML::_('select.option', 'country', 'country'),
			JHTML::_('select.option', 'lang', 'lang'),
			JHTML::_('select.option', 'timezone', 'timezone'),
			JHTML::_('select.option', 'idnumber', 'idnumber'),
			JHTML::_('select.option', 'description',  'description')
			);
		$this->lists['moodle_field'] = JHTML::_('select.genericlist',  $moodle_fields, 'moodle_field', 'class="inputbox" size="1" ', 'value',
                        'text', $this->mapping->moodle_field);

		$tpl = 'item';

		parent::display($tpl);

		return;
	}
	
	/* List of mappings */

	$params = &JComponentHelper::getParams( 'com_joomdle' );

	$filter_order           = $mainframe->getUserStateFromRequest( "$option.filter_order",          'filter_order',         'joomla_app',       'cmd' );
	$filter_order_Dir       = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",      'filter_order_Dir',     'asc',                     'word' );

	/* kludge while I learn how to do it FIXME */
	if (($filter_order != 'joomla_app') && ($filter_order != 'moodle_field'))
		$filter_order = 'joomla_app';
	/* kludge while I learn how to do it FIXME */

	$filter_type            = $mainframe->getUserStateFromRequest( "$option.filter_type",           'filter_type',          0,                      'string' );
	$search                         = $mainframe->getUserStateFromRequest( "$option.search",                        'search',                       '',                     'string' );
	$search                         = JString::strtolower( $search );

	$limit          = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
	$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );


	$types[] = JHTML::_('select.option',  0, '- '. JText::_( 'CJ SELECT JOOMLA APP' ) .' -');
	$types[] = JHTML::_('select.option',  'jomsocial',  'Jomsocial');
	$types[] = JHTML::_('select.option',  'cb',  'Community Builder');
	$types[] = JHTML::_('select.option',  'virtuemart', 'Virtuemart' );
	$types[] = JHTML::_('select.option',  'tienda',  'Tienda');
	$lists['type']  = JHTML::_('select.genericlist',   $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type" );

	$lists['search'] = $search;

	// table ordering
	$lists['order_Dir']     = $filter_order_Dir;
	$lists['order']         = $filter_order;


	$this->assignRef('lists',               $lists);


	jimport('joomla.html.pagination');
	$db           =& JFactory::getDBO();
	if ($search)
		$searchEscaped = $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
	else $searchEscaped = "";

	$this->mappings = JoomdleHelperMappings::getMappings ($filter_type, $limitstart, $limit,$filter_order, $filter_order_Dir, $searchEscaped);

	/*
	if ($filter_type)
	{

		if ($filter_type == 'moodle')
		{
			$total = JoomdleHelperMapping::getMappingsNumber ($searchEscaped);
			$pagination = new JPagination( $total, $limitstart, $limit );
			$this->users = JoomdleHelperMapping::getMappings ($limitstart, $limit,$filter_order, $filter_order_Dir, $searchEscaped);
		}
		else if ($filter_type == 'joomla')
		{
			$total = JoomdleHelperContent::getJoomlaUsersNumber ($searchEscaped);
			$pagination = new JPagination( $total, $limitstart, $limit );
			$this->users = JoomdleHelperContent::getJoomlaUsers ($limitstart, $limit, $filter_order, $filter_order_Dir, $searchEscaped);
		}
		else if ($filter_type == 'joomdle')
		{
			$total = count (JoomdleHelperContent::getJoomdleUsers (0, 0, $filter_order, $filter_order_Dir, $searchEscaped));
			$pagination = new JPagination( $total, $limitstart, $limit );
			$this->users = JoomdleHelperContent::getJoomdleUsers ($limitstart, $limit,  $filter_order, $filter_order_Dir, $searchEscaped);
		}
		else if ($filter_type == 'not_joomdle')
		{
			$total = count (JoomdleHelperContent::getNotJoomdleUsers (0, 0, $filter_order, $filter_order_Dir, $searchEscaped));
			$pagination = new JPagination( $total, $limitstart, $limit );
			$this->users = JoomdleHelperContent::getNotJoomdleUsers ($limitstart, $limit, $filter_order, $filter_order_Dir, $searchEscaped);
		}
	}
	else {
		/* Show all users */
	//	$this->mappings = JoomdleHelperMappings::getMappings ($limitstart, $limit,$filter_order, $filter_order_Dir, $searchEscaped);

		if (!$this->mappings)
			$this->mappings = array ();

	//	print_r ($this->mappings);
		$total  = count (JoomdleHelperMappings::getMappings ('', 0, 100000, 'joomla_app', '', ''));
	//	$total = JoomdleHelperContent::getAllUsersNumber ($searchEscaped);
		$pagination = new JPagination( $total, $limitstart, $limit );
	//	$this->users = JoomdleHelperContent::getAllUsers ($limitstart, $limit, $filter_order, $filter_order_Dir, $searchEscaped); //XXX merge lists
		//$pagination = new JPagination( count ($this->users), $limitstart, $limit );
	//	$pagination = new JPagination( $total, $limitstart, $limit );
		/*
		$this->users = JoomdleHelperContent::getJoomlaUsers ();
		print_r ($this->users);*/
//	}

	$this->assignRef('pagination',  $pagination);


        parent::display($tpl);
    }
}
?>
