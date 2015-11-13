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

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

//remove $this
$config   = $this->config;
$database = $this->database;
$citation = $this->citation;

//load user profile
$profile = \Hubzero\User\Profile::getInstance($citation->uid);

//get citation type
$ct = new \Components\Citations\Tables\Type($database);
$type = $ct->getType($citation->type);

//get citation sponsors
$cs = new \Components\Citations\Tables\Sponsor($database);
$sponsors = $cs->getSponsorsForCitationWithId($citation->id);

//determine the separator
$urlSeparator = PHP_EOL;
if (strstr($citation->url, " ") !== false)
{
	$urlSeparator = " ";
}
else if (strstr($citation->url, "\t") !== false)
{
	$urlSeparator = "\t";
}

//get citation url
$urls = array_map("trim", explode($urlSeparator, html_entity_decode($citation->url)));
$url = (filter_var($urls[0], FILTER_VALIDATE_URL)) ? $urls[0] : '';

//get citation eprint
$eprints = array_map("trim", explode(PHP_EOL, html_entity_decode($citation->eprint)));
$eprintUrl = (filter_var($eprints[0], FILTER_VALIDATE_URL)) ? $eprints[0] : '';

//check to see if we are wanting to use a custom url
$customUrl = '';
$citationUrlFormat = $config->get("citation_url", "url");
$citationUrlFormatString = $config->get("citation_custom_url", '');

//if we have a custom url string
//replace all matches
if ($citationUrlFormatString != '')
{
	preg_match_all('/\{(\w+)\}/', $citationUrlFormatString, $matches, PREG_SET_ORDER);
	if ($matches)
	{
		foreach ($matches as $match)
		{
			$field = strtolower($match[1]);
			$replace = $match[0];
			$replaceWith = '';
			if (property_exists($citation, $field))
			{
				if (strstr($citation->$field, 'http'))
				{
					$customUrl = $citation->$field;
				}
				else
				{
					$replaceWith = $citation->$field;
					$customUrl = str_replace($replace, $replaceWith, $citationUrlFormatString);
				}
			}
		}
	}
}

//do we want to use our custom url string with placeholders
$citationURL = ($citationUrlFormat == 'custom' && $customUrl != '') ? $customUrl : $url;

//if we have an eprint use that
$citationURL = ($eprintUrl && $eprintUrl != '') ? $eprintUrl : $citationURL;


//are we showing abstracts hub wide
$showAbstract = $config->get('citation_rollover', 'no');
$showAbstract = ($showAbstract == "yes") ? 1 : 0;

//are we showing this citations abstract
$params = new JParameter($citation->params);
$showThisAbstract = $params->get('rollover', $showAbstract);

//get tags and badges
$tags   = \Components\Citations\Helpers\Format::citationTags($citation, $database, false);
$badges = \Components\Citations\Helpers\Format::citationBadges($citation, $database, false);

//are we allowed to show tags and badges
$showTags 	= $config->get('citation_show_tags', 'yes');
$showBadges	= $config->get('citation_show_badges', 'yes');

//get internal associations
$associationLinks = array();
foreach ($this->associations as $a)
{
	if ($a->tbl == 'resource')
	{
		$sql = "SELECT * FROM `#__resources` WHERE id=" . $a->oid;
		$database->setQuery($sql);
		$resource = $database->loadObject();

		if (is_object($resource))
		{
			$associationLinks[] = '<a href="'.Route::url('index.php?option=com_resources&id='.$a->oid).'">'.$resource->title.'</a>';
		}
	}
}

//get the sub area we are trying to load
$area = Request::getVar('area', 'about');
?>

