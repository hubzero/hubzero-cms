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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css('introduction.css', 'system')
     ->css()
     ->js();

Document::addScript('/app/templates' . DS . App::get('template')->template . DS . 'js' . DS . 'masonry.pkgd.min.js');
Document::addScript('/app/templates' . DS . App::get('template')->template . DS . 'js' . DS . 'fit.js');

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>">
				<?php echo Lang::txt('Submit a resource'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span9">
			<div class="grid">
				<div class="col span6">
					<h3><?php echo Lang::txt('What are resources?'); ?></h3>
					<p><?php echo Lang::txt('Resources are user-submitted pieces of content that range from video presentations to publications to simulation tools.'); ?></p>
				</div>
				<div class="col span6 omega">
					<h3><?php echo Lang::txt('Who can submit a resource?'); ?></h3>
					<p><?php echo Lang::txt('Anyone can submit a resource! Resources must be relevant to the community and may undergo a short approval process to ensure all appropriate files and information are included.'); ?></p>
				</div>
			</div>
		</div>
		<div class="col span3 omega">
			<p>
				<a class="popup" href="<?php echo Route::url('index.php?option=com_help&component=resources&page=index'); ?>">
					<?php echo Lang::txt('Need Help?'); ?>
				</a>
			</p>
		</div>
	</div><!-- / .aside -->
</section><!-- / #introduction.section -->

<section class="section">

	<div class="grid">
		<div class="col span3">
			<h2><?php echo Lang::txt('Find a resource'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
				<div class="col span-half">
					<form action="<?php echo Route::url('index.php?option=com_search'); ?>" method="get" class="search">
						<fieldset>
							<p>
								<label for="rsearch"><?php echo Lang::txt('Keyword or phrase:'); ?></label>
								<input type="text" name="terms" id="rsearch" value="" />
								<input type="hidden" name="domain" value="resources" />
								<input type="submit" value="<?php echo Lang::txt('Search'); ?>" />
							</p>
						</fieldset>
					</form>
				</div><!-- / .col span-half -->
				<div class="col span-half omega">
					<div class="browse">
						<p><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo Lang::txt('Browse all'); ?></a> in resources</p>
					</div>
					<div class="browse popular">
						<p><a href="#popular"><?php echo Lang::txt('Browse new and popular'); ?></a> in resources</p>
					</div>
				</div><!-- / .col span-half -->
			</div><!-- / .grid -->
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->

<?php
if ($this->categories) {
?>
	<div class="grid">
		<div class="col span3">
			<h2><?php echo Lang::txt('Categories'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
				<div class="col span-third ">
					<div class="resource-type teaching">
						<h3><a href="<?php echo Route::url('index.php?option=' . $this->option . '&type=teachingandreference'); ?>">Teaching &amp; Reference Material </a></h3>

						<p>The catch-all resource type, including materials used in a teaching context (lecture notes, in-class modules, exams, problem sets, etc) or for reference or archival purposes (books, articles, recorded talks, websites, etc).</p>

						<p><a class="read-more" href="<?php echo Route::url('index.php?option=' . $this->option . '&type=teachingandreference'); ?>" title="Browse Teaching &amp; Reference Material">Browse <span>Teaching &amp; Reference Material </span>&rsaquo; </a></p>
					</div>
				</div>

				<div class="col span-third ">
					<div class="resource-type series">
						<h3><a href="<?php echo Route::url('index.php?option=' . $this->option . '&type=meetings'); ?>">Meetings &amp; Workshops </a></h3>

						<p>Collection of presentations&nbsp;occurring in the same time and space, including seminars, workshops, and conferences.</p>

						<p><a class="read-more" href="<?php echo Route::url('index.php?option=' . $this->option . '&type=meetings'); ?>" title="Browse Meetings &amp; Workshops">Browse <span>Meetings &amp; Workshops </span>&rsaquo; </a></p>
					</div>
				</div>

				<div class="col span-third omega">
					<div class="resource-type toolsoffsite">
						<h3><a href="<?php echo Route::url('index.php?option=' . $this->option . '&type=software'); ?>">Software</a></h3>

						<p>Software resources consist of simulation, modeling and data analysis tools, many of which can be executed on QUBES Hub (on-site) or downloaded/executed elsewhere on the web (off-site).</p>

						<p><a class="read-more" href="<?php echo Route::url('index.php?option=' . $this->option . '&type=software'); ?>" title="Browse Software">Browse <span>Software</span>&rsaquo; </a></p>
					</div>
				</div>
			</div>
			<div class="grid">
				<div class="col span-third ">
					<div class="resource-type data">
						<h3><a href="<?php echo Route::url('index.php?option=' . $this->option . '&type=data'); ?>">Data </a></h3>

						<p>Data collected from field work or experiment: could be raw or processed data.</p>

						<p><a class="read-more" href="<?php echo Route::url('index.php?option=' . $this->option . '&type=data'); ?>" title="Browse Data">Browse <span>Data </span>&rsaquo; </a></p>
					</div>
				</div>

				<div class="col span-third ">
					<div class="resource-type model">
						<h3><a href="<?php echo Route::url('index.php?option=' . $this->option . '&type=models'); ?>">Models </a></h3>

						<p>An abstract model of a biological process: could be mathematical, statistical, or computational.</p>

						<p><a class="read-more" href="<?php echo Route::url('index.php?option=' . $this->option . '&type=models'); ?>" title="Browse Models">Browse <span>Models </span>&rsaquo; </a></p>
					</div>
				</div>

				<div class="col span-third omega">&nbsp;</div>
			<?php
			/*$i = 0;
			$clm = '';

			// Try to sort by predefined ordering
			$order = array('teachingandreference', 'series', 'tools', 'data', 'models');
			$categories = array();
			$cats = array();
			foreach ($this->categories as $category)
			{
				$found = false;

				foreach ($order as $z => $alias)
				{
					if ($category->alias == $alias)
					{
						$categories[$z] = $category;
						$found = true;
					}
				}

				if ($found)
				{
					continue;
				}

				$cats[] = $category;
			}
			ksort($categories);
			foreach ($cats as $cat)
			{
				array_push($categories, $cat);
			}

			foreach ($categories as $category)
			{
				if ($category->id == 7 && !JComponentHelper::isEnabled('com_tools', true))
				{
					continue;
				}

				if ($category->alias == 'toolsoffsite')
				{
					continue;
				}

				if ($category->alias == 'series')
				{
					$category->alias = 'meetings';
				}

				$i++;
				switch ($i)
				{
					case 3: $clm = 'omega'; break;
					case 2: $clm = ''; break;
					case 1:
					default: $clm = ''; break;
				}

				if (substr($category->alias, -3) == 'ies')
				{
					$cls = $category->alias;
				}
				else
				{
					$cls = rtrim($category->alias, 's');
				}

				if (substr($category->alias, 0, strlen('teaching')) == 'teaching')
				{
					$cls = 'teaching';
				}

				if ($category->alias == 'tools')
				{
					$category->type = 'Software';
					$category->alias = 'software';
					$category->description = 'Software resources consist of simulation, modeling and data analysis tools, many of which can be executed on QUBES Hub (on-site) or downloaded/executed elsewhere on the web (off-site).';
				}
				?>
				<div class="col span-third <?php echo $clm; ?>">
					<div class="resource-type <?php echo $cls; ?>">
						<h3>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&type=' . $category->alias); ?>">
								<?php echo $this->escape(strip_tags(stripslashes($category->type))); ?>
							</a>
						</h3>
						<p>
							<?php echo html_entity_decode(strip_tags(stripslashes($this->escape($category->description)))); ?>
						</p>
						<p>
							<a class="read-more" href="<?php echo Route::url('index.php?option=' . $this->option . '&type=' . $category->alias); ?>" title="<?php echo JText::sprintf('Browse %s', $this->escape(stripslashes($category->type))); ?>">
								<?php echo JText::sprintf('Browse <span>%s </span>&rsaquo;', $this->escape(stripslashes($category->type))); ?>
							</a>
						</p>
					</div>
				</div><!-- / .col span-third <?php echo $clm; ?> -->
				<?php
				if ($clm == 'omega') {
					echo '</div><div class="grid">';
					$clm = '';
					$i = 0;
				}
			}
			if ($i == 1) {
				?>
				<div class="col span-third">
					<p> </p>
				</div><!-- / .col span-third -->
				<?php
			}
			if ($i == 1 || $i == 2) {
				?>
				<div class="col span-third omega">
					<p> </p>
				</div><!-- / .col span-third -->
				<?php
			}*/
			?>
			</div><!-- / .grid -->
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->
<?php
}
?>

</section><!-- / .section -->

<?php

$limit = 16;

// get the most recent resources
$db = App::get('db');

$query = '	SELECT r.id, r.title, r.publish_up, r.created, t.alias  AS tAlias, t.type as tType, p.uidNumber, p.`name` 
			FROM #__resources r LEFT JOIN #__resource_types t ON r.type=t.id
			LEFT JOIN `#__xprofiles` p ON p.`uidNumber` = r.`created_by`
			WHERE r.published=1 AND r.standalone=1 AND r.type!=8 AND (r.access=0 OR r.access=3) ORDER BY publish_up DESC, created DESC LIMIT ' . $limit;

$db->setQuery($query);
$results = $db->loadObjectList();
//print_r($results); die;

// get the highest rated resources
$query = '	SELECT r.id, r.title, r.rating, t.alias AS tAlias, t.type as tType, p.uidNumber, p.`name`  
			FROM `#__resources` r 
			LEFT JOIN #__resource_types t ON r.type=t.id
			LEFT JOIN `#__xprofiles` p ON p.`uidNumber` = r.`created_by`
			WHERE r.rating >= 4.4 AND r.published=1 AND r.standalone=1 AND r.type!=8 AND (r.access=0 OR r.access=3) ORDER BY rating DESC LIMIT ' . ($limit / 2); 

$db->setQuery($query);
$rated_results = $db->loadObjectList();

// combine resources

// keep track of IDs
$ids = array();

// combined resources
$featured = array();

foreach($rated_results as $res)
{
	$featured[] = array('type' => 'rating', 'res' => $res);
	$ids[] = $res->id;
}

foreach ($results as $res)
{
	if (sizeof($featured) == $limit)
	{
		break;
	}
	if (!in_array($res->id, $ids))
	{
		$featured[] = array('type' => 'new', 'res' => $res);
	}
}
shuffle($featured);
//print_r($featured); die;
?>

<div class="explore" id="popular">
	<h2><span>explore</span> the freshest and most popular</h2>

	<div class="inner cf">
		<?php
			foreach ($featured as $result)
			{
				$res = $result['res'];

				// resolve the css class
				$class = $res->tAlias;
				if (substr($res->tAlias, 0, 4) == 'tool')
				{
					$class = 'tool';
				}
				elseif ($res->tAlias == 'teaching')
				{
					$class = 'instruct';
				}
				elseif ($res->tAlias == 'models')
				{
					$class = 'model';
				}

				if ($result['type'] == 'new')
				{
					// Date
					$published = $res->publish_up;
					//echo $published; die;
					if (strtotime($published) == -62169966000)
					{
						$published = $res->created;
						//die;	
					}
					$published = date(("Y-m-d"), strtotime($published));

					$start_date = new DateTime($published);
					$now = new DateTime('now');
					$diff = $now->diff($start_date);

					$since = '';
					if ($diff->y > 1) 
					{
						$since = 'more then a year ago';
					}
					elseif ($diff->m > 1)
					{
						$since = $diff->m . ' months ago';
					}
					elseif ($diff->m == 1)
					{
						$since = 'a month ago';
					}
					elseif ($diff->d > 1)
					{
						$since = $diff->d . ' days ago';
					}
					elseif ($diff->d == 1)
					{
						$since = 'a day ago';
					}
					elseif ($diff->h > 1)
					{
						$since = $diff->h . ' hours ago';
					}
					elseif ($diff->h == 1)
					{
						$since = 'an hour ago';
					}
					elseif ($diff->i > 1)
					{
						$since = $diff->i . ' minutes ago';
					}
					else
					{
						$since = 'just now';
					}
				}
				elseif($result['type'] == 'rating') {
					$rating = $res->rating;
				}

				echo '<div class="expoblock ' . $class . '">';
					echo '<a href="' . Route::url('index.php?option=com_resources&id=' . $res->id) . '"><div class="inn">';
						echo '<p>' . $this->escape($res->title) . '</p>';
					echo '</div></a>';

					if ($result['type'] == 'new')
					{
						echo '<p class="posted">' . $since . ' in <a href="' . Route::url('index.php?option=com_resources&alias=' . $res->tAlias) . '">' . $this->escape($res->tType) . '</a><br />';
					}
					elseif ($result['type'] == 'rating')
					{
						echo '<p class="posted">Rated ' . $rating . ' out of 5 in <a href="' . Route::url('index.php?option=com_resources&alias=' . $res->tAlias) . '">' . $this->escape($res->tType) . '</a><br />';
					}
					echo 'by <a href="' . Route::url('index.php?option=com_members&id=' . $res->uidNumber) . '">' . $this->escape($res->name) . '</a></p>';

					echo '<div class="go"><a href="' . Route::url('index.php?option=com_resources&id=' . $res->id) . '">Go</a></div>';
				echo '</div>'; 
			}
		?>
	</div>
</div>
