<?php
/**
* @version		$Id: utils.php 46 2009-05-26 16:59:42Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
class Utils 
{
	/**
    * Append a / to the path if required.
    * @param string $path the path
    * @return string path with trailing /
    */
    function fixPath($path)
	{
        //append a slash to the path if it doesn't exists.
        if (!(substr($path, -1) == '/'))
            $path .= '/';
        return $path;
    }
   	/**
    * Concat two paths together. Basically $pathA+$pathB
    * @param string $pathA path one
    * @param string $pathB path two
    * @return string a trailing slash combinded path.
    */
    function makePath($a, $b)
	{
        $a = Utils::fixPath($a);
        if (substr($b, 0, 1)  ==  '/') {
            $b = substr($b, 1);
		}
        return $a.$b;
    }
	/**
	 * Makes file name safe to use
	 * @param string The name of the file (not full path)
	 * @return string The sanitised string
	 */
	function makeSafe($file)
	{
		jimport('joomla.filesystem.file');
		return JFile::makeSafe(preg_replace('#\s#', '_', $file));
	}
	/**
    * Format the file size, limits to Mb.
    * @param int $size the raw filesize
    * @return string formated file size.
    */
    function formatSize($size)
	{
        if ($size < 1024)
            return $size.' '.JText::_('BYTES');
        else if ($size >= 1024 && $size < 1024*1024)
            return sprintf('%01.2f',$size/1024.0).' '.JText::_('KB');
        else
            return sprintf('%01.2f', $size/(1024.0*1024)).' '.JText::_('MB');
    }
   	/**
  	* Format the date.
    * @param int $date the unix datestamp
    * @return string formated date.
    */
   	function formatDate($date, $format="%d/%m/%Y, %H:%M")
	{
		return strftime($format, $date);
   	}
	/**
	 * Get the modified date of a file
	 * 
	 * @return Formatted modified date
	 * @param string $file Absolute path to file
	 */
	function getDate($file) 
	{
		return Utils::formatDate(@filemtime($file));
	}
	/**
	 * Get the size of a file
	 * 
	 * @return Formatted filesize value
	 * @param string $file Absolute path to file
	 */
	function getSize($file) 
	{
		return Utils::formatSize(@filesize($file));
	}
	/**
     * Count the number of folders in a given folder
	 * @return integer Total number of folders
	 * @param string $path Abolute path to folder
	 */
    function countDirs($path)
	{
		jimport('joomla.filesystem.folder');
		$total = 0;
        if (JFolder::exists($path)) {
            $folders = JFolder::folders($path);
            $total = count($folders);
        }
        return $total;
    }
	/**
	 * Count the number of files in a folder
	 * @return integer File total 
	 * @param string $path Absolute path to folder
	 */
	function countFiles($path)
	{
		jimport('joomla.filesystem.file');
		$total = 0;
        if (JFolder::exists($path)) {
            $files = JFolder::files($path);
            $total = count($files);
            foreach ($files as $file) {
                if (strtolower($file) == 'index.html' || strtolower($file) == 'thumbs.db') {
                    $total = $total -1;
                }
            }
        }
        return $total;
    }
}
?>