<header id="content-header" class="half">
	<div class="content-header-left">
		<h2>
			<?php echo $citation->title; ?>
			<?php if (User::get('id') == $citation->uid) : ?>
				<a class="edit" href="<?php echo Route::url('index.php?option=com_citations&task=edit&id=' . $citation->id); ?>">Edit</a>
			<?php endif; ?>
		</h2>

		<div class="citation-author">
			<?php if ($citation->author) : ?>
				<span><?php echo Lang::txt('COM_CITATIONS_BY'); ?>:</span>
				<?php
					$a = array();
					$authors = array_map('trim', explode(';', $citation->author));
					foreach ($authors as $author)
					{
						preg_match('/{{(.*?)}}/s', $author, $matches);
						if (!empty($matches))
						{
							if (is_numeric($matches[1]))
							{
								$user = User::getInstance($matches[1]);
								if (is_object($user))
								{
									$a[] = '<a rel="external" href="' . Route::url('index.php?option=com_members&id=' . $matches[1]) . '">' . str_replace($matches[0], '', $author) . '</a>';
								}
								else
								{
									$a[] = $author;
								}
							}
						}
						else
						{
							$a[] = $author;
						}
					}
					echo implode(", ", $a);
				?>
			<?php endif; ?>
		</div>

		<?php if ($citation->abstract && $showThisAbstract) : ?>
			 <div class="citation-abstract">
				<?php
					$max = 1000;
					$abstract = nl2br($citation->abstract);

					if (strlen($abstract) > $max)
					{
						echo substr($abstract, 0, $max) . ' <span class="show-more-hellip">&hellip;</span> ';
						echo '<a id="show-more-button" href="javascript:void(0);">show more</a>';
						echo '<span class="show-more-text hide">' . substr($abstract, $max) . '</span>';
					}
					else
					{
						echo $abstract;
					}
				?>
			</div>
		<?php endif;?>

		<div class="citation-citation">
			<?php
				$citationsFormat = new \Components\Citations\Tables\Format( $this->database );
				$template = ($citationsFormat->getDefaultFormat()) ? $citationsFormat->getDefaultFormat()->format : null;

				$cf = new \Components\Citations\Helpers\Format();
				$cf->setTemplate($template);
				echo strip_tags($cf->formatCitation($citation, null, false, $config));
			?>
			<div class="download">
				<a class="" href="<?php echo Route::url('index.php?option=com_citations&task=download&citationFormat=bibtex&id=' . $citation->id . '&no_html=1'); ?>" title="Download in BibTex Format"><?php echo Lang::txt('COM_CITATIONS_DOWNLOAD_BIBTEX'); ?></a> |
				<a class="" href="<?php echo Route::url('index.php?option=com_citations&task=download&citationFormat=endnote&id=' . $citation->id . '&no_html=1'); ?>" title="Download in Endnote Format"><?php echo Lang::txt('COM_CITATIONS_DOWNLOAD_ENDNOTE'); ?></a>
			</div>
		</div>

	</div>

	<div class="content-header-extra">
		<?php if ($citationURL != '') : ?>
			<a class="primary" rel="external" href="<?php echo $citationURL; ?>">
				<?php echo Lang::txt('COM_CITATIONS_VIEW_ARTICLE'); ?>
			</a>
			<ul class="secondary">
				<li>
					<a class="locate" rel="" href="<?php echo Route::url('index.php?option=com_citations&task=view&id='.$citation->id.'&area=find#find'); ?>">
						<?php echo Lang::txt('COM_CITATIONS_FINDTHISTEXT'); ?>
					</a>
				</li>
			</ul>
		<?php else : ?>
			<a class="primary" rel="" href="<?php echo Route::url('index.php?option=com_citations&task=view&id='.$citation->id.'&area=find#find'); ?>">
				<?php echo Lang::txt('COM_CITATIONS_FINDTHISTEXT'); ?>
			</a>
		<?php endif; ?>


		<?php if (count($sponsors) > 0) : ?>
			<div id="citation-sponsors" class="container">
				<h3><?php echo Lang::txt('COM_CITATIONS_SPONSORED_BY'); ?></h3>
				<ul class="citation-sponsor">
					<?php foreach ($sponsors as $s) : ?>
						<li>
							<a class="sponsor" rel="external" href="<?php echo $s->link; ?>">
								<?php echo ($s->image) ? '<img src="'.$s->image.'" />' : $s->sponsor; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</header>

	<ul class="sub-menu">
		<?php
			$menu = array(
				'about' => Lang::txt('COM_CITATIONS_ABOUT'),
				'resources' => Lang::txt('COM_CITATIONS_CITED'),
				'reviews' => Lang::txt('COM_CITATIONS_REVIEWS'),
				'find' => Lang::txt('COM_CITATIONS_FINDTHISTEXT')
			);
		?>

		<?php foreach ($menu as $k => $v) : ?>
			<?php
				if ($k == 'resources' && count($associationLinks) < 1)
				{
					continue;
				}

				$cls = ($k == $area) ? 'active' : '';
			?>
			<li class="<?php echo $cls; ?>">
				<a class="tab" href="<?php echo Route::url('index.php?option=com_citations&task=view&id='.$citation->id.'&area='.$k); ?>">
					<span><?php echo $v; ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

