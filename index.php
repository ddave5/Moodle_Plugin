<?php

require(__DIR__. '/../../config.php');
require_once('classes/Relaciosema.php');
require_login();


defined('MOODLE_INTERNAL') || die();

$difficulty    = optional_param('difficulty', '', PARAM_TEXT);
$nf2    = optional_param('nf2', '', PARAM_TEXT);
$nf3    = optional_param('nf3', '', PARAM_TEXT);
$bcnf   = optional_param('BCNF', '', PARAM_TEXT);
$controller = optional_param('controller',0,PARAM_INT);
$taskCode = optional_param('task','',PARAM_TEXT);

$task = null;

$PAGE->set_url(new moodle_url('/local/szakdolgozat/index.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_szakdolgozat'));
$PAGE->set_heading(get_string('mainpageheading', 'local_szakdolgozat'));
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

global $COURSE, $USER;

$roleassignments = $DB->get_records('role_assignments', ['userid' => $USER->id]);
$roleassignments = array_values($roleassignments);
if($roleassignments[0]->roleid >= 5){
    redirect(new moodle_url('/local/szakdolgozat/Difficulty.php'));
}
else{
    redirect(new moodle_url('/local/szakdolgozat/teacher.php'));
}


