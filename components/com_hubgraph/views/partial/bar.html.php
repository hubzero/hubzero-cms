<? defined('JPATH_BASE') or die(); ?>
<div class="search-bar">
	<input type="text" name="terms" class="terms" autocomplete="off" value="<?= str_replace('"', '&quot;', $req->getTerms()) ?>" />
	<a href="<?= preg_replace('/[?&]+$/', '', $base.($_SERVER['QUERY_STRING'] ? '?'.preg_replace('/^&/', '', preg_replace('/&?terms=[^&]*/', '', urldecode($_SERVER['QUERY_STRING']))) : '')) ?>">x</a>
	<button type="submit">Search</button>
</div>
<ul id="inventory">
<? foreach ($req->getTags() as $tag): ?>
	<li class="tag">
		<input type="hidden" name="tags[]" value="<?= $tag['id'] ?>" />
		<strong>Tagged: </strong><?= h($tag['tag']) ?>
		<a class="remove" href="<?= preg_replace('/[?&]+$/', '', $base.'?'.preg_replace('/^&/', '', preg_replace('/&?tags\[\]='.$tag['id'].'/', '', urldecode($_SERVER['QUERY_STRING'])))) ?>">x</a>
	</li>
<? endforeach; ?>
<? if (($domain = $req->getDomain())): ?>
	<li class="domain">
		<input type="hidden" name="domain" value="<?= a($domain) ?>" />
		<strong>Section: </strong><?= str_replace('~', ' &ndash; ', h($domain)) ?>
		<a class="remove" href="<?= preg_replace('/[?&]+$/', '', $base.'?'.preg_replace('/^&/', '', preg_replace('/&?domain=[^&]*/', '', $_SERVER['QUERY_STRING']))) ?>">x</a>
	</li>
<? endif; ?>
<? if (($contributors = $req->getContributors())): ?>
	<? foreach ($contributors as $cont): ?>
		<li class="contributor">
			<input type="hidden" name="contributors[]" value="<?= a($cont['id']) ?>" />
			<strong>Contributor: </strong><?= h($cont['name']) ?>
			<a class="remove" href="<?= preg_replace('/[?&]+$/', '', $base.'?'.preg_replace('/^&/', '', preg_replace('/&?contributors\[\]='.$cont['id'].'/', '', urldecode($_SERVER['QUERY_STRING'])))) ?>">x</a>
		</li>
	<? endforeach; ?>
<? endif; ?>
</ul>
<ol class="tags" id="tag-suggestions"></ol>
<ol id="suggestions"></ol>
<? if ($results && $results['criteria']): ?>
<ol id="criteria-details">
	<? if ($results['criteria']['contributors']): ?>
		<? 
		foreach ($results['criteria']['contributors'] as $cont): 
			if (!$cont['public']) continue;
		?>
			<li>
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
			<li>
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
								<ol class="tags related" data-parent-name="<?= $tag['name'] ?>" data-parent-id="<?= $id ?>">
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
</ol>
<? endif; ?>
