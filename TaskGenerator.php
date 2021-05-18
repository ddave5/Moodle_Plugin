<?php
require(__DIR__. '/../../config.php');
require('classes/local_szakdolgozat_form.php');
require_once('classes/Relaciosema.php');
require_login();


defined('MOODLE_INTERNAL') || die();

$difficulty    = optional_param('difficulty', '', PARAM_TEXT);
$nf2    = optional_param('nf2', '', PARAM_TEXT);
$nf3    = optional_param('nf3', '', PARAM_TEXT);
$bcnf   = optional_param('BCNF', '', PARAM_TEXT);
$taskCode = optional_param('task','',PARAM_TEXT);

$PAGE->set_url(new moodle_url('/local/szakdolgozat/TaskGenerator.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_szakdolgozat'));
$PAGE->set_heading(get_string('taskheading', 'local_szakdolgozat'));
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

global $CFG,$USER,$DB,$sol,$nf2sol,$nf3sol,$bcnfsol,$encoded;


echo "<h3> " . get_string('task', 'local_szakdolgozat') . "</h3>";
echo "<br>";
echo "<b> " . get_string('format', 'local_szakdolgozat') . "</b> <br>";
echo "<b> " . get_string('example', 'local_szakdolgozat') . "</b> <br>";
echo "<br>";
if ($difficulty == get_string('easy', 'local_szakdolgozat')) {
    $sol = Relaciosema::createEasyTask();
    $task = clone $sol;
    echo "<h4>". get_string('taskLabel','local_szakdolgozat').":</h4>";
    echo "<b>$task</b> <br>";
    echo "<b>". get_string('key','local_szakdolgozat'). ": </b>";

    foreach($task->getKulcsok() as $kulcs){
        echo "$kulcs ";
    }
    echo "<br>";
    echo "<b>". get_string('dep','local_szakdolgozat') . ": </b>";
    foreach ($task->getFuggeshalmaz() as $fugges) {
        echo $fugges . " ";
    }
    echo "<br>";
    $encoded = Relaciosema::encoder($task);
}
else if ($difficulty == get_string('medium', 'local_szakdolgozat')) {
    $sol = Relaciosema::createDoubleTask();
    $task = clone $sol;
    echo "<h4>". get_string('taskLabel','local_szakdolgozat').":</h4>";
    echo  "<b>$task</b> <br>";
    echo "<b>". get_string('key','local_szakdolgozat'). ": </b>";

    foreach($task->getKulcsok() as $kulcs){
        echo "$kulcs ";
    }
    echo "<br>";
    echo "<b>". get_string('dep','local_szakdolgozat') . ": </b>";
    foreach ($task->getFuggeshalmaz() as $fugges) {
        echo $fugges . " ";
    }
    echo "<br>";
    $encoded = Relaciosema::encoder($task);
}
else if ($difficulty == get_string('hard', 'local_szakdolgozat')) {
    $sol =  random_int(1, 2) % 2 == 0 ? Relaciosema::createComplexTask() : Relaciosema::createClosedTask();
    $task = clone $sol;
    echo "<h4>". get_string('taskLabel','local_szakdolgozat').":</h4>";
    echo "<b>$task</b> <br>";
    echo "<b>". get_string('key','local_szakdolgozat'). ": </b>";

    foreach($task->getKulcsok() as $kulcs){
        echo "$kulcs ";
    }
    echo "<br>";
    echo "<b>". get_string('dep','local_szakdolgozat') . ": </b>";
    foreach ($task->getFuggeshalmaz() as $fugges) {
        echo $fugges . " ";
    }
    echo "<br>";
    $encoded = Relaciosema::encoder($task);
}
echo "<br>";

echo '<form method="get" action="' .
    new moodle_url($CFG->wwwroot.'/local/szakdolgozat/Solution.php',array('difficulty' => $difficulty,'taskCode'=>$taskCode,'nf2' => $nf2, 'nf3' => $nf3, 'bcnf' => $bcnf))
    . '">';
echo html_writer::tag('label', get_string('nf2', 'local_szakdolgozat') . ': ', [
    "for" => 'nf2',
]);
echo "<br>";
echo html_writer::tag('input', '', [
    'type' => 'text',
    'style' => 'width: 35%',
    'id' => 'nf2',
    'name' => 'nf2',
    'value' => '',
]);
echo "<br>";
echo "<br>";
echo html_writer::tag('label', get_string('nf3', 'local_szakdolgozat') . ': ', [
    "for" => 'nf3',
]);
echo "<br>";
echo html_writer::tag('input', '', [
    'type' => 'text',
    'style' => 'width: 35%',
    'id' => 'nf3',
    'name' => 'nf3',
    'value' => '',
]);
echo "<br>";
echo "<br>";
echo html_writer::tag('label', get_string('bcnf', 'local_szakdolgozat') . ': ', [
    "for" => 'BCNF',
]);
echo "<br>";
echo html_writer::tag('input', '', [
    'type' => 'text',
    'style' => 'width: 35%',
    'id' => 'BCNF',
    'name' => 'BCNF',
    'value' => '',
]);
echo html_writer::tag('input', '', [
    'type' => 'hidden',
    'name' => 'difficulty',
    'value' => $difficulty,
]);
echo html_writer::tag('input', '', [
    'type' => 'hidden',
    'name' => 'task',
    'value' => Relaciosema::encoder($task),
]);
echo "<br>";
echo "<br>";
echo html_writer::tag('input','', [
    'type' => 'submit',
    'value' => get_string('submit', 'local_szakdolgozat'),
]);







