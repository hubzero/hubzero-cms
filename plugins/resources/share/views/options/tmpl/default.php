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
defined('_JEXEC') or die( 'Restricted access' );

$this->css()
     ->js();

$jconfig = JFactory::getConfig();

$i = 1;
$limit = intval($this->_params->get('icons_limit')) ? intval($this->_params->get('icons_limit')) : 8;

$popup = '<ul class="sharelinks">';
$title = JText::sprintf('PLG_RESOURCES_SHARE_VIEWING',$jconfig->getValue('config.sitename'),stripslashes($this->resource->title));
$metadata  = '<div class="share">'."\n";
$metadata .= "\t".JText::_('PLG_RESOURCES_SHARE').': ';

// Facebook
if ($this->_params->get('share_facebook'))
{
	$inline  = "\t".'<a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=share&sharewith=facebook');
	$inline .= '" title="'.JText::sprintf('PLG_RESOURCES_SHARE_ON', JText::_('PLG_RESOURCES_SHARE_FACEBOOK')).'" class="share_facebook popup" rel="external">&nbsp;'."\n";

	$metadata .= ($i <= $limit) ? $inline.'</a>' :'';
	$popup    .= '<li class="';
	$popup    .= ($i % 2) ? 'odd' : 'even';
	$popup    .= '">'.$inline.' '.JText::_('PLG_RESOURCES_SHARE_FACEBOOK').'</a></li>';
	$i++;
}

// Twitter
if ($this->_params->get('share_twitter'))
{
	$inline = "\t".'<a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=share&sharewith=twitter');
	$inline .= '" title="'.JText::sprintf('PLG_RESOURCES_SHARE_ON', JText::_('PLG_RESOURCES_SHARE_TWITTER')).'" class="share_twitter popup" rel="external">&nbsp;'."\n";

	$metadata .= ($i <= $limit) ? $inline.'</a>' :'';
	$popup    .= '<li class="';
	$popup    .= ($i % 2) ? 'odd' : 'even';
	$popup    .= '">'.$inline.' '.JText::_('PLG_RESOURCES_SHARE_TWITTER').'</a></li>';
	$i++;
}

// Google
if ($this->_params->get('share_google'))
{
	$inline = "\t".'<a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=share&sharewith=google');
	$inline .= '" title="'.JText::sprintf('PLG_RESOURCES_SHARE_CREATE_BOOKMARK', JText::_('PLG_RESOURCES_SHARE_GOOGLE')).'" class="share_google popup" rel="external">&nbsp;'."\n";

	$metadata .= ($i <= $limit) ? $inline.'</a>' :'';
	$popup    .= '<li class="';
	$popup    .= ($i % 2) ? 'odd' : 'even';
	$popup    .= '">'.$inline.' '.JText::_('PLG_RESOURCES_SHARE_GOOGLE').'</a></li>';
	$i++;
}

// Digg
if ($this->_params->get('share_digg'))
{
	$inline = "\t".'<a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=share&sharewith=digg');
	$inline .= '" title="'.JText::sprintf('PLG_RESOURCES_SHARE_ON', JText::_('PLG_RESOURCES_SHARE_DIGG')).'" class="share_digg popup" rel="external">&nbsp;'."\n";

	$metadata .= ($i < $limit) ? $inline.'</a>' :'';
	$popup    .= '<li class="';
	$popup    .= ($i % 2) ? 'odd' : 'even';
	$popup    .= '">'.$inline.' '.JText::_('PLG_RESOURCES_SHARE_DIGG').'</a></li>';
	$i++;
}

// Technorati
if ($this->_params->get('share_technorati'))
{
	$inline  = "\t".'<a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=share&sharewith=technorati');
	$inline .= '" title="'.JText::sprintf('PLG_RESOURCES_SHARE_ON', JText::_('PLG_RESOURCES_SHARE_TECHNORATI')).'" class="share_technorati popup" rel="external">&nbsp;'."\n";

	$metadata .= ($i < $limit) ? $inline.'</a>' :'';
	$popup    .= '<li class="';
	$popup    .= ($i % 2) ? 'odd' : 'even';
	$popup    .= '">'.$inline.' '.JText::_('PLG_RESOURCES_SHARE_TECHNORATI').'</a></li>';
	$i++;
}

// Delicious
if ($this->_params->get('share_delicious'))
{
	$inline    = "\t".'<a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=share&sharewith=delicious');
	$inline   .= '" title="'.JText::sprintf('PLG_RESOURCES_SHARE_ON', JText::_('PLG_RESOURCES_SHARE_DELICIOUS')).'" class="share_delicious popup" rel="external">&nbsp;'."\n";

	$metadata .= ($i < $limit) ? $inline.'</a>' :'';
	$popup    .= '<li class="';
	$popup    .= ($i % 2) ? 'odd' : 'even';
	$popup    .= '">'.$inline.' '.JText::_('PLG_RESOURCES_SHARE_DELICIOUS').'</a></li>';
	$i++;
}

// Reddit
if ($this->_params->get('share_reddit'))
{
	$inline    = "\t".'<a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=share&sharewith=reddit');
	$inline   .= '" title="'.JText::sprintf('PLG_RESOURCES_SHARE_ON', JText::_('PLG_RESOURCES_SHARE_REDDIT')).'" class="share_reddit popup" rel="external">&nbsp;'."\n";

	$metadata .= ($i < $limit) ? $inline.'</a>' :'';
	$popup    .= '<li class="';
	$popup    .= ($i % 2) ? 'odd' : 'even';
	$popup    .= '">'.$inline.' '.JText::_('PLG_RESOURCES_SHARE_REDDIT').'</a></li>';
	$i++;
}

// Pop up more
if (($i+2) > $limit)
{
	$metadata .= '...';
}

$popup .= '</ul>';

$metadata .= '<dl class="shareinfo">'."\n";
$metadata .= "\t".'<dt>' . JText::_('PLG_RESOURCES_SHARE') . '</dt>'."\n";
$metadata .= "\t".'<dd>'."\n";
$metadata .= "\t\t".'<p>'."\n";
$metadata .= "\t\t\t".JText::_('PLG_RESOURCES_SHARE_RESOURCE')."\n";
$metadata .= "\t\t".'</p>'."\n";
$metadata .= "\t\t".'<div>'."\n";
$metadata .= $popup;
$metadata .= "\t\t".'</div>'."\n";
$metadata .= "\t".'</dd>'."\n";
$metadata .= '</dl>'."\n";
$metadata .= '</div>'."\n";

echo $metadata;
