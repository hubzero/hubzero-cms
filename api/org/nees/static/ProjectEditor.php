<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ProjectEditor{

  const DEFAULT_PROJECT_URL = "/warehouse/project/[id]";
  const DEFAULT_AWARD_NUMBER = "Award Number";
  const DEFAULT_WEBSITE_TITLE = "Website Title";
  const DEFAULT_SPONSOR = "NSF";
  const DEFAULT_PROJECT_IMAGE = "/components/com_projecteditor/images/logos/NEES-logo_grayscale.png";
  const DEFAULT_PROJECT_CAPTION = "Enter project photo caption";
  const DEFAULT_EXPERIMENT_CAPTION = "Enter experiment photo caption";
  const DEFAULT_NAME = "Last, First Name";

  const ACTIVE_PROJECT = "Active Project";

  /** Message to display when a project is not selected.  */
  const PROJECT_ERROR_MESSAGE = "Unable to handle request.  Please save project before attempting to view its details.";
  const EXPERIMENT_ERROR_MESSAGE = "Unable to handle request.  Please save experiment before attempting to view its details.";

  const PROJECT_OWNER_USERNAME = "OWNER";
  const PROJECT_ADMIN_USERNAME = "SYSTEM ADMIN";

  const PREVIEW_NEW_PROJECT = "/warehouse/projecteditor/preview";
  const PREVIEW_EXISTING_PROJECT = "/warehouse/projecteditor/previewprojectedit";

  const PROJECT_IMAGE = "Project Image";
  const PROJECT_UPLOAD_DIR = "/www/neeshub/components/com_projecteditor/uploads/members";
  const PROJECT_UPLOAD_DIR_WEB = "/components/com_projecteditor/uploads/members";
  const EXPERIMENT_IMAGE = "Experiment Image";
  const FILMSTRIP_IMAGE = "Film Strip";
  const GENERAL_IMAGE = "General Photo";
  const PHOTO_CAPTION = "Photo Caption";
  const PHOTO_NAME = "Photo Name";
  const PHOTO_USAGE_TYPE_ID = "Photo Usage Type";
  const PHOTO_DESTINATION_SUFFIX = "/Documentation/Photos";
  const VIDEO_FRAMES_PATTERN = "/Experiment-([0-9])+\/Trial-([0-9])+\/Rep-([0-9])+\/(\w)+_Data\/Videos\/Frames/";
  const VIDEO_MOVIES_PATTERN = "/Experiment-([0-9])+\/Trial-([0-9])+\/Rep-([0-9])+\/(\w)+_Data\/Videos\/Movies/";

  const UPLOAD_FIELD_NAME = "upload";

  const UPLOAD_CODE_SUCCESS = 1;
  const UPLOAD_CODE_FAILED = 10;
  const UPLOAD_CODE_IMAGE_SCALED = 2;
  const UPLOAD_CODE_IMAGE_NOT_SCALED = 20;
  const UPLOAD_CODE_IMAGE_INVALID = 30;

  const CURATION_UNCURATED = "Uncurated";
  const CURATION_REQUEST = "Submitted";
  const CURATION_CURATED = "Curated";
  const CURATION_INCOMPLETE = "Incomplete";
  const CURATION_CURRENT = "Current";

  const VALID_IMAGE_EXTENSIONS = "jpeg,jpg,png,gif";

  //const PERSON_NAME_PATTERN = "/\(\w+\)/";  //match any word

  const PERSON_NAME_PATTERN = "/\([a-zA-Z0-9_-]+\)/";  //match any letter, number, hypen

  const MEMBER_NOT_FOUND = "Team Member not found.";

  const PHOTO_NOTE = "If uploading mutliple image files (PNG, JPG, or GIF), please allow time for the server to generate smaller versions of the images. The original photo will be stored as well.";

  const MOVIE_FRAMES_NOTE = "If uploading frames for a movie, compress the <span style='font-weight:bold;color:#990000'>folder</span> then upload the images.  A nightly process will unzip and store all of the images in the database.";

  const UPLOAD_CODE_FILE_LARGER_PHP_INI = 100;
  const UPLOAD_CODE_FILE_LARGER_HTML_FORM = 110;
  const UPLOAD_CODE_PARTIAL_UPLOAD = 120;
  const UPLOAD_CODE_NO_FILE = 130;
  const UPLOAD_CODE_INVALID_EXTENSION = 140;
  const UPLOAD_CODE_INVALID_FILE_TYPE = 150;
  const UPLOAD_CODE_ERROR_MOVING_FILE = 160;
  const UPLOAD_CODE_FILE_TOO_BIG = 170;

  const UPLOAD_MESSAGE_FILE_LARGER_PHP_INI = "Upload Error - FILE LARGER THAN PHP INI ALLOWS";
  const UPLOAD_MESSAGE_FILE_LARGER_HTML_FORM = "Upload Error - FILE LARGER THAN HTML FORM ALLOWS";
  const UPLOAD_MESSAGE_PARTIAL_UPLOAD = "Upload Error - ERROR PARTIAL UPLOAD";
  const UPLOAD_MESSAGE_NO_FILE = "Upload Error - ERROR NO FILE";
  const UPLOAD_MESSAGE_INVALID_EXTENSION = "Upload Error - INVALID EXTENSION. Photos should be PNG, JPG, or GIF.";
  const UPLOAD_MESSAGE_INVALID_FILE_TYPE = "Upload Error - INVALID FILETYPE";
  const UPLOAD_MESSAGE_ERROR_MOVING_FILE = "Upload Error - ERROR MOVING FILE";

  const AUTHORIZER_PROJECT_EDIT_ERROR = "You cannot edit this project.";
  const AUTHORIZER_EXPERIMENT_CREATE_ERROR = "You cannot create an experiment for this project.";
  const AUTHORIZER_EXPERIMENT_EDIT_ERROR = "You cannot edit this experiment.";

  const CREATE_PROJECT_EXPERIMENTS_ALERT = "javascript:alert('Save project before editing experiments.');";
  const CREATE_PROJECT_MEMBERS_ALERT = "javascript:alert('Save project before managing team members.');";
  const CREATE_PROJECT_SUBTAB_ALERT = "javascript:alert('Save project (About tab) before editing its details.');";

  const CREATE_EXPERIMENT_PROJECT_ALERT = "javascript:alert('Save experiment before reviewing project.');";
  const CREATE_EXPERIMENT_MEMBERS_ALERT = "javascript:alert('Save experiment before managing team members.');";
  const CREATE_EXPERIMENT_SUBTAB_ALERT = "javascript:alert('Save experiment (About tab) before editing its details.');";

  const QUICK_START_GUIDE = "/components/com_projecteditor/downloads/ProjectEditorQuickStartGuide.pdf";

  const CURATE_LAYOUT = "curate";

  const SENSOR_TYPE_DOWNLOAD = "/www/neeshub/components/com_projecteditor/downloads/SensorTypes.txt";
  //const SENSOR_TYPE_DOWNLOAD_LINK = "/warehouse/projecteditor/sensortypes?tmpl=component";
  const SENSOR_TYPE_DOWNLOAD_LINK = "/warehouse/projecteditor/sensortypes";
  const SENSOR_REQUIRED_LINK = "/warehouse/projecteditor/sensorrequired";
}

?>
