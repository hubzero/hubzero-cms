<?php
/**
 * $Id: XmapConfig.php 95 2010-04-14 18:38:36Z guilleva $
 * $LastChangedDate: 2010-04-14 12:38:36 -0600 (mié, 14 abr 2010) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

/** Wraps all configuration functions for Xmap */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xmap'.DS.'classes'.DS.'XmapSitemap.php');
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xmap'.DS.'classes'.DS.'XmapPlugin.php');

class XmapConfig {
	var $version 			= '1.1';
	var $classname 			= 'sitemap';
	var $expand_category 	= 1;
	var $expand_section 	= 1;
	var $show_menutitle 	= 1;
	var $columns 			= 1;
	var $exlinks 			= 1;
	var $ext_image 			= 'img_grey.gif';
	var $exclmenus			= '';
	var $includelink		= 1;
	var $sitemap_default		= 1;
	var $exclude_css		= 0;
	var $exclude_xsl		= 0;

	function XmapConfig () {
		$version 		= '1.2';
		$classname 		= 'sitemap';
		$expand_category 	= 1;
		$expand_section 	= 1;
		$show_menutitle 	= 1;
		$columns 		= 1;
		$exlinks 		= 1;
		$ext_image 		= 'img_grey.gif';
		$exclmenus		= '';
		$includelink		= 1;
		$sitemap_default	= 1;
		$exclude_css		= 0;
		$exclude_xsl		= 0;

	}

	/** Return $menus as an associative array */
	function &getSitemaps() {
		$db = & JFactory::getDBO();

		$query = "SELECT id FROM #__xmap_sitemap";
		$db->setQuery($query);
		$ids = $db->loadResultArray();
		$sitemaps = array();
		foreach ($ids as $id ) {
			$sitemap = new XmapSitemap($db);
			$sitemap->load($id);
			$sitemaps[] = $sitemap;
		}
		return $sitemaps;

	}

	/** Create the settings table for Xmap and add initial default values */
	function create() {
		$db = & JFactory::getDBO();
		jimport('joomla.filesystem.file');

		$fields = array();
		$fields[] = "`name` varchar(30) not null primary key";
		$fields[] = "`value` varchar(100)";


		$query = "CREATE TABLE IF NOT EXISTS #__xmap (". implode(', ', $fields) .")";
		$db->setQuery( $query );
		if( $db->query() === FALSE ) {
			echo _XMAP_ERR_NO_CREATE . "<br />\n";
			echo stripslashes($db->getErrorMsg());
			return false;
		}

		$fields = array();
		$fields[] = "`id` int not null primary key auto_increment";
		$fields[] = "`extension` varchar(100) not null";
		$fields[] = "`published` int(1) default 0";
		$fields[] = "`params` text";

		$query = "CREATE TABLE IF NOT EXISTS #__xmap_ext (". implode(', ', $fields) .")";
		$db->setQuery( $query );
		if( $db->query() === FALSE ) {
			echo _XMAP_ERR_NO_CREATE . "<br />\n";
			echo stripslashes($db->getErrorMsg());
			return false;
		}

		$extensions = array (
			//	name			published
			array(	'com_agora',		1),
			array(	'com_contact',		1),
			array(	'com_content',		1),
			array(	'com_eventlist',	1),
			array(	'com_g2bridge',		1),
			array(	'com_glossary',		1),
			array(	'com_hotproperty',	1),
			array(	'com_jcalpro',		1),
			array(	'com_jdownloads',	1),
			array(	'com_jevents',		1),
			array(	'com_jmovies',		1),
			array(	'com_jomres',		1),
			array(	'com_joomdoc',		1),
			array(	'com_joomgallery',	1),
			array(	'com_kb',		    1),
			array(	'com_kunena',		1),
			array(	'com_mtree',		1),
			array(	'com_myblog',		1),
			array(	'com_rapidrecipe',	1),
			array(	'com_remository',	1),
			array(	'com_resource',		1),
			array(	'com_rokdownloads',	1),
			array(	'com_rsgallery2',	1),
			array(	'com_sectionex',	1),
            array(  'com_cmsshopbuilder', 1),
			array(	'com_sobi2',		1),
            array(  'com_virtuemart',   1),
			array(	'com_weblinks',     1)
		);

		foreach ( $extensions as $ext ) {
			$query = "SELECT COUNT(*) FROM `#__xmap_ext` WHERE extension='{$ext[0]}'";
			$db->setQuery($query);
			$extension = new XmapPlugin($db);
			$extension->extension = $ext[0];
			$extension->published = $ext[1];
			$xmlfile = $extension->getXmlPath();
			JFile::move("$xmlfile.txt",$xmlfile);
			$extension->setParams($extension->loadDefaultsParams(true),'-1');
			if ( $db->loadResult() == 0 ) {
				$extension->store();
			}
		}

		$vars = get_class_vars('XmapSitemap');
		$fields = '';
		foreach($vars as $name => $value) {
			if ($name[0]!=='_') {
				if ($name == 'id') {
					$fields[] = 'id INT NOT NULL PRIMARY KEY AUTO_INCREMENT';
				} else {
					switch( gettype( $value ) ) {
					case 'integer':
							$fields[] = "`$name` INTEGER NULL";
							break;
					case 'string':
							if( $name == 'menus' || $name == 'excluded_items')
									$fields[] = "`$name` TEXT NULL";
							else
									$fields[] = "`$name` VARCHAR(255) NULL";
							break;
					}
				}
			}
		}
		$query = "CREATE TABLE IF NOT EXISTS #__xmap_sitemap (". implode(', ', $fields) .")";
		$db->setQuery( $query );
		if( $db->query() === FALSE ) {
				echo _XMAP_ERR_NO_CREATE . "<br />\n";
				echo stripslashes($db->getErrorMsg());
				return false;
		}

		$query = "CREATE TABLE IF NOT EXISTS #__xmap_items ( uid varchar(100) not null, itemid int not null, view varchar(10) not null, sitemap_id int not null, properties varchar(300), primary key (uid,itemid,view,sitemap_id),index (uid,itemid),index (view));";
		$db->setQuery( $query );
		if( $db->query() === FALSE ) {
				echo _XMAP_ERR_NO_CREATE . "<br />\n";
				echo stripslashes($db->getErrorMsg());
				return false;
		}

		echo _XMAP_MSG_SET_DB_CREATED . "<br />\n";


		// Insert default Settings

		$query = "SELECT COUNT(*) from `#__xmap_sitemap`";
		$db->setQuery($query);
		if ( $db->loadResult() == 0 ) {
			$sitemap = new XmapSitemap($db);
			$sitemap->save();
		}

		$query = "SELECT COUNT(*) from `#__xmap`";
		$db->setQuery($query);
		if ( $db->loadResult() == 0 ) {
			$fields = array();
			$vars = get_class_vars('XmapConfig');
			foreach($vars as $name => $value) {
				if ($name == 'sitemap_default') {
					$value = $sitemap->id;
				}
				$query = "INSERT INTO #__xmap (`name`,`value`) values ('$name','$value')";
				$db->setQuery( $query );
				if( $db->query() === FALSE ) {
					echo _XMAP_ERR_NO_DEFAULT_SET . "<br />\n";
					echo stripslashes($db->getErrorMsg());
					return false;
				}
			}
		}

		echo _XMAP_MSG_SET_DEF_INSERT . "<br />\n";
		return true;
	}

