<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//import user profile lib
ximport('Hubzero_User_Profile');

//remove $this 
$juser = $this->juser;
$config = $this->config;
$database = $this->database;
$citation = $this->citation;

//load user profile
$profile = Hubzero_User_Profile::getInstance( $citation->uid );

//get citation type
$ct = new CitationsType($database);
$type = $ct->getType( $citation->type );

//get citation sponsors
$cs = new CitationsSponsor($database);
$sponsors = $cs->getSponsorsForCitationWithId( $citation->id );

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
	if($matches)
	{
		foreach($matches as $match)
		{
			$field = strtolower($match[1]);
			$replace = $match[0];
			$replaceWith = '';
			if(property_exists($citation, $field))
			{
				if(strstr($citation->$field, 'http'))
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
$tags 	= CitationFormat::citationTags($citation, $database, false);
$badges = CitationFormat::citationBadges($citation, $database, false);

//are we allowed to show tags and badges
$showTags 	= $config->get('citation_show_tags', 'yes');
$showBadges	= $config->get('citation_show_badges', 'yes');

//get internal associations
$associationLinks = array();
foreach($this->associations as $a)
{
	if($a->tbl == 'resource')
	{
		$sql = "SELECT * FROM #__resources WHERE id=".$a->oid;
		$database->setQuery($sql);
		$resource = $database->loadObject();
		
		if(is_object($resource))
		{
			$associationLinks[] = '<a href="'.JRoute::_('index.php?option=com_resources&id='.$a->oid).'">'.$resource->title.'</a>';
		}
	}
}

//get the sub area we are trying to load
$area = JRequest::getVar('area', 'about');
?>

<div id="content-header" class="half">
	<div class="content-header-left">
		<h2>
			<?php echo $citation->title; ?>
			<?php if($juser->get('id') == $citation->uid) : ?>
				<a class="edit" href="<?php echo JRoute::_('index.php?option=com_citations&task=edit&id=' . $citation->id); ?>">Edit</a>
			<?php endif; ?>
		</h2>
	
		<div class="citation-author">
			<?php if($citation->author) : ?>
				<span>By:</span>
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
								$user =& JUser::getInstance($matches[1]);
								if (is_object($user)) 
								{
									$a[] = '<a rel="external" href="' . JRoute::_('index.php?option=com_members&id=' . $matches[1]) . '">' . str_replace($matches[0], '', $author) . '</a>';
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
		
		<?php if($citation->abstract && $showThisAbstract) : ?>
			 <div class="citation-abstract">
				<?php
					$max = 1000;
					$abstract = nl2br($citation->abstract);
					
				 	if(strlen($abstract) > $max)
					{
						echo substr($abstract, 0, $max) . ' <span class="show-more-hellip">&hellip;</span> ';
						echo '<a id="show-more-button" href="javascript:void(0);">show more</a>';
						echo '<span class="show-more-text hide">' . substr($abstract, $max) . '</span>';
					}
				?>
			</div>
		<?php endif;?>
		
		<div class="citation-citation">
			<?php
				$citationsFormat = new CitationsFormat( $this->database );
				$template = $citationsFormat->getDefaultFormat()->format;
				
				$cf = new CitationFormat();
				$cf->setTemplate($template);
				echo strip_tags($cf->formatCitation($citation, null, false, $config));
			?>
			<div class="download">
				<a class="" href="<?php echo JRoute::_('index.php?option=com_citations&task=download&format=bibtex&id=' . $citation->id . '&no_html=1'); ?>" title="Download in BibTex Format">Export to BibTex</a> | 
				<a class="" href="<?php echo JRoute::_('index.php?option=com_citations&task=download&format=endnote&id=' . $citation->id . '&no_html=1'); ?>" title="Download in Endnote Format">Export to Endnote</a>
			</div>
		</div>
		
	</div>
	
	<div class="content-header-extra">
		<?php if($citationURL != '') : ?>
			<a class="primary" rel="external" href="<?php echo $citationURL; ?>">
				View Article
			</a>
			<ul class="secondary">
				<li>
					<a class="locate" rel="" href="<?php echo JRoute::_('index.php?option=com_citations&task=view&id='.$citation->id.'&area=find#find'); ?>">
						Find this Text
					</a>
				</li>
			</ul>
		<?php else : ?>
			<a class="primary" rel="" href="<?php echo JRoute::_('index.php?option=com_citations&task=view&id='.$citation->id.'&area=find#find'); ?>">
				Find this Text
			</a>
		<?php endif; ?>
		
		
		<?php if(count($sponsors) > 0) : ?>
			<div id="citation-sponsors" class="container">
				<h3>Sponsored By</h3>
				<ul class="citation-sponsor">
					<?php foreach($sponsors as $s) : ?>
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
</div>

	<ul class="sub-menu">
		<?php
			$menu = array(
				'about' => 'About',
				'resources' => 'Cited Resources',
				'reviews' => 'Reviews',
				'find' => 'Find this Text'
			);
		?>
		
		<?php foreach($menu as $k => $v) : ?>
			<?php 
				if($k == 'resources' && count($associationLinks) < 1) 
				{ 
					continue; 
				}
				
				$cls = ($k == $area) ? 'active' : '';
			?>
			<li class="<?php echo $cls; ?>">
				<a class="tab" href="<?php echo JRoute::_('index.php?option=com_citations&task=view&id='.$citation->id.'&area='.$k.'#'.$k); ?>">
					<span><?php echo $v; ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

<?php if ($area == 'about') : ?>
	<div class="main section citation-section">
		<a name="about"></a>
		<h3>About</h3>
		<table class="citation">
			<tbody>
				<tr>
					<th>Type</th>
					<td>
						<a href="<?php echo JRoute::_('index.php?option=com_citations&task=browse&type='.$type[0]['id']); ?>"><?php echo $type[0]['type_title']; ?></a>
					</td>
				</tr>
			
				<?php if($citation->journal) : ?>
					 <tr>
						<th><?php echo JText::_('Journal'); ?></th>
						<td><?php echo $citation->journal; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->publisher) : ?>
					 <tr>
						<th><?php echo JText::_('Publisher'); ?></th>
						<td><?php echo $citation->publisher; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->booktitle) : ?>
					 <tr>
						<th><?php echo JText::_('Book Title'); ?></th>
						<td><?php echo $citation->booktitle; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->short_title) : ?>
					 <tr>
						<th><?php echo JText::_('Short Title'); ?></th>
						<td><?php echo $citation->short_title; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->editor) : ?>
					 <tr>
						<th><?php echo JText::_('Editor(s)'); ?></th>
						<td><?php echo $citation->editor; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->cite) : ?>
					 <tr>
						<th><?php echo JText::_('Cite Key'); ?></th>
						<td><?php echo $citation->cite; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->ref_type) : ?>
					 <tr>
						<th><?php echo JText::_('Ref Type'); ?></th>
						<td><?php echo $citation->ref_type; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->date_submit && $citation->date_submit != '0000-00-00 00:00:00') : ?>
					 <tr>
						<th><?php echo JText::_('Date Submitted'); ?></th>
						<td><?php echo date("F d, Y", strtotime($citation->date_submit)); ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->date_accept && $citation->date_accept != '0000-00-00 00:00:00') : ?>
					 <tr>
						<th><?php echo JText::_('Date Accepted'); ?></th>
						<td><?php echo date("F d, Y", strtotime($citation->date_accept)); ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->date_publish && $citation->date_publish != '0000-00-00 00:00:00') : ?>
					 <tr>
						<th><?php echo JText::_('Date Published'); ?></th>
						<td><?php echo date("F d, Y", strtotime($citation->date_publish)); ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->year) : ?>
					 <tr>
						<th><?php echo JText::_('Year'); ?></th>
						<td><?php echo $citation->year; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->month) : ?>
					 <tr>
						<th><?php echo JText::_('Month'); ?></th>
						<td><?php echo $citation->month; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->author_address) : ?>
					 <tr>
						<th><?php echo JText::_('Author Address'); ?></th>
						<td><?php echo nl2br($citation->author_address); ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->volume) : ?>
					 <tr>
						<th><?php echo JText::_('Volume'); ?></th>
						<td><?php echo $citation->volume; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->number) : ?>
					 <tr>
						<th><?php echo JText::_('Issue'); ?></th>
						<td><?php echo $citation->number; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->pages) : ?>
					 <tr>
						<th><?php echo JText::_('Pages'); ?></th>
						<td><?php echo $citation->pages; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->isbn) : ?>
					 <tr>
						<th><?php echo JText::_('ISBN/ISSN'); ?></th>
						<td><?php echo $citation->isbn; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->doi) : ?>
					 <tr>
						<th><?php echo JText::_('DOI'); ?></th>
						<td>
							<a href="http://dx.doi.org/<?php echo $citation->doi; ?>">
								<?php echo $citation->doi; ?>
							</a>
						</td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->call_number) : ?>
					 <tr>
						<th><?php echo JText::_('Call Number'); ?></th>
						<td><?php echo $citation->call_number; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->accession_number) : ?>
					 <tr>
						<th><?php echo JText::_('Accession Number'); ?></th>
						<td><?php echo $citation->accession_number; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->series) : ?>
					 <tr>
						<th><?php echo JText::_('Series'); ?></th>
						<td><?php echo $citation->series; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->edition) : ?>
					 <tr>
						<th><?php echo JText::_('Edition'); ?></th>
						<td><?php echo $citation->edition; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->school) : ?>
					 <tr>
						<th><?php echo JText::_('School'); ?></th>
						<td><?php echo $citation->school; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->institution) : ?>
					 <tr>
						<th><?php echo JText::_('Institution'); ?></th>
						<td><?php echo $citation->institution; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->address) : ?>
					 <tr>
						<th><?php echo JText::_('Address'); ?></th>
						<td><?php echo $citation->address; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->location) : ?>
					 <tr>
						<th><?php echo JText::_('Location'); ?></th>
						<td><?php echo $citation->location; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->howpublished) : ?>
					 <tr>
						<th><?php echo JText::_('How Published'); ?></th>
						<td><?php echo $citation->howpublished; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->language) : ?>
					 <tr>
						<th><?php echo JText::_('Language'); ?></th>
						<td><?php echo $citation->language; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->label) : ?>
					 <tr>
						<th><?php echo JText::_('Label'); ?></th>
						<td><?php echo $citation->label; ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->notes) : ?>
					 <tr>
						<th><?php echo JText::_('Notes'); ?></th>
						<td><?php echo nl2br($citation->notes); ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->research_notes) : ?>
					 <tr>
						<th><?php echo JText::_('Research Notes'); ?></th>
						<td><?php echo nl2br($citation->research_notes); ?></td>
					</tr>
				<?php endif;?>
			
				<?php if($citation->keywords) : ?>
					 <tr>
						<th><?php echo JText::_('Keywords'); ?></th>
						<td><?php echo nl2br($citation->keywords); ?></td>
					</tr>
				<?php endif;?>
			
				<?php if(is_array($tags) && count($tags) > 0 && $showTags == 'yes') : ?>
					<tr>
						<th><?php echo JText::_('Tags'); ?></th>
						<td>
							<ol class="tags">
								<?php
									foreach($tags as $tag)
									{
										$cls = ($tag['admin']) ? 'admin' : '';
										$isAdmin = (in_array($juser->get('usertype'), array('Super Administrator', 'Administrator'))) ? true : false;
										
										//display tag if not admin tag or if admin tag and user is adminstrator
										if (!$tag['admin'] || ($tag['admin'] && $isAdmin))
										{
											echo '<li class="'.$cls.'"><a href="'.JRoute::_('index.php?option=com_tags&tag=' . $tag['tag']).'">'.stripslashes($tag['raw_tag']).'</a></li> ';
										}
									}
								?>
							</ol>
						</td>
					</tr>
				<?php endif; ?>
			
				<?php if(is_array($badges) && count($badges) > 0 && $showBadges == 'yes') : ?>
					<tr>
						<th><?php echo JText::_('Badges'); ?></th>
						<td>
							<ol class="tags badges">
								<?php
									foreach($badges as $badge)
									{
										echo '<li><a href="javascript:void(0);">'.$badge['raw_tag'].'</a></li> ';
									}
								?>
							</ol>
						</td>
					</tr>
				<?php endif; ?>
			
				<?php if(isset($citation->uid)) : ?>
					<?php if(is_object($profile) && $profile->get('uidNumber')) : ?>
						<tr>
							<th><?php echo JText::_('Submitted By'); ?></th>
							<td>
								<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $profile->get('uidNumber')); ?>">
									<?php echo $profile->get('name'); ?>
								</a>
							</td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
			
				<?php if(isset($citation->created) && $citation->created != '0000-00-00 00:00:00') : ?>
					<tr>
						<th><?php echo JText::_('Submitted'); ?></th>
						<td><?php echo date("l, F d, Y @ g:ia", strtotime($citation->created)); ?></td>
					</tr>
				<?php endif; ?>
			
			</tbody>
		</table>
	</div>
