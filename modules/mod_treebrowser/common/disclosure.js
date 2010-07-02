
  //
  // JavaScript for Disclosure (show/hide child) functionality
  //
  
  var effecttime = 300;

  // Preload plus and Minus images for tree.
  var imgPlus = new Image();
  var imgMinus = new Image();
  imgPlus.src  = '/images/icons/arrow_right.png';
  imgMinus.src = '/images/icons/arrow_down.png';

  // Display/hide a div.  
  function swap(divName) {
    if(!document.getElementById) { return; }

    // Find div and +/- image.
    var div = document.getElementById(divName);
    if( !div ) { return; }
    var img = document.getElementById(divName + 'img');

    // Turn div on
    if( div.style.height == '0px' ) {
      img.src = imgMinus.src;
    }
    // Turn div off
    else {
      img.src = imgPlus.src;
    }

    // Grow div.
    var myEffect = new fx.Height(div, {duration: effecttime} );
    myEffect.toggle();
  }




function discloseAnim(i,l,n,f) {
    if (n < l.length) {
	i.src=l[n];
        setTimeout(function () { discloseAnim(i,l,n+1,f); }, 20);
    } else { f(); }
}

function disclose(e,d) {
	c = e.childNodes; 
	for (var i = 0; i < c.length; ++i) {
	    n = c[i];
	    if (n.className =="disclosureDetail") {
	    	n.style.display=d;
	    }
	}
}

function toggleDisclosure(e) { 
	if (e.parentNode.disclosed=="true") {
         e.parentNode.disclosed="false";
         discloseAnim(e,["/images/arrow_down.png", "/images/arrow_mid2.png", "/images/arrow_mid1.png", "/images/arrow_right.png"], 0, function () {disclose(e.parentNode,"none");});
      } else {
         e.parentNode.disclosed="true";
         discloseAnim(e,["/images/arrow_right.png", "/images/arrow_mid1.png", "/images/arrow_mid2.png", "/images/arrow_down.png"], 0, function () {disclose(e.parentNode,"block");});
      }
}

function paneDisclose(p) {
	if (p.disclosed) {
	    p.disclosed="false";
    	p.style.display="none";
    } else {
        p.disclosed="true";
        p.style.display="block";
    }
}
