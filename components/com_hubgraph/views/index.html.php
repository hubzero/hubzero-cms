<? defined('JPATH_BASE') or die(); 
if (!defined('HG_INLINE')) {
	$doc->setTitle('Search');
}
$tags = $req->getTags();
$contributors = $req->getContributors();
$group = $req->getGroup();
$domainMap = $req->getDomainMap();
$loggedIn = (bool)JFactory::getUser()->id;
if (!defined('HG_AJAX')):
?>
<link type="text/css" href="/components/com_hubgraph/resources/selectmenu/themes/base/jquery.ui.core.css" rel="stylesheet" />
<link type="text/css" href="/components/com_hubgraph/resources/selectmenu/themes/base/jquery.ui.theme.css" rel="stylesheet" />
<link type="text/css" href="/components/com_hubgraph/resources/selectmenu/themes/base/jquery.ui.selectmenu.css" rel="stylesheet" />
<script type="text/javascript" src="/components/com_hubgraph/resources/selectmenu/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="/components/com_hubgraph/resources/selectmenu/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="/components/com_hubgraph/resources/selectmenu/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="/components/com_hubgraph/resources/selectmenu/ui/jquery.ui.selectmenu.js"></script>
<script type="text/javascript">
window.searchBase = '<?= $base ?>';
</script>
<form id="search-form" class="hubgraph" action="<?= $base ?>" method="get">
<div class="subject">
	<? require 'partial/bar.html.php'; ?>
</div>
<div id="hg-dynamic-wrap">
<? endif; ?>

<div class="aside criteria <?= defined('HG_INLINE') ? 'inline' : 'full' ?>">
	<? if ($results && $results['domains']): ?>
	<h2>Filters</h2>
		<h3 class="domains">Section</h3>
		<ol class="domains clear">
			<?
			$renderDomains = function($domains, $lineage = '') use(&$renderDomains, $domainMap) {
				foreach ($domains as $domain) {
					$key = $lineage == '' ? ucfirst($domain['name']) : $lineage.'~'.$domain['name'];
					echo '<li><button class="domain'.(isset($domainMap[$key]) ? ' current' : '').'" name="domain" value="'.$key.'" type="submit">'.str_replace('<', '&lt;', ucfirst($domain['name'])).' <span>'.$domain['count'].'</span></button>';
					if ($domain['children']) {
						echo '<ol class="sub domains">';
						$renderDomains($domain['children'], $key);
						echo '</ol>';
					}
					echo '</li>';
				}
			};
			$renderDomains($results['domains']);
			?>
		</ol>
		<? if ($results['tags']): ?>
		<h3>Tags <span class="sort alpha">A-Z</span><span class="sort number">#</span></h3>
		<ol class="tags clear">
			<? 
			foreach ($results['tags'] as $tag):
				$found = FALSE;
				foreach ($tags as $selectedTag):
					if ($selectedTag['id'] == $tag[0]):
						$found = TRUE;
						echo '<li class="selected"><a>'.h($tag[1]).'</a> <span>'.$tag[2].'</span></li>';
						break;
					endif;
				endforeach; 
				if (!$found):
			?>
				<li><button type="submit" name="tags[]" value="<?= $tag[0] ?>"><a><?= h($tag[1]) ?></a></button> <?= $tag[2] ?></li>
			<?
				endif;
			endforeach; 
			?>
		</ol>
		<? endif; ?>
		<? if ($results['groups'] && !$req->getGroup()): ?>
		<h3>Groups <span class="sort alpha">A-Z</span><span class="sort number">#</span></h3>
		<ol class="groups clear">
			<? 
			foreach ($results['groups'] as $gr):
				$found = FALSE;
				if ($group == $gr[0]):
					$found = TRUE;
					echo '<li class="selected">'.h($gr[1]).' <span>'.$gr[2].'</span></li>';
				endif;
				if (!$found):
			?>
				<li><button type="submit" name="group" value="<?= $gr[0] ?>"><?= h($gr[1]) ?></button> <?= $gr[2] ?></li>
			<?
				endif;
			endforeach; 
			?>
		</ol>
		<? endif; ?>
		<? if ($results['contributors']): ?>
		<h3>Contributors <span class="sort alpha">A-Z</span><span class="sort number">#</span></h3>
		<ol class="contributors clear">
			<? 
			foreach ($results['contributors'] as $contrib):
				$found = FALSE;
				foreach ($contributors as $selectedContrib):
					if ($selectedContrib['id'] == $contrib[0]):
						$found = TRUE;
						echo '<li class="selected">'.h($contrib[1]).' <span>'.$contrib[2].'</span></li>';
						break;
					endif;
				endforeach; 
				if (!$found):
			?>
				<li><button type="submit" name="contributors[]" value="<?= $contrib[0] ?>"><?= h($contrib[1]) ?></button> <?= $contrib[2] ?></li>
			<?
				endif;
			endforeach; 
			?>
		</ol>
		<? endif; ?>
		<? if ($results['timeframe']['by_year']): ?>
		<h3>Timeframe</h3>
		<? $years = array_keys($results['timeframe']['by_year']); rsort($years); ?>
		<ol class="timeframe clear">
			<? if ($results['timeframe']['day']): ?>
				<li><button type="submit" name="timeframe" value="day">today</button> <?= $results['timeframe']['day'] ?></li>
			<? endif; ?>
			<? if ($results['timeframe']['week']): ?>
				<li><button type="submit" name="timeframe" value="week">within the last week</button> <?= $results['timeframe']['week'] ?></li>
			<? endif; ?>
			<? if ($results['timeframe']['month']): ?>
				<li><button type="submit" name="timeframe" value="month">within the last month</button> <?= $results['timeframe']['month'] ?></li>
			<? endif; ?>
			<? if ($results['timeframe']['year']): ?>
				<li><button type="submit" name="timeframe" value="year">within the last year</button> <?= $results['timeframe']['year'] ?></li>
			<? endif; ?>
			<? foreach ($years as $year): ?>
				<li><button type="submit" name="timeframe" value="<?= $year ?>"><?= $year ?></button> <?= $results['timeframe']['by_year'][$year] ?></li>
			<? endforeach; ?>
		</ol>
		<? endif; ?>
	<? endif; ?>
	<p class="clear"> &nbsp; </p>
