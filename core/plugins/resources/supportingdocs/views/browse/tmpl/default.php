<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

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
	<?php echo Lang::txt('PLG_RESOURCES_SUPPORTINGDOCS'); ?>
</h3>

<div id="supportingdocs" class="supportingdocs">
	<?php if ($children) { ?>
		<ul>
			<?php
			$linkAction = 0;
			$base = $this->model->params->get('uploadpath');
			$i = 0;

			$xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'all');
			$usersgroups = array();
			if (!empty($xgroups))
			{
				foreach ($xgroups as $group)
				{
					if ($group->regconfirmed)
					{
						$usersgroups[] = $group->cn;
					}
				}
			}
			$allowedgroups = $this->model->resource->getGroups();

			foreach ($children as $child)
			{
				if ($child->access == 0 || ($child->access == 1 && !User::isGuest()) || ($child->access == 3 && in_array($this->model->resource->group_owner, $usersgroups)))
				{
					$i++;

					$ftype = Filesystem::extension($child->path);
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
						$rt = \Components\Resources\Tables\Type::getRecordInstance($child->type);
						$tparams = new \Hubzero\Config\Registry($rt->params);

						$lt = \Components\Resources\Tables\Type::getRecordInstance($child->logicaltype);
						$ltparams = new \Hubzero\Config\Registry($lt->params);

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
						$childParams = new \Hubzero\Config\Registry($child->params);
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

					$url = \Components\Resources\Helpers\Html::processPath($this->option, $child, $this->model->resource->id, $linkAction);

					//$child->title = str_replace('"', '&quot;', $child->title);
					//$child->title = str_replace('&amp;', '&', $child->title);
					//$child->title = str_replace('&', '&amp;', $child->title);
					//$child->title = str_replace('&amp;quot;', '&quot;', $child->title);

					// width & height
					if (preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $child->path))
					{
						if (!preg_match("/(?:https?:|mailto:|ftp:|gopher:|news:|file:)/", $child->path))
						{
							/*Component::params('com_tools');
							$base_path = $config->get('uploadpath', '/site/resources');
							if ($base_path)
							{
								$base_path = DS . trim($base_path, DS);
							}*/
							$filename = $child->path;

							// Does the path start with a slash?
							if (substr($filename, 0, 1) != DS)
							{
								$filename = DS . $filename;
								// Does the beginning of the $resource->path match the config path?
								if (substr($filename, 0, strlen($base)) == $base)
								{
									// Yes - this means the full path got saved at some point
								}
								else
								{
									// No - append it
									$filename = $base . $filename;
								}
							}

							// Add PATH_CORE
							$filename = PATH_APP . $filename;

							$width  = 0;
							$height = 0;
							if (file_exists($filename))
							{
								list($width, $height) = getimagesize($filename);
							}
							if ($width > 0 && $height > 0)
							{
								$class .= ' ' . $width . 'x' . $height;
							}
						}
					}
					else
					{
						$attribs = new \Hubzero\Config\Registry($child->attribs);
						$width  = intval($attribs->get('width', 640));
						$height = intval($attribs->get('height', 360));
						if ($width > 0 && $height > 0)
						{
							$class .= ' ' . $width . 'x' . $height;
						}
					}

					// user guide
					if (strtolower($title) !=  preg_replace('/user guide/', '', strtolower($title)))
					{
						$liclass = ' class="guide"';
					}
					?>
					<li<?php echo $liclass; ?>>
						<?php echo \Components\Resources\Helpers\Html::getFileAttribs($child->path, $base, 0); ?>
						<a<?php echo ($class) ? ' class="' . $class . '"' : '';?> href="<?php echo $url; ?>" title="<?php echo $this->escape(stripslashes($child->title)); ?>" <?php echo ($action)  ? ' ' . $action : ''; ?>>
							<?php echo $title; ?>
						</a>
					</li>
					<?php
				}
			}
			if ($i <= 0)
			{
				?>
					<li><?php echo Lang::txt('PLG_RESOURCES_SUPPORTINGDOCS_NONE'); ?></li>
				<?php
			}
			?>
		</ul>
	<?php } else { ?>
		<p><?php echo Lang::txt('PLG_RESOURCES_SUPPORTINGDOCS_NONE'); ?></p>
	<?php } ?>
</div><!-- / .supportingdocs -->
