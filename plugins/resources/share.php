<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_resources_share' );
	
//-----------

class plgResourcesShare extends JPlugin
{
	function plgResourcesShare(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'share' );
		$this->_params = new JParameter( $this->_plugin->params );
		
		$this->_option = 'com_resources';
	}
	
	//-----------
	
	function &onResourcesAreas( $resource )
	{
		static $areas = array(
			
		);
		
		return $areas;
	}

	//-----------

	function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		
		$xhub =& XFactory::getHub();
		$app =& JFactory::getApplication();
		$url  = $xhub->getCfg('hubLongURL').JRoute::_('index.php?option='.$this->_option.a.'id='.$resource->id);
		$html = '';
			
		// Incoming action
		$sharewith = JRequest::getVar( 'sharewith', '' );
		if($sharewith && $sharewith!='email') {
			$this->share($sharewith, $url, $resource, $xhub);
			return;
		}
		
		// Email form
		if($sharewith =='email') {
			$this->pageTop( $option, $app );
			$this->pageBottom( );
			exit();
		}
		
		
			
		// Build the HTML meant for the "about" tab's metadata overview
		$metadata = '';
		if ($rtrn == 'all' || $rtrn == 'metadata') {
			
			$popup = '<ul class="sharelinks">';
			$title = JText::_('Now viewing').' "'.$resource->title.'" '.JText::_('on').' '.$xhub->getCfg('hubShortName');
			$metadata  = '<div class="share">'.n;
			$metadata .= t.JText::_('Share').': ';
			$i = 1;
			$limit = intval($this->_params->get('icons_limit')) ? intval($this->_params->get('icons_limit')) : 8;
			
			// Facebook
			if($this->_params->get('share_facebook')) {
			$inline  = t.'<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=share'.a.'sharewith=facebook');
			$inline .= '" title="'.JText::_('Share on Facebook').'" class="share_facebook popup" rel="external">&nbsp;'.n;
			
			$metadata .= ($i <= $limit) ? $inline.'</a>' :'';
			$popup 	  .= '<li class="';
			$popup 	  .= ($i % 2) ? 'odd' : 'even';
			$popup    .= '">'.$inline.' '.JText::_('Facebook').'</a></li>';
			$i++;
			}
			
			// Twitter
			if($this->_params->get('share_twitter')) {
			$inline = t.'<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=share'.a.'sharewith=twitter');
			$inline .= '" title="'.JText::_('Share on Twitter').'" class="share_twitter popup" rel="external">&nbsp;'.n;
			
			$metadata .= ($i <= $limit) ? $inline.'</a>' :'';
			$popup 	  .= '<li class="';
			$popup 	  .= ($i % 2) ? 'odd' : 'even';
			$popup    .= '">'.$inline.' '.JText::_('Twitter').'</a></li>';
			$i++;
			}
			
			// Google
			if($this->_params->get('share_google')) {
			$inline = t.'<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=share'.a.'sharewith=google');
			$inline .= '" title="'.JText::_('Create Google bookmark').'" class="share_google popup" rel="external">&nbsp;'.n;
			
			$metadata .= ($i <= $limit) ? $inline.'</a>' :'';
			$popup 	  .= '<li class="';
			$popup 	  .= ($i % 2) ? 'odd' : 'even';
			$popup    .= '">'.$inline.' '.JText::_('Google').'</a></li>';
			$i++;
			}
			
			// Digg
			if($this->_params->get('share_digg')) {
			$inline = t.'<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=share'.a.'sharewith=digg');
			$inline .= '" title="'.JText::_('Share on Digg').'" class="share_digg popup" rel="external">&nbsp;'.n;
			
			$metadata .= ($i < $limit) ? $inline.'</a>' :'';
			$popup 	  .= '<li class="';
			$popup 	  .= ($i % 2) ? 'odd' : 'even';
			$popup    .= '">'.$inline.' '.JText::_('Digg').'</a></li>';
			$i++;
			}
			
			// Technorati
			if($this->_params->get('share_technorati')) {
			$inline  = t.'<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=share'.a.'sharewith=technorati');
			$inline .= '" title="'.JText::_('Share on Technorati').'" class="share_technorati popup" rel="external">&nbsp;'.n;
			
			$metadata .= ($i < $limit) ? $inline.'</a>' :'';
			$popup 	  .= '<li class="';
			$popup 	  .= ($i % 2) ? 'odd' : 'even';
			$popup    .= '">'.$inline.' '.JText::_('Technorati').'</a></li>';
			$i++;
			}
			
			// Delicious
			if($this->_params->get('share_delicious')) {
			$inline    = t.'<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=share'.a.'sharewith=delicious');
			$inline   .= '" title="'.JText::_('Share on Delicious').'" class="share_delicious popup" rel="external"">&nbsp;'.n;
			
			$metadata .= ($i < $limit) ? $inline.'</a>' :'';
			$popup 	  .= '<li class="';
			$popup 	  .= ($i % 2) ? 'odd' : 'even';
			$popup    .= '">'.$inline.' '.JText::_('Delicious').'</a></li>';
			$i++;
			}
			
			// reddit
			if($this->_params->get('share_reddit')) {
			$inline    = t.'<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=share'.a.'sharewith=reddit');
			$inline   .= '" title="'.JText::_('Share on Reddit').'" class="share_reddit popup" rel="external">&nbsp;'.n;
			
			$metadata .= ($i < $limit) ? $inline.'</a>' :'';
			$popup 	  .= '<li class="';
			$popup 	  .= ($i % 2) ? 'odd' : 'even';
			$popup    .= '">'.$inline.' '.JText::_('Reddit').'</a></li>';
			$i++;
			}
			
			// Email
			/*
			if($this->_params->get('share_email')) {
			$inline  = t.'<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=share'.a.'no_html=1'.a.'sharewith=email');
			$inline .= '" title="'.JText::_('Email this resource').'" class="share_email popup" rel="external">&nbsp;'.n;
			
			$metadata .= $inline.'</a>';
			$popup 	  .= '<li class="';
			$popup 	  .= ($i % 2) ? 'odd' : 'even';
			$popup    .= '">'.$inline.' '.JText::_('Email').'</a></li>';
			$i++;
			}
			
			// Print
			if($this->_params->get('share_print')) {
			$inline  = t.'<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=share'.a.'no_html=1'.a.'sharewith=print');
			$inline .= '" title="'.JText::_('Print this resource page').'" class="share_print popup" rel="external">&nbsp;'.n;
			
			$metadata .= $inline.'</a>';
			$popup 	  .= '<li class="';
			$popup 	  .= ($i % 2) ? 'odd' : 'even';
			$popup    .= '">'.$inline.' '.JText::_('Print').'</a></li>';
			$i++;
			}
			*/
			
			// pop up more
			if(($i+2) > $limit) {
			$metadata .= '...';
			}
			
			$popup .= '</ul>';
			
			$html  = '<dl class="shareinfo">'.n;
			//$html .= t.'<dt>Share this resource:</dt>'.n;
			$html .= t.'<dd>'.n;
			$html .= t.t.'<p>'.n;
			$html .= t.t.t.'Share this resource:'.n;
			$html .= t.t.'</p>'.n;
			$html .= t.t.'<div>'.n;
			$html .= $popup;
			$html .= t.t.'<div class="clear"></div>'.n;		
			$html .= t.t.'</div>'.n;
			$html .= t.'</dd>'.n;
			$html .= '</dl>'.n;
			$metadata .= $html;
			$metadata .= '</div>'.n;
			
		} 
		
		
		$arr = array(
				'html'=>'',
				'metadata'=>$metadata
			);

		return $arr;
	}
	
	
	//-----------
	public function share($with, $url, $resource, $xhub)
	{
	
		$title = '';
		$link = '';
		switch ( $with ) 
		{
			case 'facebook':   	
			$link =   'http://www.facebook.com/sharer.php?u='.$url;		
			break;
			
			case 'twitter':   	
			$link =   'http://twitter.com/home?status='.JText::_('Currently on').' '.$xhub->getCfg('hubShortName').' '.JText::_('viewing').' '.$resource->title	;
			break;
			
			case 'google':   	
			$link =   'http://www.google.com/bookmarks/mark?op=edit&bkmk='.$url.'&title='.$xhub->getCfg('hubShortName').': '.JText::_('resource').' '.$resource->id.' - '.$resource->title.'&labels='.$xhub->getCfg('hubShortName');	
			break;
			
			case 'digg':   	
			$link =   'http://digg.com/submit?phase=2&url='.$url.'&title='.$xhub->getCfg('hubShortName').': '.JText::_('resource').' '.$resource->id.' - '.$resource->title;
			break;
			
			case 'technorati':   	
			$link =   'http://www.technorati.com/faves?add='.$url;
			break;
			
			case 'delicious':   	
			$link =   'http://del.icio.us/post?url='.$url.'&title='.$xhub->getCfg('hubShortName').': '.JText::_('resource').' '.$resource->id.' - '.$resource->title;
			break;
			
			case 'reddit':   	
			$link =   'http://reddit.com/submit?url='.$url.'&title='.$xhub->getCfg('hubShortName').': '.JText::_('resource').' '.$resource->id.' - '.$resource->title;
			break;
			
			
		}
		
	
		if($link) {
			$this->redirect($link);
		}
		
	}
	
	//-----------

	public function redirect($url)
	{
		if ($url != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $url, '', '' );
		}
	}
	
	//----------
	public function pageTop( $option, $app ) 
	{
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('Email this resource'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<link rel="stylesheet" type="text/css" media="screen" href="/templates/<?php echo $app->getTemplate(); ?>/css/main.css" />
	
    <script type="text/javascript" src="/media/system/js/mootools.js"></script>
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
	
	
}

