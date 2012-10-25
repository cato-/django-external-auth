<?php
/**
 * django auth backend
 *
 * Uses external Trust mechanism to check against a django session id
 *
 * @author    Andreas Gohr <andi@splitbrain.org>
 * @author    Robert Weidlich
 */
 
define('DOKU_AUTH', dirname(__FILE__));
define('AUTH_USERFILE',DOKU_CONF.'users.auth.php');
 
class auth_extdjango extends auth_basic {
	var $link = null;
 
	/**
	 * Constructor.
	 *
	 * Sets additional capabilities and config strings
	 * @author    Michael Luggen <michael.luggen at rhone.ch>
	 */
	function auth_extdjango(){
		global $conf;
		$this->cando['external'] = true;
		$this->cando['getGroups'] = true;
		$this->cando['logout'] = false;
	}
 
	/**
	 * Just checks against the django sessionid variable
	 */
	function trustExternal($user,$pass,$sticky=false){
		global $USERINFO;
		global $conf;
		$sticky ? $sticky = true : $sticky = false; //sanity check
 
		if( isset($_COOKIE['sessionid'])){
 
			/**
			 * get user info from django-database (only mysql at the moment)
			 */
 
			$s_id =  $_COOKIE['sessionid'];

			$url = $conf['auth']['extdjango']['url'];
 
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
 
	function retrieveGroups($start=0,$limit=0){
		// Performing SQL query
		$url = $conf['auth']['extdjango']['url'];
 
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
 
//Setup VIM: ex: et ts=4 :


