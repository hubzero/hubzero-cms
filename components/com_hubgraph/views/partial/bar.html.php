<? defined('JPATH_BASE') or die(); ?>
<div class="search-bar">
	<input type="text" name="terms" class="terms" autocomplete="off" value="<?= str_replace('"', '&quot;', $req->getTerms()) ?>" />
	<a href="<?= preg_replace('/[?&]+$/', '', $base.($_SERVER['QUERY_STRING'] ? '?'.preg_replace('/^&/', '', preg_replace('/&?terms=[^&]*/', '', urldecode($_SERVER['QUERY_STRING']))) : '')) ?>"></a>
	<button type="submit">Search</button>
</div>
<ul id="inventory">
<? foreach ($req->getTags() as $tag): ?>
	<li class="tag">
		<a class="remove" href="<?= preg_replace('/[?&]+$/', '', $base.'?'.preg_replace('/^&/', '', preg_replace('/&?tags\[\]='.$tag['id'].'/', '', urldecode($_SERVER['QUERY_STRING'])))) ?>"></a>
		<input type="hidden" name="tags[]" value="<?= $tag['id'] ?>" />
		<strong>Tag: </strong><?= h($tag['tag']) ?>
	</li>
<? endforeach; ?>
<? if (($domain = $req->getDomain())): ?>
	<li class="domain">
		<a class="remove" href="<?= preg_replace('/[?&]+$/', '', $base.'?'.preg_replace('/^&/', '', preg_replace('/&?domain=[^&]*/', '', $_SERVER['QUERY_STRING']))) ?>"></a>
		<input type="hidden" name="domain" value="<?= a($domain) ?>" />
		<strong>Section: </strong><?= ucfirst(str_replace('~', ' &ndash; ', h($domain))) ?>
	</li>
<? endif; ?>
<? if (($group = $req->getGroup())): ?>
	<li class="group">
		<a class="remove" href="<?= preg_replace('/[?&]+$/', '', $base.'?'.preg_replace('/^&/', '', preg_replace('/&?group=[^&]*/', '', $_SERVER['QUERY_STRING']))) ?>"></a>
		<input type="hidden" name="group" value="<?= a($group) ?>" />
		<strong>Group: </strong><?= str_replace('~', ' &ndash; ', h($req->getGroupName($group))) ?>
	</li>
<? endif; ?>
<? if (($contributors = $req->getContributors())): ?>
	<? foreach ($contributors as $cont): ?>
		<li class="contributor">
			<a class="remove" href="<?= preg_replace('/[?&]+$/', '', $base.'?'.preg_replace('/^&/', '', preg_replace('/&?contributors\[\]='.$cont['id'].'/', '', urldecode($_SERVER['QUERY_STRING'])))) ?>"></a>
			<input type="hidden" name="contributors[]" value="<?= a($cont['id']) ?>" />
			<strong>Contributor: </strong><?= h($cont['name']) ?>
		</li>
	<? endforeach; ?>
<? endif; ?>
<? if (($timeframe = $req->getTimeframe())): ?>
	<li class="group">
		<a class="remove" href="<?= preg_replace('/[?&]+$/', '', $base.'?'.preg_replace('/^&/', '', preg_replace('/&?timeframe=[^&]*/', '', $_SERVER['QUERY_STRING']))) ?>"></a>
		<input type="hidden" name="group" value="<?= a($group) ?>" />
		<strong>Timeframe: </strong><?= h(preg_match('/\d\d\d\d/', $timeframe) ? $timeframe : 'within the last '.$timeframe) ?>
	</li>
<? endif; ?>
</ul>
<ol id="tag-suggestions"></ol>
<ol id="suggestions"></ol>

