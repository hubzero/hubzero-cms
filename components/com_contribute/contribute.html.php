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
	define('n',"\n");
	define('t',"\t");
	define('r',"\r");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class ContributeHtml 
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

	public function passed( $msg, $tag='p' )
	{
		return '<'.$tag.' class="passed">'.$msg.'</'.$tag.'>'.n;
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
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}

	//-----------

	public function parseTag($text, $tag)
	{
		preg_match("#<nb:".$tag.">(.*?)</nb:".$tag.">#s", $text, $matches);
		if (count($matches) > 0) {
			$match = $matches[0];
			$match = str_replace('<nb:'.$tag.'>','',$match);
			$match = str_replace('</nb:'.$tag.'>','',$match);
		} else {
			$match = '';
		}
		return $match;
	}

	//-----------

	public function selectAccess($as, $value)
	{
		$html  = '<select name="access">';
		for ($i=0, $n=count( $as ); $i < $n; $i++)
		{
			if ($as[$i] != 'Registered' && $as[$i] != 'Special') {
				$html .= '<option value="'.$i.'"';
				if ($value == $i) {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('ACCESS_'.strtoupper($as[$i])) .'</option>';
			}
		}
		$html .= '</select>';
		return $html;
	}

	//-----------
	
	public function selectGroup($groups, $value)
	{
		$html  = '<select name="group_owner">'.n;
		$html .= t.'<option value="">'.JText::_('SELECT_GROUP').'</option>'.n;
		if ($groups && count($groups) > 0) {
			foreach ($groups as $group)
			{
				$html .= t.'<option value="'.$group->cn.'"';
				if ($value == $group->cn) {
					$html .= ' selected="selected"';
				}
				$html .= '>'.$group->description .'</option>'.n;
			}
		}
		$html .= '</select>'.n;
		return $html;
	}
	
	//-----------

	public function writeSteps( $steps, $progress, $option, $active_step, $id=0 )
	{
		$laststep = (count($steps) - 1);
		
		$html  = '<ol id="steps">'.n;
		if ($progress['submitted'] == 1) {
			$html .= t.'<li id="start"><a href="'. JRoute::_('index.php?option=com_resources&id='.$id) .'">'.JText::_('START').'</a></li>'.n;
		} else {
			$html .= t.'<li id="start"><a href="'. JRoute::_('index.php?option='.$option) .'">'.JText::_('START').'</a></li>'.n;
		}
		for ($i=1, $n=count( $steps ); $i < $n; $i++) 
		{
			$html .= t.t.' <li';
			if ($active_step == $i) { 
				$html .= ' class="active"';
			} elseif($progress[$steps[$i]] == 1) {
				$html .= ' class="completed"';
			}
			$html .= '>';
			if ($active_step == $i) {
				$html .= $steps[$i];
			} elseif ($progress[$steps[$i]] == 1) {
				$html .= '<a href="'. JRoute::_('index.php?option='.$option) .'?step='.$i.a.'id='.$id.'">'.JText::_('STEP_'.strtoupper($steps[$i])).'</a>';
			} else {
				if ($progress['submitted'] == 1) {
					$html .= '<a href="'. JRoute::_('index.php?option='.$option) .'?step='.$i.a.'id='.$id.'">'.JText::_('STEP_'.strtoupper($steps[$i])).'</a>';
				} else {
					$html .= $steps[$i];
				}
			}
			$html .= '</li>'.n;
		}
		if ($progress['submitted'] != 1) {
			$html .= ' <li id="trash"';
			if ($active_step == 'discard') { 
				$html .= ' class="active">'.JText::_('Cancel'); 
			} else {
				$html .= '><a href="'.JRoute::_('index.php?option='.$option).'?task=discard';
				$html .= ($id) ? '&amp;id='.$id : '';
				$html .= '">'.JText::_('CANCEL').'</a>';
			}
			$html .= '</li>'.n;
		}
		$html .= '</ol>'.n;
		$html .= '<div class="clear"></div>'.n;
		
		echo $html;
	}

	//-------------------------------------------------------------
	// Steps
	//-------------------------------------------------------------

	public function stepType($option, $step, $types)
	{
		$jconfig =& JFactory::getConfig();
		$hubShortName = $jconfig->getValue('config.sitename');
		$hubShortURL = $jconfig->getValue('config.sitename');

		$license = "/legal/license";
		?>
		<div class="main section withleft">
			<div class="aside">
				<ul>
					<?php
					if ($types) {
						foreach ($types as $type) 
						{
							if ($type->contributable == 1) {
								if ($type->id == 7) {
									$url = '/contribute/tools/register/';
								} else {
									$url = JRoute::_('index.php?option='.$option.a.'step='.$step.a.'type='.$type->id);
								}
								echo t.'<li><a class="tooltips" href="'.$url.'" title="'.htmlentities(stripslashes($type->type), ENT_QUOTES).' :: '.htmlentities(stripslashes($type->description), ENT_QUOTES).'">'.stripslashes($type->type).'</a></li>'.n;
							}
						}
					}
					?>
				</ul>
			</div><!-- /.aside -->
			<div class="subject">
				<p>Select one of the resource types listed to proceed to the next step. The type of resource chosen can affect what information you will need to provide in the following steps.</p>

				<h4>What if I want to contribute a type not listed here?</h4>
				<p>If you feel your contribution does not fit into any of our predefined types, please <a href="feedback/report_problems/">contact us</a> with details of</p>
				<ol>
					<li>what you wish to contribute, including a description and file types</li>
					<li>how you believe it should be categorized</li>
				</ol>
				<p>We will try to accomodate you or provide another suggestion.</p>

				<p>In order for <?php echo $hubShortName; ?> to display your content, we must be given legal license to do so. At the very least, <?php echo $hubShortName; ?> must be authorized to 
				hold, copy, distribute, and perform (play back) your material according to <a class="popup" href="<?php echo $license; ?>">this agreement</a>. 
				You will retain any copyrights to the materials and decide how they should be licensed for end-user access. We encourage you to <a class="popup" href="legal/licensing/">license your contributions</a> 
				so that others can build upon them.</p>
			</div><!-- /.subject -->
			<div class="clear"></div>
		</div><!-- /.main section -->
		<?php
	}

	//-----------
	
	public function stepCompose( $database, $option, $task, $row, $config, $step )
	{
		$row->fulltext = ($row->fulltext) ? stripslashes($row->fulltext): stripslashes($row->introtext);
		/*$nbtags = explode(',',$nbtags);
		foreach ($nbtags as $nbtag)
		{
			$nbtag = strtolower(trim($nbtag));
			$nbtag = str_replace(' ','', $nbtag);
			// explore the text and pull out all matches
			$allnbtags[$nbtag] = ContributeHtml::parseTag($row->fulltext, $nbtag);
			// clean the original text of any matches
			$row->fulltext = str_replace('<nb:'.$nbtag.'>'.$allnbtags[$nbtag].'</nb:'.$nbtag.'>','',$row->fulltext);
		}
		$row->fulltext = trim(stripslashes($row->fulltext));
		$row->fulltext = preg_replace('/<br\\s*?\/??>/i', "", $row->fulltext);
		$row->fulltext = ContributeController::txt_unpee($row->fulltext);*/
		$type = new ResourcesType( $database );
		$type->load( $row->type );
		
		$fields = array();
		if (trim($type->customFields) != '') {
			$fs = explode("\n", trim($type->customFields));
			foreach ($fs as $f) 
			{
				$fields[] = explode('=', $f);
			}
		} else {
			$flds = $config->get('tagstool');
			$flds = explode(',',$flds);
			foreach ($flds as $fld) 
			{
				$fields[] = array($fld, $fld, 'textarea', 0);
			}
		}
		
		if (!empty($fields)) {
			for ($i=0, $n=count( $fields ); $i < $n; $i++) 
			{
				// Explore the text and pull out all matches
				array_push($fields[$i], ContributeHtml::parseTag($row->fulltext, $fields[$i][0]));
				
				// Clean the original text of any matches
				$row->fulltext = str_replace('<nb:'.$fields[$i][0].'>'.end($fields[$i]).'</nb:'.$fields[$i][0].'>','',$row->fulltext);
			}
			$row->fulltext = trim($row->fulltext);
		}
		?>

		<form action="index.php" method="post" id="hubForm">
			<div class="explaination">
				<p><?php echo JText::_('COMPOSE_EXPLANATION'); ?></p>
	
				<p><?php echo JText::_('COMPOSE_ABSTRACT_HINT'); ?></p>
			</div>
			<fieldset>
				<h3><?php echo JText::_('COMPOSE_ABOUT'); ?></h3>
				<label>
					<?php echo JText::_('COMPOSE_TITLE'); ?>: <span class="required"><?php echo JText::_('REQUIRED'); ?></span>
					<input type="text" name="title" maxlength="250" value="<?php echo htmlentities(stripslashes($row->title), ENT_QUOTES); ?>" />
				</label>
			
				<label>
					<?php echo JText::_('COMPOSE_ABSTRACT'); ?>:
					<textarea name="fulltext" cols="50" rows="20"><?php echo htmlentities(stripslashes($row->fulltext)); ?></textarea>
				</label>
			</fieldset><div class="clear"></div>

			<div class="explaination">
				<p><?php echo JText::_('COMPOSE_CUSTOM_FIELDS_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<h3><?php echo JText::_('COMPOSE_DETAILS'); ?></h3>
<?php 
//foreach ($allnbtags as $tagname => $tagcontent) 
foreach ($fields as $field)
{ 
	$tagcontent = preg_replace('/<br\\s*?\/??>/i', "", end($field));
?>
				<label>
					<?php echo stripslashes($field[1]); ?>: <?php echo ($field[3] == 1) ? '<span class="required">'.JText::_('REQUIRED').'</span>': ''; ?>
					<?php if ($field[2] == 'text') { ?>
					<input type="text" name="<?php echo 'nbtag['.$field[0].']'; ?>"><?php echo htmlentities(stripslashes($tagcontent), ENT_QUOTES); ?></input>
					<?php } else { ?>
					<textarea name="<?php echo 'nbtag['.$field[0].']'; ?>" cols="50" rows="6"><?php echo htmlentities(stripslashes($tagcontent)); ?></textarea>
					<?php } ?>
				</label>
<?php 
} 
?>
				<input type="hidden" name="published" value="<?php echo $row->published; ?>" />
				<input type="hidden" name="standalone" value="1" />
				<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
				<input type="hidden" name="type" value="<?php echo $row->type; ?>" />
				<input type="hidden" name="created" value="<?php echo $row->created; ?>" />
				<input type="hidden" name="created_by" value="<?php echo $row->created_by; ?>" />
				<input type="hidden" name="publish_up" value="<?php echo $row->publish_up; ?>" />
				<input type="hidden" name="publish_down" value="<?php echo $row->publish_down; ?>" />
		 
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="<?php echo $task; ?>" />
				<input type="hidden" name="step" value="<?php echo $step; ?>" />
			</fieldset><div class="clear"></div>
			<p class="submit">
				<input type="submit" value="<?php echo JText::_('NEXT'); ?>" />
			</p>
		</form>
		<?php
	}
	
	//-----------
	
	public function stepAttach( $option, $task, $id, $type, $step )
	{
		?>
		<form action="index.php" method="get" id="hubForm">
			<div class="explaination">
				<h4><?php echo JText::_('ATTACH_WHAT_ARE_ATTACHMENTS'); ?></h4>
				<p><?php echo JText::_('ATTACH_EXPLANATION'); ?></p>

				<h4><?php echo JText::_('ATTACH_HOW_TO_ATTACH_BREEZE'); ?></h4>
				<p><?php echo JText::_('ATTACH_BREEZE_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<h3><?php echo JText::_('ATTACH_ATTACHMENTS'); ?></h3>
				<iframe width="100%" height="280" frameborder="0" name="attaches" id="attaches" src="index.php?option=<?php echo $option; ?>&amp;task=attach&amp;id=<?php echo $id; ?>&amp;no_html=1&amp;type=<?php echo $type; ?>"></iframe>
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="<?php echo $task; ?>" />
				<input type="hidden" name="step" value="<?php echo $step; ?>" />
				<input type="hidden" name="id" value="<?php echo $id; ?>" />
			</fieldset><div class="clear"></div>
			<div class="submit">
				<input type="submit" value="<?php echo JText::_('NEXT'); ?>" />
			</div>
		</form>
		<?php
	}

	//-----------

	public function stepAuthors( $option, $task, $id, $step, $lists )
	{
		$jconfig =& JFactory::getConfig();
		$hubShortName = $jconfig->getValue('config.sitename');
		?>
		<form action="index.php" method="post" id="hubForm">
			<div class="explaination">
				<h4><?php echo JText::_('GROUPS_HEADER'); ?></h4>
				<p><?php echo JText::_('GROUPS_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<h3><?php echo JText::_('GROUPS_OWNERSHIP'); ?></h3>
				<div class="group">
				<label>
					<?php echo JText::_('GROUPS_GROUP'); ?>: <span class="optional"><?php echo JText::_('OPTIONAL'); ?></span>
					<?php echo $lists['groups']; ?>
				</label>
				<label>
					<?php echo JText::_('GROUPS_ACCESS_LEVEL'); ?>: <span class="optional"><?php echo JText::_('OPTIONAL'); ?></span>
					<?php echo $lists['access']; ?>
				</label>
				</div>
				<p>
					<strong><?php echo JText::_('ACCESS_PUBLIC'); ?></strong> = <?php echo JText::_('ACCESS_PUBLIC_EXPLANATION'); ?><br />
					<strong><?php echo JText::_('ACCESS_PROTECTED'); ?></strong> = <?php echo JText::_('ACCESS_PROTECTED_EXPLANATION'); ?><br />
					<strong><?php echo JText::_('ACCESS_PRIVATE'); ?></strong> = <?php echo JText::_('ACCESS_PRIVATE_EXPLANATION'); ?>
				</p>
			</fieldset><div class="clear"></div>
			
			<div class="explaination">
				<h4><?php echo JText::_('AUTHORS_NO_LOGIN'); ?></h4>
				<p><?php echo JText::_('AUTHORS_NO_LOGIN_EXPLANATION'); ?></p>
			
				<h4><?php echo JText::_('AUTHORS_NOT_AUTHOR'); ?></h4>
				<p><?php echo JText::_('AUTHORS_NOT_AUTHOR_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<h3><?php echo JText::_('AUTHORS_AUTHORS'); ?></h3>
				<iframe width="100%" height="400" frameborder="0" name="authors" id="authors" src="index2.php?option=<?php echo $option; ?>&amp;task=authors&amp;id=<?php echo $id; ?>&amp;no_html=1"></iframe>
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="<?php echo $task; ?>" />
				<input type="hidden" name="step" value="<?php echo $step; ?>" />
				<input type="hidden" name="id" value="<?php echo $id; ?>" />
			</fieldset><div class="clear"></div>
			
			<div class="submit">
				<input type="submit" value="<?php echo JText::_('NEXT'); ?>" />
			</div>
		</form>
		<?php
	}

	//-----------

	public function stepTags( $option, $task, $id, $tags, $html, $step, $tagfa, $fat )
	{
		?>
		<form action="index.php" method="post" id="hubForm">
			<div class="explaination">
				<h4><?php echo JText::_('TAGS_WHAT_ARE_TAGS'); ?></h4>
				<p><?php echo JText::_('TAGS_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="<?php echo $task; ?>" />
				<input type="hidden" name="step" value="<?php echo $step; ?>" />
				<input type="hidden" name="id" value="<?php echo $id; ?>" />

				<h3><?php echo JText::_('TAGS_ADD'); ?></h3>
<?php if (count($fat) > 0) { ?>
				<fieldset>
					<legend><?php echo JText::_('TAGS_SELECT_FOCUS_AREA'); ?>:</legend>
					<?php
					foreach ($fat as $key => $value) 
					{
						if ($key && $value) {
							echo '<label><input class="option" type="radio" name="tagfa" value="' . $value . '"';
							if ($tagfa == $value) {
								echo ' checked="checked "';
							}
							echo ' /> '.$key.'</label>'.n;
						}
					}
					?>
				</fieldset>
<?php } ?>				
				<label>
					<?php echo JText::_('TAGS_ASSIGNED'); ?>:
					<?php
					JPluginHelper::importPlugin( 'tageditor' );
					$dispatcher =& JDispatcher::getInstance();
					
					$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$tags,'')) );
					
					if (count($tf) > 0) {
						echo $tf[0];
					} else {
						echo t.t.t.'<textarea name="tags" id="tags-men" rows="6" cols="35">'. $tags .'</textarea>'.n;
					}
					?>
				</label>
				<p><?php echo JText::_('TAGS_NEW_EXPLANATION'); ?></p>
			</fieldset><div class="clear"></div>
			
			<p class="submit">
				<input type="submit" value="<?php echo JText::_('NEXT'); ?>" />
			</p>
		</form>
		<?php
	}

	//-----------

	public function stepReview( &$database, $option, $progress, $task, $id, $resource, $step, $config, $usersgroups )
	{
	    $juser =& JFactory::getUser();
		$jconfig =& JFactory::getConfig();
		$hubShortName = $jconfig->getValue('config.sitename');
		$hubShortURL = $jconfig->getValue('config.sitename');
		$license = '/legal/license';

		$html = '';
		
		// Get parameters
		$rparams =& new JParameter( $resource->params );
		$params = $config;
		$params->merge( $rparams );

		// Get attributes
		$attribs =& new JParameter( $resource->attribs );
		
		// Get the resource's children
		$helper = new ResourcesHelper( $id, $database );
	
		if ($progress['submitted'] == 1) {
			if ($params->get('license') == 'cc') {
				/*?>
				<p>This resource is licensed under the <a class="popup" href="legal/cc/">Creative Commons 2.5</a> license recommended by <?php echo $hubShortName; ?>. 
				The <a class="popup" href="http://creativecommons.org/licenses/by-nc-sa/2.5/">license terms</a> support 
				non-commercial use, require attribution, and require sharing derivative works under the same license.</p>
				<?php*/
			} else {
				?>
				<form action="index.php" method="post" id="hubForm">
					<div class="explaination">
						<h4>What happens after I submit?</h4>
						<p>The submission will be licensed under Creative Commons</p>
					</div>
					<fieldset>
						<h3>Licensing</h3>
						<label><input class="option" type="checkbox" name="license" value="1" /> <span class="optional">optional</span> 
						License the work under the <a class="popup" href="legal/cc/">Creative Commons 2.5</a> license recommended by <?php echo $hubShortName; ?>. 
						The <a class="popup" href="http://creativecommons.org/licenses/by-nc-sa/2.5/">license terms</a> support 
						non-commercial use, require attribution, and require sharing derivative works under the same license.</label>
					
						<input type="hidden" name="option" value="<?php echo $option; ?>" />
						<input type="hidden" name="task" value="<?php echo $task; ?>" />
						<input type="hidden" name="id" value="<?php echo $id; ?>" />
						<input type="hidden" name="step" value="<?php echo $step; ?>" />
						<input type="hidden" name="published" value="1" />
				 	</fieldset><div class="clear"></div>
					<p class="submit">
						<input type="submit" value="Save" />
					</p>
				</form>
				<?php
			}
			?>
			<p class="help">This contribution has already been submitted and passed review. <a href="<?php echo JRoute::_('index.php?option=com_resources&id='.$id); ?>">View it here</a></p>
			<?php
		} else {
			?>
			<form action="index.php" method="post" id="hubForm">
				<div class="explaination">
					<h4>What happens after I submit?</h4>
					<p>Your submission will be reviewed. If it is accepted, the submission will be given a "live" status and will appear 
					in our <a href="<?php echo JRoute::_('index.php?option=com_resources'); ?>">resources</a> and at the top of our <a href="<?php echo JRoute::_('index.php?option=com_whatsnew'); ?>">What's New</a> listing.</p>
				</div>
				<fieldset>
					<h3>Authorization</h3>
					<label><input class="option" type="checkbox" name="authorization" value="1" /> <span class="required">required</span> I certify that I am the owner of all submitted materials 
					or am authorized by the owner to grant license to its use and that I hereby grant <?php echo $hubShortURL; ?> license to copy, distribute, display, 
					and perform the materials here submitted and any derived or collected works based upon them in perpetuity. <?php echo $hubShortURL; ?> may make modifications 
					to the submitted materials or build upon them as necessary or appropriate for their services. <?php echo $hubShortName; ?> must attribute these materials to 
					the author(s). This is a human-readable summary of the Legal Code (<a class="popup 760x560" href="<?php echo $license; ?>">the full license</a>).</label>
					
					<?php if ($config->get('cc_license')) { ?>
					<label><input class="option" type="checkbox" name="license" value="1" /> <span class="optional">optional</span> 
					I further agree to license my work under the <a class="popup" href="legal/cc/">Creative Commons 2.5</a> license recommended by <?php echo $hubShortName; ?>. 
					I have read the <a class="popup" href="http://creativecommons.org/licenses/by-nc-sa/2.5/">license terms</a>, which support 
					non-commercial use, require attribution, and require sharing derivative works under the same license.</label>
					<?php } ?>
					
					<input type="hidden" name="option" value="<?php echo $option; ?>" />
					<input type="hidden" name="task" value="<?php echo $task; ?>" />
					<input type="hidden" name="id" value="<?php echo $id; ?>" />
					<input type="hidden" name="step" value="<?php echo $step; ?>" />
					<input type="hidden" name="published" value="0" />
			 	</fieldset><div class="clear"></div>
				<div class="submit">
					<input type="submit" value="<?php echo JText::_('SUBMIT_CONTRIBUTION'); ?>" />
				</div>
			</form>
			<?php 
		}
		$cats = array();
		$sections = array();
		
		ximport('resourcestats');
		
		$body = ResourcesHtml::about( $database, 0, $usersgroups, $resource, $helper, $config, array(), null, null, null, null, $params, $attribs, $option, 0 );
		
		$cat = array();
		$cat['about'] = JText::_('ABOUT');
		array_unshift($cats, $cat);
		array_unshift($sections, array('html'=>$body,'metadata'=>''));
		
		$html  = '<h1 id="preview-header">'.JText::_('REVIEW_PREVIEW').'</h1>'.n;
		$html .= '<div id="preview-pane">'.n;
		$html .= ResourcesHtml::title( 'com_resources', $resource, $params, false );
		$html .= ResourcesHtml::tabs( 'com_resources', $resource->id, $cats, 'about' );
		$html .= ResourcesHtml::sections( $sections, $cats, 'about', 'hide', 'main' );
		$html .= '</div><!-- / #preview-pane -->'.n;
		
		echo $html;
	}
	
	//-------------------------------------------------------------
	// Other views
	//-------------------------------------------------------------
	
	public function thanks( $option, $config, $resource )
	{
		if ($config->get('autoapprove') == 1) {
			?>
			<p class="passed">Thank you for your contribution! You may view your contribution <a href="<?php echo JRoute::_('index.php?option=com_resources&id='.$resource->id); ?>">here</a>.</p>
			<?php
		} else {
			?>
			<p class="passed">Thank you for your contribution! All contributions must undergo a review process. If accepted, you will be notified when it is available from our <a href="<?php echo JRoute::_('index.php?option=com_resources'); ?>">resources</a>.</p>
			<?php	
		}
		?>
		<p>Contribution submitted:</p>
		<p>
			<strong>Title:</strong> <?php echo stripslashes($resource->title); ?><br />
			<strong>ID#:</strong> <?php echo $resource->id; ?><br />
		</p>
		<p class="adminoptions">
			<a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=start'); ?>">Start a new submission</a> or 
			<a href="<?php echo JRoute::_('index.php?option='.$option); ?>">Return to start</a>
		</p>
		<?php
	}

	//-----------

	public function delete( $row, $option ) 
	{
		?>
		<form name="hubForm" id="hubForm" method="post" action="index.php" class="contrib">
			<div class="explaination">
				<p class="warning"><?php echo JText::_('Canceling a contribution will permanently delete any stored description, linked files, and tags. These cannot be recovered.'); ?><p>
			</div>
			<fieldset>
				<h3><?php echo JText::_('Contribution'); ?></h3>
				<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="discard" />
				<input type="hidden" name="step" value="2" />
				
				<p><strong><?php echo stripslashes($row->title); ?></strong><br />
				<?php echo $row->typetitle; ?></p>
				
				<label><input type="checkbox" name="confirm" value="confirmed" class="option" /> <?php echo JText::_('Confirm discard'); ?></label>
			</fieldset><div class="clear"></div>
			
			<div class="submit">
				<input type="submit" value="<?php echo JText::_('Delete'); ?>" />
			</div>
		</form>
		<?php
	}
	
	//-----------
	
 	public function attachments( $option, $id, $path, $children, $config, $error='' ) 
	{
		?>
		<form action="index.php" name="hubForm" id="attachments-form" method="post" enctype="multipart/form-data">
			<fieldset>
				<label>
					<input type="file" class="option" name="upload" />
				</label>
				<input type="submit" class="option" value="<?php echo JText::_('UPLOAD'); ?>" />

				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="no_html" value="1" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $id; ?>" />
				<input type="hidden" name="path" id="path" value="<?php echo $path; ?>" />
				<input type="hidden" name="task" value="saveattach" />
			</fieldset>
		</form>
		<?php
		$out = '';
		
		if ($error) {
			$out .= ContributeHtml::error($error);
		}
		
		// loop through children and build list
		if ($children) {
			$base = $config->get('uploadpath');
			
			$k = 0;
			$i = 0;
			$files = array(13,15,26,33,35,38);
			$n = count( $children );
			
			$out .= '<p>'.Jtext::_('ATTACH_EDIT_TITLE_EXPLANATION').'</p>'.n;
			$out .= '<table class="list">'.n;
			
			foreach ($children as $child) 
			{
				$k++;
			
				// figure ou the URL to the file
				switch ($child->type) 
				{
					case 12:
						if ($child->path) {
							// internal link, not a resource
							$url = $child->path; 
						} else {
							// internal link but a resource
							$url = '/index.php?option=com_resources'.a.'id='. $child->id;
						}
						break;
					default: 
						$url = $child->path;
						/*if (substr($url, 0, 1) == '/') {
							$url = substr($url, 1, strlen($url)-1);
						}*/
						break;
				}

				// figure out the file type so we can give it the appropriate CSS class
				$type = '';
				$liclass = '';
				$file_name_arr = explode('.',$url);
	    		$type = end($file_name_arr);
				$type = (strlen($type) > 3) ? substr($type, 0, 3): $type;
				if ($child->type == 12) {
					$liclass = 'html';
				} else {
					$type = ($type) ? $type : 'html';
					$liclass = $type;
				}
			
				$out .= ' <tr>'.n;
				$out .= '  <td width="100%" class="'.$liclass.'"><span class="ftitle item:name id:'.$child->id.'">'.$child->title.'</span> '.ContributeHtml::getFileAttribs( $url, $base ).'</td>'.n;
				$out .= '  <td class="d">'.ContributeHtml::orderUpIcon( $i, $id, $child->id, 'a' ).'</td>'.n;
				$out .= '  <td class="u">'.ContributeHtml::orderDownIcon( $i, $n, $id, $child->id, 'a' ).'</td>'.n;
				$out .= '  <td class="t"><a href="index.php?option='.$this->_option.a.'task=deleteattach'.a.'no_html=1'.a.'id='.$child->id.a.'pid='.$id.'"><img src="/components/'.$this->_option.'/images/trash.gif" alt="'.JText::_('DELETE').'" /></a></td>'.n;
				$out .= ' </tr>'.n;

				$i++;
			}
			$out .= '</table>'.n;
		} else {
			$out .= '<p>'.JText::_('ATTACH_NONE_FOUND').'</p>'.n;
		}
		echo $out;
	}

	//-----------
	
 	public function contributors( $id, $rows, $contributors, $option, $error='' ) 
	{
		?>
		<form action="index.php" id="authors-form" method="post" enctype="multipart/form-data">
			<fieldset>
				<label>
					<?php
					if ($error) {
						echo ContributeHtml::error($error);
					}
					
					$html  = '<select name="authid" id="authid">'.n;
					$html .= ' <option value="">'.JText::_('AUTHORS_SELECT').'</option>'.n;
					foreach ($rows as $row) 
					{
						if ($row->surname || $row->givenName) {
							$name  = stripslashes($row->surname).', ';
							$name .= stripslashes($row->givenName);
							if ($row->middleName != NULL) {
								$name .= ' '.stripslashes($row->middleName);
							}
 						} else {
 							$name = stripslashes($row->name);
 						}
 						
 						$html .= t.'<option value="'.$row->uidNumber.'">'.$name.'</option>'.n;
					}
					$html .= '</select>'.n;
					echo $html;
					?> 
					<?php echo JText::_('OR'); ?>
				</label>
				
				<label>
					<input type="text" name="new_authors" value="" />
					<?php echo JText::_('AUTHORS_ENTER_LOGINS'); ?>
				</label>
				
				<p class="submit">
					<input type="submit" value="<?php echo JText::_('ADD'); ?>" />
				</p>

				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="no_html" value="1" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $id; ?>" />
				<input type="hidden" name="task" value="saveauthor" />
			</fieldset>
		</form>
		<?php
		$out = '';
		
		// Do we have any contributors associated with this resource?
		if ($contributors) {
			$i = 0;
			$n = count( $contributors );
	
			// loop through contributors and build HTML list
			$out .= '<table class="list">'.n;
			$out .= ' <tbody>'.n;
			foreach ($contributors as $contributor) 
			{
				$out .= ' <tr>'.n;
				// build name
				$out .= '  <td width="100%">';
				if ($contributor->lastname || $contributor->firstname) {
					$out .= stripslashes($contributor->firstname) .' ';
					if ($contributor->middlename != NULL) {
						$out .= stripslashes($contributor->middlename) .' ';
					}
					$out .= stripslashes($contributor->lastname);
				} else {
					$out .= stripslashes($contributor->name);
				}
				$out .= ($contributor->org) ? ' <span class="caption">('.$contributor->org.')</span></td>'.n : '</td>'.n;
				// build order-up/down icons
				$out .= '  <td class="u">'.ContributeHtml::orderUpIcon( $i, $id, $contributor->id, 'c' ).'</td>'.n;
				$out .= '  <td class="d">'.ContributeHtml::orderDownIcon( $i, $n, $id, $contributor->id, 'c' ).'</td>'.n;
				// build trash icon
				$out .= '  <td class="t"><a href="index.php?option='.$option.a.'task=removeauthor'.a.'no_html=1'.a.'id='.$contributor->id.a.'pid='.$id.'"><img src="/components/'.$option.'/images/trash.gif" alt="'.JText::_('DELETE').'" /></a></td>'.n;
				$out .= ' </tr>'.n;

				$i++;
			}
			$out .= ' </tbody>'.n;
			$out .= '</table>'.n;
		} else {
			$out  = '<p>'.JText::_('AUTHORS_NONE_FOUND').'</p>'.n;
		}
		echo $out;
	}

	//-------------------------------------------------------------
	// Media manager functions
	//-------------------------------------------------------------
	
	public function pageTop( $option, $app ) 
	{
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('CONTRIBUTE'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link rel="stylesheet" type="text/css" media="screen" href="/templates/<?php echo $app->getTemplate(); ?>/css/main.css" />
	<?php
		if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'contribute.css')) {
			echo '<link rel="stylesheet" type="text/css" media="screen" href="'.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'contribute.css" />'.n;
		} else {
			echo '<link rel="stylesheet" type="text/css" media="screen" href="'.DS.'components'.DS.$option.DS.'contribute.css" />'.n;
		}
	?>
	
    <script type="text/javascript" src="/media/system/js/mootools.js"></script>
	<script type="text/javascript" src="/components/<?php echo $option; ?>/contribute.js"></script>
 </head>
 <body id="small-page">
 		<?php
	}
	
	//-----------
	
	public function pageBottom() 
	{
		$html  = ' </body>'.n;
		$html .= '</html>'.n;
		echo $html;
	}
	
	//-----------
	
	public function orderUpIcon( $i, $pid, $cid, $for='' ) 
	{
		if ($i > 0 || ($i+0 > 0)) {
		    return '<a href="index.php?option=com_contribute'.a.'no_html=1'.a.'pid='.$pid.a.'id='.$cid.a.'task=orderup'.$for.'" class="order up" title="'.JText::_('MOVE_UP').'"><span>'.JText::_('MOVE_UP').'</span></a>';
  		} else {
  		    return '&nbsp;';
		}
	}
	
	//-----------

	public function orderDownIcon( $i, $n, $pid, $cid, $for='' ) 
	{
		if ($i < $n-1 || $i+0 < $n-1) {
			return '<a href="index.php?option=com_contribute'.a.'no_html=1'.a.'pid='.$pid.a.'id='.$cid.a.'task=orderdown'.$for.'" class="order down" title="'.JText::_('MOVE_DOWN').'"><span>'.JText::_('MOVE_DOWN').'</span></a>';
  		} else {
  		    return '&nbsp;';
		}
	}
	
	//-----------

	public function niceidformat($someid) 
	{
		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}
	
	//-----------
	
	public function build_path( $date, $id, $base='' )
	{
		if ($date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs )) {
			$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		if ($date) {
			$dir_year  = date('Y', $date);
			$dir_month = date('m', $date);
		} else {
			$dir_year  = date('Y');
			$dir_month = date('m');
		}
		$dir_id = ContributeHtml::niceidformat( $id );
		
		$path = $base.DS.$dir_year.DS.$dir_month.DS.$dir_id;
	
		//return $base.DS.$dir_id;
		return $path;
	}
	
	//-----------
	
	public function dateToPath( $date ) 
	{
		if ($date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs )) {
			$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		$dir_year  = date('Y', $date);
		$dir_month = date('m', $date);
		return $dir_year.DS.$dir_month;
	}
	
	//-----------
	
	public function getFileAttribs( $path, $base_path='' )
	{
		// Return nothing if no path provided
		if (!$path) {
			return '';
		}
		
		if ($base_path) {
			// Strip any trailing slash
			if (substr($base_path, -1) == DS) { 
				$base_path = substr($base_path, 0, strlen($base_path) - 1);
			}
			// Ensure a starting slash
			if (substr($base_path, 0, 1) != DS) { 
				$base_path = DS.$base_path;
			}
		}
		
		// Ensure a starting slash
		if (substr($path, 0, 1) != DS) { 
			$path = DS.$path;
		}
		if (substr($path, 0, strlen($base_path)) == $base_path) {
			// Do nothing
		} else {
			$path = $base_path.$path;
		}
		$path = JPATH_ROOT.$path;

		//$file_name_arr = explode('.',$path);
	    //$type = end($file_name_arr);
		//$type = strtoupper($type);
		$file_name_arr = explode(DS,$path);
	    $type = end($file_name_arr);
	
		$fs = '';
		
		// Get the file size if the file exist
		if (file_exists( $path )) {
			$fs = filesize( $path );
		}
		
		$html  = '<span class="caption">('.$type;
		if ($fs) {
			switch ($type)
			{
				case 'HTM':
				case 'HTML':
				case 'PHP':
				case 'ASF':
				case 'SWF': $fs = ''; break;
				default: 
					$fs = ContributeHtml::formatsize($fs); 
					break;
			}
		
			$html .= ($fs) ? ', '.$fs : '';
		}
		$html .= ')</span>';
		
		return $html;
	}
	
	//-----------

	public function formatsize($file_size) 
	{
		if ($file_size >= 1073741824) {
			$file_size = round($file_size / 1073741824 * 100) / 100 . ' <abbr title="gigabytes">Gb</abbr>';
		} elseif ($file_size >= 1048576) {
			$file_size = round($file_size / 1048576 * 100) / 100 . ' <abbr title="megabytes">Mb</abbr>';
		} elseif ($file_size >= 1024) {
			$file_size = round($file_size / 1024 * 100) / 100 . ' <abbr title="kilobytes">Kb</abbr>';
		} else {
			$file_size = $file_size . ' <abbr title="bytes">b</abbr>';
		}
		return $file_size;
	}
}
?>
