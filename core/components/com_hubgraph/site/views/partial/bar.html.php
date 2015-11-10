<?php
defined('_HZEXEC_') or die();
?>
<div class="search-bar">
	<input type="text" name="terms" class="terms" autocomplete="off" value="<?php echo str_replace('"', '&quot;', $req->getTerms()); ?>" />
	<a href="<?php echo preg_replace('/[?&]+$/', '', $base.($_SERVER['QUERY_STRING'] ? '?'.preg_replace('/^&/', '', preg_replace('/&?terms=[^&]*/', '', urldecode($_SERVER['QUERY_STRING']))) : '')); ?>"></a>
	<button type="submit">Search</button>
</div>
<ul id="inventory">
<?php foreach ($req->getTags() as $tag): ?>
	<li class="tag">
		<a class="remove" href="<?php echo preg_replace('/[?&]+$/', '', $base.'?'.preg_replace('/^&/', '', preg_replace('/&?tags\[\]='.$tag['id'].'/', '', urldecode($_SERVER['QUERY_STRING'])))); ?>"></a>
		<input type="hidden" name="tags[]" value="<?php echo $tag['id']; ?>" />
		<strong>Tag: </strong><?php echo h($tag['tag']); ?>
	</li>
<?php endforeach; ?>
<?php if (($domain = $req->getDomain())): ?>
	<li class="domain">
		<a class="remove" href="<?php echo preg_replace('/[?&]+$/', '', $base.'?'.preg_replace('/^&/', '', preg_replace('/&?domain=[^&]*/', '', $_SERVER['QUERY_STRING']))); ?>"></a>
		<input type="hidden" name="domain" value="<?php echo a($domain); ?>" />
		<strong>Section: </strong><?php echo ucfirst(str_replace('~', ' &ndash; ', h($domain))); ?>
	</li>
<?php endif; ?>
<?php if (($group = $req->getGroup())): ?>
	<li class="group">
		<a class="remove" href="<?php echo preg_replace('/[?&]+$/', '', $base.'?'.preg_replace('/^&/', '', preg_replace('/&?group=[^&]*/', '', $_SERVER['QUERY_STRING']))); ?>"></a>
		<input type="hidden" name="group" value="<?php echo a($group); ?>" />
		<strong>Group: </strong><?php echo str_replace('~', ' &ndash; ', h($req->getGroupName($group))); ?>
	</li>
<?php endif; ?>
<?php if (($contributors = $req->getContributors())): ?>
	<?php foreach ($contributors as $cont): ?>
		<li class="contributor">
			<a class="remove" href="<?php echo preg_replace('/[?&]+$/', '', $base.'?'.preg_replace('/^&/', '', preg_replace('/&?contributors\[\]='.$cont['id'].'/', '', urldecode($_SERVER['QUERY_STRING'])))); ?>"></a>
			<input type="hidden" name="contributors[]" value="<?php echo a($cont['id']); ?>" />
			<strong>Contributor: </strong><?php echo h($cont['name']); ?>
		</li>
	<?php endforeach; ?>
<?php endif; ?>
<?php if (($timeframe = $req->getTimeframe())): ?>
	<li class="group">
		<a class="remove" href="<?php echo preg_replace('/[?&]+$/', '', $base.'?'.preg_replace('/^&/', '', preg_replace('/&?timeframe=[^&]*/', '', $_SERVER['QUERY_STRING']))); ?>"></a>
		<input type="hidden" name="group" value="<?php echo a($group); ?>" />
		<strong>Timeframe: </strong><?php echo h(preg_match('/\d\d\d\d/', $timeframe) ? $timeframe : 'within the last '.$timeframe); ?>
	</li>
<?php endif; ?>
</ul>
<ol id="tag-suggestions"></ol>
<ol id="suggestions"></ol>

