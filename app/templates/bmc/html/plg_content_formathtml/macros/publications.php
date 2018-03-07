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

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;
use Hubzero\User\Group;
use Hubzero\Base\ItemList;

/**
 * Publications Macro Base Class
 * Extends basic macro class
 */
class Publications extends Macro
{
	/**
	 * Database
	 *
	 * @var  object
	 */
	protected $_db = null;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return  array
	 */
	public function description()
	{
		$txt = array();
		$txt['html']  = '<p>Displays a grid of publications.</p>';
		$txt['html'] .= '<p>Examples:</p>
							<ul>
								<li><code>[[Publications()]]</code> - Shows all publications.</li>
								<li><code>[[Publications(limit=5)]]</code> - Show the 5 most recent publications.</li>
								<li><code>[[Publications(sponsor=1003)]]</code> - Display a sponsor ribbon with each publication, linking to Group 1003 (multiple sponsors are allowed if separated by a semicolon).</li>
								<li><code>[[Publications(group=1003;1006, project=2, id=2;6;8)]]</code> - Display all publications from Groups 1003 and 1006, Project 2, and Publications with ids 2, 6, and 8.</li>
								<li><code>[[Publications(group=1003, focusarea=myfa)]]</code> - Display all publications from Group 1003, using the children tags of the myfa tag as the primary categories.  Color scheme used is <a href="http://colorbrewer2.org/#type=qualitative&scheme=Dark2">Dark2 from http://colorbrewer2.org</a>.</li>
							</ul>';
		return $txt['html'];
	}

