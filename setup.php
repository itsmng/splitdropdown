<?php
/**
 * ---------------------------------------------------------------------
 * ITSM-NG
 * Copyright (C) 2022 ITSM-NG and contributors.
 *
 * https://www.itsm-ng.org
 *
 * based on GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2003-2014 by the INDEPNET Development Team.
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of ITSM-NG.
 *
 * ITSM-NG is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * ITSM-NG is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ITSM-NG. If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 */

/**
 * Init the hooks of the plugin - Needed
 * 
 * @return void
 */
function plugin_init_splitdropdown() {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['splitdropdown'] = true;
    $PLUGIN_HOOKS['add_javascript']['splitdropdown'] = 'js/ajax.js';

    if (Session::haveRight("profile", UPDATE)) {
        $PLUGIN_HOOKS['config_page']['splitdropdown'] = 'front/config.form.php';
    }
}

/**
 * Get the name and the version of the plugin - Needed
 */
function plugin_version_splitdropdown() {
    return array(
        'name'           => __("Split dropdown", "splitdropdown"),
        'version'        => '1.0.0',
        'author'         => 'ITSM Dev Team, Antoine ROBIN',
        'license'        => 'GPLv3+',
        'homepage'       => 'https://github.com/itsmng/splitdropdown',
        'minGlpiVersion' => '9.5.7'
    );
}


/**
 * Check if the prerequisites of the plugin are satisfied - Needed
 */
function plugin_splitdropdown_check_prerequisites() {
    // Check that the GLPI version is compatible
    if (version_compare(GLPI_VERSION, '9.5.7', 'lt')) {
        echo "This plugin Requires GLPI >= 9.5.7";
        return false;
    }

    return true;
}


/**
 *  Check if the config is ok - Needed
 */
function plugin_splitdropdown_check_config() {
    return true;
}
