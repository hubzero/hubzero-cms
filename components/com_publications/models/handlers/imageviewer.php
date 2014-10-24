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

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Image Viewer Handler
 */
class PublicationsModelHandlerImageViewer extends PublicationsModelHandler
{
	/**
	* Handler type name
	*
	* @var		string
	*/
	protected	$_name 		= 'imageviewer';

	/**
	* Configs
	*
	* @var
	*/
	protected	$_config 	= NULL;

	/**
	* Image Helper
	*
	* @var
	*/
	protected	$_imgHelper = NULL;

	/**
	 * Get default params for the handler
	 *
	 * @return  void
	 */
	public function getConfig()
	{
		// Defaults
		$configs = array(
			'name' 			=> 'imageviewer',
			'label' 		=> 'Image Gallery',
			'title' 		=> 'Viewer for image files',
			'about'			=> 'Selected images will be viewed together in a slideshow',
			'params'	=> array(
				'allowed_ext' 		=> array('gif', 'jpg', 'png', 'bmp', 'jpeg'),
				'required_ext' 		=> array(),
				'min_allowed' 		=> 1,
				'max_allowed' 		=> 1000,
				'thumbSuffix' 		=> '_tn',
				'thumbFormat' 		=> 'png',
				'thumbWidth' 		=> '100',
				'thumbHeight' 		=> '60',
				'masterWidth' 		=> '600',
				'masterHeight' 		=> '400',
				'defaultThumb'		=> '/components/com_publications/assets/img/resource_thumb.gif'
			)
		);

		// Load config from db
		$obj = new PublicationHanlder($this->_parent->_db);
		$savedConfig = $obj->getConfig($this->_name);

		if ($savedConfig)
		{
			foreach ($configs as $configName => $configValue)
			{
				if ($configName == 'params')
				{
					foreach ($configValue as $paramName => $paramValue)
					{
						$configs['params'][$paramName] = isset($savedConfig['params'][$paramName]) && $savedConfig['params'][$paramName] ? $savedConfig['params'][$paramName] : $paramValue;
					}
				}
			}
		}

		$this->_config = json_decode(json_encode($configs), FALSE);

		return $this->_config;
	}

	/**
	 * Clean-up related files
	 *
	 * @return  void
	 */
	public function cleanup( $path )
	{
		// Make sure we got config
		if (!$this->_config)
		{
			$this->getConfig();
		}

		// Get image helper
		if (!$this->_imgHelper)
		{
			include_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects'
				. DS . 'helpers' . DS . 'imghandler.php' );
			$this->_imgHelper = new ProjectsImgHandler();
		}

		$thumbName = $this->_imgHelper->createThumbName(
			basename($path),
			$this->_config->params->thumbSuffix,
			$this->_config->params->thumbFormat
		);
		$thumbPath = dirname($path) . DS . $thumbName;

		if (is_file($thumbPath))
		{
			JFile::delete($thumbPath);
		}

