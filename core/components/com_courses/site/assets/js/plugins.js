if (!HUB) {
	var HUB = {};
}

//-----

HUB.NanoHUBU = {
	
	initialize: function()
	{
		HUB.NanoHUBU.outline();
		HUB.NanoHUBU.youtubeLightbox();
		HUB.NanoHUBU.courseLogin();
		HUB.NanoHUBU.manage();
		
		HUB.NanoHUBU.supportPage();
		HUB.NanoHUBU.hotseatPage();
			
		$jQ("#yes-btn, #no-btn").bind("click", function(e){
			e.preventDefault();
			var id = "#"+this.id;
			
			$jQ("#yes, #no").addClass("hide");
			$jQ(id.replace("-btn", "")).toggleClass("hide");
		});
	},
	
	//-----
	
	courseLogin: function()
	{
		if( $jQ("#course-login").length )
		{
			$jQ("#course-login input[name=username]").focus();
		}
	},
	
	//-----
	
	outline: function()
	{
		var t = $jQ('.detailsWrapper').parent('td');

		$jQ.each(t, function(key, value) { 
			$jQ(this).find('.detailsWrapper').height($jQ(this).height());
		});

		$jQ('.details').hide();
		$jQ('.detailsWrapper').hide();

		var fLink = $jQ('tr').not('.details');
		fLink.find('td').not('.week').not('.status').addClass('fLink');

		var openMe = $jQ('.open').next('.details');
		openMe.show();
		openMe.find('.detailsWrapper').show();

		$jQ('tr').not('.comingSoon').not('.details').mouseenter(function() {
			$jQ(this).css('cursor','pointer');
			if(!$jQ(this).hasClass('open')) 
			{
				$jQ(this).addClass('over');
			}
		});

		$jQ('tr').mouseleave(function() {
			if($jQ(this).hasClass('over')) 
			{
				$jQ(this).removeClass('over');
			}
		});

		$jQ('tr').not('.comingSoon').not('.details').click(function() {
			var me = $jQ(this);
			var details = $jQ(this).next('.details');
			//detailsWrapper

			if(me.hasClass('open')) 
			{
				details.find('.detailsWrapper').slideUp('slow', function() {
					me.removeClass('open');
					details.hide();
				});
				me.addClass('open');
			}
			else 
			{			
				var openTab = $jQ('.open');
				var openTabDetails = openTab.next('.details');

				openTab.removeClass('open');
				openTabDetails.find('.detailsWrapper').slideUp('slow', function() {
					openTabDetails.hide();
				});

				details.show();
				details.find('.detailsWrapper').slideDown('slow');
				me.addClass('open');
			}
			//$(this).next('.details').toggle();
		});
		
		//
		HUB.NanoHUBU.hubpresenterWindow();
	},
	
	//-----
	
	hubpresenterWindow: function()
	{
		//HUBpresenter open window
		$jQ(".hubpresenter").live("click", function(e) {
			mobile = navigator.userAgent.match(/iPad|iPhone|iPod|Android/i) != null;
			if(!mobile) {
		 		HUBpresenter_window = window.open(this.href,'name','height=650,width=1100');
				e.preventDefault();
			}
		});
	},
	
	//-----
	
	youtubeLightbox: function()
	{
		$jQ(".videos a").live("click", function(e) {
			e.preventDefault();
			var url = this.href;
		
			if(url)
			{
				var html = "<iframe width=\"100%\" height=\"500\" src=\""+ url +"\" frameborder=\"0\" allowfullscreen></iframe>";
				$jQ("<div id=\"youtube-lightbox\" class=\"reveal-modal\"></div>").html(html).appendTo(document.body);
				var timeout = setTimeout( function() {
					$jQ("#youtube-lightbox").reveal({
						closeonescapepress:true,
						afterUnReveal: function() {
							$jQ("#youtube-lightbox").remove();
						}
					});
				}, 100);
			
			}
		});
	},
      
	//-----

	supportPage: function()
	{
		if( $jQ("#course-tech-support").length )
		{
			$jQ("#course-tech-support").load(function() {
				//remove header on support form
				$jQ("#course-tech-support").contents().find("#content-header").hide();	

				//remove styling on part of support form
				$jQ("#course-tech-support").contents().find(".main").css("border","none");
				$jQ("#course-tech-support").contents().find(".section").css("padding","0");

				//form all links in iframe to open in new window
				$jQ("#course-tech-support").contents().find("a").attr("target", "_blank");
			});
		}
	},
	
	//-----
	
	hotseatPage: function()
	{
		if( $jQ("#course-hotseat").length )
		{
			document.domain = 'nanohub.org';
			
			var ua = navigator.userAgent,
				matches = ua.match(/Chrome/gi);
			
			if(!matches)
			{
				$jQ("#course-hotseat").load(function() {
					var body = $jQ("#course-hotseat").contents().find("html");
					$jQ("#course-hotseat").attr("height", body.height());
				});
			}
		}
	}, 
	

	/////////////////////////////////////////////////////
	//	Manage Section
	////////////////////////////////////////////////////	
	
	manage: function()
	{
		if( $jQ("#tabbed-content").length )
		{            
			HUB.NanoHUBU.manageCheckAll();
		    
			HUB.NanoHUBU.manageCheckSubmit();
			
			HUB.NanoHUBU.manageEmailTemplateSelect();
			
			HUB.NanoHUBU.manageEmailAutocomplete();
			
			HUB.NanoHUBU.manageAnnouncements();
			
			HUB.NanoHUBU.manageMembership();
			
			$jQ('<div id="course-overlay"></div>').appendTo(document.body);
		}
	},
	
	//-----
	
	manageCheckAll: function()
	{      
		if( $jQ(".manage-form").length )
		{               
			$jQ(".manage-form").append("<input type=\"hidden\" name=\"\" class=\"inputchecked\" value=\"0\" />");
			$jQ("table tbody input[type=checkbox]").bind("click",function(e){
				var hiddeninput = $jQ(this).parents("form").find(".inputchecked"),
					val = parseInt(hiddeninput.val());
				if(this.checked === true)
				{
					hiddeninput.val(val + 1);
				}
				else
				{
					hiddeninput.val(val - 1);
				}
			});
		}
			   
		
		$jQ(".checkall").bind("click", function(e) {
			var checked = this.checked; 
			var boxes = 0;
			var rows = $jQ(this).parents("table").find("tbody:not(.spacer) tr");
            
			rows.each(function(i, el) {
				if(!$jQ(el).hasClass("none"))
				{       
					boxes++
					$jQ(el).find("td input[type=checkbox]").attr("checked", checked);
				}
			});
			
			if(checked)
			{
				$jQ(this).parents("form").find(".inputchecked").val(boxes);
			} 
			else
			{
				$jQ(this).parents("form").find(".inputchecked").val(0);
			}
		});
	},
	
	//-----
	
	manageCheckSubmit: function()
	{
		$jQ(".manage-submit").bind("click", function(e) {
			var selectVal = $jQ(this).prev(".manage-select").val(),
				inputchecked = $jQ(this).parents("form").find(".inputchecked").val();
			                 
			if(selectVal == "")
			{
				alert("You must select an action to perform.");
				e.preventDefault();
			}                                             
			
			if(parseInt(inputchecked) < 1)
			{
				alert("You must select at least one item to perfom an action on."); 
				e.preventDefault(); 
			}
		});
	},
	  
	//-----
	
	manageEmailTemplateSelect: function()
	{
		$jQ("#email-template").bind("change", function(e) {
			var value = $jQ(this).val().split(":");
			var subject = $jQ("#email-"+value[0]+"-subject").html();
			var body = $jQ("#email-"+value[0]+"-body").html();
			
			if(value[0]) 
			{
				$jQ(".email-subject").css("display","block").find("input").val(subject);
				$jQ(".email-body").css("display","block").find("textarea").html(body);
				$jQ("#send-email-submit").attr("disabled",false)
			}
			else
			{
				$jQ(".email-subject, .email-body").css("display","none"); 
				$jQ("#send-email-submit").attr("disabled",true);
			}
		});
	}, 
	   
	//-----
	
	manageEmailAutocomplete: function()
	{
		if( $jQ("#to").length )
		{
			$jQ("#token-input-to").live("focus",function(e){
				$jQ(".token-input-dropdown").width( $jQ(".token-input-list").outerWidth(true) );
			});
			
			value = $jQ("#to").val();
			var data = [];
			if (value) {
				if (value.indexOf(',') == -1) {
					var values = [value];
				} else {
					var values = value.split(',');
				}
				$jQ(values).each(function(i, v){
					v = v.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
					var id = null, 
						name = null;
					if (v.match(/(.+?) \((.+?)\)/ig)) {
						id = v.replace(/(.+?) \((.+?)\)/ig, '$2');
					    name = v.replace(/(.+?) \((.+?)\)/ig, '$1');
					}
					id = (id) ? id : v;
					name = (name) ? name : id;

					data[i] = {
						'id': id,
						'name': name
					};
				});
			}
			
			$jQ("#to").tokenInput("https://dev04.hubzero.org/groups/" + $jQ("#to").attr("rel") + "/manage?manage-action=autocomplete&no_html=1", {
				method: "POST",
				minChars: 1,
				prePopulate: data,
				preventDuplicates: true,
				hintText: "Enter a group member's name..."
			});
		}
	},
	
	//-----
	
	manageAnnouncements: function()
	{   
		$jQ("#preview").bind("click",function(e){
	    	e.preventDefault();
		   	var html = "<iframe width=\"100%\" height=\"500\" id=\"announcements-preview-iframe\" src=\""+ $jQ(this).attr("rel") +"\"></iframe>";
			$jQ("<div id=\"annoucement-preview-box\" class=\"reveal-modal\"></div>").html(html).appendTo(document.body);    
		   
			$jQ("#announcements-preview-iframe").load(function() {
			   	$jQ("#annoucement-preview-box").reveal({
					closeonescapepress:true,
					afterReveal: function() {
						$jQ("#announcements-preview-iframe").contents().find("#announcements").html($jQ("#announcements-textbox").val());
					},
					afterUnReveal: function() {
						$jQ("#annoucement-preview-box").remove();
					}
				});                                                                          
			});
		 });
	},  
	
	//----- 
	
	manageMembership: function()
	{
		if( $jQ("#membership").length )
		{   
			$jQ(".listing-details .listing-details-box").hide();
			$jQ(".course-listing tbody tr").bind("click", function(e) {
				if(e.target.type != 'checkbox')
				{
					var next = $jQ(this).next();

					if(next.hasClass("listing-details"))
					{
						var details = next.find(".listing-details-box");
						if(details.length) 
						{
							details.slideToggle();
						}	
					}
				}
			});

			$jQ("#pec-enrollments").bind("click",function(e) {
				$jQ("#course-overlay").css("height","100%");
			});
			
			$jQ(".email-preview").bind("click",function(e) {
				e.preventDefault();                                                                                 
				var html =  $jQ("#"+this.rel).html();
				$jQ("<div id=\"email-preview-box\" class=\"reveal-modal\"></div>").html(html).appendTo(document.body);
				$jQ("#email-preview-box").reveal({
					closeonescapepress:true,
					afterUnReveal: function() {
						$jQ("#email-preview-box").remove();
					}
				});
			});
		}
	} 
	
	//-----
}

////////////////////////////////////////////////////////////

$jQ = jQuery.noConflict();

//-----

$jQ(document).ready(function() {
	HUB.NanoHUBU.initialize();
});