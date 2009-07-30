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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_groups_forum' );

//-----------

class plgGroupsForum extends JPlugin
{
	function plgGroupsForum(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'forum' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onGroupAreas( $authorized )
	{
		/*if (!$authorized) {
			$areas = array();
		} else {*/
			$areas = array(
				'forum' => JText::_('GROUPS_FORUM')
			);
		//}

		return $areas;
	}

	//-----------

	function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $areas=null )
	{
		$return = 'html';

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onGroupAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onGroupAreas( $authorized ) ) )) {
				$return = '';
			}
		}
		
		// Are we on the overview page?
		if ($areas[0] == 'overview') {
			$return = 'metadata';
		}
		
		// The output array we're returning
		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'dashboard'=>''
		);

		// Do we need to return any data?
		if ($return != 'html' && $return != 'metadata') {
			return $arr;
		}
		
		ximport('xforum');
		
		$this->group = $group;
		$this->option = $option;
		$this->limitstart = $limitstart;
		$this->limit = $limit;
		$this->authorized = $authorized;
		
		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html') {
			$html = '';
			
			if ($this->getError()) {
				$html .= $this->getError();
			}
			
			if (!$action) {
				$t = JRequest::getInt( 'topic', 0 );
				if ($t) {
					$action = 'topic';
				}
			}
			
			switch ($action) 
			{
				case 'newtopic':    $html .= $this->edittopic();   break;
				case 'savetopic':   $html .= $this->savetopic();   break;
				case 'deletetopic': $html .= $this->deletetopic(); break;
				case 'edittopic':   $html .= $this->edittopic();   break;
				case 'reply':       $html .= $this->reply();       break;
				case 'savereply':   $html .= $this->savereply();   break;
				case 'deletereply': $html .= $this->deletereply(); break;
				case 'topic':       $html .= $this->topic();       break;
				case 'topics':      $html .= $this->topics();      break;
				
				default: $html .= $this->topics(); break;
			}
			
			$arr['html'] = $html;
		} else {
			$database =& JFactory::getDBO();
			
			/*
			// Get a count of the number of events
			$forum = new XForum( $database );
			$num = $forum->getCount();

			// Build the HTML meant for the "profile" tab's metadata overview
			$metadata = '<p class="discussions"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'active=forum').'">'.JText::sprintf('NUMBER_DISCUSSIONS',$num).'</a></p>'.n;
			*/

			$tables = $database->getTableList();
			$table = $database->_table_prefix.'xforum';
			if (!in_array($table,$tables)) {
				$database->setQuery( "CREATE TABLE `#__xforum` (
				  `id` int(11) NOT NULL auto_increment,
				  `topic` varchar(255) default NULL,
				  `comment` text,
				  `created` datetime NOT NULL default '0000-00-00 00:00:00',
				  `created_by` int(11) default '0',
				  `state` tinyint(3) NOT NULL default '0',
				  `sticky` tinyint(2) NOT NULL default '0',
				  `parent` int(11) NOT NULL default '0',
				  `hits` int(11) default '0',
				  `group` int(11) default '0',
				  `access` tinyint(2) default '4',
				  PRIMARY KEY  (`id`),
				  FULLTEXT KEY `question` (`comment`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;" );
				if (!$database->query()) {
					echo $database->getErrorMsg();
					return false;
				}
			}

			// Incoming
			$filters = array();
			$filters['authorized'] = $this->authorized;
			$filters['limit'] = $this->limit;
			$filters['start'] = 0;
			$filters['group'] = $this->group->get('gidNumber');
			$filters['sticky'] = false;

			// Initiate a forum object
			$forum = new XForum( $database );

			$num = $forum->getCount( $filters );
			
			// Get records
			$rows = $forum->getRecords( $filters );

			// Output HTML
			$arr['metadata'] = '<a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'active=forum').'">'.JText::sprintf('NUMBER_DISCUSSIONS',$num).'</a>'.n;
			$arr['dashboard'] = $this->topicsHtml( $this->group, $forum, 0, $rows, null, $this->authorized, '', $this->getError() );
		}

		// Return the output
		return $arr;
	}
	
	//-----------
	
	function topics() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$filters = array();
		$filters['authorized'] = $this->authorized;
		$filters['limit'] = $this->limit;
		$filters['start'] = $this->limitstart;
		$filters['group'] = $this->group->get('gidNumber');
		$filters['search'] = JRequest::getVar('q', '');
		
		// Initiate a forum object
		$forum = new XForum( $database );
		
		// Get record count
		$total = $forum->getCount( $filters );
		
		// Get records
		$rows = $forum->getRecords( $filters );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );
		
		// Output HTML
		return $this->topicsHtml( $this->group, $forum, $total, $rows, $pageNav, $this->authorized, $filters['search'], $this->getError() );
	}
	
	//-----------
	
	function topic($id=0) 
	{
		$database =& JFactory::getDBO();

		// Incoming
		$filters = array();
		$filters['authorized'] = $this->authorized;
		$filters['limit']  = $this->limit;
		$filters['start']  = $this->limitstart;
		$filters['parent'] = ($id) ? $id : JRequest::getInt( 'topic', 0 );
		
		if ($filters['parent'] == 0) {
			return $this->topics();
		}
		
		// Initiate a forum object
		$forum = new XForum( $database );
		
		// Load the topic
		$forum->load( $filters['parent'] );
		if ($forum->access == 4 && !$this->authorized) {
			return $this->topics();
		}
		
		// Get reply count
		$total = $forum->getCount( $filters );
		
		// Get replies
		$rows = $forum->getRecords( $filters );
		
		// Record the hit
		$forum->hit();
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );
		
		// Output HTML
		return $this->topicHtml( $this->group, $forum, $total, $rows, $pageNav, $this->authorized );
	}
	
	//-----------
	
	function deletetopic() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'topic', 0 );
		if (!$id) {
			return $this->topics();
		}
		
		// Initiate a forum object
		$forum = new XForum( $database );
		
		if (!$forum->deleteReplies( $id )) {
			$this->setError( $forum->getError() );
			return $this->topics();
		}

		if (!$forum->delete( $id )) {
			$this->setError( $forum->getError() );
		}
		
		return $this->topics();
	}
	
	//-----------
	
	function edittopic()
	{
		// Is the user logged in?
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('GROUPS_LOGIN_NOTICE') );
			return $this->topics();
		}

		// Incoming
		$id = JRequest::getInt( 'topic', 0 );

		$database =& JFactory::getDBO();
		
		$row = new XForum( $database );
		$row->load( $id );
		if (!$id) {
			// New review, get the user's ID
			$row->created_by = $juser->get('id');
		} else {
			// Editing a review, do some prep work
			$row->comment = str_replace('<br />','',$row->comment);
		}

		return $this->topicForm( $this->group, $row, $this->option, $this->getError() );
	}
	
	//-----------
	
	function reply()
	{
		// Is the user logged in?
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('GROUPS_LOGIN_NOTICE') );
			return $this->topics();
		}

		// Incoming
		$parent = JRequest::getInt( 'topic', 0 );
		if (!$parent) {
			$this->setError( JText::_('FORUM_MISSING_TOPIC') );
			return $this->topics();
		}

		$database =& JFactory::getDBO();
		
		$row = new XForum( $database );
		$row->load();
		$row->created_by = $juser->get('id');
		$row->parent = $parent;

		return $this->topicForm( $this->group, $row, $this->option );
	}
	
	//-----------
	
	function savetopic() 
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('GROUPS_LOGIN_NOTICE') );
			return;
		}
		
		$database =& JFactory::getDBO();
		
		$row = new XForum( $database );
		if (!$row->bind( $_POST )) {
			$this->setError( $row->getError() );
			exit();
		}
		
		$row->id = JRequest::getInt( 'topic_id', 0 );
		
		if (!$row->id) {
			$row->created = date( 'Y-m-d H:i:s', time() );  // use gmdate() ?
			$row->created_by = $juser->get('id');
		}
		
		if (trim($row->topic) == '') {
			$row->topic = substr($row->comment, 0, 70);
			if (strlen($row->topic >= 70)) {
				$row->topic .= '...';
			}
		}
		
		// Check content
		if (!$row->check()) {
			$this->setError( $row->getError() );
			return $this->edittopic();
		}

		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return $this->edittopic();
		}

		if ($row->parent) {
			return $this->topic($row->parent);
		} else {
			return $this->topics();
		}
	}
	
	//-----------
	
	function topicsHtml( $group, $forum, $total, $rows, $pageNav, $authorized, $search='', $error='' ) 
	{
		$html  = '';
		if ($error) {
			$html .= GroupsHtml::error( $error );
		}
		$aside  = '<fieldset>'.n;
		$aside .= t.'<label>'.n;
		$aside .= t.t.JText::_('FORUM_SEARCH').n;
		$aside .= t.t.'<input type="text" name="q" value="'.htmlentities($search, ENT_QUOTES).'" />'.n;
		$aside .= t.'</label>'.n;
		$aside .= t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		$aside .= '</fieldset>'.n;
		$aside .= '<span class="add"><a href="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum'.a.'task=newtopic').'">'.JText::_('NEW_TOPIC').'</a></span>'.n;
		
		$html .= '<table id="forum-list">'.n;
		if ($pageNav) {
			$html .= t.'<caption>'.$aside.'</caption>'.n;
		}
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th>'.JText::_('FORUM_TOPIC').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('FORUM_REPLIES').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('FORUM_AUTHOR').'</th>'.n;
		//$html .= t.t.t.'<th>'.JText::_('FORUM_VIEWS').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('FORUM_LAST_POST').'</th>'.n;
		if ($authorized == 'admin' || $authorized == 'manager') {
			$html .= t.t.t.'<th colspan="2">'.JText::_('FORUM_CONTROLS').'</th>'.n;
		}
		$html .= t.t.'</tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.'<tbody>'.n;
		if ($rows) {
			$cls = 'even';
			
			foreach ($rows as $row) 
			{
				$name = JText::_('UNKNOWN');
				$xuser =& JUser::getInstance( $row->created_by );
				if (is_object($xuser) && $xuser->get('name')) {
					$name = $xuser->get('name');
				}
				
				$lpname = '';
				$lastpost = $forum->getLastPost( $row->id );
				if ($lastpost && count($lastpost) > 0) {
					$lastpost = $lastpost[0];
					
					$vuser =& JUser::getInstance( $lastpost->created_by );
					if (is_object($vuser) && $vuser->get('name')) {
						$lpname = $vuser->get('name');
					}
				}

				$forumpages = new XForumPagination( $row->replies, 0, $this->limit, $row->id );
				
				$cls = (($cls == 'even') ? 'odd' : 'even');
				
				$html .= t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.t.'<td>';
				$html .= ($row->sticky == 1) ? t.t.t.t.'<strong>'.JText::_('FORUM_STICKY').'</strong> ' : t.t.t.t.'';
				$html .= '<a href="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum'.a.'topic='.$row->id).'">'.stripslashes($row->topic).'</a>'.n;
				$html .= ($row->replies > $this->limit) ? t.t.t.t.'<br /><span class="forum-pages">('.$forumpages->getPagesLinks().')</span>'.n : '';
				$html .= t.t.t.'</td>'.n;
				$html .= t.t.t.'<td>'.$row->replies.'</td>'.n;
				$html .= t.t.t.'<td><a href="'.JRoute::_('index.php?option=com_members'.a.'id='.$row->created_by).'">'.$name.'</a><br />';
				$html .= '<span class="post-date"><a href="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum'.a.'topic='.$row->id).'">'.$row->created.'</a></span>';
				$html .= '</td>'.n;
				//$html .= t.t.t.'<td>'.$row->hits.'</th>'.n;
				$html .= t.t.t.'<td>';
				if ($lpname) {
					$html .= '<a href="'.JRoute::_('index.php?option=com_members'.a.'id='.$lastpost->created_by).'">'.$lpname.'</a><br />';
					$html .= '<span class="lastpost-date"><a href="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum'.a.'topic='.$row->id.'#c'.$lastpost->id).'">'.$lastpost->created.'</a></span>';
				}
				$html .= '</td>'.n;
				if ($authorized == 'admin' || $authorized == 'manager') {
					$html .= t.t.t.'<td><a class="delete" href="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum'.a.'topic='.$row->id.a.'task=deletetopic').'" title="'.JText::_('FORUM_DELETE').'">'.JText::_('FORUM_DELETE').'</a></td>'.n;
					$html .= t.t.t.'<td><a class="edit" href="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum'.a.'topic='.$row->id.a.'task=edittopic').'" title="'.JText::_('FORUM_EDIT').'">'.JText::_('FORUM_EDIT').'</a></td>'.n;
				}
				$html .= t.t.'</tr>'.n;
			}
		} else {
			$html .= t.t.'<tr class="odd">'.n;
			$html .= t.t.t.'<td colspan="5">'.JText::_('FORUM_NO_TOPICS_FOUND').'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;
		
		$out = '';
		if ($pageNav) {
			$html .= $pageNav->getListFooter();
			
			$out .= '<form action="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum').'" method="get">'.n;
			//$out .= GroupsHtml::div( $aside, 'aside' );
			//$out .= GroupsHtml::div( $html, 'subject' );
		}
		$out .= $html;
		if ($pageNav) {
			$out .= '</form>'.n;
		}
		
		return $out;
	}
	
	//-----------
	
	function topicHtml( $group, $forum, $total, $rows, $pageNav, $authorized ) 
	{
		$html  = '<h3>'.stripslashes($forum->topic).'</h3>'.n;
		$html .= '<ol class="comments">'.n;
		if ($rows) {
			ximport('wiki.parser');

			$p = new WikiParser( $group->get('cn'), $this->option, 'group'.DS.'forum', 'group', $group->get('gidNumber'), '' );
			
			$o = 'even';
			
			foreach ($rows as $row) 
			{
				$name = JText::_('UNKNOWN');
				$xuser =& JUser::getInstance( $row->created_by );
				if (is_object($xuser) && $xuser->get('name')) {
					$name = $xuser->get('name');
				}
				
				$comment = $p->parse( n.stripslashes($row->comment) );
				
				$o = ($o == 'odd') ? 'even' : 'odd';
				
				$html .= t.'<li class="comment '.$o.'" id="c'.$row->id.'">'.n;
				$html .= t.t.'<a name="c'.$row->id.'"></a>'.n;
				$html .= t.t.'<dl class="comment-details">'.n;
				$html .= t.t.t.'<dt class="type"><span class="plaincomment"><span>'.JText::sprintf('COMMENT').'</span></span></dt>'.n;
				// Last parameter is time offset - if not set, it will subtract/add hours based on UTC
				$html .= t.t.t.'<dd class="date">'.JHTML::_('date',$row->created, '%d %b, %Y', 0).'</dd>'.n;
				$html .= t.t.t.'<dd class="time">'.JHTML::_('date',$row->created, '%I:%M %p', 0).'</dd>'.n;
				$html .= t.t.'</dl>'.n;
				$html .= t.t.'<div class="cwrap">'.n;
				$html .= t.t.t.'<p class="name"><strong><a href="'.JRoute::_('index.php?option=com_members'.a.'id='.$row->created_by).'">'.$name.'</a></strong> '.JText::_('SAID').':</p>'.n;
				$html .= t.t.t.'<p>'.$comment.'</p>'.n;
				$html .= t.t.'</div>'.n;
				$html .= t.'</li>'.n;
			}
		} else {
			$html .= t.'<li>'.n;
			$html .= t.t.'<p>'.JText::_('FORUM_NO_REPLIES_FOUND').'</p>'.n;
			$html .= t.'</li>'.n;
		}
		$html .= '</ol>'.n;
		//$html .= '<p class="add"><a href="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum'.a.'task=reply'.a.'topic='.$forum->id).'">'.JText::_('REPLY').'</a></p>'.n;
		$html .= $pageNav->getListFooter();
		
		$aside = '<p class="add"><a href="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum'.a.'task=newtopic').'">'.JText::_('NEW_TOPIC').'</a></p>'.n;
		
		$out  = '<form action="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum').'" method="get">'.n;
		$out .= GroupsHtml::div( $aside, 'aside' );
		$out .= GroupsHtml::div( $html, 'subject' );
		$out .= '</form>'.n;
		$out .= '<div class="clear"></div>'.n;
		if ($authorized) {
			$faside = '<p>Comments support <a href="'.JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting').'">Wiki Formatting</a>. Please keep comments polite and on topic. Offensive posts may be removed.</p>'.n;

			$frm  = '<form action="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum').'" method="post" id="hubForm">'.n;
			$frm .= t.'<fieldset>'.n;
			$frm .= GroupsHtml::hed(4,'<a name="commentform"></a>'.JText::_('ADD_COMMENT')).n;
			$frm .= t.t.'<label>'.n;
			$frm .= t.t.t.JText::_('FORUM_FORM_COMMENTS').':'.n;
			$frm .= t.t.t.'<textarea name="comment" id="forum_comments" rows="15" cols="35"></textarea>'.n;
			$frm .= t.t.'</label>'.n;
			$frm .= t.t.'<input type="hidden" name="option" value="'. $this->option .'" />'.n;
			$frm .= t.t.'<input type="hidden" name="gid" value="'. $group->get('cn') .'" />'.n;
			$frm .= t.t.'<input type="hidden" name="task" value="savetopic" />'.n;
			$frm .= t.t.'<input type="hidden" name="parent" value="'.$forum->id.'" />'.n;
			$frm .= t.t.'<input type="hidden" name="topic_id" value="" />'.n;
			$frm .= t.t.'<input type="hidden" name="active" value="forum" />'.n;
			$frm .= t.t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
			$frm .= t.'</fieldset>'.n;
			$frm .= '</form>'.n;
			
			$rp  = GroupsHtml::div( $faside, 'aside' );
			$rp .= GroupsHtml::div( $frm, 'subject' );
			
			$out .= '<hr />'.n;
			$out .= GroupsHtml::div( $rp, 'section' );
		}
		
		return $out;
	}

	//-----------
	
	public function topicForm( $group, $row, $option, $error='' ) 
	{
		if ($row->parent) {
			$title = JText::_('ADD_REPLY_TO_TOPIC');
		} else {
			if ($row->id) {
				$title = JText::_('EDIT_TOPIC');
			} else {
				$row->access = 4;
				$title = JText::_('NEW_TOPIC');
			}
		}
		
		$html  = '<form action="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum').'" method="post" id="hubForm">'.n;
		if ($error) {
			$html .= GroupsHtml::error( $error );
		}
		$html .= t.'<div class="explaination">'.n;
		if (!$row->parent && $row->id) {
			//$html .= t.t.'<p><a href="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum').'">All topics</a></p>'.n;
			$html .= t.t.'<p class="add"><a href="'.JRoute::_('index.php?option='.$this->option.a.'gid='.$group->get('cn').a.'active=forum'.a.'task=newtopic').'">'.JText::_('NEW_TOPIC').'</a></p>'.n;
		}
		$html .= t.t.'<p>Comments support <a href="'.JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting').'">Wiki Formatting</a>. Please keep comments polite and on topic. Offensive posts may be removed.</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= GroupsHtml::hed(3,'<a name="topicform"></a>'.$title).n;
		
		if ($row->parent) {
			$html .= t.t.t.'<input type="hidden" name="sticky" id="forum_sticky" value="'. $row->sticky .'" />'.n;
			$html .= t.t.t.'<input type="hidden" name="topic" id="forum_topic" value="'. htmlentities(stripslashes($row->topic), ENT_QUOTES) .'" />'.n;
		} else {
			if ($this->authorized == 'admin' || $this->authorized == 'manager') {
				$html .= t.t.'<label>'.n;
				$html .= t.t.t.'<input class="option" type="checkbox" name="sticky" id="forum_sticky"';
				$html .= ($row->sticky == 1) ? ' checked="checked"' : '';
				$html .= ' /> '.n;
				$html .= t.t.t.JText::_('FORUM_FORM_STICKY').n;
				$html .= t.t.'</label>'.n;	
			} else {
				$html .= t.t.t.'<input type="hidden" name="sticky" id="forum_sticky" value="'. $row->sticky .'" />'.n;
			}
			
			$html .= t.t.'<label>'.n;
			$html .= t.t.t.'<input class="option" type="checkbox" name="access" id="forum_access"';
			$html .= ($row->access != 4) ? ' checked="checked"' : '';
			$html .= ' /> '.n;
			$html .= t.t.t.JText::_('FORUM_FORM_ACCESS').n;
			$html .= t.t.'</label>'.n;

			$html .= t.t.'<label>'.n;
			$html .= t.t.t.JText::_('FORUM_FORM_TOPIC').':'.n;
			$html .= t.t.t.'<input type="text" name="topic" id="forum_topic" value="'. htmlentities(stripslashes($row->topic), ENT_QUOTES) .'" size="38" />'.n;
			$html .= t.t.'</label>'.n;
		}

		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('FORUM_FORM_COMMENTS').':'.n;
		$html .= t.t.t.'<textarea name="comment" id="forum_comments" rows="15" cols="35">'. stripslashes($row->comment) .'</textarea>'.n;
		$html .= t.t.'</label>'.n;

		$html .= t.t.'<input type="hidden" name="created" value="'. $row->created .'" />'.n;
		$html .= t.t.'<input type="hidden" name="created_by" value="'. $row->created_by .'" />'.n;
		$html .= t.t.'<input type="hidden" name="parent" value="'. $row->parent .'" />'.n;
		$html .= t.t.'<input type="hidden" name="topic_id" value="'. $row->id .'" />'.n;
		$html .= t.t.'<input type="hidden" name="group" value="'. $group->get('gidNumber') .'" />'.n;
		
		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		//$html .= t.t.'<input type="hidden" name="task" value="view" />'.n;
		$html .= t.t.'<input type="hidden" name="gid" value="'. $group->get('cn') .'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="savetopic" />'.n;
		$html .= t.t.'<input type="hidden" name="active" value="forum" />'.n;
		
		$html .= t.'</fieldset>'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= t.t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;

		return $html;
	}

	//-----------
	
	function onGroupDelete( $group ) 
	{
		ximport('xforum');
		$database =& JFactory::getDBO();
		
		$results = $this->getForumIDs( $group->get('cn') );

		$log = 'Events: ';
		if ($results && count($results) > 0) {
			// Initiate a forum object
			$forum = new XForum( $database );
			
			foreach ($results as $result)
			{
				$forum->deleteReplies( $result->id );
				$forum->delete( $result->id );
			
				$log .= $result->id.' '.n;
			}
		} else {
			$log .= 'none'.n;
		}
		
		return $log;
	}
	
	//-----------
	
	function onGroupDeleteCount( $group ) 
	{
	}
	
	//-----------
	
	function getForumIDs( $gid=NULL )
	{
		if (!$gid) {
			return array();
		}
		$database =& JFactory::getDBO();
		
		// Initiate a forum object
		$forum = new XForum( $database );
		
		// Get records
		$filters = array();
		$filters['start'] = 0;
		$filters['group'] = $gid;
		
		return $forum->getRecords( $filters );
	}
}