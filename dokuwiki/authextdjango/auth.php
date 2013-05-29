<?php
/**
 * DokuWiki Plugin authextdjango (Auth Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Robert Weidlich <dev@robertweidlich.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class auth_plugin_authextdjango extends DokuWiki_Auth_Plugin {


    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(); // for compatibility

        // FIXME set capabilities accordingly
        //$this->cando['addUser']     => false; // can Users be created?
        //$this->cando['delUser']     => false; // can Users be deleted?
        //$this->cando['modLogin']    => false; // can login names be changed?
        //$this->cando['modPass']     => false; // can passwords be changed?
        //$this->cando['modName']     => false; // can real names be changed?
        //$this->cando['modMail']     => false; // can emails be changed?
        //$this->cando['modGroups']   => false; // can groups be changed?
        //$this->cando['getUsers']    => false; // can a (filtered) list of users be retrieved?
        //$this->cando['getUserCount']=> false; // can the number of users be retrieved?
        $this->cando['getGroups']   => true; // can a list of available groups be retrieved?
        $this->cando['external']    => true; // does the module do external auth checking?
        $this->cando['logout']      => false; // can the user logout again? (eg. not possible with HTTP auth)

        // FIXME intialize your auth system and set success to true, if successful
        $this->success = true;
    }


    /**
     * Log off the current user [ OPTIONAL ]
     */
    //public function logOff() {
    //}

    /**
     * Do all authentication [ OPTIONAL ]
     *
     * @param   string  $user    Username
     * @param   string  $pass    Cleartext Password
     * @param   bool    $sticky  Cookie should not expire
     * @return  bool             true on successful auth
     */
    public function trustExternal($user, $pass, $sticky = false) {

        global $USERINFO;
        global $conf;
        $sticky ? $sticky = true : $sticky = false; //sanity check

        // do the checking here

	if( isset($_COOKIE['sessionid'])) {
     
            /**
            * get user info from django-database (only mysql at the moment)
            */
             
            $s_id = $_COOKIE['sessionid'];
            
            $url = $this->getConf('url');
             
            $request = curl_init($url . "/trust_external/");
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($request, CURLOPT_HEADER, 0);
            curl_setopt($request, CURLOPT_COOKIE, "sessionid=" . $s_id);
            curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($request);
            if (curl_errno($request) != 0) {
                msg("Some error occured during connecting to django. Login not possible");
                curl_close($request);
                return false;
            }
            curl_close($request);
            
            $data = json_decode($response, true);
            
            $USERINFO['name'] = $data['userfullname'];
            $USERINFO['pass'] = '';
            $USERINFO['mail'] = $data['useremail'];
            $groups[0] = 'user';
            $USERINFO['grps'] = $groups;
             
            $_SERVER['REMOTE_USER'] = $data['username'];
             
            $_SESSION[DOKU_COOKIE]['auth']['user'] = $data['username'];
            $_SESSION[DOKU_COOKIE]['auth']['info'] = $USERINFO;
            
            // msg("Successfully authed ". $data['username']);
            
            return true;
        }
         
        return false;
        
    
    }

    /**
     * Retrieve groups [implement only where required/possible]
     *
     * Set getGroups capability when implemented
     *
     * @param   int $start
     * @param   int $limit
     * @return  array
     */
    public function retrieveGroups($start = 0, $limit = 0) {
	// Performing SQL query
        $url = $this->getConf('url');
         
        $request = curl_init($url . "/retrieve_groups/");
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($request);
        if (curl_errno($request) != 0) {
        msg("Some error occured during connecting to django. Login not possible");
        curl_close($request);
        return false;
        }
        curl_close($request);
        
        $data = json_decode($response, true);
        
        // msg("group data: " . var_export($data, true));
        return $data;
    }

}

// vim:ts=4:sw=4:et:
