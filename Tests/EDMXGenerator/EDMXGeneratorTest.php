<?php
/**
 * Contains tests for EDMXGenerator class.
 *
 * PHP version 5.3
 *
 * @category  Tests
 * @package   Tests_EDMXGenerator
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version   SVN: 1.0
 * @link
 *
 */

require_once 'PHPUnit\Framework\Assert.php';
require_once 'PHPUnit\Framework\Test.php';
require_once 'PHPUnit\Framework\SelfDescribing.php';
require_once 'PHPUnit\Framework\TestCase.php';
require_once 'PHPUnit\Framework\TestSuite.php';
require_once 'ODataConnectorForMySQL\Common\ClassAutoLoader.php';
ODataConnectorForMySQL\Common\ClassAutoLoader::register();
use ODataConnectorForMySQL\EDMXGenerator\EDMXGenerator;
use ODataConnectorForMySQL\EDMXGenerator\ConnectionParams;
use ODataConnectorForMySQL\Common\ODataConnectorForMySQLConstants;
use ODataConnectorForMySQL\Common\ServiceConfig;
use ODataConnectorForMySQL\Common\ODataConnectorForMySQLException;
use ODataConnectorForMySQL\ObjectModel\Association;
use ODataConnectorForMySQL\ObjectModel\AssociationEnd;
use ODataConnectorForMySQL\ObjectModel\AssociationSet;
use ODataConnectorForMySQL\ObjectModel\AssociationSetEnd;
use ODataConnectorForMySQL\ObjectModel\Dependent;
use ODataConnectorForMySQL\ObjectModel\EntityContainer;
use ODataConnectorForMySQL\ObjectModel\EntitySet;
use ODataConnectorForMySQL\ObjectModel\EntityType;
use ODataConnectorForMySQL\ObjectModel\Key;
use ODataConnectorForMySQL\ObjectModel\MappingDetails;
use ODataConnectorForMySQL\ObjectModel\MapEntity;
use ODataConnectorForMySQL\ObjectModel\MapProperty;
use ODataConnectorForMySQL\ObjectModel\NavigationProperty;
use ODataConnectorForMySQL\ObjectModel\Principal;
use ODataConnectorForMySQL\ObjectModel\Property;
use ODataConnectorForMySQL\ObjectModel\PropertyRef;
use ODataConnectorForMySQL\ObjectModel\ReferentialConstraint;
use ODataConnectorForMySQL\ObjectModel\Schema;
use ODataConnectorForMySQL\ObjectModel\EntityNameInformation;
use ODataConnectorForMySQL\ObjectModel\EntityName;
ob_start();
require_once '\ODataConnectorForMySQL\Common\Inflector.php';
ob_end_clean();

/**
 * tests for EDMXGenerator class.
 *
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_EDMXGenerator
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link
 */
class EDMXGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public $connectionParams;
    protected function setUp()
    {
        $connectionParams['host'] = "localhost";
        $connectionParams['dbname'] = "northwind";
        $connectionParams['user'] = "root";
        $connectionParams['password'] = "";
        $connectionParams['driver'] = "pdo_mysql";
        $connectionParams['serviceName'] = "Northwind";
        $this->connectionParams = $connectionParams;
    }

    public function testGenerateEDMX()
    {
        try {
            //Generate Schema
            $edmxGenerator = new EDMXGenerator($this->connectionParams);
            $this->assertNotNull($edmxGenerator);

            //assertion for schema
            $schema = $edmxGenerator->getSchema();
            $schema = $edmxGenerator->modifySchema($schema);
            $this->assertTrue(is_object($schema));
            $this->assertTrue($schema instanceof Schema);

            $namespace = $schema->namespace;
            $this->assertNotNull($namespace);
            $this->assertEquals($namespace, $this->connectionParams['serviceName']);

            //assertion for EntityType
            $entityTypes = $schema->entityTypes;
            $this->assertNotNull($entityTypes);
            $this->assertTrue(is_array($entityTypes));
            $this->assertArrayHasKey("customers", $entityTypes);
            $this->assertTrue($entityTypes['customers'] instanceof EntityType);

            $customerEntityType = $entityTypes['customers'];
            $customerEntityType->name = "customers";

            //Assertion for Key
            $this->assertNotNull($customerEntityType->key);
            $this->assertTrue(is_object($customerEntityType->key));
            $this->assertTrue($customerEntityType->key instanceof Key);
            $this->assertNotNull($customerEntityType->key->propertyRefs);
            $this->assertTrue(is_array($customerEntityType->key->propertyRefs));
            $this->assertEquals($customerEntityType->key->propertyRefs, array("CustomerID"));

            //assertion for property
            $this->assertNotNull($customerEntityType->properties);
            $this->assertTrue(is_array($customerEntityType->properties));
            $this->assertArrayHasKey("CompanyName", $customerEntityType->properties);
            $this->assertNotNull($customerEntityType->properties['CompanyName']);
            $this->assertTrue($customerEntityType->properties['CompanyName'] instanceof Property);
            $companyNameProperty = $customerEntityType->properties['CompanyName'];
            $this->assertNotNull($companyNameProperty->name);
            $this->assertEquals($companyNameProperty->name, "CompanyName");
            $this->assertNotNull($companyNameProperty->type);
            $this->assertNotNull($companyNameProperty->nullable);
            $this->assertEquals($companyNameProperty->nullable, false);
            $this->assertNotNull($companyNameProperty->maxLength);
            $this->assertEquals($companyNameProperty->maxLength, "40");
            $this->assertNotNull($companyNameProperty->precision);
            $this->assertEquals($companyNameProperty->precision, "10");
            $this->assertNull($companyNameProperty->fixedLength);
            $this->assertNull($companyNameProperty->scale);
            $this->assertNull($companyNameProperty->unicode);

            //assertion for Navigation property
            $this->assertNotNull($customerEntityType->navigationProperties);
            $this->assertArrayHasKey("FK_orders_customer_id", $customerEntityType->navigationProperties);
            $this->assertNotNull($customerEntityType->navigationProperties['FK_orders_customer_id']);
            $this->assertTrue($customerEntityType->navigationProperties['FK_orders_customer_id'] instanceof NavigationProperty);
            $navigationProperty = $customerEntityType->navigationProperties['FK_orders_customer_id'];
            $this->assertNotNull($navigationProperty->name);
            $this->assertEquals($navigationProperty->name, "orders");
            $this->assertNotNull($navigationProperty->relationship);
            $this->assertEquals($navigationProperty->relationship, "FK_orders_customer_id");
            $this->assertNotNull($navigationProperty->toRole);
            $this->assertEquals($navigationProperty->toRole, "orders");
            $this->assertNotNull($navigationProperty->fromRole);
            $this->assertEquals($navigationProperty->fromRole, "customer");

            //assertion for Association
            $associations = $schema->associations;
            $this->assertNotNull($associations);
            $this->assertTrue(is_array($associations));
            $this->assertArrayHasKey("FK_order_details_orderid", $associations);
            $this->assertTrue($associations['FK_order_details_orderid'] instanceof Association);

            $association = $associations['FK_order_details_orderid'];
            $this->assertNotNull($association);
            $this->assertEquals($association->name, "FK_order_details_orderid");

            //Assertion for end1
            $associationEnd1 = $association->end1;
            $this->assertNotNull($associationEnd1);
            $this->assertTrue(is_object($associationEnd1));
            $this->assertTrue($associationEnd1 instanceof AssociationEnd);
            $this->assertNotNull($associationEnd1->type);
            $this->assertEquals($associationEnd1->type, "Northwind.order");
            $this->assertNotNull($associationEnd1->multiplicity);
            $this->assertEquals($associationEnd1->multiplicity, "1");
            $this->assertNotNull($associationEnd1->role);
            $this->assertEquals($associationEnd1->role, "order");

            //Assertion for end2
            $associationEnd2 = $association->end2;
            $this->assertNotNull($associationEnd2);
            $this->assertTrue(is_object($associationEnd2));
            $this->assertTrue($associationEnd2 instanceof AssociationEnd);
            $this->assertNotNull($associationEnd2->type);
            $this->assertEquals($associationEnd2->type, "Northwind.order_detail");
            $this->assertNotNull($associationEnd2->multiplicity);
            $this->assertEquals($associationEnd2->multiplicity, "*");
            $this->assertNotNull($associationEnd2->role);
            $this->assertEquals($associationEnd2->role, "order_details");

            //Assertion for refrerntial constraint
            $associationReferentialConstraint = $association->referentialConstraint;
            $this->assertNotNull($associationReferentialConstraint);
            $this->assertTrue(is_object($associationReferentialConstraint));
            $this->assertTrue($associationReferentialConstraint instanceof ReferentialConstraint);

            //assertion for principal
            $principal = $associationReferentialConstraint->principal;
            $this->assertNotNull($principal);
            $this->assertTrue(is_object($principal));
            $this->assertTrue($principal instanceof Principal);
            $this->assertNotNull($principal->role);
            $this->assertEquals($principal->role, "order");
            $this->assertTrue(is_array($principal->propertyRefs));
            $this->assertNotNull($principal->propertyRefs['0']);
            $this->assertEquals($principal->propertyRefs['0'], "OrderID");

            //assertion for Dependent
            $dependent = $associationReferentialConstraint->dependent;
            $this->assertNotNull($dependent);
            $this->assertTrue(is_object($dependent));
            $this->assertTrue($dependent instanceof Dependent);
            $this->assertNotNull($dependent->role);
            $this->assertEquals($dependent->role, "order_details");
            $this->assertTrue(is_array($dependent->propertyRefs));
            $this->assertNotNull($dependent->propertyRefs['0']);
            $this->assertEquals($dependent->propertyRefs['0'], "OrderID");

            //Assertion for entityContainer
            $entityContainer = $schema->entityContainer;
            $this->assertNotNull($entityContainer);
            $this->assertTrue($entityContainer instanceof EntityContainer);
            $this->assertNotNull($entityContainer->name);
            $this->assertEquals($entityContainer->name, "NorthwindEntities");
            $this->assertNull($entityContainer->extends);
            $this->assertTrue(is_array($entityContainer->entitySets));
            $this->assertArrayHasKey("employees", $entityContainer->entitySets);
            $this->assertNotNull($entityContainer->entitySets['employees']);
            $this->assertTrue($entityContainer->entitySets['employees'] instanceof EntitySet);
            $this->assertNotNull($entityContainer->entitySets['employees']->name);
            $this->assertEquals($entityContainer->entitySets['employees']->name, "employees");
            $this->assertNotNull($entityContainer->entitySets['employees']->entityType);
            $this->assertEquals($entityContainer->entitySets['employees']->entityType, "Northwind.employee");

            //Assertion for AssociationSet
            $this->assertNotNull($entityContainer->associationSets);
            $this->assertTrue(is_array($entityContainer->associationSets));
            $this->assertArrayHasKey("FK_orders_shipvia", $entityContainer->associationSets);
            $this->assertNotNull($entityContainer->associationSets['FK_orders_shipvia']);
            $this->assertTrue($entityContainer->associationSets['FK_orders_shipvia'] instanceof AssociationSet);

            $associationSet = $entityContainer->associationSets['FK_orders_shipvia'];
            $this->assertNotNull($associationSet->name);
            $this->assertEquals($associationSet->name, "FK_orders_shipvia");
            $this->assertNotNull($associationSet->association);
            $this->assertEquals($associationSet->association, "Northwind.FK_orders_shipvia");

            //Assertion for AssociationSet End1
            $this->assertNotNull($associationSet->end1);
            $this->assertTrue(is_object($associationSet->end1));
            $this->assertTrue($associationSet->end1 instanceof AssociationSetEnd);
            $this->assertNotNull($associationSet->end1->role);
            $this->assertEquals($associationSet->end1->role, "shipper");
            $this->assertNotNull($associationSet->end1->entitySet);
            $this->assertEquals($associationSet->end1->entitySet, "shippers");

            //Assertion for AssociationSet End2
            $this->assertNotNull($associationSet->end2);
            $this->assertTrue(is_object($associationSet->end2));
            $this->assertTrue($associationSet->end2 instanceof AssociationSetEnd);
            $this->assertNotNull($associationSet->end2->role);
            $this->assertEquals($associationSet->end2->role, "orders");
            $this->assertNotNull($associationSet->end2->entitySet);
            $this->assertEquals($associationSet->end2->entitySet, "orders");

            //assertion for EDMX file.
            $xml = $edmxGenerator->generateEDMX();
            $this->assertNotNull($xml);
            $this->assertStringStartsWith("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n<edmx:Edmx ", $xml);
            $this->assertStringEndsWith("</Schema>\n </edmx:DataServices>\n</edmx:Edmx>\n", $xml);

            $currentDir = str_replace("\\", "/", dirname(__FILE__));
            $serviceOutDir = $currentDir."/../OutputFiles"."/".$this->connectionParams['serviceName'];
            if (!is_dir($serviceOutDir)) {
                mkdir($serviceOutDir, 0777);
            }
            $edmxPath = $serviceOutDir."/".$this->connectionParams['serviceName']."EDMX.xml";
            $fp = fopen($edmxPath, "w");
            fwrite($fp, $xml);
            fclose($fp);
            unset($xml);
            $connectionParams = new ConnectionParams($this->connectionParams);
            $xml = $connectionParams->saveConnectionParams();

            $connectionParamFile = $serviceOutDir."/".$this->connectionParams['serviceName']."ConnectionParams.xml";
            $fp = fopen($connectionParamFile, "w");
            fwrite($fp,$xml);
            fclose($fp);
            $this->assertFileExists($connectionParamFile);
            unset($edmxGenerator);
            unset($schema);
        }
        catch (\Exception $e)
        {
            $this->fail('An unexpected Exception has been raised . ' . $e->getMessage());
        }
    }

    protected function tearDown()
    {
    }
}
?>