<?php

/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>, Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=citations';

JHTML::_('behavior.chart', 'pie');

$this->css();
?>
<!-- javascript flot processing -->
<script src="/media/system/js/flot/jquery.flot.min.js" type="text/javascript"></script>
<script src="/media/system/js/flot/jquery.flot.orderBars.js" type="text/javascript"></script>
<script src="/media/system/js/flot/jquery.flot.pie.js" type="text/javascript"></script>
<script type="text/javascript">

	function getCitationTypes()
	{
		/* PHP Code */
		/* This gets the variables needed from the server-side, and echoes them to client-side code for
		 * use within flot.js
		 */
		<?php
$TypesArr = array();
$CountTypeArr = array();
$CitationTypes = $this->typestats;
// passes through the citations types and their counts

foreach ($CitationTypes as $type => $count) {

	/* We won't count what doesn't need to be there */
	if ($count > 0) {
		array_push($TypesArr, $type);
		array_push($CountTypeArr, $count);
	}
}

/* Convert the PHP arrays into JS arrays */
$TypesArr = json_encode($TypesArr);
$CountTypeArr = json_encode($CountTypeArr);

/* Put them in the script */
echo "var types =" . $TypesArr . ";\n";
echo "var TypeCount =" . $CountTypeArr . ";\n";
?>
		/* End PHP Code */

		var data = [],
			series = types.length;

		for (var i = 0; i < series; i++) {
			data[i] = {
				label: types[i],
				data: TypeCount[i] / types.length,
			}
		}

		$.plot('#citationsTypes', data, {
			legend: {
					show : true
			},
			series: {
				pie: {
					show: true,
					innerRadius: 0.5,
					stroke: {
						color: '#efefef'
					}// end stroke
				} //end pie
			} //end series
		});
		//return dataObj;
	}

	function getCitationCount()
	{

		/* PHP Code */
		/* This gets the variables needed from the server-side, and echoes them to client-side code for
		 * use within flot.js
		 */
		<?php
$yearArr = array();
// array to store the years
$NonAffCitations = array();
// array to store the non-affiliated citations
$AffCitations = array();
// array to store the affiliated citations
$TypesArr = array();
$CountTypeArr = array();
$yearlystats = $this->yearlystats;
// passes through the yearly citation counts
$CitationTypes = $this->typestats;
// passes through the citations types and their counts

foreach ($yearlystats as $year => $amt) {
	array_push($yearArr, $year);
	array_push($NonAffCitations, $amt['non-affiliate']);
	array_push($AffCitations, $amt['affiliate']);
}

foreach ($CitationTypes as $type => $count) {
	array_push($TypesArr, $type);
	array_push($CountTypeArr, $count);
}

/* Convert the PHP arrays into JS arrays */
$js_arry = json_encode($yearArr);
$NonAffCitationsArr = json_encode($NonAffCitations);
$AffCitationsArr = json_encode($AffCitations);
$TypesArr = json_encode($TypesArr);
$CountTypeArr = json_encode($CountTypeArr);

/* Put them in the script */
echo "var NonAffCitations =" . $NonAffCitationsArr . ";\n";
echo "var AffCitations =" . $AffCitationsArr . ";\n";
echo "var years =" . $js_arry . ";\n";
echo "var types =" . $TypesArr . ";\n";
echo "var TypeCount =" . $CountTypeArr . ";\n";
?>
		/* End PHP Code */

		var d1 = [];
		var d2 = [];
		var yeartks = [];

		var dataset = Array()

		var k = years.length - 1;
		for (var i = 0; i < years.length; i++) {
			d1.push([ i, AffCitations[k] ]);
			d2.push([ i, NonAffCitations[k] ]);
			yeartks.push([i, years[k]]);
			k--;
		}

		/* Only display plots that exist / have all zero values */
		var NonZeroCnt1 = 0;
		var NonZeroCnt2 = 0;
		for(var i = 0; i < years.length; i++)
		{
			if(AffCitations[i] != "0")
			{
				NonZeroCnt1++;
			}
			if(NonAffCitations[i] != "0")
			{
				NonZeroCnt2++;
			}
		}

		if(NonZeroCnt1 > 0)
		{
			dataset.push({
				data: d1,
				bars: {
					show: true,
					barWidth: 0.5,
					order: 1,
					lineWidth : 1,
					label: "Affliated Citations"

				}
			});
		}

		if(NonZeroCnt2 > 0)
		{
			dataset.push({
				data: d2,
				bars: {
					show: true,
					barWidth: 0.5,
					order: 2,
					lineWidth: 1,
					label: "Non-Affliated Citations"
				}
			});
		}

		var plotData = {
			citationCounts : dataset,
			yeartks : yeartks
		}

		var options =
		{
			grid:{
				hoverable: true,
				clickable: true,
				aboveData: true
			} ,

			legend: {
				show: true,
				position: "nw"

			},

			yaxis:
			{
				ticks: 1,
				tickLength: 1
			},

			xaxis:
			{
				ticks: plotData.yeartks,
				tickLength: 1

			}
		}

		$.plot('#citationsPlot', plotData.citationCounts, options); //end plot

	}

	/* Calling function */
	$(function() {

		var plotData = getCitationCount();

		getCitationTypes();

	}); //end function
