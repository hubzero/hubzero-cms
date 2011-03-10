<?php  //$Id: filtersettings.php,v 1.1.2.2 2007/12/19 17:38:45 skodak Exp $

$settings->add(new admin_setting_configcheckbox('filter_multilang_force_old', 'filter_multilang_force_old',
                   get_string('multilangforceold', 'admin'), 0));

?>