<?php endif;?>

<?php if ($area == 'resources') : ?>
	<div class="main section citation-section hide">
		<a name="resources"></a>
		<h3>Cited Resources</h3>
		<?php if(count($associationLinks) > 0) : ?>
			<p>Below is a list of resources that this citation is associated with.</p>
			<ul class="">
				<li>
					<?php echo implode($associationLinks, '</li><li>'); ?>
				</li>
			</ul>
		<?php else : ?>
			<p>We were unable to find any internally cited resources associated with this citation.</p>
		<?php endif; ?>
	</div>
<?php endif;?>

<?php if ($area == 'reviews') : ?>
	<div class="main section citation-section">
		<a name="reviews"></a>
		<h3>Reviews</h3>
		<?php
			JPluginHelper::importPlugin('hubzero');
			$dispatcher =& JDispatcher::getInstance();
			
			$params = array(
				$citation,
				$this->option,
				JRoute::_('index.php?option='.$this->option.'&task=view&id='.$citation->id.'&area=reviews#reviews')
			);
			$comments = $dispatcher->trigger( 'onAfterDisplayContent', $params );
			echo $comments[0];
		?>
	</div>
<?php endif; ?>

<?php if ($area == 'find') : ?>
	<div class="main section citation-section">
		<a name="find"></a>
		<h3>Find this Text</h3>
		<p>Below you can find links that may assist you in locating a copy of this item:</p>
		<table class="citation">
			<tbody>
				<?php if($citation->doi) : ?>
					 <tr>
						<th><?php echo JText::_('DOI Resolver'); ?></th>
						<td>
							<a rel="external" href="http://dx.doi.org/<?php echo $citation->doi; ?>">
								http://dx.doi.org/<?php echo $citation->doi; ?>
							</a>
						</td>
					</tr>
				<?php endif;?>
				<?php if($config->get('citation_openurl', 1)) : ?>
					<tr>
						<th>Local Library</th>
						<td>
							<p style="margin-top:0">If you are a member of this institution, you may be able to access this item through them either in print or perhaps online. If this is a public library or land-grant university, you may be able to at least access this item when you visit the library.</p>
							<p>If your local public or college library does not have this item in its collection, you may be able to request a copy through a service called "Interlibrary Loan." Why not give them a call to see if they can help you?</p>
							<ul class="secondary openurl">
								<?php if($this->openUrl) : ?>
									<li>
										<?php echo CitationFormat::citationOpenUrl($this->openUrl, $citation); ?>
									</li>
								<?php endif; ?>
							</ul>
						</td>
					</tr>
				<?php endif; ?>
				<tr>
					<th>Other Sources</th>
					<td>
						<ul>
							<li>
								<a rel="external" href="http://www.deepdyve.com/search?query=<?php echo str_replace(' ', '+', $citation->title); ?>">
									Deep Dyve
								</a> - rent this
							</li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?php endif; ?>

