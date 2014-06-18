<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css();

$juser = JFactory::getUser();
$database = JFactory::getDBO();

if ($this->model->isTool())
{
	$children = $this->model->children();
}
else
{
	$children = $this->model->children('!standalone');
}
?>
<h3 class="section-header">
	<?php echo JText::_('PLG_RESOURCES_SUPPORTINGDOCS'); ?>
</h3>

<div id="supportingdocs" class="supportingdocs">
	<?php if ($children) { ?>
		<ul>
			<?php
			jimport('joomla.filesystem.file');
			$linkAction = 0;
			$base = $this->model->params->get('uploadpath');
			foreach ($children as $child)
			{
				if ($child->access == 0 || ($child->access == 1 && !$juser->get('guest')))
				{
					$ftype = JFile::getExt($child->path);
					if (substr($child->path, 0, 4) == 'http')
					{
						$ftype = 'html';
					}

					$class = '';
					$action = '';
					if ($child->standalone == 1)
					{
						$liclass = ' class="html"';
						$title = stripslashes($child->title);
					}
					else
					{
						$rt = ResourcesType::getRecordInstance($child->type);
						$tparams = new JRegistry($rt->params);

						$lt = ResourcesType::getRecordInstance($child->logicaltype);
						$ltparams = new JRegistry($lt->params);

						// Check the link action by child's type
						if ($child->logicaltype)
						{
							$rtLinkAction = $ltparams->get('linkAction', 'extension');
						}
						else
						{
							$rtLinkAction = $tparams->get('linkAction', 'extension');
						}

						switch ($rtLinkAction)
						{
							case 'download':
								$class = 'download';
								$linkAction = 3;
							break;

							case 'lightbox':
								$class = 'play';
								$linkAction = 2;
							break;

							case 'newwindow':
								$action = 'rel="external"';
								$linkAction = 1;
							break;

							case 'extension':
							default:
								$linkAction = 0;

								$mediatypes = array('elink','quicktime','presentation','presentation_audio','breeze','quiz','player','video_stream','video','hubpresenter');
								$downtypes = array('thesis','handout','manual','software_download');

								if (in_array($lt->alias, $downtypes))
								{
									$class = 'download';
								}
								elseif (in_array($rt->alias, $mediatypes))
								{
									$mediatypes = array('flash_paper','breeze','32','26');
									if (in_array($child->type, $mediatypes))
									{
										$class = 'play';
									}
								}
								else
								{
									$class = 'download';
								}
							break;
						}

						// Check for any link action overrides on the child itself
						$childParams = new JRegistry($child->params);
						$linkAction = intval($childParams->get('link_action', $linkAction));
						switch ($linkAction)
						{
							case 3:
								$class = 'download';
							break;

							case 2:
								$class = 'play';
							break;

							case 1:
								$action = 'rel="external"';
							break;

							case 0:
							default:
								// Do nothing
							break;
						}

						switch ($rt->alias)
						{
							case 'user_guide':
								$liclass = ' class="guide"';
								break;
							case 'ilink':
								$liclass = ' class="html"';
								break;
							case 'breeze':
								$liclass = ' class="swf"';
								//$class = ' class="play"';
								break;

							case 'hubpresenter':
							 	$liclass = ' class="presentation"';
								$class = 'hubpresenter';
								break;
							default:
								$liclass = ' class="' . strtolower($ftype) . '"';
								break;
						}

						$title = ($child->logicaltitle) ? $child->logicaltitle : stripslashes($child->title);
					}

					$url = ResourcesHtml::processPath($this->option, $child, $this->model->resource->id, $linkAction);

					//$child->title = str_replace('"', '&quot;', $child->title);
					//$child->title = str_replace('&amp;', '&', $child->title);
					//$child->title = str_replace('&', '&amp;', $child->title);
					//$child->title = str_replace('&amp;quot;', '&quot;', $child->title);

					// width & height
					$attribs = new JRegistry($child->attribs);
					$width  = intval($attribs->get('width', 640));
					$height = intval($attribs->get('height', 360));
					if ($width > 0 && $height > 0)
					{
						$class .= ' ' . $width . 'x' . $height;
					}

					// user guide
					if (strtolower($title) !=  preg_replace('/user guide/', '', strtolower($title)))
					{
						$liclass = ' class="guide"';
					}
					?>
					<li<?php echo $liclass; ?>>
						<?php echo ResourcesHtml::getFileAttribs($child->path, $base, 0); ?>
						<a<?php echo ($class) ? ' class="' . $class . '"' : '';?> href="<?php echo $url; ?>" title="<?php echo $this->escape(stripslashes($child->title)); ?>" <?php echo ($action)  ? ' ' . $action : ''; ?>>
							<?php echo $title; ?>
						</a>
					</li>
					<?php
				}
			}
			?>
		</ul>
	<?php } else { ?>
		<p><?php echo JText::_('PLG_RESOURCES_SUPPORTINGDOCS_NONE'); ?></p>
	<?php } ?>
</div><!-- / .supportingdocs -->
