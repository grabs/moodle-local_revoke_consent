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

/**
 * @package    local
 * @subpackage revoke_consent
 * @author     Andreas Grabs <info@grabs-edv.de>
 * @copyright  2018 Andreas Grabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_revoke_consent;

defined('MOODLE_INTERNAL') || die;

class legacy_policy_handler extends policy_handler_base {
    private $user;
    private $agree;
    private $sitepolicymanager;
    private $sitepolicy;
    private $myurl;

    public function __construct($user) {
        global $FULLME;

        $this->myurl = $FULLME;
        $this->user = $user;
        $this->agree = optional_param('agree', false, PARAM_INT);

        $this->sitepolicymanager = new \core_privacy\local\sitepolicy\manager();
        $this->sitepolicy = $this->sitepolicymanager->get_embed_url();
    }

    public function handle() {
        global $DB, $CFG;

        if ($this->agree === 0) {
            $DB->set_field('user', 'policyagreed', 0, array('id' => $this->user->id));
            $logouturl = new \moodle_url($CFG->wwwroot.'/login/logout.php', array('sesskey' => sesskey()));
            redirect($logouturl, get_string('logoutafterrevoke', 'local_revoke_consent'));
        }
        if ($this->agree === 1) {
            redirect(new \moodle_url('/user/profile.php', ['id' => $this->user->id]));
        }

        if (empty($this->sitepolicy)) {
            // No active site policy.
            redirect($CFG->wwwroot);
        }
    }

    /**
     * Get the data for usage in a mustache template
     *
     * @param \renderer_base $output
     * @return void
     */
    public function export_for_template(\renderer_base $renderer) {
        global $CFG;

        require_once($CFG->libdir.'/filelib.php');
        require_once($CFG->libdir.'/resourcelib.php');

        $strpolicyagree = get_string('policyagree');
        $strpolicyagreement = get_string('policyagreement');
        $strpolicyagreementclick = get_string('policyagreementclick');

        $mimetype = mimeinfo('type', $this->sitepolicy);
        if ($mimetype == 'document/unknown') {
            // Fallback for missing index.php, index.html.
            $mimetype = 'text/html';
        }

        // We can not use our popups here, because the url may be arbitrary, see MDL-9823.
        $clicktoopen = '<a href="'.$this->sitepolicy.'" onclick="this.target=\'_blank\'">'.$strpolicyagreementclick.'</a>';

        $output = new \stdClass();

        $output->title = $strpolicyagreement;
        $output->noticebox = resourcelib_embed_general($this->sitepolicy, $strpolicyagreement, $clicktoopen, $mimetype);

        $formcontinue = new \single_button(new \moodle_url($FULLME, ['agree' => 1]), get_string('yes'));
        $formcancel = new \single_button(new \moodle_url($FULLME, ['agree' => 0]), get_string('no'));
        $output->confirm = $renderer->confirm($strpolicyagree, $formcontinue, $formcancel);

        return $output;
    }

    public function get_template() {
        return 'local_revoke_consent/legacy_policy';
    }
}

