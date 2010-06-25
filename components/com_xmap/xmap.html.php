<?php
/**
 * $Id: xmap.html.php 100 2010-04-16 08:58:26Z guilleva $
 * $LastChangedDate: 2010-04-16 02:58:26 -0600 (vie, 16 abr 2010) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * A Sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/** Wraps HTML output */
class XmapHtml extends Xmap {
	var $level = -1;
	var $_openList = '';
	var $_closeList = '';
	var $_closeItem = '';
	var $_childs;
	var $_width;
	var $_isAdmin = 0;

        function XmapHtml (&$config, &$sitemap) {
                $this->view = 'html';
                Xmap::Xmap($config, $sitemap);
		$this->_parent_children=array();
		$this->_last_child=array();
        }

	/** 
	 * Print one node of the sitemap
	 */
	function printNode( &$node ) {
		$Itemid = JRequest::getInt('Itemid');

		$out = '';

		if ($this->sitemap->isExcluded($node->id,$node->uid) && !$this->_isAdmin) {
			return FALSE;
		}

		// To avoid duplicate children in the same parent
		if ( !empty($this->_parent_children[$this->level][$node->uid]) ) {
			return FALSE;
		}

		//var_dump($this->_parent_children[$this->level]);
		$this->_parent_children[$this->level][$node->uid] = true;

		$out .= $this->_closeItem;
		$out .= $this->_openList;
		$this->_openList = "";

		if ( $Itemid == $node->id )
			$out .= '<li class="active">';
		else
			$out .= '<li>';

		$link = Xmap::getItemLink($node);

		if( !isset($node->browserNav) )
			$node->browserNav = 0;

		$node->name = htmlspecialchars($node->name);
		switch( $node->browserNav ) {
			case 1:		// open url in new window
				$ext_image = '';
				if ( $this->sitemap->exlinks ) {
					$ext_image = '&nbsp;<img src="'. $this->live_site .'/components/com_xmap/images/'. $this->sitemap->ext_image .'" alt="' . _XMAP_SHOW_AS_EXTERN_ALT . '" title="' . _XMAP_SHOW_AS_EXTERN_ALT . '" border="0" />';
				}
				$out .= '<a href="'. $link .'" title="'. htmlspecialchars($node->name) .'" target="_blank">'. $node->name . $ext_image .'</a>';
				break;

			case 2:		// open url in javascript popup window
				$ext_image = '';
				if( $this->sitemap->exlinks ) {
					$ext_image = '&nbsp;<img src="'. $this->live_site .'/components/com_xmap/images/'. $this->sitemap->ext_image .'" alt="' . _XMAP_SHOW_AS_EXTERN_ALT . '" title="' . _XMAP_SHOW_AS_EXTERN_ALT . '" border="0" />';
				}
				$out .= '<a href="'. $link .'" title="'. $node->name .'" target="_blank" '. "onClick=\"javascript: window.open('". $link ."', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false;\">". $node->name . $ext_image."</a>";
				break;

			case 3:		// no link
				$out .= '<span>'. $node->name .'</span>';
				break;

			default:	// open url in parent window
				$out .= '<a href="'. $link .'" title="'. $node->name .'">'. $node->name .'</a>';
				break;
		}

		$this->_closeItem = "</li>\n";
		$this->_childs[$this->level]++;
		echo $out;
		
		if ($this->_isAdmin) {
			if ( $this->sitemap->isExcluded($node->id,$node->uid) ) {
				$img = '<img src="'.$this->live_site.'/administrator/images/publish_x.png" alt="'._XMAP_EXT_PUBLISHED.'" title="'._XMAP_EXT_UNPUBLISHED.'">';
				$class= 'xmapexclon';
			} else {
				$img = '<img src="'.$this->live_site.'/administrator/images/tick.png" alt="'._XMAP_EXT_PUBLISHED.'" title="'._XMAP_EXT_PUBLISHED.'" />';
				$class= 'xmapexcloff';
			}
			echo ' <a href= "#" class="xmapexcl '.$class.'" rel="{uid:\''.$node->uid.'\',itemid:'.$node->id.'}">'.$img.'</a>';
			# echo ' <a href= "#" class="xmapoptions" rel="{uid:\''.$node->uid.'\',itemid:'.$node->id.'}"><img src="'.$this->live_site.'/components/com_xmap/images/options.gif" border="0" alt="Options" title="Options" /></a>';
		}
		//echo $this->_last_child[$this->level-1] . ' ' . $this->_parent_children[$this->level]['parent'];
		$this->count++;
		
		$this->_last_child[$this->level] = $node->uid;
		
		return TRUE;
	}

