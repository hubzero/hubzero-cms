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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Publication helper class
 */
class PublicationHelper extends JObject
{
	/**
	 * JDatabase
	 *
	 * @var object
	 */
	var $_db = NULL;

	/**
	 * Publication ID
	 *
	 * @var integer
	 */
	var $_pub_id = NULL;

	/**
	 * Publication version ID
	 *
	 * @var integer
	 */
	var $_version_id = NULL;

	/**
	 * Data
	 *
	 * @var array
	 */
	var $_data = array();

	/**
	 * Constructor
	 *
	 * @param      object  $db      	 JDatabase
	 * @param      integer $versionid    Publication Version ID
	 * @param      integer $pubid    	 Publication ID
	 * @return     void
	 */
	public function __construct( &$db, $versionid = 0, $pubid = 0 )
	{
		$this->_db         = $db;
		$this->_version_id = $versionid;
		$this->_pub_id     = $pubid;

		$this->contributors = null;
		$this->primary_content = null;
		$this->supporting_content = null;
	}

	/**
	 * Set
	 *
	 * @param      string 	$property
	 * @param      string 	$value
	 * @return     mixed
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get
	 *
	 * @param      string 	$property
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Get publication path
	 *
	 * @param      string 	$pid
	 * @param      string 	$vid
	 * @param      string 	$base
	 * @param      string 	$filedir
	 * @param      boolean 	$root
	 * @return     string
	 */
	public static function buildPath( $pid = NULL, $vid = NULL, $base = '', $filedir = '', $root = 0 )
	{
		if ($vid === NULL or $pid === NULL )
		{
			return false;
		}
		if (!$base)
		{
			$pubconfig = JComponentHelper::getParams( 'com_publications' );
			$base = $pubconfig->get('webpath');
		}

		$base = DS . trim($base, DS);

		$pub_dir     =  \Hubzero\Utility\String::pad( $pid );
		$version_dir =  \Hubzero\Utility\String::pad( $vid );
		$path        = $base . DS . $pub_dir . DS . $version_dir;
		$path        = $filedir ? $path . DS . $filedir : $path;
		$path        = $root ? JPATH_ROOT . $path : $path;

		return $path;
	}

