<?php

/**
 * Physical inventory plugin
 *
 * Copyright (C) 2016-2020 by David Durieux & DCS company.
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
 * @copyright Copyright (c) 2016-2020 David Durieux & DCS company
 * @license   AGPL License 3.0 or (at your option) any later version
 *            http://www.gnu.org/licenses/agpl-3.0-standalone.html
 * @link      https://github.com/ddurieux/glpi_physicalinv
 *
 */

class InventoryTest extends RestoreDatabase_TestCase {


   /**
    * @test
    */
   public function searchDeviceWithSerialNumber() {
      $auth = new Auth();
      $auth->login('glpi', 'glpi');
      file_put_contents(GLPI_ROOT."/files/_log/php-errors.log", '');

      $pInventory = new PluginPhysicalinvInventory();
      $computer = new Computer();
      $monitor  = new Monitor();
      $networkEquipment = new NetworkEquipment();
      $phone = new Phone();
      $GLPIlog = new GLPIlogs();

      $input = [
          'name'        => 'not important',
          'entities_id' => 0,
      ];

      // Add 1 computer with serial number xxxxx, and 1 with serial yyyyy
      $input['serial'] = 'xxxxx';
      $computer->add($input);
      $input['serial'] = 'yyyyy';
      $computer->add($input);
      $devices = $pInventory->searchItemWithNumber('xxxxx');
      $GLPIlog->testSQLlogs();
      $GLPIlog->testPHPlogs();
      $ref = [
          'Computer' => [
              '1' => '1'
          ]
      ];
      $this->assertEquals($ref, $devices);

      // Add 2 more computers with serial number xxxxx
      $input['serial'] = 'xxxxx';
      $computer->add($input);
      $computer->add($input);
      $devices = $pInventory->searchItemWithNumber('xxxxx');
      $GLPIlog->testSQLlogs();
      $GLPIlog->testPHPlogs();
      $ref = [
          'Computer' => [
              '1' => '1',
              '3' => '3',
              '4' => '4'
          ]
      ];
      $this->assertEquals($ref, $devices);

      // Add 1 more computers with serial number xxxx and 1 with xxxxxx
      $input['serial'] = 'xxxx';
      $computer->add($input);
      $input['serial'] = 'xxxxxx';
      $computer->add($input);
      $this->assertEquals($ref, $devices);

      // Add 1 monitor with serial xxxxx, and 1 with serial yyyyy
      $input['serial'] = 'xxxxx';
      $monitor->add($input);
      $devices = $pInventory->searchItemWithNumber('xxxxx');
      $GLPIlog->testSQLlogs();
      $GLPIlog->testPHPlogs();
      $ref = [
          'Computer' => [
              '1' => '1',
              '3' => '3',
              '4' => '4'
          ],
          'Monitor' => [
              '1' => '1'
          ]
      ];
      $this->assertEquals($ref, $devices);

      // Add networkequipment with serial xxxxx
      $input['serial'] = 'xxxxx';
      $networkEquipment->add($input);
      $devices = $pInventory->searchItemWithNumber('xxxxx');
      $GLPIlog->testSQLlogs();
      $GLPIlog->testPHPlogs();
      $ref = [
          'Computer' => [
              '1' => '1',
              '3' => '3',
              '4' => '4'
          ],
          'Monitor' => [
              '1' => '1'
          ],
          'NetworkEquipment' => [
              '1' => '1'
          ]
      ];
      $this->assertEquals($ref, $devices);

      // Add a phone with serial xxxxx and another with xxyy
      $input['serial'] = 'xxxxx';
      $phone->add($input);
      $input['serial'] = 'xxyy';
      $phone->add($input);
      $devices = $pInventory->searchItemWithNumber('xxxxx');
      $GLPIlog->testSQLlogs();
      $GLPIlog->testPHPlogs();
      $ref = [
          'Computer' => [
              '1' => '1',
              '3' => '3',
              '4' => '4'
          ],
          'Monitor' => [
              '1' => '1'
          ],
          'NetworkEquipment' => [
              '1' => '1'
          ],
          'Phone' => [
              '1' => '1'
          ]
      ];
      $this->assertEquals($ref, $devices);
   }


   /**
    * @test
    */
   public function searchDeviceWithInventoryNumber() {


   }



   /**
    * @test
    */
   public function searchDeviceWithSerianAndInventoryNumber() {


   }

}

