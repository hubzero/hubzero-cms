<?php //$Id: block_rss_client_error.php,v 1.7 2007/07/30 17:11:55 stronk7 Exp $
// Print an error page condition
require_once('../../config.php');

$error = required_param('error', PARAM_CLEAN);

print_header(get_string('error'),
              get_string('error'),
              get_string('error') );

print clean_text(urldecode($error));

print_footer();
?>