<?php if ($area == 'about') : ?>
	<section class="main section citation-section" id="about">
		<h3><?php echo Lang::txt('COM_CITATIONS_ABOUT'); ?></h3>
		<table class="citation">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_CITATIONS_TYPE'); ?></th>
					<td>
						<a href="<?php echo Route::url('index.php?option=com_citations&task=browse&type='.$type[0]['id']); ?>"><?php echo $type[0]['type_title']; ?></a>
					</td>
				</tr>

				<?php if ($citation->journal) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_JOURNAL'); ?></th>
						<td><?php echo $citation->journal; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->publisher) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_PUBLISHER'); ?></th>
						<td><?php echo $citation->publisher; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->booktitle) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_BOOK_TITLE'); ?></th>
						<td><?php echo $citation->booktitle; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->short_title) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_SHORT_TITLE'); ?></th>
						<td><?php echo $citation->short_title; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->editor) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_EDITORS'); ?></th>
						<td><?php echo $citation->editor; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->cite) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_CITE_KEY'); ?></th>
						<td><?php echo $citation->cite; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->ref_type) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_REF_TYPE'); ?></th>
						<td><?php echo $citation->ref_type; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->date_submit && $citation->date_submit != '0000-00-00 00:00:00') : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_DATE_SUBMITTED'); ?></th>
						<td><?php echo date("F d, Y", strtotime($citation->date_submit)); ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->date_accept && $citation->date_accept != '0000-00-00 00:00:00') : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_DATE_ACCEPTED'); ?></th>
						<td><?php echo date("F d, Y", strtotime($citation->date_accept)); ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->date_publish && $citation->date_publish != '0000-00-00 00:00:00') : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_DATE_PUBLISHED'); ?></th>
						<td><?php echo date("F d, Y", strtotime($citation->date_publish)); ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->year) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_YEAR'); ?></th>
						<td><?php echo $citation->year; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->month) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_MONTH'); ?></th>
						<td><?php echo $citation->month; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->author_address) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_AUTHOR_ADDRESS'); ?></th>
						<td><?php echo nl2br($citation->author_address); ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->volume) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_VOLUME'); ?></th>
						<td><?php echo $citation->volume; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->number) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_ISSUE'); ?></th>
						<td><?php echo $citation->number; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->pages) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_PAGES'); ?></th>
						<td><?php echo $citation->pages; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->isbn) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_ISBN'); ?></th>
						<td><?php echo $citation->isbn; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->doi) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_DOI'); ?></th>
						<td>
							<a href="http://dx.doi.org/<?php echo $citation->doi; ?>">
								<?php echo $citation->doi; ?>
							</a>
						</td>
					</tr>
				<?php endif;?>

				<?php if ($citation->call_number) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_CALL_NUMBER'); ?></th>
						<td><?php echo $citation->call_number; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->accession_number) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_ACCESSION_NUMBER'); ?></th>
						<td><?php echo $citation->accession_number; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->series) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_SERIES'); ?></th>
						<td><?php echo $citation->series; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->edition) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_EDITION'); ?></th>
						<td><?php echo $citation->edition; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->school) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_SCHOOL'); ?></th>
						<td><?php echo $citation->school; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->institution) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_INSTITUTION'); ?></th>
						<td><?php echo $citation->institution; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->address) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_ADDRESS'); ?></th>
						<td><?php echo $citation->address; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->location) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_LOCATION'); ?></th>
						<td><?php echo $citation->location; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->howpublished) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_PUBLISH_METHOD'); ?></th>
						<td><?php echo $citation->howpublished; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->language) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_LANGUAGE'); ?></th>
						<td><?php echo $citation->language; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->label) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_LABEL'); ?></th>
						<td><?php echo $citation->label; ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->notes) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_NOTES'); ?></th>
						<td><?php echo nl2br($citation->notes); ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->research_notes) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_RESEARCH_NOTES'); ?></th>
						<td><?php echo nl2br($citation->research_notes); ?></td>
					</tr>
				<?php endif;?>

				<?php if ($citation->keywords) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_KEYWORDS'); ?></th>
						<td><?php echo nl2br($citation->keywords); ?></td>
					</tr>
				<?php endif;?>

				<?php if (is_array($tags) && count($tags) > 0 && $showTags == 'yes') : ?>
					<tr>
						<th><?php echo Lang::txt('COM_CITATIONS_TAGS'); ?></th>
						<td>
							<?php echo \Components\Citations\Helpers\Format::citationTags($citation, App::get('db')); ?>
						</td>
					</tr>
				<?php endif; ?>

				<?php if (is_array($badges) && count($badges) > 0 && $showBadges == 'yes') : ?>
					<tr>
						<th><?php echo Lang::txt('COM_CITATIONS_BADGES'); ?></th>
						<td>
							<?php echo \Components\Citations\Helpers\Format::citationBadges($citation, App::get('db')); ?>
						</td>
					</tr>
				<?php endif; ?>

				<?php if (isset($citation->uid)) : ?>
					<?php if (is_object($profile) && $profile->get('uidNumber')) : ?>
						<tr>
							<th><?php echo Lang::txt('COM_CITATIONS_SUBMITTED_BY'); ?></th>
							<td>
								<a href="<?php echo Route::url('index.php?option=com_members&id=' . $profile->get('uidNumber')); ?>">
									<?php echo $profile->get('name'); ?>
								</a>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>

				<?php if (isset($citation->created) && $citation->created != '0000-00-00 00:00:00') : ?>
					<tr>
						<th><?php echo Lang::txt('COM_CITATIONS_SUBMITTED'); ?></th>
						<td><?php echo date("l, F d, Y @ g:ia", strtotime($citation->created)); ?></td>
					</tr>
				<?php endif; ?>

			</tbody>
		</table>
	</section>
