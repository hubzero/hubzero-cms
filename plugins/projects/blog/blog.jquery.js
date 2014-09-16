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
// Project Micro Blog JS
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.ProjectMicroblog = {
	
	jQuery: jq,
	
	initialize: function() 
	{			
		var $ = this.jQuery;
		
		// Infofeed - Comments
		$('.addcomment').each(function(i, el) 
		{
			if (!$(el).hasClass('hidden')) 
			{
				$(el).addClass('hidden');
			}
		});
		
		// Showing comment area
		$('.showc').each(function(i, item) 
		{
			$(item).on('click', function(e) 
			{
				e.preventDefault();
				var id = $(item).attr('id').replace('addc_', '');
				var acid = '#commentform_' + id;				
			
				if ($(acid) && $(acid).hasClass('hidden')) 
				{
					$(acid).removeClass('hidden');
					var coord = $(acid).position();
					$('html, body').animate({
						scrollTop: $(acid).offset().top
					}, 2000);
				} 
				else if ($(acid) && !$(acid).hasClass('hidden')) 
				{
					$(acid).addClass('hidden');
				}
			});	
		});
		
		// Comment form
		$('.commentarea').each(function(i, item) 
		{
			$(item).on('keyup', function(e) 
			{
				HUB.Projects.setCounter($(this));
			});
			if ($(item).val() == '') 
			{
				$(item)
					.css('color', '#999')
					.css('height', '20px')
					.css('font-size', '100%');
			}
			$(item).on('focus', function(e) 
			{
				$(this).css('color', '#000')
					   .css('height', '70px');
			});	
		});
		
		// Blog entry form
		if ($('#blogentry')) 
		{
			if ($('#blogentry').val() == '') 
			{
				$('#blogentry')
					.css('color', '#999')
					.css('height', '20px')
					.css('font-size', '100%');
				$('#blog-submit').addClass('hidden');
				$('#blog-submitarea').css('height', '0');
			}
			
			$('#blogentry').on('focus', function(e) 
			{
				$('#blogentry')
					.css('color', '#000')
					.css('height', '60px');
				$('#blog-submit').removeClass('hidden');
				$('#blog-submitarea').css('height', '20px');
			});	
			
			$('#blogentry').on('keyup', function(e) {
				HUB.Projects.setCounter('#blogentry', '#counter_number_blog');
			});	
			
			// On click outside
			if ($('#blog-submitarea')) {
				$('#blog-submitarea').on('click', function(e) {

						$('#blogentry')
							.css('color', '#999')
							.css('height', '20px');
						$('#blog-submit').addClass('hidden');
						$('#blog-submitarea').css('height', '0');
				});	
			}
		}
		
		// Do not allow to post default values
		if ($('#blog-submit')) {
			$('#blog-submit').on('click', function(e){
				if ($('#blogentry').val() == '') {
					e.preventDefault();
					$('#blogentry')
						.css('color', '#999')
						.css('height', '20px');
					$('#blog-submit').addClass('hidden');
					$('#blog-submitarea').css('height', '0');
				}
			});	
		}
		$('.c-submit').each(function(index, item) {
			$(item).on('click', function(e){
				
				var cid = $(this).attr('id').replace('cs_', '');
				var caid = '#ca_' + cid;
				var acid = '#commentform_' + cid;
				if ($(caid)) {
					if ($(caid).val() == '') {
						e.preventDefault();
						$(acid).addClass('hidden');
					}
				}
			});
		});

		// Confirm delete
		$('.delit').each(function(i, el) {
			var link = $(el).find("a");
			$(link).on('click', function(e) {	
				e.preventDefault();
				if (HUB.Projects) {
					HUB.Projects.addConfirm($(link), 'Permanently delete this entry?', 'yes, delete', 'cancel');
					if ($('#confirm-box')) {
						$('#confirm-box').css('margin-left', '-100px');
					}
				}
			});
		});
		
		// Show more updates
		if ($('#more-updates') && $('#pid')) {
			$('#more-updates').on('click', function(e) {
				e.preventDefault();

				var link = $('#more-updates').find("a");
				var url = link.attr('href') + '&no_html=1&ajax=1&action=update';

				$.get(url, {}, function(data) {
					$('#latest_activity').html(data);
					HUB.ProjectMicroblog.initialize();
				});
			});	
		}
	}
}

jQuery(document).ready(function($){
	HUB.ProjectMicroblog.initialize();
});