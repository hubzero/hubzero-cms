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

if (!defined('HG_INLINE'))
{
	Document::setTitle(Lang::txt('COM_SEARCH'));
}

// Build the querystring from allowed values rather than
// using $_SERVER['QUERY_STRING'] as 'QUERY_STRING' may
// contain potentially dangerous values (e.g., quote marks,
// javascript. etc.)
$qs = array();
if ($terms = $this->req->getTerms())
{
	$qs[] = 'terms=' . $terms;
}
foreach ($this->req->getTags() as $tag)
{
	$qs[] = 'tags[]=' . $tag['id'];
}
if ($domain = $this->req->getDomain())
{
	$qs[] = 'domain=' . $domain;
}
if ($group = $this->req->getGroup())
{
	$qs[] = 'group=' . $group;
}
if ($page = $this->req->getPage())
{
	$qs[] = 'page=' . $page;
}
if ($per = $this->req->getPerPage())
{
	$qs[] = 'per=' . $per;
}
if ($contributors = $this->req->getContributors())
{
	foreach ($contributors as $cont)
	{
		$qs[] = 'contributors[]=' . $cont['id'];
	}
}
if ($timeframe = $this->req->getTimeframe())
{
	$qs[] = 'timeframe=' . $timeframe;
}
$qs = implode('&', $qs);

$this->css('./hubgraph/hubgraph.css')
     ->js('./hubgraph/hubgraph-update.js')
     ->js('./hubgraph/jquery.inview.js');

if (isset($this->results['js'])): ?>
	<script type="text/javascript">
		<?php echo $this->results['js'] ?>
	</script>
<?php endif; ?>
<?php if (isset($this->results['css'])): ?>
	<style type="text/css">
		<?php echo $this->results['css'] ?>
	</style>
<?php endif; ?>

<?php if (!defined('HG_INLINE')): ?>
	<header id="content-header">
		<h2><?php echo Lang::txt('COM_SEARCH'); ?></h2>
	</header><!-- / #content-header -->
<?php endif; ?>

	<form id="search-form" class="section-inner search" action="<?php echo Route::url('index.php?option=com_search'); ?>" method="get">
		<div class="bar">
			<fieldset>
				<input type="text" autocomplete="off" name="terms" class="terms" placeholder="<?php echo Lang::txt('COM_SEARCH_TERMS_PLACEHOLDER'); ?>" value="<?php echo a($this->req->getTerms()) ?>" />
				<input type="hidden" name="domain" value="<?php echo Request::getString('domain', ''); ?>" />
				<a class="clear" href="<?php echo preg_replace('/[?&]+$/', '', $this->base . ($qs ? '?' . ltrim(preg_replace('/&?terms=[^&]*/', '', $qs), '&') : '')) ?>">&#x2716;</a>
				<button class="submit btn" type="submit"><span><?php echo Lang::txt('COM_SEARCH_SEARCH'); ?></span></button>
			</fieldset>
			<ul class="complete">
				<li class="cat users" title="<?php echo Lang::txt('COM_SEARCH_HUBGRAPH_CONTRIBUTORS'); ?>"><ul></ul></li>
				<li class="cat tags" title="<?php echo Lang::txt('COM_SEARCH_HUBGRAPH_TAGS'); ?>"><ul></ul></li>
				<li class="cat orgs" title="<?php echo Lang::txt('COM_SEARCH_HUBGRAPH_ORGANIZATION'); ?>"><ul></ul></li>
				<li class="cat text"><ul></ul></li>
			</ul>
		</div>
		<?php 
		if (isset($this->results['clientDebug'])):
			define('HG_DEBUG', 1);
		endif;

		if (isset($this->results['html'])):
			echo $this->results['html'];
		endif;

		if ($this->results['terms']['autocorrected']):
			$terms = $this->escape($this->req->getTerms());
			foreach ($this->results['terms']['autocorrected'] as $k => $v):
				$terms = preg_replace('#' . preg_quote($k) . '#i', '<strong>' . $v . '</strong>', $terms);
			endforeach;
		elseif ($this->results['terms']['suggested']):
			$terms = $this->escape($this->req->getTerms());
			$rawTerms = $terms;
			foreach ($this->results['terms']['suggested'] as $k => $v):
				$terms    = str_replace($k, '<strong>' . $v . '</strong>', strtolower($terms));
				$rawTerms = str_replace($k, $v, $rawTerms);
			endforeach;
			$link = preg_replace('/\?terms=[^&]*/', 'terms=' . $rawTerms, $qs);
			if ($link[0] != '?'):
				$link = '?' . $link;
			endif;
		endif;

		$view = $this->view('page')
			->set('req', $this->req)
			->set('results', $this->results)
			->set('perPage', $this->perPage)
			->set('domainMap', $this->domainMap);
		if (isset($terms))
		{
			$view->set('terms', $terms);
		}
		$view->display();
		?>
	</form>
