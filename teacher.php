<?php

require(__DIR__. '/../../config.php');
require_once('classes/Relaciosema.php');
require_login();


defined('MOODLE_INTERNAL') || die();

$PAGE->set_url(new moodle_url('/local/szakdolgozat/teacher.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_szakdolgozat'));
$PAGE->set_heading(get_string('teacherheading','local_szakdolgozat'));
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();
echo "<a href='./Difficulty.php'><button>".get_string('taskgenerator','local_szakdolgozat')." </button></a> ";
echo "<a href='./Searching.php'><button>".get_string('solutionsearch','local_szakdolgozat')."</button></a>";

?>

