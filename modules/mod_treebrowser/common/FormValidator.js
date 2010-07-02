// ----------------------------------------------------------------------
// ADAPTED FROM:
// Author Stephen Poley's Javascript form validation routines.
//
// Simple routines to quickly pick up obvious typos.
// These checks should be ignored by older browsers, so the
// forms that use these checks should
// ----------------------------------------------------------------------

var node_text = 3; // DOM text node-type
var emptyString = /^\s*$/
var global_focus_field; // retain field for timer thread
var field_ok = 'field-ok';
var field_error = 'field-error';

// Does the browser support getElementById?
var checksOk = false;
if( document.getElementById ) {
  checksOk = true;
}

// -----------------------------------------
// trim
// Trim leading/trailing whitespace off string
// -----------------------------------------
function trim(str) {
  if( str ) {
    return str.replace(/^\s+|\s+$/g, '')
  }
  return '';
};


// -----------------------------------------
// setfocus
// Delayed focus setting to get around IE bug
// -----------------------------------------
function setFocusDelayed() {
  global_focus_field.focus();
}

function setfocus(field) {
  // save field in global variable so value retained when routine exits
  global_focus_field = field;
  setTimeout( 'setFocusDelayed();', 100 );
}


// -----------------------------------------
// msg
// Display warn/error message in HTML element
// -----------------------------------------
function msg(errdiv,   // id of element to display message in
             message,  // string to display
             problemField,
             display)
{
  var elem = document.getElementById(errdiv);
  if( !elem ) {
    return false; // Element doesn't exist!
  }

  if (emptyString.test(message) || !display ) {
    elem.style.display = 'none';
  }
  else {
    setfocus(problemField);
    if( elem.className.indexOf('relativeerrorlabel') > -1 ) {
      elem.style.display = 'block';
    }
    else {
      elem.style.display = 'inline';
    }
    elem.innerHTML = message;
  }
}


// -----------------------------------------
// validateSelectPresent
// Validate whether there's data in a SELECT box.
// -----------------------------------------
function validateSelectPresent(field,  // element to be validated
                         errdiv,     // id of div to send error message to
                         errormsg)   // error message to display for failure
{
  value = field[field.selectedIndex].value;
  isEmpty = true;
  if( value && value != 0 ) {
    isEmpty = false;
  }

  msg(errdiv, errormsg, field, isEmpty);

  return !isEmpty;
}

// -----------------------------------------
// validatePresent
// Validate whether there's data in the field.
// -----------------------------------------
function validatePresent(field,  // element to be validated
                         errdiv,     // id of div to send error message to
                         errormsg)   // error message to display for failure
{
  var isEmpty = emptyString.test(field.value);

  msg(errdiv, errormsg, field, isEmpty);

  return !isEmpty;
}

// -----------------------------------------
// validateInt
// Does the field contain a valid integer?
// -----------------------------------------
function validateInt  (field, // element to be validated
                         errdiv,    // id of element to receive info/error msg
                         errormsg)  // error message to display for failure
{
  if( field.value.length == 0 ) {
    msg(errdiv, errormsg, field, false);
    return true;  // If we don't have a value, we don't need to continue checking.
  }

  // A regex that matches a non-blank integer.
  var intRegex = /^(\+|-)?\d+$/

  var isInt = intRegex.test(field.value);

  msg(errdiv, errormsg, field, !isInt);

  return isInt;
}