	/**
	 * Output tab controls for resource plugins (sub views)
	 *
	 * @param      string $path 	File path
	 * @param      string $error
	 *
	 * @return     boolean False in valid, or error
	 */
	public static function checkValidPath( $path, $error = '' )
	{
		// Ensure we have a path
		if (empty($path))
		{
			$error = JText::_('COM_PUBLICATIONS_FILE_NOT_FOUND');
		}
		if (preg_match("/^\s*http[s]{0,1}:/i", $path))
		{
			$error = JText::_('COM_PUBLICATIONS_BAD_FILE_PATH');
		}
		if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $path))
		{
			$error = JText::_('COM_PUBLICATIONS_BAD_FILE_PATH');
		}
		// Disallow windows drive letter
		if (preg_match("/^\s*[.]:/", $path))
		{
			$error = JText::_('COM_PUBLICATIONS_BAD_FILE_PATH');
		}
		// Disallow \
		if (strpos('\\',$path))
		{
			$error = JText::_('COM_PUBLICATIONS_BAD_FILE_PATH');
		}
		// Disallow ..
		if (strpos('..',$path))
		{
			$error = JText::_('COM_PUBLICATIONS_BAD_FILE_PATH');
		}
		return $error ? $error : false;
	}

	/**
	 * Get project path
	 *
	 * @param      string 	$project_alias
	 * @param      string 	$base
	 * @param      string 	$root
	 * @param      string 	$case
	 * @return     string
	 */
	public static function buildDevPath( $project_alias = '', $base = '', $root = '', $case = 'files'  )
	{
		if (!$project_alias)
		{
			return false;
		}

		$pconfig = JComponentHelper::getParams( 'com_projects' );
		if (!$base)
		{
			$base = $pconfig->get('webpath');
		}
		if (!$root)
		{
			$root = $pconfig->get('offroot', 0) ? 0 : 1;
		}

		// Build upload path for project files
		$dir = strtolower($project_alias);
		$base = DS . trim($base, DS);
		$path  = $base . DS . $dir . DS . $case;

		$path = $root ? JPATH_ROOT . $path : $path;

		return $path;
	}

	//----------------------------------------------------------
	// Disk Usage
	//----------------------------------------------------------

	/**
	 * Get disk space
	 *
	 * @param      object  	$project	Project object
	 * @param      array  	$rows		Publications objet array
	 *
	 * @return     integer
	 */
	public static function getDiskUsage( $project = NULL, $rows = array() )
	{
		if ($project === NULL)
		{
			return false;
		}

		$used = 0;

		$pubconfig = JComponentHelper::getParams( 'com_publications' );
		$base = trim($pubconfig->get('webpath'), DS);

		if (!empty($rows))
		{
			foreach ($rows as $row)
			{
				$path = DS . $base . DS . \Hubzero\Utility\String::pad( $row->id );
				$used = $used + PublicationHelper::computeDiskUsage($path, JPATH_ROOT, false);
			}
		}

		return $used;
	}

	/**
	 * Get used disk space in path
	 *
	 * @param      string 	$path
	 * @param      string 	$prefix
	 * @param      boolean 	$git
	 *
	 * @return     integer
	 */
	public static function computeDiskUsage($path = '', $prefix = '', $git = true)
	{
		$used = 0;
		if ($path && is_dir($prefix . $path))
		{
			chdir($prefix . $path);

			$where = $git == true ? ' .[!.]*' : '';
			exec('du -sk ' . $where, $out);

			if ($out && isset($out[0]))
			{
				$dir = $git == true ? '.git' : '.';
				$kb = str_replace($dir, '', trim($out[0]));
				$used = $kb * 1024;
			}
		}

		return $used;
	}

	//----------------------------------------------------------
	// Contributors
	//----------------------------------------------------------

	/**
	 * Get used disk space in path
	 *
	 * @param      array 	$contributors
	 * @param      boolean 	$showorgs
	 * @param      boolean 	$showaslist
	 * @param      boolean 	$incSubmitter
	 *
	 * @return     string
	 */
	public function showContributors( $contributors = '', $showorgs = false, $showaslist = false, $incSubmitter = false, $format = false)
	{
		$view = new \Hubzero\Component\View(array(
			'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_publications',
			'name'   => 'view',
			'layout' => '_contributors',
		));
		$view->contributors  = $contributors;
		$view->showorgs  = $showorgs;
		$view->showaslist  = $showaslist;
		$view->incSubmitter  = $incSubmitter;
		$view->format  = $format;
		return $view->loadTemplate();
	}

	/**
	 * Get used disk space in path
	 *
	 * @param      array 	$contributors
	 * @param      boolean 	$incSubmitter
	 *
	 * @return     string
	 */
	public function getUnlinkedContributors($contributors = '', $incSubmitter = false )
	{
		if (!$contributors)
		{
			$contributors = $this->_contributors;
		}

		$html = '';
		if ($contributors != '')
		{
			$names = array();
			foreach ($contributors as $contributor)
			{
				if ($incSubmitter == false && $contributor->role == 'submitter')
				{
					continue;
				}
				if ($contributor->lastName || $contributor->firstName)
				{
					$name  = stripslashes($contributor->lastName);
					$name .= ', ' . substr(stripslashes($contributor->firstName), 0, 1) . '.';
				}
				else
				{
					$name = $contributor->name;
				}
				$name = str_replace( '"', '&quot;', $name );
				$names[] = $name;
			}
			if (count($names) > 0)
			{
				$html = implode( '; ', $names );
			}
		}
		return $html;
	}

	//----------------------------------------------------------
	// Publication thumbnail
	//----------------------------------------------------------

	/**
	 * Get publication thumbnail
	 *
	 * @param      int 		$pid
	 * @param      int 		$versionid
	 * @param      array 	$config
	 * @param      boolean 	$force
	 * @param      string	$cat
	 * @return     string HTML
	 */
	public function getThumb ($pid = 0, $versionid = 0, $config, $force = false, $cat = '')
	{
		// Get publication directory path
		$webpath = $config->get('webpath', 'site/publications');
		$path = $this->buildPath($pid, $versionid, $webpath);

		// Get default picture
		$default = $cat == 'tools'
				? $config->get('toolpic', '/components/com_publications/assets/img/tool_thumb.gif')
				: $config->get('defaultpic', '/components/com_publications/assets/img/resource_thumb.gif');

		if (is_file(JPATH_ROOT.$path . DS . 'thumb.gif') && $force == false)
		{
			return $path . DS . 'thumb.gif';
		}
		else
		{
			include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
				. 'com_publications' . DS . 'tables' . DS . 'screenshot.php');

			// Get screenshots
			$pScreenshot = new PublicationScreenshot($this->_db);
			$shots = $pScreenshot->getScreenshots( $versionid );

			if ($shots && count($shots) > 0)
			{
				$image = $path . DS . 'gallery' . DS . $shots[0]->srcfile;

				jimport('joomla.filesystem.file');
				if (is_file(JPATH_ROOT . $path . DS . 'thumb.gif'))
				{
					JFile::delete(JPATH_ROOT . $path . DS . 'thumb.gif');
				}

				// Make publication thumbnail
				if (is_file(JPATH_ROOT.$image))
				{
					include_once( JPATH_ROOT . DS . 'components' . DS
						. 'com_projects' . DS . 'helpers' . DS . 'imghandler.php' );

					$image_ext = array('jpg', 'jpeg', 'gif', 'png');
					$ext = explode('.', $shots[0]->srcfile);
					$ext = end($ext);

					if (in_array($ext, $image_ext))
					{
						$ih = new ProjectsImgHandler();
						JFile::copy(JPATH_ROOT.$image, JPATH_ROOT.$path . DS . 'thumb.gif');
						$ih->set('image','thumb.gif');
						$ih->set('overwrite',true);
						$ih->set('path',JPATH_ROOT.$path.DS);
						$ih->set('maxWidth', 100);
						$ih->set('maxHeight', 100);
						$ih->set('cropratio', '1:1');
						if ($ih->process())
						{
							return $path . DS . 'thumb.gif';
						}
					}
				}
			}

			return $default;
		}
	}

	//----------------------------------------------------------
	// Wiki-type publication
	//----------------------------------------------------------

	/**
	 * Get wiki page
	 *
	 * @param      object $attachment
	 * @param      object $publication
	 * @param      string $masterscope
	 * @param      string $versionid
	 * @return     object
	 */
	public function getWikiPage( $pageid = NULL, $publication = NULL, $masterscope = NULL, $versionid = NULL )
	{
		if (!$pageid || !$publication)
		{
			return false;
		}

		$query = "SELECT p.* ";
		if ($publication->state == 3)
		{
			// Draft - load latest version
			$query .= ", (SELECT v.pagetext FROM #__wiki_version as v WHERE v.pageid=p.id
			  ORDER by p.state ASC, v.version DESC LIMIT 1) as pagetext ";
		}
		else
		{
			$date = $publication->accepted && $publication->accepted != '0000-00-00 00:00:00'
				? $publication->accepted : $publication->submitted;
			$date = (!$date || $date == '0000-00-00 00:00:00') ? $publication->published_up : $date;

			$query .= ", (SELECT v.pagetext FROM #__wiki_version as v WHERE v.pageid=p.id AND ";
			$query .= $versionid ? " v.id=" . $versionid : " v.created <= '" . $date . "'";
			$query .= " ORDER BY v.created DESC LIMIT 1) as pagetext ";
		}

		$query .= " FROM #__wiki_page as p WHERE p.scope LIKE '" . $masterscope . "%' ";
		$query .=  is_numeric($pageid) ? " AND p.id='$pageid' " : " AND p.pagename='$pageid' ";
		$query .= " LIMIT 1";

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		return $result ? $result[0] : NULL;
	}

	//----------------------------------------------------------
	// Citations
	//----------------------------------------------------------

	/**
	 * Get citations
	 *
	 * @return     void
	 */
	public function getCitations()
	{
		if (!$this->_pub_id)
		{
			return false;
		}

		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
			. 'com_citations' . DS . 'tables' . DS . 'citation.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
			. 'com_citations' . DS . 'tables' . DS . 'association.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
			. 'com_citations' . DS . 'tables' . DS . 'author.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
			. 'com_citations' . DS . 'tables' . DS . 'secondary.php' );
		$database = JFactory::getDBO();

		$cc = new CitationsCitation( $database );

		$this->citations = $cc->getCitations( 'publication', $this->_pub_id );
	}

	/**
	 * Get citations count
	 *
	 * @return     void
	 */
	public function getCitationsCount()
	{
		$citations = $this->citations;
		if (!$citations)
		{
			$citations = $this->getCitations();
		}

		$this->citationsCount = $citations;
	}

	/**
	 * Get last citation date
	 *
	 * @return     void
	 */
	public function getLastCitationDate()
	{
		$this->lastCitationDate = NULL;

		if ($this->_pub_id)
		{
			return false;
		}

		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
			. 'com_citations' . DS . 'tables' . DS . 'citation.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
			. 'com_citations' . DS . 'tables' . DS . 'association.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
			. 'com_citations' . DS . 'tables' . DS . 'author.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
			. 'com_citations' . DS . 'tables' . DS . 'secondary.php' );
		$database = JFactory::getDBO();

		$cc = new CitationsCitation( $database );

		$this->lastCitationDate = $cc->getLastCitationDate( 'publication', $this->_pub_id );
	}

	//----------------------------------------------------------
	// Tags
	//----------------------------------------------------------

	/**
	 * Get tags
	 *
	 * @param      int $tagger_id
	 * @param      int $strength
	 * @param      boolean $admin
	 *
	 * @return     string HTML
	 */
	public function getTags($tagger_id = 0, $strength = 0, $admin = 0)
	{
		if ($this->_pub_id == 0)
		{
			return false;
		}

		include_once( JPATH_ROOT . DS . 'components' . DS . 'com_publications'
			. DS . 'helpers' . DS . 'tags.php' );

		$rt = new PublicationTags( $this->_db );
		$this->tags = $rt->get_tags_on_object($this->_pub_id, 0, 0, $tagger_id, $strength, $admin);
	}

	/**
	 * Get tags for editing
	 *
	 * @param      int $tagger_id
	 * @param      int $strength
	 *
	 * @return     string HTML
	 */
	public function getTagsForEditing( $tagger_id = 0, $strength = 0 )
	{
		if ($this->_pub_id == 0)
		{
			return false;
		}

		include_once( JPATH_ROOT . DS . 'components' . DS . 'com_publications'
			. DS . 'helpers' . DS . 'tags.php' );

		$rt = new PublicationTags( $this->_db );
		$this->tagsForEditing = $rt->get_tag_string( $this->_pub_id, 0, 0, $tagger_id, $strength, 0 );
	}

	/**
	 * Get tag cloud
	 *
	 * @param      boolean $admin
	 *
	 * @return     string HTML
	 */
	public function getTagCloud( $admin=0 )
	{
		if ($this->_pub_id == 0)
		{
			return false;
		}

		include_once( JPATH_ROOT . DS . 'components' . DS . 'com_publications'
			. DS . 'helpers' . DS . 'tags.php' );
		$database = JFactory::getDBO();

		$rt = new PublicationTags( $database );
		$this->tagCloud = $rt->get_tag_cloud(0, $admin, $this->_pub_id);
	}

	//----------------------------------------------------------
	// Contribute publications
	//----------------------------------------------------------

	/**
	 * Get publication property
	 *
	 * @param      object $row
	 * @param      string $get
	 * @param      boolean $version
	 * @return     string HTML
	 */
	public static function getPubStateProperty($row, $get = 'class', $version = 1)
	{
		$dateFormat = 'M d, Y';

		$status = '';
		$date   = '';
		$class  = '';

		$now = JFactory::getDate()->toSql();

		switch ($row->state)
		{
			case 0:
				$class  = 'unpublished';
				$status = JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_UNPUBLISHED');
				$date = strtolower(JText::_('PLG_PROJECTS_PUBLICATIONS_UNPUBLISHED'))
					.' ' . JHTML::_('date', $row->published_down, $dateFormat);
				break;

			case 1:
				$class  = 'published';
				$status.= JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_PUBLISHED');
				$date   = $row->published_up > $now ? JText::_('to be') . ' ' : '';
				$date  .= strtolower(JText::_('PLG_PROJECTS_PUBLICATIONS_RELEASED'))
					.' ' . JHTML::_('date', $row->published_up, $dateFormat);
				break;

			case 3:
			default:
				$class = 'draft';
				$status = JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_DRAFT');
				$date = strtolower(JText::_('PLG_PROJECTS_PUBLICATIONS_STARTED'))
					.' ' . JHTML::_('date', $row->created, $dateFormat);
				break;

			case 4:
				$class   = 'ready';
				$status .= JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_READY');
				$date = strtolower(JText::_('PLG_PROJECTS_PUBLICATIONS_RELEASED'))
					.' ' . JHTML::_('date', $row->published_up, $dateFormat);
				break;

			case 5:
				$class  = 'pending';
				$status = JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_PENDING');
				$date  .= strtolower(JText::_('PLG_PROJECTS_PUBLICATIONS_SUBMITTED'))
					.' ' . JHTML::_('date', $row->submitted, $dateFormat);
				break;

			case 6:
				$class  = 'archived';
				$status = JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_DARKARCHIVE');
				$date   = $row->published_up > $now ? JText::_('to be') . ' ' : '';
				$date  .= strtolower(JText::_('PLG_PROJECTS_PUBLICATIONS_RELEASED'))
					.' ' . JHTML::_('date', $row->published_up, $dateFormat);
				break;

			case 7:
				$class  = 'wip';
				$status = JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_WIP');
				$date  .= strtolower(JText::_('PLG_PROJECTS_PUBLICATIONS_SUBMITTED'))
					.' ' . JHTML::_('date', $row->submitted, $dateFormat);
				break;
		}

		switch ($get)
		{
			case 'class':
				return $class;
				break;

			case 'status':
				return $status;
				break;

			case 'date':
				return $date;
				break;
		}
	}

	/**
	 * Write publication category
	 *
	 * @param      string $cat_alias
	 * @param      string $typetitle
	 * @return     string HTML
	 */
	public static function writePubCategory( $cat_alias = '', $typetitle = '' )
	{
		$html = '';

		if (!$cat_alias && !$typetitle)
		{
			return false;
		}

		if ($cat_alias)
		{
			$cls = str_replace(' ', '', $cat_alias);
			$title = $cat_alias;
		}
		elseif ($pubtitle)
		{
			$normalized = strtolower($typetitle);
			$cls = preg_replace("/[^a-zA-Z0-9]/", "", $normalized);

			if (substr($normalized, -3) == 'ies')
			{
				$title = $normalized;
				$cls = $cls;
			}
			else
			{
				$title = substr($normalized, 0, -1);
				$cls = substr($cls, 0, -1);
			}
		}

		$html .= '<span class="' . $cls . '"></span> ' . $title;

		return $html;
	}

	/**
	 * Show publication title
	 *
	 * @param      object $pub
	 * @param      string $url
	 * @param      string $tabtitle
	 * @param      string $append
	 * @return     string HTML
	 */
	public static function showPubTitle( $pub, $url, $tabtitle = '', $append = '' )
	{
		$typetitle = PublicationHelper::writePubCategory($pub->cat_alias, $pub->cat_name);
		$tabtitle  = $tabtitle ? $tabtitle : ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATIONS'));
		$pubUrl    = JRoute::_($url . a . 'pid=' . $pub->id) .'?version=' . $pub->version_number;
		?>
		<div id="plg-header">
			<h3 class="publications c-header"><a href="<?php echo $url; ?>" title="<?php echo $tabtitle; ?>"><?php echo $tabtitle; ?></a> &raquo; <span class="restype indlist"><?php echo $typetitle; ?></span> <span class="indlist">"<?php if ($append) { echo '<a href="' . $pubUrl . '" >'; } ?><?php echo \Hubzero\Utility\String::truncate($pub->title, 65); ?>"<?php if ($append) { echo '</a>'; } ?></span>
			<?php if ($append) { echo $append; } ?>
			</h3>
		</div>
	<?php
	}

	/**
	 * Show pub title for provisioned projects
	 *
	 * @param      object $pub
	 * @param      string $url
	 * @param      string $append
	 * @return     string HTML
	 */
	public static function showPubTitleProvisioned( $pub, $url, $append = '' )
	{
	?>
		<h3 class="prov-header">
			<a href="<?php echo $url; ?>"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> &raquo; "<?php echo \Hubzero\Utility\String::truncate($pub->title, 65); ?>"
			<?php if ($append) { echo $append; } ?>
		</h3>
	<?php
	}

	/**
	 * Send email
	 *
	 * @param      array 	$config
	 * @param      object 	$publication
	 * @param      array 	$addressees
	 * @param      string 	$subject
	 * @param      string 	$message
	 * @return     void
	 */
	public static function notify( $config, $publication, $addressees = array(),
		$subject = NULL, $message = NULL, $hubMessage = false)
	{
		if (!$subject || !$message || empty($addressees))
		{
			return false;
		}

		// Is messaging turned on?
		if ($config->get('email') != 1)
		{
			return false;
		}

		// Set up email config
		$jconfig = JFactory::getConfig();
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_('COM_PUBLICATIONS');
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Html email
		$from['multipart'] = md5(date('U'));

		// Get message body
		$eview = new \Hubzero\Component\View(array(
			'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_publications',
			'name'   => 'emails',
			'layout' => '_plain'
		));

		$eview->publication 	= $publication;
		$eview->message			= $message;
		$eview->subject			= $subject;

		$body = array();
		$body['plaintext'] 	= $eview->loadTemplate();
		$body['plaintext'] 	= str_replace("\n", "\r\n", $body['plaintext']);

		// HTML email
		$eview->setLayout('_html');
		$body['multipart'] = $eview->loadTemplate();
		$body['multipart'] = str_replace("\n", "\r\n", $body['multipart']);

		$body_plain = is_array($body) && isset($body['plaintext']) ? $body['plaintext'] : $body;
		$body_html  = is_array($body) && isset($body['multipart']) ? $body['multipart'] : NULL;

		// Send HUB message
		if ($hubMessage)
		{
			JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger( 'onSendMessage',
				array(
					'publication_status_changed',
					$subject,
					$body,
					$from,
					$addressees,
					'com_publications'
				)
			);
		}
		else
		{
			// Send email
			foreach ($addressees as $userid)
			{
				$user = \Hubzero\User\Profile::getInstance($userid);
				if ($user === false)
				{
					continue;
				}

				$mail = new \Hubzero\Mail\Message();
				$mail->setSubject($subject)
					->addTo($user->get('email'), $user->get('name'))
					->addFrom($from['email'], $from['name'])
					->setPriority('normal');

				$mail->addPart($body_plain, 'text/plain');

				if ($body_html)
				{
					$mail->addPart($body_html, 'text/html');
				}

				$mail->send();
			}
		}
	}
}