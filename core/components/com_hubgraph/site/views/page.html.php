<?php
defined('_HZEXEC_') or die();

class GenericRenderer
{
	protected $item;
	private $renderers = array();
	private static $context = array();
	const DATE_FORMAT = 'n M Y';

	public static function setContext($ctx)
	{
		self::$context = $ctx;
	}

	protected static function getContext($k, $ik = NULL)
	{
		$rv = isset(GenericRenderer::$context[$k]) ? self::$context[$k] : array();
		if ($ik)
		{
			$rv = isset($rv[$ik]) ? $rv[$ik] : NULL;
		}
		return $rv;
	}

	public function __construct($item)
	{
		$this->item = $item;
		$this->build();
	}

	protected function children()
	{
		if (isset($this->item['children']) && is_array($this->item['children']))
		{
			$rv = array('<ol class="children">');
			foreach ($this->item['children'] as $child)
			{
				$class = $child['domain'].'ChildRenderer';
				$class = class_exists($class) ? $class : 'GenericChildRenderer';
				$rv[] = (string)new $class($child);
			}
			$rv[] = '</ol>';
			return $rv;
		}
	}

	protected function push($renderer)
	{
		$this->renderers[] = $renderer;
		return $this;
	}

	protected static function debug($str)
	{
		return defined('HG_DEBUG') ? $str : '';
	}

	protected function domain()
	{
		return ucfirst($this->item['domain']);
	}

	protected function date()
	{
		if (isset($this->item['publicationyear']))
		{
			return array('<td>', h($this->item['publicationyear']), '</td>');
		}

		if (isset($this->item['date']))
		{
			if (preg_match('/^\s*(\d{4})\/01\/01\s*00:00:00/', $this->item['date'], $ma))
			{
				return array('<td>', $ma[1], '</td>');
			}
			return array('<td>', date(self::DATE_FORMAT, strtotime($this->item['date'])), '</td>');
		}
	}

	protected function type()
	{
		$st = $this->subtype();
		return array(
			'<td class="domain">',
			$this->domain(),
			$st ? '/'.$st : '',
			'</td>'
		);
	}

	protected function isbn()
	{
		if (isset($this->item['isbn']))
		{
			return array('<td>ISBN: ', $this->item['isbn'], '</td>');
		}
	}

	protected function publicationDetails()
	{
		if (isset($this->item['publisher']))
		{
			return array(
				'<td>',
				(isset($this->item['placeofpublication']) ? h($this->item['placeofpublication']) . ' ' : ''),
				h($this->item['publisher']),
				'</td>'
			);
		}
	}

	protected function subtype()
	{
		foreach (array('type', 'section', 'category') as $subtype)
		{
			if (isset($this->item[$subtype]))
			{
				return $this->item[$subtype];
			}
		}
	}

	protected function contributors()
	{
		$rv = array();
		if (isset($this->item['group_ids']) && $this->item['group_ids'])
		{
			$group = self::getContext('groups', $this->item['group_ids'][0]);
			$rv[] = array('<td>', $group[0], '</td>');
		}
		if (isset($this->item['contributor_ids']))
		{
			$rv[] = '<td>';
			foreach ($this->item['contributor_ids'] as $idx=>$cid)
			{
				if (($c = self::getContext('contributors', $cid)) && $c[0])
				{
					$rv[] = ($idx == 0 ? '' : ', ') . h($c[0]);
				}
			}
			$rv[] = '</td>';
			return $rv;
		}
	}

	protected function extraDetails()
	{
		$rv = array();
		$nums = array();
		foreach (array('totalnoofpages', 'volumeno', 'issuenomonth', 'pagenumbers') as $k)
		{
			if (isset($this->item[$k]))
			{
				$nums[] = $k == 'issuenomonth' ? '('.$this->item[$k].')' : $this->item[$k];
			}
		}
		foreach (array('publication_title', 'booktitle', 'journaltitle', $this->item['type'] == 'Books' ? 'title' : '') as $k)
		{
			if (isset($this->item[$k]) && trim($this->item[$k]))
			{
				$rv[] = array(
					'<td>',
					$this->item[$k], $nums ? ', '.str_replace(', (', '(', implode(', ', $nums)) : NULL,
					'</td>'
				);
				break;
			}
		}
		return $rv ? array('<tr>', $rv, '</tr>') : NULL;
	}

