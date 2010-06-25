<?php
/**
* @author Guillermo Vargas
* @email guille@vargas.co.cr
* @version $Id: com_acymailing.php
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Joomla's web links component
*/

defined( '_JEXEC' ) or die( 'Restricted access.' );

class xmap_com_acymailing {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item and indicate whether the node is expandible or not
	*/
	function prepareMenuItem(&$node) {
		$query = parse_url( $node->link );
		parse_str( html_entity_decode($query['query']), $url_vars);
        $view = JArrayHelper::getValue($url_vars,'view','');
        $task = JArrayHelper::getValue($url_vars,'task','');
        $ctrl = JArrayHelper::getValue($url_vars,'ctrl','');
		$mailid = intval(JArrayHelper::getValue($url_vars,'mailid',''));
		if ( $task == 'view' && $mailid ) {
			$node->uid = 'com_acymailingm'.$id;
			$node->expandible = false;
		} elseif ( $view == 'lists' ) {
            $node->uid = 'com_acymailinglists';
            $node->expandible = true;
        } elseif ( $view == 'archive' || ($ctrl == 'archive' && !$mailid) ) {
			$catid = intval(JArrayHelper::getValue($url_vars,'listid',0));
			$node->uid = 'com_acymailingl'.$catid;
			$node->expandible = true;
		}
	}   

	function getTree( &$xmap, &$parent, &$params) {
		$url_query = parse_url( $parent->link );
		parse_str( html_entity_decode($url_query['query']), $url_vars );
        $view = JArrayHelper::getValue($url_vars,'view','');
        $ctrl = JArrayHelper::getValue($url_vars,'ctrl','');
		$mailid = intval(JArrayHelper::getValue($url_vars,'mailid',''));
        $extraParams = '';

		$menu =& JSite::getMenu();
		$menuparams = $menu->getParams($parent->id);

		if ( $view == 'archive' || ($ctrl == 'archive' && !$mailid) ) {
			    $catid = intval($menuparams->get('listid',0));
        } elseif ( $view == 'lists' ) {
                $catid = 0;
        } else { // Only expand category menu items
			return;
		}

		$include_mails = JArrayHelper::getValue( $params, 'include_mails',1,'' );
		$include_mails = ( $include_mails == 1
				  || ( $include_mails == 2 && $xmap->view == 'xml')
				  || ( $include_mails == 3 && $xmap->view == 'html')
				  ||   $xmap->view == 'navigator');
		$params['include_mails'] = $include_mails;

		$priority = JArrayHelper::getValue($params,'cat_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'cat_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['cat_priority'] = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority = JArrayHelper::getValue($params,'mail_priority',$parent->priority,'');
		$changefreq = JArrayHelper::getValue($params,'mail_changefreq',$parent->changefreq,'');
		if ($priority  == '-1')
			$priority = $parent->priority;

		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['mail_priority'] = $priority;
		$params['mail_changefreq'] = $changefreq;

		$params['limit'] = '';
		$limit = intval(JArrayHelper::getValue($params,'max_mails',0,''));

		if ( intval($limit) && $xmap->view != 'navigator' ) {
			$params['limit'] = ' LIMIT '.$limit;
		}

		xmap_com_acymailing::getCategoryTree($xmap, $parent, $params, $catid, $extraParams );

	}

	function getCategoryTree ( &$xmap, &$parent, &$params, $listid) {
		$db = &JFactory::getDBO();

        if (!$listid) { // view=lists
            $query = ' SELECT a.listid,a.name'.
                     ' ,CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS("-", a.listid, a.alias) ELSE a.listid END as slug' .
                     ' FROM #__acymailing_list AS a '.
                     ' WHERE a.published=1 AND  a.visible=1'.
                     ' ORDER by a.ordering ';

            $db->setQuery($query);
            $cats = $db->loadObjectList();
            $xmap->changeLevel(1);
            foreach($cats as $cat) {
                $node = new stdclass;
                $node->id   = $parent->id;
                $node->uid  = 'com_acymailingl'.$cat->listid;
                $node->name = $cat->name;
                $node->link = 'index.php?option=com_acymailing&ctrl=archive&listid='.$cat->slug;
                $node->priority   = $params['cat_priority'];
                $node->changefreq = $params['cat_changefreq'];
                $node->expandible = true;
                if ( $xmap->printNode($node) !== FALSE ) {
                    xmap_com_acymailing::getCategoryTree( $xmap, $parent, $params, $cat->listid,'&ctrl=archive' );
                }
            }
            $xmap->changeLevel(-1);
        } elseif ( $params['include_mails'] ) { //view=archive || //ctrl=archive&listid=...
        
		    $query = ' SELECT a.mailid,a.subject,a.created'.
		             ' ,CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS("-", a.mailid, a.alias) ELSE a.mailid END as slug' .
		             ' ,CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS("-", c.listid, c.alias) ELSE c.listid END as catslug'.
		             ' FROM #__acymailing_mail AS a,#__acymailing_listmail AS ac,#__acymailing_list AS c '.
		             ' WHERE ac.mailid = a.mailid AND a.published=1 and a.visible=1 '.
		             ' AND ac.listid='.$listid.' AND c.listid=ac.listid ' .
		             ' ORDER by a.created '.
		             $params['limit'];

            $db->setQuery($query);
		    $mails = $db->loadObjectList();
		    $xmap->changeLevel(1);
		    foreach($mails as $mail) {
			    $node = new stdclass;
			    $node->id   = $parent->id;
			    $node->uid  = 'com_acymailingm'.$mail->mailid;
                $node->name = $mail->subject;
			    $node->created = $mail->created;
			    $node->link = 'index.php?option=com_acymailing&ctrl=archive&task=view&listid='.$mail->catslug.'&mailid='.$mail->slug;
			    $node->priority   = $params['mail_priority'];
			    $node->changefreq = $params['mail_changefreq'];
			    $node->expandible = false;
			    $xmap->printNode($node);
		    }
		    $xmap->changeLevel(-1);
        }
	}

}
