<?php  // $Id: tabs.php,v 1.2.2.1 2007/11/19 20:27:50 skodak Exp $
    $row = $tabs = array();
    $row[] = new tabobject('groups',
                           $CFG->wwwroot.'/group/index.php?id='.$courseid,
                           get_string('groups'));

    if (!empty($CFG->enablegroupings)) {
        $row[] = new tabobject('groupings',
                               $CFG->wwwroot.'/group/groupings.php?id='.$courseid,
                               get_string('groupings', 'group'));
    }

    $row[] = new tabobject('overview',
                           $CFG->wwwroot.'/group/overview.php?id='.$courseid,
                           get_string('overview', 'group'));
    $tabs[] = $row;
    echo '<div class="groupdisplay">';
    print_tabs($tabs, $currenttab);
    echo '</div>';
?>