	protected function details()
	{
		return array(
			'<table class="details"><tr>',
			$this->contributors(),
			$this->date(),
			'</tr>',
			$this->extraDetails(),
			'</table>'
		);
	}

	protected function related()
	{
		return '<a class="related" data-domain="'.a($this->item['domain']).'" data-id="'.a($this->item['id']).'">Show related results</a>';
	}

	protected function language()
	{
		$lang = array();
		if (isset($this->item['language']))
		{
			$lang[] = $this->item['language'];
		}
		if (isset($this->item['additionallanguage']) && trim($this->item['additionallanguage']))
		{
			$lang[] = implode(', ', preg_split('/[^-\w]+/', $this->item['additionallanguage']));
		}
		if ($lang)
		{
			return array('<td>', implode(', ', $lang), '</td>');
		}
	}

	protected function doi()
	{
		if (isset($this->item['doi']) && $this->item['doi'])
		{
			return array('<td><a href="http://dx.doi.org/', $this->item['doi'], '">DOI: ', $this->item['doi'], '</a></td>');
		}
	}

	protected function metadata()
	{
		return array(
			'<table class="details"><tr>',
			$this->publicationDetails(),
			$this->isbn(),
			$this->type(),
			$this->language(),
			$this->doi(),
			'</tr></table>',
			$this->tags(),
			$this->related()
		);
	}

	protected function tags()
	{
		if (isset($this->item['tag_ids']) && is_array($this->item['tag_ids']))
		{
			$rv = array();
			foreach ($this->item['tag_ids'] as $tid)
			{
				if (($t = GenericRenderer::getContext('tags', $tid)) && $t[0])
				{
					$rv[] = array($tid, $t[0], $t[1]);
				}
			}
			usort($rv, function($a, $b) {
				return strcasecmp($a[1], $b[1]);
			});
			$rv = array_map(function($tag) {
				return '<button data-id="'.$tag[0].'" title="'.p($tag[2], 'result').'">'.h($tag[1]).'</button>';
			}, $rv);
			return $rv ? array('<ol class="tags"><li>', implode('</li><li>', $rv), '</li></ol>') : null;
		}
	}

	protected function title($h = 'h3')
	{
		$links = (array)$this->item['link'];
		$rv = array(
			'<'.$h.'>',
			self::debug($this->item['domain'].':'.$this->item['id'].' - '.$this->item['weight'].' - '),
			'<a href="'.a($links[0]).'">',
			$this->item['title'],
			'</a>'
		);
		for ($idx = 1; isset($links[$idx]); ++$idx)
		{
			$rv[] = '<a class="alt-link" href="'.a($links[$idx]).'">[';
			$rv[] = $idx + 1;
			$rv[] = ']</a>';
		}
		$rv[] = '</'.$h.'>';
		return $rv;
	}

	protected function body()
	{
		if (isset($this->item['body']) && trim($this->item['body']))
		{
			return array(
				'<blockquote>',
				$this->item['body'],
				'</blockquote>'
			);
		}
	}

	protected function build()
	{
		$this->push(array(
			$this->title(),
			$this->details(),
			$this->body(),
			$this->children(),
			$this->metadata(),
			$this->debug('<pre>'.print_r($this->item, 1).'</pre>')
		));
	}

	public function __toString()
	{
		$stringArray = function($r) use(&$stringArray)
		{
			if (is_array($r))
			{
				$rv = array();
				foreach ($r as $k=>$v)
				{
					$rv = array_merge($rv, $stringArray($v));
				}
				return $rv;
			}
			if (is_callable($r))
			{
				return $stringArray(call_user_func($r));
			}
			if (is_null($r))
			{
				return array('');
			}
			return array($r);
		};
		return '<li class="result '.a(str_replace(' ', '-', $this->item['domain'])).'">'.implode('', $stringArray($this->renderers)).'</li>';;
	}
}

class GenericChildRenderer extends GenericRenderer
{
	protected function build()
	{
		$this->push(array(
			$this->title('h4'),
			$this->body()
		));
	}
}

class GroupsRenderer extends GenericRenderer
{
	public function date()
	{
		return array('<td>', p($this->item['member_count'], 'member'), '</td>');
	}
}

class CitationsRenderer extends GenericRenderer
{
	protected function contributors()
	{
		$rv = array();
		if (isset($this->item['authors']))
		{
			$rv[] = $this->item['authors'];
		}
		if (isset($this->item['editor']) && trim($this->item['editor']))
		{
			$rv[] = array(' (ed: ', $this->item['editor'], ')');
		}
		if ($rv)
		{
			array_unshift($rv, '<td>');
			$rv[] = '</td>';
			return $rv;
		}
	}

