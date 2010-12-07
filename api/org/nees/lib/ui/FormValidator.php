<?php

class FormValidator {
  // Accepts data-to-validate, and type to validate against.
  // Type is formatted as datatype[:min[:max]]
  public static function validate($data, $checks) {
    foreach( $checks as $type ) {
      $min = false;
      $max = false;
      $types = preg_split('/:/', $type, -1);
      if(count($types) > 1) {
        $type = $types[0];
        $min = $types[1];
        if ( isset( $types[2])) {
          $max = $types[2];
        }
      }

      // make sure they're trying to validate a type that we
      // have a validation function for, then run the validation.
      $method_to_call = 'validate' . ucfirst($type);
      if( method_exists('FormValidator', $method_to_call)) {
        $result = FormValidator::$method_to_call($data, $min, $max);
        if( $result !== true ) {
          return $result;
        }
      }
      else {
        // They're trying to validate a type we don't know about!
        return false;
      }
    }
    return true;
  }

  public static function validateForm(Request $request, $validate) {
    // 'validate' is an array like so:
    // $validate = array(
    //     'fieldname0' => array('required', 'int'),
    //     'fieldname1' => array('string:0:10'),
    // }
    $errors = array();
    foreach ( $validate as $fieldname => $reqs ) {
      $data = $request->getProperty($fieldname);
      $res = FormValidator::validate($data, $reqs);
      // Our validation functions can return an error message,
      // or true or false.  Only 'true' is an ok status.
      if( $res !== true ) {
        if( $res ) {
          $errors[$fieldname] = $res;
        }
        else {
          $errors[$fieldname] = true;
        }
      }
    }
    return $errors;
  }


  /**
   * Get Epoch number from a String request formatted: mm-dd-yyyy
   *
   * @param String $date
   * @return boolean
   */
  function getEpochFromString($date)
  {
    if(strlen($date) != 10) return false;
    if(count(explode("-", $date)) != 3) return false;

    list($mm, $dd, $yyyy) = explode("-", $date);
    if (is_numeric($yyyy) && is_numeric($mm) && is_numeric($dd) && checkdate($mm,$dd,$yyyy))
    {
      $epoch = strtotime($mm . "/" . $dd . "/" . $yyyy);
      if(empty($epoch)) return -1;
      else return $epoch;
    }
    return false;
  }

  /**
   * Get Epoch number from a String request formatted: mm/dd/yyyy
   *
   * @param String $date
   * @return boolean
   */
  function getHubEpochFromString($date)
  {
    if(strlen($date) != 10) return false;
    if(count(explode("/", $date)) != 3) return false;

    list($mm, $dd, $yyyy) = explode("/", $date);
    if (is_numeric($yyyy) && is_numeric($mm) && is_numeric($dd) && checkdate($mm,$dd,$yyyy))
    {
      $epoch = strtotime($mm . "/" . $dd . "/" . $yyyy);
      if(empty($epoch)) return -1;
      else return $epoch;
    }
    return false;
  }


  public static function validateRequired($value, $nothing, $nothing2) {
    if( strlen(trim($value)) >= 1 )  {
      return true;
    }
    return false;
  }

  public static function validateString($string, $minlength=0, $maxlength=0) {
    if( strlen($string) > $maxlength ) {
      return "too long";
    }
    if( strlen($string) < $minlength ) {
      return "too short";
    }
    return true;
  }

  public static function validateInt($int, $min=false, $max=false) {
    if( !$int ) { return true; }
    if( preg_match('/^\d+$/', $int)) {
      if( $min !== false && $int < $min ) {
        return "must be greater than $min";
      }
      if( $max !== false && $int > $max ) {
         return "must be less than $max";
      }
      return true;
    }
    return false;
  }

  public static function validateEmail($email, $nothing, $nothing2) {
    if( !$email ) {
      return true;
    }
    // A "pretty good" email address regex, from the nice folks at phpBB.
    if(preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $email)) {
      return true;
    }
    return false;
  }

  public static function validateUrl($url, $nothing, $nothing2) {
    if( !$url ) {
      return true;
    }
    // A very simple/inaccurate URL regex.
    if( preg_match('/^http[s]*:\/\/[A-Za-z0-9-]+\.[A-Za-z0-9-]+/', $url) ) {
      return true;
    }
    return false;
  }

  public static function validateFloat($float, $min = false, $max = false) {
    if( preg_match('/^(\+|-)?\d*\.?\d*$/', $float)) {
      if( $min !== false && $float < $min ) {
        return "must be greater than $min";
      }
      if( $max !== false && $float > $max ) {
        return "must be less than $max";
      }
      return true;
    }
    return false;
  }

  // Very simple is-valid-date function, will accept any
  // format accepted by strtotime().  We'll need more
  // format-specific date validation functions eventually.
  public static function validateAnyDate($date, $min = false, $max = false) {
    if( !$date ) { return true; }

    // strtotime doesn't like dashes.
    $date = preg_replace('/-/', '/', $date);

    $timestamp = strtotime($date);
    if( $timestamp && $timestamp != time() ) {
      if( $min !== false && $timestamp < strtotime($min)) {
        return false;
      }
      if( $max !== false && $timestamp > strtotime($max)) {
        return false;
      }
      return true;
    }
    return false;
  }
}

?>
