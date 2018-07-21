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

require_once(__DIR__.'/../../config.php');

$showthispage = false;
// Lagacy policy handler but not defined policy.
if (empty($CFG->sitepolicyhandler) and !empty($CFG->sitepolicy)) {
    $showthispage = true;
}

if ($CFG->sitepolicyhandler == 'tool_policy') {
    $showthispage = true;
}

if (!$showthispage) {
    redirect($CFG->wwwroot);
}

require_login();
if (isguestuser()) {
    throw new \moodle_exception('Error: guest are not allowed here');
}

$userid = $USER->id;

$PAGE->set_context(\context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new \moodle_url($FULLME));
$PAGE->set_popup_notification_allowed(false);
$PAGE->navbar->add(get_string('profile'), new \moodle_url('/user/profile.php', ['id' => $userid]));
$PAGE->navbar->add(get_string('pluginname', 'local_revoke_consent'));

$renderer = $PAGE->get_renderer('local_revoke_consent');

if ($CFG->sitepolicyhandler == 'tool_policy') {
    $policyhandler = new \local_revoke_consent\tool_policy_handler($USER);
} else {
    $policyhandler = new \local_revoke_consent\legacy_policy_handler($USER);
}
$policyhandler->handle();

echo $renderer->header();
echo $renderer->render_from_template(
    $policyhandler->get_template(),
    $policyhandler->export_for_template($renderer)
);
echo $renderer->footer();