	protected function extraDetails()
	{
		$rv = array();
		foreach (array('publication_title', 'booktitle') as $k)
		{
			if (isset($this->item[$k]) && trim($this->item[$k]))
			{
				$rv[] = array('<td>', h($this->item[$k]), '</td>');
				break;
			}
		}
		$parts = array();
		if (isset($this->item['chapter']))
		{
			$parts[] = 'ch. '.$this->item['chapter'];
		}
		if (isset($this->item['pages']))
		{
			$parts[] = 'pp. '.$this->item['pages'];
		}
		if ($parts)
		{
			$rv[] = '<td>';
			$rv[] = implode(', ', $parts);
			$rv[] = '</td>';
		}
		if (isset($this->item['publisher']))
		{
			$rv[] = array('<td>', h($this->item['publisher']), '</td>');
		}
		if ($rv)
		{
			return array(
				'<tr>',
				$rv,
				'</tr>'
			);
		}
	}
}

class EventsRenderer extends GenericRenderer
{
	protected function date()
	{
		static $now;
		if (!$now)
		{
			$now = time();
		}
		return str_replace('<td>', '<td>'.(strtotime($this->item['date'] > $now) ? 'happening ' : 'happened '), parent::date());
	}
}

class MembersRenderer extends GenericRenderer
{
	protected function domain()
	{
		return ($this->item['count'] ? 'Contributor' : 'Member').($this->item['organization'] ? ', '.h($this->item['organization']) : '');
	}

	protected function date()
	{
		return str_replace('<td>', '<td>since ', parent::date());
	}

	protected function extraDetails()
	{
		if ($this->item['contributions'])
		{
			$rv = array();
			foreach ($this->item['contributions'] as $type=>$num)
			{
				if ($type == 'content')
				{
					$type = 'content pages';
				}
				$rv[] = $num.' '.($num == 1 ? preg_replace('/e?s$/', '', $type) : $type);
			}
			return '<td>'.implode(', ', $rv).'</td>';
		}
	}
}

function rmUrl($base, $key, $id)
{
	return preg_replace('/[&?]$/', '', preg_replace('/'.$key.'(?:\[\])?='.preg_quote($id).'/i', '', urldecode($base)));
}
$url = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : $_SERVER['REDIRECT_QUERY_STRING'];
$url = '/hubgraph'.($url ? '?'.$url : '');

$begin = ($req->getPage() - 1) * $req->getPerPage() + 1;
$end = $begin + count($results['results']) - 1;
if (!defined('HG_AJAX')):
	if ($req->anyCriteria()):
