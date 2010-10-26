<?php

class JCEGroupsHelper {
	function getUserGroupFromId( $id ){
		$db	=& JFactory::getDBO();
		
		$query = 'SELECT *'
		. ' FROM #__jce_groups'
		. ' WHERE '.$id.' IN (users)'
		;			
		$db->setQuery( $query );
		$groups = $db->loadObjectList();
		return $groups[0];
	}
	function getUserGroupFromType( $type ){
		$db	=& JFactory::getDBO();
		
		if(!is_int($type)){
			$query = 'SELECT id'
			. ' FROM #__core_acl_aro_groups'
			. ' WHERE name = "'.$type.'"'
			;				
			$db->setQuery( $query );
			$id = $db->loadResult();
		}
		
		$query = 'SELECT *'
		. ' FROM #__jce_groups'
		. ' WHERE '.$type.' IN (types)'
		;			
		$db->setQuery( $query );
		$groups = $db->loadObjectList();
		return $groups[0];
	}
	function getRowArray($rows){
		$out = array();
		$rows = explode(';', $rows);
		$i = 1;
		foreach($rows as $row){
			$out[$i] = $row;
			$i++;
		}
		return $out;
	}
	function getExtensions($plugin){
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$path 		= JCE_PLUGINS.DS.$plugin.DS.'extensions';
		$extensions = array();

		if (JFolder::exists($path)) {
			$types = JFolder::folders($path);
			
			foreach ($types as $type) {
				$files = JFolder::files($path.DS.$type, '\.xml$');
				foreach ($files as $file) {
					$object = new StdClass();
					$object->folder = $type;
					$name = JFile::stripExt($file);
					if (JFile::exists($path.DS.$type.DS.$name.'.php')) {
						$object->extension 	= $name;
						// Load xml file
						$xml =& JFactory::getXMLParser('Simple');
						if ($xml->loadFile($path.DS.$type.DS.$file)) {
							$root =& $xml->document;	
							$name = $root->getElementByPath('name');
							$object->name = $name->data();
						} else {
							$object->name = $name;
						}
						$extensions[] = $object;
					}
				}				
			}
		}
		return $extensions;
	}
}