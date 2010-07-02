<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php 
	//echo $_SERVER['DOCUMENT_ROOT'];
  $document =& JFactory::getDocument();
  $document->addStyleSheet("/modules/mod_treebrowser/common/main.css",'text/css');
//  $document->addScript("/modules/mod_treebrowser/common/base.js", 'text/javascript');
  
  $document->addStyleSheet("modules/mod_treebrowser/tree_browser/style.css",'text/css');
  $document->addStyleSheet("modules/mod_treebrowser/tree_browser/nlstree-basic.css",'text/css');
  $document->addStyleSheet("modules/mod_treebrowser/tree_browser/nlstree.css",'text/css');
  
  $document->addScript("/modules/mod_treebrowser/tree_browser/nlstree.js", 'text/javascript');
  $document->addScript("/modules/mod_treebrowser/tree_browser/nlstreeext_state.js", 'text/javascript');
  $document->addScript("/modules/mod_treebrowser/tree_browser/nlstreeext_ctx.js", 'text/javascript');
  $document->addScript("/modules/mod_treebrowser/tree_browser/nlsctxmenu.js", 'text/javascript');
  $document->addScript("/modules/mod_treebrowser/tree_browser/nlstreeext_frm_inc.js", 'text/javascript');
  
  
  //$document->addStyleSheet("modules/mod_treebrowser/tree_browser/nlsctxmenu.css",'text/css');
  
//  $document->addScript("/modules/mod_treebrowser/tree_browser/nlstree_limited.js", 'text/javascript');
//    
//  
//  $document->addScript("/modules/mod_treebrowser/tree_browser/nlstreeext_dd.js", 'text/javascript');
//  $document->addScript("/modules/mod_treebrowser/tree_browser/nlstreeext_htm.js", 'text/javascript');
//  $document->addScript("/modules/mod_treebrowser/tree_browser/nlstreeext_inc.js", 'text/javascript');
//  $document->addScript("/modules/mod_treebrowser/tree_browser/nlstreeext_sel.js", 'text/javascript');
//  
//  $document->addScript("/modules/mod_treebrowser/tree_browser/nlstreeext_xml.js", 'text/javascript');
//  
  
  //$document->addScript($this->baseurl."/includes/js/joomla.javascript.js", 'text/javascript');
?>

<?php echo $left_portlet ?>

