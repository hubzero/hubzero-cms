<?php

class InvalidFieldError extends Exception {
  
  private $fileLine;
  private $fieldName;
  
  public function __construct($message, $fieldName = "", $fileLine = "", $code = 0) {
    
    $this->fileLine = $fileLine;
    $this->fieldName = $fieldName;
    
    parent::__construct($message, $code);
  }
  
  public function getFileLine() {
    return $this->fileLine;
  }
  
  public function getFieldName() {
    return $this->fieldName;
  }
  
  public function __toString() {
    return "{$this->message} (line: {$this->fileLine} field: {$this->fieldName})";
  }
}

?>
