<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once("$CFG->libdir/formslib.php");
require_once('classes/Relaciosema.php');
class local_szakdolgozat_form extends moodleform
{
    public function definition(){



    $mform = &$this->_form;
    $mform->addElement('text', 'name', get_string('name','local_szakdolgozat'));
    $mform->setType('name', PARAM_TEXT);
    $mform->addElement('text', 'email', get_string('email','local_szakdolgozat'));
    $mform->setType('email', PARAM_TEXT);
    $mform->addRule('email', 'Email is required!', 'email', null, 'client');
    $mform->addElement('date_selector', 'startdate', get_string('startdate','local_szakdolgozat'),array(
        'startyear' => 2000,
        'stopyear'  => 2021,
        'timezone'  => 99,
        'optional'  => false
    ));
    $mform->setDefault('startdate',1);
    $mform->addElement('date_selector', 'enddate', get_string('enddate','local_szakdolgozat'),array(
        'startyear' => 2000,
        'stopyear'  => 2021,
        'timezone'  => 99,
        'optional'  => false
    ));
    $mform->setDefault('enddate',time());


    $buttonarray=array();
    $buttonarray[] = &$mform->createElement('submit', 'submitbutton',get_string('search','local_szakdolgozat'));
    $buttonarray[] = &$mform->createElement('cancel','cancelbutton',get_string('backtoteacherpage','local_szakdolgozat'));
    $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    $mform->closeHeaderBefore('buttonar');
    }
}