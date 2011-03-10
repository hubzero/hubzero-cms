<?php //$Id: field.class.php,v 1.6.2.1 2007/12/21 08:17:36 scyrma Exp $

class profile_field_text extends profile_field_base {

    function edit_field_add(&$mform) {
        $size = $this->field->param1;
        $maxlength = $this->field->param2;
        $fieldtype = ($this->field->param3 == 1 ? 'password' : 'text');

        /// Create the form field
        $mform->addElement($fieldtype, $this->inputname, format_string($this->field->name), 'maxlength="'.$maxlength.'" size="'.$size.'" ');
        $mform->setType($this->inputname, PARAM_MULTILANG);
    }

}

?>
