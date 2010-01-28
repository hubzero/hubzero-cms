//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------
HUB.MembersMsg = {
	checkAll: function( ele, clsName ) {
		if (ele.checked) {
			var val = true;
		} else {
			var val = false;
		}
		
		$$('input.'+clsName).each(function(el) {
			if (el.checked) {
				el.checked = val;
			} else {
				el.checked = val;
			}
		});
	}
}