</div>
<? if ($results && $results['total']): ?>
	<? if ($results['terms']['autocorrected']):
		$terms = h($req->getTerms());
		foreach ($results['terms']['autocorrected'] as $k=>$v):
			$terms = preg_replace('#'.preg_quote($k).'#i', '<strong>'.$v.'</strong>', $terms);
		endforeach; 
	?>
		<p id="autocorrect-notice" class="info">&raquo; Showing results for <?= $terms ?></p>
	<? elseif ($results['terms']['suggested']):
		$terms = h($req->getTerms());
		$rawTerms = $terms;
		foreach ($results['terms']['suggested'] as $k=>$v):
			$terms = str_replace($k, '<strong>'.$v.'</strong>', strtolower($terms));
			$rawTerms = str_replace($k, $v, $rawTerms);
		endforeach; 
		$link = preg_replace('/terms=[^&]*/', 'terms='.$rawTerms, $_SERVER['QUERY_STRING']);
		if ($link[0] != '?') {
			$link = '?'.$link;
		}
	?>
		<p id="autocorrect-notice" class="info">&raquo; Did you mean <a href="<?= $base.$link ?>"><?= $terms ?></a>?</p>
	<? endif; ?>
<p id="count">Results <?= $results['offset'] + 1 ?>-<?= $results['offset'] + count($results['results']) ?> of <?= $results['total'] == count($results['results']) ? '' : 'about' ?> <?= $results['total'] ?></p>
<ol id="results">
<? if ($results && $results['criteria']): ?>
<li><ol id="criteria-details">
	<? if (isset($results['criteria']['contributors'])): ?>
		<? 
		foreach ($results['criteria']['contributors'] as $cont): 
			if (!$cont['public'] || (!$cont['img_href'] && !$cont['organization'] && !$cont['url'] && !$cont['bio'] && !$cont['tags'])) continue;
		?>
			<li class="contributor">
				<table>
					<tbody>
						<tr>
							<td class="contributor-head left">
								<h3><?= h($cont['title']) ?></h3>
								<? if ($cont['img_href'] && file_exists(JPATH_BASE.$cont['img_href'])): ?>
									<img class="profile-picture" src="<?= a($cont['img_href']) ?>" />
								<? endif; ?>
								<? if ($cont['organization']): ?>
									<p class="organization"><?= h($cont['organization']) ?></p>
								<? endif; ?>
								<? if ($cont['url']): ?>
									<p class="profile-url">
										<a href="<?= a($cont['url']) ?>"><?= h($cont['url']) ?></a>
									</p>
								<? endif; ?>
							</td>
							<td>
								<?= Wiki::parse(h($cont['bio'])) ?>
								<? if ($cont['tags']): ?>
									<ul class="tags">
										<? foreach ($cont['tags'] as $tag): ?>
											<li><button name="tags[]" value="<?= $tag[0] ?>"><?= h($tag[1]) ?></button></li>
										<? endforeach; ?>
									</ul>
								<? endif; ?>
							</td>
						</tr>
					</tbody>
				</table>
			</li>
		<? endforeach; ?>
	<? endif; ?>
	<? 
		if ($results['criteria']['tags'] && $results['criteria']['tags']['base']):
			$doc->addScript($basePath.'/resources/d3.v2.js');
	?>
			<li class="tag">
				<script type="text/javascript">var relatedTags = <?= json_encode($results['criteria']['tags']) ?>;</script>
				<table>
					<tbody>
						<tr>
							<td class="left"><h3>Tags related to <?= implode(', ', array_map(function($tag) { return $tag[1]; }, $results['criteria']['tags']['base'])) ?></h3></td>
							<td>
							<? if ($results['criteria']['tags']['parents']): ?>
								<h4>Parent tags: </h4>
								<ol class="tags parents">
									<? foreach ($results['criteria']['tags']['parents'] as $parent): ?>
										<li><button name="tags[]" value="<?= $parent[0] ?>"><?= h($parent[1]) ?></button></li>
									<? endforeach; ?>
								</ol>
							<? endif; ?>	
							<? if ($results['criteria']['tags']['children']): ?>
								<h4>Child tags: </h4>
								<ol class="tags children">
									<? foreach ($results['criteria']['tags']['children'] as $child): ?>
										<li><button name="tags[]" value="<?= $child[0] ?>"><?= h($child[1]) ?></button></li>
									<? endforeach; ?>
								</ol>
							<? endif; ?>
							<? if ($results['criteria']['tags']['related']): ?>
								<ol class="tags related <?= $conf['showTagCloud'] ? 'cloud' : 'no-cloud' ?>" data-parent-name="<?= $tag['name'] ?>" data-parent-id="<?= $id ?>">
									<? foreach ($results['criteria']['tags']['related'] as $related): ?>
										<li><button name="tags[]" value="<?= $related[0] ?>" data-weight="<?= $related[2] ?>"><?= h($related[1]) ?></button></li>
									<? endforeach; ?>
								</ol>
							<? endif; ?>
							</td>
						</tr>
					</tbody>
				</table>
			</li>
	<? endif; ?>
