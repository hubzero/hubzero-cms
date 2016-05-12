/**
 * @package     hubzero-cms
 * @file        plugins/members/profile/profile.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
		
		//terms of use
		HUB.Members.Profile.editTermsOfUse();
		
		//profile completeness meter
		HUB.Members.Profile.editCompletenessMeter();
		
		//edit profile section if we have section specified in window hash
		HUB.Members.Profile.editProfileSectionWithHash();
		
		//profile address section
		HUB.Members.Profile.addresses();
		HUB.Members.Profile.locateMe();
		HUB.Members.Profile.fetchOrcid();
	},
	
	//-------------------------------------------------------------
	
	edit: function()
	{
		var $ = this.jQuery;
		
		//hide edit and password links for when jquery is not enabled
		$("#page_options .edit, #page_options .password").parent("li").hide();
		
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
				
			$("body")
				.on("click", ".fancybox-wrap .section-edit-submit", function(event) {
					HUB.Members.Profile.editSubmitForm( $(this) );
					event.preventDefault();
				})
				.on("click", ".fancybox-wrap .usage-agreement-do-not-agree", function(event) {
					$("#usage-agreement-box").css("background", "#ffefef");
					$("#usage-agreement").hide();
					$("#usage-agreement-last-chance").show();
					$("#usage-agreement-buttons").hide();
					$("#usage-agreement-last-chance-buttons").show();
					$("#usage-agreement-popup input[name=declinetou]").attr("value", 1);
					$("#usage-agreement-popup input[name=usageAgreement]").attr("value", 0);
					event.preventDefault();
				})
				.on("click", ".fancybox-wrap .usage-agreement-back-to-agree", function(event) {
					$("#usage-agreement-box").css("background", "#FFF");
					$("#usage-agreement").show();
					$("#usage-agreement-last-chance").hide();
					$("#usage-agreement-buttons").show();
					$("#usage-agreement-last-chance-buttons").hide();
					$("#usage-agreement-popup input[name=declinetou]").attr("value", 0);
					$("#usage-agreement-popup input[name=usageAgreement]").attr("value", 1);
					event.preventDefault();
				})
				.on("click", ".fancybox-wrap .usage-agreement-dont-accept", function(event) {
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
		if (!$section.find(".section-edit a").hasClass('open'))
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
		var form = submit_button.parents("form"),
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
				console.log(data);
				//parse the returned json data
				var returned = jQuery.parseJSON(data);
				
				//remove saving indicator and enable save button
				//form.find(".section-edit-saving").remove();
				submit_button.attr("disabled", false);

				//if we successfully saved
				if(returned.success)
				{
					switch( profile_field )
					{
						case 'email':
						case 'usageAgreement':
							HUB.Members.Profile.editRedirect(window.location.href);
						break;
						
						default:
							HUB.Members.Profile.editReloadSections();
					}
				}
				else if(returned.loggedout)
				{
					HUB.Members.Profile.editRedirect("/");
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
		if (typeof(wykiwygs) === 'undefined') 
		{
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
			//reset wiki toolbar editor
			if (typeof(wyktoolbar) !== 'undefined') 
			{
				wyktoolbar   = [];
			}
			
			//reset wiki wysiwyg editor
			if (typeof(wykiwygs) !== 'undefined') 
			{
				wykiwygs   = [];
			}
			
			//call ajaxLoad which triggers re-apply
			jQuery(document).trigger('ajaxLoad');
		}
	},
	
	//-------------------------------------------------------------
	
	editInterestsAutocompleterReinstantiate: function()
	{
		if(HUB.Plugins != null)
		{
			if(HUB.Plugins.Autocomplete != null)
			{
				HUB.Plugins.Autocomplete.initialize();
			}
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
		
		//close any open lightboxes
		$.fancybox.close();
		
		//check to see if we are edit our profile or we were forced to fill in fields due to registration update
		if(window.location.pathname.match(/\/members\/\d+\/profile/g) || !$('.member-update-missing').length)
		{
			if (window.location.protocol + '//' + window.location.host + '/' == window.location.href)
			{
				HUB.Members.Profile.editRedirect(window.location.href);
				return;
			}
			
			//show updating overlay
			HUB.Members.Profile.editShowUpdatingOverlay(".member_profile");

			var url = $('#profile-page-content').attr('data-url');

			$(".member_profile").load(url + " #profile-page-content", function() {
				//reload page header in case we edited name
				$("#page_header").load(url +  " #page_header > *");
			
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
		}
		else
		{
			HUB.Members.Profile.editRedirect(window.location.href);
		}
	},

	//-------------------------------------------------------------

	editValidationHandling: function( form, returned_data, registration_field )
	{
		var $ = this.jQuery;

		var error = "",
			missing = returned_data._missing,
			invalid = returned_data._invalid;

		if (missing[registration_field] || invalid[registration_field]) 
		{
			if (missing[registration_field])
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
		var $ = this.jQuery,
			privacy = $("#profile-privacy");

		privacy.on('click', function(event){
			var pub = 1,
				id  = $(this).attr('data-id'),
				url = $(this).attr('href');

			if ($(this).hasClass("private")) {
				pub = 5;
			}

			var params = {
				'option': 'com_members',
				'id': id,
				'task': 'save',
				'profileaccess': pub,
				'field_to_check[]': 'profileaccess',
				'no_html': 1
			};

			$.post(url, params, function(data){ 
				var returned = jQuery.parseJSON(data);

				if (returned.success) {
					if (pub) {
						privacy
							.removeClass("private")
							.html("Public Profile :: " + privacy.attr('data-private'));

						$("body").find(".tooltip-text").html(privacy.attr('data-private'));
					} else {
						privacy
							.addClass("private")
							.html("Private Profile :: " + privacy.attr('data-public'));

						$("body").find(".tooltip-text").html(privacy.attr('data-public'));
					}
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
			var w = $identity.find("img").width() + 2;
			w = (w < 165) ? 165 : w;
			
			$change
				.css('width',  w)
				.attr("href", window.location.href.replace("profile","ajaxupload"))
				.appendTo($identity);
					
			//edit picture	
			$('.com_members')
				.on("click", "#page_identity_change", function(event) {
					HUB.Members.Profile.editProfilePicturePopup();
					event.preventDefault();
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

						// save profile picture updates
						$.post( form.attr("action"), form.serialize(), function(data){
							var save = jQuery.parseJSON(data);
							if(save.success)
							{
								// load exact page to get results new profile pic sources
								// not ideal way to do this, save should really return new profile pic
								$.get(window.location.href, function(data)
								{
									var full = $(data).find('.profile-pic.full').first().attr('src'),
										thumb = $(data).find('.profile-pic.thumb').first().attr('src');

									// update all full & thumb
									$('.profile-pic.full').attr('src', full + '?' + new Date().getTime());
									$('.profile-pic.thumb').attr('src', thumb + '?' + new Date().getTime());

									// close fancy box
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
			template: '<div class="qq-uploader">' + 
	                '<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
	                '<div class="qq-upload-button">Upload an Image</div>' +
	                '<ul class="qq-upload-list"></ul>' + 
	             '</div>',
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
						
						$("#picture-src").attr("src", upload.src + "?v=" + new Date().getTime());
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
	
	editTermsOfUse: function()
	{
		var $ = this.jQuery;
		
		if( $("#usage-agreement-popup").length )
		{
			$("#usage-agreement-popup").hide();
			
			$.fancybox({
				type:'inline',
				autoSize: false, 
				modal: true,
				width: 600,
				height: 'auto',
				content:$("#usage-agreement-popup"),
				beforeLoad: function() 
				{
					href = $("#usage-agreement-popup form").attr('action').replace("#", "");
					href += (href.indexOf('?') == -1) ? '?no_html=1' : '&no_html=1' ;
					$("#usage-agreement-popup form").attr('action', href);	
				}
			});
		}
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
	},
	
	addresses: function()
	{
		var $ = this.jQuery;
		
		//delete confirmation
		$('.com_members').on('click','.delete-address', function(event) {
			if (!confirm("Are you sure you want to delete this member Address?"))
			{
				event.preventDefault();
			}
		});
		
		//add/edit addresses
		if ($('.add-address, .edit-address').length) {
			$('.add-address, .edit-address').fancybox({
				type: 'ajax',
				width: 700,
				height: 'auto',
				autoSize: false,
				fitToView: false,  
				titleShow: false,
				tpl: {
					wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
				},
				beforeLoad: function() {
					href = $(this).attr('href');
					if (href.indexOf('?') == -1) {
						href += '?no_html=1';
					} else {
						href += '&no_html=1';
					}
					$(this).attr('href', href);
				},
				afterShow: function() {
					if ($('#hubForm-ajax')) {
						$('#hubForm-ajax').submit(function(e) {
							e.preventDefault();
							$.post($(this).attr('action'),$(this).serialize(), function(data) {
								$.fancybox.close();
								HUB.Members.Profile.editReloadSections();
							});
						});
					}
				}
			});
		}
	},
	
	locateMe: function()
	{
		var $ = this.jQuery;
		
		//locate me
		$('body').on('click', '#locate-me', function(event) {
			event.preventDefault();
			
			//make sure we have the ability
			if (!navigator.geolocation) 
			{
				alert('You browser is not capable of gettting you location.');
				return;
			}
				
			//use the browser geo location
			navigator.geolocation.getCurrentPosition(
				HUB.Members.Profile.locateMeGotLocation,
				HUB.Members.Profile.locateMeGotError,
				{
					enableHighAccuracy: true,
					timeout: 1000 * 5,
					maximumAge: 0
				}
			);
		});
	},
	
	locateMeGotLocation: function( location )
	{
		var $ = HUB.Members.Profile.jQuery;
		
		var latitude      = location.coords.latitude,
			longitude     = location.coords.longitude,
			reverseGeoUrl = 'https://maps.google.com/maps/api/geocode/json?sensor=true&latlng=' + latitude + ',' + longitude;
		
		var address_parts = [];
		
		$.getJSON(reverseGeoUrl, function(json){
			var result = json.results[0].address_components;
			
			for (var i=0, n=result.length; i<n; i++)
			{
				//do we have a street
				if (jQuery.inArray('street_number', result[i].types) > -1)
				{
					address_parts['address1'] = result[i].long_name;
				}
				
				//do we have a street
				if (jQuery.inArray('route', result[i].types) > -1)
				{
					address_parts['address1'] += ' ' + result[i].long_name;
				}
				
				//do we have a state / region
				if (jQuery.inArray('locality', result[i].types) > -1)
				{
					address_parts['city'] = result[i].long_name;
				}
				
				//do we have a state / region
				if (jQuery.inArray('administrative_area_level_1', result[i].types) > -1)
				{
					address_parts['region'] = result[i].long_name;
				}
				
				//do we have a postal code
				if (jQuery.inArray('postal_code', result[i].types) > -1)
				{
					address_parts['postal'] = result[i].long_name;
				}
				
				//do we have a country
				if (jQuery.inArray('country', result[i].types) > -1)
				{
					address_parts['country'] = result[i].long_name;
				}
			}
			
			//set values
			$('.member-address-form').find('#address1').val(address_parts['address1']);
			$('.member-address-form').find('#addressCity').val(address_parts['city']);
			$('.member-address-form').find('#addressRegion').val(address_parts['region']);
			$('.member-address-form').find('#addressPostal').val(address_parts['postal']);
			$('.member-address-form').find('#addressCountry').val(address_parts['country']);
			$('.member-address-form').find('#addressLatitude').val(latitude);
			$('.member-address-form').find('#addressLongitude').val(longitude);
		});
	},

	locateMeGotError: function( error )
	{
		var $ = HUB.Members.Profile.jQuery;

		alert('Geo Location Error: ' + error.message);
	},

	fetchOrcidRecords: function()
	{
		var $ = this.jQuery;

		var firstName = $('#first-name').val();
		var lastName  = $('#last-name').val();
		var email     = $('#email').val();

		if (!firstName && !lastName && !email) {
			alert('Please fill at least one of the fields.');
			return;
		}

		// return param: 1 means return ORCID to use to finish registration, assumes registration page
		// return param: 0 means do not return ORCID, assumes profile page
		var url = $('#base_uri').val() + '/index.php?option=com_members&controller=orcid&task=fetch&no_html=1&fname=' + firstName + '&lname=' + lastName + '&email=' + email + '&return=0';

		$.ajax({
			url: url,
			type: 'GET',
			success: function(data, status, jqXHR)
			{
				$('#section-orcid-results').html(jQuery.parseJSON(data));
			}
		});
	},

	fetchOrcid: function()
	{
		var $ = this.jQuery;

		$('body').on('click', '#get-orcid-results', function(event) {
			event.preventDefault();

			HUB.Members.Profile.fetchOrcidRecords();
		});
	},

	associateOrcid: function(parentField, orcid)
	{
		var url = $('#base_uri').val() + '/index.php?option=com_members&controller=orcid&task=associate&no_html=1&orcid=' + orcid;

		$.ajax({
			url: url,
			type: 'GET',
			success: function(data, status, jqXHR) {
				var status = jQuery.parseJSON(data);

				if (status) {
					//window.parent.document.getElementById('orcid').value = orcid;
					window.parent.jQuery.fancybox.close();
				}
			}
		});
	},

	createOrcid: function(fname, lname, email)
	{
		$.ajax({
			url: $('#base_uri').val() + '/index.php?option=com_members&controller=orcid&task=create&no_html=1&fname=' + fname + '&lname=' + lname + '&email=' + email,
			type: 'GET',
			success: function(data, status, jqXHR) {
				var response = jQuery.parseJSON(data);

				if (response.success) {
					if (response.orcid) {
						alert('Successful creation of your new ORCID. Claim the ORCID through the link sent to your email.');
						window.parent.document.getElementById('orcid').value = response.orcid;
						window.parent.jQuery.fancybox.close();
					} else {
						alert('ORCID service reported a successful creation but we failed to retrieve an ORCID. Please contact support.');
					}
				} else {
					if (response.message) {
						alert(response.message);
					} else {
						alert('Failed to create a new ORCID. Possible existence of an ORCID with the same email.');
					}
				}
			}
		});
	}
};

//-------------------------------------------------------------

jQuery(document).ready(function($){
	HUB.Members.Profile.initialize();

	// Iframe method
	if ($('#orcid-fetch').length) {
		$('#orcid-fetch').fancybox({
			type: 'iframe',   // change this to 'ajax' if you want to use AJAX
			width: 700,
			height: 'auto',
			autoSize: false,
			fitToView: false,
			titleShow: false,
			closeClick: false,
			helpers: { 
				overlay : {closeClick: false} // prevents closing when clicking OUTSIDE fancybox
			},
			tpl: {
				wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
			},
			beforeLoad: function() {
				href = $(this).attr('href');
				if (href.indexOf('?') == -1) {
						href += '?tmpl=component';    // Change to no_html=1 if using AJAX
				} else {
						href += '&tmpl=component';    // Change to no_html=1 if using AJAX
				}
				$(this).attr('href', href);
			},
			afterClose: function() {
				HUB.Members.Profile.editReloadSections();
			}
		});
	}
});
