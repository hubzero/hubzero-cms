<?php
/**
* @version		$Id$
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
class ArticleConfig {
	function getConfig( &$vars ){
		$jce =& JContentEditor::getInstance();
		
		$params = $jce->getPluginParams('article');
		
		$vars['article_hide_xtd_btns'] = $jce->getParam($params, 'article_hide_xtd_btns', 0, 0);
	}
}
?>