</ol></li>
<? endif; ?>
	<? 
	foreach($results['results'] as $res):
		if ($res['domain'] == 'questions'):
			$res['title'] .= ' <small>('.$res['answer_count'].' answers)</small>';
		endif; 
	?>
		<li class="result <?= str_replace(' ', '-', $res['domain']) ?>">
			<h3> 
				<? if ($res['link']): ?>
					<? if (is_array($res['link'])): ?>
						<a href="<?= a($res['link'][0]) ?>"><?= html_entity_decode($res['title']) ?></a>
						<? for ($idx = 1; $idx < count($res['link']); ++$idx): ?>
							<a href="<?= a($res['link'][$idx]) ?>" class="alt"><?= $idx ?></a>
						<? endfor; ?>
					<? else: ?>
						<a href="<?= a($res['link']) ?>"><?= html_entity_decode($res['title']) ?></a>
					<? endif; ?>
				<? else: ?>
					<?= $res['title'] ?>
				<? endif; ?>
				<? if ($res['domain'] == 'citations'): ?>
					<a href="/citations/download/<?= $res['id'] ?>/bibtex?no_html=1" class="alt">BibTex</a>
					<a href="/citations/download/<?= $res['id'] ?>/endnote?no_html=1" class="alt">EndNote</a>
				<? endif; ?>
				<small><?= $res['weight'] ?></small>
			</h3>
			<div class="details">
				<? if ($res['domain'] == 'members'): ?>
					<h4><?= ucfirst($res['type'] ? $res['type'] : 'Members') ?></h4>
				<? else: ?>
					<h4><?= ucfirst($res['domain']).(isset($res['type']) ? ' &ndash; '.$res['type'] : '').(isset($res['publication_title']) ? ' &ndash; '.html_entity_decode($res['publication_title']) : '').(isset($res['organization']) ? ' &ndash; '.h($res['organization']) : '') ?></h4>
				<? endif; ?>
				<h4 class="date"><?= isset($res['date']) ? date($res['domain'] == 'citations' ? 'Y' : 'j M Y', strtotime($res['date'])) : '&nbsp;' ?></h4>
				<h4>
					<? if ($res['domain'] == 'contributors'): ?>
						<?= $res['wiki_count'] + $res['resource_count'] ?> contribution<?= $res['wiki_count'] + $res['resource_count'] == 1 ? '' : 's' ?>
					<? elseif (isset($res['contributor_ids'])): ?>
						<ul class="contributors">
						<? 
							foreach ($res['contributor_ids'] as $cid): 
								if (!isset($results['contributor_map'][$cid])) continue;
						?>
							<li><?= h($results['contributor_map'][$cid]['name']) ?></li>
						<? endforeach; ?>
						</ul>
					<? elseif ($res['domain'] === 'questions'): ?>
						<ul class="contributors">
							<li>Anonymous</li>
						</ul>
					<? endif; ?>
				</h4>
				<? if ($res['body']): ?>
					<blockquote class="description clear">
						<? if (($res['domain'] == 'members' || $res['domain'] == 'questions') && $res['img_href']): 
							$thumb = preg_replace('/[.](.*?)$/', '_thumb.$1', $res['img_href']);
							if (!file_exists(JPATH_BASE.$thumb)) {
								$thumb = preg_replace('#^(.*)/(?:.*?)[.](.*?)$#', '$1/thumb.$2', $res['img_href']);
							}
							if (!file_exists(JPATH_BASE.$thumb)) {
								$thumb = '/components/com_members/assets/img/profile_thumb.gif';
							}
						?>
							<img src="<?= $thumb ?>" />
						<? endif; ?>
						<?= $res['body'] ?>
					</blockquote>
				<? else: ?>
					<? if ($res['domain'] == 'members' || $res['domain'] == 'contributors'): ?>
						<img src="<?= $res['img_href'] && file_exists(JPATH_BASE.$res['img_href']) ? $res['img_href'] : '/components/com_members/assets/img/profile_thumb.gif' ?>" />
					<? endif; ?>
				<? endif; ?>
				<? if (isset($res['children'])): ?>
					<ul class="children">
						<? foreach ($res['children'] as $child): ?>
							<? if ($child['domain'] == 'resources'): ?>
								<? if (!$child['title']) continue; ?>
								<li class="<?= strtolower($child['type']) ?>">
										<h5><?= (isset($child['logical_type']) ? '<span class="logical-type">'.$child['logical_type'].'</span>: ' : '').$child['title'] ?></h5>
							<? else: ?>
								<li class="<?= strtolower($child['domain']) ?>">
									<? if ($res['domain'] == 'questions' && isset($results['contributor_map'][$child['contributor_ids'][0]]['img_href'])): ?>
										<img src="<?= preg_replace('/([.].*?)/', '_thumb$1', $results['contributor_map'][$child['contributor_ids'][0]]['img_href']) ?>" />
									<? endif; ?>
									<h5>
										<? if ($child['title']): ?>
											<?= $child['title'] ?>
										<? else: ?>
											<? if ($res['domain'] == 'questions'): ?>
												<a href="<?= $res['link'].'#c'.$child['id'] ?>">
											<? endif; ?> 
											<ul class="contributors">
												<? if ($child['contributor_ids']): ?>
													<? foreach ($child['contributor_ids'] as $cid): ?>
														<li><?= h($results['contributor_map'][$cid]['name']) ?></li>
													<? endforeach; ?>
												<? else: ?>
													<li>Anonymous</li>
												<? endif; ?>
											</ul>
											<span class="date">on <?= date('j M Y', strtotime($child['date'])) ?></span>
											<? if ($res['domain'] == 'questions'): ?>
												</a>
											<? endif; ?> 
										<? endif; ?>
									</h5>
							<? endif; ?>
							<? if ($child['body']): ?>
								<blockquote><?= $child['body'] ?></blockquote>
							<? endif; ?>
						</li>
						<? endforeach; ?>
					</ul>
				<? elseif ($res['domain'] == 'questions'): ?>
				<? endif; ?>
				<? if ($res['tags']): ?>
				<ul class="tags clear">
					<? 
					foreach ($res['tags'] as $tag): 
						$found = FALSE;
						foreach ($tags as $selectedTag):
							if ($selectedTag['id'] == $tag[0]):
								$found = TRUE;
								echo '<li class="selected"><a>'.h($tag[1]).'</a></li>';
								break;
							endif;
						endforeach; 
						if (!$found):
					?>
					<li><button type="submit" name="tags[]" value="<?= $tag[0] ?>"><a><?= h($tag[1]) ?></a></button></li>
					<?
						endif;
					endforeach; 
					?>
				</ul>
				<? endif; ?>
				<p class="clear"> &nbsp; </p>
			</div>
			<? if ($res['domain'] != 'members'): ?>
				<? if ($res['domain'] == 'contributors'): ?>
					<button class="more contributions" name="contributors[]" value="<?= $res['id'] ?>">Show contributions</button>
				<? else: ?>
					<button class="more related" value="<?= $res['domain'].':'.$res['id'] ?>">Related results</button>
				<? endif; ?>
			<? endif; ?>
		</li>
	<? endforeach; ?>
</ol>
<ol class="pages">
<? for ($start = 0, $page = 1; $start <= $results['total']; $start += $perPage, ++$page): ?>
	<li<? if ($start == $results['offset']) echo ' class="current"'; ?>><? if ($start == $results['offset']): ?><?= $page ?><? else: ?><button type="submit" name="offset" value="<?= $start ?>"><?= $page ?></button><? endif; ?></li>
<? endfor; ?>
</ol>
<? elseif ($results && !$results['results']): ?>
	<p class="info">No results were found for the criteria you specified, sorry.</p>
<? endif; ?>
</form>
<? if (!defined('HG_AJAX')): ?>
</div>
<? endif; ?>
