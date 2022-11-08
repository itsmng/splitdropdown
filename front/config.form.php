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
include('../../../inc/includes.php');
include_once('../inc/splitdropdown.class.php');
include_once('../inc/config.class.php');

$plugin  = new Plugin();

if ($plugin->isActivated('splitdropdown')) {
    $splitdropdown = new SplitDropdown();
    $config = new PluginSplitdropdownConfig();

    if (isset($_POST["splitOption"]) && $_POST["splitOption"] === "2") $_POST["splitOption"] = "0";
    if (isset($_POST["splitOption"], $_POST["levelOption"]) && $_POST["splitOption"] === "1" && $_POST["levelOption"] === "") $_POST["levelOption"] = "1";
    
    Html::header('List Category', $_SERVER["PHP_SELF"], "config", "plugins");

    $config->showFormulaire();

    echo "<br>";

    if (isset($_POST['update'], $_POST["categoryOption"], $_POST["splitOption"], $_POST["levelOption"]) && $_POST["categoryOption"] != "0") {
        $splitdropdown->addToDatabase($_POST["categoryOption"], $_POST["splitOption"], $_POST["levelOption"]);
    }

    $config->showTable();
    echo "<br>";

    if (isset($_POST['update'], $_POST["categoryOption"], $_POST["splitOption"], $_POST["levelOption"]) && $_POST["splitOption"] != "0") {
        $config->showPreview($_POST["categoryOption"]);
    }
} else {
    global $CFG_GLPI;
    echo '<div class=\'center\'><br><br><img src=\'' . $CFG_GLPI['root_doc'] . '/pics/warning.png\' alt=\'warning\'><br><br>';
    echo '<b>' . __("Enable your plugin", "splitdropdown") . '</b></div>';
}

Html::footer();
