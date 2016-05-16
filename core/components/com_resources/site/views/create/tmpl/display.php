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

// No direct access.
defined('_HZEXEC_') or die();

$database = App::get('db');

$submissions = null;
if (!User::isGuest())
{
	$query  = "SELECT DISTINCT R.id, R.title, R.type, R.logical_type AS logicaltype,
						AA.subtable, R.created, R.created_by, R.published, R.publish_up, R.standalone,
						R.rating, R.times_rated, R.alias, R.ranking, rt.type AS typetitle ";
	$query .= "FROM #__author_assoc AS AA, #__resource_types AS rt, #__resources AS R ";
	$query .= "LEFT JOIN #__resource_types AS t ON R.logical_type=t.id ";
	$query .= "WHERE AA.authorid = ". User::get('id') ." ";
	$query .= "AND R.id = AA.subid ";
	$query .= "AND AA.subtable = 'resources' ";
	$query .= "AND R.standalone=1 AND R.type=rt.id AND (R.published=2 OR R.published=3) AND R.type!=7 ";
	$query .= "ORDER BY published ASC, title ASC";

	$database->setQuery($query);
	$submissions = $database->loadObjectList();
}

$this->css('introduction.css', 'system')
     ->css('create.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section id="introduction" class="contribute section">
	<div class="grid">
		<div class="col span9">
			<div class="col span6">
				<h3>Present your work!</h3>
				<p>Become a contributor and share your work with the community! Contributing content is easy. Our step-by-step forms will guide you through the process.</p>
			</div>
			<div class="col span6 omega">
				<h3>What do I need?</h3>
				<p>The submission process will guide you through step-by-step, but for more detailed instructions on what can be submitted and how, please see the list of submission types below.</p>
			</div>
		</div>
		<div class="col span3 omega">
			<p id="getstarted">
				<a class="btn btn-primary" href="<?php echo Route::url('index.php?option='.$this->option.'&task=draft'); ?>">Get Started &rsaquo;</a>
			</p>
		</div><!-- / .aside -->
	</div>
</section><!-- / #introduction.section -->

<section class="section">

<?php if (!User::isGuest()) { ?>
	<div class="grid">
		<div class="col span3">
			<h2><?php echo Lang::txt('In Progress'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
		<?php if ($submissions) { ?>
			<table id="submissions">
				<thead>
					<tr>
						<th scope="col"><?php echo Lang::txt('Title'); ?></th>
						<th scope="col" colspan="3"><?php echo Lang::txt('Associations'); ?></th>
						<th scope="col" colspan="2"><?php echo Lang::txt('Status'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$cls = 'even';
				foreach ($submissions as $submission)
				{
					$cls = ($cls == 'even') ? 'odd' : 'even';

					$resource = Components\Resources\Models\Orm\Resource::oneOrNew($submission->id);

					switch ($resource->get('published'))
					{
						case 1: $state = 'published';  break;  // published
						case 2: $state = 'draft';      break;  // draft
						case 3: $state = 'pending';    break;  // pending
						case 0:
						default: $state = 'unpublished';  break;  // unpublished
					}

					$attachments = $resource->children()->total();

					$authors =  $resource->authors()->total();

					$tags = $resource->tags()->count();
					?>
					<tr class="<?php echo $cls; ?>">
						<td><?php if ($submission->published == 2) { ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft&step=1&id='.$submission->id); ?>"><?php } ?><?php echo stripslashes($submission->title); ?><?php if ($submission->published == 2) { ?></a><?php } ?><br /><span class="type"><?php echo stripslashes($submission->typetitle); ?></span></td>
						<td><?php if ($submission->published == 2) { ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft&step=2&id='.$submission->id); ?>"><?php } ?><?php echo $attachments; ?> attachment(s)<?php if ($submission->published == 2) { ?></a><?php } ?></td>
						<td><?php if ($submission->published == 2) { ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft&step=3&id='.$submission->id); ?>"><?php } ?><?php echo $authors; ?> author(s)<?php if ($submission->published == 2) { ?></a><?php } ?></td>
						<td><?php if ($submission->published == 2) { ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft&step=4&id='.$submission->id); ?>"><?php } ?><?php echo $tags; ?> tag(s)<?php if ($submission->published == 2) { ?></a><?php } ?></td>
						<td>
							<span class="<?php echo $state; ?> status"><?php echo $state; ?></span>
							<?php if ($submission->published == 2) { ?>
							<br /><a class="review" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft&step=5&id='.$submission->id); ?>"><?php echo Lang::txt('Review &amp; Submit &rsaquo;'); ?></a>
							<?php } elseif ($submission->published == 3) { ?>
							<br /><a class="retract" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=retract&id='.$submission->id); ?>"><?php echo Lang::txt('&lsaquo; Retract'); ?></a>
							<?php } ?>
						</td>
						<td><a class="icon-delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=discard&id='.$submission->id); ?>" title="<?php echo Lang::txt('Delete'); ?>"><?php echo Lang::txt('Delete'); ?></a></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		<?php } else { ?>
			<p class="info">
				<strong>You currently have no contributions in progress.</strong><br /><br />
				Once you've started a new contribution, you can proceed at your leisure. Stop half-way through and watch a presentation, go to lunch, even close the browser and come back a different day! Your contribution will be waiting just as you left it, ready to continue at any time.
			</p>
		<?php } ?>
		</div><!-- / .col span9 omega -->
	</div>
<?php } ?>

	<div class="grid">
		<div class="col span3">
			<h2>Before starting</h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
				<div class="col span6">
					<h3>Intellectual Property Considerations</h3>
					<p>All materials contributed must have <strong>clearly defined rights and privileges</strong>. Online presentations and instructional material are normally licensed under <a class="legal creative-commons" href="/legal/cc" title="Learn more about Creative Commons">Creative Commons 3</a>. Read <a class="legal licensing" href="/legal/licensing">more details</a> about our licensing policies.</p>
				</div><!-- / .col span6 -->
				<div class="col span6 omega">
					<h3>Questions or concerns?</h3>
					<p>We hope that our self-service upload process is intuitive and easy to use. If you encounter any problems during the upload process or need assistance of any kind, please <a class="new-ticket" href="/support/ticket/new">file a trouble report</a>.</p>
				</div><!-- / .col span6 -->
			</div><!-- / .grid -->
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->

<?php
$categories = Components\Resources\Models\Type::getMajorTypes();
if ($categories) {
?>
	<div class="grid">
		<div class="col span3">
			<h2>What can I contribute?</h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
		<?php
		$i = 0;
		$clm = '';

		foreach ($categories as $category)
		{
			if ($category->get('contributable') != 1)
			{
				continue;
			}

			$i++;
			switch ($i)
			{
				case 3: $clm = 'omega'; break;
				case 2: $clm = ''; break;
				case 1:
				default: $clm = ''; break;
			}

			if (substr($category->get('alias'), -3) == 'ies') {
				$cls = $category->get('alias');
			} else {
				$cls = substr($category->get('alias'), 0, -1);
			}
			?>
			<div class="col span-third <?php echo $clm; ?>">
				<div class="<?php echo $cls; ?>">
					<h3><a href="<?php echo Route::url('index.php?option='.$this->option.'&task=draft&step=1&type='.$category->get('id')); ?>"><?php echo stripslashes($category->get('type')); ?></a></h3>
					<p><?php echo stripslashes($category->description); ?></p>
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
			</div><!-- / .col span-third omega -->
			<?php
		}
		?>
			</div>
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->
<?php
}
?>

</section><!-- / .section -->
