<?php
require(__DIR__. '/../../config.php');
require_once('classes/Relaciosema.php');
require_login();

defined('MOODLE_INTERNAL') || die();

$difficulty = optional_param('difficulty', '', PARAM_TEXT);
$taskCode = optional_param('task','',PARAM_TEXT);
$nf2    = optional_param('nf2', '', PARAM_TEXT);
$nf3    = optional_param('nf3', '', PARAM_TEXT);
$bcnf   = optional_param('BCNF', '', PARAM_TEXT);
$taskCode = optional_param('task','',PARAM_TEXT);
$maxpontszam = 50;
$task = Relaciosema::decoder($taskCode);
$alsolutePythonPath = "C:/Python386/python.exe";

$PAGE->set_url(new moodle_url('/local/szakdolgozat/TaskGenerator.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_szakdolgozat'));
$PAGE->set_heading(get_string('solutionheading', 'local_szakdolgozat'));
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

global $COURSE,$USER,$CFG;


echo "<h4>". get_string('taskLabel','local_szakdolgozat').":</h4>";
echo "<div style='left :5%; position:relative;'> <b>$task</b> <br>";
echo "<b>". get_string('key','local_szakdolgozat'). ": </b>";

foreach($task->getKulcsok() as $kulcs){
    echo "$kulcs ";
}
echo "<br>";
echo "<b>". get_string('dep','local_szakdolgozat') . ": </b>";
foreach ($task->getFuggeshalmaz() as $fugges) {
    echo $fugges . " ";
}
echo "</div> <br>";

$sol = clone $task;
$nf2SolFilePath = "db/solutions/minta2nf" . date("Ymd").$USER->firstname . $USER->lastname. ".txt";
$nf2UserFilePath = "db/solutions/user2nf".date("Ymd").$USER->firstname . $USER->lastname. ".txt";
$nf2Sol = Relaciosema::createSolution(Relaciosema::NF2($sol));
Relaciosema::saveSolution($nf2Sol, $nf2SolFilePath);
Relaciosema::writeInputToTxt($nf2, $nf2UserFilePath);
$resultnf2 = shell_exec(sprintf("%s RelationDbScheme.py %s %s %d",$alsolutePythonPath, $nf2SolFilePath, $nf2UserFilePath, $maxpontszam));


$sol = clone $task;
$nf3SolFilePath = "db/solutions/minta3nf" . date("Ymd").$USER->firstname . $USER->lastname. ".txt";
$nf3UserFilePath = "db/solutions/user3nf".date("Ymd").$USER->firstname . $USER->lastname. ".txt";
$nf3Sol = Relaciosema::createSolution(Relaciosema::NF3($sol));
Relaciosema::saveSolution($nf3Sol, $nf3SolFilePath);
Relaciosema::writeInputToTxt($nf3, $nf3UserFilePath);
$resultnf3 = shell_exec(sprintf("%s RelationDbScheme.py %s %s %d",$alsolutePythonPath, $nf3SolFilePath, $nf3UserFilePath, $maxpontszam));


$sol = clone $task;
$bcnfSolFilePath = "db/solutions/mintabcnf" . date("Ymd").$USER->firstname . $USER->lastname. ".txt";
$bcnfUserFilePath = "db/solutions/userbcnf".date("Ymd").$USER->firstname . $USER->lastname. ".txt";
$bcnfSol = Relaciosema::createSolution(Relaciosema::BCNF($sol));
Relaciosema::saveSolution($bcnfSol, $bcnfSolFilePath);
Relaciosema::writeInputToTxt($bcnf, $bcnfUserFilePath);
$resultbcnf = shell_exec(sprintf("%s RelationDbScheme.py %s %s %d",$alsolutePythonPath, $bcnfSolFilePath, $bcnfUserFilePath, $maxpontszam));


$totalPoints = $resultnf2 + $resultnf3 + $resultbcnf;
echo "<h4>". get_string('solution','local_szakdolgozat').":</h4>";


$table = new html_table();

$table->head = array("", get_string('yourAnswer','local_szakdolgozat'),get_string('solution','local_szakdolgozat'),get_string('points','local_szakdolgozat'));
$table->align = array('centre','centre');

if (is_float($resultnf2) and is_float($resultnf3) and is_float($resultbcnf)){
    echo "ERROR: Bad datas!";
}
else{
    $table->data[] = new html_table_row(array(get_string('nf2','local_szakdolgozat'),$nf2,$nf2Sol,$resultnf2));
    $table->data[] = new html_table_row(array(get_string('nf3','local_szakdolgozat'),$nf3,$nf3Sol,$resultnf3));
    $table->data[] = new html_table_row(array(get_string('bcnf','local_szakdolgozat'),$bcnf,$bcnfSol,$resultbcnf));
}

echo html_writer::table($table);

echo get_string('totalPoints','local_szakdolgozat') . $totalPoints;

echo "<form style='position: relative;' method='get' action='" .new moodle_url('/local/szakdolgozat/TaskGenerator.php',array('difficulty'=>$difficulty)). "'>";
echo html_writer::tag('input', '', [
    'type' => 'hidden',
    'name' => 'difficulty',
    'value' => $difficulty,
]);
echo "<br>";
echo html_writer::tag('button', get_string('anotherTask','local_szakdolgozat'), [
    'type' => 'submit',
]);
echo "</form>";
echo '<form method="get" action="' . new moodle_url('/local/szakdolgozat/Difficulty.php') . '">';
echo html_writer::tag('button', get_string('changeDifficulty','local_szakdolgozat'), [
    'type' => 'submit',
]);
echo "</form> ";

$roleassignments = $DB->get_records('role_assignments', ['userid' => $USER->id]);
$roleassignments = array_values($roleassignments);
if($roleassignments[0]->roleid  <= 5){
    echo '<form method="get" action="' . new moodle_url('/local/szakdolgozat/teacher.php') . '">';
    echo html_writer::tag('button', get_string('backtoteacherpage','local_szakdolgozat'), [
        'type' => 'submit',
    ]);
    echo "</form> ";
}
$fulldata = array(
    "name" => $USER->lastname." ".$USER->firstname,
    "email" => $USER->email,
    "date" => time(),
    "task" => $taskCode,
    "nf2sol" => $nf2Sol,
    "nf2ans" => $nf2,
    "nf3sol" => $nf3Sol,
    "nf3ans" => $nf3,
    "bcnfsol" => $bcnfSol,
    "bcnfans" => $bcnf,
    "points" => $totalPoints);

if (!$yoursolution = $DB->get_record_sql('SELECT * FROM {local_szakdolgozat} WHERE name=? and task = ?', [$USER->lastname." ".$USER->firstname,$taskCode])){
    $DB->insert_record('local_szakdolgozat',$fulldata);
}