?>
	<div class="count">
		<ol class="domains">
			<li<?php echo isset($domainMap['']) ? ' class="current"' : '' ?>><button type="submit" name="domain" value=""><?php echo p($results['total'], 'result') . ($results['total'] == 0 ? ':[' : '') ?></button></li>
			<?php 
				// nees :[
				$domains = array();
				if (!isset($results['domains'])):
					$results['domains'] = array();
				endif;
				foreach ($results['domains'] as $k=>$v):
					$domains[] = array('title' => $k, 'count' => $v);
				endforeach;
				uasort($domains, function($a, $b) {
					if ($a['title'] == 'projects'):
						return -1;
					endif;
					if ($b['title'] == 'projects'):
						return 1;
					endif;
					if ($a['count'] > $b['count']):
						return -1;
					endif;
					if ($a['count'] < $b['count']):
						return 1;
					endif;
					return strcasecmp($a['title'], $b['title']);
				});
				foreach ($domains as $domain):
			?>
				<li<?php echo isset($domainMap[$domain['title']]) ? ' class="current subsel"' : '' ?>><button type="submit" name="domain" value="<?php echo isset($domainMap[$domain['title']]) ? '' : a($domain['title']) ?>"><?php echo h(p($domain['count'], Inflect::singularize($domain['title']))) ?></button></li>
			<?php endforeach; ?>
		</ol>
		</div>
		<table class="facets">
			<tbody>
			<?php
			$timeframe = array();
			if (isset($_GET['timeframe']) && is_array($_GET['timeframe']))
			{
				foreach ($_GET['timeframe'] as $tf)
				{
					$timeframe[] = array('id' => $tf, 'title' => $tf);
				}
			}
			$timeframe = isset($_GET['timeframe']) ? array_map(function($t) { return array('id' => $t, 'title' => $t); }, (array)$_GET['timeframe']) : NULL;
			foreach (array('tags' => 'Tagged', 'contributors' => 'Contributed&nbsp;by', 'groups' => 'In&nbsp;group', 'timeframe' => 'Date') as $key=>$label):
				$transportKey = $key == 'contributors' ? 'users' : $key;
				$inReq = isset($_GET[$transportKey]) ? array_flip($_GET[$transportKey]) : array();
				if (!$inReq):
					if (!$results[$key]):
						continue;
					else:
						$explicit = FALSE;
						foreach ($results[$key] as $res):
							if ($res[1] > 0):
								$explicit = TRUE;
								break;
							endif;
						endforeach;
						if (!$explicit):
							continue;
						endif;
					endif;
				endif;

				uasort($results[$key],
					$key == 'timeframe'
						? function($a, $b) {
							foreach (array('today', 'prior week', 'prior month', 'prior year') as $relative):
								if ($a[0] == $relative):
									return -1;
								endif;
								if ($b[0] == $relative):
									return 1;
								endif;
							endforeach;
							return $a[0] > $b[0] ? -1 : 1;
						}
						: function($a, $b) {
							if ($a[1] == $b[1]):
								return strcasecmp($a[0], $b[0]);
							endif;
							return $a[1] > $b[1] ? -1 : 1;
						});
				$used = array();
				$max = NULL;
			?>
			<tr>
				<td class="label"><?php echo $label ?>:</td>
				<td>
					<ol class="<?php echo $key ?>">
						<?php 
						if (isset($$transportKey)):
							foreach ((array)$$transportKey as $item):
								$inReq[$item['id']] = TRUE;
								if ($item['title']):
							?>
								<li>
									<input type="hidden" name="<?php echo $transportKey ?>[]" value="<?php echo a($item['id']) ?>" />
									<a href="<?php echo rmUrl($url, $transportKey, $item['id']) ?>"><?php echo h($item['title']) ?><span>x</span></a>
								</li>
							<?php
								endif;
							endforeach;
						endif;
						foreach ($results[$key] as $id=>$item):
							if (array_key_exists($id, $inReq) || !$item[1] || !$item[0] || isset($used[$item[0]])):
								continue;
							endif;
							$used[$item[1]] = TRUE;
						?>
							<li><button type="submit" title="<?php echo p($item[1], 'result') ?>" name="<?php echo $transportKey ?>[]" value="<?php echo $id ?>"><?php echo $item[0]; ?></button></li>
						<?php endforeach; ?>
					</ol>
				</td>
			</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php
	endif;
endif;
if (isset($terms)):
	if ($results['terms']['suggested']):
?>
	<p class="info">(Did you mean <em><a href="/search<?php echo $link ?>"><?php echo $terms ?></a></em>?)</p>
<?php elseif ($results['terms']['autocorrected']): ?>
	<p class="info">(Showing results for <em><?php echo $terms ?></em>)</p> 
<?php endif;
endif;
if ($results['results']):
	GenericRenderer::setContext(array(
		'tags'         => $results['tags'],
		'contributors' => $results['contributors'],
		'groups'       => $results['groups']
	));
	if (!defined('HG_AJAX')):
?>
	<ul class="results" data-cache="<?php echo $results['cache'] ?>">
	<?php
	endif;
	foreach ($results['results'] as $result):
		if (isset($result['html'])):
			echo $result['html'];
			continue;
		endif;
		$class = str_replace(' ', '', $result['domain']).'Renderer';
		echo class_exists($class, FALSE) ? new $class($result) : new GenericRenderer($result);
	endforeach;
	if (!defined('HG_AJAX')):
	?>
	</ul>
	<div class="pages">
		<span>Page</span>
		<ol>
		<?php 
			$curDomain = $req->getDomain();
			for ($start = 0, $page = 1; $start <= ($curDomain ? $results['domains'][$curDomain] : $results['total']); $start += $perPage, ++$page):
		?>
			<li<?php echo $page == $results['page'] ? ' class="current"' : ''; ?>>
				<button type="submit" name="page" value="<?php echo $page ?>"><?php echo $page ?></button>
			</li>
		<?php endfor; ?>
		</ol>
	</div>
	<?php endif; ?>
<?php endif; ?>
