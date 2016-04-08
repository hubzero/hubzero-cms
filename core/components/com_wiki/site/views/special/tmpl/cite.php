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

$page = $this->book->pages()
	->whereEquals('pagename', Request::getVar('page', ''))
	->whereEquals('path', Request::getVar('scope', ''))
	->row();

if ($v = Request::getInt('version', 0))
{
	$revision = $page->versions()
		->whereEquals('id', $v)
		->row();
}
else
{
	$revision = $page->version();
}

$yFormat = 'Y';
$apaFormat = 'Y, M d';
$apaFormatRetrieved = 'H:i, M d, Y';
$mlaFormat = 'd M. Y';
$mlaFormatRetrieved = 'd M. Y';
$mhraFormat = 'd M Y H:i';
$mhraFormatRetrieved = 'd M Y';
$cbeFormat = 'Y M d';
$bluebookFormat = 'M. d, Y';
$amaFormat = 'M d, Y, H:i';
$amaFormatRetrieved = 'M d, Y';

$now = Date::toSql();

$permalink = rtrim(Request::base(), '/') . '/' . ltrim(Route::url($page->link() . '&version=' . $revision->get('version')), '/');
?>
<div class="admon-note">
<p>
	<strong>IMPORTANT NOTE</strong>: Most educators and professionals do not consider it appropriate to use tertiary sources such as encyclopedias as a sole source for any information—citing an encyclopedia as an important reference in footnotes or bibliographies may result in censure or a failing grade. Wiki articles should be used for background information, as a reference for correct terminology and search terms, and as a starting point for further research.
</p>
<p>
	As with any community-built reference, there is a possibility for error in content&ndash;please check your facts against multiple sources and read our disclaimers for more information.
</p>
</div>
<div class="wiki-box highlight-box">
	<h3>Bibliographic details for "<?php echo $this->escape(stripslashes($page->title)); ?>"</h3>
	<ul>
		<li>
			Page name: <?php echo $this->escape(stripslashes($page->get('pagename'))); ?>
		</li>
		<li>
			Author: <?php echo $this->escape(Config::get('sitename')); ?> contributors
		</li>
		<li>
			Publisher: <i><?php echo $this->escape(Config::get('sitename')); ?></i>
		</li>
		<li>
			Date of last revision: <?php echo $this->escape(stripslashes($revision->get('created'))); ?>
		</li>
		<li>
			Date retrieved: <?php echo $now; ?>
		</li>
		<li>
			Permanent link: <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a>
		</li>
		<li>
			Primary contributors: <a href="<?php echo Route::url($page->link('history')); ?>">Revision history</a>
		</li>
		<li>
			Page version ID: <?php echo $this->escape($revision->get('id')); ?>
		</li>
	</ul>
	<p>
		Please remember to check your manual of style, standards guide or instructor's guidelines for the exact syntax to suit your needs.
	</p>
</div>

<div class="wiki-box">
	<h3>Citation styles for "<?php echo $this->escape(stripslashes($page->get('title'))); ?>"</h3>

	<h4>APA style</h4>
	<p>
		<?php echo $this->escape(stripslashes($page->get('title'))); ?>. (<?php echo Date::of($revision->get('created'))->format($apaFormat); ?>). In <i><?php echo $this->escape(Config::get('sitename')); ?></i>. Retrieved <?php echo Date::of($now)->format($apaFormatRetrieved); ?>, from <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a>
	</p>

	<h4>MLA style</h4>
	<p>
		<?php echo $this->escape(Config::get('sitename')); ?> contributors. "<?php echo $this->escape(stripslashes($page->get('title'))); ?>." <i><?php echo $this->escape(Config::get('sitename')); ?></i>. <?php echo $this->escape(Config::get('sitename')); ?>, <?php echo Date::of($revision->get('created'))->format($apaFormat); ?>. Web. <?php echo Date::of($now)->format($mlaFormatRetrieved); ?>
	</p>

	<h4>MHRA style</h4>
	<p>
		<?php echo $this->escape(Config::get('sitename')); ?> contributors, '<?php echo $this->escape(stripslashes($page->get('title'))); ?>,' <i><?php echo $this->escape(Config::get('sitename')); ?></i>, <?php echo Date::of($revision->get('created'))->format($mhraFormat); ?>, &lt;<a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a>&gt; [accessed <?php echo Date::of($now)->format($mhraFormatRetrieved); ?>]
	</p>

	<h4>Chicago style</h4>
	<p>
		<?php echo $this->escape(Config::get('sitename')); ?> contributors, "<?php echo $this->escape(stripslashes($page->get('title'))); ?>," <i><?php echo $this->escape(Config::get('sitename')); ?></i>, <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a> (accessed <?php echo Date::of($now)->format($mhraFormatRetrieved); ?>).
	</p>

	<h4>CBE/CSE style</h4>
	<p>
		<?php echo $this->escape(Config::get('sitename')); ?> contributors. <?php echo $this->escape(stripslashes($page->get('title'))); ?> [Internet]. <?php echo $this->escape(Config::get('sitename')); ?>; <?php echo Date::of($revision->get('created'))->format($bluebookFormat); ?> [cited <?php echo Date::of($now)->format($cbeFormat); ?>]. Available from: <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a>.
	</p>

	<h4>Bluebook style</h4>
	<p>
		<?php echo $this->escape(stripslashes($page->get('title'))); ?>, <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a> (last visited <?php echo Date::of($now)->format($bluebookFormat); ?>).
	</p>

	<h4>Bluebook: Harvard JOLT style</h4>
	<p>
		<?php echo $this->escape(Config::get('sitename')); ?>, <i><?php echo $this->escape(stripslashes($page->get('title'))); ?></i>, <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a> (optional description here) (as of <?php echo Date::of($now)->format($bluebookFormat); ?>).
	</p>

	<h4>AMA style</h4>
	<p>
		<?php echo $this->escape(Config::get('sitename')); ?> contributors. <?php echo $this->escape(stripslashes($page->get('title'))); ?>. <?php echo $this->escape(Config::get('sitename')); ?>. <?php echo Date::of($revision->get('created'))->format($amaFormat); ?>. Available at <a href="<?php echo $permalink; ?>"><?php echo $permalink; ?></a>. Accessed <?php echo Date::of($now)->format($amaFormatRetrieved); ?>.
	</p>

	<h4>BibTeX entry</h4>
<pre>
@misc{ wiki:xxx,
    author = "<?php echo $this->escape(Config::get('sitename')); ?>",
    title = "<?php echo $this->escape(stripslashes($page->get('title'))); ?> --- <?php echo $this->escape(Config::get('sitename')); ?>",
    year = "<?php echo Date::of($revision->get('created'))->format($yFormat); ?>",
    url = "<?php echo $permalink; ?>",
    note = "[Online; accessed 1-October-2012]"
}
</pre>