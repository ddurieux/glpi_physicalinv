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

/**
 * Manage the profiles in plugin.
 */
class PluginPhysicalinvProfile extends Profile {

   /**
    * The right name for this class
    *
    * @var string
    */
   static $rightname = "config";

   /**
    * Get the tab name used for item
    *
    * @param object $item the item object
    * @param integer $withtemplate 1 if is a template form
    * @return string name of the tab
    */
   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      if ($item->fields['interface'] == 'central') {
         return self::createTabEntry(__('Physical inventory', 'physicalinv'));
      }
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
      $pfProfile = new self();
      if ($item->fields['interface'] == 'central') {
         $pfProfile->showForm($item->getID());
      }
      return TRUE;
   }



   /**
    * Display form
    *
    * @param integer $profiles_id
    * @param boolean $openform
    * @param boolean $closeform
    * @return true
    */
   function showForm($profiles_id=0, $openform=TRUE, $closeform=TRUE) {

      echo "<div class='firstbloc'>";
      if (($canedit = Session::haveRightsOr(self::$rightname, array(CREATE, UPDATE, PURGE)))
          && $openform) {
         $profile = new Profile();
         echo "<form method='post' action='".$profile->getFormURL()."'>";
      }

      $profile = new Profile();
      $profile->getFromDB($profiles_id);

      $rights = array(
          array(
              'rights' => array(UPDATE => __('Update')),
              'label'  => __('Physical inventory', 'physicalinv'),
              'field'  => 'plugin_physicalinv_inventory'
              )
          );

      $profile->displayRightsChoiceMatrix($rights, array(
          'canedit'       => $canedit,
          'default_class' => 'tab_bg_2',
          'title'         => __('General', 'physicalinv')));

      if ($canedit
          && $closeform) {
         echo "<div class='center'>";
         echo Html::hidden('id', array('value' => $profiles_id));
         echo Html::submit(_sx('button', 'Save'), array('name' => 'update'));
         echo "</div>\n";
         Html::closeForm();
      }
      echo "</div>";

      $this->showLegend();
      return TRUE;
   }



   /**
    * Delete profiles
    */
   static function uninstallProfile() {
      $pfProfile = new self();
      $a_rights = array(
          array(
              'rights' => array(UPDATE => __('Update')),
              'label'  => __('Physical inventory', 'physicalinv'),
              'field'  => 'plugin_physicalinv_inventory'
              )
          );
      foreach ($a_rights as $data) {
         ProfileRight::deleteProfileRights(array($data['field']));
      }
   }



   /**
    * Add the default profile
    *
    * @param integer $profiles_id
    * @param array $rights
    */
   static function addDefaultProfileInfos($profiles_id, $rights) {
      $profileRight = new ProfileRight();
      foreach ($rights as $right => $value) {
         if (!countElementsInTable('glpi_profilerights',
                                   "`profiles_id`='$profiles_id' AND `name`='$right'")) {
            $myright['profiles_id'] = $profiles_id;
            $myright['name']        = $right;
            $myright['rights']      = $value;
            $profileRight->add($myright);

            //Add right to the current session
            $_SESSION['glpiactiveprofile'][$right] = $value;
         }
      }
   }



   /**
    * Create first access (so default profile)
    *
    * @param integer $profiles_id id of profile
    */
   static function createFirstAccess($profiles_id) {
      include_once(GLPI_ROOT."/plugins/physicalinv/inc/profile.class.php");
      $profile = new self();
      $a_rights = array(
          array(
              'rights' => array(UPDATE => __('Update')),
              'label'  => __('Physical inventory', 'physicalinv'),
              'field'  => 'plugin_physicalinv_inventory'
              )
          );
      foreach ($a_rights as $right) {
         self::addDefaultProfileInfos($profiles_id,
                                      array($right['field'] => ALLSTANDARDRIGHT));
      }
   }



   /**
    * Delete rights stored in session
    */
   static function removeRightsFromSession() {
      $profile = new self();
      $a_rights = array(
          array(
              'rights' => array(UPDATE => __('Update')),
              'label'  => __('Physical inventory', 'physicalinv'),
              'field'  => 'plugin_physicalinv_inventory'
              )
          );
      foreach ($a_rights as $right) {
         if (isset($_SESSION['glpiactiveprofile'][$right['field']])) {
            unset($_SESSION['glpiactiveprofile'][$right['field']]);
         }
      }
      ProfileRight::deleteProfileRights(array($right['field']));
   }



   /**
    * Init profiles during installation:
    * - add rights in profile table for the current user's profile
    * - current profile has all rights on the plugin
    */
   static function initProfile() {
      $pfProfile = new self();
      $profile   = new Profile();
      $a_rights = array(
          array(
              'rights' => array(UPDATE => __('Update')),
              'label'  => __('Physical inventory', 'physicalinv'),
              'field'  => 'plugin_physicalinv_inventory'
              )
          );

      foreach ($a_rights as $data) {
         if (countElementsInTable("glpi_profilerights", "`name` = '".$data['field']."'") == 0) {
            ProfileRight::addProfileRights(array($data['field']));
            $_SESSION['glpiactiveprofile'][$data['field']] = 0;
         }
      }

      // Add all rights to current profile of the user
      if (isset($_SESSION['glpiactiveprofile'])) {
         $dataprofile       = array();
         $dataprofile['id'] = $_SESSION['glpiactiveprofile']['id'];
         $profile->getFromDB($_SESSION['glpiactiveprofile']['id']);
         foreach ($a_rights as $info) {
            if (is_array($info)
                && ((!empty($info['itemtype'])) || (!empty($info['rights'])))
                  && (!empty($info['label'])) && (!empty($info['field']))) {

               if (isset($info['rights'])) {
                  $rights = $info['rights'];
               } else {
                  $rights = $profile->getRightsFor($info['itemtype']);
               }
               foreach ($rights as $right => $label) {
                  $dataprofile['_'.$info['field']][$right] = 1;
                  $_SESSION['glpiactiveprofile'][$data['field']] = $right;
               }
            }
         }
         $profile->update($dataprofile);
      }
   }
}

?>
