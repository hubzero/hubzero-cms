<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
?>

<div id="abox-content">
<?php
/*
if (!is_file(JPATH_ROOT . $this->url))
{
	echo '<p class="error">'.JText::_('COM_PUBLICATIONS_FILE_NOT_FOUND').'</p>'."\n";
	return;
}
*/

$oWidth = '780';
$oHeight= '480';

// Get some attributes
$attribs 	= new JParameter( $this->primary->attribs );
$width  	= $attribs->get( 'width', '' );
$height 	= $attribs->get( 'height', '' );
$attributes = $attribs->get('attributes', '');

$width 		= (intval($width) > 0) ? $width : $oWidth;
$height 	= (intval($height) > 0) ? $height : $oHeight;

// Get mime type
$mTypeParts = explode(';', $this->mimetype);						
$cType 		= $mTypeParts[0];

if ($attributes) 
{
	$a = explode(',', $attributes);
	$bits = array();
	if ($a && is_array($a)) 
	{
		foreach ($a as $b) 
		{
			if (strstr($b, ':')) 
			{
				$b = explode(':', $b);
				$bits[] = trim($b[0]) . '="' . trim($b[1]) . '"';
			}
		}
	}
	$attributes = implode(' ', $bits);
}

// Formats that can be previewed via Google viewer
$docs 	= array('pdf', 'doc', 'docx', 'xls', 'xlsx', 
	'ppt', 'pptx', 'pages', 'ai', 
	'psd', 'tiff', 'dxf', 'eps', 'ps', 'ttf', 'xps', 'svg'
);

$html5video = array("mp4","m4v","webm","ogv");

$token = '';
$juser = JFactory::getUser();
if (!$juser->get('guest'))
{
	$session = JFactory::getSession();

	$session_id = $session->getId();
	
	jimport('joomla.utilities.simplecrypt');
	$crypter = new JSimpleCrypt();
	$token = base64_encode($crypter->encrypt($session_id));
}

$juri = JURI::getInstance();
$downloadUrl = JRoute::_('index.php?option=com_publications&id=' . $this->publication->id . '&task=serve&aid=' 
	  . $this->aid . '&render=download&token=' . $token);

$viewUrl = JRoute::_('index.php?option=com_publications&id=' . $this->publication->id . '&task=serve&aid=' 
	  . $this->aid . '&render=download&disposition=inline&token=' . $token);
	
?>
<div class="sample"><p><?php echo JText::_('COM_PUBLICATIONS_PUBLICATION') . ': <strong>' . $this->publication->title . '</strong>'; ?> <?php if ($this->primary->role != 1) { echo '&nbsp;&nbsp; Supporting Doc: <strong>' . $this->primary->path . '</strong>'; } ?></p></div>

<?php
// Image?
if ($this->type == 'image') 
{
	echo '<img ' . $attributes . ' src="' . $this->url . '" alt="Image" />'."\n";
}
elseif (in_array(strtolower($this->ext), $docs) && $this->googleView) 
{	
	// View via Google
	echo  '<iframe src="https://docs.google.com/viewer?url=' . urlencode($juri->base() 
			. $downloadUrl) . '&amp;embedded=true#:0.page.0" width="100%" height="500" name="file_resource" frameborder="0" bgcolor="white"></iframe>'
			."\n";
}
else
		// View in html5-browser 
		{ ?>
			<p class="direct-download">Publication doesn't load in your browser or shows partial file? <a href="<?php echo $juri->base() . $downloadUrl; ?>">Download file</a>
			</p>
		<?php 
	if (strtolower($this->ext) == 'wmv') { ?>
	<object type="video/x-ms-wmv" 
		  data="<?php echo $this->url; ?>" width="100%" height="<?php echo $height; ?>">
		  <param name="src" value="<?php echo $this->url; ?>" />
		  <param name="autostart" value="true" />
		  <param name="controller" value="true" />
	</object>
<?php } else { 
	 ?>
	<div class="video-container">
		<object width="100%" height="<?php echo $height; ?>">
		<param name="allowfullscreen" value="true" />
		<param name="allowscriptaccess" value="always" />
		<param name="movie" value="<?php echo $this->url; ?>" />
		<param name="scale" value="aspect" />
		<embed src="<?php echo $this->url; ?>" scale="aspect"></embed>
		</object>
	</div>
<?php }
} ?>
</div>
