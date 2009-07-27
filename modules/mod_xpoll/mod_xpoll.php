<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class modXPoll
{
	private $attributes = array();

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//-----------
	
	public function display() 
	{
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_xpoll'.DS.'xpoll.class.php' );
		
		$database =& JFactory::getDBO();
		
		$params =& $this->params;
		$formid = $params->get( 'formid' );

		// Load the latest poll
		$poll = new XPollPoll( $database );
		$poll->getLatestPoll();

		// Did we get a result from the database?
		if ($poll->id && $poll->title) {
			$xpdata = new XPollData( $database );
			$options = $xpdata->getPollOptions( $poll->id, false );
			
			// Push the module CSS to the template
			ximport('xdocument');
			XDocument::addModuleStyleSheet('mod_xpoll');
			
			$this->html( $poll, $options, $formid );
		}
	}
	
	//-----------

	protected function html( &$poll, &$options, $formid ) 
	{
		$tabcnt = 0;
		?>
		<form id="<?php echo ($formid) ? $formid : 'xpoll'.rand(); ?>" method="post" action="<?php echo JRoute::_('index.php?option=com_xpoll'); ?>">
			<fieldset>
				<h4><?php echo $poll->title; ?></h4>
				<ul class="poll">
<?php
		for ($i=0, $n=count( $options ); $i < $n; $i++) 
		{ 
?>
				 <li>
					<input type="radio" name="voteid" id="voteid<?php echo $options[$i]->id;?>" value="<?php echo $options[$i]->id;?>" alt="<?php echo $options[$i]->id;?>" />
					<label for="voteid<?php echo $options[$i]->id;?>"><?php echo stripslashes($options[$i]->text); ?></label>
				 </li>
<?php
			if ($tabcnt == 1) {
				$tabcnt = 0;
			} else {
				$tabcnt++;
			}
		}
?>
				</ul>
				<p><input type="submit" name="task_button" value="<?php echo JText::_('BUTTON_VOTE'); ?>" />&nbsp;&nbsp;
				<a href="<?php echo JRoute::_("index.php?option=com_xpoll&amp;task=view&amp;id=$poll->id"); ?>"><?php echo JText::_('BUTTON_RESULTS'); ?></a></p>
		
				<input type="hidden" name="id" value="<?php echo $poll->id;?>" />
				<input type="hidden" name="task" value="vote" />
			</fieldset>
		</form>
		<?php
	}
}

//-------------------------------------------------------------

$modxpoll = new modXPoll();
$modxpoll->params = $params;

require( JModuleHelper::getLayoutPath('mod_xpoll') );
?>