<?php

require_once 'PHPUnit\Framework\Assert.php';
require_once 'PHPUnit\Framework\Test.php';
require_once 'PHPUnit\Framework\SelfDescribing.php';
require_once 'PHPUnit\Framework\TestCase.php';
require_once 'PHPUnit\Framework\TestSuite.php';
require_once 'Tests\CustomMySQLConnector.php';

define('OUT_DIR', '../OutputFiles');
define('XSL_DIR', '../../ODataConnectorForMySQL/ProvidersGenerator');
define('CONFIG_FILE', '../../ODataConnectorForMySQL/service.config.xml');

Class TestMySQLConnector extends PHPUnit_Framework_TestCase {

	public $cmdArgv;
	protected $db;
	protected $host;
	protected $user;
	protected $pw;
	protected $srvc;

	protected function setUp()
	{
		$this->db = 'northwind';
		$this->host = 'localhost';
		$this->user = 'root';
		$this->pw = '';
		$this->srvc = "Northwind";

		$this->cmdArgv = array (
    		 "MySQLConnector.php",
			 "/db=".$this->db,
			 "/u=".$this->user,
			 "/pw=".$this->pw,
			 "/h=".$this->host,
			 "/srvc=".$this->srvc	
		);
	}

	/*
	 * Generate providers.
	 */
	function generateSrvc() {
		try
		{
			$util = new MySQLConnector($this->cmdArgv);
			$options = $util->getOptions();
			$stage = $util->_validateAndBuidOptions();
			$currentDir = str_replace("\\", "/", dirname(__FILE__));
			$serviceOutDir = $currentDir."/".OUT_DIR."/".$util->_options['serviceName'];
			$serviceXslDir = $currentDir."/".XSL_DIR;
			$configFileName = $currentDir."/".CONFIG_FILE;
		    $stage = 2;
			$util->generateProviders($serviceOutDir, $serviceXslDir, $configFileName, $stage);
		}
		catch (\Exception $e)
		{
		    $this->fail('An unexpected Exception has been raised . ' . $e->getMessage());
		}
	}

	/*
	 * Test the generated file exist or not.
	 */
	function testGeneratedFilesExists() {
		$this->generateSrvc();
		$this->assertFileExists(dirname(__FILE__)."/../OutputFiles/".$this->srvc."/".$this->srvc.'EDMX.xml');
		$this->assertFileExists(dirname(__FILE__)."/../OutputFiles/".$this->srvc."/".$this->srvc.'QueryProvider.php');
		$this->assertFileExists(dirname(__FILE__)."/../OutputFiles/".$this->srvc."/".$this->srvc.'Metadata.php');
		$this->assertFileExists(dirname(__FILE__)."/../OutputFiles/".$this->srvc."/".$this->srvc.'DSExpressionProvider.php');
		$this->assertFileExists(dirname(__FILE__)."/../OutputFiles/".$this->srvc."/".$this->srvc.'DataService.php');
	}

	/*
	 * Test the QueryProvider class contains all the required APIs.
	 */

	function testQueryProviderApiExists() {
		require_once "/Tests/OutputFiles/".$this->srvc."/".$this->srvc."QueryProvider.php";
		$className = $this->srvc."QueryProvider";
		$this->assertTrue(class_exists($className));
		$classObj = new $className();
		$this->assertTrue(method_exists($classObj, "__construct"));
		$this->assertTrue(method_exists($classObj, "getResourceSet"));
		$this->assertTrue(method_exists($classObj, "getResourceFromResourceSet"));
		$this->assertTrue(method_exists($classObj, "getResourceFromRelatedResourceSet"));
		$this->assertTrue(method_exists($classObj, "getRelatedResourceSet"));
		$this->assertTrue(method_exists($classObj, "getRelatedResourceReference"));
		$this->assertTrue(method_exists($classObj, "_serializeshipper"));
		$this->assertTrue(method_exists($classObj, "_serializeproduct"));
		$this->assertTrue(method_exists($classObj, "__destruct"));
	}


	/*
	 * Test the DataService class contains all the required APIs.
	 */

	function testMetadataApiExists() {
		require_once "Tests/OutputFiles/".$this->srvc."/".$this->srvc."DataService.php";
		$className = "Create".$this->srvc."Metadata";
		$this->assertTrue(class_exists($className));
		$classObj = new $className();
		$this->assertTrue(method_exists($classObj, "create"));
	}


	/*
	 * Test the DSExpressionProvider class contains all the required APIs.
	 */

	function testDSExpressionProviderApiExists() {
		require_once "Tests/OutputFiles/".$this->srvc."/".$this->srvc."DSExpressionProvider.php";
		$className = $this->srvc."DSExpressionProvider";
		$this->assertTrue(class_exists($className));
		$classObj = new $className();
		$this->assertTrue(method_exists($classObj, "getIteratorName"));
		$this->assertTrue(method_exists($classObj, "onLogicalExpression"));
		$this->assertTrue(method_exists($classObj, "onArithmeticExpression"));
		$this->assertTrue(method_exists($classObj, "onRelationalExpression"));
		$this->assertTrue(method_exists($classObj, "onUnaryExpression"));
		$this->assertTrue(method_exists($classObj, "onConstantExpression"));
		$this->assertTrue(method_exists($classObj, "onPropertyAccessExpression"));
		$this->assertTrue(method_exists($classObj, "onFunctionCallExpression"));
		$this->assertTrue(method_exists($classObj, "_prepareBinaryExpression"));
		$this->assertTrue(method_exists($classObj, "_prepareUnaryExpression"));
	}


	/*
	 * Test the DSExpressionProvider class contains all the required APIs.
	 */

	function testDDataServiceApiExists() {
		require_once "Tests/OutputFiles/".$this->srvc."/".$this->srvc."DataService.php";
		$className = $this->srvc."DataService";
		$this->assertTrue(class_exists($className));
		$classObj = new $className();
		$this->assertTrue(method_exists($classObj, "initializeService"));
		$this->assertTrue(method_exists($classObj, "getService"));
		$this->assertTrue(method_exists($classObj, "getExpressionProvider"));
	}
	
	protected function tearDown()
	{
    }
}