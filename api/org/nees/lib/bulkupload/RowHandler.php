<?php

require_once "lib/error/BlankFieldError.php";
require_once "lib/error/InvalidFieldError.php";
require_once "lib/error/NoObjectFoundError.php";

/**
 * An abstract class for ingesting rows of text data.
 * It first uses the data to instantiate an object, and
 * then saves the data in the database.
 */

abstract class RowHandler {

  private $columnNames;
  private $errorMessages; // An array of arrays: array( array(rowstring, column, message) )

  public function __construct($columnString) {
    $columnString = preg_replace("/\s+$/", "", $columnString);
    $columnNames = preg_split("/[\t|,]/", $columnString);
    $this->columnNames = $this->stripQuotes($columnNames);
    $this->errorMessages = array();
  }

  protected function getColumnNames() {
    return $this->columnNames;
  }

  private function stripQuotes($s) {
    $quotePatterns = array("/\s+$/", "/[\"\']+$/", "/^[\"\']+/", "/^\s+/");
    return preg_replace($quotePatterns, array("", "", "", ""), $s);
  }

  /**
   * Factory method to return an appropriate domain object.
   */
  protected abstract function createDomainObject();

  /**
   * Descendants may wish to translate a text value into an object, depending
   * on which setter is being called.  Override this function to do so.
   */
  protected function translateValue($val, $setter) {
    return $val;
  }

  /**
   * parseRow splits the row string into tokens, delimited either by commas or tabs.
   */
  protected function parseRow($rowString) {

    /*    $rowString = preg_replace("/\s+$/", "", $rowString);
    */    if (!$rowString || preg_match("/^\s*$/", $rowString)) {
    return;
    }


    $delimiters = array(",", "\t");

    foreach ($delimiters as $delim) {
      $row_values = split($delim,$rowString);
      if (count($row_values) == count($this->columnNames)) {
        return $this->stripQuotes($row_values);
      }
    }

    throw new Exception("Row does not have correct number of tokens ($rowString)");
  }

  /**
   * ingestTextRow is the public interface for a row handler.  The UI controller handles
   * an uploaded text file, and sends it, one row at a time, to the RowHandler for importing
   * of domain objects.
   */
  public function ingestTextRow($rowString) {

    if (preg_match("/^\s*$/", $rowString)) {
      return;
    }

    $obj = $this->createDomainObject();

    $row_values = $this->parseRow($rowString);

    $i = 0;

    foreach ($this->getColumnNames() as $column) {

      $setter = $this->getSetter($column);

      try {
        $arg = $this->translateValue($row_values[$i], $setter);
      }
      catch(Exception $e) {
        throw new Exception("The message is: '" . $e->getMessage() . "' The row that caused the error is: '$rowString'. The column that caused the error is: '$column'");
      }

      // Make sure $obj has a setter for this - allows us
      // to include labels in the spreadsheet that don't belong
      // to $obj

      // Minh, do this, there is no DomainObjectMetadata::getPropertyBySetter() here :-(

      try {

        //if( $obj->getPropertyBySetter($setter) ) {
          $this->setValue($obj, $setter, $arg);

          //$obj->$setter($row_values[$i]);
          //print("<br/>" . $setter . "----" . $row_values[$i]);

        //}
      }
      catch(Exception $e) {
        throw new Exception("The message is: '" . $e->getMessage() . "'");
      }

      $i++;
    }

    try {
      $obj = $this->verifyObject($obj);
    }
    catch(Exception $e) {
      throw new Exception($e->getMessage());
    }

    return $obj;
  }

  /**
   * Returns the proper setter for the column name.
   * This allows us some flexibility in the spreadsheets
   * in terms of the column labels. This function returns
   * the assumed default. Override in descendants, if
   * desired.
   *
   * @param string $column
   * @return string $setter
   */
  protected function getSetter($column) {
    return 'set'.ucfirst($column);
  }

  protected function addToErrorMessage($col, $m, $rowString="") {
    $this->errorMessages[] = array($rowString, $col, $m);
  }

  public function getErrorMessages() {
    return $this->errorMessages;
  }

  public function setErrorMessage($message) {
    $this->errorMessages[] = $message;
  }

  public function msgToString($m) {
    return "Column {$m[1]}: {$m[2]} [" . $m[0] . "]";
  }

  protected function setValue($obj, $setter, $arg) {
    $obj->$setter($arg);
  }

  protected function verifyObject($obj) {
    return $obj;
  }

}

?>