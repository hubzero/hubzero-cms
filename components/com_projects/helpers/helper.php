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
 * Projects helper class
 */
class ProjectsHelper extends JObject {
		
	/**
	 * Project ID
	 * 
	 * @var mixed
	 */
	private $_id = 0;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_data = array();

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */	
	public function __construct( &$db )
	{
		$this->_db =& $db;
	}
		
	/**
	 * Get a list of project notes
	 * 
	 * @param      string $group cn of project group
	 * @param      string $masterscope
	 * @param      int $limit
	 * @param      string $orderby
	 * @return     object list
	 */	
	public function getNotes( $group, $masterscope, $limit = 0, $orderby = 'p.scope, p.times_rated ASC, p.id' ) 
	{		
		$orderby 	= $orderby ? $orderby : 'p.scope, p.times_rated ASC, p.id';
		
		$query = "SELECT DISTINCT p.id, p.pagename, p.title, p.scope, p.times_rated 
		          FROM #__wiki_page AS p 
				  WHERE p.group_cn='" . $group . "' 
				  AND p.scope LIKE '" . $masterscope . "%' 
				  AND p.pagename NOT LIKE 'Template:%' 
				  AND p.state!=2
				  ORDER BY $orderby ";
		$query.= intval($limit) ? " LIMIT $limit" : '';
				
		$this->_db->setQuery($query);				
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Get count of project notes
	 * 
	 * @param      string $group cn of project group
	 * @return     void
	 */	
	public function getNoteCount( $group, $scope = '' ) 
	{
		$query = "SELECT COUNT(*) FROM #__wiki_page AS p 
				  WHERE p.group_cn='" . $group . "' AND p.state!=2 
				  AND p.pagename NOT LIKE 'Template:%'";
		$query.= $scope ? " AND p.scope LIKE '" . $scope . "%'" : "";
				
		$this->_db->setQuery($query);				
		return $this->_db->loadResult();
	}
	
	/**
	 * Get a list of parent project notes
	 * 
	 * @param      string $group cn of project group
	 * @param      string $scope
	 * @param      string $task
	 * @return     void
	 */	
	public function getParentNotes( $group, $scope, $task = '' ) 
	{
		$parts = explode ( '/', $scope );	
		$remaining = array_slice($parts, 3);
		if ($remaining) 
		{
			$query = "SELECT DISTINCT p.pagename, p.title, p.scope ";
			$query.= "FROM #__wiki_page AS p ";
			$query.= "WHERE p.group_cn='" . $group . "' AND p.state!=2 ";
			$k = 1;
			$where = '';
			foreach ($remaining as $r) 
			{
				$where .= "p.pagename='" . trim($r) . "'";
				$where .= $k == count($remaining) ? '' : ' OR ';
				$k++;
			}
			$query.= "AND (".$where.")" ;
			$this->_db->setQuery($query);				
			return $this->_db->loadObjectList();
		}
		else 
		{
			return array();
		}
	}
	
	/**
	 * Get project note
	 * 
	 * @param      string $group cn of project group
	 * @param      string $masterscope
	 * @param      string $prefix
	 * @return     void
	 */	
	public function getSelectedNote( $id = '', $group = '', $masterscope = '' ) 
	{
		$query = "SELECT DISTINCT p.id, p.pagename, p.title, p.scope, p.times_rated,
	 		  	  (SELECT v.version FROM #__wiki_version as v WHERE v.pageid=p.id 
				  ORDER by v.version DESC LIMIT 1) as version,
				  (SELECT vv.id FROM #__wiki_version as vv WHERE vv.pageid=p.id 
				  ORDER by vv.id DESC LIMIT 1) as instance
			      FROM #__wiki_page AS p
				  WHERE p.group_cn='" . $group . "' 
				  AND p.scope LIKE '" . $masterscope . "%' 
				  AND p.state!=2
				  AND p.pagename NOT LIKE 'Template:%'";
		$query.=  is_numeric($id) ? " AND p.id='$id' LIMIT 1" : " AND p.pagename='$id' LIMIT 1";				  
				
		$this->_db->setQuery($query);				
		$result = $this->_db->loadObjectList();
		
		return $result ? $result[0] : NULL;
	}
	
	/**
	 * Get default project note
	 * 
	 * @param      string $group cn of project group
	 * @param      string $masterscope
	 * @param      string $prefix
	 * @return     void
	 */	
	public function getFirstNote( $group, $masterscope, $prefix = '' ) 
	{
		$query = "SELECT p.pagename FROM #__wiki_page AS p 
				  WHERE p.group_cn='" . $group . "' AND p.state!=2
				  AND p.scope='" . $masterscope . "'";
		$query.= $prefix ? "AND p.pagename LIKE '" . $prefix . "%'" : "";
		$query.= " ORDER BY p.times_rated, p.id ASC LIMIT 1";				  
				
		$this->_db->setQuery($query);				
		return $this->_db->loadResult();
	}
	
	/**
	 * Get a last note
	 * 
	 * @param      string $group cn of project group
	 * @param      string $scope
	 * @return     void
	 */	
	public function getLastNote( $group, $scope ) 
	{
		$query = "SELECT p.pagename, p.title, p.scope, p.times_rated 
		          FROM #__wiki_page AS p 
				  WHERE p.group_cn='" . $group . "' 
				  AND p.scope='" . $scope . "' 
				  ORDER BY p.times_rated DESC LIMIT 1";
				
		$this->_db->setQuery($query);				
		return $this->_db->loadResult();
	}
	
	/**
	 * Get last note order
	 * 
	 * @param      string $group cn of project group
	 * @param      string $scope
	 * @return     void
	 */	
	public function getLastNoteOrder( $group, $scope ) 
	{
		$query = "SELECT p.times_rated FROM #__wiki_page AS p 
				  WHERE p.group_cn='" . $group . "' 
				  AND p.scope='" . $scope . "' 
				  ORDER BY p.times_rated DESC LIMIT 1";
				
		$this->_db->setQuery($query);				
		return $this->_db->loadResult();
	}
	
	/**
	 * Save note order
	 * 
	 * @param      string $group cn of project group
	 * @param      string $scope
	 * @param      int $order
	 * @return     void
	 */	
	public function saveNoteOrder( $group, $scope, $order = 0 ) 
	{
		$query = "UPDATE #__wiki_page AS p SET p.times_rated='" . $order . "' 
				  WHERE p.group_cn='" . $group . "' 
				  AND p.scope='" . $scope . "' 
				  AND p.times_rated='0'";
				
		$this->_db->setQuery($query);				
		if (!$this->_db->query()) 
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Fix scope paths after page rename
	 * 
	 * @param      string $group cn of project group
	 * @param      string $scope
	 * @param      string $oldpagename
	 * @param      string $newpagename
	 * @return     void
	 */	
	public function fixScopePaths( $group, $scope, $oldpagename, $newpagename ) 
	{		
		$query = "UPDATE #__wiki_page AS p SET p.scope=replace(p.scope, '/" . $oldpagename . "', '/" . $newpagename . "')
				  WHERE p.group_cn='" . $group . "'";
				
		$this->_db->setQuery($query);				
		if (!$this->_db->query()) 
		{
			return false;
		}
		return true;		
	}
		
	/**
	 * Save wiki attachment
	 * 
	 * @param      string $page
	 * @param      string $file
	 * @param      int $uid
	 * @return     void
	 */	
	public function saveWikiAttachment( $page, $file, $uid = 0 )
	{
		// Create database entry
		$attachment 				= new WikiPageAttachment($this->_db);
		$attachment->pageid      	= $page->id;
		$attachment->filename    	= $file;
		$attachment->description 	= '';
		$attachment->created     	= date('Y-m-d H:i:s', time());
		
		if (!$uid)
		{
			$juser =& JFactory::getUser();
			$uid   = $juser->get('id');
		}
		$attachment->created_by  	= $uid;

		if (!$attachment->check()) 
		{
			$this->setError($attachment->getError());
		}
		if (!$attachment->store()) 
		{
			$this->setError($attachment->getError());
		}
		return $attachment->id;
	}
	
	/**
	 * Get suggestions for new project members
	 * 
	 * @param      object $project
	 * @param      string $option
	 * @param      int $uid
	 * @param      array $config
	 * @param      array $params
	 * @return     void
	 */	
	public function getSuggestions( $project, $option, $uid = 0, $config, $params ) 
	{	
		$suggestions = array();
		$creator = $project->created_by_user == $uid ? 1 : 0;
		$goto  = 'alias=' . $project->alias;
		
		// Adding a picture
		if ($creator && !$project->picture) 
		{
			$suggestions[] = array(
				'class' => 's-picture', 
				'text' => JText::_('COM_PROJECTS_WELCOME_ADD_THUMB'), 
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&task=edit')
			);
		}
		
		// Adding grant information
		if ($creator && $config->get('grantinfo') && !$params->get('grant_title', '')) 
		{
			$suggestions[] = array(
				'class' => 's-about', 
				'text' => JText::_('COM_PROJECTS_WELCOME_ADD_GRANT_INFO'), 
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&task=edit') . '?edit=settings'
			);
		}
		
		// Adding about text
		if ($creator && !$project->about && $project->private == 0) 
		{
			$suggestions[] = array(
				'class' => 's-about', 
				'text' => JText::_('COM_PROJECTS_WELCOME_ADD_ABOUT'), 
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&task=edit')
			);
		}
		
		// File upload
		if ($project->counts['files'] == 0 || (!$creator && $project->num_visits < 5) 
			|| ($creator && $project->num_visits < 5 &&  $project->counts['files'] == 0 ) ) 
		{
			$text = $creator ? JText::_('COM_PROJECTS_WELCOME_UPLOAD_FILES') : JText::_('COM_PROJECTS_WELCOME_SHARE_FILES');
			$suggestions[] = array(
				'class' => 's-files', 
				'text' => $text, 
				'url' =>  JRoute::_('index.php?option=' . $option . a . $goto . '&active=files')
			);
		}
		
		// Inviting others
		if ($project->counts['team'] == 1 && $project->role == 1) 
		{
			$suggestions[] = array(
				'class' => 's-team', 
				'text' => JText::_('COM_PROJECTS_WELCOME_INVITE_USERS'), 
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&task=edit').'?edit=team'
			);
		}
		
		// Todo items
		if ($project->counts['todo'] == 0 || (!$creator && $project->num_visits < 5) 
			|| ($creator && $project->num_visits < 5 &&  $project->counts['todo'] == 0 ) ) 
		{
			$suggestions[] = array(
				'class' => 's-todo', 
				'text' => JText::_('COM_PROJECTS_WELCOME_ADD_TODO'), 
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&active=todo')
			);
		}
		
		// Notes
		if ($project->counts['notes'] == 0 || (!$creator && $project->num_visits < 5) 
			|| ($creator && $project->num_visits < 5 &&  $project->counts['notes'] == 0 )  ) 
		{
			$suggestions[] = array(
				'class' => 's-notes', 
				'text' => JText::_('COM_PROJECTS_WELCOME_START_NOTE'), 
				'url' => JRoute::_('index.php?option=' . $option . a . $goto . '&active=notes')
			);
		}
		return $suggestions;
	}
	
	/**
	 * Get project path
	 * 
	 * @param      string $projectAlias
	 * @param      string $webdir
	 * @param      boolean $offroot
	 * @param      string $case
	 * @return     string
	 */	
	public function getProjectPath( $projectAlias = '', $webdir = '', $offroot = 0, $case = 'files' ) 
	{		
		if (!$projectAlias || ! $webdir)
		{
			return false;
		}
		
		// Build upload path for project files
		$dir = strtolower($projectAlias);
		
		if (substr($webdir, 0, 1) != DS) 
		{
			$webdir = DS.$webdir;
		}
		if (substr($webdir, -1, 1) == DS) 
		{
			$webdir = substr($webdir, 0, (strlen($webdir) - 1));
		}
		$path  = $case ? $webdir . DS . $dir. DS . $case : $webdir . DS . $dir ;
		$path  = $offroot ? $path : JPATH_ROOT . $path;
		return $path;		
	}
	
	/**
	 * Covert param to array of values
	 * 
	 * @param      string $param
	 * 
	 * @return     array
	 */
	public function getParamArray($param = '')
	{
		if ($param)
		{
			$array = explode(',', $param);
			return array_map('trim', $array);		
		}
		return array();
	}
	
	/**
	 * Send hub message
	 * 
	 * @param      string 	$option
	 * @param      array 	$config
	 * @param      object 	$project
	 * @param      array 	$addressees
	 * @param      string 	$subject
	 * @param      string 	$component
	 * @param      string 	$layout
	 * @param      string 	$message
	 * @param      string 	$reviewer
	 * @return     void
	 */	
	public function sendHUBMessage( 
		$option, $config, $project, 
		$addressees = array(), $subject = '', 
		$component = '', $layout = '', 
		$message = '', $reviewer = '')
	{
		if (!$layout || !$subject || !$component || empty($addressees))
		{
			return false;
		}
		
		// Is messaging turned on?
		if ($config->get('messaging') != 1)
		{
			return false;
		}
		
		// Set up email config
		$jconfig =& JFactory::getConfig();
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('COM_PROJECTS');
		$from['email'] = $jconfig->getValue('config.mailfrom');
		
		// Get message body
		$eview 					= new JView( array('name'=>'emails', 'layout'=> $layout ) );
		$eview->option 			= $option;
		$eview->hubShortName 	= $jconfig->getValue('config.sitename');
		$eview->project 		= $project;
		$eview->params 			= new JParameter( $project->params );
		$eview->config 			= $config;
		$eview->message			= $message;	
		$eview->reviewer		= $reviewer;				

		// Get profile of author group
		if ($project->owned_by_group) 
		{
			$eview->nativegroup = Hubzero_Group::getInstance( $project->owned_by_group );	
		}
		$body = $eview->loadTemplate();
		$body = str_replace("\n", "\r\n", $body);

		// Send HUB message
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger( 'onSendMessage', 
			array( 
				$component, 
				$subject,
			 	$body, 
				$from, 
				$addressees, 
				$option 
			)
		);		
	}
	
	/**
	 * Show Git info
	 * 
	 * @param      string 	$gitpath
	 * @param      string 	$path
	 * @param      string 	$fhash
	 * @param      string 	$file
	 * @param      int 		$showstatus
	 * @return     void
	 */	
	public function showGitInfo ($gitpath = '', $path = '', $fhash = '', $file = '', $showstatus = 1) 
	{
		if (!$gitpath || !$path || !$file) 
		{
			return false;
		}
		chdir($path);
		
		// Get all file revisions
		exec ($gitpath . ' log --diff-filter=AM --pretty=format:%H ' . escapeshellarg($file) . ' 2>&1', $out);
		
		$versions = array();
		$hashes = array();
		$added = array();
		$latest = array();

		if (count($out) > 0 && $out[0] != '') 
		{
			foreach ($out as $line) 
			{
				if (preg_match("/[a-zA-Z0-9]/", $line) && strlen($line) == 40) 
				{
					$hashes[]  = $line;
				}
			}
			// Start with oldest commit
			$hashes = array_reverse($hashes);
		}
		
		if (!empty($hashes)) 
		{
			$i = 0;
			foreach ($hashes as $hash) 
			{
				$out1 = array();
				$out2 = array();
							
				exec($gitpath . ' log --pretty=format:%cd ' . escapeshellarg($hash) . ' 2>&1', $out1);
				$arr = explode("\t", $out1[0]);
				$timestamp = strtotime($arr[0]);
				$versions[$i]['date'] =  date ('m/d/Y g:i A', $timestamp);
					
				exec($gitpath . ' log --pretty=format:%an ' . escapeshellarg($hash) . ' 2>&1', $out2);
				$arr = explode("\t", $out2[0]);
				$versions[$i]['author'] = $arr[0];
				$versions[$i]['num']    = $i+1;
				$versions[$i]['latest'] = ($i == (count($hashes) - 1)) ? 1 : 0;

				$versions[$i]['hash'] = $hash;
				if ($hash == $fhash) 
				{
					$versions[$i]['changes'] = count($hashes) - 1 - $i;
					$added = $versions[$i];					
				}
				if ($i == (count($hashes) - 1)) 
				{
					$latest = $versions[$i];
				}

				$i++;
			}
			
			if ($showstatus == 1) 
			{
				// Review stage
				$status = '';
				if (empty($added)) 
				{
					$added = $latest;
				}
				if (!empty($added)) 
				{
					$status .= 'Added at revision #'.$added['num'];
					if ($added['latest'] == 1) 
					{
						$status .= ' (latest) ';
					}
					else 
					{
						$status .= ' ('.$added['changes'].' new update(s)';
						$status .= ' - last update by '.$latest['author'].' on '.$latest['date'].')';
					}
				}
				return $status;
			}
			elseif ($showstatus == 2) 
			{
				// Get latest commit
				return $latest;
			}
			elseif ($showstatus == 3) 
			{
				// Get added commit
				return $added;
			}
			else 
			{
				return $versions;
			}
		}
	}
	
	/**
	 * Parse html of external web page
	 * 
	 * @param      string 		$html
	 * @param      string 		$tag
	 * @param      boolean 		$selfclosing
	 * @param      boolean 		$return_the_entire_ta
	 * @param      string 		$charset
	 * @return     array
	 */	
	public function extract_tags( $html, $tag, $selfclosing = null, $return_the_entire_tag = false, $charset = 'ISO-8859-1' ){

		if ( is_array($tag) )
		{
			$tag = implode('|', $tag);
		}

		//If the user didn't specify if $tag is a self-closing tag we try to auto-detect it
		//by checking against a list of known self-closing tags.
		$selfclosing_tags = array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta', 'col', 'param' );
		if ( is_null($selfclosing) )
		{
			$selfclosing = in_array( $tag, $selfclosing_tags );
		}

		//The regexp is different for normal and self-closing tags because I can't figure out 
		//how to make a sufficiently robust unified one.
		if ( $selfclosing )
		{
			$tag_pattern = 
				'@<(?P<tag>'.$tag.')			# <tag
				(?P<attributes>\s[^>]+)?		# attributes, if any
				\s*/?>					# /> or just >, being lenient here 
				@xsi';
		} 
		else 
		{
			$tag_pattern = 
				'@<(?P<tag>'.$tag.')			# <tag
				(?P<attributes>\s[^>]+)?		# attributes, if any
				\s*>					# >
				(?P<contents>.*?)			# tag contents
				</(?P=tag)>				# the closing </tag>
				@xsi';
		}

		$attribute_pattern = 
			'@
			(?P<name>\w+)							# attribute name
			\s*=\s*
			(
				(?P<quote>[\"\'])(?P<value_quoted>.*?)(?P=quote)	# a quoted value
				|							# or
				(?P<value_unquoted>[^\s"\']+?)(?:\s+|$)			# an unquoted value (terminated by whitespace or EOF) 
			)
			@xsi';

		// Find all tags 
		if ( !preg_match_all($tag_pattern, $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) )
		{
			//Return an empty array if we didn't find anything
			return array();
		}

		$tags = array();
		
		foreach ($matches as $match)
		{
			//Parse tag attributes, if any
			$attributes = array();
			if ( !empty($match['attributes'][0]) )
			{ 
				if ( preg_match_all( $attribute_pattern, $match['attributes'][0], $attribute_data, PREG_SET_ORDER ) )
				{
					//Turn the attribute data into a name->value array
					foreach ($attribute_data as $attr)
					{
						if ( !empty($attr['value_quoted']) )
						{
							$value = $attr['value_quoted'];
						} 
						elseif ( !empty($attr['value_unquoted']) )
						{
							$value = $attr['value_unquoted'];
						} 
						else 
						{
							$value = '';
						}

						//Passing the value through html_entity_decode is handy when you want
						//to extract link URLs or something like that. You might want to remove
						//or modify this call if it doesn't fit your situation.
						$value = html_entity_decode( $value, ENT_QUOTES, $charset );

						$attributes[$attr['name']] = $value;
					}
				}
			}

			$tag = array(
				'tag_name' => $match['tag'][0],
				'offset' => $match[0][1], 
				'contents' => !empty($match['contents']) ? $match['contents'][0] : '', //empty for self-closing tags
				'attributes' => $attributes, 
			);
			
			if ( $return_the_entire_tag )
			{
				$tag['full_tag'] = $match[0][0]; 			
			}

			$tags[] = $tag;
		}

		return $tags;
	}
	
	/**
	 * check values
	 * 
	 * @param      string	$value	
	 *
	 * @return     response string
	 */
	public function checkValues ($value)
	{
		$value = trim($value);
		if (get_magic_quotes_gpc())
		{
			$value = stripslashes($value);
		}
		$value = strtr($value, array_flip(get_html_translation_table(HTML_ENTITIES)));
		$value = strip_tags($value);
		$value = htmlspecialchars($value);
		return $value;
	}	
	
	/**
	 * Run cURL
	 * 
	 * @param      string	$url
	 * @param      string	$method
	 * @param      array	$postvals	
	 *
	 * @return     response string
	 */	
	public function runCurl($url, $method = 'GET', $postvals = null)
	{
	    $ch = curl_init($url);

	    //GET request: send headers and return data transfer
	    if ($method == 'GET')
		{
	        
			$headers   = array();
			$headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
			$headers[] = 'Connection: Keep-Alive';
			$headers[] = 'Content-type: text/html;charset=UTF-8';
			
			$options = array(
	            CURLOPT_URL => $url,
	            CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_HTTPHEADER => $headers,
				CURLOPT_HEADER => 0,
				CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)',
				CURLOPT_COOKIEFILE => '/tmp/cookies.txt',
				CURLOPT_ENCODING => 'gzip',
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTPGET => true,
				CURLOPT_FOLLOWLOCATION => 1
	        );
	        curl_setopt_array($ch, $options);
	    } 
		else 
		{
	        $options = array(
	            CURLOPT_URL => $url,
	            CURLOPT_POST => 1,
	            CURLOPT_POSTFIELDS => $postvals,
	            CURLOPT_RETURNTRANSFER => 1
	        );
	        curl_setopt_array($ch, $options);
	    }

	    $response = curl_exec($ch);
	    curl_close($ch);

	    return $response;
	}
	
	/**
	 * Check file for viruses
	 * 
	 * @param      string 	$fpath		Full path to scanned file
	 *
	 * @return     mixed
	 */
	public function virusCheck( $fpath = '' ) 
	{
		exec("clamscan -i --no-summary --block-encrypted " . escapeshellarg($fpath), $output, $status);

		if ($status == 1)
		{
			unlink($fpath);
			return true;
		}

		return false;
	}
	
	/**
	 * Get path to wiki page images and files
	 * 
	 * @param      int 	$page
	 *
	 * @return     string
	 */
	public function getWikiPath( $id = 0)
	{				
		// Ensure we have an ID to work with
		$listdir = JRequest::getInt('lid', 0);
		$id = $id ? $id : $listdir;
		
		if (!$id)
		{
			return false;
		}
		
		// Load wiki configs
		$wiki_config =& JComponentHelper::getParams( 'com_wiki' ); 			
		
		$path =  DS . trim($wiki_config->get('filepath', '/site/wiki'), DS) . DS . $id;

		if (!is_dir(JPATH_ROOT . $path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create(JPATH_ROOT . $path, 0777)) 
			{
				return false;
			}
		}
		
		return $path;
	}
	
	/**
	 * Fix up internal image references
	 * 
	 *
	 * @return     mixed
	 */
	public function wikiFixImages( $page, $pagetext, $projectid, $alias, $publication = NULL, $html = '', $copy = true ) 
	{
		if (!$page || !$pagetext)
		{
			return $html;
		}
		
		// Load component configs
		$config =& JComponentHelper::getParams( 'com_projects' );
		$option = 'com_projects';
		
		// Get project path
		$projectPath = ProjectsHelper::getProjectPath(
			$alias, 
			$config->get('webpath', 0),
			$config->get('offroot', 0)
		);
		
		// Load wiki configs
		$wiki_config =& JComponentHelper::getParams( 'com_wiki' ); 	
		
		// Get wiki upload path
		$previewPath = ProjectsHelper::getWikiPath($page->id);		
		
		// Get joomla libraries
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$database =& JFactory::getDBO();
		
		// Inlcude public stamps class
		if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php');
		}
		else
		{
			return $html;
		}
		
		// Get publication path
		if ($publication)
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'helper.php');
			
			$pubconfig =& JComponentHelper::getParams( 'com_publications' );
			$base_path = $pubconfig->get('webpath');
			$pubPath = PublicationHelper::buildPath($publication->id, $publication->version_id, $base_path, 'wikicontent', $root = 0);
			
			if (!is_dir(JPATH_ROOT . $pubPath))
			{
				if (!JFolder::create( JPATH_ROOT . $pubPath, 0777 )) 
				{
					return $html;
				}
			}
			
			$previewPath = $pubPath;			
		}
		
		// Get image extensions
		$imgs = explode(',', $wiki_config->get('img_ext'));
		array_map('trim', $imgs);
		array_map('strtolower', $imgs);
		
		// Parse images
		preg_match_all("'Image\\(.*?\\)'si", $pagetext, $images);
		if (!empty($images))
		{
			$images = $images[0];
													
			foreach ($images as $image)
			{
				$ibody = str_replace('Image(' , '', $image);
				$ibody = str_replace(')' , '', $ibody);
				$args  = explode(',', $ibody);
				$file  = array_shift($args);
								
				$fpath = $projectPath . DS . $file;
				
				$pubstamp = NULL;
				
				// Copy file to wiki dir if not there
				if (is_file( $fpath ) && !is_file(JPATH_ROOT . $previewPath . DS . $file) && $copy == true)
				{											
					if (!is_dir(JPATH_ROOT . $previewPath . DS . dirname($file)))
					{
						if (!JFolder::create( JPATH_ROOT . $previewPath . DS . dirname($file), 0777 )) 
						{
							return $html;
						}
					}
					
					JFile::copy($fpath, JPATH_ROOT . $previewPath . DS . $file);
				}
				
				// Get public stamp for file
				$objSt = new ProjectPubStamp( $database );
				if (is_file( $fpath ) && !$publication)
				{
					// Build reference
					$reference = array(
						'file'   => $file,
						'disp' 	 => 'inline'
					);
					
					if ($objSt->registerStamp($projectid, json_encode($reference), 'files'))
					{
						$pubstamp = $objSt->stamp;
					}
				}
				elseif ($publication)
				{
					// Build reference
					$reference = array(
						'pid'	 => $publication->id,
						'vid'	 => $publication->version_id,
						'path'   => $file,
						'folder' => 'wikicontent',
						'disp'	 => 'inline'
					);
					
					if ($objSt->registerStamp($projectid, json_encode($reference), 'publications'))
					{
						$pubstamp = $objSt->stamp;
					}
				}
				
				// Get link
				if ($pubstamp)
				{
					$link = JRoute::_('index.php?option=com_projects' . a . 'task=get') . '/?s=' . $pubstamp;
				}
				else
				{
					$link = $previewPath . DS . $file;
				}
				
				// Replace not found error
				$href = '<a href="' . $link . '" rel="lightbox"><img src="' . $link . '" /></a>';				
				$replace = '(Image(' . $ibody . ') failed - File not found)' . JPATH_ROOT . $previewPath . DS . $file;

				$fname = preg_quote($replace, '/');
				$html = str_replace($replace, $href, $html);										
				
				// Replace reference
				$replace = $page->scope. DS . $page->pagename . DS . 'Image:' . $file;
				$replace = preg_quote($replace, '/');
				$html = preg_replace("/\/projects\/$replace/", $link, $html);				
			}
		}
		
		return $html;			
	}
	
	/**
	 * Fix up references to other project notes
	 * 
	 *
	 * @return     mixed
	 */
	public function parseNoteRefs( $page, $projectid, $masterscope = '', $publication = NULL, $html = '') 
	{
		if (!$page)
		{
			return $html;
		}
		
		if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php');
		}
		else
		{
			return $html;
		}
		
		$database =& JFactory::getDBO();
		$pagename = $page->pagename;
		
		// Change all relative links to point to correct locations
		$weed = DS . $masterscope . DS;
		
		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		if (preg_match_all("/$regexp/siU", $html, $matches)) 
		{    
			foreach ($matches[2] as $match)
			{
				$pagename = str_replace($weed, '', $match);
				$pagename = trim(str_replace('view', '', $pagename), DS);

				$part  = dirname($pagename);
				$scope = $masterscope;
				$scope.= $part ? DS . $part : '';
				
				$objSt = new ProjectPubStamp( $database );

				// Get page id
				$page = new WikiPage( $database );		
				if ($page->load( basename($pagename), $scope)) 
				{								
					$pubstamp = NULL;
					
					// Build reference
					$reference = array(
						'pageid'   => $page->id,
						'pagename' => $page->pagename,
						'revision' => NULL
					);

					if ($objSt->registerStamp($projectid, json_encode($reference), 'notes'))
					{
						$pubstamp = $objSt->stamp;	
					}
					
					if ($publication)
					{
						$html = str_replace($weed. $pagename . DS . 'view', DS . 'publications' . DS . $publication->id 
								. DS . 'wiki?s=' . $pubstamp, $html );
					}
					else
					{
						$html = str_replace($weed. $pagename . DS . 'view', DS . 'projects' . DS . 'get?s=' . $pubstamp, $html );
					}
				}
			}
		}
		
		return $html;
	}
	
	/**
	 * Fix up internal file references
	 * 
	 *
	 * @return     mixed
	 */
	public function parseProjectFileRefs( $page, $pagetext, $projectid, $alias, $publication = NULL, $html = '', $copy = false) 
	{
		if (!$page || !$pagetext || !$projectid || !$alias)
		{
			return $html;
		}
		
		// Load component configs
		$config =& JComponentHelper::getParams( 'com_projects' );
		
		// Get project path
		$projectPath = ProjectsHelper::getProjectPath(
			$alias, 
			$config->get('webpath', 0),
			$config->get('offroot', 0)
		);
		
		$database =& JFactory::getDBO();
		
		// Get wiki upload path
		$previewPath = ProjectsHelper::getWikiPath($page->id);
		
		if ($copy == true)
		{
			// Get joomla libraries
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');
		}
		
		// Inlcude public stamps class
		if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php');
		}
		else
		{
			return $html;
		}
		
		// Get publication path
		if ($publication)
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'helper.php');
			
