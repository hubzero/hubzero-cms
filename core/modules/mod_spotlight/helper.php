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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Spotlight;

use Hubzero\Module\Module;
use Filesystem;
use Component;
use Request;
use Route;
use Date;
use Lang;
use User;

/**
 * Module class for showing content spotlight
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}

	/**
	 * Get module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		include_once(Component::path('com_resources') . DS . 'tables' . DS . 'resource.php');
		include_once(Component::path('com_members') . DS . 'tables' . DS . 'profile.php');
		include_once(Component::path('com_members') . DS . 'tables' . DS . 'association.php');
		include_once(Component::path('com_answers') . DS . 'tables' . DS . 'question.php');
		include_once(Component::path('com_answers') . DS . 'tables' . DS . 'response.php');
		include_once(Component::path('com_blog') . DS . 'tables' . DS . 'entry.php');
		include_once(Component::path('com_blog') . DS . 'tables' . DS . 'comment.php');

		$this->database = \App::get('db');

		// Get the admin configured settings
		$filters = array();
		$filters['limit'] = 5;
		$filters['start'] = 0;

		// featured items
		$tbls = array('resources', 'profiles');

		$spots    = array();
		$spots[0] = trim($this->params->get('spotone'));
		$spots[1] = trim($this->params->get('spottwo'));
		$spots[2] = trim($this->params->get('spotthree'));
		$spots[3] = trim($this->params->get('spotfour'));
		$spots[4] = trim($this->params->get('spotfive'));
		$spots[5] = trim($this->params->get('spotsix'));
		$spots[6] = trim($this->params->get('spotseven'));

		$numspots = $this->params->get('numspots', 3);

		// some collectors
		$activespots = array();
		$rows = array();

		// styling
		$cls = trim($this->params->get('moduleclass_sfx'));
		$txtLength = trim($this->params->get('txt_length'));

		$start = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . ' 00:00:00';
		$end   = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . ' 23:59:59';

		$this->html = '';
		$k = 1;
		$out = '';

		for ($i = 0, $n = $numspots; $i < $numspots; $i++)
		{
			$spot = $spots[$i];
			if ($spot == '')
			{
				continue;
			}

			$row = null;
			$out = '';
			$tbl = '';

			$tbl = ($spot == 'tools' || $spot == 'nontools') ? 'resources' : '';
			$tbl = $spot == ('members') ? 'profiles' : $tbl;
			$tbl = $spot == ('topics')  ? 'topics'   : $tbl;
			$tbl = $spot == ('itunes')  ? 'itunes'   : $tbl;
			$tbl = $spot == ('answers') ? 'answers'  : $tbl;
			$tbl = $spot == ('blog')    ? 'blog'     : $tbl;
			$tbl = (!$tbl) ? array_rand($tbls, 1) : $tbl;

			// we need to randomly choose one
			switch ($tbl)
			{
				case 'resources':
					// Initiate a resource object
					$rr = new \Components\Resources\Tables\Resource($this->database);
					$filters['start'] = 0;
					$filters['type'] = $spot;
					$filters['sortby'] = 'random';
					$filters['minranking'] = trim($this->params->get('minranking'));
					$filters['tag'] = ($spot == 'tools') ? trim($this->params->get('tag')) : ''; // tag is set for tools only

					// Get records
					$rows[$spot] = (isset($rows[$spot])) ? $rows[$spot] : $rr->getRecords($filters, false);
				break;

				case 'profiles':
					// No - so we need to randomly choose one
					$filters['start'] = 0;
					$filters['sortby'] = "RAND()";
					$filters['search'] = '';
					$filters['state'] = 'public';
					$filters['authorized'] = false;
					$filters['tag'] = '';
					$filters['contributions'] = trim($this->params->get('min_contributions'));
					$filters['show'] = trim($this->params->get('show'));

					$mp = new \Components\Members\Tables\Profile($this->database);

					// Get records
					$rows[$spot] = (isset($rows[$spot])) ? $rows[$spot] : $mp->getRecords($filters, false);
				break;

				case 'topics':
					// No - so we need to randomly choose one
					$topics_tag = trim($this->params->get('topics_tag'));
					$query  = "SELECT DISTINCT w.id, w.pagename, w.title ";
					$query .= " FROM #__wiki_page AS w ";
					if ($topics_tag)
					{
						$query .= " JOIN #__tags_object AS RTA ON RTA.objectid=w.id AND RTA.tbl='wiki' ";
						$query .= " INNER JOIN #__tags AS TA ON TA.id=RTA.tagid ";
					}
					else
					{
						$query .= ", #__wiki_version AS v ";
					}
					$query .= " WHERE w.access!=1 AND w.scope = ''  ";
					if ($topics_tag)
					{
						$query .= " AND (TA.tag='" . $topics_tag . "' OR TA.raw_tag='" . $topics_tag . "') ";
					}
					else
					{
						$query .= " AND v.pageid=w.id AND v.approved = 1 AND v.pagetext != '' ";
					}
					$query .= " ORDER BY RAND() ";
					$this->database->setQuery($query);

					$rows[$spot] = (isset($rows[$spot])) ? $rows[$spot] : $this->database->loadObjectList();
				break;

				case 'itunes':
					// Initiate a resource object
					$rr = new \Components\Resources\Tables\Resource($this->database);
					$filters['start'] = 0;
					$filters['sortby'] = 'random';
					$filters['tag'] = trim($this->params->get('itunes_tag'));

					// Get records
					$rows[$spot] = (isset($rows[$spot])) ? $rows[$spot] : $rr->getRecords($filters, false);
				break;

				case 'answers':
					$query  = "SELECT C.id, C.subject, C.question, C.created, C.created_by, C.anonymous  ";
					$query .= ", (SELECT COUNT(*) FROM #__answers_responses AS a WHERE a.state!=2 AND a.question_id=C.id) AS rcount ";
					$query .= " FROM #__answers_questions AS C ";
					$query .= " WHERE C.state=0 ";
					$query .= " AND (C.reward > 0 OR C.helpful > 0) ";
					$query .= " ORDER BY RAND() ";
					$this->database->setQuery($query);

					$rows[$spot] = (isset($rows[$spot])) ? $rows[$spot] : $this->database->loadObjectList();
				break;

				case 'blog':
					$filters = array();
					$filters['limit'] = 1;
					$filters['start'] = 0;
					$filters['state'] = 'public';
					$filters['order'] = "RAND()";
					$filters['search'] = '';
					$filters['scope'] = 'member';
					$filters['group_id'] = 0;
					$filters['authorized'] = false;
					$filters['sql'] = '';
					$mp = new \Components\Blog\Tables\Entry($this->database);
					$entry = $mp->getRecords($filters);

					$rows[$spot] = (isset($rows[$spot])) ? $rows[$spot] : $entry;
				break;
			}

			if ($rows && count($rows[$spot]) > 0)
			{
				$row = $rows[$spot][0];
			}

			// make sure we aren't pulling the same item
			if ($k != 1 && in_array($spot, $activespots) && $rows && count($rows[$spot]) > 1)
			{
				$row = (count($rows[$spot]) < $k) ? $rows[$spot][$k-1] : $rows[$spot][1]; // get the next one
			}

			// pull info
			if ($row)
			{
				$out = $this->_composeEntry($row, $tbl, $txtLength);
				$itemid = $this->_composeEntry($row, $tbl, 0, 1);
				$activespots[] = $spot;
			}

			// Did we get any results?
			if ($out)
			{
				$this->html .= '<li class="spot_' . $k . '">' . $out . '</li>' . "\n";

				$k++;
			}
		}

		// Output HTML
		require $this->getLayoutPath();
	}

	/**
	 * Format an entry
	 *
	 * @param      object  $row       Database row
	 * @param      string  $tbl       Format type
	 * @param      number  $txtLength Max text length to display
	 * @param      integer $getid     Just return the ID or not
	 * @return     string HTML
	 */
	private function _composeEntry($row, $tbl, $txtLength=100, $getid=0)
	{
		$yearFormat = 'Y';
		$monthFormat = 'm';

		$out = '';

		// Do we have a picture?
		$thumb = '';

		switch ($tbl)
		{
			case 'profiles':
				if ($getid)
				{
					return $row->uidNumber;
				}

				// Load their bio
				$profile = \Hubzero\User\Profile::getInstance($row->uidNumber);

				$title = $row->name;
				if (!trim($title))
				{
					$title = $row->givenName . ' ' . $row->surname;
				}
				$out .= '<span class="spotlight-img"><a href="' . Route::url('index.php?option=com_members&id=' . $row->uidNumber) . '"><img width="30" height="30" src="' . $profile->getPicture() . '" alt="' . htmlentities($title) . '" /></a></span>' . "\n";
				$out .= '<span class="spotlight-item"><a href="' . Route::url('index.php?option=com_members&id=' . $row->uidNumber) . '">' . $title . '</a></span>, ' . $row->organization . "\n";
				$out .= ' - ' . Lang::txt('Contributions') . ': ' . $this->_countContributions($row->uidNumber) . "\n";
				$out .= '<div class="clear"></div>'."\n";
			break;

			case 'blog':
				$thumb = trim($this->params->get('default_blogpic', '/core/modules/mod_spotlight/assets/img/default.gif'));
				if ($thumb == '/modules/mod_spotlight/default.gif')
				{
					$thumb = '/core/modules/mod_spotlight/assets/img/default.gif';
				}

				$profile = \Hubzero\User\Profile::getInstance($row->created_by);

				if ($getid)
				{
					return $row->id;
				}
				if (!$row->title)
				{
					$out = '';
				}
				else
				{
					$out .= '<span class="spotlight-img"><a href="' . Route::url('index.php?option=com_members&id=' . $row->created_by . '&active=blog&task=' . Date::of($row->publish_up)->toLocal($yearFormat) . '/' . Date::of($row->publish_up)->toLocal($monthFormat) . '/' . $row->alias) . '"><img width="30" height="30" src="' . rtrim(Request::base(true), '/') . $thumb . '" alt="' . htmlentities(stripslashes($row->title)) . '" /></a></span>'."\n";
					$out .= '<span class="spotlight-item"><a href="' . Route::url('index.php?option=com_members&id=' . $row->created_by . '&active=blog&task=' . Date::of($row->publish_up)->toLocal($yearFormat) . '/' . Date::of($row->publish_up)->toLocal($monthFormat) . '/' . $row->alias) . '">' . $row->title . '</a></span> ';
					$out .=  ' by <a href="' . Route::url('index.php?option=com_members&id=' . $row->created_by) . '">' . $profile->get('name') . '</a> - ' . Lang::txt('in Blogs') . "\n";
					$out .= '<div class="clear"></div>'."\n";
				}
			break;

			case 'topics':
				if ($getid)
				{
					return $row->id;
				}
				$url = ($row->group_cn && $row->scope) ? 'groups' . DS . $row->scope . DS . $row->pagename : 'topics' . DS . $row->pagename;

				$thumb = trim($this->params->get('default_topicpic', '/core/modules/mod_spotlight/assets/img/default.gif'));
				if ($thumb == '/modules/mod_spotlight/default.gif')
				{
					$thumb = '/core/modules/mod_spotlight/assets/img/default.gif';
				}

				$out .= '<span class="spotlight-img"><a href="' . Route::url('index.php?option=com_topics&pagename=' . $row->pagename) . '"><img width="30" height="30" src="' . rtrim(Request::base(true), '/') . $thumb . '" alt="'.htmlentities(stripslashes($row->title)) . '" /></a></span>'."\n";
				$out .= '<span class="spotlight-item"><a href="' . $url . '">'.stripslashes($row->title) . '</a></span> ';
				$out .=  ' - ' . Lang::txt('in') . ' <a href="' . Route::url('index.php?option=com_topics') . '">' . Lang::txt('Topics') . '</a>'."\n";
				$out .= '<div class="clear"></div>'."\n";
			break;

			case 'answers':
				if ($getid)
				{
					return $row->id;
				}
				$thumb = trim($this->params->get('default_questionpic', '/core/modules/mod_spotlight/assets/img/default.gif'));
				if ($thumb == '/modules/mod_spotlight/default.gif')
				{
					$thumb = '/core/modules/mod_spotlight/assets/img/default.gif';
				}

				$name = Lang::txt('Anonymous');
				if ($row->anonymous == 0)
				{
					$user = User::getInstance($row->created_by);
					if (is_object($user))
					{
						$name = $user->get('name');
					}
				}
				$out .= '<span class="spotlight-img"><a href="' . Route::url('index.php?option=com_answers&task=question&id=' . $row->id) . '"><img width="30" height="30" src="' . rtrim(Request::base(true), '/') . $thumb . '" alt="'.htmlentities(stripslashes($row->subject)) . '" /></a></span>'."\n";
				$out .= '<span class="spotlight-item"><a href="' . Route::url('index.php?option=com_answers&task=question&id=' . $row->id) . '">' . stripslashes($row->subject) . '</a></span> ';
				$out .=  ' - ' . Lang::txt('asked by') . ' ' . $name . ', ' . Lang::txt('in') . ' <a href="' . Route::url('index.php?option=com_answers') . '">' . Lang::txt('Answers') . '</a>'."\n";
				$out .= '<div class="clear"></div>'."\n";
			break;

			default:
				if ($getid)
				{
					return $row->id;
				}

				if ($tbl == 'itunes')
				{
					$thumb = trim($this->params->get('default_itunespic', '/core/modules/mod_spotlight/assets/img/default.gif'));
					if ($thumb == '/modules/mod_spotlight/default.gif')
					{
						$thumb = '/core/modules/mod_spotlight/assets/img/default.gif';
					}
				}
				else
				{
					$rconfig = Component::params('com_resources');
					$path = substr(PATH_APP, strlen(PATH_ROOT)) . DS . trim($rconfig->get('uploadpath', '/site/resources'), DS);
					$path = DS . trim($path, DS);

					$path = $this->_buildPath($row->created, $row->id, $path);

					if ($row->type == 7)
					{
						include_once(Component::path('com_tools') . DS . 'tables' . DS . 'version.php');

						$tv = new \Components\Tools\Tables\Version($this->database);

						$versionid = $tv->getVersionIdFromResource($row->id, 'current');

						$picture = $this->_getToolImage($path, $versionid);
					}
					else
					{
						$picture = $this->_getImage($path);
					}

					$thumb = $path . DS . $picture;

					if (!is_file(PATH_ROOT . $thumb) or !$picture)
					{
						$thumb = DS . trim($rconfig->get('defaultpic', '/core/modules/mod_spotlight/assets/img/default.gif'), DS);
						if ($thumb == '/modules/mod_spotlight/default.gif')
						{
							$thumb = '/core/modules/mod_spotlight/assets/img/default.gif';
						}
					}
					if (substr($thumb, 0, strlen('/modules')) == '/modules'
					 || substr($thumb, 0, strlen('/components')) == '/components')
					{
						$thumb = '/core' . $thumb;
					}
					$thumb = str_replace('com_resources/assets', 'com_resources/site/assets', $thumb);
				}

				$normalized = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($row->typetitle));

				$row->typetitle = trim(stripslashes($row->typetitle));
				$row->title = stripslashes($row->title);

				$chars = strlen($row->title . $row->typetitle);
				$remaining = $txtLength - $chars;
				$remaining = ($remaining <= 0) ? 0 : $remaining;
				$titlecut  = ($remaining) ? 0 : $txtLength - strlen($row->typetitle);
				if ($titlecut)
				{
					$title = \Hubzero\Utility\String::truncate($row->title, $titlecut);
				}
				else
				{
					$title = $row->title;
				}

				// resources
				$out .= '<span class="spotlight-img">';
				$out .= "\t" . '<a href="' . Route::url('index.php?option=com_resources&id=' . $row->id) . '">' . "\n";
				$out .= "\t\t" . '<img width="30" height="30" src="' . rtrim(Request::base(true), '/') . $thumb . '" alt="' . htmlentities($row->title) . '" />' . "\n";
				$out .= "\t" . '</a>' . "\n";
				$out .= '</span>' . "\n";
				$out .= '<span class="spotlight-item">' . "\n";
				$out .= "\t" . '<a href="' . Route::url('index.php?option=com_resources&id=' . $row->id) . '">' . $title . '</a>' . "\n";
				$out .= '</span>' . "\n";
				if ($row->type == 7 && $remaining > 30)
				{
					// Show bit of description for tools
					if ($row->introtext)
					{
						$out .= ': ' . \Hubzero\Utility\String::truncate($this->_encodeHtml(strip_tags($row->introtext)), $txtLength);
					}
					else
					{
						$out .= ': ' . \Hubzero\Utility\String::truncate($this->_encodeHtml(strip_tags($row->fulltxt)), $txtLength);
					}
				}
				if ($tbl == 'itunes')
				{
					$out .=  ' - ' . Lang::txt('featured on') .' <a href="/itunes">' . Lang::txt('iTunes') . ' U</a>' . "\n";
				}
				else
				{
					$out .=  ' - ' . Lang::txt('in') . ' <a href="' . Route::url('index.php?option=com_resources&type=' . $normalized) . '">' . $row->typetitle . '</a>' . "\n";
				}
				$out .= '<div class="clear"></div>' . "\n";
			break;
		}

		return $out;
	}

	/**
	 * Get a user's average ranking
	 *
	 * @param      integer $uid User ID
	 * @return     integer
	 */
	private function _getAverageRanking($uid)
	{
		if ($uid === NULL)
		{
			 return 0;
		}

		// get average ranking of contributed resources
		$query  = "SELECT AVG (R.ranking) ";
		$query .= "FROM #__author_assoc AS AA,  #__resources AS R ";
		$query .= "WHERE AA.authorid = " . $uid . " ";
		$query .= "AND R.id = AA.subid ";
		$query .= "AND AA.subtable = 'resources' ";
		$query .= "AND R.published=1 AND R.standalone=1 AND R.access!=2 AND R.access!=4";

		$this->database->setQuery($query);
		return $this->database->loadResult();
	}

	/**
	 * Get a count of a user's contributions
	 *
	 * @param      integer $uid User ID
	 * @return     integer
	 */
	private function _countContributions($uid)
	{
		if ($uid === NULL)
		{
			 return 0;
		}

		$this->database->setQuery('SELECT total_count FROM `#__contributors_view` WHERE uidNumber = ' . $uid);
		return $this->database->loadResult();
	}

	/**
	 * Get a resource image
	 *
	 * @param      string $path Path to get resource image from
	 * @return     string
	 */
	private function _getImage($path)
	{
		$d = @dir(PATH_ROOT . $path);

		$images = array();

		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				if (is_file(PATH_ROOT . $path . DS . $entry)
				 && substr($entry,0,1) != '.'
				 && strtolower($entry) !== 'index.html')
				{
					if (preg_match("/^bmp|gif|jpg|jpe|jpeg|png$/i", $entry))
					{
						$images[] = $entry;
					}
				}
			}
			$d->close();
		}

		if ($images)
		{
			foreach ($images as $ima)
			{
				$bits = explode('.', $ima);
				$type = array_pop($bits);
				$img  = implode('.', $bits);

				if ($img == 'thumb')
				{
					return $ima;
				}
			}
		}
	}

	/**
	 * Get a screenshot of a tool
	 *
	 * @param      string  $path      Path to look for screenshots in
	 * @param      integer $versionid Tool version
	 * @return     string
	 */
	private function _getToolImage($path, $versionid=0)
	{
		// Get contribtool parameters
		$tconfig = Component::params('com_tools');
		$allowversions = $tconfig->get('screenshot_edit');

		if ($versionid && $allowversions)
		{
			// Add version directory
			//$path .= DS.$versionid;
		}

		$d = @dir(PATH_ROOT . $path);

		$images = array();

		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				if (is_file(PATH_ROOT . $path . DS . $entry)
				 && substr($entry,0,1) != '.'
				 && strtolower($entry) !== 'index.html')
				{
					if (preg_match("/^bmp|gif|jpg|jpe|jpeg|png$/i", $entry))
					{
						$images[] = $entry;
					}
				}
			}
			$d->close();
		}

		if ($images)
		{
			foreach ($images as $ima)
			{
				$bits = explode('.', $ima);
				$type = array_pop($bits);
				$img  = implode('.', $bits);

				if ($img == 'thumb')
				{
					return $ima;
				}
			}
		}
	}

	/**
	 * Generate a thumbnail name from a picture name
	 *
	 * @param      string $pic Picture name
	 * @return     string
	 */
	private function _thumbnail($pic)
	{
		return Filesystem::name($pic) . '-tn.gif';
	}

	/**
	 * Build a path to a resource's files
	 *
	 * @param      string  $date Resource date
	 * @param      integer $id   Resource ID
	 * @param      string  $base Base path to prepend
	 * @return     string
	 */
	private function _buildPath($date, $id, $base='')
	{
		if ($date && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs))
		{
			$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}

		if ($date)
		{
			$dir_year  = date('Y', $date);
			$dir_month = date('m', $date);
		}
		else
		{
			$dir_year  = date('Y');
			$dir_month = date('m');
		}
		$dir_id = \Hubzero\Utility\String::pad($id);

		return $base . DS . $dir_year . DS . $dir_month . DS . $dir_id;
	}

	/**
	 * Encode some HTML entities
	 *
	 * @param      string  $str    String to encode
	 * @param      integer $quotes Encode quote marks?
	 * @return     string
	 */
	private function _encodeHtml($str, $quotes=1)
	{
		$str = $this->_ampersands($str);

		$a = array(
			//'&' => '&#38;',
			'<' => '&#60;',
			'>' => '&#62;',
		);

		if ($quotes)
		{
			$a = $a + array(
				"'" => '&#39;',
				'"' => '&#34;',
			);
		}

		return strtr($str, $a);
	}

	/**
	 * Convert ampersands
	 *
	 * @param      string  $str    String to encode
	 * @return     string
	 */
	private function _ampersands($str)
	{
		$str = stripslashes($str);
		$str = str_replace('&#','*-*', $str);
		$str = str_replace('&amp;','&',$str);
		$str = str_replace('&','&amp;',$str);
		$str = str_replace('*-*','&#', $str);
		return $str;
	}
}
