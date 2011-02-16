//NESEhub Tag browser enhancements
//Authored by Jason Lambert, January 2011
if (!HUB) {
	var HUB = {};
}
HUB.BrowseEnhancement = {
	
	hideVideo: function()
	{
		$jQ('#hubfancy-introvideo').hide();
	},
	
	showVideo: function()
	{
		$jQ('#hubfancy-level3').hide();
		$jQ('#hubfancy-introvideo').fadeIn();			
	},
	
	showCol2: function()
	{
		$jQ('#level-2').fadeIn();
	},
	
	showCol3: function()
	{
		$jQ('#hubfancy-level3').hide();
		$jQ('#level-3').fadeIn();
	},
	
	hideCol3: function()
	{
		$jQ('#level-3').fadeOut();
	},
	
		
	showPH3: function()
	{
		$jQ('#hubfancy-level3').show();
	},
	
	emptyCol3: function()
	{
		this.hideCol3();
		this.hideVideo();
		this.showPH3();
	},

	showResults: function()
	{
		$jQ('#hubfancy-seeresults').hide();
		$jQ('#hubfancy-hideresults').show();
		$jQ('#hubfancy-topratedresults').fadeIn();
	},
	
	hideResults: function()
	{
		$jQ('#hubfancy-seeresults').show();
		$jQ('#hubfancy-hideresults').hide();
		$jQ('#hubfancy-topratedresults').fadeOut();
	}

}