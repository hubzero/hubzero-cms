<?php

defined('_JEXEC') or die( 'Restricted access' );

Class InformationModule
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
			'name' => 'information',
			'title' => 'General Group Informaiton',
			'description' => 'Information block shows basic details about the group, including the managers, number of members, etc',
			'input_title' => '',
			'input' => 'There is nothing you can edit for this group module. Clicking the update button below or the back above will take you back to the manage pages dashboard.'
		);
		
		return $mod;
	}
	
	//-----
	
	function render()
	{
		//var to hold content being returned
		$content  = '';
		
		//get the group object
		$group = Hubzero_Group::getInstance( $this->group->get('gidNumber') );
		
		// Get the group tags
		$database =& JFactory::getDBO();
		$gt = new GroupsTags( $database );
		$tags = $gt->get_tag_cloud(0,0,$group->get('gidNumber'));
		if (!$tags) {
			$tags = JText::_('None');
		}

		// Get the managers
		$managers = $group->get('managers');
		$m = array();
		if ($managers) {
			foreach ($managers as $manager) 
			{
				$person =& JUser::getInstance($manager);
				$m[] = '<a href="'.JRoute::_('index.php?option=com_members&id='.$manager).'" rel="member">'.stripslashes($person->get('name')).'</a>';
			}
		}
		$m = implode(', ', $m);

		// Determine the join policy
		switch ($group->get('join_policy')) 
		{
			case 3: $policy = JText::_('Closed');      break;
			case 2: $policy = JText::_('Invite Only'); break;
			case 1: $policy = JText::_('Restricted');  break;
			case 0:
			default: $policy = JText::_('Open'); break;
		}

		// Determine the privacy
		switch ($group->get('privacy')) 
		{
			case 1: $privacy = JText::_('Hidden'); break;
			case 0:
			default: $privacy = JText::_('Visible'); break;
		}

		// Get the group creation date
		$gl = new XGroupLog( $database );
		$gl->getLog( $group->get('gidNumber'), 'first' );
		
		
		$information  = '<div class="metadata">'."\n";
		$information .= '<table summary="Meatadata about this group">'."\n";
		$information .= '<tbody>'."\n";
			$information .= '<tr>'."\n";
			$information .= '<th>Managers:</th>'."\n";
			$information .= '<td>'.$m.'</td>'."\n";
			$information .= '</tr>'."\n";
			$information .= '<tr>'."\n";
			$information .= '<th>Members:</th>'."\n";
			$information .= '<td>'.count($group->get('members')).'</td>'."\n";
			$information .= '</tr>'."\n";
			$information .= '<tr>'."\n";
			$information .= '<th>Discoverability:</th>'."\n";
			$information .= '<td>'.$privacy.'</td>'."\n";
			$information .= '</tr>'."\n";
			$information .= '<tr>'."\n";
			$information .= '<th>Policy:</th>'."\n";
			$information .= '<td>'.$policy.'</td>'."\n";
			$information .= '</tr>'."\n";
			$information .= '<tr>'."\n";
			$information .= '<th>Created:</th>'."\n";
			$information .= '<td>'.JHTML::_('date', $gl->timestamp, '%d %b. %Y').'</td>'."\n";
			$information .= '</tr>'."\n";
			$information .= '<tr>'."\n";
			$information .= '<th>Tags:</th>'."\n";
			$information .= '<td>'.$tags.'</td>'."\n";
			$information .= '</tr>'."\n";
		$information .= '</tbody>'."\n";
		$information .= '</table>'."\n";
		$information .= '</div>'."\n";
		
		$content = $information;
		
		//return content
		return $content;
	}
	
	
}
?>