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

class tool_policy_handler extends policy_handler_base {
    private $user;

    public function __construct($user) {
        $this->user = $user;
    }

    public function handle() {
        return; // Actually we do nothing here.
    }

    public function export_for_template(\renderer_base $output) {
        $policies = \tool_policy\api::list_current_versions(\tool_policy\policy_version::AUDIENCE_LOGGEDIN);

        $data = (object) [
            'pluginbaseurl' => (new \moodle_url('/admin/tool/policy'))->out(false),
            'myurl' => (new \moodle_url('/admin/tool/policy/index.php'))->out(false),
            'sesskey' => sesskey(),
        ];

        $acceptances = \tool_policy\api::get_user_acceptances($this->user->id);
        foreach ($policies as $policy) {
            $policy->versionacceptance = \tool_policy\api::get_user_version_acceptance($this->user->id, $policy->id, $acceptances);
            if (!empty($policy->versionacceptance)) {
                $policy->url = new \moodle_url(
                                    '/admin/tool/policy/view.php',
                                    ['policyid' => $policy->policyid, 'returnurl' => qualified_me()]
                                );

                // The policy version has ever been agreed. Check if status = 1 to know if still is accepted.
                $policy->versionagreed = $policy->versionacceptance->status;

                $policyattributes = ['data-action' => 'view',
                    'data-versionid' => $policy->id,
                    'data-behalfid' => 0];
                $policy->policymodal = \html_writer::link($policy->url, $policy->name, $policyattributes);
                $policy->policylink = \html_writer::link($policy->url, $policy->name);
            }
        }

        $data->policies = array_values($policies);

        $data->title = get_string('pluginname', 'local_revoke_consent');

        return $data;
    }

    public function get_template() {
        return 'local_revoke_consent/tool_policy';
    }
}