<?php
	/**
	 * Coins stuff
	 */
	
	//get hub url and name
	$jconfig = JFactory::getConfig();
	$hubName = $jconfig->getValue('config.sitename');
	$hubUrl = rtrim(JURI::base(), '/');
	
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
	if($citation->doi)
	{
		$coinsData[] = 'rft_id=info:doi/' . $citation->doi;
	}
	
	//add isbn/issn to coins
	if($citation->isbn)
	{
		$coinsData[] = ($coinsType == 'book') ? 'rft.isbn='.$citation->isbn : 'rft.issn='.$citation->isbn;
	}
	
	//add url to coins
	if($citation->url)
	{
		$coinsData[] = 'rft_id=' . htmlentities($citation->url);
	}
	
	//add volume to coins
	if($citation->volume)
	{
		$coinsData[] = 'rft.volume=' . $citation->volume;
	}
	
	//add issue to coins
	if($citation->number)
	{
		$coinsData[] = 'rft.issue=' . $citation->number;
	}
	
	//add pages to coins
	if($citation->pages)
	{
		$coinsData[] = 'rft.pages=' . $citation->pages;
	}
	
	//add authors to coins
	if($citation->author)
	{
		$authors = array_filter(array_values(explode(";", $citation->author)));
		foreach($authors as $a)
		{
			$coinsData[] = 'rft.au=' . trim($a);
		}
	}
	
	//replace chars
	$chars = array(' ', '/', ':', '"', '&amp;');
	$replace = array("%20", "%2F", "%3A", "%22", "%26");
	$coinsData = str_replace($chars, $replace, implode('&', $coinsData));
	
	//echo coins tag to doc
	if($config->get('citation_coins', 1))
	{
		echo '<span class="Z3988" title="'.$coinsData.'"></span>';
	}
?>

