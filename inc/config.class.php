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

class PluginSplitdropdownConfig extends CommonDBTM {

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
     * Open HTML field wrapper
     *
     * @param string $label Field label
     *
     * @return void
     */
    private function startField(string $label, string $id = '') {
        echo "<tr class='tab_bg_1' $id>";
        echo "<th>";
        echo $label;
        echo "</th>";
        echo "<td colspan='3'>";
    }

    /**
     * Close HTML field wrapper
     *
     * @return void
     */
    private function endField() {
        echo "</td>";
        echo "</tr>";
    }

    /**
     * showFormulaire
     *
     * @return void
     */
    public function showFormulaire() {
        $ITIL_CATEGORY = new ITILCategory();
        $LOCATION = new Location();

        $categoryOptions = array(
            get_class($ITIL_CATEGORY) => $ITIL_CATEGORY->getTypeName(),
            get_class($LOCATION) => $LOCATION->getTypeName(),
        );

        $splitOptions = array(
            "2" => __("No"),
            "1" => __("Yes"),
        );

        $form = [
            'action' => $this->getFormURL(),
            'buttons' => [
                [
                    'name' => 'update',
                    'value' => __('Add'),
                    'class' => 'btn btn-secondary',
                ],
            ],
            'content' => [
                __('Add splitted dropdown', 'splitdropdown') => [
                    'visible' => true,
                    'inputs' => [
                        __('Dropdown') => [
                            'type' => 'select',
                            'name' => 'categoryOption',
                            'values' => [Dropdown::EMPTY_VALUE] + $categoryOptions,
                            'required' => true,
                        ],
                        __('Enable split', 'splitdropdown') => [
                            'type' => 'select',
                            'name' => 'splitOption',
                            'values' => [Dropdown::EMPTY_VALUE] + $splitOptions,
                            'required' => true,
                        ],
                        __('Level') => [
                            'type' => 'number',
                            'name' => 'levelOption',
                            'required' => true,
                        ],
                    ],
                ]
            ],
        ];
        renderTwigForm($form);
    }

    /**
     * showTable
     *
     * @return void
     */
    public function showTable() {
        global $DB;

        $criteria = "SELECT * FROM `glpi_plugin_splitdropdown`";
        $iterators = $DB->request($criteria);

        echo "<table class='tab_cadre' cellpadding='5'>";
        echo "<tr><th colspan='5'>" . __("Splitted dropdowns", "splitdropdown") . "</th></tr>";

        echo "<tr>";
        echo "<th style='width:20%'>".__("Dropdown")."</th>";
        echo "<th style='width:20%'>".__("Enable split", "splitdropdown")."</th>";
        echo "<th style='width:20%'>".__("Level")."</th>";
        echo "<th style='width:20%'>".__("Delete")."</th>";
        echo "</tr>";

        $split = [
            0 => __("No"),
            1 => __("Yes")
        ];

        foreach ($iterators as $iterator) {
            echo "<tr>";
            echo "<td>" . $iterator["category"] . "</td>";
            echo "<td>" . $split[$iterator["split"]] . "</td>";
            echo "<td>" . $iterator["level"] . "</td>";
            echo "<td><a style='font-size: 15px'class='fas fa-times-circle' href='../ajax/delete.database.php?id=" . $iterator["id"] . "'></a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    /**
     * showPreview
     *
     * @param  mixed $category
     * @return void
     */
    public function showPreview($category) {
        global $DB, $CFG_GLPI;

        $criteria = "SELECT `category`, `split`, `level` FROM `glpi_plugin_splitdropdown` WHERE `category`='" . $category . "'";
        $iterators = $DB->request($criteria);
        $list = [];

        foreach ($iterators as $iterator) {
            $list[] = array(
                "category" => $iterator["category"],
                "split" => $iterator["split"],
                "level" => $iterator["level"]
            );
        }

        $category = $list[0]["category"];
        $split = $list[0]["split"];
        $level = $list[0]["level"];

        $table = getTableForItemType($category);
        $itemType_id = getForeignKeyFieldForTable($table);
        $itemType = getSingular(ucfirst($category));

        echo "<table class='tab_cadre' cellpadding='5'>";
        echo "<tr><th colspan='2'>" . __("Splitted dropdown preview", "splitdropdown") . "</th></tr>";
        echo "<tr>";
        echo "<td>" . __("Parent", "splitdropdown") . "</td>";
        echo "<td>";

        if ($split === 1) {
            $itemType::dropdown([
                "name" => "parent_dropdown_",
                "conditions" => ["level" => $level],
            ]);
            echo "</td>";
            echo "</tr>";
            echo "<tr id='childrenArea'>";
            echo "<td>" . __("Children", "splitdropdown") . "</td>";
            echo "<td>";
            echo "<span id='children'></span>";
        } else {
            $itemType::dropdown([
                "name" => "dropdown_",
                "rand" => 1,
                "value" => -1,
            ]);
        }
        echo "</td>";
        echo "</tr>";
        echo "</table>";

        $JS = <<<JAVASCRIPT
            $('#childrenArea').hide();
            $('select[id^=dropdown_parent_dropdown]').on('change', function(e) {
                e.preventDefault();
                var fk = $('select[id^=dropdown_parent_dropdown]').val();

                $.ajax({
                    url: '{$CFG_GLPI['root_doc']}/plugins/splitdropdown/ajax/children.dropdown.php',
                    type: "GET",
                    data: { fk: fk, table: '$table', itemType_id: '$itemType_id', itemType: '$itemType' },
                    success: function(response){
                        $('#children').html(response);
                    }
                });
                if($('select[id^=dropdown_parent_dropdown]').val() !== "0") $('#childrenArea').show();
                else
                {
                    $('#childrenArea').hide();
                }
            });
        JAVASCRIPT;

        echo Html::scriptBlock($JS);
    }
}
