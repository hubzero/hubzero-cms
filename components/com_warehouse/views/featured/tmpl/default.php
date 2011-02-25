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

			<p>An <strong>Enhanced project</strong> is a project that has been checked for completeness and includes:</p>

			<p>
			- Drawings<br>
			- Material Information<br>
			- Complete and detailed Sensor Information<br>
			- At least one data file that can be read and plotted by a data visualization tool (e.g. inDEED)<br>
			- At least one report or publication <br>
			</p>


      		<h2 style="margin-top:0px">Quick Links to Enhanced Projects</h2>

      		<ul>
                    <?php
                      $oProjectArray = array();
                      if(isset ($_REQUEST[ProjectPeer::TABLE_NAME])){
                        $oProjectArray = unserialize($_REQUEST[ProjectPeer::TABLE_NAME]);
                      }

                      /* @var $oProject Project */
                      foreach($oProjectArray as $oProject){?>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/<?php echo $oProject->getId(); ?>"><?php echo $oProject->getId(); ?> - <?php echo $oProject->getNickname(); ?></a><br/></li>
                      <?php
                      }
                    ?>
                </ul>

			<br/><br/><br/><br/><br/><br/>

</div>
</div>