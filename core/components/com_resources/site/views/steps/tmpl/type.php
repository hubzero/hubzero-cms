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

$this->css('create.css')
     ->js('create.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-main btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>">
				<?php echo Lang::txt('Main page'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>

<section class="main section">
	<div class="subject">
		<div class="grid">
<?php
	if ($this->types)
	{
		$i = 0;
		$clm = '';
		foreach ($this->types as $type)
		{
			if ($type->contributable != 1)
			{
				continue;
			}
			if ($type->alias == 'tools')
			{
				if (!Component::isEnabled('com_tools', true))
				{
					continue;
				}
				$url = Route::url('index.php?option=com_tools&task=create');
			}
			else
			{
				$url = Route::url('index.php?option=' . $this->option . '&task=draft&step=' . $this->step . '&type=' . $type->id . ($this->group ? '&group=' . $this->group : ''));
			}

			$i++;
			switch ($i)
			{
				case 3: $clm = 'omega'; break;
				case 2: $clm = ''; break;
				case 1:
				default: $clm = ''; break;
			}

			if (substr($type->alias, -3) == 'ies')
			{
				$cls = $type->alias;
			}
			else
			{
				$cls = substr($type->alias, 0, -1);
			}
			// Need to do some decoding to ensure escaped characters aren't encoded twice.
			$type->description = html_entity_decode(str_replace('&amp;', '&', strip_tags(stripslashes($type->description))));
?>
		<div class="col span-third <?php echo $clm; ?>">
			<div class="type-container <?php echo $cls; ?>">
				<p class="type-button"><a class="btn icon-<?php echo $cls; ?>" href="<?php echo $url; ?>"><?php echo $this->escape(stripslashes($type->type)); ?></a></p>
				<p><?php echo $this->escape($type->description); ?></p>
			</div>
		</div><!-- / .col span-third <?php echo $clm; ?> -->
<?php
			if ($clm == 'omega')
			{
				echo '</div><div class="grid">';
				$clm = '';
				$i = 0;
			}
		}
		if ($i == 1 || $i == 2)
		{
?>
		<div class="col span-third omage">
			<p> </p>
		</div><!-- / .col span-third -->
<?php
		}
?>
		</div>
<?php
	}
?>

		<p class="info">
			In order for <?php echo Config::get('sitename'); ?> to display your content, we must be given legal license to do so. At the very least, <?php echo Config::get('sitename'); ?> must be authorized to
			hold, copy, distribute, and perform (play back) your material according to <a class="popup" href="<?php echo Request::base(true); ?>/legal/license">this agreement</a>.
			You will retain any copyrights to the materials and decide how they should be licensed for end-user access. We encourage you to <a class="popup" href="<?php echo Request::base(true); ?>/legal/licensing">license your contributions</a>
			so that others can build upon them.
		</p>

		<div class="container" id="entry-29">
			<div class="container-block">
				<h3>Frequently Asked Questions</h3>
				<div class="entry-content">
					<ul class="faq-list">
						<li><a href="#unknowntype">What if I want to contribute a type not listed here?</a></li>
						<li><a href="#drafts">What if I don't have all the materials right now?</a></li>
						<li><a href="#submission">What happens after submission?</a></li>
						<li><a href="#retract">Ooops! I missed something and/or submitted too early!</a></li>
					</ul>
				</div>
				<div class="entry-content">
					<h4><a name="unknowntype"></a>What if I want to contribute a type not listed here?</h4>
					<p>If you feel your contribution does not fit into any of our predefined types, please <a href="<?php echo Route::url('index.php?option=com_support&controller=tickets&task=new'); ?>">contact us</a> with details of</p>
					<ol>
						<li>what you wish to contribute, including a description and file types</li>
						<li>how you believe it should be categorized</li>
					</ol>
					<p>We will try to accommodate you or provide another suggestion.</p>
				</div>
				<div class="entry-content">
					<h4><a name="drafts"></a>What if I don't have all the materials right now?</h4>
					<p>This is perfectly fine. When you start a new contribution, it remains in a "draft" state until you decide to submit it for publication. You may work on portions of it at your leisure and return to a step at any time.</p>
					<p>You can find a list of your drafts through a variety of methods:</p>
					<ul>
						<li>Go to the "contributions" tab under your <a href="<?php echo Route::url('index.php?option=com_members&task=myaccount'); ?>">account</a>.</li>
						<li>Add the "My Drafts" module to your personalized dashboard (found <a href="<?php echo Route::url('index.php?option=com_members&task=myaccount'); ?>">here</a>).</li>
						<li>Visit the <a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>">new contribution</a> page.</li>
					</ul>
				</div>
				<div class="entry-content">
					<h4><a name="submission"></a>What happens after submission?</h4>
					<p>After submitting your contribution, it will be reviewed for completeness. If all appears satisfactory, the contribution will be approved and immediately appear in the <a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>">resources listing</a>.</p>
				</div>
				<div class="entry-content">
					<h4><a name="retract"></a>Ooops! I missed something and/or submitted too early!</h4>
					<p>No worries! You can retract a submission by following these steps:</p>
					<ul>
						<li>Visit the <a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>">new contribution</a> page.</li>
						<li>You should be presented with a list of your "drafts" and "pending" submissions. Find the (pending) contribution you wish to retract.</li>
						<li>Click "retract".</li>
					</ul>
				</div>
			</div><!-- / .container-block -->
		</div><!-- / .container -->
	</div><!-- /.subject -->
	<aside class="aside">
		<h3>Select a type</h3>
		<p>Select one of the resource types listed to proceed to the next step. The type of resource chosen can affect what information you will need to provide in the following steps.</p>
	</aside><!-- /.aside -->
</section><!-- /.main section -->