	/**
	 * Get macro args
	 * @return array of arguments
	 */
	protected function getArgs()
	{
		//get the args passed in
		return explode(',', $this->args);
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		// Get args
		$args = $this->getArgs();

		// Database
		$this->_db = App::get('db');

		// Get details
		$limit = $this->_getLimit($args);
		$sponsors = $this->_getSponsor($args);
		$group = $this->_getGroup($args);
		$project = $this->_getProject($args);
		$pubid = $this->_getId($args);
		$focusTags = $this->_getFocusTags($args);

		// 2.2 should take care of not needed to import?  i.e. the "use" command above should handle this
		include_once \Component::path('com_publications') . DS . 'models' . DS . 'publication.php';

		// Get publications
		$items = $this->_getPublications($group, $project, $pubid, $limit);

		$base = rtrim(str_replace(PATH_ROOT, '', __DIR__));

		\Document::addStyleSheet($base . DS . 'assets' . DS . 'publications' . DS . 'css' . DS . 'pubcards.css');
		\Document::addStyleSheet($base . DS . 'assets' . DS . 'publications' . DS . 'css' . DS . 'colorbrewer.css');
		\Document::addScript($base . DS . 'assets' . DS . 'publications' . DS . 'js' . DS . 'pubcards.js');

		$html = '<div class="card-container">';
		foreach ($items as $pub)
		{
			$html .= '<div class="demo-two-card">';

			// Sponsors
			if ($sponsors) {
				$html .= '  <div class="ribbon-alt">';
				$html .= '    Sponsored by <br>';
				foreach ($sponsors as $sponsor)
				{
					$logo = 'app' . DS . 'site' . DS . 'groups' . DS . $sponsor->get('gidNumber') . DS . 'uploads' . DS . $sponsor->get('logo');
					$html .= '    <a href=' . \Route::url('index.php?option=com_groups&cn=' . $sponsor->get('cn')) . ' title="' . $sponsor->get('description') . ' Home"><img src="' . \Route::url($logo) . '" alt="Sponsor Logo"></a>';
				}
				$html .= '  </div>';
			}

			// Watch and share
			$html .= '  <div class="favorites-alt">';

			// Watch
			// Code pulled from: plugins/publications/watch
			// Bug in watching code - Waiting for fix (see https://qubeshub.org/support/tickets/990)
			$watching = \Hubzero\Item\Watch::isWatching(
				$pub->get('id'),
				'publication',
				User::get('id')
			);
			// $html .= '    <span class="watch tooltips" title="Watch this publication to be notified when a new version is released"><i class="fa fa-eye"></i></span>';

			// Share
			// $html .= '    <span title="Share" class="share-alt tooltips"><i class="fa fa-share-alt"></i></span>';

			$html .= '  </div>'; // .favorites-alt

			// Thumbnail
			$html .= '  <div class="thumbnail">';
        	$html .= '    <div class="resource-img">';
        	$html .= '      <img src="' . Route::url($pub->link('masterimage')) . '" alt="">';
        	$html .= '    </div>';
      		$html .= '  </div>';

      		// Content
      		$html .= '  <div class="demo-two-content">';

      		// Focus Tags
      		if ($focusTags) {
      			// http://colorbrewer2.org/#type=qualitative&scheme=Dark2
      			// Dark2 has a maximum of 8 colors
      			$ncolors = count($focusTags);
      			foreach ($pub->getTags() as $tag) {
      				if (!$tag->admin && (($ind = array_search($tag->raw_tag, $focusTags)) !== false)) {
      					$html .= '    <div class="categories">';
      					$html .= '      <a href="' . $tag->link() . '"><span class="primary cat Dark2 q' . $ind % $ncolors . '-' . min($ncolors, 8) . '">' . $tag->raw_tag . '</span></a>';
      					$html .= '    </div>';
      				}
      			}
      		}

      		// Post
      		$html .= '	  <div class="demo-two-post">';
      		$html .= '      <div class="demo-two-title fade">';
      		$html .= '        <a href="' . $pub->link() . '">' . $pub->get('title') . '</a>';
      		$html .= '      </div>';

			// Fork
			$html .= '      <div class="fork">';

      		// Forked from?
      		if ($v = $pub->forked_from) {
      			// Get forked ancestor
      			// Code pulled from: com_publications/site/views/view/tmpl/default.php
				$this->_db->setQuery("SELECT publication_id FROM `#__publication_versions` WHERE `id`=" . $this->_db->quote($v));
				$p = $this->_db->loadResult();
				$ancestor = new \Components\Publications\Models\Publication($p, 'default', $v);

      			$html .= '        <span><i class="fa fa-code-branch" aria-hidden="true"></i>';
				if ($ancestor->version->get('state') == 1 &&
					($ancestor->version->get('published_up') == '0000-00-00 00:00:00' || ($ancestor->version->get('published_up') != '0000-00-00 00:00:00' && $ancestor->version->get('published_up') <= Date::toSql())) &&
					($ancestor->version->get('published_down') == '0000-00-00 00:00:00' || ($ancestor->version->get('published_down') != '0000-00-00 00:00:00' && $ancestor->version->get('published_down') > Date::toSql())))
				{
					$html .= '          Forked from: <a href="' . $ancestor->link('version') . '">' . $ancestor->version->get('title') . '</a> v' . $ancestor->version->get('version_number');
				}
				else
				{
					$from = '           Forked from: ' . $ancestor->version->get('title') . ' <span class="publication-status">' . Lang::txt('(unpublished)') . '</span>';
				}
				$html .= '        </span>';
      		}
				$html .= '      </div>';

      		// First author
      		$html .= '      <div class="author">';
      		$html .= '        <span>' . $pub->authors()[0]->name . '</span>';
      		if (!empty($pub->authors()[0]->organization))
      		{
      			$html .= '        <br><span class="italic">' . $pub->authors()[0]->organization . '</span>';
      		}
      		if (count($pub->authors()) > 1)
      		{
      			$html .= '        <span class="more-info"><a href="' . $pub->link() . '"> ...et. al</a></span>';
      		}
      		$html .= '      </div>';

      		// Description
      		$html .= '      <div class="description">';
      		$html .= '        <div class="abstract">' . $pub->get('abstract') . '</div>';

      		// Version info
      		$html .= '        <div class="resource-meta">';
      		$html .= '          <a href="' . $pub->link('version') . '">Version ' . $pub->version->get('version_number') . ' - published on ' . Date::of($pub->version->get('published_up'))->toLocal('M d, Y') . '</a>'; // Could use version_label
      		$html .= '        </div>';

      		// Tags
      		$html .= '        <div class="secondary-tags">';
      		foreach($pub->getTags() as $tag) {
      			if (!$tag->admin) {
      				$html .= '           <a href="' . $tag->link() . '"><span class="secondary-tag">' . $tag->raw_tag . '</span></a>';
      			}
   			}
      		$html .= '        </div>';
      		$html .= '        <a href="' . $pub->link() . '" class="show-more">...view record</a>';

      		$html .= '      </div>'; // .description

      		$html .= '    </div>'; // .demo-two-post
      		$html .= '  </div>'; // .demo-two-content

      		$html .= '  <div class="meta-alt">';

      		// Likes
        	// $html .= '    <i class="heart tooltips fa fa-heart-o" title="Like"></i>25';

        	// Download information
        	// Code pulled from: plugins/publications/usage/usage.php (onPublication)
        	$this->_db->setQuery(
				"SELECT SUM(primary_accesses)
				FROM `#__publication_logs`
				WHERE `publication_id`=" . $this->_db->quote($pub->id) . " AND `publication_version_id`=" . $this->_db->quote($pub->version->id) . "
				ORDER BY `year` ASC, `month` ASC"
			);
			$downloads = (int) $this->_db->loadResult();
        	$html .= '    <a href="' . $pub->link('serve') . '?render=archive"><i class="downloads tooltips fa fa-download" title="Download" aria-hidden="true"></i></a><a href="' . $pub->link() . '/usage?v=' . $pub->version->version_number . '">' . $downloads . ' downloads</a>';

        	// Comments
        	// $html .= '    <a href="#"><i class="comments tooltips fa fa-comment-o" title="Comment" aria-hidden="true"></i></a><a href="#">0 comments</a>';
      		$html .= '  </div>';

			$html .= '</div>';
		}
		$html .= '</div>';

		return $html;
	}

