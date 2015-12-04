<?php
/**
 * @package   orcid-php
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid;

/**
 * ORCID profile API class
 **/
class Profile
{
    /**
     * The oauth object
     *
     * @var  object
     **/
    private $oauth = null;

    /**
     * The raw orcid profile
     *
     * @var  object
     **/
    private $raw = null;

    /**
     * Constructs object instance
     *
     * @param   object  $oauth  the oauth object used for making calls to orcid
     * @return  void
     **/
    public function __construct($oauth = null)
    {
        $this->oauth = $oauth;
    }

    /**
     * Grabs the ORCID iD
     *
     * @return  string
     **/
    public function id()
    {
        return $this->oauth->getOrcid();
    }

    /**
     * Grabs the orcid profile (oauth client must have requested this level or access)
     *
     * @return  object
     **/
    public function raw()
    {
        if (!isset($this->raw)) {
            $this->raw = $this->oauth->getProfile()->{'orcid-profile'};
        }

        return $this->raw;
    }

    /**
     * Grabs the ORCID bio
     *
     * @return  object
     **/
    public function bio()
    {
        $this->raw();

        return $this->raw->{'orcid-bio'};
    }

    /**
     * Grabs the users email if it's set and available
     *
     * @return  string|null
     **/
    public function email()
    {
        $this->raw();

        $email = null;
        $bio   = $this->bio();

        if (isset($bio->{'contact-details'})) {
            if (isset($bio->{'contact-details'}->email)) {
                if (is_array($bio->{'contact-details'}->email) && isset($bio->{'contact-details'}->email[0])) {
                    $email = $bio->{'contact-details'}->email[0]->value;
                }
            }
        }

        return $email;
    }

    /**
     * Grabs the raw name elements to create fullname
     *
     * @return  string
     **/
    public function fullName()
    {
        $this->raw();
        $details = $this->bio()->{'personal-details'};

        return $details->{'given-names'}->value . ' ' . $details->{'family-name'}->value;
    }
}
