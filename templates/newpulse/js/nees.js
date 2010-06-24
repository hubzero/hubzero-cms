window.addEvent('domready', function(){
	currentlyLiveInUsCheck();
});



function neesAffiliationCheck() {
	
	var w = document.getElementById('neesaffiliation').selectedIndex;
	var selected_value = document.getElementById('neesaffiliation').options[w].value;

	if(selected_value != 'other')
	{
		var textbox = document.getElementById('txtneesaffiliation');
		textbox.disabled = true;
		textbox.style.backgroundColor = "#eeeeee"; 
		textbox.value = '';
	}
	else
	{
		var textbox = document.getElementById('txtneesaffiliation');
		textbox.disabled = false;
		textbox.style.backgroundColor = ""; 
	}

}

function relationshipToNeesCheck() {

	var w = document.getElementById('neesrelationship').selectedIndex;
	var selected_value = document.getElementById('neesrelationship').options[w].value;
	
	if(selected_value != 'other')
	{
		var textbox = document.getElementById('txtneesrelationship');
		textbox.disabled = true;
		textbox.style.backgroundColor = "#eeeeee"; 
		textbox.value = '';
	}
	else
	{
		var textbox = document.getElementById('txtneesrelationship');
		textbox.disabled = false;
		textbox.style.backgroundColor = "";
	}
	
}


function currentlyLiveInUsCheck() {

	var yesButton = document.getElementById('cresident_usyes');
	var noButton = document.getElementById('cresident_usno');
	var countryList = document.getElementById('cresident');
	var stateList = document.getElementById('UsState');

	// Redundant, but there is a chance that for initial form, neither button is checked, so we need
	// to account for three cases: (Yes, No, Neither)
	if(!yesButton.checked && !noButton.checked)
	{
		countryList.disabled = true;
		countryList.selectedIndex = 0;
		countryList.style.backgroundColor = "#eeeeee"; 

		stateList.disabled = true;
		stateList.selectedIndex = 0;
		stateList.style.backgroundColor = "#eeeeee"; 

		return;
	}
	else if(!noButton.checked)
	{
		countryList.disabled = true;
		countryList.selectedIndex = 0;
		countryList.style.backgroundColor = "#eeeeee"; 

		stateList.style.backgroundColor = ""; 
		stateList.disabled = false;
	}
	else if(!yesButton.checked)
	{
		countryList.disabled = false;
		countryList.style.backgroundColor = ""; 

		stateList.disabled = true;
		stateList.selectedIndex = 0;
		stateList.style.backgroundColor = "#eeeeee"; 
	}
	
	
	
	
	
}