		return true;
	}

	/**
	 * Make image default for publication
	 *
	 * @return  void
	 */
	public function makeDefault( $row, $pub, $configs)
	{
		// Make sure we got config
		if (!$this->_config)
		{
			$this->getConfig();
		}

		// TBD - to come from component configs
		$defaultMasterName  = 'master.png';
		$defaultThumbName 	= 'thumb.gif';

		// Get image helper
		if (!$this->_imgHelper)
		{
			include_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects'
				. DS . 'helpers' . DS . 'imghandler.php' );
			$this->_imgHelper = new ProjectsImgHandler();
		}

		$path = $this->getFilePath($row->path, $row->id, $configs, $row->params);

		// No file found
		if (!is_file($path))
		{
			return false;
		}

		// Check if image
		if (!getimagesize($path))
		{
			return false;
		}

		$copyToThumb  = $configs->pubBase . DS . $defaultThumbName;
		$copyToMaster = $configs->pubBase . DS . $defaultMasterName;

		$thumbName = $this->_imgHelper->createThumbName(
			basename($path),
			$this->_config->params->thumbSuffix,
			$this->_config->params->thumbFormat
		);
		$thumbPath = dirname($path) . DS . $thumbName;

		// Copy to master
		if (is_file($path))
		{
			JFile::copy($path, $copyToMaster);

			// Create/update thumb
			JFile::copy($path, $copyToThumb);
			$this->_imgHelper->set('image', basename($copyToThumb));
			$this->_imgHelper->set('overwrite', true);
			$this->_imgHelper->set('path', $configs->pubBase . DS );
			$this->_imgHelper->set('maxWidth', 100);
			$this->_imgHelper->set('maxHeight', 100);
			$this->_imgHelper->set('cropratio', '1:1');
			$this->_imgHelper->process();
		}
		else
		{
			return false;
		}

		// Get current default
		$currentDefault = new PublicationAttachment( $this->_parent->_db );
		$currentDefault->getDefault($row->publication_version_id);

		// Unmark as default
		if ($currentDefault->id)
		{
			$currentDefault->saveParam($currentDefault, 'pubThumb', '');
		}

		// Mark this image as default
		$currentDefault->saveParam($row, 'pubThumb', '1');

		return true;
	}

	/**
	 * Show attachments in an image band (gallery)
	 *
	 * @return  void
	 */
	public function showImageBand($pub)
	{
		// Get element manifest to deliver content as intended
		$elements = $pub->_curationModel->getElements(3);

		if (empty($elements))
		{
			return false;
		}

		// Show first element
		$element = $elements[0];

		$manifest 		= $element->manifest;
		$params   		= $manifest->params->typeParams;
		$dirHierarchy 	= isset($params->dirHierarchy) ? $params->dirHierarchy : 1;

		// Get files directory
		$directory = isset($params->directory) && $params->directory
							? $params->directory : $pub->secret;
		$pubPath = $pub->_helpers->pubHelper->buildPath($pub->id, $pub->version_id, '', $directory, 0);

		$configs 		= new stdClass;
		$configs->dirHierarchy = $dirHierarchy;
		$configs->pubPath = $pubPath;

		// Do we have attachments?
		$attachments = isset($pub->_attachments['elements'][$element->id])
					? $pub->_attachments['elements'][$element->id] : NULL;

		if (!$attachments)
		{
			return false;
		}

		// Get image helper
		if (!$this->_imgHelper)
		{
			include_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects'
				. DS . 'helpers' . DS . 'imghandler.php' );
			$this->_imgHelper = new ProjectsImgHandler();
		}

		$html 	= '';
		$els 	= '';
		$i 		= 0;
		$k 		= 0;
		$g 		= 0;

		$i = 0;

		$els .=  '<div class="showcase-pane">'."\n";
		foreach ($attachments as $attach)
		{
			$fpath = $this->getFilePath($attach->path, $attach->id, $configs, $attach->params);

			$thumbName = $this->_imgHelper->createThumbName(
				basename($fpath),
				$this->_config->params->thumbSuffix,
				$this->_config->params->thumbFormat
			);
			$thumbPath = dirname($fpath) . DS . $thumbName;

			if (is_file(JPATH_ROOT . DS . $fpath) && is_file(JPATH_ROOT . DS . $thumbPath))
			{
				// Get extentsion
				$ext = explode('.', basename($fpath));
				$ext = strtolower(end($ext));

				$title = $attach->title ? $attach->title : basename($attach->path);
				if ($ext == 'swf' || $ext == 'mov')
				{
					$g++;
					$els .= ' <a class="video"  href="' . $fpath . '" title="' . $title . '">';
					$els .= '<img src="' . $thumbPath . '" alt="' . $title . '" /></a>';
				}
				else
				{
					$k++;
					$els .= ' <a rel="lightbox" href="' . $fpath . '" title="' . $title . '">';
					$els .= '<img src="' . $thumbPath . '" alt="' . $title . '" class="thumbima" /></a>';
				}
				$i++;
			}
		}
		$els .=  '</div>'."\n";

		if ($i > 0)
		{
			$html .= '<div id="showcase">'."\n" ;
			$html .= '<div id="showcase-prev" ></div>'."\n";
			$html .= '  <div id="showcase-window">'."\n";
			$html .= $els;
			$html .= '  </div>'."\n";
			$html .= '  <div id="showcase-next" ></div>'."\n";
			$html .= '</div>'."\n";
		}

		return $html;

	}

	/**
	 * Side controls for handler
	 *
	 * @return  void
	 */
	public function drawControls($pub, $elementid, $attachments)
	{
		// Make sure we got config
		if (!$this->_config)
		{
			$this->getConfig();
		}

		$html = '<div class="' . $this->_name . '">';
		$html.= '<h5>' . $this->_config->label . '</h5>';
		$html.= '<p>' . $this->_config->title . '</p>';
		$html.= '<p class="hint">' . $this->_config->about . '</p>';

		$html.= '</div>';

		return $html;
	}

	/**
	 * Side controls for handler
	 *
	 * @return  void
	 */
	public function drawSelectedHandler($pub, $elementid, $attachments)
	{
		// Make sure we got config
		if (!$this->_config)
		{
			$this->getConfig();
		}

		$html = '<div class="handler-' . $this->_name . '">';
		$html.= '<h3>' . JText::_('Presentation') . ': ' . $this->_config->label . '</h3>';
		$html.= '<p>' . $this->_config->about . '</p>';
		$html.= '</div>';

		return $html;
	}

	/**
	 * Draw list of included files
	 *
	 * @return  void
	 */
	public function drawList($attachments, $attConfigs, $pub, $authorized )
	{
		if (!$attachments)
		{
			return false;
		}

		// Make sure we got config
		if (!$this->_config)
		{
			$this->getConfig();
		}

		// Get image helper
		if (!$this->_imgHelper)
		{
			include_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects'
				. DS . 'helpers' . DS . 'imghandler.php' );
			$this->_imgHelper = new ProjectsImgHandler();
		}

		$path = str_replace(JPATH_ROOT, '', $attConfigs->pubPath);

		$html = '';

		foreach ($attachments as $attach)
		{
			$fpath = $this->getFilePath($attach->path, $attach->id, $attConfigs, $attach->params);
			$fpath = str_replace(JPATH_ROOT, '', $fpath);

			$thumbName = $this->_imgHelper->createThumbName(
				basename($fpath),
				$this->_config->params->thumbSuffix,
				$this->_config->params->thumbFormat
			);
			$thumbPath = dirname($fpath) . DS . $thumbName;
			$thumbPath = str_replace(JPATH_ROOT, '', $thumbPath);

			$title 		= $attach->title ? $attach->title : $attConfigs->title;
			$title 		= $title ? $title : basename($attach->path);

			$params = new JParameter( $attach->params );

			$html .= '<li>';
			$html .= ' <a rel="lightbox" href="' . $fpath . '">';
			$html .= '<span class="item-image';
			$html .= $params->get('pubThumb', NULL) && $authorized == 'administrator' ? ' starred' : '';
			$html .= '"><img src="' . $thumbPath . '" alt="' . $title . '" class="thumbima" /></span>';
			$html .= '<span class="item-title">' . $title . '<span class="details">' . $attach->path . '</span></span>';
			$html .= '</a>';
			$html .= '<span class="clear"></span>';
			$html .= '</li>';
		}

		return $html;
	}

	/**
	 * Make thumb
	 *
	 * @return  void
	 */
	public function makeThumbnail( $row, $pub, $configs)
	{
		// Make sure we got config
		if (!$this->_config)
		{
			$this->getConfig();
		}

		// Get image helper
		if (!$this->_imgHelper)
		{
			include_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects'
				. DS . 'helpers' . DS . 'imghandler.php' );
			$this->_imgHelper = new ProjectsImgHandler();
		}

		$fpath = $this->getFilePath($row->path, $row->id, $configs, $row->params);

		$thumbName = $this->_imgHelper->createThumbName(
			basename($fpath),
			$this->_config->params->thumbSuffix,
			$this->_config->params->thumbFormat
		);
		$thumbPath = $configs->pubPath . DS . $thumbName;

		// No file found
		if (!is_file($fpath))
		{
			return;
		}

		// Check if image
		if (!getimagesize($fpath))
		{
			return false;
		}

		$md5 = hash_file('sha256', $fpath);

		// Create/update thumb if doesn't exist or file changed
		if (!is_file($thumbPath) || $md5 != $row->content_hash)
		{
			JFile::copy($fpath, $thumbPath);
			$this->_imgHelper->set('image', basename($thumbName));
			$this->_imgHelper->set('overwrite', true);
			$this->_imgHelper->set('path', $configs->pubPath . DS);
			$this->_imgHelper->set('maxWidth', $this->_config->params->thumbWidth);
			$this->_imgHelper->set('maxHeight', $this->_config->params->thumbHeight);
			if (!$this->_imgHelper->process())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Draw attachment
	 *
	 * @return  void
	 */
	public function drawAttachment($data, $params)
	{
		// Make sure we got config
		if (!$this->_config)
		{
			$this->getConfig();
		}

		// Get image helper
		if (!$this->_imgHelper)
		{
			include_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects'
				. DS . 'helpers' . DS . 'imghandler.php' );
			$this->_imgHelper = new ProjectsImgHandler();
		}

		// Metadata file?
		$layout =  ($data->ext == 'csv') ? 'file' : 'image';

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'publications',
				'name'		=>'attachments',
				'layout'	=>$layout
			)
		);
		$view->data    		= $data;
		$view->config  		= $this->_config;
		$view->ih	   		= $this->_imgHelper;
		$view->params 		= $params;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Build file path depending on configs
	 *
	 * @return  string
	 */
	public function getFilePath( $path, $id, $configs = NULL, $params = NULL, $suffix = NULL )
	{
		// Do we transfer file with subdirectories?
		if ($configs->dirHierarchy == 1)
		{
			$fpath = $configs->pubPath . DS . trim($path, DS);
		}
		elseif ($configs->dirHierarchy == 2)
		{
			if (!$suffix && $params)
			{
				// Get file attachment params
				$fParams = new JParameter( $params );
				$suffix  = $fParams->get('suffix');
			}

			// Do not preserve dir hierarchy, but append number for same-name files
			$name 	= $suffix ? ProjectsHtml::fixFileName(basename($path), ' (' . $suffix . ')') : basename($path);
			$fpath  = $configs->pubPath . DS . $name;
		}
		else
		{
			// Attach record number to file name
			$name 	= ProjectsHtml::fixFileName(basename($path), '-' . $id);
			$fpath  = $configs->pubPath . DS . $name;
		}

		return $fpath;
	}
}