</script>


<!-- end javascript flot processing -->
<div id="container" class="frm">
	<!-- Place Import -->
<?php
if ($this->allow_import == 1 || ($this->allow_import == 2 && $this->isAdmin)): ?>
	<a class="btn icon-add" href="<?php
	echo JRoute::_($base . '?action=add'); ?>">
		<?php
	echo JText::_('PLG_GROUPS_CITATIONS_SUBMIT_CITATION'); ?>
	</a>
<?php
endif; ?>

<?php
foreach ($this->messages as $message) {
	echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
}
?>


<section id="intro" class="section">
<!--<section id="introduction" class="section"> -->
	<div class="grid">
		<div class="col span-half">
			<h3><?php
echo JText::_('PLG_GROUPS_CITATIONS_WHAT_ARE_CITATIONS'); ?></h3>
			<p><?php
echo JText::_('PLG_GROUPS_CITATIONS_WHAT_ARE_CITATIONS_DESC'); ?></p>
		</div>
		<div class="col span-half omega">
			<h3><?php
echo JText::_('PLG_GROUPS_CITATIONS_SUBMIT_CITATIONS'); ?></h3>
			<?php
if ($this->allow_import == 1 || $this->allow_bulk_import == 1 || ($this->allow_import == 2 && $this->isAdmin) || ($this->allow_bulk_import == 2 && $this->isAdmin)): ?>
			<p><?php
	echo JText::sprintf('PLG_GROUPS_CITATIONS_SUBMIT_CITATIONS_DESC', JRoute::_($base . '?action=add')); ?></p>
			<?php
else: ?>
				<p><?php
	echo JText::sprintf('PLG_GROUPS_CITATIONS_SUBMIT_CITATIONS_DESC_NOTALLOWED', '/support'); ?></p>
			<?php
endif; ?>
		</div>
	</div><!-- / .grid -->
</section><!-- / #introduction.section -->

<section class="section">

	<div class="grid">
		<div class="col span3">
			<h2><?php
