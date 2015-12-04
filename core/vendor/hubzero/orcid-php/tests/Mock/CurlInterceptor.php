<?php
/**
 * @package   orcid-php
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Http;

function curl_init()
{
    echo 'INIT';
}
function curl_exec($ch)
{
    echo 'EXEC';
}
function curl_close($ch)
{
    echo 'CLOSE';
}
function curl_setopt($ch, $option, $value)
{
    echo $value;
}
