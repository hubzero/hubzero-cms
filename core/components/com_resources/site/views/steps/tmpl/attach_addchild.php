<div class="col span">
	<?php
		$baseUrl = '/resources/draft?controller=attachments&no_html=1';
		$actionUrl = $baseUrl . '&task=create';
	?>
	<input id="resource-finder" type="text" autocomplete="off" placeholder="Search Resources by ID or Title." data-script="<?php echo '/api/v1.1' . Route::url('index.php?option=com_resources&task=autocomplete');?>" />
	<a href="<?php echo $actionUrl;?>" class="btn" id="add-child" data-pid="<?php echo $this->id;?>" data-childid="" disabled>Add</a>
</div>
<script type="text/javascript">
$(function(){
	var autocompleteSrc = $('#resource-finder').data('script');
	var iframe = $('#attaches');
	var processing = $('<div class="processing-indicator"></div>');
	iframe.attr('src', iframe.attr('src') + '&hideform=1');
	iframe.parent().append(processing);
	processing.show();
	$('#link-adder').show();
	$('#resource-finder').autocomplete({
		open: function(event, ui){
			$('#add-child').attr('disabled', true);
			$('#add-child').removeData('childid');
		},
		source: function(request, response){
			var existingCids = [];
			iframe.contents().find('span[data-id]').each(function(index){
				existingCids.push($(this).data('id'));
			});
			$.ajax({
				url: autocompleteSrc,
				data: {
					search: request.term,
					existingCids: existingCids
				},
				success: function(data){
					response(data.records);
				}
			});
		},
		select: function(event, ui){
			$('#resource-finder').val(ui.item.title);
			$('#add-child').attr("disabled", false);
			$('#add-child').attr("data-childid", ui.item.id);
			return false;
		},
		create: function() {
			$(this).data('ui-autocomplete')._renderItem = function(ul, item){
				return $('<li>')
					.append(item.title)
					.appendTo(ul);
			}
			$(this).data('ui-autocomplete')._renderMenu = function(ul, items){
				var that = this;
				ul.addClass('autocomplete-list');
				$.each(items, function (index, item){
					that._renderItemData(ul, item);
				});
			}
			$(this).data('ui-autocomplete')._resizeMenu = function(){
				var searchWidth = $('#resource-finder').width();
				this.menu.element.outerWidth(searchWidth);
			}
		}
	});
	
	$('#add-child').on('click', function(e){
		e.preventDefault();
		processing.show();
		$(this).attr('disabled', true);
		var data = {
			pid: $(this).data('pid'),
			childid: $(this).data('childid')
		};
		var url = $(this).attr('href');
		$.ajax({
			url: url,
			data: data,
			success: function(data){
				iframe.attr('src', iframe.attr('src'));
				$('#resource-finder').val('');
				$(iframe).ready(function(){
					processing.hide();
				});
			}
		});

	});
	processing.hide();
});
</script>
