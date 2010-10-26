<?php defined('_JEXEC') or die('Restricted access');	
	JHTML::script('sortables.js', 'administrator/components/com_jce/js/');
	
	JHTML::stylesheet('styles.css', 'administrator/components/com_jce/css/');
	JHTML::stylesheet('icons.css', 'administrator/components/com_jce/css/');
 	JHTML::stylesheet('layout.css', 'administrator/components/com_jce/css/');
	
	JToolBarHelper::title( JText::_( 'JCE Group' ) .': <small><small>[' .JText::_('Edit'). ']</small></small>', 'user.png' );
	JToolBarHelper::save();
	JToolBarHelper::apply();
	JToolBarHelper::cancel( 'cancelEdit', 'Close' );
	jceToolbarHelper::help( 'groups' );

	// clean item data
	JFilterOutput::objectHTMLSafe( $this->group, ENT_QUOTES, '' );
?>
<script type="text/javascript">
	window.addEvent('domready', function(){
		$ES('h3.jpane-toggler-down').removeClass('jpane-toggler-down').addClass('jpane-toggler');
		new Sortables('.sortableList', {revert: true, 
			onComplete : function(el){
				var state = el.getParent().id == 'groupLayout';
				$ES('li.sortableItem', el).each(function(c){
					setParams(c.id, state);	
				});			
			}
		});
		new Sortables('.sortableRow', {revert: true, 
			onComplete : function(el){
				var state = $ES('.sortableItem', $('groupLayout')).contains(el);
				setParams(el.id, state);			
			}
		});
		$ES('div[id^=plugin_params_]', $('plugin_params')).each(function(p){			
			if(p.style.display == 'none'){
				setParams(p.id, false);
			}
		});	
		
		var ew = $('paramseditor_width');
		var eh = $('paramseditor_height');
		ew.addEvent('change', function(){
			v = ew.value;
			if(/%/.test(v)){
				return;
			}else{
				ew.value = v.replace(/[^0-9]/g, '');
				$ES('span.sortableListSpan').each(function(el){
					el.style.width = ew.value + 'px';
				});
				$ES('div.widthMarker').each(function(el){
					el.style.width 	= ew.value + 'px';
					el.innerHTML 	= ew.value + 'px';
				});
			}
		});
		eh.addEvent('change', function(){
			eh.value = eh.value.replace(/[^0-9%]/g, '');
		});
		$ES('.icon-add').each(function(el){
			var o = el.className.replace(/icon-(add|remove)/i, '').trim();
			if(o == 'users') return;
			el.addEvent('click', function(){
				var s = $(o) || [];
				$each(s.options, function(n){
					n.selected = true;
				});
			});
		});
		$ES('.icon-remove').each(function(el){
			var o = el.className.replace(/icon-(add|remove)/i, '').trim();
			el.addEvent('click', function(){
				var s = $(o) || [];
				if(o == 'users'){
					for(var i = s.length - 1; i>=0; i--){
						if (s.options[i].selected) {
							s.options[i] = null;
						}
					}
				}else{
					for(var i=0; i<s.length; i++){
						s.options[i].selected = false;
					}
				}
			});
		});
		$('components-all').addEvent('click', function(){
			var o = $('components');
			o.disabled = true;
			$each(o.options, function(n) {
            	n.selected = false;;
				n.disabled = true;
            });
		});
		
		$('components-select').addEvent('click', function(){
			var o = $('components');
			o.disabled = false;
			$each(o.options, function(n) {
				n.disabled = false;
            });
		});
	});
	function setParams(id, state){
		id = id.replace(/[^0-9]/gi, ''); 
		var params = $('plugin_params_' + id) || false;		
		if(params){
			var disabled = state ? '' : 'disabled';
			
			params.style.display = state ? 'block' : 'none';				
			$ES('input, select, textarea', params).each(function(input){
				input.disabled = disabled;
			});
		}
	}
	function submitbutton(pressbutton) {
		var form = document.adminForm, items = [];
		// Cancel button
		if (pressbutton == "cancelEdit") {
			submitform(pressbutton);
			return;
		}
		// validation
		if (form.name.value == "") {
			alert( "<?php echo JText::_( 'Group must have a name', true ); ?>" );
		} else {
			// Serialize group layout
			$ES('ul.sortableRow', $('groupLayout')).each(function(el){
				items.include(el.getChildren().map(function(o, i){
					if(o.hasClass('spacer')){
						return '00';
					}
					return o.id.replace(/[^0-9]/gi, '');
				}).join(','));	
			});
			form.rows.value = items.join(';') || '';
			// Select added users
			for (var i=0; i<form.users.options.length; i++) {
				form.users.options[i].selected = true;
			}		
			submitform(pressbutton);
		}
	}
