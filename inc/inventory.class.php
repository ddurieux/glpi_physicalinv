<?php

/**
 * Physical inventory plugin
 *
 * Copyright (C) 2016-2016 by David Durieux & DCS company.
 *
 * https://github.com/ddurieux/glpi_physicalinv
 *
 * ------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of "Physical inventory" plugin project.
 *
 * Physical inventory is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Physical inventory is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Physical inventory. If not, see <http://www.gnu.org/licenses/>.
 *
 * ------------------------------------------------------------------------
 *
 * This file is used to manage the setup / initialize plugin Physical inventory.
 *
 * ------------------------------------------------------------------------
 *
 * @package   Physical inventory
 * @author    David Durieux
 * @copyright Copyright (c) 2016-2016 David Durieux & DCS company
 * @license   AGPL License 3.0 or (at your option) any later version
 *            http://www.gnu.org/licenses/agpl-3.0-standalone.html
 * @link      https://github.com/ddurieux/glpi_physicalinv
 *
 */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginPhysicalinvInventory extends CommonGLPI {


   /**
    * The right name for this class
    *
    * @var string
    */
   static $rightname = 'computer';


   /**
    * Get name of this type by language of the user connected
    *
    * @param integer $nb number of elements
    * @return string name of this type
    */
   static function getTypeName($nb=0) {
      return __('Physical inventory', 'physicalinv');
   }



   /**
    * Get the tab name used for item
    *
    * @param object $item the item object
    * @param integer $withtemplate 1 if is a template form
    * @return string name of the tab
    */
   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      return '';
   }



   /**
    * Display the content of the tab
    *
    * @param object $item
    * @param integer $tabnum number of the tab to display
    * @param integer $withtemplate 1 if is a template form
    * @return boolean
    */
   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      return FALSE;
   }



   /**
    * Display information about computer (bios, last contact...)
    *
    * @global array $CFG_GLPI
    * @return true
    */
   static function showSearchForm() {
      global $CFG_GLPI;

      echo "<form method='post' name='' id=''  action=\"".$CFG_GLPI['root_doc'] .
         "/plugins/physicalinv/front/inventory.form.php\">";

      echo "<table width='950' class='tab_cadre_fixe'>";

      echo "<tr>";
      echo "<th>";
      echo __('Physical inventory', 'physicalinv');
      echo "</th>";
      echo "</tr>";

      echo "<tr>";
      echo "<td class='center' style='height: 40px;'>";
      echo __('Search with the serial number or inventory number', 'physicalinv');
      echo "</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td class='center' style='height: 80px;'>";
      echo "<input type='text' name='searchnumber' value='' size='50' style='height: 40px;font-size: 20px;' />";
      echo "</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td class='center' style='height: 40px;'>";
      echo "<input type='submit' name='search_item' value=\"".
               __('Search')."\" class='submit' >";
      echo "</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td class='center' style='height: 300px;'>";
      echo "</td>";
      echo "</tr>";

      echo "</table>";

      Html::closeForm();
   }



   /**
    * Search and get list of devices have the 'number' in serial number or
    * inventory number
    *
    * @param string $number
    * @return array
    */
   function searchItemWithNumber($number) {
      global $DB, $CFG_GLPI;

      $id_list = array();

      // search in inventory have serial number or inventory number
      foreach($CFG_GLPI["asset_types"] as $itemtype) {
         $where_fields = array();
         $table = getTableForItemType($itemtype);
         $item = new $itemtype();

         if (FieldExists($table, 'serial')) {
            $where_fields[] = 'serial';
         }
         if (FieldExists($table, 'otherserial')) {
            $where_fields[] = 'otherserial';
         }
         if (count($where_fields) == 0) {
            continue;
         }
         $query = "SELECT *
                   FROM `".$table."` WHERE (";
         $first = True;
         foreach ($where_fields as $field) {
            if (!$first) {
               $query .= " OR ";
            }
            $query .= " `$field`='$number'";
            $first = False;
         }
         $query .= ") AND `is_deleted`='0' AND `is_template`='0'";
         $result = $DB->query($query);
         while ($data = $DB->fetch_array($result)) {
            if ($item->canEdit($data['id'])) {
               $id_list[$itemtype][$data['id']] = $data['id'];
            }
         }
      }
      return $id_list;
   }


   /**
    * Display the fields to be updated
    *
    * @param integer $id
    * @param string $itemtype
    */
   function displayItemtypeInformation($id, $itemtype) {
      global $CFG_GLPI;

      $item = new $itemtype();
      $item->getFromDB($id);

      echo "<form method='post' name='' id=''  action=\"".$CFG_GLPI['root_doc'] .
         "/plugins/physicalinv/front/inventory.form.php\">";

      echo "<table width='950' class='tab_cadre_fixe'>";

      echo "<tr>";
      echo "<th colspan='4'>";
      echo $item->getTypeName().": ".$item->getName();
      echo "</th>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Name')."</td>";
      echo "<td>";
      $objectName = autoName($item->fields["name"], "name", False,
                             $item->getType(), $item->fields["entities_id"]);
      Html::autocompletionTextField($item, 'name', array('value' => $objectName));
      echo "</td>";
      echo "<td>".__('Status')."</td>";
      echo "<td>";
      State::dropdown(array('value'     => $item->fields["states_id"],
                            'entity'    => $item->fields["entities_id"],
                            'condition' => "`is_visible_computer`"));
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Location')."</td>";
      echo "<td>";
      Location::dropdown(array('value'  => $item->fields["locations_id"],
                               'entity' => $item->fields["entities_id"]));
      echo "</td>";
      echo "<td>".__('Type')."</td>";
      echo "<td>";
      ComputerType::dropdown(array('value' => $item->fields["computertypes_id"]));
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('User')."</td>";
      echo "<td>";
      User::dropdown(array('value'  => $item->fields["users_id"],
                           'entity' => $item->fields["entities_id"],
                           'right'  => 'all'));
      echo "</td>";
      echo "<td>".__('Manufacturer')."</td>";
      echo "<td>";
      Manufacturer::dropdown(array('value' => $item->fields["manufacturers_id"]));
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td rowspan='3'>".__('Comments')."</td>";
      echo "<td rowspan='3' class='middle'>";

      echo "<textarea cols='45' rows='".($rowspan+3)."' name='comment' >".
           $item->fields["comment"];
      echo "</textarea></td>";
      $model = $itemtype.'Model';
      if (TableExists(getTableForItemType($model))) {
         echo "<td>";
         echo __('Model');
         echo "</td>";
         echo "<td>";
         $model::dropdown(array('value' => $item->fields["computermodels_id"]));
         echo "</td>";
      } else {
         echo "<td colspan='2'></td>";
      }
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Serial number')."</td>";
      echo "<td >";
      Html::autocompletionTextField($item,'serial');
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Inventory number')."</td>";
      echo "<td>";
      $objectName = autoName($item->fields["otherserial"], "otherserial", False,
                             $item->getType(), $this->fields["entities_id"]);
      Html::autocompletionTextField($item, 'otherserial', array('value' => $objectName));
      echo "</td></tr>\n";

      echo "<tr>";
      echo "<td class='center' style='height: 60px;' colspan='4'>";
      echo Html::hidden('itemtype', array('value' => $itemtype));
      echo Html::hidden('id', array('value' => $id));
      echo "<input type='submit' name='valid_inventory' value=\"".
               __('Validate physical inventory', 'physicalinv')."\" class='submit' >";
      echo "</td>";
      echo "</tr>";

      echo "</table>";
      Html::closeForm();
   }



   /**
    * Save form data in database
    *
    */
   function saveData($data) {
      $item = new $data['itemtype'];
      $item->update($data);
      // + update physical inventory date
      $infocom = new Infocom();

      if ($infocom->getFromDBforDevice($data['itemtype'], $data['id'])) {
         $input = array('id'             => $infocom->fields['id'],
                        'inventory_date' => date("Y-m-d H:i:s"));
         $infocom->update($input);
      } else {
         $input = array(
             'items_id'       => $data['id'],
             'itemtype'       => $data['itemtype'],
             'inventory_date' => date("Y-m-d H:i:s")
         );
         $infocom->add($input);
      }
      Session::addMessageAfterRedirect(__('Information udpated', 'physicalinv'), false, INFO);
      Session::addMessageAfterRedirect(__('Physical inventry date updated', 'physicalinv'), false, INFO);
   }



   /**
    * If multiple devices found, display all and give to user possibility to
    * select the right.
    *
    * @param type $devices
    */
   function multipleDevices($devices) {
      global $CFG_GLPI;

      $num_devices = 0;
      foreach ($devices as $itemtype=>$theids) {
         $num_devices += count($theids);
      }
      if ($num_devices == 1) {
         return False;
      }

      // Have multiple, so display all
      echo "<table width='950' class='tab_cadre_fixe'>";

      echo "<tr>";
      echo "<th colspan='5'>";
      echo __('Multiple devices found, choose the right', 'physicalinv');
      echo "</th>";
      echo "</tr>";

      foreach ($devices as $itemtype=>$theids) {
         $item = new $itemtype();
         foreach ($theids as $id) {
            $item->getFromDB($id);
            echo "<tr class='tab_bg_1'>";
            echo "<td>".$item->getTypeName()."</td>";
            echo "<td>".$item->getLink()."</td>";
            echo "<td>".$item->fields['serial']."</td>";
            echo "<td>".Dropdown::getDropdownName('glpi_manufacturers', $item->fields['manufacturers_id'])."</td>";
            echo "<td>";

            echo "<form method='post' name='' id=''  action=\"".$CFG_GLPI['root_doc'] .
               "/plugins/physicalinv/front/inventory.form.php\">";
            echo Html::hidden('itemtype', array('value' => $itemtype));
            echo Html::hidden('id', array('value' => $id));
            echo "<input type='submit' name='choose_device' value=\"".
                     __('Choose it', 'physicalinv')."\" class='submit' >";
            Html::closeForm();
            echo "</td>";
            echo "</tr>";
         }
      }
      echo "</table>";
      return True;
   }
}
