/**
 * @package     hubzero-cms
 * @file        plugins/members/profile/profile.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

if(!HUB.Members) {
	HUB.Members = {};
}

//-------------------------------------------------------------
//	Members Profile 
//-------------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Members.Profile = {
	jQuery: jq,
	
	initialize: function()
	{
		//enable edit mode
		HUB.Members.Profile.edit();
		
		//profile privacy actions
		HUB.Members.Profile.editPrivacy();
		
		//profile picture editor
		HUB.Members.Profile.editProfilePicture();
		
		//profile completeness meter
		HUB.Members.Profile.editCompletenessMeter();
		
		//edit profile section if we have section specified in window hash
		HUB.Members.Profile.editProfileSectionWithHash();
	},
	
	//-------------------------------------------------------------
	
	edit: function()
	{
		var $ = this.jQuery;
		
		//hide edit and password links for when jquery is not enabled
		$("#page_options .edit, #page_options .password").hide();
		
		//do we have the ability to edit
		if( $('.section-edit-container').length )
		{
			$(".section-edit a").show();
			
			$(".com_members")
				.on("mouseenter", "#profile li.section:not(.active)", function(event) {
					$(this).append("<div class=\"section-hover\" />");
				})
				.on("mouseleave", "#profile li.section", function(event) {
					$(this).children(".section-hover").remove(); 
				})
				.on("click", "#profile li.section .section-hover", function(event) {
					HUB.Members.Profile.editToggleSection( $(this) );
					event.preventDefault();
				})
				.on("click", ".edit-profile-section", function(event) {
					HUB.Members.Profile.editToggleSection( $(this) );
					event.preventDefault();
				})
				.on("click", ".section-edit-cancel", function(event) {
					HUB.Members.Profile.editToggleSection( $(this) );
					event.preventDefault();
				})
				.on("click", ".section-edit-submit", function(event) {
					HUB.Members.Profile.editSubmitForm( $(this) );
					event.preventDefault();
				});
		}
	},
	
	//-------------------------------------------------------------
	
	editToggleSection: function( trigger )
	{
		var $ = this.jQuery;
		
		var $section = trigger.parents("li"),
			section_classes = $section.attr("class").split(" ");
		
		//show edit or close link
		if($section.find(".section-edit a").html() == "Edit")
		{   
			$section.find(".section-edit a").addClass("open").html('&times;'); 
		}
		else
		{
			$section.find(".section-edit a").removeClass("open").html('Edit');
		}
			
		//hide all open sections
		$("#profile li:not(."+section_classes[0]+") .section-edit a").removeClass("open").html('Edit');
		$("#profile li:not(."+section_classes[0]+")").removeClass("active").find(".section-edit-container").slideUp();
		
		//remove hover div
		$section.find(".section-hover").remove();
		
		//slide open new section
		$section.toggleClass("active").find(".section-edit-container").slideToggle();
	},
	
	//-------------------------------------------------------------
	
	editSubmitForm: function( submit_button )
	{
		var $ = this.jQuery;
		
		//get the needed vars
		var form = submit_button.parent("form"),
			registration_field = form.attr("data-section-registation"),
			profile_field = form.attr("data-section-profile");

		//disable submit button and show saving graphic	
		submit_button.attr("disabled", true);
		//form.children(".section-edit-cancel").after("<div class=\"section-edit-saving\" />");

		//auto convert any wykiwygs editors before submitting
		HUB.Members.Profile.editBiographyConvert();

		//run ajax request
		$.ajax({
			type: 'POST',
			url: form.attr("action"),
			data: form.serialize(),
			success: function(data, status, xhr)
			{
				//parse the returned json data
				var returned = jQuery.parseJSON(data);
				
				//remove saving indicator and enable save button
				//form.find(".section-edit-saving").remove();
				submit_button.attr("disabled", false);

				//if we successfully saved
				if(returned.success)
				{   
					HUB.Members.Profile.editShowUpdatingOverlay(".member_profile");

					switch( profile_field )
					{
						case 'email': 	HUB.Members.Profile.editRedirect(window.location.href);		break;
						default: 		HUB.Members.Profile.editReloadSections();
					}
				}
				else
				{
					HUB.Members.Profile.editValidationHandling(form, returned, registration_field);
				}
			},
			error: function(xhr, status, error)
			{
				console.log("An error occured while trying to save your profile.");
			},
			complete: function(xhr, status) {}
		});
	},

	//-------------------------------------------------------------

	editBiographyConvert: function()
	{
		//if we have any active wykiwyg editors we want to auto-convert html to wiki before submitting
		if (typeof(wykiwygs) === 'undefined') {
			return;
		}
		if (wykiwygs.length) 
		{
			for (i=0; i<wykiwygs.length; i++)
			{
				wykiwygs[i].t.value = wykiwygs[i].makeWiki();
			}
		}
	},

	//-------------------------------------------------------------

	editBiographyEditorReinstantiate: function()
	{
		var $ = this.jQuery;
		
		if ($("#profile_bio").length)
		{
			if (typeof(wykiwygs) === 'undefined') 
			{
				if(HUB.Plugins.WikiEditorToolbar)
				{
					HUB.Plugins.WikiEditorToolbar.initialize();
				}
			}
			else
			{
				var edtr = new WYKIWYG.editor.edit('editor',{
					id: "profile_bio",
					controls: [
								'bold','italic','underline','strikethrough','|',
								'subscript','superscript','|',
								'orderedlist','unorderedlist','|',
								'outdent','indent','|',
								'unformat','|',
								'style','|',
								'hr','link','unlink'
							],
					footer: true,
					toggle: true,
					resize: true,
					xhtml: true,
					cssfile: '/plugins/hubzero/wikieditorwykiwyg/wikieditorwykiwyg.css'
				});
				wykiwygs.push(edtr);
			}
		}
	},
	
	//-------------------------------------------------------------
	
	editInterestsAutocompleterReinstantiate: function()
	{
		if(HUB.Plugins.Autocomplete)
		{
			HUB.Plugins.Autocomplete.initialize();
		}
	},

	//-------------------------------------------------------------

	editShowUpdatingOverlay: function( element )
	{
		var $ = this.jQuery;
		
		$(element).css("position","relative").append("<div class=\"edit-profile-overlay update\" />"); 

		var windowHeight = $(window).height(),
			windowScroll = $(document).scrollTop(),
			profilePosition = $(element).offset().top, 
			diff = ((windowHeight - profilePosition + windowScroll) / 2) - 64;

		$(".edit-profile-overlay").css("background-position", "50% "+diff+"px");
	},

	//-------------------------------------------------------------

	editRedirect: function( location )
	{              
		if(location != '')
		{
			window.location.href = location;
		}
	},

	//-------------------------------------------------------------

	editReloadSections: function()
	{
		var $ = this.jQuery;
		
		$(".member_profile").load(window.location.href + " #profile-page-content", function() {
			//reload page header in case we edited name
			$("#page_header").load(window.location.href +  " #page_header > *");
			
			//show edit links
			$(".section-edit a").show();
			
			//re-initalize autocompler for tags and wiki editor for bio
			HUB.Members.Profile.editInterestsAutocompleterReinstantiate();
			HUB.Members.Profile.editBiographyEditorReinstantiate();
			
			//update the complete ness meter
			var new_completeness = $("#profile-page-content #member-profile-completeness #meter-percent").attr("data-percent");
			$("#page_options #meter-percent").width( new_completeness + "%" );
			$("#page_options #meter-percent").attr("data-percent", new_completeness);
		});

	},

	//-------------------------------------------------------------

	editValidationHandling: function( form, returned_data, registration_field )
	{
		var $ = this.jQuery;
		
		var error = "",
			missing = returned_data._missing,
			invalid = returned_data._invalid;

		if(missing[registration_field] || invalid[registration_field]) 
		{     
			if(missing[registration_field])
			{
				error = '<p class="error no-margin-top"><strong>Missing Required Field:</strong> ' + missing[registration_field] + '</p>';
			}
			else if(invalid[registration_field])
			{
				error = '<p class="error no-margin-top"><strong>Validation Error:</strong> ' + invalid[registration_field] + '</p>';	
			}
			form.find(".section-edit-errors").html( error );
		}
	},
	
	//-------------------------------------------------------------
	
	editPrivacy: function()
	{
		var $ = this.jQuery;
		
		//show privacy button
		$("#profile-privacy").css('display','inline-block');
		
		$("#page_identity_link img").load(function(){
			var w = $("#page_identity_link img").outerWidth(true);
			w = (w > 150) ? w : 150;
			$("#page_identity").outerWidth(w + 2);
			$("#profile-privacy").outerWidth(w + 2);
		});
		
		
		$("#profile-privacy").on("click", function(event){
			var text = "",
				pub = 0,
				id = $(this).attr("data-uidnumber");
			
			if($(this).hasClass("private"))
			{
				text = "Public Profile. Set Private?";
				pub = 1;
			}
			else
			{
				text = "Private Profile. Set Public?";
				pub = 0;
			}
			
			var params = {
				'option': 'com_members',
				'id': id,
				'task': 'save',
				'profile[public]': pub,
				'field_to_check[]': 'profile[public]',
				'no_html': 1
			};
			
			$.post('index.php', params, function(data){ 
				var returned = jQuery.parseJSON(data);
				
				if(returned.success)
				{
					$("#profile-privacy")
						.toggleClass("private")
						.html(text);
					$("#page_header").load(window.location.href +  " #page_header > *");
				}
			});
			
			event.preventDefault();
		});
	},
	
	//-------------------------------------------------------------
	
	editProfilePicture: function()
	{
		var $ = this.jQuery;
		
		var $identity = $("#page_identity"),
		    $change = $("<a id=\"page_identity_change\"><span>Change Picture</span></a>");
			
		//if this is our profile otherwise dont do ot
		if( $(".section-edit a").length )
		{
			$identity.find("img").load(function(){
				$change
					.css('width', $identity.find("img").width())
					.attr("href", window.location.href.replace("profile","ajaxupload"))
					.appendTo($identity);

				//edit picture	
				$('.com_members')
					.on("mouseenter", "#page_identity", function(event) {
						$change.fadeIn("slow");
					})
					.on("mouseleave", "#page_identity", function(event) {
						$change.fadeOut("slow");
					})
					.on("click", "#page_identity_change", function(event) {
						HUB.Members.Profile.editProfilePicturePopup();
						event.preventDefault();
					});
			});	
		}
	},
	
	//-------------------------------------------------------------
	
	editProfilePicturePopup: function()
	{
		var $ = this.jQuery;
		
		$('#page_identity_change').fancybox({
			type: 'ajax',
			width: 500,
			height: 'auto',
			autoSize: false,
			fitToView: false,
			title: '',
			keys: { close: null },
			closeClick: false,
			beforeLoad: function() 
			{
				href = $(this).attr('href').replace("#", "");
				href += (href.indexOf('?') == -1) ? '?no_html=1' : '&no_html=1' ;
				$(this).attr('href', href);	
			},
			beforeShow: function()
			{
				HUB.Members.Profile.editProfilePictureUpload();
			},
			afterShow: function()
			{
				$("#ajax-upload-container")
					.on("click", "#remove-picture", function(event) {
						event.preventDefault();
						$(this).hide();
						$("#ajax-upload-right").find("table").hide();
						$("#ajax-upload-right").append("<p class=\"warning\" style=\"margin-top:0;\">You must save changes to remove your profile picture.</p>"); 
						
						$("#profile-picture").attr("value", "");
						$("#picture-src").attr("src", $("#picture-src").attr("data-default-pic"));
					})
					.on("click", ".section-edit-cancel", function(event) {
						event.preventDefault();
						$.fancybox.close(true);
					})
					.on("click", ".section-edit-submit", function(event) {
						event.preventDefault();
						var form = $("#ajax-upload-container").find("form");

						$.post( form.attr("action"), form.serialize(), function(data){
							var save = jQuery.parseJSON(data);
							if(save.success)
							{
								$.get(window.location.href, function(data) {
									var new_logo = $(data).find("#page_identity_link img").attr("src") + '?' + new Date().getTime();
									$("#page_identity_link img").attr("src", new_logo);
									$.fancybox.close();
								});
							}
						});
					});
			}
		});
	},
	
	//-------------------------------------------------------------
	
	editProfilePictureUpload: function()
	{
		var $ = this.jQuery;
		
		var uploader = new qq.FileUploader({
			element: $("#ajax-uploader")[0],
			action: $("#ajax-uploader").attr("data-action"),
			multiple: false,
			onSubmit: function(id, file)
			{
				$("#ajax-upload-left").append("<div id=\"ajax-upload-uploading\" />");
			},
			onComplete: function(id, file, response)
			{
				$("#ajax-upload-uploading").fadeOut("slow").remove();
				var url = $("#ajax-uploader").attr("data-action");
				url = url.replace("doajaxupload","getfileatts"); 
				
				$.post(url, {file:response.file, dir:response.directory}, function(data) {
					var upload = jQuery.parseJSON( data );
					if(upload)
					{
						$("#ajax-upload-right").find("table").show();
						$("#ajax-upload-right").find("p.warning").remove();
						
						$("#picture-src").attr("src", upload.src + "?" + new Date().getTime());
						$("#picture-name").html(upload.name);
						$("#picture-size").html(upload.size);
						$("#picture-width").html(upload.width);
						$("#picture-height").html(upload.height);
						$("#profile-picture").attr("value", upload.name); 
					}
				})
			}
		});
	},
	
	//-------------------------------------------------------------
	
	editCompletenessMeter: function()
	{
		var $ = this.jQuery;
		
		if( $("#member-profile-completeness").length )
		{
			$("#member-profile-completeness").appendTo( $("#page_options") ).show();
			var timeout = setTimeout(function() {
				$("#meter-percent").width( $("#meter-percent").attr("data-percent") + "%" );
			}, 1000);
		}
		
		if( $("#award-info").length )
		{
			$("#completeness-info").on("click", function(event) {
				$("#award-info").slideToggle();
			});
		}
	},
	
	//-------------------------------------------------------------
	
	editProfileSectionWithHash: function()
	{
		var $ = this.jQuery;
		
		var timeout = null,
			distance = 0,
			bottom = 0,
			window_bottom = 0,
			item = null,
			item_edit_btn = null,
			hash = document.location.hash.replace("#", "");
			
		//if we have a hash and we have an edit btn(on our profile)
		if(hash != "")
		{
			item = $("." + hash),
			item_edit_btn = item.find(".section-edit a");
			if(item_edit_btn.length)
			{
				//trigger edit button click
				item_edit_btn.trigger("click");
			
				//set timeout to allow section to open so we can capture height of section
				timeout = setTimeout(function() {
					window_bottom = $(window).innerHeight();
					bottom = item.offset().top + item.outerHeight(true);
				
					if(bottom > window_bottom)
					{   
						distance = bottom - window_bottom + 20;
						$("body").animate({ scrollTop: distance }, 1500);
					}
				}, 800);
			}
		}
	}
};

//-------------------------------------------------------------

jQuery(document).ready(function($){
	HUB.Members.Profile.initialize();
});