</script>
<form action="index.php" method="post" name="adminForm">
<?php
jimport('joomla.html.pane');
$pane =& JPane::getInstance('tabs', array( 'allowAllClose' => true ) );
echo $pane->startPane("config-document");
echo $pane->startPanel(JText :: _('Setup'), "page-setup");
?>                            
<div>
<fieldset class="adminform">
<legend><?php echo JText::_( 'Details' ); ?></legend>
<table class="admintable">
    <tr>
        <td width="100" class="key">
            <label for="name">
                <?php echo JText::_( 'Name' ); ?>:
            </label>
        </td>
        <td>
            <?php if( $this->group->name == 'Default' ){
                echo $this->group->name;
            ?>
                <input type="hidden" name="name" value="<?php echo $this->group->name; ?>" />
            <?php }else{?>
                <input class="text_area" type="text" name="name" id="name" size="35" value="<?php echo $this->group->name; ?>" />
            <?php }?>
        </td>
    </tr>
    <tr>
        <td width="100" class="key">
            <label for="name">
                <?php echo JText::_( 'Description' ); ?>:
            </label>
        </td>
        <td>
            <input class="text_area" type="text" name="description" id="description" size="100" value="<?php echo $this->group->description; ?>" />
        </td>
    </tr>
    <tr>
        <td valign="top" class="key">
            <?php echo JText::_( 'Published' ); ?>:
        </td>
        <td>
            <?php if( $this->group->name == 'Default' ){
                echo JText::_('Enabled');
            ?>
                <input type="hidden" name="published" value="1" />
            <?php }else{
                echo $this->lists['published'];
            }?>
        </td>
    </tr>
    <tr>
        <td valign="top" class="key">
            <?php echo JText::_( 'Priority' ); ?>:
        </td>
        <td>
            <?php echo $this->lists['ordering']; ?>
        </td>
    </tr>
</table>
</fieldset>
</div>
<div>
<fieldset class="adminform">
    <legend><?php echo JText::_( 'Restrictions' ); ?></legend>
    <table class="admintable">
        <tr>
            <td colspan="3"><?php echo JText::_( 'Restrictions DESC' ); ?></td>
        </tr>
		<tr style="border:1px solid black;">
            <td valign="top" class="key">
                <?php echo JText::_( 'Components' ); ?>:
            </td>
            <td>
				<p>
					<!--input id="components-all" type="radio" name="components-select" value="all" /><label for="components-all"><?php echo JText::_( 'All Components' ); ?></label>
					<input id="components-select" type="radio" name="components-select" value="select" /><label for="components-select"><?php echo JText::_( 'Select From List' ); ?></label-->
					<?php echo $this->lists['components_radio'];?>
				</p>
				<p><?php echo $this->lists['components']; ?></p>
            </td>
            <td class="note">
                <p><?php echo JText::_('GROUP COMPONENTS DESC');?></p>
                <p><strong><?php echo JText::_('NOTE');?> :</strong><br /> <?php echo JText::_('GROUP COMPONENTS NOTE');?></p>
                <p><strong><?php echo JText::_('TIP');?> :</strong><br /> <?php echo JText::_('GROUP COMPONENTS TIP');?></p>
            </td>
        </tr>
        <tr>
            <td valign="top" class="key">
                <?php echo JText::_( 'Types' ); ?>:
            </td>
            <td>
                <?php echo $this->lists['types']; ?>
                <p>
                    <span class="icon-add types"><span class="icon-text"><?php echo JText::_('Add All');?></span></span>
                    <span class="icon-remove types"><span class="icon-text"><?php echo JText::_('Remove All');?></span></span>
                </p>
            </td>
            <td class="note">
                <p><?php echo JText::_('GROUP TYPES DESC');?></p>
                <p><strong><?php echo JText::_('NOTE');?> :</strong><br /> <?php echo JText::_('GROUP TYPES NOTE');?></p>
                <p><strong><?php echo JText::_('TIP');?> :</strong><br /> <?php echo JText::_('GROUP TYPES TIP');?></p>
            </td>
        </tr>
        <tr>
            <td valign="top" class="key">
                <?php echo JText::_( 'Users' ); ?>:
            </td>
            <td>
                <?php echo $this->lists['users']; ?>
                <p><a class="modal" rel="{handler: 'iframe', size: {x: 750, y: 600}}" href="index.php?option=com_jce&tmpl=component&type=group&task=addusers"><span class="icon-add users"><span class="icon-text"><?php echo JText::_('Add User');?></span></span></a>
                <span class="icon-remove users"><span class="icon-text"><?php echo JText::_('Remove Users');?></span></span></p>
            </td>
            <td class="note">
                <p><?php echo JText::_('GROUP USERS DESC');?></p>
                <p><strong><?php echo JText::_('NOTE');?> :</strong><br /> <?php echo JText::_('GROUP USERS NOTE');?></p>
                <p><strong><?php echo JText::_('TIP');?> :</strong><br /> <?php echo JText::_('GROUP USERS TIP');?></p>
            </td>
        </tr>
    </table>
    </fieldset>
