<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
	.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php'))
{
	require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
		.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php');
}
else
{
	return false;
}

/*
$plugin = JPluginHelper::getPlugin( 'projects', 'notes' );
$params = new JParameter( $plugin->params );

if (!$params->get('enable_publinks'))
{
	return false;
}
*/

$database 	= JFactory::getDBO();
$objSt 		= new ProjectPubStamp( $database );
$page		= new WikiTablePage( $database );

// Get listed public notes
$items = $objSt->getPubList($this->project->id, 'notes');

$link = JRoute::_('index.php?option=com_projects' . a . 'task=get') . '/?s=';

if ($items) {
?>
<div class="public-list-header">
	<h3><?php echo ucfirst(JText::_('COM_PROJECTS_PUBLIC')); ?> <?php echo JText::_('COM_PROJECTS_NOTES'); ?></h3>
</div>
<div class="public-list-wrap">
	<ul>
		<?php foreach ($items as $item) {
			$ref = json_decode($item->reference);

			if (isset($ref->pageid) && $page->loadById( $ref->pageid ))
			{
		?>
		<li class="notes"><a href="<?php echo $link . $item->stamp; ?>"><?php echo $page->title; ?></li>
		<?php }
		} ?>
	</ul>
</div>
<?php } ?>
