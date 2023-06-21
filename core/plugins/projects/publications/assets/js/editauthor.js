/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$(function(){
	$('[name="organization"]').autocomplete({
		source: function(req, resp){
			var rorURL = "index.php?option=com_members&controller=profiles&task=getOrganizations&term=";

			var terms = $('[name="organization"]').val();

			if (terms.indexOf(" "))
			{
				rorURL = rorURL + terms.split(" ").join("+");
			}
			else
			{
				rorURL = rorURL + terms;
			}

			$.ajax({
				url: rorURL,
				data: null,
				dataType: "json",
				success:function(result){
					resp(result);
				},
				error:function(jqXHR, textStatus, errorThrown){
					console.log(textStatus);
					console.log(errorThrown);
					console.log(jqXHR.responseText);
				}
			});
		},

		appendTo: '#autocomplete-organization',
	});
});