</div>
<?php echo $pane->endPanel();?>
<?php echo $pane->startPanel(JText :: _('Editor Parameters'), "page-editor-params");?>
<div id="editor_params">
    <fieldset class="adminform">
    <legend><?php echo JText :: _('Editor Setup');?></legend>
    <?php
        if($output = $this->params->render('params', 'groups-editor')) :
            echo $output;
        else :
            echo "<div style=\"text-align: center; padding: 5px; \">".JText::_('There are no parameters for this item')."</div>";
        endif;
    ?>
    </fieldset>
    <fieldset class="adminform">
    <legend><?php echo JText :: _('Editor Options');?></legend>
    <?php 
        if($output = $this->params->render('params', 'groups-options')) :
            echo $output;
        else :
            echo "<div style=\"text-align: center; padding: 5px; \">".JText::_('There are no parameters for this item')."</div>";
        endif;
    ?>
    </fieldset>
    <fieldset class="adminform">
    <legend><?php echo JText :: _('Plugin Options');?></legend>
    <?php 
       
        if($output = $this->params->render('params', 'groups-plugins')) :
            echo $output;
        else :
            echo "<div style=\"text-align: center; padding: 5px; \">".JText::_('There are no parameters for this item')."</div>";
        endif;
    ?>
    </fieldset>
