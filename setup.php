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

define ("PLUGIN_PHYSICALINV_VERSION", "0.90+1.0");

include_once(GLPI_ROOT."/inc/includes.php");

/**
 * Init the hooks of Physical inventory
 *
 * @global array $PLUGIN_HOOKS
 * @global array $CFG_GLPI
 */
function plugin_init_physicalinv() {
   global $PLUGIN_HOOKS, $CFG_GLPI;

   $PLUGIN_HOOKS['csrf_compliant']['physicalinv'] = TRUE;

   $Plugin = new Plugin();

   if ($Plugin->isActivated('physicalinv')) { // check if plugin is active

      if (Session::haveRight('plugin_physicalinv_inventory', UPDATE)) {
         $PLUGIN_HOOKS["menu_toadd"]['physicalinv']['plugins'] = 'PluginPhysicalinvInventory';
      }

      $Plugin->registerClass('PluginPhysicalinvProfile',
              array('addtabon' => array('Profile')));

   }
}



/**
 * Manage the version information of the plugin
 *
 * @return array
 */
function plugin_version_physicalinv() {
   return array('name'           => 'Physical inventory',
                'shortname'      => 'physicalinv',
                'version'        => PLUGIN_PHYSICALINV_VERSION,
                'license'        => 'AGPLv3+',
                'author'         => '<a href="mailto:david@durieux.family">David DURIEUX</a>
                                    & <a href="mailto:dcs.glpi@dcsit-group.com">DCS company</a>',
                'homepage'       => 'https://github.com/ddurieux/glpi_physicalinv',
                'minGlpiVersion' => '0.85'
   );
}



/**
 * Manage / check the prerequisites of the plugin
 *
 * @global object $DB
 * @return boolean
 */
function plugin_physicalinv_check_prerequisites() {
   global $DB;

   if (!isset($_SESSION['glpi_plugins'])) {
      $_SESSION['glpi_plugins'] = array();
   }

   if (version_compare(GLPI_VERSION, '0.85', 'lt') || version_compare(GLPI_VERSION, '0.90', 'ge')) {
      echo __('Your GLPI version not compatible, require >= 0.85 and < 9.1', 'physicalinv');
      return FALSE;
   }
   return TRUE;
}



/**
 * Check if the config is ok
 *
 * @return boolean
 */
function plugin_physicalinv_check_config() {
   return TRUE;
}



/**
 * Check the rights
 *
 * @param string $type
 * @param string $right
 * @return boolean
 */
function plugin_physicalinv_haveTypeRight($type, $right) {
   return TRUE;
}
