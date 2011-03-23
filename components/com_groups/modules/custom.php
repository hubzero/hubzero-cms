<?php

defined('_JEXEC') or die( 'Restricted access' );

Class CustomModule
{
	
	function __construct( $group )
	{
		//group object
		$this->group = $group;
		
	}
	
	//-----
	
	function onManageModules()
	{
		$mod = array(
			'name' => 'custom',
			'title' => 'Custom Content',
			'description' => 'The custom module allows for group manager to create a custom content block using wiki syntax.',
			'input_title' => "Custom Content: <span class=\"optional\">Optional</span>",
			'input' => "<textarea name=\"module[content]\" rows=\"15\">{{VALUE}}</textarea>"
		);
		
		return $mod;
	}
	
	//-----
	
	function render()
	{
		//option
		$option = 'com_groups';
		
		//get the config
		$config = JComponentHelper::getParams( $option );
		
		//var to hold content being returned
		$content  = '';
		
		//html wrapper for module 
		$content .= "<div class=\"group_module_custom\">";
		
		$wikiconfig = array(
			'option'   => $option,
			'scope'    => $this->group->get('cn').DS.'wiki',
			'pagename' => 'group',
			'pageid'   => $this->group->get('gidNumber'),
			'filepath' => $config->get('uploadpath'),
			'domain'   => $this->group->get('cn') 
		);
		
		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();
		
		//parse the wiki content
		$content .= $p->parse( "\n".stripslashes($this->content), $wikiconfig );
		
		//close wrapper
		$content .= "</div>";
		
		//return the content
		return $content;
	}
	
	//-----
}

?>