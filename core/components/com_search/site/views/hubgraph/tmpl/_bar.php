<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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