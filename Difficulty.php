<?php
require(__DIR__. '/../../config.php');
require_once('classes/Relaciosema.php');
require_login();


defined('MOODLE_INTERNAL') || die();

$difficulty = optional_param('difficulty', '', PARAM_TEXT);
$taskCode = optional_param('task','',PARAM_TEXT);

$task = null;

$PAGE->set_url(new moodle_url('/local/szakdolgozat/Difficulty.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_szakdolgozat'));
$PAGE->set_heading(get_string('taskheading', 'local_szakdolgozat'));
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();


echo "<h3> " . get_string('chooseDifficulty','local_szakdolgozat') . "</h3>";
echo "<br>";
echo '<form method="get" action="'.new moodle_url('./TaskGenerator.php',array('difficulty'=>$difficulty)).'">';
echo html_writer::tag('input', '',[
    'type' => 'radio',
    'id' => get_string('easy','local_szakdolgozat'),
    'name' => 'difficulty',
    'value' => get_string('easy','local_szakdolgozat'),
    'checked' => 'checked',
]);
echo html_writer::tag('label', get_string('easy','local_szakdolgozat'),[
    "for" => get_string('easy','local_szakdolgozat'),
]);
echo "<br>";

echo html_writer::tag('input', '',[
    'type' => 'radio',
    'id' => get_string('medium','local_szakdolgozat'),
    'name' => 'difficulty',
    'value' => get_string('medium','local_szakdolgozat'),
]);

echo html_writer::tag('label', get_string('medium','local_szakdolgozat'),[
    "for" => get_string('medium','local_szakdolgozat'),
]);
echo "<br>";
echo html_writer::tag('input', '',[
    'type' => 'radio',
    'id' => get_string('hard','local_szakdolgozat'),
    'name' => 'difficulty',
    'value' => get_string('hard','local_szakdolgozat'),
]);

echo html_writer::tag('label', get_string('hard','local_szakdolgozat'),[
    "for" => get_string('hard','local_szakdolgozat'),
]);

echo "<br>";
echo html_writer::tag('input', '', [
    'type' => 'submit',
    'value' => get_string('submit', 'local_szakdolgozat'),
]);