</div>
<?php echo $pane->endPanel();?>
<?php echo $pane->startPanel(JText :: _('Layout'), "page-layout");?>
<div id="page-layout">
    <fieldset class="adminform">
    <legend><?php echo JText::_( 'GROUPS LAYOUT' ); ?></legend>
        <table>
            <tr><td><?php echo JText::_( 'GROUPS LAYOUT DESC' ); ?></td></tr>
            <tr>
                <td>
                <fieldset>
                <legend><?php echo JText::_( 'GROUP AVAILABLE BUTTONS' ); ?></legend>
                <div class="sortableList">
                <?php 
                $rows 	= JCEGroupsHelper::getRowArray( $this->group->rows );
				$spacer = file_exists( JPATH_PLUGINS .DS. 'editors' .DS. 'jce' .DS. 'tiny_mce' .DS. 'themes' .DS. 'advanced' .DS. 'img' .DS. 'spacer.gif' ); 
                for( $i=1; $i<=5; $i++ ){
                ?>
                    <div class="sortableListDiv">
                        <span class="sortableListSpan" style="width:<?php echo $this->params->get('editor_width', '600');?>px;">
                        <ul class="sortableRow">
                        	<?php if( $spacer ){ 
								if( $i == 5 ){
									for( $x = 1; $x<=10; $x++ ){?>
										<li class="sortableItem spacer"><img src="../plugins/editors/jce/tiny_mce/themes/advanced/img/spacer.gif" alt="<?php echo JText::_('Spacer');?>" title="<?php echo JText::_('Spacer');?>" /></li>
									<?php }
								}
							}
                    foreach( $this->plugins as $icon ){
                        if( !in_array( $icon->id, explode( ',', implode( ',', $rows ) ) ) ){
                            if( $icon->layout && $icon->row == $i ){
                                $n = "row_li_" .$icon->id;
                                $path = $icon->type == 'command' ? '../plugins/editors/jce/tiny_mce/themes/advanced/img/'. $icon->layout .'.gif' : '../plugins/editors/jce/tiny_mce/plugins/'. $icon->name .'/img/'. $icon->layout .'.gif';
                                ?>
                                <li class="sortableItem" id="<?php echo $n;?>"><img src="<?php echo $path;?>" alt="<?php echo $icon->title;?>" title="<?php echo $icon->title;?>" /></li>
							<?php }
                        }
                    }
                    ?>
                        </ul>
                        </span>
                    </div>
                <?php }?>
                </div>
                <div class="widthMarker" style="width:<?php echo $this->params->get('editor_width', '600');?>px;"><?php echo $this->params->get('editor_width', '600');?>px</div>
                </fieldset>
                
                <fieldset>
                <legend><?php echo JText::_( 'GROUP EDITOR LAYOUT' ); ?></legend>
                <div class="sortableList" id="groupLayout">
               <?php
                for( $i=1; $i<=count( $rows )+1; $i++ ){?>
                    <div class="sortableListDiv">
                        <span class="sortableListSpan" style="width:<?php echo $this->params->get('editor_width', '600');?>px;">
                        <ul class="sortableRow">
                <?php
                    for( $x=1; $x<=count( $rows ); $x++ ){
                        if( $i == $x ){
                            $icons = explode( ',', $rows[$x] );
                            foreach( $icons as $icon ){
								if( $spacer ){
									if( $icon == '00' ){?>
										<li class="sortableItem spacer"><img src="../plugins/editors/jce/tiny_mce/themes/advanced/img/spacer.gif" alt="<?php echo JText::_('Spacer');?>" title="<?php echo JText::_('Spacer');?>" /></li>
								<?php }
								}
                                foreach( $this->plugins as $button ){
									if( $button->layout && $button->id == $icon ){
                                        $n = "group_li_". $button->id;
                                        $path = $button->type == 'command' ? '../plugins/editors/jce/tiny_mce/themes/advanced/img/'. $button->layout .'.gif' : '../plugins/editors/jce/tiny_mce/plugins/'. $button->name .'/img/'. $button->layout .'.gif';
                                        ?>
                                        <li class="sortableItem" id="<?php echo $n;?>"><img src="<?php echo $path;?>" alt="<?php echo $button->title;?>" title="<?php echo $button->title;?>" /></li>
                                    <?php }
                                }
                            }
                        }
                    }
                    ?>
                        </ul>
                        </span>
                    </div>
                <?php }?>
                </div>
                <div class="widthMarker" style="width:<?php echo $this->params->get('editor_width', '600');?>px;"><?php echo $this->params->get('editor_width', '600');?>px</div>
                </fieldset>
                </td>
            </tr>
            <tr><td><a class="modal" rel="{handler: 'iframe', size: {x: 750, y: 600}}" href="index.php?option=com_jce&tmpl=component&type=group&task=legend"><span class="icon-legend"><span class="icon-text"><?php echo JText::_('Icon Legend');?></span></span></a></td></tr>
        </table>
    </fieldset>	
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'GROUPS OTHER PLUGINS' ); ?></legend>
        <table class="admintable">
            <tr><td colspan="2"><?php echo JText::_('GROUPS OTHER PLUGINS DESC');?></td></tr>
            <tr>
                <?php 
                $i = 0;
                foreach( $this->plugins as $plugin ){
                     if( $plugin->layout == '' ){
                        if( $plugin->editable ){?>
                            <tr>
                                <td><input type="checkbox" id="cb<?php echo $i;?>" name="pid[]" value="<?php echo $plugin->id;?>" onclick="isChecked(this.checked);setParams(this.value, this.checked);" <?php echo in_array( $plugin->id, explode( ',', $this->group->plugins ) ) ? 'checked="checked"' : '';?>/></td>
                                <td><?php echo $plugin->title;?></td>
                            </tr>
                 <?php }else{?>
                            <tr>
                                <td><input type="checkbox" id="cb<?php echo $i;?>" name="pid[]" value="<?php echo $plugin->id;?>" onclick="isChecked(this.checked);" <?php echo in_array( $plugin->id, explode( ',', $this->group->plugins ) ) ? 'checked="checked"' : '';?>/></td>
                                <td><?php echo $plugin->title;?></td>
                            </tr>
                <?php  }
                    }
                    $i++;
                }?>
            </tr>
        </table>
    </fieldset>
