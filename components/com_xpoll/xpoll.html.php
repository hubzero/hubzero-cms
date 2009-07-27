<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class XPollHtml 
{
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------

	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}
	
	//-----------

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>'.n;
		$html .= $txt.n;
		$html .= '</div><!-- / ';
		if ($id) {
			$html .= '#'.$id;
		}
		if ($cls) {
			$html .= '.'.$cls;
		}
		$html .= ' -->'.n;
		return $html;
	}
	
	//-----------
	
	public function hed( $level, $txt )
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}
	
	//-----------
	
	public function voted( $option, $pid ) 
	{
		return '<p><a href="'. JRoute::_('index.php?option='.$option.'&task=view&id='.$pid).'">'.JText::_('RESULTS_OTHER_POLLS').'</a></p>'.n;
	}
	
	//-----------
	
	public function thanks( $option, $pid ) 
	{
		$html  = XPollHtml::div( XPollHtml::hed(3,JText::_('THANKS')), '', 'content-header').n;
		$html .= '<p><a href="'. JRoute::_( 'index.php?option='.$option.'&task=view&id='. $pid ) .'">'.JText::_('BUTTON_RESULTS').'</a></p>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function showResults( &$poll, &$votes, $first_vote, $last_vote, $polls, $option ) 
	{
		?>
		<div id="content-header">
			<h2><?php echo JText::_('XPOLL'); ?></h2>
		</div><!-- / #content-header -->
		<div id="content-header-extra">
			<p id="tagline"><a class="stats" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=latest'); ?>">Take the latest Poll</a></p>
		</div><!-- / #content-header-extra -->
		<?php
		if ($votes) {
			$j = 0;
			$data_arr['text'] = null;
			$data_arr['hits'] = null;
			foreach ($votes as $vote) 
			{
				$data_arr['text'][$j] = trim($vote->text);
				$data_arr['hits'][$j] = $vote->hits;
				$j++;
			}
					
			$polls_graphwidth = 200;
			$polls_barheight  = 2;
			$polls_maxcolors  = 5;
			$polls_barcolor   = 0;

			$tabcnt = 0;
			$colorx = 0;
			$maxval = 0;

			array_multisort( $data_arr['hits'], SORT_NUMERIC, SORT_DESC, $data_arr['text'] );

			foreach ($data_arr['hits'] as $hits) 
			{
				if ($maxval < $hits) {
					$maxval = $hits;
				}
			}
			$sumval = array_sum( $data_arr['hits'] );
			?>
			<div class="main section">
				<div class="aside">
					<p>
						<strong><?php echo JText::_('FIRST_VOTE'); ?></strong><br />
						<?php echo ($first_vote) ? $first_vote : '--'; ?>
					</p>
					<p>
						<strong><?php echo JText::_('LAST_VOTE'); ?></strong><br />
						<?php echo ($last_vote) ? $last_vote : '--'; ?>
					</p>
				</div><!-- / .aside -->
				<div class="subject">
					<table class="pollresults" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
						<thead>
							<tr>
								<th colspan="3">
									<form action="<?php echo JRoute::_('index.php?option='.$option); ?>" method="post" id="poll"> 
										<fieldset> 
											<?php echo XpollHtml::selectPolls( $poll->id, $polls ); ?>
											<input type="submit" name="submit" value="<?php echo JText::_('GO'); ?>" />
											<input type="hidden" name="task" value="view" />
										</fieldset> 
									</form>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="3"><span><?php echo JText::_('NUM_VOTERS'); ?>:</span> <?php echo $sumval; ?></td>
							</tr>
						</tfoot>
						<tbody>
			<?php
					for ($i=0, $n=count($data_arr['text']); $i < $n; $i++) 
					{
						$text =& $data_arr['text'][$i];
						$hits =& $data_arr['hits'][$i];
						if ($maxval > 0 && $sumval > 0) {
							$width = ceil( $hits*$polls_graphwidth/$maxval );
							$percent = round( 100*$hits/$sumval, 1 );
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
			?> 
							<tr>
								<td>
									<div class="graph">
										<strong class="bar <?php echo $tdclass; ?>" style="width: <?php echo $percent; ?>%;"><span><?php echo $percent; ?>%</span></strong>
									</div>
								</td>
								<td><?php echo stripslashes($text); ?></td>
								<td class="votes"><?php echo $hits; ?></td>
							</tr>
			<?php
						$tabcnt = 1 - $tabcnt;
					}
			?> 
						</tbody>
					</table>
				</div><!-- / .subject -->
			</div><!-- / .main section -->
			<div class="clear"></div>
			<?php
		} else {
			echo XPollHtml::warning( JText::_('NO_RESULTS') ).n;
		}
	}
	
	//-----------
	
	public function selectPolls( $pid, $polls ) 
	{
		$html  = '<select name="id" id="pollid">'.n;
		$html .= t.'<option value="">'. JText::_('SELECT_POLL') .'</option>'.n;
		foreach ($polls as $poll) 
		{
			$html .= t.'<option value="'.$poll->id.'"';
			$html .= ($poll->id == intval( $pid ) ? ' selected="selected"' : '');
			$html .= '>'. $poll->title .'</option>'.n;
		}
		$html .= '</select>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function latest($poll, $options, $option) 
	{
		$html  = XPollHtml::div( XPollHtml::hed(2,JText::_('XPOLL').': '.JText::_('LATEST')),'full','content-header').n;
		$html .= '<div class="main section">'.n;
		if (count($options) > 0) {
			$html .= '<form id="pollform" method="post" action="'. JRoute::_('index.php?option='.$this->_option) .'">'.n;
			$html .= t.XPollHtml::hed(3, stripslashes($poll->title)).n;
			$html .= t.'<ul class="poll">'.n;
			for ($i=0, $n=count( $options ); $i < $n; $i++) 
			{ 
				$html .= t.t.' <li>'.n;
				$html .= t.t.t.'<input type="radio" name="voteid" id="voteid'. $options[$i]->id .'" value="'. $options[$i]->id .'" alt="'. $options[$i]->id .'" />'.n;
				$html .= t.t.t.'<label for="voteid'. $options[$i]->id .'">'. $options[$i]->text .'</label>'.n;
				$html .= t.t.' </li>'.n;
			}
			$html .= t.'</ul>'.n;
			$html .= t.'<p><input type="submit" name="task_button" value="'. JText::_('BUTTON_VOTE') .'" />&nbsp;&nbsp;'.n;
			$html .= t.'<a href="'. JRoute::_('index.php?option='.$this->_option.'&amp;task=view&amp;id='.$poll->id) .'">'. JText::_('BUTTON_RESULTS') .'</a></p>'.n;
			$html .= t.'<input type="hidden" name="id" value="'. $poll->id .'" />'.n;
			$html .= t.'<input type="hidden" name="task" value="vote" />'.n;
			$html .= '</form>'.n;
		} else {
			$html .= '<p>'. JText::_('NO_POLL') .'</p>'.n;
		}
		$html .= '</div><!-- / .main section -->'.n;
		
		echo $html;
	}
}
?>
