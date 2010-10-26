<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php  
JHTML::_('behavior.tooltip'); 
?>
<script type="text/javascript">
	function checkUser(s, v){
		var a = [];
		$each(s, function(n){
			a.push(n.value);
		});
		return a.contains(v);
	}
	function selectUsers(){
		var u = [], v, s, o, h;
		if(document.adminForm.boxchecked.value == 0){
			alert('Please select at least 1 user!');
			return false;
		}else{
			s = window.parent.document.getElementById('users').options;
			$ES('input[type=checkbox]').each(function(el){
				if(el.name == 'cid[]' && el.checked){
					v = el.value;
					h = $('username_' + v).innerHTML.trim();
					
					if(checkUser(s, v))
						return;
					
					
					o = new Option(h, v);
					s[s.length] = o;
				}
			});
			window.parent.document.getElementById('sbox-window').close();
		}
	}
</script>
<form action="index.php?option=com_jce&tmpl=component" method="post" name="adminForm">
	<fieldset>
        <div style="float: right">
            <button type="button" onclick="selectUsers();"><?php echo JText::_( 'Select' );?></button>
            <button type="button" onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_( 'Cancel' );?></button>
        </div>
        <div class="configuration" >
            <?php echo JText::_( 'Group Users' );?>
        </div>
    </fieldset>
    <table>
		<tr>
			<td width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php echo $this->lists['group'];?>
			</td>
		</tr>
	</table>

	<table class="adminlist" cellpadding="1">
		<thead>
			<tr>
				<th width="2%" class="title">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="3%" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort',   'Name', 'a.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="15%" class="title" >
					<?php echo JHTML::_('grid.sort',   'Username', 'a.username', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="15%" class="title">
					<?php echo JHTML::_('grid.sort',   'Type', 'groupname', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="15%" class="title">
					<?php echo JHTML::_('grid.sort',   'E-Mail', 'a.email', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th width="1%" class="title" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',   'ID', 'a.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$k = 0;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
				$row 		=& $this->items[$i];

				$task 		= $row->block ? 'unblock' : 'block';
				$alt 		= $row->block ? JText::_( 'Enabled' ) : JText::_( 'Blocked' );
				
				//$group 		= JCEGroupsHelper::getUserGroupFromId($row->id); 
				//$checked 	= $group->id ? '<img src="images/disabled.png"/>' : JHTML::_('grid.id', $i, $row->id );			
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td>
					<?php echo JHTML::_('grid.id', $i, $row->id );?>
				</td>
				<td>
					<?php echo $row->name; ?>
                </td>
				<td>
					<span id="username_<?php echo $row->id;?>"><?php echo $row->username; ?></span>
				</td>
				<td>
					<?php echo JText::_( $row->groupname ); ?>
				</td>
				<td><?php echo $row->email; ?></a></td>
				<td>
					<?php echo $row->id; ?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_jce" />
	<input type="hidden" name="task" value="addusers" />
    <input type="hidden" name="type" value="group" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>