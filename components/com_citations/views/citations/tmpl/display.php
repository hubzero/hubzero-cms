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
defined('_JEXEC') or die( 'Restricted access');

$this->css('introduction.css', 'system')
     ->css('usage.css', 'com_usage')
     ->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul>
			<?php if ($this->allow_import == 1 || ($this->allow_import == 2 && $this->isAdmin)) : ?>
				<li><a class="btn icon-add" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=add'); ?>">
					<?php echo JText::_('COM_CITATIONS_SUBMIT_CITATION'); ?>
				</a></li>
			<?php endif; ?>
			<?php if ($this->allow_bulk_import == 1 || ($this->allow_bulk_import == 2 && $this->isAdmin)) : ?>
				<li><a class="btn icon-upload" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=import'); ?>">
					<?php echo JText::_('COM_CITATIONS_IMPORT_CITATION'); ?>
				</a></li>
			<?php endif; ?>
		</ul>
	</div>
</header>
<?php
	foreach ($this->messages as $message) {
		echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
	}
?>

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span-half">
			<h3><?php echo JText::_('COM_CITATIONS_WHAT_ARE_CITATIONS'); ?></h3>
			<p><?php echo JText::_('COM_CITATIONS_WHAT_ARE_CITATIONS_DESC'); ?></p>
		</div>
		<div class="col span-half omega">
			<h3><?php echo JText::_('COM_CITATIONS_SUBMIT_CITATIONS'); ?></h3>
			<?php if ($this->allow_import == 1 || $this->allow_bulk_import == 1 ||
			         ($this->allow_import == 2 && $this->isAdmin) || ($this->allow_bulk_import == 2 && $this->isAdmin)) : ?>
			<p><?php echo JText::sprintf('COM_CITATIONS_SUBMIT_CITATIONS_DESC', JRoute::_('index.php?option='.$this->option.'&task=add')); ?></p>
			<?php else : ?>
				<p><?php echo JText::sprintf('COM_CITATIONS_SUBMIT_CITATIONS_DESC_NOTALLOWED', '/support'); ?></p>
			<?php endif; ?>
		</div>
	</div><!-- / .grid -->
</section><!-- / #introduction.section -->

