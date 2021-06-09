<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/auth/catadmin/lib.php');

require_once($CFG->dirroot . '/auth/saml2/classes/idp_parser.php');
require_once($CFG->dirroot . '/login/lib.php');

/**
 * Class auth_plugin_catadmin
 *
 * @copyright Alex Morris <alex.morris@catalyst.net.nz>
 */
class auth_plugin_catadmin extends auth_plugin_base {

    /**
     * @var array Our hard coded values
     */
    public $defaults = [
        'idpname' => '',
        'idpmetadata' => '',
        'multiidp' => false,
        'metadataentities' => '',
        'debug' => 0,
        'idpattr' => 'uid',
        'mdlattr' => 'username',
        'tolower' => 0,
        'spmetadatasign' => true,
        'logtofile' => 0,
        'logdir' => '/tmp/',
        'groups' => 'elearning',
        'groupattribute' => 'affiliations',
        // Data mapping defaults.
        'field_map_firstname' => 'catalystFirstName',
        'field_map_lastname' => 'catalystLastName',
        'field_map_email' => 'catalystEmail',
    ];

    public function __construct() {
        $this->authtype = 'catadmin';
    }

    public function initialise() {
        global $CFG;
        $mdl = new moodle_url($CFG->wwwroot);
        $this->spname = $mdl->get_host();
        $this->certpem = $this->get_file("{$this->spname}.pem");
        $this->certcrt = $this->get_file("{$this->spname}.crt");
        $this->config = (object) array_merge($this->defaults, (array) get_config('auth_catadmin'));
        // Now lets go back over the defaults and decide if any should replace empty values.
        // This is necessary as defaults can contain items not in config,
        // So we can't just do a single conditional run based on just one of the arrays.
        foreach ($this->defaults as $key => $value) {
            if (empty($this->config->$key)) {
                $this->config->$key = $value;
            }
        }

        $parser = new auth_saml2\idp_parser();
        $metadata = get_config('auth_catadmin', 'idpmetadata');
        $metadata = str_replace(PHP_EOL, ' ', $metadata);
        $this->metadatalist = $parser->parse($metadata);

        $this->metadataentities = auth_catadmin_get_idps(true);

        // Check if we have mutiple IdPs configured.
        // If we have mutliple metadata entries set multiidp to true.
        $this->multiidp = false;

        if (count($this->metadataentities) > 1) {
            $this->multiidp = true;
        } else {
            // If we have mutliple IdP entries for a metadata set multiidp to true.
            foreach ($this->metadataentities as $idpentities) {
                if (count($idpentities) > 1) {
                    $this->multiidp = true;
                }
            }
        }

        $this->defaultidp = auth_catadmin_get_default_idp();
    }

    private function log($msg) {
        if ($this->config->debug) {
            // @codingStandardsIgnoreStart
            error_log('auth_catadmin: ' . $msg);
            // @codingStandardsIgnoreEnd

            // If SSP logs to tmp file we want these to also go there.
            if ($this->config->logtofile) {
                require_once('setup.php');
                SimpleSAML\Logger::debug('auth_saml2: ' . $msg);
            }
        }
    }

    public function user_login($username, $password) {
        return false;
    }

    public function is_internal() {
        return false;
    }

    public function can_be_manually_set() {
        return true;
    }

    /**
     * Checks to see if the plugin has been configured and the IdP/SP metadata files exist.
     *
     * @return bool
     */
    public function is_configured() {
        $file = $this->certcrt;
        if (!file_exists($file)) {
            $this->log(__FUNCTION__ . ' file not found, ' . $file);
            return false;
        }

        $file = $this->certpem;
        if (!file_exists($file)) {
            $this->log(__FUNCTION__ . ' file not found, ' . $file);
            return false;
        }

        $eids = $this->metadataentities;
        foreach ($eids as $metadataid => $idps) {
            $file = $this->get_file_idp_metadata_file($metadataid);
            if (!file_exists($file)) {
                $this->log(__FUNCTION__ . ' file not found, ' . $file);
                return false;
            }
        }

        if (empty(get_config('auth_catadmin', 'privatekeypass'))) {
            set_config('privatekeypass', get_site_identifier(), 'auth_catadmin');
        }

        return true;
    }

    public function pre_loginpage_hook() {
        if (is_enabled_auth('catadmin')) {
            $this->loginpage_hook();
        }
    }

