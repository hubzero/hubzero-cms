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

      		<h2 style="margin-top:0px">Quick Links to Enhanced Projects</h2>
      	
      		<ul>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/21">21 - Semiactive Control of Nonlinear Structures</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/22">22 - Non-rectangular Walls</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/27">27 - Slickensided Surfaces</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/32">32 - SFSI Testbed: Shaking Table Tests</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/33">33 - Bridge Systems with Conventional and Innovative Materials</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/42">42 - Passive Pressure on Pile Caps</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/78">78 - High-Strength-Concrete Structural Walls</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/84">84 - Sidesway Collapse of Deteriorating Structural Systems</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/116">116 - Damage-Tolerant Slab-Column Frame Systems</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/138">138 - Full-Scale Two-Story Wood Building</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/180">180 - SFSI Testbed: Centrifuge Tests</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/411">411 - Elastomeric Structural Damper</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/412">412 - Landslides in Cohesive Slopes</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/414">414 - Using the NEES Field Shakers to Induce Liquefaction at Previous Liquefaction Sites</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/625">625 - Full-Scale RC Flat-Plate Structure</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/637">637 - Vulnerable Concrete Buildings</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/665">665 - Wave Loading on Residential Structures</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/672">672 - Deep Shear Wave Velocities in the Las Vegas Basin</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/711">711 - Advanced Servo-Hydraulic Control and Real-Time Testing Of Damped Structures</a><br/></li>
                        <li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/904">904 - Concrete Columns Reinforced with High-Strength Steel</a><br/></li>
						<li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/905">905 - Shear Strength Decay in Reinforced Concrete Columns Subjected to Large Deflection Reversals</a><br/></li>
						<li style="line-height: 1.5em; margin: 0em;"><a href="/warehouse/project/915">915 - Behavior of Ten Story Reinforced Concrete Walls Subjected to Earthquake Motions</a><br/></li>
                </ul>

			<br/><br/><br/><br/><br/><br/>
  
</div>
</div>