			$pubconfig =& JComponentHelper::getParams( 'com_publications' );
			$base_path = $pubconfig->get('webpath');
			$pubPath = PublicationHelper::buildPath($publication->id, $publication->version_id, $base_path, 'wikicontent', $root = 0);
			
			if (!is_dir(JPATH_ROOT . $pubPath))
			{
				if (!JFolder::create( JPATH_ROOT . $pubPath, 0777 )) 
				{
					return $html;
				}
			}
			
			$previewPath = $pubPath;			
		}
		
		// Parse files
		preg_match_all("'File\\(.*?\\)'si", $pagetext, $files);
		if (!empty($files))
		{
			$files = $files[0];
								
			foreach ($files as $file)
			{
				$ibody = str_replace('File(' , '', $file);
				$ibody = str_replace(')' , '', $ibody);
				$args  = explode(',', $ibody);
				$file  = array_shift($args);

				$fpath = $projectPath . DS . $file;
				
				$pubstamp = NULL;
				
				if (is_file( $fpath ) && $copy == true && !is_file(JPATH_ROOT . $previewPath . DS . $file))
				{
					// Copy if not there
					if (!is_dir(JPATH_ROOT . $previewPath . DS . dirname($file)))
					{
						if (!JFolder::create( JPATH_ROOT . $previewPath . DS . dirname($file), 0777 )) 
						{
							return $html;
						}
					}

					JFile::copy($fpath, JPATH_ROOT . $previewPath . DS . $file);
				}
				
				// Get public stamp for file
				$objSt = new ProjectPubStamp( $database );
				if (is_file( $fpath ) && !$publication)
				{
					// Build reference
					$reference = array(
						'file'   => $file,
						'disp' 	 => 'attachment'
					);
					
					if ($objSt->registerStamp($projectid, json_encode($reference), 'files'))
					{
						$pubstamp = $objSt->stamp;
					}
				}
				elseif ($publication)
				{
					// Build reference
					$reference = array(
						'pid'	 => $publication->id,
						'vid'	 => $publication->version_id,
						'path'   => $file,
						'folder' => 'wikicontent',
						'disp'	 => 'attachment'
					);
					
					if ($objSt->registerStamp($projectid, json_encode($reference), 'publications'))
					{
						$pubstamp = $objSt->stamp;
					}
				}				
				
				if ($pubstamp)
				{
					$link = JRoute::_('index.php?option=com_projects' . a . 'task=get') . '/?s=' . $pubstamp;
				}
				else
				{
					$link = $previewPath . DS . $file;
				}
				
				// Replace not found error				
				$href = '<a href="' . $link . '">' . basename($file) . '</a>';				
				$fname = preg_quote($file, '/');
				$html = preg_replace("/\\(file:". $fname . " not found\\)/", $href, $html);			
				
				// Replace reference
				$replace = $page->scope. DS . $page->pagename . DS . 'File:' . $file;
				$replace = preg_quote($replace, '/');
				$html = preg_replace("/\/projects\/$replace/", $link, $html);
				
				// Replace image reference
				$replace = $page->scope. DS . $page->pagename . DS . 'Image:' . $file;
				$replace = preg_quote($replace, '/');
				$html = preg_replace("/\/projects\/$replace/", $link, $html);
			}
		}
		
		return $html;			
	}
}
