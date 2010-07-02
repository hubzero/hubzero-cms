<?php
/********************************************************************
**
** The code written by:
**
**    NACSE Team, Febuary 2006, Ben Steinberg, Adam Ryan
**    Module Name: Filter.php
**    Last Modification by: Adam Ryan
**    Last Modified date: 2/22/06
**    Copyright � 2006
**       Northwest Alliance for Computational Science and Engineering
**       (NACSE, www.nacse.org)
**
* @description
*   Currently only produces SQL filter.  Given an input string
*   of a particular syntax, produces logical filter combinations:
*     ex. foo1 < foo2 AND bar1 >= bar2 OR baz1 != baz2 NOT blah1 = blah2
*
**
********************************************************************/


class Filter {



  /********************************
  * Mapping Arrays
  *
  ********************************/
  protected $operators=array("=" => "=", "<" => "<", "<=" => "<=", ">" => ">", ">=" => ">=", "<>" => "!=", "like" => "like");

  protected $clauses=array(")AND(" => "AND", ")OR(" => "OR", ")NOT(" => "NOT", "(" => "(", ")" => ")");

  protected $clausePattern='/(\)AND\()|(\)OR\()|(\)NOT\()|(\()|(\))/';

  protected $operatorPattern='/(<>)|(<=)|(>=)|(!=)|(<)|(>)|(=)/';

  protected $filter;
  protected $where;
  protected $fields;
  protected $values;
  protected $ops;
  protected $metaData;

  protected $request;
  protected $order;

  protected $lckm;


  private $Error;

  private $quoteables = array("char","varchar","enum");

  /***
  ** Function: Constructor
  **
  ** Parameters: None
  **
  ** Return Values: None
  */
  function Filter()
  {
    $filter = null;
    $where = null;
    $fields = null;
    $metaData = null;
    $values = null;
    $ops = null;
    $request = null;
    $order = null;
  }


  public function validateAndSubstitute(&$f, &$v)
  {
    if (is_null($this->metaData))
    return($this->setError("Meta Data array net set"));

    if(isset($this->metaData[$this->lckm[$f]])){

      // Add value quotes here
      if (in_array($this->metaData[$this->lckm[$f]]["type"],$this->quoteables)){
        if (!strstr($v,"'"))
        $v="'".$v."'";
      }

      $f = $this->metaData[$this->lckm[$f]]["field"];

    }

    else {
      return($this->setError($f." is not a valid parameter"));

    }

    return(true);
  }


  public function setMetaData($md=NULL)
  {
    if (is_null($md))
    return($this->setError("Can't set metadata to NULL"));

    if (!is_array($md))
    return($this->setError("Can't set metadata to NON-array entity"));

    if(count($md) == 0)
    return($this->setError("Can't set metadata to empty-array entity"));

    $this->metaData=$md;
    $this->lckm=$this->lowerCaseKeyMap($md);

    return(true);
  }

  /***
  ** Function: setFilter - Assign the input string
  **            to the filter
  **
  ** Parameters: an input string
  **
  ** Return Values: FALSE - if input string is NULL
  **                TRUE - in all other cases
  */
  public function setFilter($input=NULL)
  {
    if (is_null($input))
    return(FALSE);

    $this->filter=$input;
    return(TRUE);
  }


  /***
  ** Function: setRequest - Assign the request global
  **            to the filter object
  **
  ** Parameters: an array
  **
  ** Return Values: FALSE - if this is null or not an array
  **                TRUE - in all other cases, including an empty array
  */
  public function setRequest($request=NULL)
  {
    if (is_null($request))
    return($this->setError("Can't set request to NULL"));

    if (!is_array($request))
    return($this->setError("Can't set request to NON-array entity"));

    $this->request=$request;

    return(TRUE);
  }


  public function getRequest(){
    return $this->request;
  }


  public function getOrder()
  {
    #   Check if null
    if (is_null($this->order))
    return($this->setError("Order has not been determined."));

    return $this->order;
  }