<?php endif;?>

<?php if ($area == 'resources') : ?>
	<section class="main section citation-section" id="resources">
		<h3><?php echo Lang::txt('COM_CITATIONS_CITED'); ?></h3>
		<?php if (count($associationLinks) > 0) : ?>
			<p><?php echo Lang::txt('COM_CITATIONS_CITED_DESC'); ?></p>
			<ul class="">
				<li>
					<?php echo implode($associationLinks, '</li><li>'); ?>
				</li>
			</ul>
		<?php else : ?>
			<p><?php echo Lang::txt('COM_CITATIONS_CITED_NONE'); ?></p>
		<?php endif; ?>
	</section>
<?php endif;?>

<?php if ($area == 'reviews') : ?>
	<section class="main section citation-section" id="reviews">
		<h3><?php echo Lang::txt('COM_CITATIONS_REVIEWS'); ?></h3>
		<?php
			$params = array(
				$citation,
				$this->option,
				Route::url('index.php?option='.$this->option.'&task=view&id='.$citation->id.'&area=reviews'),
				array('png','jpg','gif','tiff','pdf')
			);
			$comments = Event::trigger('hubzero.onAfterDisplayContent', $params);
			echo $comments[0];
		?>
	</section>
<?php endif; ?>

<?php if ($area == 'find') : ?>
	<section class="main section citation-section" id="find">
		<h3><?php echo Lang::txt('COM_CITATIONS_FINDTHISTEXT'); ?></h3>
		<p><?php echo Lang::txt('COM_CITATIONS_FINDTHISTEXT_DESC'); ?></p>
		<table class="citation">
			<tbody>
				<?php if ($citation->doi) : ?>
					 <tr>
						<th><?php echo Lang::txt('COM_CITATIONS_DOI_RESOLVER'); ?></th>
						<td>
							<a rel="external" href="http://dx.doi.org/<?php echo $citation->doi; ?>">
								http://dx.doi.org/<?php echo $citation->doi; ?>
							</a>
						</td>
					</tr>
				<?php endif;?>
				<?php if ($config->get('citation_openurl', 1)) : ?>
					<tr>
						<th><?php echo Lang::txt('COM_CITATIONS_LOCAL_LIBRARY'); ?></th>
						<td>
							<?php echo Lang::txt('COM_CITATIONS_LOCAL_LIBRARY_DESC'); ?>
							<ul class="secondary openurl">
								<?php if ($this->openUrl) : ?>
									<li>
										<?php echo \Components\Citations\Helpers\Format::citationOpenUrl($this->openUrl, $citation); ?>
									</li>
								<?php endif; ?>
							</ul>
						</td>
					</tr>
				<?php endif; ?>

				<tr>
					<th><?php echo Lang::txt('COM_CITATIONS_GOOGLE_SCHOLAR'); ?></th>
					<td>
						<?php
						$query = '';
						if ($citation->doi)
						{
							$query .= $citation->doi;
						}
						elseif ($citation->title)
						{
							$query .= $citation->title;
						}
						?>
						<a target="_blank" title="Google Scholar Search Results" href="http://scholar.google.com/scholar?q=<?php echo $query; ?>">
							<img src="/components/com_citations/assets/img/googlescholar.gif" alt="Google Scholar Search Results" width="100" />
						</a>
					</td>
				</tr>

				<tr>
					<th><?php echo Lang::txt('COM_CITATIONS_OTHER_SOURCES'); ?></th>
					<td>
						<ul>
							<li>
								<a rel="external" href="http://www.deepdyve.com/search?query=<?php echo str_replace(' ', '+', $citation->title); ?>">
									<?php echo Lang::txt('COM_CITATIONS_DEEP_DYVE'); ?>
								</a><?php echo Lang::txt('COM_CITATIONS_DEEP_DYVE_RENT'); ?>
							</li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
	</section>
