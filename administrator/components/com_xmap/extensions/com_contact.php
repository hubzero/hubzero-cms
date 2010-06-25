<?php
/**
* @author Guillermo Vargas
* @email guille@vargas.co.cr
* @version $Id: com_contact.php
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Joomla's contact component
*/

defined( '_JEXEC' ) or die( 'Restricted access.' );

class xmap_com_contact {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item and indicate whether the node is expandible or not
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$view = JArrayHelper::getValue($link_vars,'view','');
		if ( $view == 'contact') {
			$id = intval(JArrayHelper::getValue($link_vars,'id',0));
			if ( $id ) {
				$node->uid = 'com_contacti'.$id;
				$node->expandible = false;
			}
		}else{
			$catid = intval(JArrayHelper::getValue($link_vars,'catid',0));
			$node->uid = 'com_contactc'.$catid;
			$node->expandible = true;
		}

	}

	function getTree( &$xmap, &$parent, &$params) {

		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars );
		$view = JArrayHelper::getValue($link_vars,'view',0);

		$menu =& JSite::getMenu();
		$menuparams = $menu->getParams($parent->id);

		$catid = 0;
		if ( $view == 'category' ) {
			$catid = intval(JArrayHelper::getValue($link_vars,'catid',0));
		}  else { // Only expand category menu items
			if (!$menuparams->get('show_contact_list')) {
				return;
			}
		}

		$include_contacts = JArrayHelper::getValue( $params, 'include_contacts',1,'' );
		$include_contacts = ( $include_contacts == 1
				  || ( $include_contacts == 2 && $xmap->view == 'xml')
				  || ( $include_contacts == 3 && $xmap->view == 'html')
				  ||   $xmap->view == 'navigator');
		$params['include_contacts'] = $include_contacts;

		$priority = JArrayHelper::getValue($params,'cat_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'cat_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = JArrayHelper::getValue($params,'contact_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'contact_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;

		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['contact_priority'] = $priority;
		$params['contact_changefreq'] = $changefreq;

		$params['limit'] = '';
		$limit = JArrayHelper::getValue($params,'max_contacts','','');

		if ( intval($limit) && $xmap->view != 'navigator' ) {
			$params['limit'] = ' LIMIT '.$limit;
		}

		xmap_com_contact::getCategoryTree($xmap, $parent, $params, $catid );

	}

	function getCategoryTree ( &$xmap, &$parent, &$params, $catid) {
		$db = &JFactory::getDBO();

		$query = ' SELECT a.id,a.name'.
		         ' ,CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug' .
		         ' ,CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug'.
		         ' FROM #__contact_details a,#__categories c '.
		         ' WHERE a.catid = c.id AND c.published=1 and c.access<='.$xmap->gid.' AND '.
		         ' a.access<='.$xmap->gid.' AND  a.published=1 '.
		         ( $catid? ' AND a.catid='.$catid.' ' : ' ') .
		         ' ORDER by a.ordering '.
		         $params['limit'];

		$db->setQuery($query);
		//echo $db->getQuery();
		$contacts = $db->loadObjectList();
		$xmap->changeLevel(1);
		foreach($contacts as $contact) {
			$node = new stdclass;
			$node->id   = $parent->id;
			$node->uid  = $parent->uid .'i'.$contact->id;
			$node->name = $contact->name;
			$node->link = 'index.php?option=com_contact&amp;view=contact&amp;id='.$contact->slug . "&amp;catid=" . $contact->catslug;
			$node->priority   = $params['contact_priority'];
			$node->changefreq = $params['contact_changefreq'];
			$node->expandible = false;
			//$node->tree = array();
			$xmap->printNode($node);
		}
		$xmap->changeLevel(-1);
	}

}
