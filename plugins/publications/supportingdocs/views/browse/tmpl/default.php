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

$database  = JFactory::getDBO();
$useBlocks = $this->config->get('curation', 0);

// Add stylesheet
$document = JFactory::getDocument();
$document->addStyleSheet('plugins' . DS . 'publications' . DS
	. 'supportingdocs' . DS . 'assets' . DS . 'css' . DS . 'supportingdocs.css');

?>
<div class="supportingdocs">
<h3>
	<a name="supportingdocs"></a>
	<?php echo JText::_('PLG_PUBLICATION_SUPPORTINGDOCS'); ?>
</h3>

<?php
if ($useBlocks)
{
	// Get primary elements
	$elements = $this->publication->_curationModel->getElements(2);

	// Get attachment type model
	$attModel = new PublicationsModelAttachments($database);

	$attachments = $this->publication->_attachments;
	$attachments = isset($attachments[2])
				 ? $attachments[2] : NULL;

	if ($elements && $attachments)
	{
		foreach ($elements as $element)
		{
			// Draw button
			$launcher = $attModel->drawLauncher(
				$element->manifest->params->type,
				$this->publication,
				$element,
				$elements,
				$this->authorized
			);

			echo $launcher;
		}
	}
	else
	{
		?>
		<p class="noresults"><?php echo JText::_('PLG_PUBLICATION_SUPPORTINGDOCS_NONE_FOUND'); ?></p>
<?php	}
}
elseif ($this->docs) {
	$dls = '';
	$dls .= '<ul>'."\n";
	foreach ($this->docs as $child) {

		$child->title = $child->title ? stripslashes($child->title) : '';
		$child->title = str_replace( '"', '&quot;', $child->title );
		$child->title = str_replace( '&amp;', '&', $child->title );
		$child->title = str_replace( '&', '&amp;', $child->title );
		$child->title = str_replace( '&amp;quot;', '&quot;', $child->title );

		$params = new JParameter( $child->params );

		switch ( $child->type )
		{
			case 'file':
			default:

				$mt = new \Hubzero\Content\Mimetypes();

				$mimetype 	= $mt->getMimeType($child->path);
				$parts		= explode('/', $mimetype);
				$type 		= array_shift($parts);
				$type 		= strtolower($type);

				// Some files can be viewed inline
				if ($type == 'image' || $type == 'video' || $type == 'audio')
				{
					$default_type = 'inlineview';
				}
				else
				{
					$default_type = 'download';
				}
				break;

			case 'link':
				$default_type = 'external';
				break;
		}
		$serveas = $params->get('serveas', $default_type);

		// Get ext
		$parts  = explode('.', $child->path);
		$ext 	= array_pop($parts);
		$ext	= strtolower($ext);

		// Get size
		$fpath = $this->path . DS . $child->path;
		$size = ($serveas == 'download' && $child->type == 'file' && file_exists( $fpath )) ? filesize( $fpath ) : '';
		$size = $size ? PublicationsHtml::formatsize($size) : '';

		// Get file icon
		$icon  = ($child->type == 'file')
		? '<img src="' . ProjectsHtml::getFileIcon($ext) . '" alt="'.$ext.'" /> '
		: '<span class="'.$child->type.'"></span> ';

		$url = JRoute::_('index.php?option=com_publications&id='
			 . $this->publication->id . '&task=serve&v=' . $this->version . '&a='
			 . $child->id);

		$extra = '';

		switch ( $serveas )
		{
			case 'download':
			default:
				break;
			case 'external':
				$extra = ' rel="external"';
				break;
			case 'inlineview':
				$extra = ' class="play"';
				$url  .= '?render=inline';
				break;
		}

		$title = $params->get('title', $child->title);
		$title = $title ? $title : basename($child->path);

		$dls .= "\t".'<li><a href="'.$url.'"' . $extra .'>'.$icon.$title.'</a> ';
		$dls .= $ext ? ' <span class="ext">('.strtoupper($ext) : '';
		$dls .= $size ? ' | '.$size : '';
		$dls .= $ext ? ')</span>' : '';
		$dls .= '</li>'."\n";
	}
	$dls .= '</ul>'."\n";
	echo $dls;
?>
<?php } else { ?>
	<p class="noresults"><?php echo JText::_('PLG_PUBLICATION_SUPPORTINGDOCS_NONE_FOUND'); ?></p>
<?php } ?>
</div><!-- / .supportingdocs -->