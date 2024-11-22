/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// This retrieval of organization is specifically for the following modal:
// projects > publications > authors > edit author > organization text field
$(function(){
	if ($(".rorApiAvailable")[0]){
		$('[name="organization"]').autocomplete({
			source: function(req, resp){
				var rorURL = "index.php?option=com_members&controller=profiles&task=getOrganizations&term=" + $('[name="organization"]').val();

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
	}
	
	$('[name = "orcid"]').on('input', function(){
		var regex = /^[0-9]{4}-([0-9]{4}-){2}[0-9X]{4}/;
		var orcidInput = $('[name = "orcid"]').val();
		
		if (!regex.test(orcidInput))
		{
			$('#orcid-message').addClass("prompt");
			$('#orcid-message').text('*Invalid ORCID ID. Please enter the 16-digit ORCID ID');
			$('#orcid-message').show();
		}
		else
		{
			$('#orcid-message').removeClass("prompt");
			$('#orcid-message').text('*You have successfully entered a valid 16-digit ORCID ID');
			$('#orcid-message').show();
		}
	})
});