// -----------------------------------------
// validateFloat
// Does the field contain a valid integer?
// -----------------------------------------
function validateFloat  (field, // element to be validated
                         errdiv,    // id of element to receive info/error msg
                         errormsg)  // error message to display for failure
{
  if( field.value.length == 0 ) {
    msg(errdiv, errormsg, field, false);
    return true;  // If we don't have a value, we don't need to continue checking.
  }

  // A regex that matches a non-blank integer.
  var intRegex = /^(\+|-)?\d*\.?\d*$/

  var isInt = intRegex.test(field.value);

  msg(errdiv, errormsg, field, !isInt);

  return isInt;
}
// -----------------------------------------
// validateEmail
// Does the field contain a valid email address?
// -----------------------------------------
function validateEmail  (field, // element to be validated
                         errdiv,    // id of element to receive info/error msg
                         errormsg)  // error message to display for failure
{
  if( field.value.length == 0 ) {
    msg(errdiv, errormsg, field, false);
    return true;  // If we don't have a value, we don't need to continue checking.
  }

  // A "pretty good" email regex from the nice folks at phpBB
  var email = /^[A-Za-z0-9&\'\.\-_\+]+@[A-Za-z0-9\-]+\.([A-Za-z0-9\-]+\.)*?[A-Za-z]+$/

  var isEmail = email.test(field.value);

  msg(errdiv, errormsg, field, !isEmail);

  return isEmail;
}

// -----------------------------------------
// validateTimestamp
// Does the field contain a valid date/time?
// -----------------------------------------
function validateTimestamp  (field, // element to be validated
                             errdiv,    // id of element to receive info/error msg
                             errormsg)  // error message to display for failure
{
  if( field.value.length == 0 ) {
    msg(errdiv, errormsg, field, false);
    return true;  // If we don't have a value, we don't need to continue checking.
  }

  // First pass to see if we have the right format for a date.
  var tstamp = /\d{2}-\d{2}-\d{4} \d{2}:\d{2}/;
  var isTimestamp = tstamp.test(field.value);
  var isDate = false;

  msg(errdiv, errormsg, field, !isTimestamp);

  // Build a standard date string.
  var dateval = field.value;
  var months = new Array('NaN', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
  var monthnum = dateval.substr(0,2);
  if( monthnum.substr(0,1) == '0' ) {
    monthnum = monthnum.substr(1,1);
  }
  var stringToParse = dateval.substr(3,2) + ' ' + months[parseInt(monthnum)] + ' ' + dateval.substr(6,4) + ' ' + dateval.substr(11,5);

  if( isTimestamp ) {
    var myDate = new Date(Date.parse(stringToParse));
    // We wouldn't have to do this extra check if firefox's date parser
    // was more strict about what's a valid date string (i.e. not allowing
    // 32 days in a month and things of that sort.  But here it is.
    if(myDate.valueOf()) {
      // Subtract our timezone offset from GMT (convert minutes to milliseconds)
      // This just makes string comparison easier.
      var offset = myDate.getTimezoneOffset();
      var cmpdate = new Date(myDate.valueOf() - offset*60*1000);
      var newstring = cmpdate.toUTCString().substr(5, 17);
      // IE doesn't prepend a 0 for day-of-month before 10.
      var newstring_ie_hack = '0' + newstring.substr(0, 16);

      // Make sure our parsed date matches our date string.
      if( stringToParse == newstring || stringToParse == newstring_ie_hack) {
        isDate = true;
      }
    }
    else {
      msg(errdiv, errormsg, field, !isDate);
    }
  }

  return isDate;
}

// -----------------------------------------
// validateURL
// Does the field contain something that looks like a URL?
// -----------------------------------------
function validateURL  (field, // element to be validated
                       errdiv,    // id of element to receive info/error msg
                       errormsg)  // error message to display for failure
{
  if( field.value.length == 0 ) {
    msg(errdiv, errormsg, field, false);
    return true;  // If we don't have a value, we don't need to continue checking.
  }

  // A very simple/inaccurate URL regex.
  var url = /^http[s]*:\/\/[A-Za-z0-9-]+\.[A-Za-z0-9-]+/

  var isUrl = url.test(field.value);

  msg(errdiv, errormsg, field, !isUrl);

  return isUrl;
}

// -----------------------------------------
// validatePhone
// Validate telephone number
// Permits spaces, hyphens, parens and leading +
// -----------------------------------------
function validatePhone (field,    // element to be validated
                        errdiv,   // id of element to receive info/error msg
                        errormsg) // true if required
{
  if( field.value.length == 0 ) {
    msg(errdiv, errormsg, field, false);
    return true;  // If we don't have a value, we don't need to continue checking.
  }

  return true;
}

// -----------------------------------------
// validatePhone_bk on April 02/2007 fix case #9339
// Validate telephone number
// Permits spaces, hyphens, parens and leading +
// -----------------------------------------
/*
function validatePhone_bk (field,    // element to be validated
                        errdiv,   // id of element to receive info/error msg
                        errormsg) // true if required
{
    if( field.value.length == 0 ) {
      msg(errdiv, errormsg, field, false);
      return true;  // If we don't have a value, we don't need to continue checking.
    }

  //var phonenumber = /^\+?[0-9 ()-]+[0-9]$/
  var phonenumber = /^(\(){0,1}\s*\d{3}\s*(\)){0,1}\s*\-*\s*\d{3}\-*\d{4}\s*((ext)\s*\d*){0,1}$/
  var isPhone = phonenumber.test(field.value);
  if( !isPhone ) {
    msg(errdiv, errormsg + ' - please use only digits and dashes.', field, true);
    return false;
  }

  fieldvalue = field.value;
  var numdigits = 0;
  for (var j=0; j<fieldvalue.length; j++) {
    if (fieldvalue.charAt(j)>='0' && fieldvalue.charAt(j)<='9') {
      numdigits++;
    }
  }

  if (numdigits<6) {
    msg(errdiv, errormsg + ' - must be at least 6 digits long.', field, true);
    return false;
  }
  msg(errdiv, errormsg, field, false);
  return true;
}
*/


// Just a stub - if someone's running validateField on a field without
// any checks specified.  Field will turn green once onChange is
// triggered
function doNothing(field) {
  return true;
}

// Parse through the 'formtests' array for a given field, and attempt
// to validate it against the specified checks.
function validateField(field) {
  if( !checksOk ) {
    return;
  }

  // If we got a field name instead of a field object...
  if( typeof(field) == 'string' ) {
    field = document.getElementById(field);
  }

  field.value = trim(field.value);
  if( !formtests[field.id] ) {
    formtests[field.id] = [['doNothing', '']];
  }
  var checks = formtests[field.id];

  for( i=0; i < checks.length; i++) {
    if( checks[i] ) {
      var command = 'var result = ' + checks[i][0] + "(field, '" + field.id + '_err\', "' + checks[i][1] + '");';
      eval(command.valueOf());

      if( result ) {
        field.style.backgroundColor = '#FFFFFF';
      }
      else {
        field.style.backgroundColor = '#ffeeee';
        return false;
      }
    }
  }
  return true;
}

// The user wants to submit the form - check the formtests array
// for problems first.  Popup an error if there are probs.
function validateSubmit() {
  if( !checksOk ) {
    return true;
  }

  var errors = 0;

  if(typeof formtests == "undefined") return true;

  for ( i in formtests ) {
    if(formtests[i] && typeof(formtests[i]) != 'function') {
      if( !validateField(i) ) {
        errors++;
      }
    }
  }

  if( errors > 0 ) {
    alert('There are fields that need to be corrected before saving.');
    return false;
  }

  return true;

}