    public function loginpage_hook() {
        if (!is_enabled_auth('catadmin')) {
            return;
        }

        if ($this->should_login_redirect()) {
            redirect(new moodle_url('/auth/catadmin/login.php'));
        }
    }

    private function should_login_redirect() {
        global $SESSION;

        // Do not redirect if we aren't configured to.
        if (get_config('auth_catadmin', 'ipsubnets') == null) {
            return false;
        }

        // Do not redirect if we aren't in one of the specified IP ranges.
        if (!remoteip_in_list(get_config('auth_catadmin', 'ipsubnets'))) {
            return false;
        }

        $catadmin = optional_param('catadmin', null, PARAM_BOOL);
        $noredirect = optional_param('noredirect', 0, PARAM_BOOL);
        if (!empty($noredirect)) {
            $catadmin = 0;
        }

        // Never redirect on POST.
        if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST')) {
            return false;
        }

        // Never redirect if requested so.
        if ($catadmin === 0) {
            $SESSION->catadmin = $catadmin;
            return false;
        }

        // Never redirect if has error.
        if (!empty($_GET['SimpleSAML_Auth_State_exceptionId'])) {
            return false;
        }

        // If ?catadmin=on go directly to IdP.
        if ($catadmin == 1) {
            return true;
        }

        // Check whether we have already logged out of IdP. This sets a cookie,
        // That persists until next login.
        if (!NO_MOODLE_COOKIES) {
            if (!empty($_COOKIE['catadmin_logout_cookie'])) {
                if ($_COOKIE['catadmin_logout_cookie'] > (time() - DAYSECS)) {
                    return false;
                }
            }
        }

        // Check whether we've skipped catadmin already.
        // This is here because loginpage_hook is called again during form
        // submission (all of login.php is processed) and ?catadmin=off is not
        // preserved forcing us to the IdP.
        if ((isset($SESSION->catadmin) && $SESSION->catadmin == 0)) {
            return false;
        }

        // Remove force in session as we are redirecting to login page.
        if (isset($SESSION->catadmin)) {
            unset($SESSION->catadmin);
        }

