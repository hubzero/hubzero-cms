<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'StringHelper.php';
require_once 'api/org/nees/static/ProjectEditor.php';

class UploadHelper{

  public static function getErrorMessage($p_iErrorCode){
    $strMessage = StringHelper::EMPTY_STRING;
    switch($p_iErrorCode){
      case ProjectEditor::UPLOAD_CODE_FILE_LARGER_PHP_INI:
          $strMessage = ProjectEditor::UPLOAD_MESSAGE_FILE_LARGER_PHP_INI;
          break;
      case ProjectEditor::UPLOAD_CODE_FILE_LARGER_HTML_FORM:
          $strMessage = ProjectEditor::UPLOAD_MESSAGE_FILE_LARGER_HTML_FORM;
          break;
      case ProjectEditor::UPLOAD_CODE_PARTIAL_UPLOAD:
          $strMessage = ProjectEditor::UPLOAD_MESSAGE_PARTIAL_UPLOAD;
          break;
      case ProjectEditor::UPLOAD_CODE_NO_FILE:
          $strMessage = ProjectEditor::UPLOAD_MESSAGE_NO_FILE;
          break;
      case ProjectEditor::UPLOAD_CODE_INVALID_EXTENSION:
          $strMessage = ProjectEditor::UPLOAD_MESSAGE_INVALID_EXTENSION;
          break;
      case ProjectEditor::UPLOAD_CODE_INVALID_FILE_TYPE:
          $strMessage = ProjectEditor::UPLOAD_MESSAGE_INVALID_FILE_TYPE;
          break;
      case ProjectEditor::UPLOAD_CODE_ERROR_MOVING_FILE:
          $strMessage = ProjectEditor::UPLOAD_MESSAGE_ERROR_MOVING_FILE;
          break;
      case ProjectEditor::UPLOAD_BAD_FILE_NAME:
          $strMessage = ProjectEditor::UPLOAD_MESSAGE_BAD_FILE_NAME;
          break;
    }
    return $strMessage;
  }

}

?>
