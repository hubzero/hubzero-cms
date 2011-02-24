<?php

/**
 * @package    php_mailman
 * @author     Mikael Korpela <mikael@ihminen.org>
 * @copyright  Copyright (c) 2010 {@link http://www.ihminen.org Mikael Korpela}
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 *
 * This is just a basic skeleton to send some most used commands to Mailman.
 * You shouldn't echo functions out, because cURL returns an admin view.
 * Though you can use something like {@link http://simplehtmldom.sourceforge.net/ Simple HTML DOM}
 * to get some feedback from this class. Use at your own risk, though.
 *
 * Created: 2010-04-11
 *
 * Read more about method:
 * http://wiki.list.org/pages/viewpage.action?pageId=4030567
 *
 * Modifications of class made by Dave Benham, Sep 2010, NEESComm IT
 *
 */
class php_mailman {

    /**
     * SETTINGS
     * It's best to set these when construction the class
     */
    public $domain = '';            // Default listname
    public $adminpasswd = '';       // Default list admin password
    public $listname = '';          // Domain of your server
    public $protocol = 'http';      // Protocol of your server (http | https | ...)
    public $listlanguage = 'en';    // Default list language
    public $notifyowner = '0';      // Send notifications to list owner on (un)subscribe, 0 = no | 1 = yes
    public $notifyuser = '0';       // Send notifications to user on (un)subscribe, 0 = no | 1 = yes
    public $digest = '0';           // Digest status, 0 = no | 1 = yes

    // SETTINGS END
    // Tags (like "<protocol>") are replaced by class.
    // Don't change them unless you know what you are doing.
    // Subscribe:
    const URL_SUBSCRIBE = '<protocol>://<domain>/mailman/admin/<listname>/members/add?subscribe_or_invite=0&send_welcome_msg_to_this_batch=<notify-user>&notification_to_list_owner=<notify-owner>&subscribees_upload=<email-address>&adminpw=<adminpassword>';

    // Unsubscribe:
    const URL_UNSUBSCRIBE = '<protocol>://<domain>/mailman/admin/<listname>/members/remove?send_unsub_ack_to_this_batch=<notify-user>&send_unsub_notifications_to_list_owner=<notify-owner>&unsubscribees_upload=<email-address>&adminpw=<adminpassword>';

    // Set digest (you have to first subscribe them using URL above, then set digest):
    const URL_SET_DIGEST = '<protocol>://<domain>/mailman/admin/<listname>/members?user=<email-address>&<email-address>_digest=<digest>&setmemberopts_btn=Submit%20Your%20Changes&allmodbit_val=0&<email-address>_language=<list-language>&<email-address>_nodupes=1&adminpw=<adminpassword>';

    // List a member:
    const URL_LIST_A_MEMBER = '<protocol>://<domain>/mailman/admin/<listname>/members?findmember=<email-address>&setmemberopts_btn&adminpw=<adminpassword>';

    // List lists:
    const URL_LIST_LISTS = '<protocol>://<domain>/mailman/admin/';

    /**
     * Set variables on construct
     */
    public function __construct($domain='', $adminpasswd='', $listname='', $protocol='', $listlanguage='', $notifyowner='', $notifyuser='', $digest='') {

        if ($domain != '')
            $this->domain = $domain;

        if ($adminpasswd != '')
            $this->adminpasswd = $adminpasswd;

        if ($listname != '')
            $this->listname = $listname;

        if ($protocol != '')
            $this->protocol = $protocol;

        if ($listlanguage != '')
            $this->listlanguage = $listlanguage;

        if ($notifyowner != '')
            $this->notifyowner = $notifyowner;

        if ($digest != '')
            $this->digest = $digest;
    }

    /**
     * Make requests for the API
     */
    public function api($action="") {

        // cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $action);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        //echo $action;
        return $output;
    }

    /**
     * Set domain
     */
    public function set_domain($domain='') {
        $this->domain = $domain;
    }

    /**
     * Set admin password
     */
    public function set_adminpasswd($adminpasswd='') {
        $this->adminpasswd = $adminpasswd;
    }

    /**
     * Set listname
     */
    public function set_list($listname='') {
        $this->listname = $listname;
    }

    /**
     * Set list language
     */
    public function set_language($listlanguage='en') {
        $this->listlanguage = $listlanguage;
    }

    /**
     * Set protocol
     */
    public function set_protocol($protocol='http') {
        $this->protocol = $protocol;
    }

    /**
     * Set notify owner
     */
    public function set_notifyowner($notifyowner='0') { // 0 | 1
        $this->notifyowner = $notifyowner;
    }

    /**
     * Set digest
     */
    public function set_digest($digest='0') { // 0 | 1
        echo "digest: " . $digest;
        $this->digest = $digest;
    }

    /**
     * Subscribe
     */
    public function subscribe($email='') {
        if ($email != '')
            return $this->api($this->prepare_url(self::URL_SUBSCRIBE, $email));
        else
            return false;
    }

    /**
     * Unsubscribe
     */
    public function unsubscribe($email='') {
        if ($email != '')
            return $this->api($this->prepare_url(self::URL_UNSUBSCRIBE, $email));
        else
            return false;
    }

    /**
     * Digest
     */
    public function digest($email='') {
        if ($email != '')
            return $this->api($this->prepare_url(self::URL_SET_DIGEST, $email));
        else
            return false;
    }

    /**
     * Set a member
     */
    public function list_member($email='') {
        if ($email != '')
            return $this->api($this->prepare_url(self::URL_LIST_A_MEMBER, $email));
        else
            return false;
    }

    /**
     * List lists
     */
    public function list_lists() {
        return $this->api(self::URL_LIST_LISTS);
    }

    /**
     * Prepare URL for the API
     */
    private function prepare_url($url='', $email='') {

        // Tags
        $tags = array(
            '<protocol>',
            '<domain>',
            '<listname>',
            '<adminpassword>',
            '<email-address>',
            '<notify-owner>',
            '<notify-user>',
            '<list-language>',
            '<digest>'
        );

        // Tags replaced with variables:
        $replacements = array(
            $this->protocol,
            $this->domain,
            $this->listname,
            $this->adminpasswd,
            $email,
            $this->notifyowner,
            $this->notifyuser,
            $this->listlanguage,
            $this->digest
        );

        // Replace tags with variables
        return str_replace($tags, $replacements, $url);
    }

}

?>