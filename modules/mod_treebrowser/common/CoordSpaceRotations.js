function deg2rad(degs) {
  return Math.PI * (degs / 180);
}

function formatFloat(result) {
  // Some simple formatting.
  var formatted = Math.floor(result * 10000.00) / 10000.00;
  formatted = formatted.toString();
  formatted = formatted.substring(0,formatted.indexOf('.') + 3);
  var decimal = formatted.substr(formatted.indexOf('.'));

  // Don't display 1.00 or 0.00 - just use 1 or 0
  if( !decimal || decimal == '.00' ) {
    formatted = formatted.substring(0, formatted.indexOf('.'));
  }

  // -0 is just sad. really.
  return parseFloat(formatted);
}

// This function handles a change in any of the euler angle rotations,
// and updates the numbers in the resulting rotation matrix.
function changeRotation() {
  var labels = new Array('phi', 'theta', 'psi');
  var rotations = new Array('z', 'x', 'z');

  var calcs = new Array();
  calcs['z'] = new Array(
      new Array( "Math.cos(angle)",  "Math.sin(angle)", "0" ),
      new Array( "-Math.sin(angle)", "Math.cos(angle)", "0" ),
      new Array( "0",           "0",          "1" ) );
  calcs['x'] = new Array(
      new Array( "1", "0",           "0"),
      new Array( "0", "Math.cos(angle)",  "Math.sin(angle)"),
      new Array( "0", "-Math.sin(angle)", "Math.cos(angle)") );

  var values = new Array( getRotValue('0'), getRotValue('1'), getRotValue('2') );

  for( i = 0; i < 3; i++ ) {
    // Rotations.
    var rotation = rotations[i];
    var label = labels[i];
    var angle = values[i];

    for( j = 0; j < 3; j++ ) {
      // Rows.
      for( k = 0; k < 3; k++ ) {
        // Cols
        var numberfield = document.getElementById(label+'_'+j+'_'+k);
        if( !numberfield ) {
          return;
        }
        eval('var result = ' + calcs[rotation][j][k] + ';');
        numberfield.innerHTML = formatFloat(result);
      }
    }
  }

  // Big result matrix, one array per column.
  var resultCalcs = new Array(
      new Array('Math.cos(psi)*Math.cos(phi)-Math.cos(theta)*Math.sin(phi)*Math.sin(psi)',
                '-Math.cos(psi)*Math.sin(phi)-Math.cos(theta)*Math.cos(phi)*Math.sin(psi)',
                'Math.sin(psi)*Math.sin(theta)' ),
      new Array('Math.sin(psi)*Math.cos(phi)+Math.cos(theta)*Math.sin(phi)*Math.cos(psi)',
                '-Math.sin(psi)*Math.sin(phi)+Math.cos(theta)*Math.cos(phi)*Math.cos(psi)',
                '-Math.cos(psi)*Math.sin(theta)' ),
      new Array('Math.sin(theta)*Math.sin(phi)',
                'Math.sin(theta)*Math.cos(phi)',
                'Math.cos(theta)') );

  // Expand the values into vars phi, theta, psi.
  for( i = 0; i < 3; i++ ) {
    eval('var ' + labels[i] + ' = values[i];');
  }

  for( i = 0; i < 3; i++ ) {
    // cols.
    for( j = 0; j < 3; j++ ) {
      // rows.
      var numberfield = document.getElementById('final_'+i+'_'+j);
      if( !numberfield ) {
        return;
      }
      eval('var result = ' + resultCalcs[j][i] + ';');
      numberfield.innerHTML = formatFloat(result);
    }
  }
}