        return true;
    }

    /**
     * Return HTML code of admins table.
     *
     * @return string
     */
    public function get_admins_table() {
        global $CFG, $DB;

        $adminstable = '';

        // Get all catalyst users.
        $sql = "SELECT *
                FROM {user}
                WHERE (auth = 'catadmin'
                  OR " . $DB->sql_like('email', '?') . ")
                AND NOT (deleted = 1
                  OR suspended = 1)
                ORDER BY currentlogin DESC";

        $catadmins = $DB->get_records_sql($sql, array('%@catalyst%'));

        if (!empty($catadmins)) {
            $table = new html_table();
            $table->attributes['class'] = 'generaltable catalystadmins';
            $table->head = array(
                get_string('username', 'auth_catadmin'),
                get_string('userprofile', 'auth_catadmin'),
                get_string('lastlogin', 'auth_catadmin'),
                get_string('ipaddress', 'auth_catadmin'),
                get_string('loghistory', 'auth_catadmin'),
            );

            foreach ($catadmins as $catadmin) {
                $profileurl = new moodle_url('/user/view.php', array('id' => $catadmin->id));
                $logsurl = new moodle_url('/report/log/index.php', array(
                        'user' => $catadmin->id,
                        'chooselog' => '1',
                        'id' => '1',
                        'edulevel' => '-1',
                        'logreader' => 'logstore_standard',
                    )
                );

                $row = array();
                $row[] = format_string($catadmin->username);
                $row[] = html_writer::link($profileurl, fullname($catadmin)) . '<br>' . $catadmin->email;
                $row[] = userdate($catadmin->currentlogin, get_string('strftimerecentfull'));
                $row[] = $catadmin->lastip;
                $row[] = html_writer::link($logsurl, get_string('userlogs', 'auth_catadmin'));

                $table->data[] = $row;
            }

            $adminstable = html_writer::table($table);
        }

        // Append link to Deep admin report.
        $url = new moodle_url('/auth/catadmin/admin_report.php');
        $adminstable .= html_writer::link($url, get_string('adminreport', 'auth_catadmin'));

        return $adminstable;
    }

    public function saml_login() {
        // @codingStandardsIgnoreStart
        global $CFG, $SESSION, $catadminsaml;
        // @codingStandardsIgnoreEnd

        if (!$this->is_configured()) {
            return;
        }

        require('setup.php');
        require_once("$CFG->dirroot/login/lib.php");
        require_once($CFG->dirroot . '/user/lib.php');

        // Set the default IdP to be the first in the list. Used when dual login is disabled.
        $arr = array_reverse($catadminsaml->metadataentities);
        $metadataentities = array_pop($arr);
        $idpentity = array_pop($metadataentities);
        $idp = md5($idpentity->entityid);

        // Specify the default IdP to use.
        $SESSION->catadminidp = $idp;

        // We store the IdP in the session to generate the config/config.php array with the default local SP.
        $idpalias = optional_param('idpalias', '', PARAM_TEXT);
        if (!empty($idpalias)) {
            $idpfound = false;

            foreach ($catadminsaml->metadataentities as $idpentities) {
                foreach ($idpentities as $md5idpentityid => $idpentity) {
                    if ($idpalias == $idpentity->alias) {
                        $SESSION->catadminidp = $md5idpentityid;
                        $idpfound = true;
                        break 2;
                    }
                }
            }

            if (!$idpfound) {
                $this->error_page(get_string('noidpfound', 'auth_catadmin', $idpalias));
            }
        } else if (!empty(optional_param('idp', '', PARAM_RAW))) {
            $SESSION->catadminidp = md5(optional_param('idp', '', PARAM_RAW));
        } else if (!is_null($catadminsaml->defaultidp)) {
            $SESSION->catadminidp = md5($catadminsaml->defaultidp->entityid);
        }

        if (!NO_MOODLE_COOKIES) {
            $cookiename = 'MOODLECATIDP_' . $CFG->sessioncookie;
            setcookie($cookiename, $SESSION->catadminidp, time() + (DAYSECS * 60), $CFG->sessioncookiepath,
                $CFG->sessioncookiedomain, is_moodle_cookie_secure(), $CFG->cookiehttponly);
        }

        $auth = new \SimpleSAML\Auth\Simple($this->spname);

        $auth->requireAuth();
        $attributes = $auth->getAttributes();

        $this->saml_login_complete($attributes);
    }

    public function remove_admin_from_user($userid) {
        global $CFG;
        $admins = explode(',', $CFG->siteadmins);
        $key = array_search($userid, $admins);
        if ($key !== false && $key !== null) {
            unset($admins[$key]);
        }
        $logstringold = $CFG->siteadmins;
        $newadmins = implode(',', $admins);
        if ($newadmins != $logstringold) {
            set_config('siteadmins', $newadmins);
            add_to_config_log('siteadmins', $logstringold, $newadmins, 'core');
        }
    }

    public function give_user_admin($userid) {
        global $CFG;
        $admins = array();
        foreach (explode(',', $CFG->siteadmins) as $admin) {
            $admin = (int) $admin;
            if ($admin) {
                $admins[$admin] = $admin;
            }
        }
        $admins[$userid] = $userid;
        $logstringold = $CFG->siteadmins;
        $newadmins = implode(',', $admins);
        if ($newadmins != $logstringold) {
            set_config('siteadmins', implode(',', $admins));
            add_to_config_log('siteadmins', $logstringold, implode(',', $admins), 'core');
        }
    }

    public function prelogout_hook() {
        global $CFG, $SESSION, $USER;

        if ($USER->auth == 'catadmin') {
            $cookiename = 'MOODLECATIDP_' . $CFG->sessioncookie;
            if (!empty($_COOKIE[$cookiename])) {
                $SESSION->catadminidp = $_COOKIE[$cookiename];

                require('setup.php');
                $this->initialise();

                $auth = new \SimpleSAML\Auth\Simple($this->spname);
                setcookie($cookiename, '', time() - HOURSECS, $CFG->sessioncookiepath, $CFG->sessioncookiedomain,
                    is_moodle_cookie_secure(), $CFG->cookiehttponly);

                // Now set a cookie for signing out of IdP that prevents redirects back to IdP.
                setcookie('catadmin_logout_cookie', time(), time() + 10 * YEARSECS, $CFG->sessioncookiepath,
                    $CFG->sessioncookiedomain, is_moodle_cookie_secure(), $CFG->cookiehttponly);

                if ($auth->isAuthenticated()) {
                    $auth->logout();
                }
            }
        }
    }

    public function error_page($msg) {
        global $PAGE, $OUTPUT;

        $logouturl = new moodle_url('/auth/catadmin/logout.php');

        $PAGE->set_context(context_system::instance());
        $PAGE->set_url('/');
        echo $OUTPUT->header();
        echo $OUTPUT->box($msg);
        echo html_writer::link($logouturl, get_string('logout'));
        echo $OUTPUT->footer();
        exit;
    }

    public function get_file_sp_metadata_file() {
        return $this->get_file($this->spname . '.xml');
    }

    public function get_file_idp_metadata_file($url) {
        if (is_object($url)) {
            $url = (array) $url;
        }
        if (is_array($url)) {
            $url = array_keys($url);
            $url = implode("\n", $url);
        }

        $filename = md5($url) . '.idp.xml';
        return $this->get_file($filename);
    }

    public function get_file($file) {
        return $this->get_catadmin_directory() . '/' . $file;
    }

    public function get_catadmin_directory() {
        global $CFG;
        $directory = "{$CFG->dataroot}/catadmin";
        if (!file_exists($directory)) {
            mkdir($directory);
        }
        return $directory;
    }

    /**
     * Checks the field map config for values that update onlogin or when a new user is created
     * and returns true when the fields have been merged into the user object.
     *
     * @param $attributes
     * @param bool $newuser
     * @return bool true on success
     */
    public function update_user_profile_fields(&$user, $attributes, $newuser = false) {
        global $CFG;

        $mapconfig = get_config('auth_catadmin');
        $allkeys = array_keys(get_object_vars($mapconfig));
        $update = false;

        foreach ($allkeys as $key) {
            if (preg_match('/^field_updatelocal_(.+)$/', $key, $match)) {
                $field = $match[1];
                if (!empty($mapconfig->{'field_map_'.$field})) {
                    $attr = $mapconfig->{'field_map_'.$field};
                    $updateonlogin = $mapconfig->{'field_updatelocal_'.$field} === 'onlogin';

                    if ($newuser || $updateonlogin) {
                        // Basic error handling, check to see if the attributes exist before mapping the data.
                        if (array_key_exists($attr, $attributes)) {
                            // Handing an empty array of attributes.
                            if (!empty($attributes[$attr])) {
                                // Custom profile fields have the prefix profile_field_ and will be saved as profile field data.
                                $user->$field = $attributes[$attr][0];
                                $update = true;
                            }
                        }
                    }
                }
            }
        }

        if ($update) {
            require_once($CFG->dirroot . '/user/lib.php');
            if ($user->description === true) {
                // Function get_complete_user_data() sets description = true to avoid keeping in memory.
                // If set to true - don't update based on data from this call.
                unset($user->description);
            }
            // We should save the profile fields first so they are present and
            // then we update the user which also fires events which other
            // plugins listen to so they have the correct user data.
            profile_save_data($user);
            user_update_user($user, false);
        }

        return $update;
    }

    public function loginpage_idp_list($wantsurl) {
        if (empty($_COOKIE['catadmin_logout_cookie'])) {
            return [];
        }

        $idpicon = new pix_icon('i/user', 'Login');
        $idpurl = new moodle_url('/auth/catadmin/login.php', ['wants' => $wantsurl]);
        $idpname = get_string('pluginname', 'auth_catadmin');

        return [[
            'url'  => $idpurl,
            'icon' => $idpicon,
            'iconurl' => null,
            'name' => $idpname,
        ]];
    }

    /**
     * Finish the SAML login, now that the attributes are set.
     *
     * @param array $attributes
     * @return void
     */
    public function saml_login_complete($attributes) {
        global $CFG, $DB, $SESSION, $USER;

        $attr = $this->config->idpattr;
        if (empty($attributes[$attr])) {
            $this->error_page(get_string('noattribute', 'auth_catadmin', $attr));
        }

        // Check if user is in same group as site.
        $groups = explode(',', $this->config->groups);
        $ingroup = false;
        foreach ($groups as $group) {
            if (!empty($group)) {
                if (!empty($attributes[$this->config->groupattribute])
                        && in_array($group, $attributes[$this->config->groupattribute])) {
                    $ingroup = true;
                }
            }
        }
        if (!$ingroup && !empty($groups[0])) {
            $this->error_page(get_string('noaccess', 'auth_catadmin', $group));
        }

        $user = null;
        foreach ($attributes[$attr] as $key => $uid) {
            $suffix = $this->config->suffix;
            if ($this->config->mdlattr === 'username') {
                // If we are matching on username, we need to check it *with* suffix.
                $uid .= $suffix;
            }
            if ($this->config->tolower) {
                $this->log(__FUNCTION__ . " to lowercase for $key => $uid");
                $uid = strtolower($uid);
            }
            if ($user = $DB->get_record('user', array($this->config->mdlattr => $uid, 'deleted' => 0))) {
                if ($user->auth == 'catalyst') {
                    $user->auth = 'catadmin';
                    user_update_user($user, false);
                }
                if ($user->auth != 'catadmin') {
                    $this->log(__FUNCTION__ . " user '$uid' is not authtype catadmin but attempted to log in!");
                    $this->error_page(get_string('incorrectauthtype', 'auth_catadmin'));
                }
                continue;
            }
        }

        $newuser = false;
        if (!$user) {
            $email = $attributes[$this->config->field_map_email][0];
            if ($this->config->mdlattr !== 'username') {
                // We aren't matching users on username. Here we should attach the suffix before saving $uid as username.
                $uid .= $suffix;
            }
            if (!empty($email)) {
                // Make a case-insensitive query for the given email address.
                $select = $DB->sql_equal('email', ':email', false) . ' AND mnethostid = :mnethostid AND deleted = :deleted';
                $params = array(
                    'email' => $email,
                    'mnethostid' => $CFG->mnet_localhost_id,
                    'deleted' => 0
                );

                // If there are other user(s) that already have the same email, display an error.
                if ($DB->record_exists_select('user', $select, $params)) {
                    $this->log(__FUNCTION__ . " user '$uid' can't be autocreated as email '$email' is taken");
                    $this->error_page(get_string('emailtaken', 'auth_catadmin', $email));
                } else {
                    $this->log(__FUNCTION__ . " user '$uid' is not in moodle so autocreating");
                    $user = create_user_record($uid, '', 'catadmin');
                    $newuser = true;
                }
            } else {
                $this->log(__FUNCTION__ . " user '$uid' is not in moodle so error");
                $this->error_page(get_string('nouser', 'auth_catadmin', $uid));
            }
        } else {
            // Revive users who are suspended.
            if ($user->suspended) {
                $user->suspended = 0;
                user_update_user($user, false);
            }
            // Make sure all user data is fetched.
            $user = get_complete_user_data('username', $user->username);
            $this->log(__FUNCTION__ . ' found user ' . $user->username);
        }

        // We have a user now. Apply custom mappings.
        $this->update_user_profile_fields($user, $attributes, $newuser);

        // Deny user admin rights based on SAML attribute.
        if (isset($attributes['denyAdmin'])) {
            $this->remove_admin_from_user($user->id);
        } else {
            // Only add as admin if config has not been set or it is turned on.
            if (get_config('auth_catadmin', 'autoadmin') == null || get_config('auth_catadmin', 'autoadmin') === 'on') {
                $this->give_user_admin($user->id);
            } else {
                $this->remove_admin_from_user($user->id);
            }
        }

        // Make sure all user data is fetched.
        $user = get_complete_user_data('username', $user->username);

        complete_user_login($user);
        $USER->loggedin = true;
        $USER->site = $CFG->wwwroot;
        set_moodle_cookie($USER->username);

        // Clear any autologin prevention cookies now that we have a successful login.
        setcookie('catadmin_logout_cookie', -1, time() + 10 * YEARSECS, $CFG->sessioncookiepath,
                $CFG->sessioncookiedomain, is_moodle_cookie_secure(), $CFG->cookiehttponly);

        $urltogo = core_login_get_return_url();
        // If we are not on the page we want, then redirect to it.
        if (qualified_me() !== $urltogo) {
            $this->log(__FUNCTION__ . " redirecting to $urltogo");
            // Re-store the url in Session to be used by other post login hooks.
            $SESSION->wantsurl = $urltogo;
            redirect($urltogo);
            exit;
        } else {
            $this->log(__FUNCTION__ . " continuing onto " . qualified_me());
        }

        return;
    }
}
