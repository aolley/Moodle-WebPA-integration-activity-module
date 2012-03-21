<?php // $Id: index.php,v 1.7.2.3 2009/08/31 22:00:00 mudrd8mz Exp $

/**
 * This page lists all the instances of webpa in a particular course
 *
 * @package mod/webpa
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);   // course

if (! $course = $DB->get_record('course', array('id' => $id))) {
   print_error('Course ID is incorrect');
}

require_course_login($course);

add_to_log($course->id, 'webpa', 'view all', "index.php?id=$course->id", '');


/// Get all required stringswebpa

$strwebpas = get_string('modulenameplural', 'webpa');
$strwebpa  = get_string('modulename', 'webpa');


/// Print the header

$navlinks = array();
$navlinks[] = array('name' => $strwebpas, 'link' => '', 'type' => 'activity');
$navigation = build_navigation($navlinks);

$PAGE->set_url("/mod/webpa/index.php",array("id"=>$id));
$PAGE->set_title(format_string("WebPA"));
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

//print_header_simple($strwebpas, '', $navigation, '', '', true, '', navmenu($course));

/// Get all the appropriate data

if (! $webpas = get_all_instances_in_course('webpa', $course)) {
    notice('There are no instances of webpa', "../../course/view.php?id=$course->id");
    die;
}

/// Print the list of instances (your module will probably extend this)

$timenow  = time();
$strname  = get_string('name');
$strweek  = get_string('week');
$strtopic = get_string('topic');

$table = new html_table();

if ($course->format == 'weeks') {
    $table->head  = array ($strweek, $strname);
    $table->align = array ('center', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array ($strtopic, $strname);
    $table->align = array ('center', 'left', 'left', 'left');
} else {
    $table->head  = array ($strname);
    $table->align = array ('left', 'left', 'left');
}

foreach ($webpas as $webpa) {
    if (!$webpa->visible) {
        //Show dimmed if the mod is hidden
        $link = '<a class="dimmed" href="view.php?id='.$webpa->coursemodule.'">'.format_string($webpa->name).'</a>';
    } else {
        //Show normal if the mod is visible
        $link = '<a href="view.php?id='.$webpa->coursemodule.'">'.format_string($webpa->name).'</a>';
    }

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array ($webpa->section, $link);
    } else {
        $table->data[] = array ($link);
    }
}

echo $OUTPUT->heading($strwebpas);
echo html_writer::table($table);

/// Finish the page

echo $OUTPUT->footer($course);