echo JText::_('PLG_GROUPS_CITATIONS_FIND_CITATION'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="col span-half">
				<form action="<?php
echo JRoute::_(JURI::current()); ?>" method="get" class="search">
					<fieldset>
						<p>
							<label for="csearch"><?php
echo JText::_('PLG_GROUPS_CITATIONS_FIND_CITATION_KEYWORD'); ?></label>
							<input type="text" name="search" id="csearch" value="" />
							<input type="submit" value="<?php
echo JText::_('PLG_GROUPS_CITATIONS_SEARCH'); ?>" />
						</p>
					</fieldset>
				</form>
			</div><!-- / .col span-half -->
			<div class="col span-half omega">
				<div class="browse">
					<p><a href="<?php
echo JRoute::_($base . '?action=browse'); ?>"><?php
echo JText::_('PLG_GROUPS_CITATIONS_BROWSE'); ?></a></p>
				</div><!-- / .browse -->
			</div><!-- / .col span-half -->
		</div><!-- / .col span9 -->
	</div><!-- / .grid -->

	<!-- Number of Citations Plot & Table -->
	<div class="grid">
		<div class="col span-half">
			<div id='citationsPlot' style='height: 250px; width: 100%; padding: 5px; margin: 10px;'></div>
		</div>

		<div class="col span-half omega">
			<?php
$yearlystats = $this->yearlystats;
$cls = 'even';
$tot = 0;
$rows = array();
foreach ($yearlystats as $year => $amt) {
	$cls = ($cls == 'even') ? 'odd' : 'even';

	$tr = "\t\t" . '<tr class="' . $cls . '">' . "\n";
	$tr.= "\t\t\t" . '<th class="textual-data">' . $year . '</th>' . "\n";
	$tr.= "\t\t\t" . '<td class="numerical-data">' . $amt['affiliate'] . '</td>' . "\n";
	$tr.= "\t\t\t" . '<td class="numerical-data">' . $amt['non-affiliate'] . '</td>' . "\n";
	$tr.= "\t\t\t" . '<td class="numerical-data highlight">' . (intval($amt['affiliate']) + intval($amt['non-affiliate'])) . '</td>' . "\n";
	$tr.= "\t\t" . '</tr>' . "\n";

	$rows[] = $tr;

	$tot+= (intval($amt['affiliate']) + intval($amt['non-affiliate']));
}

$html = '<table>' . "\n";
$html.= "\t" . '<caption>' . JText::_('PLG_GROUPS_CITATIONS_TABLE_METRICS_YEAR') . '</caption>' . "\n";
$html.= "\t" . '<thead>' . "\n";
$html.= "\t\t" . '<tr>' . "\n";
$html.= "\t\t\t" . '<th scope="col" class="textual-data">' . JText::_('PLG_GROUPS_CITATIONS_YEAR') . '</th>' . "\n";
$html.= "\t\t\t" . '<th scope="col" class="numerical-data"><sup><a href="#fn-1">1</a></sup> ' . JText::_('PLG_GROUPS_CITATIONS_AFFILIATED') . '</th>' . "\n";
$html.= "\t\t\t" . '<th scope="col" class="numerical-data"><sup><a href="#fn-1">1</a></sup> ' . JText::_('PLG_GROUPS_CITATIONS_NONAFFILIATED') . '</th>' . "\n";
$html.= "\t\t\t" . '<th scope="col" class="numerical-data">' . JText::_('PLG_GROUPS_CITATIONS_TOTAL') . '</th>' . "\n";
$html.= "\t\t" . '</tr>' . "\n";
$html.= "\t" . '</thead>' . "\n";
$html.= "\t" . '<tfoot>' . "\n";
$html.= "\t\t" . '<tr class="summary">' . "\n";
$html.= "\t\t\t" . '<th class="numerical-data" colspan="3">' . JText::_('PLG_GROUPS_CITATIONS_TOTAL') . '</th>' . "\n";
$html.= "\t\t\t" . '<td class="numerical-data highlight">' . $tot . '</td>' . "\n";
$html.= "\t\t" . '</tr>' . "\n";
$html.= "\t" . '</tfoot>' . "\n";
$html.= "\t" . '<tbody>' . "\n";
$html.= implode('', $rows);
$html.= "\t" . '</tbody>' . "\n";
$html.= '</table>' . "\n";

echo $html;
?>
		</div>  <!-- end first row -->

		<!-- spacing -->
		<div class="col span-half"></div>
		<div class="col span-half omega"></div>
		<div class="col span-half"></div>
		<div class="col span-half omega"></div>
		<!-- end spacing -->

		<div class="col span-half">
			<div id='citationsTypes' style='height: 400px; width: 100%; padding: 5px; margin: 10px; text-align: center;'></div>
		</div>
		<div class="col span-half omega">
			<?php
$typestats = $this->typestats;
$cls = 'even';
$rows = array();
$j = 0;
$data_arr = array();
$data_arr['text'] = null;
$data_arr['hits'] = null;
foreach ($typestats as $type => $stat) {
	$data_arr['text'][$j] = trim($type);
	$data_arr['hits'][$j] = $stat;
	$j++;
}

$polls_graphwidth = 200;
$polls_barheight = 2;
$polls_maxcolors = 5;
$polls_barcolor = 0;
$tabcnt = 0;
$colorx = 0;
$maxval = 0;

array_multisort($data_arr['hits'], SORT_NUMERIC, SORT_DESC, $data_arr['text']);

foreach ($data_arr['hits'] as $hits) {
	if ($maxval < $hits) {
		$maxval = $hits;
	}
}
$sumval = array_sum($data_arr['hits']);

for ($i = 0, $n = count($data_arr['text']); $i < $n; $i++) {
	$text = & $data_arr['text'][$i];
	$hits = & $data_arr['hits'][$i];
	if ($maxval > 0 && $sumval > 0) {
		$width = ceil($hits * $polls_graphwidth / $maxval);
		$percent = round(100 * $hits / $sumval, 1);
	} else {
		$width = 0;
		$percent = 0;
	}
	$tdclass = '';
	if ($polls_barcolor == 0) {
		if ($colorx < $polls_maxcolors) {
			$colorx = ++$colorx;
		} else {
			$colorx = 1;
		}
		$tdclass = 'color' . $colorx;
	} else {
		$tdclass = 'color' . $polls_barcolor;
	}

	$cls = ($cls == 'even') ? 'odd' : 'even';

	$tr = "\t\t" . '<tr class="' . $cls . '">' . "\n";
	$tr.= "\t\t\t" . '<th class="textual-data">' . $text . '</th>' . "\n";
	$tr.= "\t\t\t" . '<td class="numerical-data">' . "\n";
	$tr.= "\t\t\t\t" . '<div class="graph">' . "\n";
	$tr.= "\t\t\t\t\t" . '<strong class="bar ' . $tdclass . '" style="width: ' . $percent . '%;"><span>' . $percent . '%</span></strong>' . "\n";
	$tr.= "\t\t\t\t" . '</div>' . "\n";
	$tr.= "\t\t\t" . '</td>' . "\n";
	$tr.= "\t\t\t" . '<td class="numerical-data">' . $hits . '</td>' . "\n";
	$tr.= "\t\t" . '</tr>' . "\n";

	$rows[] = $tr;

	$tabcnt = 1 - $tabcnt;
}
$html = '<table>' . "\n";
$html.= "\t" . '<caption>' . JText::_('PLG_GROUPS_CITATIONS_TABLE_METRICS_TYPE') . '</caption>' . "\n";
$html.= "\t" . '<thead>' . "\n";
$html.= "\t\t" . '<tr>' . "\n";
$html.= "\t\t\t" . '<th scope="col" class="textual-data">' . JText::_('PLG_GROUPS_CITATIONS_TYPE') . '</th>' . "\n";
$html.= "\t\t\t" . '<th scope="col" class="textual-data">' . JText::_('PLG_GROUPS_CITATIONS_PERCENT') . '</th>' . "\n";
$html.= "\t\t\t" . '<th scope="col" class="numerical-data">' . JText::_('PLG_GROUPS_CITATIONS_TOTAL') . '</th>' . "\n";
$html.= "\t\t" . '</tr>' . "\n";
$html.= "\t" . '</thead>' . "\n";
$html.= "\t" . '<tfoot>' . "\n";
$html.= "\t\t" . '<tr class="summary">' . "\n";
$html.= "\t\t\t" . '<th class="text-data">' . JText::_('PLG_GROUPS_CITATIONS_TOTAL') . '</th>' . "\n";
$html.= "\t\t\t" . '<td class="textual-data">100%</td>' . "\n";
$html.= "\t\t\t" . '<td class="numerical-data">' . $sumval . '</td>' . "\n";
$html.= "\t\t" . '</tr>' . "\n";
$html.= "\t" . '</tfoot>' . "\n";
$html.= "\t" . '<tbody>' . "\n";
$html.= implode('', $rows);
$html.= "\t" . '</tbody>' . "\n";
$html.= '</table>' . "\n";
$html.= '<div class="footnotes"><hr />
					<ol><li><a name="fn-1"></a>' . JText::_('PLG_GROUPS_CITATIONS_METRICS_FOOTNOTE') . '</li></ol>
					</div>' . "\n";

echo $html;
?>
	</div>


</div> <!-- end container -->


