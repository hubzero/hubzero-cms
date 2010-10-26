window.addEvent('domready', function() {
	
	equalHeights('#member_column ul .neescommhq .person');
	$$('#team_column ul li .neescommhq').addClass('active');
	$$('#member_column ul li').addClass('hide');
	$$('#member_column ul .neescommhq').removeClass('hide');
	
	
	$$('#team_column ul li a').addEvent('click', function(el) {
		
		new Event(el).stop();
		var team = this.className;
			team = team.replace(' active','');
		var teamName = this.innerHTML;
		
		
		//$$('#member_column ul .pac .person').each( function(e){
		//	e.setStyle('height','200px');
		//});
		equalHeights('.person');
		
		$$('#team_column ul li a').removeClass('active');
		this.addClass('active');
		
		$$('#member_column ul li').each(function(element) {
			element.addClass('hide');
			
			if(element.hasClass(team)) {
				element.removeClass('hide');
			}
		});
		var head = document.getElementById('team_title');
		head.innerHTML = teamName;
		
		return false;
	});
	
});

window.addEvent('resize', function() {

	equalHeights('#member_column ul .person');
	
});

function equalHeights(elements) {
	var height = 0;

	divs = $$(elements);
 
	divs.each( function(e){
		e.setStyle('height','auto');
 		if (e.offsetHeight > height){
  			height = e.offsetHeight;
 		}
	});
 
	divs.each( function(e){
 		e.setStyle( 'height', height + 'px' );
 		if (e.offsetHeight > height) {
  			e.setStyle( 'height', (height - (e.offsetHeight - height)) + 'px' );
 		}
	});
	
}