	private function _getPublications($group, $project, $id, $limit)
	{
		// Get publication model
		//
		// Overriding the publication code in com_publications/tables/publication.php
		// Once the override is pushed to production, use the following code instead:
/*		$pubmodel = new \Components\Publications\Models\Publication();
		$filters = array(
			'group_owner' => 1003,
			'start'   => 0,
			'dev'     => 1,
			'sortby'  => 'date_created',
			'sortdir' => 'DESC'
		);
		$items = $pubmodel->entries('list', $filters);*/

		$sql = 'SELECT V.*, C.id as id, C.category, C.project_id, C.access as master_access, C.checked_out, C.checked_out_time, C.rating as master_rating, C.group_owner, C.master_type, C.master_doi, C.ranking as master_ranking, C.times_rated as master_times_rated, C.alias, V.id as version_id, t.name AS cat_name, t.alias as cat_alias, t.url_alias as cat_url, PP.alias as project_alias, PP.title as project_title, PP.state as project_status, PP.private as project_private, PP.provisioned as project_provisioned, MT.alias as base, MT.params as type_params, (SELECT vv.version_label FROM `jos_publication_versions` as vv WHERE vv.publication_id=C.id AND vv.state=3 ORDER BY ID DESC LIMIT 1) AS dev_version_label , (SELECT COUNT(*) FROM `jos_publication_versions` WHERE publication_id=C.id AND state!=3) AS versions FROM `jos_publication_versions` as V, `jos_projects` as PP, `jos_publication_master_types` AS MT, `jos_publications` AS C LEFT JOIN `jos_publication_categories` AS t ON t.id=C.category WHERE V.publication_id=C.id AND MT.id=C.master_type AND PP.id = C.project_id AND V.id = (SELECT MAX(wv2.id) FROM `jos_publication_versions` AS wv2 WHERE wv2.publication_id = C.id AND state!=3)';

		$args = array();
		if ($group)
		{
			array_push($args, '(C.group_owner IN (' . $group . ') OR PP.owned_by_group IN (' . $group . '))');
		}
		if ($project)
		{
			array_push($args, '(PP.alias IN (' . $project . '))');
		}
		if ($id)
		{
			array_push($args, '(C.id IN (' . $id . '))');
		}
		if ($nargs = count($args)) {
			$sql_args = implode(' OR ', $args);
			$sql .= ' AND ' . ($nargs == 1 ? $sql_args : '(' . $sql_args . ')');
		}

		$sql .= ' AND V.state != 2 GROUP BY C.id ORDER BY C.created DESC';

		if ($limit)
		{
			$sql .= ' LIMIT 0, ' . $limit;
		}

		$this->_db->setQuery($sql);

		if ($results = $this->_db->loadObjectList())
		{
			foreach ($results as $key => $result)
			{
				$results[$key] = new \Components\Publications\Models\Publication($result);
			}
		}

		return new ItemList($results);
	}

