<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$database =& JFactory::getDBO();
?>
<div class="supportingdocs">
<h3>
	<a name="supportingdocs"></a>
	<?php echo JText::_('PLG_RESOURCES_SUPPORTINGDOCS'); ?> 
</h3>
<?php
switch ($this->resource->type)
{
	case 7:
		$dls = ResourcesHtml::writeChildren( $this->config, $this->option, $database, $this->resource, $this->helper->children, '', '', '', $this->resource->id, 0 );									
	break;
		
	case 4:
		$dls = '';

		$database->setQuery( "SELECT r.path, r.type, r.title, r.access, r.id, r.standalone, a.* FROM #__resources AS r, #__resource_assoc AS a WHERE a.parent_id=".$this->resource->id." AND r.id=a.child_id AND r.access=1 ORDER BY a.ordering" );
		if ($database->query()) {
			$downloads = $database->loadObjectList();
		}
		$base = $this->config->get('uploadpath');
		if ($downloads) {
			$dls .= '<ul>'."\n";
			foreach ($downloads as $download)
			{
				$ftype = '';
				$liclass = '';
				$file_name_arr = explode('.',$download->path);
				$ftype = end($file_name_arr);
				$ftype = (strlen($ftype) > 3) ? substr($ftype, 0, 3): $ftype;

				if ($download->type == 12) {
					$liclass = ' class="html"';
				} else {
					$liclass = ' class="'.$ftype.'"';
				}

				$url = ResourcesHtml::processPath($this->option, $download, $this->resource->id);

				$dls .= "\t".'<li'.$liclass.'><a href="'.$url.'">'.$download->title.'</a> ';
				$dls .= ResourcesHtml::getFileAttribs( $download->path, $base, 0 );
				$dls .= '</li>'."\n";
			}
			$dls .= '</ul>'."\n";
		} else {
			$dls .= '<p>'.JText::_('PLG_RESOURCES_SUPPORTINGDOCS_NONE').'</p>';
		}
	break;
		
	case 8:
		// show no docs
	break;
	
	case 6:
	case 31:
	case 2:					
		$this->helper->getChildren( $this->resource->id, 0, 'no' );
		$dls = ResourcesHtml::writeChildren( $this->config, $this->option, $database, $this->resource, $this->helper->children, $this->live_site, '', '', $this->resource->id, 0 );
	break;
		
	default:
		$dls = ResourcesHtml::writeChildren( $this->config, $this->option, $database, $this->resource, $this->helper->children, '', '', '', $this->resource->id, 0 );
	break;
}
echo $dls;
?>
</div><!-- / .supportingdocs -->