<section class="section">

	<div class="grid">
		<div class="col span3">
			<h2><?php echo JText::_('COM_CITATIONS_FIND_CITATION'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="col span-half">
				<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>" method="get" class="search">
					<fieldset>
						<p>
							<label for="csearch"><?php echo JText::_('COM_CITATIONS_FIND_CITATION_KEYWORD'); ?></label>
							<input type="text" name="search" id="csearch" value="" />
							<input type="submit" value="<?php echo JText::_('COM_CITATIONS_SEARCH'); ?>" />
						</p>
					</fieldset>
				</form>
			</div><!-- / .col span-half -->
			<div class="col span-half omega">
				<div class="browse">
					<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>"><?php echo JText::_('COM_CITATIONS_BROWSE'); ?></a></p>
				</div><!-- / .browse -->
			</div><!-- / .col span-half -->
		</div><!-- / .col span9 -->
	</div><!-- / .grid -->

	<div class="grid">
		<div class="col span3">
			<h2><?php echo JText::_('COM_CITATIONS_METRICS'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div id="statistics">
<?php
$yearlystats = $this->yearlystats;
$cls = 'even';
$tot = 0;
$rows = array();
foreach ($yearlystats as $year=>$amt)
{
	$cls = ($cls == 'even') ? 'odd' : 'even';

	$tr  = "\t\t".'<tr class="'.$cls.'">'."\n";
	$tr .= "\t\t\t".'<th class="textual-data">'.$year.'</th>'."\n";
	$tr .= "\t\t\t".'<td class="numerical-data">'.$amt['affiliate'].'</td>'."\n";
	$tr .= "\t\t\t".'<td class="numerical-data">'.$amt['non-affiliate'].'</td>'."\n";
	$tr .= "\t\t\t".'<td class="numerical-data highlight">'.(intval($amt['affiliate']) + intval($amt['non-affiliate'])).'</td>'."\n";
	$tr .= "\t\t".'</tr>'."\n";

	$rows[] = $tr;

	$tot += (intval($amt['affiliate']) + intval($amt['non-affiliate']));
}

$html  = '<table>'."\n";
$html .= "\t".'<caption>'.JText::_('COM_CITATIONS_TABLE_METRICS_YEAR').'</caption>'."\n";
$html .= "\t".'<thead>'."\n";
$html .= "\t\t".'<tr>'."\n";
$html .= "\t\t\t".'<th scope="col" class="textual-data">'.JText::_('COM_CITATIONS_YEAR').'</th>'."\n";
$html .= "\t\t\t".'<th scope="col" class="numerical-data"><sup><a href="#fn-1">1</a></sup> '.JText::_('COM_CITATIONS_AFFILIATED').'</th>'."\n";
$html .= "\t\t\t".'<th scope="col" class="numerical-data"><sup><a href="#fn-1">1</a></sup> '.JText::_('COM_CITATIONS_NONAFFILIATED').'</th>'."\n";
$html .= "\t\t\t".'<th scope="col" class="numerical-data">'.JText::_('COM_CITATIONS_TOTAL').'</th>'."\n";
$html .= "\t\t".'</tr>'."\n";
$html .= "\t".'</thead>'."\n";
$html .= "\t".'<tfoot>'."\n";
$html .= "\t\t".'<tr class="summary">'."\n";
$html .= "\t\t\t".'<th class="numerical-data" colspan="3">'.JText::_('COM_CITATIONS_TOTAL').'</th>'."\n";
$html .= "\t\t\t".'<td class="numerical-data highlight">'.$tot.'</td>'."\n";
$html .= "\t\t".'</tr>'."\n";
$html .= "\t".'</tfoot>'."\n";
$html .= "\t".'<tbody>'."\n";
$html .= implode('',$rows);
$html .= "\t".'</tbody>'."\n";
$html .= '</table>'."\n";

$typestats = $this->typestats;
$cls = 'even';
$rows = array();
$j = 0;
$data_arr = array();
$data_arr['text'] = null;
$data_arr['hits'] = null;
foreach ($typestats as $type=>$stat)
{
	$data_arr['text'][$j] = trim($type);
	$data_arr['hits'][$j] = $stat;
	$j++;
}

$polls_graphwidth = 200;
$polls_barheight  = 2;
$polls_maxcolors  = 5;
$polls_barcolor   = 0;
$tabcnt = 0;
$colorx = 0;
$maxval = 0;

array_multisort( $data_arr['hits'], SORT_NUMERIC, SORT_DESC, $data_arr['text']);

foreach ($data_arr['hits'] as $hits)
{
	if ($maxval < $hits) {
		$maxval = $hits;
	}
}
$sumval = array_sum( $data_arr['hits']);

for ($i=0, $n=count($data_arr['text']); $i < $n; $i++)
{
	$text =& $data_arr['text'][$i];
	$hits =& $data_arr['hits'][$i];
	if ($maxval > 0 && $sumval > 0) {
		$width = ceil( $hits*$polls_graphwidth/$maxval);
		$percent = round( 100*$hits/$sumval, 1);
	} else {
		$width = 0;
		$percent = 0;
	}
	$tdclass='';
	if ($polls_barcolor==0) {
		if ($colorx < $polls_maxcolors) {
			$colorx = ++$colorx;
		} else {
			$colorx = 1;
		}
		$tdclass = 'color'.$colorx;
	} else {
		$tdclass = 'color'.$polls_barcolor;
	}

	$cls = ($cls == 'even') ? 'odd' : 'even';

	$tr  = "\t\t".'<tr class="'.$cls.'">'."\n";
	$tr .= "\t\t\t".'<th class="textual-data">'.$text.'</th>'."\n";
	$tr .= "\t\t\t".'<td class="numerical-data">'."\n";
	$tr .= "\t\t\t\t".'<div class="graph">'."\n";
	$tr .= "\t\t\t\t\t".'<strong class="bar '.$tdclass.'" style="width: '.$percent.'%;"><span>'.$percent.'%</span></strong>'."\n";
	$tr .= "\t\t\t\t".'</div>'."\n";
	$tr .= "\t\t\t".'</td>'."\n";
	$tr .= "\t\t\t".'<td class="numerical-data">'.$hits.'</td>'."\n";
	$tr .= "\t\t".'</tr>'."\n";

	$rows[] = $tr;

	$tabcnt = 1 - $tabcnt;
}

$html .= '<table>'."\n";
$html .= "\t".'<caption>'.JText::_('COM_CITATIONS_TABLE_METRICS_TYPE').'</caption>'."\n";
$html .= "\t".'<thead>'."\n";
$html .= "\t\t".'<tr>'."\n";
$html .= "\t\t\t".'<th scope="col" class="textual-data">'.JText::_('COM_CITATIONS_TYPE').'</th>'."\n";
$html .= "\t\t\t".'<th scope="col" class="textual-data">'.JText::_('COM_CITATIONS_PERCENT').'</th>'."\n";
$html .= "\t\t\t".'<th scope="col" class="numerical-data">'.JText::_('COM_CITATIONS_TOTAL').'</th>'."\n";
$html .= "\t\t".'</tr>'."\n";
$html .= "\t".'</thead>'."\n";
$html .= "\t".'<tfoot>'."\n";
$html .= "\t\t".'<tr class="summary">'."\n";
$html .= "\t\t\t".'<th class="text-data">'.JText::_('COM_CITATIONS_TOTAL').'</th>'."\n";
$html .= "\t\t\t".'<td class="textual-data">100%</td>'."\n";
$html .= "\t\t\t".'<td class="numerical-data">'.$sumval.'</td>'."\n";
$html .= "\t\t".'</tr>'."\n";
$html .= "\t".'</tfoot>'."\n";
$html .= "\t".'<tbody>'."\n";
$html .= implode('',$rows);
$html .= "\t".'</tbody>'."\n";
$html .= '</table>'."\n";
$html .= '<div class="footnotes"><hr />
	<ol><li><a name="fn-1"></a>'.JText::_('COM_CITATIONS_METRICS_FOOTNOTE').'</li></ol>
	</div>'."\n";

echo $html;

?>
			</div><!-- /#statistics -->
		</div><!-- / .col span9 omega -->
	</div><!-- / .grid -->

</section><!-- / .section -->