<?php endif; ?>

<?php
	/**
	 * Coins stuff
	 */

	//get hub url and name
	$hubName = Config::get('sitename');
	$hubUrl = rtrim(Request::base(), '/');

	//get the type of resource for coins
	switch (strtolower($type[0]['type_title']))
	{
		case 'book':
		case 'book section':
		case 'inbook':
		case 'conference':
		case 'proceedings':
		case 'inproceedings':
		case 'conference proceedings':
			$coinsType = "book";
			break;
		case 'journal':
		case 'article':
		case 'journal article':
		default:
			$coinsType = "journal";
			break;
	}

	//fix title
	$title = html_entity_decode($citation->title);
	$title = (!preg_match('!\S!u', $title)) ? utf8_encode($title) : $title;

	//coins data
	$coinsData = array(
		"ctx_ver=Z39.88-2004",
		"rft_val_fmt=info:ofi/fmt:kev:mtx:{$coinsType}",
		"rfr_id=info:sid/{$hubUrl}:{$hubName}",
		"rft.atitle={$title}"
	);

	//add doi to coins
	if ($citation->doi)
	{
		$coinsData[] = 'rft_id=info:doi/' . $citation->doi;
	}

	//add isbn/issn to coins
	if ($citation->isbn)
	{
		$coinsData[] = ($coinsType == 'book') ? 'rft.isbn='.$citation->isbn : 'rft.issn='.$citation->isbn;
	}

	//add url to coins
	if ($citation->url)
	{
		$coinsData[] = 'rft_id=' . htmlentities($citation->url);
	}

	//add volume to coins
	if ($citation->volume)
	{
		$coinsData[] = 'rft.volume=' . $citation->volume;
	}

	//add issue to coins
	if ($citation->number)
	{
		$coinsData[] = 'rft.issue=' . $citation->number;
	}

	//add pages to coins
	if ($citation->pages)
	{
		$coinsData[] = 'rft.pages=' . $citation->pages;
	}

	//add journal to coins
	if ($citation->journal)
	{
		$coinsData[] = 'rft.jtitle=' . $citation->journal;
	}

	//add authors to coins
	if ($citation->author)
	{
		$authors = array_filter(array_values(explode(";", $citation->author)));
		foreach ($authors as $a)
		{
			$coinsData[] = 'rft.au=' . trim($a);
		}
	}

	//replace chars
	$chars = array(' ', '/', ':', '"', '&amp;');
	$replace = array("%20", "%2F", "%3A", "%22", "%26");
	$coinsData = str_replace($chars, $replace, implode('&', $coinsData));

	//echo coins tag to doc
	if ($config->get('citation_coins', 1))
	{
		echo '<span class="Z3988" title="'.$coinsData.'"></span>';
	}
