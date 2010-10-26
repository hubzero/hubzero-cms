/**
 * $Id: editor_plugin.js 64 2009-06-01 10:23:25Z happynoodleboy $
 * @package      JCE
 * @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
 * @author		Ryan Demmer
 * @license      GNU/GPL
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
(function(){
    tinymce.create('tinymce.plugins.Browser', {
        init: function(ed, url){
            this.ed = ed;
        },
        browse: function(name, url, type, win){
            var ed = this.ed;
            ed.windowManager.open({
                file: ed.getParam('site_url') + 'index.php?option=com_jce&task=plugin&plugin=browser&file=browser&type=' + type,
                width: 750,
                height: 420,
                resizable: "yes",
                inline: "yes",
                close_previous: "no"
            }, {
                window: win,
                input: name,
                url: url,
                type: type
            });
            return false;
        },
        
        getInfo: function(){
            return {
                longname: 'Browser',
                author: 'Ryan Demmer',
                authorurl: 'http://www.joomlacontenteditor.net',
                infourl: 'http://www.joomlacontenteditor.net/index.php?option=com_content&amp;view=article&amp;task=findkey&amp;tmpl=component&amp;lang=en&amp;keyref=browser.about',
                version: '1.5.1'
            };
        }
    });
    
    // Register plugin
    tinymce.PluginManager.add('browser', tinymce.plugins.Browser);
})();