	/** Remove the settings table */
	function remove() {
		$db = & JFactory::getDBO();
		$querys[] = "DROP TABLE IF EXISTS #__xmap";
		$querys[] = "DROP TABLE IF EXISTS #__xmap_sitemap";
		$querys[] = "DROP TABLE IF EXISTS #__xmap_ext";
		$querys[] = "DROP TABLE IF EXISTS #__xmap_items";
		foreach ($querys as $query) {
			$db->setQuery( $query );
			if( $db->query() === FALSE ) {
				echo _XMAP_ERR_NO_DROP_DB . "<br />\n";
				echo stripslashes($db->getErrorMsg());
				return false;
			}
		}
		echo  "Xmap's tables have been saved!<br />\n";

	}

	/** Load settings from the database into this instance */
	function load() {
		$db = & JFactory::getDBO();

		$query = "SELECT * FROM #__xmap";
		$db->setQuery( $query );
		if ($result = $db->loadAssocList('name') ) {
			foreach ($result as $name => $row) {
				$this->$name = $row['value'];
			}
			return true;				// defaults are still set, though
		}
		$this->_sitemaps = array();
		return false;
	}

	/** Save current settings to the database */
	function save() {
		$db = & JFactory::getDBO();

		$vars = get_object_vars( $this );
		$query = "DELETE FROM `#__xmap`";
		$db->setQuery( $query );
		$db->query();
		foreach($vars as $name => $value) {
			if ( substr($name,0,1) !== '_' ) {
				$query = "INSERT INTO #__xmap (`name`,`value`) values ('$name','$value')";
				$db->setQuery( $query );
				if ( $db->query() === FALSE ) {
					return false;
				}
			}
		}

		return true;
	}

	/** Debug output of current settings */
	function dump() {
		$vars = get_object_vars( $this );
		echo '<pre style="text-align:left">';
		foreach( $vars as $name => $value ) {
			echo $name.': '.$value."\n";
		}
		echo '</pre>';
	}

}