	/**
	* Moves sitemap level up or down
	*/
	function changeLevel( $level ) {
		if ( $level > 0 ) {
			# We do not print start ul here to avoid empty list, it's printed at the first child
			$this->level += $level;
			$this->_childs[$this->level]=0;
                        $this->_openList = "\n<ul class=\"level_".$this->level."\">\n";
			$this->_closeItem = '';
			
			// If we are moving up, then lets clean the children of this level
			// because for sure this is a new set of links
			if ( empty ($this->_last_child[$this->level-1]) || empty ($this->_parent_children[$this->level]['parent']) || $this->_parent_children[$this->level]['parent'] != $this->_last_child[$this->level-1] ) {
				$this->_parent_children[$this->level]=array();
				$this->_parent_children[$this->level]['parent'] = @$this->_last_child[$this->level-1];
			}
		} else {
			if ($this->_childs[$this->level]){
				echo $this->_closeItem."</ul>\n";
			}
			$this->_closeItem ='</li>';
			$this->_openList = '';
			$this->level += $level;
		}
	}

	/** Print component heading, etc. Then call getHtmlList() to print list */
	function startOutput(&$menus,&$config) {
		$sitemap = &$this->sitemap;
		$this->live_site = substr_replace(JURI::root(), "", -1, 1);
        
        $Itemid = JRequest::getInt('Itemid');
		
		$user = &JFactory::getUser();

		if ($this->_isAdmin) {
			JHTML::_('behavior.mootools');
			$live_site = JURI::root();
			$ajaxurl = "$live_site/index.php?option=com_xmap&tmpl=component&task=editElement&action=toggleElement";
			
			$css = '.xmapexcl img{ border:0px; }'."\n";
			$css .= '.xmapexcloff { text-decoration:line-through; }';
			//$css .= "\n.".$this->sitemap->classname .' li {float:left;}';

			$js = "
				window.addEvent('domready',function (){
					$$('.xmapexcl').each(function(el){
						el.onclick = function(){
							if (this && this.rel) {
								options = Json.evaluate(this.rel);
								this.onComplete = checkExcludeResult
								var myAjax = new Ajax('{$ajaxurl}&sitemap={$this->sitemap->id}&uid='+options.uid+'&itemid='+options.itemid,{
											onComplete: checkExcludeResult.bind(this)
											}).request();
							}
							return false;
						};

					});
				});
				checkExcludeResult = function (txtresponse,xmlresponse) {
					//this.set('class','xmapexcl xmapexcloff');
					var imgs = this.getElementsByTagName('img');
					var response = xmlresponse.getElementsByTagName('response')[0];
					var result = response.getElementsByTagName('result')[0].firstChild.nodeValue;
					if (result == 'OK') {
						var state = response.getElementsByTagName('state')[0].firstChild.nodeValue;
						if (state==0) {
							imgs[0].src='{$live_site}administrator/images/publish_x.png';
						} else {
							imgs[0].src='{$live_site}administrator/images/tick.png';
						}
					} else {
						alert('The element couldn\\'t be published or upublished!');
					}
				}";
			
			$doc = JFactory::getDocument();
			$doc->addStyleDeclaration ($css);
			$doc->addScriptDeclaration ($js);
		}

		$menu = &JTable::getInstance('Menu');
		$menu->load( $Itemid );			// Load params for the Xmap menu-item
		$params = new JParameter($menu->params);
		$title = $params->get('page_title',$menu->name);

		$exlink[0] = $sitemap->exlinks;		// image to mark popup links
		$exlink[1] = $sitemap->ext_image;

		if( $sitemap->columns > 1 ) {		// calculate column widths
			$total = count($menus);
			$columns = $total < $sitemap->columns ? $total : $sitemap->columns;
			$this->_width	= (100 / $columns) - 1;
		}
		echo '<div class="'. $sitemap->classname .'">';

		if ( $params->get( 'show_page_title' ) ) {
		/**	echo '<div class="componentheading">'.$title.'</div>';*/
			echo '';
		}
		echo '<div class="contentpaneopen"'. ($sitemap->columns > 1 ? ' style="float:left;width:100%;"' : '') .'>';


	}

	/** Print component heading, etc. Then call getHtmlList() to print list */
	function endOutput(&$menus) {
		$sitemap = &$this->sitemap;

		echo '<div style="clear:left"></div>';
		//BEGIN: Advertisement
		if( $sitemap->includelink ) {
		/**	echo "<div style=\"text-align:center;\"><a href=\"http://joomla.vargas.co.cr\" target=\"_blank\" style=\"font-size:10px;\">Powered by Xmap!</a></div>";*/
		echo '';
		}
		//END: Advertisement

		echo "</div>";
		echo "</div>\n";
	}

	function startMenu(&$menu) {
		// Initialize them on each menu
		$this->_parent_children=array();
		$this->_last_child=array();
		$sitemap=&$this->sitemap;
		if( $sitemap->columns > 1 )			// use columns
			echo '<div style="float:left;width:'.$this->_width.'%;">';
		if( $sitemap->show_menutitle )			// show menu titles
			echo '<h2 class="menutitle">'.$menu->name.'</h2>';
	}

	function endMenu(&$menu) {
		$sitemap=&$this->sitemap;
		$this->_closeItem='';
		if( $sitemap->show_menutitle || $sitemap->columns > 1 ) {		// each menu gets a separate list
			if( $sitemap->columns > 1 ) {
				echo "</div>\n";
			}

		}
	}
}