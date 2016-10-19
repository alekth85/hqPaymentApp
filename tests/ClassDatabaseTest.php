<?php

require_once('../vendor/autoload.php');
require_once('../includes/config.php');

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    private $db;
    
    public function __construct()
    {
        $this->db = new Database;
    }
    
    public function testcheckIfExists()
    {
        $this->assertTrue($this->db->checkIfExist());
    }
    
    public function testInit()
    {
        $this->assertTrue($this->db->init());
        unlink(APP_DIR . DBPATH);
    }
}
?>