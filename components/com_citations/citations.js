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
HUB.Citations = {	
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
	}
}

//-----

$jQ = jQuery.noConflict();

//-----

$jQ(document).ready(function() {
	
	//check all
	$jQ(".checkall-download").bind("click", function(e) {
		var tbody = $jQ(this).parents("table.citations").children("tbody");
		tbody.find("input[type=checkbox]").attr("checked", this.checked);
	});
	
	//uncheck/check checkall depending if all sub checkboxes are checked
	$jQ(".download-marker").bind("click", function(e) {
		var checkboxes = $jQ(this).parents("table.citations tbody").find("input[type=checkbox]");
		var allchecked = (checkboxes.filter(":not(:checked)").length > 0) ? false : true
		$jQ(this).parents("table.citations").find(".checkall-download").attr("checked", allchecked);
	});
	
	//download 
	$jQ(".download-endnote, .download-bibtex").bind("click", function(e) {
		var markers = countDownloadMarkers();
		if(markers < 1) {
			alert("Select at least one citation to export");
			e.preventDefault();
		}
		
		if(!$jQ("#download-batch-input").length) {
			$jQ("#citeform").append("<input type=\"hidden\" name=\"task\" value=\"downloadbatch\" id=\"download-batch-input\" />");
		}
	});
	
	//
	$jQ(document.body).click(function(e) {
		var target = e.target.className;
		if($jQ("#download-batch-input").length && target != 'download-endnote' && target != 'download-bibtex') {
			$jQ("#download-batch-input").remove()
		}
	});
	
	//function that counts markers
	function countDownloadMarkers() {
		var count = 0;
		$jQ(".download-marker").each(function(i) {
			count += ($jQ(this).is(":checked")) ? 1 : 0;
		});
		return count;
	}
	
	//make citation notes hover
	$jQ(".citations tbody tr").each(function(i) {
		var link = $jQ(this).find(".citation-title");
		var cont = $jQ(this).find(".citation-container");
		var notes = $jQ(this).find(".citation-notes");
		
		//move the notes inside the link
		link.append(notes);
		
		//get the container cordinates on the page
		var cont_cords = cont.offset();
		link.bind({
			mouseenter: function( e ) {
				var posY = e.pageY - cont_cords.top;
				var posX = e.pageX - cont_cords.left;
				
				//threshold 
				posY -= 25;
				posX += 20;
				
				notes.css({
					'top'	: posY,
					'left'	: posX,
					'opacity': 0
				}).show().animate({"opacity":"1.0"});
			},
			mouseleave: function( e ) {
				notes.hide();
			}
			/*,				
			mousemove: function( e ) {
				e.preventDefault();
				e.stopPropagation();
				var posY = e.pageY - cont_cords.top;
				var posX = e.pageX - cont_cords.left;

				//threshold 
				posY -= 25;
				posX += 20;

				notes.css({
					'top'	: posY,
					'left'	: posX
				});
			}
			*/
		});
	});
	
	
	/////////////////////////////////////////////////////////////////////////////
	//	Import Feature JS													   //
	/////////////////////////////////////////////////////////////////////////////
	
	
	//click to show citation details
	$jQ(".upload-list .citation-title").each(function(i) {
		$jQ(this).bind("click", function(e) {
			var parent = $jQ(this).parents("tr");
			var table = $jQ(this).parents("tr").find(".citation-details");
			var show_more = $jQ(this).parents("tr").find(".click-more");
			
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
	$jQ(".checkall").bind("click", function(e) {
		var tbody = $jQ(this).parents("table.upload-list").children("tbody");
		tbody.find("input[type=checkbox]").attr("checked", this.checked);
	});
	
	//uncheck/check checkall depending if all sub checkboxes are checked
	$jQ(".check-single").bind("click", function(e) {
		var checkboxes = $jQ(this).parents("table.upload-list tbody").find("input[type=checkbox]");
		var allchecked = (checkboxes.filter(":not(:checked)").length > 0) ? false : true
		$jQ(this).parents("table.upload-list").find(".checkall").attr("checked", allchecked);
	});
	
	
	$jQ(".citation_require_attention_option").bind("click", function(e) {
		var action = this.value;
		var parent = $jQ(this).parents(".citation-details").find("tbody");
		
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
				parent.find(".old").addClass("insert").removeClass("delete");;
				break;
		}
	});
	
	function hideCitationFields( display )
	{    	
		$jQ(".add-citation fieldset:first label").each(function(i, el){
			var forinput = $jQ(this).attr("for");
			if(forinput != "type" && forinput != "title")
			{
				$jQ(this).css("display", display);
			}
		});
	}
	
	//hide all fields initially
	hideCitationFields("none");
	
	//on change of citation show fields we want
	$jQ(".add-citation #type").bind("change", function(e) {
		hideCitationFields("none");
    	var type = this.options[this.selectedIndex].text;
		type = type.replace(/\s+/g, "").toLowerCase();
	    
		if(fields[type])
		{
			$jQ.each(fields[type], function(index,val) {
				if( $jQ("#"+val).length )
				{
					$jQ("#"+val).parents("label").css("display","block");
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
});