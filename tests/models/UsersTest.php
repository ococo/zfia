<?php 
require_once 'PHPUnit/Framework.php'; 
require_once ROOT_DIR . '/application/models/Users.php';
require_once ROOT_DIR . '/application/models/User.php';

if(!defined('ROOT_DIR')) {                                    
    define('ROOT_DIR', realpath(dirname(dirname(dirname(__FILE__)))));
    set_include_path('.'
    . PATH_SEPARATOR . ROOT_DIR . '/library'
    . PATH_SEPARATOR . ROOT_DIR . '/../../lib/'
    . PATH_SEPARATOR . ROOT_DIR . '/application/models'
    . PATH_SEPARATOR . get_include_path());
      
    // auto load all classes as required
    require_once 'Zend/Loader.php'; 
    Zend_Loader::registerAutoload();;
} 

 
class UsersTest extends PHPUnit_Framework_TestCase
{ 
    public function __construct($name = NULL) 
    { 
        parent::__construct($name); 
 
        if(Zend_Registry::isRegistered('db')) {
            $this->db = Zend_Registry::get('db');
        } else { 
            $configFile = ROOT_DIR .'/application/config.ini';
            $config = new Zend_Config_Ini($configFile, 'test');
            Zend_Registry::set('config', $config);    
         
            // set up database 
            $db = Zend_Db::factory($config->db);
            Zend_Db_Table::setDefaultAdapter($db);
            Zend_Registry::set('db', $db);
            $this->db = $db;
        } 
    } 
    
    public function setUp()
    { 
        // reset database to known state 
        $this->_setupDatabase();        
    } 
    
    protected function _setupDatabase() 
    { 
        $this->db->query('DROP TABLE IF EXISTS users;');

        $this->db->query(<<<EOT
            CREATE TABLE users (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            date_created DATETIME,
            date_updated DATETIME,
            username VARCHAR(100) NOT NULL,
            password VARCHAR(40) NOT NULL,
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            email VARCHAR(150) NOT NULL,
            town VARCHAR(100),
            country VARCHAR(100),
            date_of_birth datetime,
            sex char(1),
            postcode VARCHAR(30)
        ) 
EOT
        );

        $row = array ( 
            'first_name' => 'Rob', 
            'last_name'  => 'Allen', 
            'username'  => 'rob', 
            'password'  => 'rob', 
            'email'  => 'rob@akrabat.com', 
            'town'  => 'London', 
            'country' => 'UK', 
            'date_of_birth' => '1970-01-04', 
            'sex' => 'M', 
            'date_created' => '2007-02-14 00:00:00', 
        );
        $this->db->insert('users', $row);

    }

    public function testInsert()
    {
        $users = new Users();
        $newUser = $users->fetchNew();
         
        $newUser->first_name = 'Nick';
        $newUser->last_name = 'Lo';
        $newUser->password = 'nick';
        $newUser->email = 'nick@example.com';
        $newUser->date_created = new Zend_Db_Expr('NOW()');
        
        $id = $newUser->save();
         
        $nick = $users->find($id)->current();
        $this->assertSame(2, (int)$nick->id);
         
        // check that the date_created has been filled in
        $this->assertNotNull($nick->date_created);
    }
    
    public function testName()
    {
        $users = new Users();
        $rob = $users->find(1)->current();
        
        $this->assertSame($rob->name(), 'Rob Allen');
    }
}