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
require_once("../../../inc/includes.php");

global $DB;

$criteria = "SELECT category, split, level FROM glpi_plugin_splitdropdown";
$iterators = $DB->request($criteria);

foreach ($iterators as $iterator) {
    $list[$iterator["category"]] = array(
        "category" => $iterator["category"],
        "split" => $iterator["split"],
        "level" => $iterator["level"]
    );
}

$options = array(
    "inNewTicket" => (preg_match("/helpdesk.public.php/", $_SERVER["HTTP_REFERER"]) || preg_match("/tracking.injector.php/", $_SERVER["HTTP_REFERER"]) || preg_match("/ticket.form.php/", $_SERVER["HTTP_REFERER"])) && !preg_match("/[?]id=/", $_SERVER["HTTP_REFERER"]),
);

if(!empty($list)) {
    foreach ($list as $element) {
        $category = $list[$element["category"]]["category"];
        $level = $list[$element["category"]]["level"];
        $split = $list[$element["category"]]["split"];
    
        $table = getTableForItemType($category);
        $itemType_id = getForeignKeyFieldForTable($table);
        $itemType = getSingular(ucfirst($category));
    
        $options[$element["category"]] = array(
            "options" => $list[$element["category"]],
            "values" => array(
                "table" => $table,
                "itemType_id" => $itemType_id,
                "itemType" => $itemType,
            ),
        );
    }
}

echo json_encode($options);
