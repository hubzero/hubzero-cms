/**
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Scripts for the citations component
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Citations = {
	jQuery: jq,
	
	initialize: function()
	{
		var $ = this.jQuery;

		//citation import functionality
		HUB.Citations.bulkimport();
		
		//citation download functionality
		HUB.Citations.download();
		
		//citation add functionality
		HUB.Citations.singleimport();
		
		// add rollovers
		HUB.Citations.rollovers();
		
		// single citation
		HUB.Citations.singleCitation();
		//HUB.Citations.singleCitationTabs();
		
		// Checks the boxes of the already selected citations when the page is done loading
		var checkList = $("input[name=idlist]").val();
		if(checkList) {
			var checkArray = checkList.split("-");

			$(".download-marker").each(function() {
				var value = $(this).val();
				var i = 0;
				for(i = 0; i < checkArray.length; i++) {
					// If the value is in the array check the checkbox
					if(value == checkArray[i]) {
						$(this).prop("checked", true);
					}
				}
			});

			// If they're all checked, check the allchecked checkbox
			if (!$('input.download-marker[type=checkbox]:not(:checked)').length) {
				$(".checkall-download").prop("checked", true);
			}
		}
	},
	
	//-----
	
	addRow: function(id) {
		var tbody = document.getElementById(id).tBodies[0];
		var counter = tbody.rows.length;
		var newNode = tbody.rows[0].cloneNode(true);
		
		var newField = newNode.childNodes;
		for (var i=0;i<newField.length;i++)
		{
			var inputs = newField[i].childNodes;
			for (var k=0;k<inputs.length;k++)
			{
				var theName = inputs[k].name;
				if (theName) {
					tokens = theName.split('[');
					n = tokens[2];
					inputs[k].name = id + '[' + counter + ']['+ n;
				}
				//var n = id + '[' + counter + '][type]';
				//var z = id + '[' + counter + '][status]';
				//if (inputs[k].value && inputs[k].name != n && inputs[k].name != z) {
				if (inputs[k].value) {
					inputs[k].value = '';
				}
			}
		}
		tbody.appendChild(newNode);
		return false;
	},
	
	//-----
	
	bulkimport: function()
	{
		var $ = this.jQuery;
		
		//click to show citation details
		$(".upload-list .citation-title").each(function(i) {
			$(this).on("click", function(e) {
				var parent = $(this).parents("tr");
				var table = $(this).parents("tr").find(".citation-details");
				var show_more = $(this).parents("tr").find(".click-more");

				if(show_more.text() == "← Click to show citation details") {
					show_more.text("← Click to hide citation details")
				} else {
					show_more.text("← Click to show citation details")
				}

				table.toggle();
				parent.toggleClass("active");
			});
		});

		//check all
		$(".checkall").on("click", function(e) {
			var tbody = $(this).parents("table.upload-list").children("tbody");
			tbody.find("input[type=checkbox]").attr("checked", this.checked);
		});

		//uncheck/check checkall depending if all sub checkboxes are checked
		$(".check-single").on("click", function(e) {
			var checkboxes = $(this).parents("table.upload-list tbody").find("input[type=checkbox]");
			var allchecked = (checkboxes.filter(":not(:checked)").length > 0) ? false : true;
			$(this).parents("table.upload-list").find(".checkall").attr("checked", allchecked);
		});


		$(".citation_require_attention_option").on("click", function(e) {
			var action = this.value;
			var parent = $(this).parents(".citation-details").find("tbody");

			switch(action)
			{
				case "overwrite":
					parent.find(".new").addClass("insert").removeClass("delete");
					parent.find(".old").addClass("delete");
					break;
				case "discard":
					parent.find(".new").removeClass("insert").addClass("delete");
					parent.find(".old").removeClass("delete").removeClass("insert");
					break;
				case "both":
					parent.find(".new").addClass("insert").removeClass("delete");
					parent.find(".old").addClass("insert").removeClass("delete");
					break;
			}
		});
	},
	
	//-----
	
	download: function()
	{
		var $ = this.jQuery;
		
		//check all
		$(".checkall-download").on("click", function(e) {
			var tbody = $(this).parents("table.citations").children("tbody");
			tbody.find("input[type=checkbox]").attr("checked", this.checked);

			// True if all box is being checked, false if it's being unchecked
			var isChecked = $('.checkall-download').attr('checked')?true : false;

			// currentList is a string of ids delimited by "-" of
			// citations the user has checked
			var currentList = $("input[name=idlist]").val();
			// Since the array is actually a string delimited by "-" it needs split up
			var idArray = currentList.split("-");

			// If the box is being checked add the id for each citation on the page to the id list
			// if it's not already there
			if(isChecked) {
				$(".download-marker").each(function() {
					var value = $(this).val();
					var i = 0;
					for(i = 0; i < idArray.length; i++) {
						// If the current id is found in the list, don't add it
						if(idArray[i] == value) {
							break;
						}
					}

					if(currentList != "") {
						currentList = currentList + "-" + value;
					} else {
						currentList = value;
					}
				});

				// Update the list stored on the page
				$("input[name=idlist]").val(currentList);
			}
			// If the box is being unchecked remove the id for each citation on the page from the list
			else {
				$(".download-marker").each(function() {
					var value = $(this).val();
					var i = 0;
					for(i = 0; i < idArray.length; i++) {
						// If the current id is found in the list, remove it
						if(idArray[i] == value) {
							idArray.splice(i, 1);
						}
					}
				});
				currentList = idArray.join("-");
				$("input[name=idlist]").val(currentList);
			}

			// Sends the new id list to the server
			$.ajax({
				url: 'index.php?option=com_citations&task=browse',
				method:'post',
				data: {'idlist': $("input[name=idlist]").val()}
			});
		});

		//uncheck/check checkall depending if all sub checkboxes are checked
		$(".download-marker").on("click", function(e) {
			var checkboxes = $(this).parents("table.citations tbody").find("input[type=checkbox]");
			//var allchecked = (checkboxes.filter(":not(:checked)").length > 0) ? false : true;
			//$(this).parents("table.citations").find(".checkall-download").attr("checked", allchecked);

			// value is the id associated checkbox clicked on
			var value = $(this).val();
			// currentList is a string of ids delimited by "-" of
			// citations the user has checked
			// input[name=idlist] references the hidden input  that
			// manages the boxes that have been selected
			var currentList = $("input[name=idlist]").val();

			// If there are items in the current list
			if(currentList != '') {
				// Since the array is actually a string delimited by "-" it needs split up
				var idArray = currentList.split("-");
				var i = 0;
				// Check to make sure the item doesn't need removed from the array
				// by seeing if the id selected is already in the array
				for(i = 0; i < idArray.length; i++) {
				// If it is in the array remove it from the array
				// and put the new array back in the input value
					if(idArray[i] == value) {
						idArray.splice(i, 1);
						currentList = idArray.join("-");
						$("input[name=idlist]").val(currentList);

						// Sends the new id list to the server
						$.ajax({
							url: 'index.php?option=com_citations&task=browse',
							method:'post',
							data: {'idlist': $("input[name=idlist]").val()}
						});

						// All the checkboxes can't be checked, so uncheck the select all checkbox
						$(".checkall-download").prop("checked", false);

						return;
					}
				}

				currentList = currentList + "-" + value;
			}
			// If the currentList is empty, then there can't be any other checked boxes
			// so add the value to the array of selected items
			else {
				currentList = value;
			}

			// Put the new list back in the input value
			$("input[name=idlist]").val(currentList);

			// Sends the new id list to the server
			$.ajax({
				url: 'index.php?option=com_citations&task=browse',
				method:'post',
				data: {'idlist': $("input[name=idlist]").val()}
			});

			// If they're all checked, check the allchecked checkbox
			if (!$('input.download-marker[type=checkbox]:not(:checked)').length) {
				$(".checkall-download").prop("checked", true);
			}
		});

		//download
		$(".download-endnote, .download-bibtex").on("click", function(e) {
			var markers = countDownloadMarkers();
			if(markers < 1) {
				alert("Select at least one citation to export");
				e.preventDefault();
			}

			if(!$("#download-batch-input").length)
			{
				$("#citeform").append("<input type=\"hidden\" name=\"task\" value=\"downloadbatch\" id=\"download-batch-input\" />");
			}
			
			$("#citeform").attr("method", "POST");
		});

		//
		$(document.body).on('click', function(e) {
			var target = e.target.className;
			if($("#download-batch-input").length && target != 'download-endnote' && target != 'download-bibtex') {
				$("#download-batch-input").remove();
				$("#citeform").attr("method", "GET");
			}
		});

		//function that counts markers
		function countDownloadMarkers() {
			var count = 0;
			$(".download-marker").each(function(i) {
				count += ($(this).is(":checked")) ? 1 : 0;
			});
			return count;
		}
	},
	
	//-----
	
	singleimport: function()
	{
		var $ = this.jQuery;
		
		function hideCitationFields( display )
		{
			$(".add-citation fieldset:first label").each(function(i, el){
				var forinput = $(this).attr("for");
				if(forinput != "type" && forinput != "title")
				{
					$(this).css("display", display);
				}
			});
		}

		// Hide all the fields initially if there is no initial
		// citiation type selected
		if($(".add-citation #type").val() == '') {
			hideCitationFields("none");
		}

		//on change of citation show fields we want
		$(".add-citation #type").on("change", function(e) {
			hideCitationFields("none");
			var type = this.options[this.selectedIndex].text;
			type = type.replace(/\s+/g, "").toLowerCase();

			if(fields[type])
			{
				$.each(fields[type], function(index,val) {
					if( $("#"+val).length )
					{
						$("#"+val).parents("label").css("display","block");
					}
				});
			}
			else
			{
				if(this.value != "")
				{
					hideCitationFields("block");
				}
			}
		});
	},
	
	//-----
	
	rollovers: function()
	{
		var $ = this.jQuery;
		
		$(".citation-container").each(function(index){
			var $title = $(this).find(".citation-title"),
				$note = $(this).find(".citation-notes");
				
			if ($note.length)
			{
				$title
					.append($note)
					.on('mouseenter', function() {
						var positionInWindow = $title.offset().top - $('body').scrollTop();

						if (positionInWindow < $note.outerHeight(true) / 2)
						{
							$note.addClass('bottom');
						}
						
						$note.fadeIn();
					})
					.on('mouseleave', function() {
						$note.hide();
					});
			}
		});
	},
	
	//-----
	
	singleCitation: function()
	{
		var $ = this.jQuery;
		
		$("#show-more-button").on('click', function(event){
			event.preventDefault();
			$(this).remove();
			$('.show-more-hellip').remove();
			$(".show-more-text").fadeIn();
		});
	},
	
	//-----
	
	singleCitationTabs: function()
	{
		var $ = this.jQuery;
		
		if( $("#sub-menu").length )
		{
			//add click event to top locate button
			$('.locate').on('click', function(event) {
				event.preventDefault();
				
				//open the locate this tab
				openTabSection( $("#sub-menu a[rel=locate-this]") );
			});
			
			//add click events to tabs
			$("#sub-menu").find("li a").on("click", function(event) {
				event.preventDefault();
				
				//open this clicked tab
				openTabSection( $(this) );
			});
			
			//handle location hash
			var locationHash = window.location.hash.replace("#", "");
			if(locationHash != '')
			{
				var tab = $("#sub-menu a[rel=" + locationHash + "]");
				
				//open the tab
				openTabSection( tab );
			}
			
			function openTabSection( tab )
			{
				//remove all active tabs
				$("#sub-menu").find("li").removeClass('active');
				
				//mark the clicked tab as active
				tab.parent().addClass('active');
				
				//hide all sections
				$('.main').hide();
				
				//fade in requested section
				$("#" + tab.attr("rel")).show();
				
				//update location without jumping to content
				var s = $('body').scrollTop();
				window.location.hash = tab.attr("rel");
				$('html,body').scrollTop(s);
				
				//scroll body to tabs
				$('body').animate({scrollTop:tab.offset().top - 15}, 'slow');
			}
		}
	}
}

//----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.Citations.initialize();
});