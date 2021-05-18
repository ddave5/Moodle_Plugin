<?php
require(__DIR__. '/../../config.php');
require_once('classes/Relaciosema.php');
require_once('classes/local_szakdolgozat_form.php');
require_login();

$allSelect = "SELECT * FROM mdl_local_szakdolgozat WHERE name = ? and email = ? and (date between ? and ?);";
$nameSelect = "SELECT * FROM mdl_local_szakdolgozat WHERE name = ? and (date between ? and ?);";
$emailSelect=  "SELECT * FROM mdl_local_szakdolgozat WHERE email = ? and (date between ? and ?);";
$dateSelect = "SELECT * FROM mdl_local_szakdolgozat WHERE (date between ? and ?)";
$secondsToDay= 86400;
$name    = optional_param('name', '', PARAM_TEXT);
$email    = optional_param('email', '', PARAM_TEXT);
$startdate   = optional_param('startdate', '', PARAM_INT);
$enddate = optional_param('enddate', '', PARAM_INT);
$records = [];
global $CFG;

defined('MOODLE_INTERNAL') || die();

$PAGE->set_url(new moodle_url('/local/szakdolgozat/Searching.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_szakdolgozat'));
$PAGE->set_heading(get_string('searchingheading', 'local_szakdolgozat'));
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();


$form = new local_szakdolgozat_form();
$form->display();
if ($form->is_cancelled()) {
    redirect(new moodle_url('./teacher.php'));
}
else if ($data = $form->get_data()) {
    redirect(new moodle_url('./Searching.php',["name" => $data->name, "email" => $data->email, "startdate" => $data->startdate, "enddate" => $data->enddate]));

}
$table = new html_table();
$records = [];
if(($startdate > 946681200 ) && $name == "" && $email == ""){
    $records = $DB->get_records_sql($dateSelect, [$startdate,$enddate+$secondsToDay]);
}
else if ($name != "" && $email == ""){
    $records = $DB->get_records_sql($nameSelect, [$name,$startdate,$enddate+$secondsToDay]);
}
else if ($name == "" && $email != ""){

    $records = $DB->get_records_sql($emailSelect, [$email,$startdate,$enddate+$secondsToDay]);
}
else{

    $records = $DB->get_records_sql($allSelect, [$name,$email,$startdate,$enddate+$secondsToDay]);
}

$table->head = array(
    get_string('name','local_szakdolgozat'),
    get_string('email','local_szakdolgozat'),
    get_string('date','local_szakdolgozat'),
    get_string('taskLabel','local_szakdolgozat'),
    get_string('2nfsol','local_szakdolgozat'),
    get_string('2nfans','local_szakdolgozat'),
    get_string('3nfsol','local_szakdolgozat'),
    get_string('3nfans','local_szakdolgozat'),
    get_string('bcnfsol','local_szakdolgozat'),
    get_string('bcnfans','local_szakdolgozat'),
    get_string('points','local_szakdolgozat')
);

$table->align = array('centre','centre');
foreach($records as $record){
    $realtask = "";
    $relsema = Relaciosema::decoder($record->task);
    $realtask .= $relsema . " Kulcsok: ";
    foreach($relsema->getKulcsok() as $kulcs){
        $realtask.= $kulcs. " ";
    }
    $realtask.="<br>";
    foreach($relsema->getFuggeshalmaz() as $fugges){
        $realtask.= $fugges ."<br>";
    }
    if(current_language() == "hu"){
        $table->data[] = new html_table_row(array(
            $record->name,
            $record->email,
            explode(", ",userdate($record->date))[0],
            $realtask,
            $record->nf2sol,
            $record->nf2ans,
            $record->nf3sol,
            $record->nf3ans,
            $record->bcnfsol,
            $record->bcnfans,
            $record->points
        ));
    }
    else{
        $table->data[] = new html_table_row(array(
            $record->name,
            $record->email,
            explode(", ",userdate($record->date))[1],
            $realtask,
            $record->nf2sol,
            $record->nf2ans,
            $record->nf3sol,
            $record->nf3ans,
            $record->bcnfsol,
            $record->bcnfans,
            $record->points
        ));
    }

}
echo html_writer::table($table);



