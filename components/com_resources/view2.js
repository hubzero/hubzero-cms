//NESEhub resource view enhancements
//Authored by Jason Lambert, January 2011
if (!HUB) {
	var HUB = {};
}
HUB.ViewEnhancement = {
	
	showTagEdit: function()
	{
		$jQ('#hubfancy-tagedit').slideToggle();
	},
	
	showSeeAlso: function()
	{
		$jQ('.hubfancy-seealso').slideToggle();
	},
	
	hideSeeAlso: function()
	{
		$jQ('.hubfancy-seealso').fadeIn();
	}
	
}

