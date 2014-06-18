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

$database 	= JFactory::getDBO();
$objSt 		= new ProjectPubStamp( $database );

// Get listed public files
$items = $objSt->getPubList($this->project->id, 'files');

$link = JRoute::_('index.php?option=com_projects' . a . 'task=get') . '/?s=';

// Load component configs
$config = JComponentHelper::getParams('com_projects');

// Get project path
$path  = ProjectsHelper::getProjectPath($this->project->alias,
		$config->get('webpath'), 1);
$prefix = $config->get('offroot', 0) ? '' : JPATH_ROOT;

if ($items) {
?>
<div class="public-list-header">
	<h3><?php echo ucfirst(JText::_('COM_PROJECTS_PUBLIC')); ?> <?php echo JText::_('COM_PROJECTS_FILES'); ?></h3>
</div>
<div class="public-list-wrap">
	<ul>
		<?php foreach ($items as $item) {
			$ref = json_decode($item->reference);

			$serve = $prefix . $path . DS . $ref->file;

			// Get file extention
			$ext = explode('.', $ref->file);
			$ext = count($ext) > 1 ? end($ext) : '';

			if (is_file($serve))
			{
		?>
		<li><a href="<?php echo $link . $item->stamp; ?>"><img src="<?php echo ProjectsHtml::getFileIcon($ext); ?>" alt="<?php echo $ext; ?>" /> <?php echo basename($ref->file); ?></li>
		<?php }
		} ?>
	</ul>
</div>
<?php } ?>