</div>
<?php echo $pane->endPanel();?>
<?php echo $pane->startPanel(JText :: _('Plugin Parameters'), "page-plugin-params");?>
        <div id="plugin_params" style="padding-left:-30px;">
            <?php	
                foreach( $this->plugins as $plugin ){
                    if( $plugin->editable ){			
                        jimport('joomla.filesystem.folder');
                        jimport('joomla.filesystem.file');
                        
                        $path		= JPATH_PLUGINS .DS. 'editors' .DS. 'jce' .DS. 'tiny_mce' .DS. 'plugins' .DS. $plugin->name;
                        $xmlPath 	= $path . DS . $plugin->name .'.xml';
                        $name 		= trim( $plugin->name ); 
                        $params 	= new JParameter( $this->group->params, $xmlPath );
						$params->addElementPath( JPATH_COMPONENT . DS . 'elements' );
                        
                        // Load Language for plugin
                        $lang =& JFactory::getLanguage();
                        $lang->load( 'com_jce_' . trim( $name ), JPATH_SITE );
                        
                        $display = in_array( $plugin->id, explode( ',', $this->group->plugins ) ) ? 'block' : 'none';
                        
                        if( $params->getNumParams('standard') || $params->getNumParams('defaults')|| $params->getNumParams('access') || $params->getNumParams('advanced') ) {
							$icon = $plugin->layout ? '<img style="vertical-align:middle;margin:0px 3px 3px; 0px;" src="../plugins/editors/jce/tiny_mce/plugins/'. $plugin->name .'/img/'. $plugin->layout .'.gif" alt="'. $plugin->title .'" />' : '';
                    ?>
                            <div id="plugin_params_<?php echo $plugin->id;?>" style="display:<?php echo $display;?>;">
                            <fieldset class="adminform">
                            <legend><?php echo $icon;?><?php echo JText::_( $plugin->title ); ?></legend>
                    <?php
                            jimport('joomla.html.pane');
							$pane =& JPane::getInstance('sliders', array( 'allowAllClose' => true ) );
                            echo $pane->startPane("group-pane-".$name);
                            if($params->getNumParams('standard')) {
                                if($output = $params->render('params', 'standard')){
                                    echo $pane->startPanel(JText :: _('STANDARD'), $name."-standard-page");
                                    echo $output;
                                    echo $pane->endPanel();
                                }
                            }
                            if($params->getNumParams('defaults')) {
                                if($output = $params->render('params', 'defaults')){
                                    echo $pane->startPanel(JText :: _('DEFAULTS'), $name."-defaults-page");
                                    echo $output;
                                    echo $pane->endPanel();
                                }
                            }
                            if($params->getNumParams('access')) {
                                if($output = $params->render('params', 'access')){
                                    echo $pane->startPanel(JText :: _('PERMISSIONS'), $name."-access-page");
                                    echo $output;
                                    echo $pane->endPanel();
                                }
                            }
                            if($params->getNumParams('advanced')) {
                                if($output = $params->render('params', 'advanced')){
                                    echo $pane->startPanel(JText :: _('ADVANCED'), $name."-advanced-page");
                                    echo $output;
                                    echo $pane->endPanel();
                                }
                            }
                            if( JFolder::exists( $path .DS. 'extensions' ) ){
                                $extensions = JCEGroupsHelper::getExtensions($plugin->name);
                                
                                foreach( $extensions as $extension ){
                                    // Load extension xml file
                                    $file = $path .DS. 'extensions' .DS. $extension->folder .DS. $extension->extension . '.xml';
                                    // Load extension language file
                                    $lang =& JFactory::getLanguage();
                                    $lang->load( 'com_jce_' . trim( $name ) . '_' . trim( $extension->extension ), JPATH_SITE );
                                    
                                    if( JFile::exists( $file ) ){
                                        $params = new JParameter( $this->group->params, $file );
                                        if($params->getNumParams()) {
                                            if($output = $params->render('params')){
                                                echo $pane->startPanel(JText :: _( $extension->name ), $extension->extension."-extension-page");
                                                echo $output;
                                                echo $pane->endPanel();
                                            }
                                        }
                                    }
                                }
                            }
                            echo $pane->endPane();
                    ?>
                            </fieldset>
                            </div>
                <?php
                        }else{
							echo JText :: _('NO PLUGINS IN LAYOUT');
						}
                    }
                }
            ?>
</div>
<?php echo $pane->endPane();?>
<div class="clr"></div>
	<input type="hidden" name="option" value="com_jce" />
	<input type="hidden" name="id" value="<?php echo $this->group->id; ?>" />
	<input type="hidden" name="cid[]" value="<?php echo $this->group->id; ?>" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="rows" value="" />
    <input type="hidden" name="type" value="group" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>