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

abstract class policy_handler_base implements \templatable {

    abstract public function __construct($user);

    /**
     * If there is something to do do it here.
     *
     * @return void
     */
    abstract public function handle();

    /**
     * Get the data for usage in a mustache template
     *
     * @param \renderer_base $output
     * @return void
     */
    abstract public function export_for_template(\renderer_base $renderer);

    abstract public function get_template();
}
