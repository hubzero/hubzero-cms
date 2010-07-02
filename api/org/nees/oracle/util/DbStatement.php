<?php 

  class DbStatement{
    
    public $m_sQuery;
    public $m_oParameterArray;

    public function __constructor(){
    }
    
    /**
     * Initializes a DbStatement query and parameter array.
     *
     */
    public function prepareStatement($p_sQuery){
      $this->m_sQuery = $p_sQuery;
      $this->m_oParameterArray = array();
    }
    
    /**
     * Sets query string.
     * @param $p_sQuery - query string
     */
    public function setQuery($p_sQuery){
      $this->m_sQuery = $p_sQuery;
    }

    /**
     * Returns query statement.
     *
     */
    public function getQuery(){
      return $this->m_sQuery;
    }

    /**
     * Binds parameter names in query to their respective values.
     */
    public function bind($p_sParameterName, $p_sParameterValue){
      array_push($this->m_oParameterArray, new DbParameter($p_sParameterName, $p_sParameterValue));
    }
    
    /**
     * Sets the bound parameter array.
     *
     */
    public function setParameters($p_oParameterArray){
      $this->m_oParameterArray = $p_oParameterArray;
    }

    /**
     * Returns the bound query parameters.
     */
    public function getParameters(){
      return $this->m_oParameterArray;
    }
    
    /**
     * Returns the value of a bound variable.
     *
     */
    public function getParameterValue($p_strParameterKey){
      return $this->m_oParameterArray[$p_strParameterKey];
    }
  }

?> 
