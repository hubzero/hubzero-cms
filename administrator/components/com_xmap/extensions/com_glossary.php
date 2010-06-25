<?php
/**
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @version $Id: com_glossary.php 52 2009-10-24 22:35:11Z guilleva $
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Glossary Forumn Component. 
*/

/** Handles Glossary component structure */
class xmap_com_glossary {

	/*
	* This function is called before a menu item is printed. We use it to set the
	* proper uniqueid for the item
	*/
	function prepareMenuItem(&$node) {
		$link_query = parse_url( $node->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$letter = JArrayHelper::getValue($link_vars,'letter','');
		$id = intval(JArrayHelper::getValue($link_vars,'id',0));
		$func = JArrayHelper::getValue( $link_vars, 'func', '', '' );
		if ( $letter && !$id ) {
			$node->uid = 'com_glossaryl'.$catid;
			$node->expandible = true;
		} elseif ( $id) {
			$node->uid = 'com_glossarye'.$id;
			$node->expandible = false;
		}
	}

	function getTree ( &$xmap, &$parent, &$params ) {
		if (! file_exists((JPATH_SITE.DS.'components'.DS.'com_glossary'.DS.'cmsapi.interface.php'))){
			return false;
		}

		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$glossaryid = $xmap->getParam($link_vars,'glossid',0);
		$letter = $xmap->getParam($link_vars,'letter','');
		$id = $xmap->getParam($link_vars,'id',0);

		$conf =&  xmap_com_glossary::getConfig();
		if ($id) { // Do not expand links to entries
			return;
		} elseif ( !$glossaryid ) {
			if (!$conf->showcategories) {
				$database =& JFactory::getDBO();
				$query = "SELECT id FROM #__glossaries ORDER BY isdefault DESC, id LIMIT 1";
                        	$database->setQuery($query);
                        	$glossaryid = $database->loadResult();
			}
		}

		$include_entries = $xmap->getParam($params,'include_entries',1);
		$include_entries = ( $include_entries == 1
				  || ( $include_entries == 2 && $xmap->view == 'xml') 
				  || ( $include_entries == 3 && $xmap->view == 'html')
				  || $xmap->view == 'navigator');
		$params['include_entries'] = $include_entries;


		$priority = $xmap->getParam($params,'letter_priority',$parent->priority);
		$changefreq = $xmap->getParam($params,'letter_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;
		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['letter_priority'] = $priority;
		$params['letter_changefreq'] = $changefreq;

		// Entries Properties
		$priority = $xmap->getParam($params,'entry_priority',$parent->priority);
		$changefreq = $xmap->getParam($params,'entry_changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority = $parent->priority;

		if ($changefreq  == '-1')
			$changefreq = $parent->changefreq;

		$params['entry_priority'] = $priority;
		$params['entry_changefreq'] = $changefreq;


		if ( $include_entries ) {
			$params['limit'] = '';
			$limit = $xmap->getParam($params,'max_entries','');
			if ( intval($limit) )
				$params['limit'] = ' LIMIT '.$limit;
		}

		if ( !$letter && $glossaryid ) {
			xmap_com_glossary::getGlossaryTree($xmap, $parent, $params, $glossaryid);
		} elseif ( $letter ) {
			xmap_com_glossary::getLetterTree($xmap, $parent, $params, $letter, $glossaryid);
		} else {
			xmap_com_glossary::getFullTree($xmap, $parent, $params);
		}
	}

	/* Return glossaries/letters/entries tree */
	function getFullTree( &$xmap, &$parent, &$params ) 
	{
		$database =& JFactory::getDBO();

		/*get list of glossaries */
		$xmap->changeLevel(1);
		$query = "select g.id,g.name,UNIX_TIMESTAMP(max(t.teditdate)) as `modified` FROM ".
			 "#__glossaries AS g LEFT JOIN #__glossary AS t ON g.id = t.catid AND t.published=1 ".
			 "WHERE g.published=1 ".
			 "GROUP BY t.catid ORDER BY name";
		$database->setQuery($query);
		# echo $database->getQuery();
		$glossaries = $database->loadObjectList();

		foreach ( $glossaries as $glossary ) {
			$node = new stdclass;
			$node->id   = $parent->id;
			$node->browserNav = $parent->browserNav;
			$node->uid   = $parent->uid.'l'.$glossary->id;
			$node->name = $glossary->name;
			$node->modified = intval($glossary->modified);
			$node->priority = $params['letter_priority'];
			$node->changefreq = $params['letter_changefreq'];
			$node->link = 'index.php?option=com_glossary&amp;glossid='.$glossary->id;
			$node->expandible = true;
			if ( ($xmap->printNode($node) !== FALSE) ) {
				xmap_com_glossary::getGlossaryTree($xmap,$parent,$params,$glossary->id,0);
			}
		}
		$xmap->changeLevel(-1);
	}

	/* Return glossary/letter tree */
	function getGlossaryTree( &$xmap, &$parent, &$params, $glossaryid ) 
	{
		$database =& JFactory::getDBO();

			/*get list of letters */
			$xmap->changeLevel(1);
			$query = "select tletter,unix_timestamp(max(teditdate)) as `modified` from #__glossary where published=1 && catid=$glossaryid group by tletter order by tletter";
			$database->setQuery($query);
			# echo $database->getQuery();
			$letters = $database->loadObjectList();

			foreach ( $letters as $letter ) {
				$node = new stdclass;
				$node->id   = $parent->id;
				$node->browserNav = $parent->browserNav;
				$node->uid   = $parent->uid.'l'.$letter->tletter;
				$node->name = $letter->tletter;
				$node->modified = intval($letter->modified);
				$node->priority = $params['letter_priority'];
				$node->changefreq = $params['letter_changefreq'];
				$node->link = 'index.php?option=com_glossary&amp;glossid='.$glossaryid.'&amp;letter='.$letter->tletter;
				$node->expandible = true;
				if ( ($xmap->printNode($node) !== FALSE) &&  $params['include_entries'] ) {
					xmap_com_glossary::getLetterTree($xmap,$parent,$params,$letter->tletter,$glossaryid);
				}
			}
			$xmap->changeLevel(-1);
	}

	function getLetterTree( &$xmap, &$parent, &$params,$letter,$glossaryid ) 
	{
		$database =& JFactory::getDBO();
		if ( $letter && $glossaryid ) {
			$xmap->changeLevel(1);
			$query = "SELECT id,tterm,unix_timestamp(tdate) as created, unix_timestamp(teditdate) as modified ".
			         "FROM #__glossary ".
			         "WHERE tletter='".$database->getEscaped($letter)."' ".
			         "AND catid=$glossaryid AND published=1 " .
				 $params['limit'];
			$database->setQuery($query);
			$entries = $database->loadObjectList();
			//get list of forums
			foreach($entries as $entry) {
				if (!$entry->modified) $entry->modified = $entry->created;
				$node = new stdclass;
				$node->id   = $parent->id;
				$node->browserNav = $parent->browserNav;
				$node->uid = $parent->uid.'e'.$entry->id;
				$node->name = $entry->tterm;
				$node->priority = $params['entry_priority'];
				$node->changefreq = $params['entry_changefreq'];
				$node->modified = intval($entry->modified);
				$node->link = 'index.php?option=com_glossary&amp;task=entry&amp;id='.$entry->id;
				$node->expandible = false;
				$xmap->printNode($node);
			}
			$xmap->changeLevel(-1);
		}
	}

	function &getConfig() {
		static $config;
		if (!isset($config)) {
			require_once(JPATH_SITE.DS.'components'.DS.'com_glossary'.DS.'cmsapi.interface.php');
			$config =& cmsapiConfiguration::getInstance();
		}

		return $config;
	}
}
