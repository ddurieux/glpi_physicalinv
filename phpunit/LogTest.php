<?php

class GLPIlogs extends PHPUnit_Framework_TestCase {

   public function testSQLlogs() {

      $filecontent = file_get_contents(GLPI_ROOT."/files/_log/sql-errors.log");

      $this->assertEquals('', $filecontent, 'sql-errors.log not empty');
      // Reinitialize file
      file_put_contents(GLPI_ROOT."/files/_log/sql-errors.log", '');
   }



   public function testPHPlogs() {

      $filecontent = file_get_contents(GLPI_ROOT."/files/_log/php-errors.log");

      $this->assertEquals('', $filecontent, 'php-errors.log not empty');
      // Reinitialize file
      file_put_contents(GLPI_ROOT."/files/_log/php-errors.log", '');
   }

}



class GLPIlogs_AllTests  {

   public static function suite() {

      $suite = new PHPUnit_Framework_TestSuite('GLPIlogs');
      return $suite;
   }
}

?>
