<?php
/** ****************************************************************************
 * @title
 *   UserCategory Class
 *
 * @author
 *   Wei Deng
 *
 * @abstract
 *   Class providing constants that define the various User Categories used
 *   by registration page and the user preferences.
 *
 * @description
 *   All NEES users can be grouped into the following categories:
 *     - Researchers (the users that use NEES equipment to conduct research)
 *     - Facility Staff (staff to support facility in all areas)
 *     - Practitioner (industry partners of researchers')
 *     - International Collaborators
 *     - Governance Group (NEESinc, ITSC and NSF)
 *
 ******************************************************************************/

class UserCategory {
  const USER_CAT_RESEARCHER = 1; // Must be the first item in the list
  const USER_CAT_FACILITYSTAFF = 2;
  const USER_CAT_PRACTITIONER = 3;
  const USER_CAT_INTERNATIONAL = 4;
  const USER_CAT_GOVERNANCE = 5;
  const USER_CAT_STUDENT = 6;
  const USER_CAT_OTHER = 7; // Must be the last item in the list

  private static $dispUserCategory = array();
  private static $instance;

  /**
   * Constructor
   * @return UserCategory
   */
  private function __construct() {
    $this->dispUserCategory[UserCategory::USER_CAT_RESEARCHER]    = 'Researcher';
    $this->dispUserCategory[UserCategory::USER_CAT_FACILITYSTAFF] = 'Facility Staff';
    $this->dispUserCategory[UserCategory::USER_CAT_PRACTITIONER]  = 'Practitioner';
    $this->dispUserCategory[UserCategory::USER_CAT_INTERNATIONAL] = 'International Collaborator';
    $this->dispUserCategory[UserCategory::USER_CAT_GOVERNANCE]    = 'Governance';
    $this->dispUserCategory[UserCategory::USER_CAT_STUDENT]       = 'Student';
    $this->dispUserCategory[UserCategory::USER_CAT_OTHER]         = 'Other';
  }


  /**
   * Display String represent for User Category
   *
   * @param int $index
   * @return String $category
   */
  public function disp($index) {
    if(isset($this->dispUserCategory[$index])) {
      return $this->dispUserCategory[$index];
    }

    return $this->dispUserCategory[UserCategory::USER_CAT_OTHER];
  }


  /**
   * Get the index of Category list from String category
   *
   * @param String $cat
   * @return ind
   */
  public function getIndex($cat) {
    for( $index = UserCategory::USER_CAT_RESEARCHER; $index <= UserCategory::USER_CAT_OTHER; $index ++ ) {
      if($this->dispUserCategory[$index] == $cat) return $index;
    }
    return UserCategory::USER_CAT_OTHER;
  }


  /**
   * Get the HTML code for dropdown menu of User Categories
   *
   * @param int $index: the selected index option
   * @return String $html
   */
  function getCategoryList($index) {
    $result = "<select name='catID'>";
    for( $ind = UserCategory::USER_CAT_RESEARCHER; $ind <= UserCategory::USER_CAT_OTHER; $ind ++ )  {
      $select = (is_numeric($index) && $ind==$index) ? ' selected' : '';
      $result .= sprintf("<option value=\"$ind\"$select>{$this->disp($ind)}</option>");
    }
    return $result . "</select>";
  }


  /**
   * Instance of class UserCategory
   * @return UserCategory
   */
  public static function getInstance() {
    if ( empty( self::$instance ) ) {
      self::$instance = new UserCategory();
    }
    return self::$instance;
  }
}
?>
