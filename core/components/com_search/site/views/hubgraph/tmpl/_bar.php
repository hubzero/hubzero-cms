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

?>
<div class="search-bar">
	<input type="text" name="terms" class="terms" autocomplete="off" value="<?php echo str_replace('"', '&quot;', $this->req->getTerms()) ?>" />
	<a href="<?php echo preg_replace('/[?&]+$/', '', $this->base.($_SERVER['QUERY_STRING'] ? '?'.preg_replace('/^&/', '', preg_replace('/&?terms=[^&]*/', '', urldecode($_SERVER['QUERY_STRING']))) : '')) ?>"></a>
	<button type="submit">Search</button>
</div>
<ul id="inventory">
<?php foreach ($this->req->getTags() as $tag): ?>
	<li class="tag">
		<a class="remove" href="<?php echo preg_replace('/[?&]+$/', '', $this->base.'?'.preg_replace('/^&/', '', preg_replace('/&?tags\[\]='.$tag['id'].'/', '', urldecode($_SERVER['QUERY_STRING'])))) ?>"></a>
		<input type="hidden" name="tags[]" value="<?php echo $tag['id'] ?>" />
		<strong>Tag: </strong><?php echo h($tag['tag']) ?>
	</li>
<?php endforeach; ?>
<?php if (($domain = $this->req->getDomain())): ?>
	<li class="domain">
		<a class="remove" href="<?php echo preg_replace('/[?&]+$/', '', $this->base.'?'.preg_replace('/^&/', '', preg_replace('/&?domain=[^&]*/', '', $_SERVER['QUERY_STRING']))) ?>"></a>
		<input type="hidden" name="domain" value="<?php echo a($domain) ?>" />
		<strong>Section: </strong><?php echo ucfirst(str_replace('~', ' &ndash; ', h($domain))) ?>
	</li>
<?php endif; ?>
<?php if (($group = $this->req->getGroup())): ?>
	<li class="group">
		<a class="remove" href="<?php echo preg_replace('/[?&]+$/', '', $this->base.'?'.preg_replace('/^&/', '', preg_replace('/&?group=[^&]*/', '', $_SERVER['QUERY_STRING']))) ?>"></a>
		<input type="hidden" name="group" value="<?php echo a($group) ?>" />
		<strong>Group: </strong><?php echo str_replace('~', ' &ndash; ', h($this->req->getGroupName($group))) ?>
	</li>
<?php endif; ?>
<?php if (($contributors = $this->req->getContributors())): ?>
	<?php foreach ($contributors as $cont): ?>
		<li class="contributor">
			<a class="remove" href="<?php echo preg_replace('/[?&]+$/', '', $this->base.'?'.preg_replace('/^&/', '', preg_replace('/&?contributors\[\]='.$cont['id'].'/', '', urldecode($_SERVER['QUERY_STRING'])))) ?>"></a>
			<input type="hidden" name="contributors[]" value="<?php echo a($cont['id']) ?>" />
			<strong>Contributor: </strong><?php echo h($cont['name']) ?>
		</li>
	<?php endforeach; ?>
<?php endif; ?>
<?php if (($timeframe = $this->req->getTimeframe())): ?>
	<li class="group">
		<a class="remove" href="<?php echo preg_replace('/[?&]+$/', '', $this->base.'?'.preg_replace('/^&/', '', preg_replace('/&?timeframe=[^&]*/', '', $_SERVER['QUERY_STRING']))) ?>"></a>
		<input type="hidden" name="group" value="<?php echo a($group) ?>" />
		<strong>Timeframe: </strong><?php echo h(preg_match('/\d\d\d\d/', $timeframe) ? $timeframe : 'within the last '.$timeframe) ?>
	</li>
<?php endif; ?>
</ul>
<ol id="tag-suggestions"></ol>
<ol id="suggestions"></ol>