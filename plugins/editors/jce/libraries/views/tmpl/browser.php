<?php
/**
* $Id: browser.php 26 2009-05-25 10:21:53Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<form onsubmit="return false;" action="<?php echo $this->action;?>" target="_self" method="post" enctype="multipart/form-data">
<div id="browser">
    <fieldset>
        <legend><?php echo JText::_('Browse');?></legend>
        <div style="width:100%" id="browser-layout">
        	<div class="layout-header-status"><div id="message-status"></div></div>
            <div class="layout-header-info"><div id="message-info"></div></div>       
            <!-- Left -->  
            <div class="layout-left">
                <div id="searchbox"><input id="search" type="text" /></div>
                <div id="tree">
                    <div class="top">
                        <div class="left"></div>
                        <div class="center"></div>
                        <div class="right"></div>
                        <span><?php echo JText::_('Folders');?></span>
                    </div>
                    <div id="tree-body" class="tree"></div>
                </div>
            </div>
            <!-- Center -->
            <div class="layout-center">
                <div id="sort-ext"></div>
                <div id="sort-name"></div>
                <div id="dir-list"></div>
				<div id="dir-limit">
					<div class="dir-limit-left">
						<div id="dir-limit-left-end"></div>
                   	 	<div id="dir-limit-left"></div>
					</div>
                    <div id="dir-limit-text"> 
						<label for="dir-limit-select"><?php echo JText::_('SHOW');?></label>
						<select id="dir-limit-select">
                    		<option value="10">10</option>
							<option value="25">25</option>
							<option value="50">50</option>
							<option value="100">100</option>
                    	</select>
                    </div>
					<div class="dir-limit-right">
						<div id="dir-limit-right-end"></div>
						<div id="dir-limit-right"></div>
					</div>
                </div>
            </div>
            <!-- Right -->
            <div class="layout-right">    
                <div id="actions"></div>
                <div class="spacer horizontal"></div>
                <div id="info">
                    <div class="top">
                        <div class="left"></div>
                        <div class="center"></div>
                        <div class="right"></div>
                        <span><?php echo JText::_('Details');?></span>
                    </div>
                    <div id="info-body">
                        <div id="info-text"></div>
                        <div class="spacer"></div>
                        <div id="info-buttons">
                            <div id="buttons"></div>
                        </div>
                        <div id="info-comment"></div>
                    </div>
                    <div id="info-nav">
                        <div id="info-nav-left"></div>
                        <div id="info-nav-text"></div>
                        <div id="info-nav-right"></div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
</div>
</form>