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
class PublicationHelper extends \JObject
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

		$cc = new \Components\Citations\Tables\Citation( $database );

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

		$cc = new \Components\Citations\Tables\Citation( $database );

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