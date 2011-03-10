<?php //$Id: user_message_form.php,v 1.2.2.2 2010/01/13 07:56:20 rwijaya Exp $

require_once($CFG->libdir.'/formslib.php');

class user_message_form extends moodleform {

    function definition() {
        $mform =& $this->_form;
        $mform->addElement('header', 'general', get_string('message', 'message'));


        $mform->addElement('htmleditor', 'messagebody', get_string('messagebody'), array('rows'=>15, 'cols'=>60));
        $mform->addRule('messagebody', '', 'required', null, 'client');
        $mform->setHelpButton('messagebody', array('writing', 'reading', 'questions', 'richtext'), false, 'editorhelpbutton');
        $mform->addElement('format', 'format', get_string('format'));

        $this->add_action_buttons();
    }
}
?>