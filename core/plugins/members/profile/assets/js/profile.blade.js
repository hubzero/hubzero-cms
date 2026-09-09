/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 *
 * Member Profile plugin — daisyUI mode JS.
 * Replaces fancybox modals with native <dialog> elements.
 */

if (!HUB) {
	var HUB = {};
}

if (!HUB.Members) {
	HUB.Members = {};
}

if (!jq) {
	var jq = $;
}

HUB.Members.Profile = {
	jQuery: jq,

	initialize: function () {
		HUB.Members.Profile.edit();
		HUB.Members.Profile.editPrivacy();
		HUB.Members.Profile.editProfilePicture();
		HUB.Members.Profile.editTermsOfUse();
		HUB.Members.Profile.editCompletenessMeter();
		HUB.Members.Profile.editProfileSectionWithHash();
		HUB.Members.Profile.addresses();
		HUB.Members.Profile.locateMe();
	},

	// ── Inline edit toggling ─────────────────────────────

	edit: function () {
		var $ = this.jQuery;

		$("#page_options .edit, #page_options .password").parent("li").hide();

		if ($('.section-edit-container').length) {
			$(".section-edit a").show();

			$(".com_members")
				.on("mouseenter", "#profile li.section:not(.active)", function (event) {
					$(this).append("<div class=\"section-hover\" />");
				})
				.on("mouseleave", "#profile li.section", function (event) {
					$(this).children(".section-hover").remove();
				})
				.on("click", "#profile li.section .section-hover", function (event) {
					HUB.Members.Profile.editToggleSection($(this));
					event.preventDefault();
				})
				.on("click", ".edit-profile-section", function (event) {
					HUB.Members.Profile.editToggleSection($(this));
					event.preventDefault();
				})
				.on("click", ".section-edit-cancel", function (event) {
					HUB.Members.Profile.editToggleSection($(this));
					event.preventDefault();
				})
				.on("click", ".section-edit-submit", function (event) {
					HUB.Members.Profile.editSubmitForm($(this));
					event.preventDefault();
				});

			// Handle submit/cancel inside dialogs
			$("body")
				.on("click", "dialog .section-edit-submit", function (event) {
					HUB.Members.Profile.editSubmitForm($(this));
					event.preventDefault();
				})
				.on("click", "dialog .usage-agreement-do-not-agree", function (event) {
					$("#usage-agreement-box").css("background",
						"color-mix(in srgb, var(--color-error) 10%, var(--color-base-100))");
					$("#usage-agreement").hide();
					$("#usage-agreement-last-chance").show();
					$("#usage-agreement-buttons").hide();
					$("#usage-agreement-last-chance-buttons").show();
					$("#usage-agreement-popup input[name=declinetou]").attr("value", 1);
					$("#usage-agreement-popup input[name=usageAgreement]").attr("value", 0);
					event.preventDefault();
				})
				.on("click", "dialog .usage-agreement-back-to-agree", function (event) {
					$("#usage-agreement-box").css("background", "");
					$("#usage-agreement").show();
					$("#usage-agreement-last-chance").hide();
					$("#usage-agreement-buttons").show();
					$("#usage-agreement-last-chance-buttons").hide();
					$("#usage-agreement-popup input[name=declinetou]").attr("value", 0);
					$("#usage-agreement-popup input[name=usageAgreement]").attr("value", 1);
					event.preventDefault();
				})
				.on("click", "dialog .usage-agreement-dont-accept", function (event) {
					HUB.Members.Profile.editSubmitForm($(this));
					event.preventDefault();
				});
		}
	},

	editToggleSection: function (trigger) {
		var $ = this.jQuery;

		var $section = trigger.parents("li"),
			section_classes = $section.attr("class").split(" ");

		if (!$section.find(".section-edit a").hasClass('open')) {
			$section.find(".section-edit a").addClass("open").html('&times;');
		} else {
			$section.find(".section-edit a").removeClass("open").html('Edit');
		}

		$("#profile li:not(." + section_classes[0] + ") .section-edit a").removeClass("open").html('Edit');
		$("#profile li:not(." + section_classes[0] + ")").removeClass("active").find(".section-edit-container").slideUp();

		$section.find(".section-hover").remove();
		$section.toggleClass("active").find(".section-edit-container").slideToggle();
	},

	// ── AJAX form submit ─────────────────────────────────

	editSubmitForm: function (submit_button) {
		var $ = this.jQuery;

		var form = submit_button.parents("form"),
			registration_field = form.attr("data-section-registration"),
			profile_field = form.attr("data-section-profile");

		submit_button.attr("disabled", true);

		HUB.Members.Profile.editBiographyConvert();

		$.ajax({
			type: 'POST',
			url: form.attr("action"),
			data: form.serialize(),
			success: function (data, status, xhr) {
				var returned = JSON.parse(data);
				submit_button.attr("disabled", false);

				if (returned.success) {
					switch (profile_field) {
						case 'email':
						case 'usageAgreement':
							HUB.Members.Profile.editRedirect(window.location.href);
							break;
						default:
							HUB.Members.Profile.editReloadSections();
					}
				} else if (returned.loggedout) {
					HUB.Members.Profile.editRedirect("/");
				} else {
					HUB.Members.Profile.editValidationHandling(form, returned, registration_field);
				}
			},
			error: function (xhr, status, error) {
				console.log("An error occurred while trying to save your profile.");
			}
		});
	},

	editBiographyConvert: function () {
		if (typeof (wykiwygs) === 'undefined') {
			return;
		}
		if (wykiwygs.length) {
			for (var i = 0; i < wykiwygs.length; i++) {
				wykiwygs[i].t.value = wykiwygs[i].makeWiki();
			}
		}
	},

	editBiographyEditorReinstantiate: function () {
		var $ = this.jQuery;

		if ($("#profile_bio").length) {
			if (typeof (wyktoolbar) !== 'undefined') {
				wyktoolbar = [];
			}
			if (typeof (wykiwygs) !== 'undefined') {
				wykiwygs = [];
			}
		}
	},

	editInterestsAutocompleterReinstantiate: function () {
		if (HUB.Plugins != null) {
			if (HUB.Plugins.Autocomplete != null) {
				HUB.Plugins.Autocomplete.initialize();
			}
		}
	},

	editShowUpdatingOverlay: function (element) {
		var $ = this.jQuery;
		$(element).css("position", "relative").append("<div class=\"edit-profile-overlay update\" />");
	},

	editRedirect: function (location) {
		if (location != '') {
			window.location.href = location;
		}
	},

	editReloadSections: function () {
		var $ = this.jQuery;

		// Close any open dialogs
		document.querySelectorAll('dialog[open]').forEach(function (d) { d.close(); });

		if (window.location.pathname.match(/\/members\/\d+\/profile/g) || !$('.member-update-missing').length) {
			if (window.location.protocol + '//' + window.location.host + '/' == window.location.href) {
				HUB.Members.Profile.editRedirect(window.location.href);
				return;
			}

			HUB.Members.Profile.editShowUpdatingOverlay(".member_profile");
			var url = $('#profile-page-content').attr('data-url');

			$(".member_profile").load(url + " #profile-page-content", function () {
				$("#page_header").load(url + " #page_header > *");
				$(".section-edit a").show();
				HUB.Members.Profile.editInterestsAutocompleterReinstantiate();
				HUB.Members.Profile.editBiographyEditorReinstantiate();
				jQuery(document).trigger('ajaxLoad');

				var new_completeness = $("#profile-page-content #member-profile-completeness #meter-percent").attr("data-percent");
				$("#page_options #meter-percent").width(new_completeness + "%");
				$("#page_options #meter-percent").attr("data-percent", new_completeness);
			});
		} else {
			HUB.Members.Profile.editRedirect(window.location.href);
		}
	},

	editValidationHandling: function (form, returned_data, registration_field) {
		var $ = this.jQuery;

		var error = "",
			missing = returned_data._missing,
			invalid = returned_data._invalid;

		if (missing[registration_field] || invalid[registration_field]) {
			if (missing[registration_field]) {
				error = '<p class="error no-margin-top"><strong>Missing Required Field:</strong> ' + missing[registration_field] + '</p>';
			} else if (invalid[registration_field]) {
				error = '<p class="error no-margin-top"><strong>Validation Error:</strong> ' + invalid[registration_field] + '</p>';
			}
			form.find(".section-edit-errors").html(error);
		}
	},

	// ── Privacy toggle ───────────────────────────────────

	editPrivacy: function () {
		var $ = this.jQuery,
			privacy = $("#profile-privacy");

		privacy.on('click', function (event) {
			var pub = 1,
				id = $(this).attr('data-id'),
				url = $(this).attr('href');

			if (!$(this).hasClass("private")) {
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

			$.post(url, params, function (data) {
				var returned = JSON.parse(data);
				if (returned.success) {
					if (pub == 1) {
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

	// ── Profile picture (native <dialog>) ────────────────

	editProfilePicture: function () {
		var $ = this.jQuery;

		var $identity = $("#page_identity");

		if ($(".section-edit a").length) {
			var w = $identity.find("img").width() + 2;
			w = (w < 165) ? 165 : w;
			var ajaxuploadurl = window.location.href.replace("profile", "") + "/ajaxupload";
			var $change = $('<a id="page_identity_change" data-upload-url="' + ajaxuploadurl + '"><span>Change Picture</span></a>');
			$change
				.css('width', w)
				.appendTo($identity);

			$('.com_members')
				.on("click", "#page_identity_change", function (event) {
					HUB.Members.Profile.editProfilePictureDialog();
					event.preventDefault();
				});
		}
	},

	editProfilePictureDialog: function () {
		var $ = this.jQuery;
		var dialog = document.getElementById('profile-picture-dialog');
		if (!dialog) return;

		var uploadUrl = $('#page_identity_change').attr('data-upload-url');
		var url = uploadUrl + (uploadUrl.indexOf('?') == -1 ? '?no_html=1' : '&no_html=1');

		// Load content via AJAX
		dialog.querySelector('.dialog-body').innerHTML = '<div style="text-align:center;padding:2rem;">Loading...</div>';
		dialog.showModal();

		$.get(url, function (html) {
			dialog.querySelector('.dialog-body').innerHTML = html;
			HUB.Members.Profile.editProfilePictureUpload();

			// Wire up dialog buttons
			$(dialog)
				.off('click.profile-pic')
				.on("click.profile-pic", "#remove-picture", function (event) {
					event.preventDefault();
					$.get($(this).attr('href'), function (data) {});
					$(this).hide();
					$("#picture-src").attr("src", $("#picture-src").attr("data-default-pic"));
					$.get(window.location.href, function (data) {
						var full = $(data).find('.profile-pic.full').first().attr('src'),
							thumb = $(data).find('.profile-pic.thumb').first().attr('src');
						$('.profile-pic.full').attr('src', full + '?' + new Date().getTime());
						$('.profile-pic.thumb').attr('src', thumb + '?' + new Date().getTime());
						dialog.close();
					});
				})
				.on("click.profile-pic", ".section-edit-cancel", function (event) {
					event.preventDefault();
					dialog.close();
				})
				.on("click.profile-pic", ".section-edit-submit", function (event) {
					event.preventDefault();
					var form = $("#ajax-upload-container").find("form");
					$.post(form.attr("action"), form.serialize(), function (data) {
						var save = JSON.parse(data);
						if (save.success) {
							$.get(window.location.href, function (data) {
								var full = $(data).find('.profile-pic.full').first().attr('src'),
									thumb = $(data).find('.profile-pic.thumb').first().attr('src');
								$('.profile-pic.full').attr('src', full + '?' + new Date().getTime());
								$('.profile-pic.thumb').attr('src', thumb + '?' + new Date().getTime());
								dialog.close();
							});
						}
					});
				});
		});

		// Close on backdrop click
		dialog.addEventListener('click', function (e) {
			if (e.target === dialog) {
				dialog.close();
			}
		});

		// Close button
		var closeBtn = dialog.querySelector('.dialog-close');
		if (closeBtn) {
			closeBtn.addEventListener('click', function () {
				dialog.close();
			});
		}
	},

	editProfilePictureUpload: function () {
		var $ = this.jQuery;

		if (typeof qq === 'undefined' || !$("#ajax-uploader").length) return;

		var uploader = new qq.FileUploader({
			element: $("#ajax-uploader")[0],
			action: $("#ajax-uploader").attr("data-action"),
			multiple: false,
			template: '<div class="qq-uploader">' +
				'<div class="qq-upload-button"><span>Upload an Image</span></div>' +
				'<div class="qq-upload-drop-area"><span>Upload an Image</span></div>' +
				'<ul class="qq-upload-list"></ul>' +
				'</div>',
			onSubmit: function (id, file) {
				$("#ajax-upload-left").append("<div id=\"ajax-upload-uploading\" />");
			},
			onComplete: function (id, file, response) {
				$("#ajax-upload-uploading").fadeOut("slow").remove();
				var url = $("#ajax-uploader").attr("data-action");
				url = url.replace("doajaxupload", "getfileatts");

				$.post(url, { file: response.file, dir: response.directory }, function (data) {
					var upload = JSON.parse(data);
					if (upload) {
						$("#ajax-upload-right").find("p.warning").remove();
						$("#picture-src").attr("src", upload.src + "?v=" + new Date().getTime());

						$.get(window.location.href, function (data) {
							var full = $(data).find('.profile-pic.full').first().attr('src'),
								thumb = $(data).find('.profile-pic.thumb').first().attr('src');
							$('.profile-pic.full').attr('src', full + '?' + new Date().getTime());
							$('.profile-pic.thumb').attr('src', thumb + '?' + new Date().getTime());
							var dialog = document.getElementById('profile-picture-dialog');
							if (dialog) dialog.close();
						});
					}
				});
			}
		});
	},

	// ── Terms of Use (native <dialog>) ───────────────────

	editTermsOfUse: function () {
		var $ = this.jQuery;

		if ($("#usage-agreement-popup").length) {
			var dialog = document.getElementById('usage-agreement-dialog');
			if (!dialog) return;

			var form = $("#usage-agreement-popup form");
			var href = form.attr('action').replace("#", "");
			href += (href.indexOf('?') == -1) ? '?no_html=1' : '&no_html=1';
			form.attr('action', href);

			dialog.showModal();
		}
	},

	// ── Completeness meter ───────────────────────────────

	editCompletenessMeter: function () {
		var $ = this.jQuery;

		if ($("#member-profile-completeness").length) {
			$("#member-profile-completeness").appendTo($("#page_options")).show();
			setTimeout(function () {
				$("#meter-percent").width($("#meter-percent").attr("data-percent") + "%");
			}, 1000);
		}

		if ($("#award-info").length) {
			$("#completeness-info").on("click", function (event) {
				$("#award-info").slideToggle();
			});
		}
	},

	// ── Hash-based section editing ───────────────────────

	editProfileSectionWithHash: function () {
		var $ = this.jQuery;

		var hash = document.location.hash.replace("#", "");

		if (hash != "") {
			var item = $("." + hash),
				item_edit_btn = item.find(".section-edit a");
			if (item_edit_btn.length) {
				item_edit_btn.trigger("click");
				setTimeout(function () {
					var window_bottom = $(window).innerHeight();
					var bottom = item.offset().top + item.outerHeight(true);
					if (bottom > window_bottom) {
						var distance = bottom - window_bottom + 20;
						$("body").animate({ scrollTop: distance }, 1500);
					}
				}, 800);
			}
		}
	},

	// ── Addresses (native <dialog>) ──────────────────────

	addresses: function () {
		var $ = this.jQuery;

		$('.com_members').on('click', '.delete-address', function (event) {
			if (!confirm("Are you sure you want to delete this member address?")) {
				event.preventDefault();
			}
		});

		if ($('.add-address, .edit-address').length) {
			$('.com_members').on('click', '.add-address, .edit-address', function (event) {
				event.preventDefault();

				var href = $(this).attr('href');
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}

				var dialog = document.getElementById('address-edit-dialog');
				if (!dialog) return;

				dialog.querySelector('.dialog-body').innerHTML = '<div style="text-align:center;padding:2rem;">Loading...</div>';
				dialog.showModal();

				$.get(href, function (html) {
					dialog.querySelector('.dialog-body').innerHTML = html;

					var form = $(dialog).find('form');
					if (form.length) {
						form.on('submit', function (e) {
							e.preventDefault();
							$.post($(this).attr('action'), $(this).serialize(), function (data) {
								dialog.close();
								HUB.Members.Profile.editReloadSections();
							});
						});
					}
				});

				dialog.addEventListener('click', function (e) {
					if (e.target === dialog) {
						dialog.close();
					}
				});

				var closeBtn = dialog.querySelector('.dialog-close');
				if (closeBtn) {
					closeBtn.addEventListener('click', function () {
						dialog.close();
					});
				}
			});
		}
	},

	// ── Geolocation ──────────────────────────────────────

	locateMe: function () {
		var $ = this.jQuery;

		$('body').on('click', '#locate-me', function (event) {
			event.preventDefault();

			if (!navigator.geolocation) {
				alert('Your browser is not capable of getting your location.');
				return;
			}

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

	locateMeGotLocation: function (location) {
		var $ = HUB.Members.Profile.jQuery;

		var latitude = location.coords.latitude,
			longitude = location.coords.longitude,
			reverseGeoUrl = 'https://maps.google.com/maps/api/geocode/json?sensor=true&latlng=' + latitude + ',' + longitude;

		var address_parts = [];

		$.getJSON(reverseGeoUrl, function (json) {
			var result = json.results[0].address_components;

			for (var i = 0, n = result.length; i < n; i++) {
				if (jQuery.inArray('street_number', result[i].types) > -1) {
					address_parts['address1'] = result[i].long_name;
				}
				if (jQuery.inArray('route', result[i].types) > -1) {
					address_parts['address1'] += ' ' + result[i].long_name;
				}
				if (jQuery.inArray('locality', result[i].types) > -1) {
					address_parts['city'] = result[i].long_name;
				}
				if (jQuery.inArray('administrative_area_level_1', result[i].types) > -1) {
					address_parts['region'] = result[i].long_name;
				}
				if (jQuery.inArray('postal_code', result[i].types) > -1) {
					address_parts['postal'] = result[i].long_name;
				}
				if (jQuery.inArray('country', result[i].types) > -1) {
					address_parts['country'] = result[i].long_name;
				}
			}

			$('.member-address-form').find('#address1').val(address_parts['address1']);
			$('.member-address-form').find('#addressCity').val(address_parts['city']);
			$('.member-address-form').find('#addressRegion').val(address_parts['region']);
			$('.member-address-form').find('#addressPostal').val(address_parts['postal']);
			$('.member-address-form').find('#addressCountry').val(address_parts['country']);
			$('.member-address-form').find('#addressLatitude').val(latitude);
			$('.member-address-form').find('#addressLongitude').val(longitude);
		});
	},

	locateMeGotError: function (error) {
		alert('Geo Location Error: ' + error.message);
	}
};

jQuery(document).ready(function ($) {
	HUB.Members.Profile.initialize();
});
