/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function($){

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



	function hideCitationFields(display)
	{
		$(".add-citation fieldset:first label").each(function(i, el){
			var forinput = $(this).attr("for");
			if (forinput != "type" && forinput != "title") {
				$(this).css("display", display);
			}
		});
	}

	// Hide all the fields initially if there is no initial
	// citiation type selected
	if ($(".add-citation #type").val() == '') {
		hideCitationFields("none");
	}

	//on change of citation show fields we want
	$(".add-citation #type").on("change", function(e) {
		hideCitationFields("none");
		var type = this.options[this.selectedIndex].text;
		type = type.replace(/\s+/g, "").toLowerCase();

		if (fields[type]) {
			$.each(fields[type], function(index,val) {
				if ($("#"+val).length) {
					$("#"+val).parents("label").css("display","block");
				}
			});
		} else {
			if (this.value != "") {
				hideCitationFields("block");
			}
		}
	});



	$(".citation-container").each(function(index){
		var $title = $(this).find(".citation-title"),
			$note  = $(this).find(".citation-notes");

		if ($note.length) {
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



	$("#show-more-button").on('click', function(event){
		event.preventDefault();
		$(this).remove();
		$('.show-more-hellip').remove();
		$(".show-more-text").fadeIn();
	});
});