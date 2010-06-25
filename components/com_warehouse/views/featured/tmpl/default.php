<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
//  $document->addScript($this->baseurl."/components/com_warehouse/js/Fx.Slide/demo.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/Fx.Slide/tree.js", 'text/javascript');
  $document->addScript($this->baseurl."/includes/js/joomla.javascript.js", 'text/javascript');

  


?>

<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  



  
  <div id="warehouseWindow" style="padding-top:20px;margin-left:27px">

  
    <?php echo $this->strTabs; ?>

			<br/>

      		<h2 style="margin-top:0px">Quick Links to Featured Projects</h2>
      	
      		<ul>
	      		<li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/22">Non-rectangular Walls</a><br/></li>
	      		<li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/27">Slickensided Clay Surfaces</a><br/></li>
	      		<li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/414">Liquefaction of Previous Liquefaction Sites </a><br/></li>
	      		<li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/625">RC Flat-plate Structure</a><br/></li>
      		</ul>

			<br/><br/><br/><br/><br/><br/>
  
</div>