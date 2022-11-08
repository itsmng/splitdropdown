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

class SplitDropdown extends CommonDBTM {
        
    /**
     * canCreate
     *
     * @return void
     */
    static function canCreate() {
        return Session::haveRight('plugin_splitdropdown', CREATE);
    }
    
    /**
     * canView
     *
     * @return void
     */
    static function canView() {
        return Session::haveRight('plugin_splitdropdown', READ);
    }
    
    /**
     * addToDatabase
     *
     * @param  mixed $category
     * @param  mixed $split
     * @param  mixed $level
     * @return void
     */
    public function addToDatabase($category, $split, $level) {
        global $DB;
        $flag = false;

        $criteria = "SELECT category FROM glpi_plugin_splitdropdown";
        $iterators = $DB->request($criteria);

        foreach ($iterators as $iterator) {
            if (strcmp($iterator["category"], $category) === 0) {
                $flag = true;
                break;
            } else $flag = false;
        }

        if ($flag === false) $DB->request("INSERT INTO `glpi_plugin_splitdropdown` (category, split, level) VALUES ('" . $category . "','" . $split . "','" . $level . "')");
        else $DB->request("UPDATE `glpi_plugin_splitdropdown` SET split='" . $split . "', level='" . $level . "' WHERE category='" . $category . "'");
    }
}
