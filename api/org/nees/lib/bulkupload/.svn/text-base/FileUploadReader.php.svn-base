<?php
ini_set('auto_detect_line_endings', true);

class FileUploadReader {

  private $numRows=null;
  private $numCols=null;
  private $datafile=null;
  private $fileErrorMsg = null;

  public function __construct($datafile) {
    $this->datafile = $datafile;
  }


  function getFileExtension() {
    if( !$this->datafile) return null;

    $pathinfo = pathinfo($this->datafile);
    return $pathinfo['extension'];
  }


  /**
   * Main Reader, driver to split reading data into cases by its file extension
   *
   * @return array Cell[Row][Column]
   */
  function getData() {

    $cells = array();
    $extension = $this->getFileExtension();

    //Excel Format, valid for Excel 95, 97, 2000, 2003 - NOT support for Excel 2007
    if(strtolower($extension) == "xls") {
      $cells = $this->getDataCellFromExcel();
    }
    //XML Spreadsheet (one of the option saved as by MS. Excel)
    elseif(strtolower($extension) == "xml") {
      $cells = $this->getDataCellFromXMLSpreadsheet();
    }
    //Comma-delimited Text Files
    elseif(strtolower($extension) == "csv") {
      $cells = $this->getDataCellFromText(",");
    }
    //Tab-delimited Text Files
    else {
      $cells = $this->getDataCellFromText("\t");
    }

    if( ! is_array($cells)) {
      return null;
    }

    $this->numRows = count($cells);

    if(isset($cells[1]) && is_array($cells[1])) {
      $this->numCols = count($cells[1]);
    }
    return $cells;
  }


  /**
   * Get data in two-dimension array[row][column] from an Excel File
   * Again, this is valid for Excel 95, 97, 2000, 2003 - NOT support for Excel 2007
   *
   * @return array two-dimension array[row][column]
   */
  function getDataCellFromExcel() {

    require_once "lib/excel/reader.php";
    $cells = array();

    // ExcelFile($filename, $encoding);
    $data = new Spreadsheet_Excel_Reader();

    // Set output Encoding.
    $data->setOutputEncoding('CP1251');

    $data->read($this->datafile);

    if( ! isset($data->sheets[0])) return null;

    for($row = 1; $row <= $data->sheets[0]['numRows']; $row++) {
      for($col = 1; $col <= $data->sheets[0]['numCols']; $col++) {
        $cells[$row][$col] = isset($data->sheets[0]['cells'][$row][$col]) ? trim($data->sheets[0]['cells'][$row][$col]) : "";
      }
    }

    return $cells;
  }


  /**
   * Get data in two-dimension array[row][column] from a text File, separated by a column delimited
   *
   * @param $delimiter: Column Delimited
   * @return array two-dimension array[row][column]
   */
  function getDataCellFromText($delimiter = "\t") {
    $cells = array();

    $row = 0;
    $handle = fopen("$this->datafile", "r");

    while (($data = fgetcsv($handle, null, $delimiter)) !== FALSE) {

      // Do not count empty line
      if(count($data) == 1 && empty($data[0])) continue;

      $numCols = count($data);

       for ($col=0; $col < $numCols; $col++) {
           $cells[$row+1][$col+1] = isset($data[$col]) ? trim($data[$col]) : "";
       }

       $row++;
    }
    fclose($handle);

    return $cells;
  }


  /**
   * Get data in two-dimension array[row][column] from an XML Spreadsheet, which was saved as by MS Excel
   *
   * @return array two-dimension array[row][column]
   */
  function getDataCellFromXMLSpreadsheet(){

    $cells = array();

    $xml = simplexml_load_file($this->datafile);

    // Only work with Excel Spreadsheet format.
    if( isset($xml->Worksheet->Table->Row)) {

      for($row = 0; $row < count($xml->Worksheet->Table->Row); $row++) {
        $line = $xml->Worksheet->Table->Row[$row]->Cell;
        for($col = 0; $col < count($line); $col++) {
          $cells[$row+1][$col+1] = isset($line[$col]->Data) ? trim($line[$col]->Data) : "";
        }
      }
    }

    return $cells;
  }


  /**
   * Get the total number of rows
   *
   * @return int
   */
  function getNumRows() {
    return $this->numRows;
  }



  /**
   * Get the total number of columns
   *
   * @return int
   */
  function getNumCols() {
    return $this->numCols;
  }


  /**
   * Get the error message when detected by reader
   *
   * @return String
   */
  function getFileErrorMsg() {
    return $this->fileErrorMsg;
  }
}
?>