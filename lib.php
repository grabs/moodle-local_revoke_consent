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

defined('MOODLE_INTERNAL') || die;

function local_revoke_consent_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $CFG;

    $showlink = false;
    // Lagacy policy handler but not defined policy.
    if (empty($CFG->sitepolicyhandler) and !empty($CFG->sitepolicy)) {
        $showlink = true;
    }

    if ($CFG->sitepolicyhandler == 'tool_policy') {
        $showlink = true;
    }

    if (!$showlink) {
        return true;
    }

    if (!array_key_exists('privacyandpolicies', $tree->__get('categories'))) {
        // Create the category.
        $categoryname = get_string('privacyandpolicies', 'admin');
        $category = new core_user\output\myprofile\category('privacyandpolicies', $categoryname, 'contact');
        $tree->add_category($category);
    } else {
        // Get the existing category.
        $category = $tree->__get('categories')['privacyandpolicies'];
    }

    // Add "Policies and agreements" node only for current user or users who can accept on behalf of current user.
    $usercontext = \context_user::instance($user->id);
    if ($iscurrentuser) {
        $url = new moodle_url('/local/revoke_consent/index.php');
        $node = new core_user\output\myprofile\node(
            'privacyandpolicies',
            'local_revoke_consent',
            get_string('revokeconsent', 'local_revoke_consent'),
            null,
            $url
        );
        $category->add_node($node);
    }

    return true;
}
