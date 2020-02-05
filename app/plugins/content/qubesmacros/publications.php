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

  // Not sure we need this
	public $limit, $sponsors, $group, $project, $id, $focusTags, $fascheme, $sponsorbgcol, $mastertype, $tags, $style, $sortby, $sortdir, $items, $base;

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
								<li><code>[[Publications(view=list)]]</code> - Display publications in list format (default is "card").</li>
								<li><code>[[Publications(limit=5, style=legacy)]]</code> - Show the 5 most recent publications using the legacy style.</li>
								<li><code>[[Publications(sponsor=mygroup, sponsorbgcol=cb48b7)]]</code> - Display a sponsor ribbon with each publication, linking to Group "mygroup" (multiple sponsors are allowed if separated by a semicolon).  Background color of ribbon is given in hexidecimal without # (default is cb48b7).</li>
								<li><code>[[Publications(group=mygroup1;mygroup2, project=myproject, id=2;6;8)]]</code> - Display all publications from Groups "mygroup1" and "mygroup2", Project "myproject", and Publications with ids 2, 6, and 8.</li>
								<li><code>[[Publications(group=mygroup, focusarea=myfa, fascheme=Dark2)]]</code> - Display all publications from Group "mygroup", using the children tags of the "myfa" tag as the primary categories.  Color scheme used is <a href="http://colorbrewer2.org/#type=qualitative&scheme=Dark2">Dark2 (default) from http://colorbrewer2.org</a>.</li>
								<li><code>[[Publications(pubtype=qubesresource, tag=ecology;genetics)]]</code> - Display all QUBES publications that are tagged "ecology" <i>or</i> "genetics".</li>
								<li><code>[[Publications(id=2;1;3, sortby=id, sortdir=none)]]</code> - Override the default sort by publish date and display publications in order given by id.</li>
								<li><code>[[Publications(group=mygroup, sortby=date, sortdir=asc)]]</code> - Display publications in mygroup from oldest to newest (rather than default newest to oldest).</li>
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
		include_once \Component::path('com_publications') . DS . 'models' . DS . 'publication.php';

		// Get args
	  $args = $this->getArgs();

		// Database
		$this->_db = App::get('db');

		// Get details
		$this->limit = $this->_getLimit($args);
		$this->sponsors = $this->_getSponsor($args);
		$this->group = $this->_getGroup($args);
		$this->project = $this->_getProject($args);
		$this->id = $this->_getId($args);
		$this->focusTags = $this->_getFocusTags($args);
		$this->fascheme = $this->_getFaScheme($args);
		$this->sponsorbgcol = $this->_getSponsorBGCol($args);
		$this->mastertype = $this->_getMasterType($args);
		$this->tags = $this->_getTags($args);
		$this->style = $this->_getStyle($args);
		$this->sortby = $this->_getSortBy($args);
		$this->sortdir = $this->_getSortDir($args);

		$this->items = $this->_getPublications();
		$this->base = rtrim(str_replace(PATH_ROOT, '', __DIR__));

		$view = $this->_getView($args);
    if($view =='card') {
      return $this->_getCardView();
		} else {
			return $this->_getListView();
		}
	}

	public function _getCardView() {
		\Document::addStyleSheet($this->base . DS . 'assets' . DS . 'publications' . DS . 'css' . DS . 'colorbrewer.css');
		\Document::addScript($this->base . DS . 'assets' . DS . 'publications' . DS . 'js' . DS . 'pubcards.js');
	  \Document::addStyleSheet($this->base . DS . 'assets' . DS . 'publications' . DS . 'css' . DS . 'pubcards.css');

		$html = '<style>';
		$html .= '  .ribbon-alt {';
		$html .= '    background-color: #' . $this->sponsorbgcol . ';';
		$html .= '    color: ' . $this->getContrastYIQ($this->sponsorbgcol) . ';';
		$html .= '  }';
		$html .= '  .ribbon-alt:before {';
		$html .= '    border-color: transparent ' . $this->sass_darken($this->sponsorbgcol, 20) . ' transparent transparent;';
		$html .= '  }';
		$html .= '</style>';
		$html .= '<div class="card-container">';
		if ($this->style == 'legacy') {
			foreach ($this->items as $pub)
			{
				$html .= '<div class="demo-two-card">';

				// Sponsors
				if ($this->sponsors) {
					$html .= '  <div class="ribbon-alt">';
					$html .= '    Sponsored by <br>';
					foreach ($this->sponsors as $sponsor)
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
				// Subscribe link
				// echo \Route::url($pub->link()) . DS . 'watch' . DS . $pub->version->get('version_number') . '?confirm=1&action=subscribe<br>';
				$html .= '    <span class="watch">';
				if ($watching) {
					$html .= '      <a href="' . \Route::url($pub->link()) . DS . 'watch' . DS . $pub->version->get('version_number') . '?confirm=1&action=unsubscribe"><i class="tooltips icon-watch watching" title="Click to unsubscribe from this publication\'s notifications" aria-hidden="true"></i></a>';
				} else {
					$html .= '      <a href="' . \Route::url($pub->link()) . DS . 'watch' . DS . $pub->version->get('version_number') . '?confirm=1&action=subscribe"><i class="tooltips icon-watch" title="Click to watch this publication and receive notifications when a new version is released" aria-hidden="true"></i></a>';
				}
				$html .= '    </span>';

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
	  		if ($this->focusTags) {
	  			// http://colorbrewer2.org/#type=qualitative&scheme=Dark2
	  			// Dark2 has a maximum of 8 colors
	  			$ncolors = count($this->focusTags);
	  			foreach ($pub->getTags() as $tag) {
	  				if (!$tag->admin && (($ind = array_search($tag->raw_tag, $this->focusTags)) !== false)) {
	  					$html .= '    <div class="categories">';
	  					$html .= '      <a href="' . $tag->link() . '">';
							$html .= '        <span class="primary cat ' . $this->fascheme . ' q' . $ind % $ncolors . '-' . min($ncolors, 8) . '">' . $tag->raw_tag . '</span>';
							$html .= '      </a>';
	  					$html .= '    </div>';
	  				}
	  			}
	  		}

	  		// Post
	  		$html .= '	  <div class="demo-two-post">';
	  		$html .= '      <div class="demo-two-title">';
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

	      	$html .= '        <span><i class="item-fork" aria-hidden="true"></i>';
					if ($ancestor->version->get('state') == 1 &&
						($ancestor->version->get('published_up') == '0000-00-00 00:00:00' || ($ancestor->version->get('published_up') != '0000-00-00 00:00:00' && $ancestor->version->get('published_up') <= Date::toSql())) &&
						($ancestor->version->get('published_down') == '0000-00-00 00:00:00' || ($ancestor->version->get('published_down') != '0000-00-00 00:00:00' && $ancestor->version->get('published_down') > Date::toSql())))
					{
						$html .= '          Adapted from: <a href="' . $ancestor->link('version') . '">' . $ancestor->version->get('title') . '</a> v' . $ancestor->version->get('version_number');
					}
					else
					{
						$from = '           Adapted from: ' . $ancestor->version->get('title') . ' <span class="publication-status">' . Lang::txt('(unpublished)') . '</span>';
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
	    	$html .= '    <a href="' . $pub->link('serve') . '?render=archive"><i class="downloads tooltips icon-download" title="Download" aria-hidden="true"></i></a><a href="' . $pub->link() . '/usage?v=' . $pub->version->version_number . '">' . $downloads . ' downloads</a>';

	    	// Comments
	    	// $html .= '    <a href="#"><i class="comments tooltips fa fa-comment-o" title="Comment" aria-hidden="true"></i></a><a href="#">0 comments</a>';
	  		$html .= '  </div>';
				$html .= '</div>';
			}
		} else {
			foreach ($this->items as $pub)
			{
				$html .= '  <div class="card" style="background-image: url(' . $this->_db->quote(Route::url($pub->link('masterimage'))) . ');">';

				// Featured ROW
				// For some reason, featured is not stored in model so we need to grab it
				$this->_db->setQuery(
					"SELECT `featured`
					FROM `#__publications`
					WHERE `id`=" . $this->_db->quote($pub->id)
				);
				$featured = (int) $this->_db->loadResult();

				if ($featured) {
					$html .= '	<div class="featured">';
					$html .= '    <a aria-label="Featured ROW" title="Featured Resouce of the Week" href="' . Route::url('/news/newsletter/row') . '">';
					$html .= '	    <span>Featured Resource of the Week</span>';
					$html .= '    </a>';
					$html .= '  </div>'; // End feature ribbon
				}



				// Focus Tags
				if ($this->focusTags) {
					// http://colorbrewer2.org/#type=qualitative&scheme=Dark2
					// Dark2 has a maximum of 8 colors
					$ncolors = count($this->focusTags);
					foreach ($pub->getTags() as $tag) {
						if (!$tag->admin && (($ind = array_search($tag->raw_tag, $this->focusTags)) !== false)) {
							$html .= '    <a href="' . $tag->link() . '">';
							$html .= '      <span class="featured-tag primary cat ' . $this->fascheme . ' q' . $ind % $ncolors . '-' . min($ncolors, 8) . '">';
							$html .= '        ' . $tag->raw_tag;
							$html .= '      </span>';
							$html .= '    </a>';
						}
					}
				}

				// Content
				$html .= '    <div class="card-content" tabindex="-1">';
				// $html .= '      <div class="img-wrap">';
				// $html .= '        <img src="' . Route::url($pub->link('masterimage')) . '" alt="Resource Image">';
				// $html .= '      </div>';

				// Citation info
	  		$html .= '	  <div class="title">';

				// Title
	  		$html .= '      <h3>';
	  		$html .= '        <a href="' . $pub->link() . '">' . $pub->get('title') . '</a>';
	  		$html .= '      </h3>';

				// Authors
				$authors = implode(', ', array_map(function ($author) {return $author->name; }, $pub->authors()));
				$html .= '      <p class="authors" title= "' . $authors . '">';
				$html .= '        ' . $authors;
	  		$html .= '      </p>';

				// Version info
				$html .= '      <p class="hist">';
				$html .= '        <span class="versions">';
				$html .= '          Version: ' . $pub->version->get('version_label');
				$html .= '        </span>';
				if ($v = $pub->forked_from) {
					// Get forked ancestor
					// Code pulled from: com_publications/site/views/view/tmpl/default.php
					$this->_db->setQuery("SELECT publication_id FROM `#__publication_versions` WHERE `id`=" . $this->_db->quote($v));
					$p = $this->_db->loadResult();
					$ancestor = new \Components\Publications\Models\Publication($p, 'default', $v);

					$html .= '        <span class="adapted">';
					$html .= '          Adapted From: <a href="' . $ancestor->link('version') . '">' . $ancestor->version->get('title') . '</a> v' . $ancestor->version->get('version_label');
					$html .= '        </span>';
				}
				$html .= '      </p>';
				$html .= '    </div>'; // End title
				$html .= '      <div class="abstract">';
				$html .= '        ' . $pub->get('abstract');
				$html .= '      </div>';

				// Watch
				// Code pulled from: plugins/publications/watch
				// Bug in watching code - Waiting for fix (see https://qubeshub.org/support/tickets/990)
				$watching = \Hubzero\Item\Watch::isWatching(
					$pub->get('id'),
					'publication',
					User::get('id')
				);

				// Sub-menu
				$html .= '    <div class="sub-menu">';
				$html .= '      <a aria-label="Full Record" title= "Full Record" href="' . $pub->link() . '" aria-hidden="true" tabindex="-1">';
		    $html .= '        <span class="menu-icon">' . file_get_contents(PATH_ROOT . DS . "core/assets/icons/arrow-right.svg") . '</span>';
		    $html .= '        Full Record';
		    $html .= '      </a>';
		    $html .= '      <a aria-label="Download" title= "Download" href="' . $pub->link('serve') . '?render=archive" aria-hidden="true" tabindex="-1">';
		    $html .= '        <span class="menu-icon">' . file_get_contents(PATH_ROOT . DS . "core/assets/icons/download-alt.svg") . '</span>';
		    $html .= '      </a>';

				$url = $pub->link() . '/forks/' . $pub->version->get('version_number') . '?action=fork';
				$html .= '      <a aria-label="Adapt" title= "Adapt" href="' . $url . '" aria-hidden="true" tabindex="-1">';
		    $html .= '        <span class="menu-icon">' . file_get_contents(PATH_ROOT . DS . "core/assets/icons/code-fork.svg") . '</span>';
		    $html .= '      </a>';
				if ($watching) {
  		    $html .= '      <a aria-label="Watch" title= "Click to unsubscribe from this resource\'s notifications" href="' . \Route::url($pub->link()) . DS . 'watch' . DS . $pub->version->get('version_number') . '?confirm=1&action=unsubscribe" aria-hidden="true" tabindex="-1">';
  		    $html .= '        <span class="menu-icon">' . file_get_contents("app/plugins/content/qubesmacros/assets/icons/feed-off.svg") . '</span>';
  		    $html .= '      </a>';
				} else {
					$html .= '      <a aria-label="Watch" title= "Click to receive notifications when a new version is released" href="' . \Route::url($pub->link()) . DS . 'watch' . DS . $pub->version->get('version_number') . '?confirm=1&action=subscribe" aria-hidden="true" tabindex="-1">';
  		    $html .= '        <span class="menu-icon">' . file_get_contents(PATH_ROOT . DS . "core/assets/icons/feed.svg") . '</span>';
  		    $html .= '      </a>';
				}
		    $html .= '    </div>'; // End sub-menu
				$html .= '    </div>'; // End content

				// Sponsors
				if ($this->sponsors) {
					$html .= '    <div class="logo-wrap">';
					foreach ($this->sponsors as $sponsor)
					{
						$logo = 'app' . DS . 'site' . DS . 'groups' . DS . $sponsor->get('gidNumber') . DS . 'uploads' . DS . $sponsor->get('logo');
						$html .= '    <a href=' . \Route::url('index.php?option=com_groups&cn=' . $sponsor->get('cn')) . ' title="Sponsored by ' . $sponsor->get('description') . ' Home"><img src="' . \Route::url($logo) . '" alt="Sponsor Logo" class="logo"></a>';
					}
					$html .= '    </div>';
				}

				// More information button
				$html .= '    <button aria-label="More Information" title="More Information" href="#" class="btn-action">';
				$html .= '      <i class="menu"></i>';
		    $html .= '    </button>';

				// Meta
				$this->tags = $pub->getTags()->toArray();
				$nonAdminTags = array_filter(array_map(function ($tag) {return (!$tag['admin'] ? $tag['raw_tag'] : NULL); }, $this->tags), 'strlen');
				$tagsTitle = implode(', ', $nonAdminTags);
				$html .= '    <div class="meta">';
				$html .= '      <div aria-label="Tags" title= "' . $tagsTitle . '" class="tag-wrap">';
	      $html .= '        <span class="icons">' . file_get_contents(PATH_ROOT . DS . "core/assets/icons/tags.svg") . '</span>';
				$html .= '        <span>';
				if ($nonAdminTags) {
					$html .= '          <span class="tags">' . implode(', </span><span class="tags">', $nonAdminTags);
				}
				$html .= '        </span>';
				$html .= '      </div>'; // End tags

				// Views Information
				// Code pulled from: plugins/publications/usage/usage.php (onPublication)
				$this->_db->setQuery(
					"SELECT SUM(page_views)
					FROM `#__publication_logs`
					WHERE `publication_id`=" . $this->_db->quote($pub->id) . " AND `publication_version_id`=" . $this->_db->quote($pub->version->id) . "
					ORDER BY `year` ASC, `month` ASC"
				);
				$views = (int) $this->_db->loadResult();

				$html .= '      <div class="views">';
				$html .= '        <span aria-label="Views" title= "Views">';
				$html .= '          <span class="icons">' . file_get_contents(PATH_ROOT . DS . "core/assets/icons/eye-open.svg") . '</span>';
				$html .= '          ' . $views;
				$html .= '        </span>';
				$html .= '      </div>'; // End views

				// Download information
				// Code pulled from: plugins/publications/usage/usage.php (onPublication)
				$this->_db->setQuery(
					"SELECT SUM(primary_accesses)
					FROM `#__publication_logs`
					WHERE `publication_id`=" . $this->_db->quote($pub->id) . " AND `publication_version_id`=" . $this->_db->quote($pub->version->id) . "
					ORDER BY `year` ASC, `month` ASC"
				);
				$downloads = (int) $this->_db->loadResult();

				$html .= '      <div class="downloads">';
				$html .= '        <span aria-label="Downloads" title= "Downloads">';
				$html .= '          <span class="icons">' . file_get_contents(PATH_ROOT . DS . "core/assets/icons/download-alt.svg") . '</span>';
				$html .= '          ' . $downloads;
				$html .= '        </span>';
				$html .= '      </div>'; // End downloads

				// Adaptation information
				$this->_db->setQuery(
					"SELECT COUNT(id)
					FROM `#__publication_versions`
					WHERE `forked_from`
					IN (" . $this->_db->quote($pub->version->id) . ")");
				$forks = (int) $this->_db->loadResult();

				$html .= '      <div class="forks">';
				$html .= '        <span aria-label="Adaptations" title= "Adaptations">';
				$html .= '          <span class="icons">' . file_get_contents(PATH_ROOT . DS . "core/assets/icons/code-fork.svg") . '</span>';
				$html .= '          ' . $forks;
				$html .= '        </span>';
				$html .= '      </div>'; // End adaptations

				// Publish Date
				$html .= '      <div class="date">';
	      $html .= '        <span aria-label="Publish Date" title= "Publish Date">';
	      $html .= '          <span class="icons">' . file_get_contents(PATH_ROOT . DS . "core/assets/icons/calendar-alt.svg") . '</span>';
	      $html .= '         ' . Date::of($pub->version->get('published_up'))->toLocal('m.d.Y');
	      $html .= '        </span>';
	      $html .= '      </div>'; // End publish date

				$html .= '    </div>'; // End meta

				$html .= '  </div>'; // End card
			}
		}

		$html .= '</div>'; // End card list

		return $html;
	}

  public function _getListView() {
		\Document::addStyleSheet($this->base . DS . 'assets' . DS . 'publications' . DS . 'css' . DS . 'list_view.css');
	  //\Document::addStyleSheet($this->base . DS . 'assets' . DS . 'publications' . DS . 'css' . DS . 'colorbrewer.css');

		$html = '<main class="main section">';
		$html .= ' <div class="resource_contents">';
		$html .= '  <div class="resource_content">';

		foreach ($this->items as $pub)
		{
			// Citation info
			$html .= '	 <div class="resource-wrapper">';

			// Featured ROW
			// For some reason, featured is not stored in model so we need to grab it
			$this->_db->setQuery(
				"SELECT `featured`
				FROM `#__publications`
				WHERE `id`=" . $this->_db->quote($pub->id)
			);
			$featured = (int) $this->_db->loadResult();

			if ($featured) {
				$html .= '	<div class="featured">';
				$html .= '    <a aria-label="Featured ROW" title="Featured Resouce of the Week" href="' . Route::url('/news/newsletter/row') . '">';
				$html .= '	    <span>Featured Resource of the Week</span>';
				$html .= '    </a>';
				$html .= '  </div>'; // End feature ribbon
			}

			// Sponsors
			if ($this->sponsors) {
				$html .= '    <div class="logo-wrap">';
				foreach ($this->sponsors as $sponsor)
				{
					$logo = 'app' . DS . 'site' . DS . 'groups' . DS . $sponsor->get('gidNumber') . DS . 'uploads' . DS . $sponsor->get('logo');
					$html .= '    <a href=' . \Route::url('index.php?option=com_groups&cn=' . $sponsor->get('cn')) . ' title="' . $sponsor->get('description') . ' Home"><img src="' . \Route::url($logo) . '" alt="Sponsor Logo" class="logo"></a>';
				}
				$html .= '    </div>';
			}

			// Focus Tags
			if ($this->focusTags) {
				// http://colorbrewer2.org/#type=qualitative&scheme=Dark2
				// Dark2 has a maximum of 8 colors
				$ncolors = count($this->focusTags);
				foreach ($pub->getTags() as $tag) {
					if (!$tag->admin && (($ind = array_search($tag->raw_tag, $this->focusTags)) !== false)) {
						$html .= '    <a href="' . $tag->link() . '">';
						$html .= '      <span class="featured-tag primary cat ' . $this->fascheme . ' q' . $ind % $ncolors . '-' . min($ncolors, 8) . '">';
						$html .= '        ' . $tag->raw_tag;
						$html .= '      </span>';
						$html .= '    </a>';
					}
				}
			}
			
			$html .= '	  <div class="resource-info-wrapper">';
			$html .= '	   <div class="resource-info">';

			// Title
			$html .= '      <h3>';
			$html .= '        <a href="' . $pub->link() . '">' . $pub->get('title') . '</a>';
			$html .= '      </h3>';

			// Authors
			$authors = implode(', ', array_map(function ($author) {return $author->name; }, $pub->authors()));
			$html .= '      <p class="author" title= "' . $authors . '">';
			$html .= '        ' . $authors;
			$html .= '      </p>';

			// Version info
			$html .= '      <p class="hist">';
			$html .= '        <span class="version">';
			$html .= '          Version: ' . $pub->version->get('version_label');
			$html .= '        </span>';
			if ($v = $pub->forked_from) {
				// Get forked ancestor
				// Code pulled from: com_publications/site/views/view/tmpl/default.php
				$this->_db->setQuery("SELECT publication_id FROM `#__publication_versions` WHERE `id`=" . $this->_db->quote($v));
				$p = $this->_db->loadResult();
				$ancestor = new \Components\Publications\Models\Publication($p, 'default', $v);

				$html .= '        <span class="adaptations">';
				$html .= '          Adapted From: <a href="' . $ancestor->link('version') . '">' . $ancestor->version->get('title') . '</a> v' . $ancestor->version->get('version_label');
				$html .= '        </span>';
			}
			$html .= '      </p>';
			$html .= '      <div class="abstract">';
			$html .= '        ' . $pub->get('abstract');
			$html .= '      </div>';
			$html .= '    </div>'; // End resource-info

			// Watch
			// Code pulled from: plugins/publications/watch
			// Bug in watching code - Waiting for fix (see https://qubeshub.org/support/tickets/990)
			$watching = \Hubzero\Item\Watch::isWatching(
				$pub->get('id'),
				'publication',
				User::get('id')
			);

			// Meta

			// Publish Date
			$html .= '    <div class="meta-wrap">';

			$html .= '     <div class="date">';
			$html .= '  Published on <span class="pub-date" aria-label="Publish Date" title= "Publish Date">';
			$html .= '         ' . Date::of($pub->version->get('published_up'))->toLocal('m.d.Y');
			$html .= '        </span>';
			$html .= '     </div>'; // End publish date

			$html .= '     <div class="meta">';

			// Views Information
			// Code pulled from: plugins/publications/usage/usage.php (onPublication)
			$this->_db->setQuery(
				"SELECT SUM(page_views)
				FROM `#__publication_logs`
				WHERE `publication_id`=" . $this->_db->quote($pub->id) . " AND `publication_version_id`=" . $this->_db->quote($pub->version->id) . "
				ORDER BY `year` ASC, `month` ASC"
			);

			$views = (int) $this->_db->loadResult();

			$html .= '      <div class="views">';
			$html .= '        <span aria-label="Views" title= "Views">';
			$html .= '          <span class="count">' . $views . '</span>';
			$html .= '          <span class="ic eye-icon">' . file_get_contents(PATH_ROOT . DS . "core/assets/icons/eye-open.svg") . '</span>';
		  $html .= '          <span class="meta-descripter">Views</span';
			$html .= '        </span>';
			$html .= '      </div>'; // End views

			// Download information
			// Code pulled from: plugins/publications/usage/usage.php (onPublication)
			$this->_db->setQuery(
				"SELECT SUM(primary_accesses)
				FROM `#__publication_logs`
				WHERE `publication_id`=" . $this->_db->quote($pub->id) . " AND `publication_version_id`=" . $this->_db->quote($pub->version->id) . "
				ORDER BY `year` ASC, `month` ASC"
			);
			$downloads = (int) $this->_db->loadResult();

			$html .= '      <div class="downloads">';
			$html .= '        <span aria-label="Downloads" title= "Downloads">';
			$html .= '          <span class="count">' . $downloads . '</span>';
			$html .= '          <span class="ic download-icon">' . file_get_contents(PATH_ROOT . DS . "core/assets/icons/download-alt.svg") . '</span>';
		  $html .= '          <span class="meta-descripter">Downloads</span';
			$html .= '        </span>';
			$html .= '      </div>'; // End downloads

			// Adaptation information
			$this->_db->setQuery(
				"SELECT COUNT(id)
				FROM `#__publication_versions`
				WHERE `forked_from`
				IN (" . $this->_db->quote($pub->version->id) . ")");
			$forks = (int) $this->_db->loadResult();

			$html .= '      <div class="forks">';
			$html .= '        <span aria-label="Adaptations" title= "Adaptations">';
			$html .= '          <span class="count">' . $forks . '</span>';
			$html .= '          <span class="ic fork-icon">' . file_get_contents(PATH_ROOT . DS . "core/assets/icons/code-fork.svg") . '</span>';
			$html .= '          <span class="meta-descripter">Adaptations</span';
			$html .= '        </span>';
			$html .= '      </div>'; // End adaptations

			$html .= '     </div>'; // End meta
			$html .= '    </div>'; // End meta-wrap
			$html .= '   </div>';  // End resource-info-wrapper

			$this->tags = $pub->getTags()->toArray();
			$nonAdminTags = array_filter(array_map(function ($tag) {return (!$tag['admin'] ? $tag['raw_tag'] : NULL); }, $this->tags), 'strlen');
			$tagsTitle = implode(', ', $nonAdminTags);

			$html .= '      <div aria-label="Tags" title= "' . $tagsTitle . '" class="tags-wrapper">';
			$html .= '        <span class="icons">' . file_get_contents(PATH_ROOT . DS . "core/assets/icons/tags.svg") . '</span>';
			$html .= '        <span>';
			if ($nonAdminTags) {
				$html .= '          <span class="tags">' . implode(', </span><span class="tags">', $nonAdminTags);
			}
			$html .= '        </span>';
			$html .= '      </div>'; // End tags

			$html .= '  </div>'; // End resource-wrapper
		}

		$html .= '  </div>'; //End resource_content
		$html .= ' </div>';  //End resource_contents
		$html .= '</main>'; //End main section

		return $html;
	}

	private function _getPublications()
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

		$sql = 'SELECT V.*, C.id as id, C.category, C.project_id, C.access as master_access, C.checked_out, C.checked_out_time, C.rating as master_rating, C.group_owner, (SELECT G.cn FROM #__xgroups as G WHERE G.gidNumber=C.group_owner) AS group_cn, (SELECT G.cn FROM #__xgroups as G WHERE G.gidNumber=PP.owned_by_group) AS project_group_cn, C.master_type, C.master_doi, C.ranking as master_ranking, C.times_rated as master_times_rated, C.alias, V.id as version_id, t.name AS cat_name, t.alias as cat_alias, t.url_alias as cat_url, PP.alias as project_alias, PP.title as project_title, PP.state as project_status, PP.private as project_private, PP.provisioned as project_provisioned, MT.alias as base, MT.params as type_params, (SELECT vv.version_label FROM #__publication_versions as vv WHERE vv.publication_id=C.id AND vv.state=3 ORDER BY ID DESC LIMIT 1) AS dev_version_label , (SELECT COUNT(*) FROM #__publication_versions WHERE publication_id=C.id AND state!=3) AS versions FROM #__publication_versions as V, #__projects as PP, #__publication_master_types AS MT, #__publications AS C LEFT JOIN #__publication_categories AS t ON t.id=C.category WHERE V.publication_id=C.id AND MT.id=C.master_type AND PP.id = C.project_id AND V.id = (SELECT MAX(wv2.id) FROM #__publication_versions AS wv2 WHERE wv2.publication_id = C.id AND state!=3)';

		// Add master type
		if ($this->mastertype) {
			$sql .= ' AND (MT.alias = ' . $this->mastertype . ')';
		}

		$args = array();
		if ($this->group)
		{
			array_push($args, '((SELECT G.cn FROM #__xgroups as G WHERE G.gidNumber=C.group_owner) IN (' . $this->group . ') OR (SELECT G.cn FROM #__xgroups as G WHERE G.gidNumber=PP.owned_by_group) IN (' . $this->group . '))');
		}
		if ($this->project)
		{
			array_push($args, '(PP.alias IN (' . $this->project . '))');
		}
		if ($this->id)
		{
			array_push($args, '(C.id IN (' . $this->id . '))');
		}
		if ($nargs = count($args)) {
			$sql_args = implode(' OR ', $args);
			$sql .= ' AND ' . ($nargs == 1 ? $sql_args : '(' . $sql_args . ')');
		}

		if ($this->tags) {
			$sql .= ' AND (V.id IN (SELECT DISTINCT(objectid) FROM #__tags_object O WHERE O.tagid IN (SELECT T.id FROM #__tags T WHERE T.tag IN (' . $this->tags . ')) AND O.tbl="publications"))';
		}

		$sql .= ' AND V.state != 2 GROUP BY C.id ORDER BY';

		// Sorting
		if ($this->sortby == 'id') {
			if ($this->sortdir == 'none') {
				$sql .= ' FIELD(C.id, ' . $this->id . ')';
			} else {
				$sql .= ' C.id';
			}
		} else {
			$sql .= ' V.published_up';
		}
		if ($this->sortdir != 'none') {
			$sql .= ($this->sortdir == 'asc' ? ' ASC' : ' DESC');
		}

		if ($this->limit)
		{
			$sql .= ' LIMIT 0, ' . $this->limit;
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
	private function _getFocusTags(&$args)
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

		// $children = $this->_getChildTags($default);
		// return $children;
		return false;
	}

	private function _getFaScheme(&$args, $default = "Dark2")
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/fascheme=([\w;]*)/', $arg, $matches))
			{

				$scheme = (isset($matches[1]) ? $matches[1] : '');
				unset($args[$k]);
				return $scheme;
			}
		}

		return $default;
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
	 * Get sponsor ribbon background color
	 *
	 * @param 	$args Macro Arguments
	 * @return  mixed
	 */
	private function _getSponsorBGCol(&$args, $default = "cb48b7")
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/sponsorbgcol=([\w;]*)/', $arg, $matches))
			{
				$sponsorbgcol = (isset($matches[1]) ? $matches[1] : '');
				unset($args[$k]);
				return $sponsorbgcol;
			}
		}

		return $default;
	}

	private function _getMasterType(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/pubtype=([\w;]*)/', $arg, $matches))
			{
				$mastertype = (isset($matches[1]) ? $matches[1] : '');
				unset($args[$k]);
				return $this->_db->quote($mastertype);
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
				$group = implode(',', array_map(array($this->_db, 'quote'), explode(';', (isset($matches[1])) ? $matches[1] : '')));
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

	/**
	 * Get publications by tag (uses OR for multiple tags)
	 *
	 * @param  	$args Macro Arguments
	 * @return 	mixed
	 */
	private function _getTags(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/tag=([\w;]*)/', $arg, $matches))
			{
				$tags = implode(',', array_map(array($this->_db, 'quote'), explode(';', (isset($matches[1])) ? $matches[1] : '')));
				unset($args[$k]);
				return $tags;
			}
		}

		return false;
	}

	/**
	 * Get card style
	 *
	 * @param  	$args Macro Arguments
	 * @return 	mixed
	 */
	private function _getStyle(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/style=([\w;]*)/', $arg, $matches))
			{
				$style = (isset($matches[1]) ? $matches[1] : '');
				unset($args[$k]);
				return $style;
			}
		}

		return false;
	}

	/**
	 * Get sort by argument
	 *
	 * @param  	$args Macro Arguments
	 * @return 	mixed
	 */
	private function _getSortBy(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/sortby=([\w;]*)/', $arg, $matches))
			{
				$sortby = (isset($matches[1]) ? $matches[1] : '');
				unset($args[$k]);
				return $sortby;
			}
		}

		return false;
	}

	/**
	 * Get sort direction argument
	 *
	 * @param  	$args Macro Arguments
	 * @return 	mixed
	 */
	private function _getSortDir(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/sortdir=([\w;]*)/', $arg, $matches))
			{
				$sortdir = (isset($matches[1]) ? $matches[1] : '');
				unset($args[$k]);
				return $sortdir;
			}
		}

		return false;
	}

	// HELPER FUNCTIONS

	/**
	 * PHP SASS darken emulator
	 *
	 * @param  	$hex	Color (string; hexidecimal)
	 * @param		$percent	Percent to darken by (0-50)
	 * @return 	string
	 */
	private function sass_darken($hex, $percent) {
		preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $hex, $primary_colors);
		str_replace('%', '', $percent);
		$color = "#";
		for($i = 1; $i <= 3; $i++) {
			$primary_colors[$i] = hexdec($primary_colors[$i]);
			$primary_colors[$i] = round($primary_colors[$i] * (100-($percent*2))/100);
			$color .= str_pad(dechex($primary_colors[$i]), 2, '0', STR_PAD_LEFT);
		}
		return $color;
	}


	private function _getView(&$args, $default = "card")
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/view=(\blist\b)/i', $arg, $matches))
			{
				$view = (isset($matches[1]) ? $matches[1] : '');
				unset($args[$k]);
				return $view;
			}
		}

		return $default;
	}

	/**
	 * Calculating Color Contrast, by Brian Suda:  https://24ways.org/2010/calculating-color-contrast/
	 *
	 * @param 	$hexcolor	Color (string; hexidecimal)
	 * @return 	string
	 */
	function getContrastYIQ($hexcolor){
		$r = hexdec(substr($hexcolor,0,2))/1000;
		$g = hexdec(substr($hexcolor,2,2))/1000;
		$b = hexdec(substr($hexcolor,4,2))/1000;
		$yiq = 1 - (($r*299)+($g*587)+($b*114))/255;
		return ($yiq < 0.5) ? 'black' : 'white';
	}
}