	/**
	 * Get focus tag from argument list
	 *
	 * @param   array    $args     Macro Arguments
	 * @param   string   $default  Default focus area
	 * @return  array    $children Child tags of focus area
	 */
	private function _getFocusTags(&$args, $default='resourcesqubes')
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/focusarea=([\w;]*)/', $arg, $matches))
			{

				$parent_label = (isset($matches[1]) ? $matches[1] : '');
				$children = $this->_getChildTags($parent_label);
				unset($args[$k]);
				return $children;
			}
		}

		$children = $this->_getChildTags($default);
		return $children;
	}

	private function _getChildTags($parent_label)
	{
		// First get tag id
		$this->_db->setQuery('SELECT t.id FROM `#__tags` t WHERE t.tag = ' . $this->_db->quote($parent_label));
		$parent_id = $this->_db->loadResult();

		// Get children of $parent_id
		$this->_db->setQuery('SELECT DISTINCT t.raw_tag AS label, t2.id, t2.tag, t2.raw_tag, t2.description
					FROM `#__tags` t
					INNER JOIN `#__tags_object` to1 ON to1.tbl = \'tags\' AND to1.tagid = t.id AND to1.label = \'label\'
					INNER JOIN `#__tags_object` to2 ON to2.tbl = \'tags\' AND to2.label = \'parent\' AND to2.objectid = to1.objectid
						AND to2.tagid = ' . $this->_db->quote($parent_id) . '
					INNER JOIN `#__tags` t2 ON t2.id = to1.objectid
					ORDER BY label, t2.raw_tag');
		$children = $this->_db->loadAssocList('raw_tag');

		return array_keys($children);
	}

	/**
	 * Get item limit
	 *
	 * @param   array    $args     Macro Arguments
	 * @param   integer  $default  Default return value
	 * @return  mixed
	 */
	private function _getLimit(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/limit=([\w]*)/', $arg, $matches) && isset($matches[1]) && is_numeric($matches[1]) && $matches[1] > 0 && $matches[1] < 50)
			{
				$limit = $matches[1];
				unset($args[$k]);
				return $limit;
			}
		}

		return false;
	}

	/**
	 * Get sponsor
	 *
	 * @param 	$args Macro Arguments
	 * @return  mixed
	 */
	private function _getSponsor(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/sponsor=([\w;]*)/', $arg, $matches))
			{
				$sponsor = explode(';', (isset($matches[1])) ? $matches[1] : '');
				unset($args[$k]);
				return array_map('Hubzero\User\Group::getInstance', $sponsor);
			}
		}

		return false;
	}

	/**
	 * Get group
	 *
	 * @param  	$args Macro Arguments
	 * @return 	mixed
	 */
	private function _getGroup(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/group=([\w;]*)/', $arg, $matches))
			{
				$group = str_replace(';',',',(isset($matches[1])) ? $matches[1] : '');
				unset($args[$k]);
				return $group;
			}
		}

		return false;
	}

	/**
	 * Get project
	 *
	 * @param  	$args Macro Arguments
	 * @return 	mixed
	 */
	private function _getProject(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/project=([\w;]*)/', $arg, $matches))
			{
				$project = implode(',', array_map(array($this->_db, 'quote'), explode(';', (isset($matches[1])) ? $matches[1] : '')));
				unset($args[$k]);
				return $project;
			}
		}

		return false;
	}

	/**
	 * Get publication id
	 *
	 * @param  	$args Macro Arguments
	 * @return 	mixed
	 */
	private function _getId(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/id=([\w;]*)/', $arg, $matches))
			{
				$pid = str_replace(';',',',(isset($matches[1])) ? $matches[1] : '');
				unset($args[$k]);
				return $pid;
			}
		}

		return false;
	}
}