  /***
  ** Function: getFilterString - return the end resultant filter string
  **
  ** Parameters: None
  **
  ** Return Values: FALSE - if the input string was never set and parsed
  **                STRING - string containing the filter n all other cases
  */
  public function getFilterString()
  {

    #   Check meta first
    if (is_null($this->metaData))
    return($this->setError("metaData array not set"));

    #   Fork according to input
    if (!is_null($this->filter)){
      if ($this->processFilter()==FALSE)
      return(FALSE);
    }else if (!is_null($this->request)){
      if ($this->processRequest()==FALSE)
      return(FALSE);
    }else{
      return($this->setError("Neither input string nor request array are set"));
    }

    if (is_null($this->where))
    return($this->setError("No valid filter string found"));

    return($this->where);
  }


  /***
  ** PROTECTED Function
  ** Function: processFilter - parses input string and build filter string
  **
  ** Parameters: None
  **
  ** Return Values: None
  */
  protected function processFilter()
  {
    $b=preg_split($this->clausePattern,$this->filter,
    -1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $i = 0;

    $tmp="";

    foreach($b as $expr){

      if(isset($this->clauses[$expr])){

        $tmp.=" ".$this->clauses[$expr]." ";
      }

      else {

        list($field,$opr,$val)=
        preg_split($this->operatorPattern,$expr,
        -1,PREG_SPLIT_NO_EMPTY |
        PREG_SPLIT_DELIM_CAPTURE);

        if (strstr($val,"*")){
          $val=str_replace("*","%",$val);
          $opr='like';
        }

        if (empty($opr) || empty($val)){
          unset($field);
        }

        else {

          $field = strtolower($field);

          //					print "Input field = $field  ->  ";

          if ($this->validateAndSubstitute($field,$val)==false)
          return(false);

          //					print "Processed field = $field\n";

          $tmp.=$field." ".  $this->operators[$opr]." ".$val;

        }

      } // else

    } //Forech

    if (!empty($tmp)) {
      $this->where=$tmp;
      return(true);
    }
    else
    return($this->setError("No string was produced"));

  }


  public function getError(){
    return $this->Error;
  }

  protected function setError($msg=null)
  {
    if(is_null($msg))
    $this->Error="Error message net to null";

    $this->Error=$msg;

    return(false);
  }


  public function setWhere($where=null)
  {
    if(is_null($where))
    return($this->setError("Where is null"));
    $this->where=$where;
  }

  public function getWhere()
  {
    return $this->where;
  }

  public function getValues(){
    return $this->values;
  }
  public function getFields(){
    return $this->fields;
  }

  protected function lowerCaseKeyMap($a)
  {
    $lca = array();
    foreach (array_keys($a) as $key){
      $lca[strtolower($key)] = $key;
    }
    return $lca;
  }


  /***
  ** PROTECTED Function
  ** Function: processRequest - parses request and build filter string
  **
  ** Parameters: None
  **
  ** Return Values: None
  */


  protected function processRequest(){

    #   Create a lower case metadata copy to test user passed parameter names (any case) against
    $lcmap = $this->lowerCaseKeyMap($this->metaData);

    #   check for where
    if (is_null($this->where)){
      $where = "";
      $sep = "";
    }else{
      $where = $this->where;
      $sep = " AND ";
    }

    #   do this for order?
    $order = "";


    #   step through the request keys
    foreach ($this->request as $key => $val){

      #   Check to see if this parameter is in the lower case metadata list
      $lckey = strtolower($key);
      if (isset($lcmap[$lckey])){

        #   Get the parameter name from the mapping
        $param = $lcmap[$lckey];

        #   Get the metadata array for this field
        $fldmeta = $this->metaData[$param];

        #   Check the value - if null add this to the order list
        if (is_null($val)){

          #   Add the field to the order
          $order = ($order == "") ? $fldmeta["field"] : $order.",".$fldmeta["field"];

        }else{

          #   Check for the need for a quote
          $q = (in_array($fldmeta["type"], $this->quoteables )) ? "'" : "";

          #   Add the param to the where claue
          $where .= "$sep ".$fldmeta["field"]." = $q$val$q ";
          $sep = " AND ";

          #   Add to the fields and values
          $this->fields[] = $fldmeta["field"];
          $this->values[] = $val;
        }

      }else{

        #ignore the key!

      }
    } # foreach

    if (!empty($where)) {
      $this->where=$where;
      return(true);
    }
    else return($this->setError("No string was produced"));


  }


}

?>