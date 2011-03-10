<?PHP // 

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "feedback", "view all", "index.php?id=$course->id", "");


/// Get all required strings

    $strfeedbacks = get_string("modulenameplural", "feedback");
    $strfeedback  = get_string("modulename", "feedback");


/// Print the header

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    }

    print_header("$course->shortname: $strfeedbacks", "$course->fullname", "$navigation $strfeedbacks", "", "", true, "", navmenu($course));

/// Get all the appropriate data

    if (! $feedbacks = get_all_instances_in_course("feedback", $course)) {
        notice("There are no feedbacks", "../../course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances (your module will probably extend this)

    $timenow = time();
    $strname  = get_string("name");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname);
        $table->align = array ("center", "left");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ("center", "left", "left", "left");
    } else {
        $table->head  = array ($strname);
        $table->align = array ("left", "left", "left");
    }

    foreach ($feedbacks as $feedback) {
        if (!$feedback->visible) {
            //Show dimmed if the mod is hidden
            $link = "<A class=\"dimmed\" HREF=\"view.php?id=$feedback->coursemodule\">$feedback->name</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id=$feedback->coursemodule\">$feedback->name</a>";
        }

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($feedback->section, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo "<br />";

    print_table($table);

/// Finish the page

    print_footer($course);

?>
