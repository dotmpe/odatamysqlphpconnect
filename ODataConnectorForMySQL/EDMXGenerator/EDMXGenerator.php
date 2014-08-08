<?php
/**
 * Contains EDMXGenerator class to create EDMX file.
 *
 * PHP version 5.3
 *
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_Generator
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   SVN: 1.0
 * @link
 *
 */

namespace ODataConnectorForMySQL\EDMXGenerator;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\DriverManager;
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
require_once 'IEDMXGenerator.php';
require_once 'ODataConnectorForMySQL/Common/Inflector.php';
/**
 * EDMX file Generator.
 *
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_EDMXGenerator
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link
 */
class EDMXGenerator implements IEDMXGenerator
{
    /**
     * Writer to which output (EDMX Document) is sent
     *
     * @var XMLWriter
     */
    public $xmlWriter;

    /**
     * Reader from which input is getting.
     *
     * @var SchemaReader
     */
    public $schemaReader;

    /**
     * Connection object for driver
     * 
     *  @var object
     */
    public $connection;

    /**
     * schema manager object of doctrine
     * 
     * @var object
     */
    public $schemaManager;

    /**
     * connection params of connection
     * 
     * @var array 
     */
    public $connectionParams;

    /**
     * Namespace of the service
     * 
     * @var string
     */
    public $myNamespace;

    /**
     * Schema of the requested database
     * 
     * @var Schema
     */
    public $schema;
    
    /**
     * Service config params
     * 
     * @var array
     */
    public $serviceInfo;

    /**
     * maping types with Odata types
     * 
     * @var array
     */
    public $mapODataType = array(
        'Boolean' => 'Edm.Boolean',
        'SmallInt' => 'Edm.Int16',
        'Integer' => 'Edm.Int32',
        'BigInt' => 'Edm.Int64',
        'Decimal' => 'Edm.Decimal',
        'Float' => 'Edm.Double',
        'Boolean' => 'Edm.Boolean',
        'Date' => 'Edm.DateTime',
        'Time' => 'Edm.DateTime',
        'DateTime' => 'Edm.DateTime',
        'String' => 'Edm.String',
        'Text' => 'Edm.String',
        'Binary' => 'Edm.Binary',
        'VarBinary' => 'Edm.Binary',
        'TinyBlob' => 'Edm.Binary',
        'Blob' => 'Edm.Binary',
        'MediumBlob' => 'Edm.Binary',
        'LongBlob' => 'Edm.Binary',
        'Enum' => 'Edm.String',
        'Set' => 'Edm.String'
        );

    /**
     * Construct a new instance of EDMXGenerator.
     *
     * @param array &$connectionParams Connection parameters for database.
     */
    public function __construct(&$connectionParams)
    {
        //set error handler
        set_error_handler(
            array($this, "customError"), 
            E_ALL & ~E_DEPRECATED & ~E_NOTICE
        );
        try {
            $this->connectionParams = $connectionParams;
            $this->myNamespace = $this->connectionParams['serviceName'];
            $this->connection = DriverManager::getConnection(
                $this->connectionParams
            );
            $this->schemaManager = $this->connection->getSchemaManager();
            $this->serviceInfo = ServiceConfig::validateAndGetsServiceInfo();
            $this->xmlWriter = new \XMLWriter();
            $this->xmlWriter->openMemory();
            $this->xmlWriter->startDocument('1.0', 'UTF-8', 'yes');
            $this->xmlWriter->setIndent(4);
            $this->addTypes();
            $this->schema = $this->getSchema();
            $this->schema = $this->modifySchema($this->schema);
        }
        catch (Exception $e)
        {
            die("\n\nError: $e->getMessage...!!!()\n");
        }
    }

    /**
     * Handles errors.
     * 
     * @param string $errno  error number code.
     * @param string $errstr error message.
     * 
     * @return void
     */
    public function customError($errno, $errstr)
    {
        die("\n\nError: $errstr..!!!\n");
    }
    
    /**
     * Adding MySQL types which are not handled by Doctrine.
     *
     * @return void
     */
    public function addTypes()
    {
        if (!(Type::hasType('longblob'))) {
            Type::addType(
                'longblob', 'ODataConnectorForMySQL\Common\Types\LongBlobType'
            );
            $this->connection->getDatabasePlatform()
                ->registerDoctrineTypeMapping('LongBlob', 'longblob');
        }
        if (!(Type::hasType('blob'))) {
            Type::addType(
                'blob', 'ODataConnectorForMySQL\Common\Types\BlobType'
            );
            $this->connection->getDatabasePlatform()
                ->registerDoctrineTypeMapping('Blob', 'blob');
        }
        if (!(Type::hasType('mediumblob'))) {
            Type::addType(
                'mediumblob', 'ODataConnectorForMySQL\Common\Types\MediumBlobType'
            );
            $this->connection->getDatabasePlatform()
                ->registerDoctrineTypeMapping('MediumBlob', 'mediumblob');
        }
        if (!(Type::hasType('tinyblob'))) {
            Type::addType(
                'tinyblob', 'ODataConnectorForMySQL\Common\Types\TinyBlobType'
            );
            $this->connection->getDatabasePlatform()
                ->registerDoctrineTypeMapping('TinyBlob', 'tinyblob');
        }
        if (!(Type::hasType('binary'))) {
            Type::addType(
                'binary', 'ODataConnectorForMySQL\Common\Types\BinaryType'
            );
            $this->connection->getDatabasePlatform()
                ->registerDoctrineTypeMapping('Binary', 'binary');
        }
        if (!(Type::hasType('varbinary'))) {
            Type::addType(
                'varbinary', 'ODataConnectorForMySQL\Common\Types\VarBinaryType'
            );
            $this->connection->getDatabasePlatform()
                ->registerDoctrineTypeMapping('VarBinary', 'varbinary');
        }
        if (!(Type::hasType('bit'))) {
            Type::addType(
                'bit', 'ODataConnectorForMySQL\Common\Types\BitType'
            );
            $this->connection->getDatabasePlatform()
                ->registerDoctrineTypeMapping('Bit', 'boolean');
        }
        if (!(Type::hasType('enum'))) {
            Type::addType(
                'enum', 'ODataConnectorForMySQL\Common\Types\EnumType'
            );
            $this->connection->getDatabasePlatform()
                ->registerDoctrineTypeMapping('Enum', 'enum');
        }
        if (!(Type::hasType('set'))) {
            Type::addType(
                'set', 'ODataConnectorForMySQL\Common\Types\SetType'
            );
            $this->connection->getDatabasePlatform()
                ->registerDoctrineTypeMapping('Set', 'set');
        }
    }

    /**
     * Initialize object model with data from database schema.
     *
     * @return Schema $schema schema of database.
     */
    public function getSchema()
    {
        try
        {
            $this->schema = new Schema();
            $this->schema->namespace = $this->replaceSpaces($this->myNamespace);
            $this->schema->entityTypes = $this->getEntityTypes();
            $this->schema->associations = $this->getAssociations();
            $this->schema->entityContainer = $this->getEntityContainer();
            $this->schema->entityNameInformation = $this->addEntityInfo();
            $this->schema->mappingDetails = $this->addMappingDetails();
            return $this->schema;
        }
        catch (Exception $e)
        {
            ODataConnectorForMySQLException::createInternalServerError();
        }
    }

    /**
     * Get entity types from database schema.
     *
     * @return array<EntityType> $entityTypes Entity types of the schema.
     */
    public function getEntityTypes()
    {
        $entityTypes = array();
        $entities = $this->schemaManager->listTables();
        foreach ($entities as $entity) {
            $entityTypes[$this->replaceSpaces($entity->getName())] 
                = $this->getEntityType($entity);
            unset($entity);
        }
        return $entityTypes;
    }

    /**
     * Get entity type from database schema.
     *
     * @param EntityType &$entity Entity type of the schema.
     * 
     * @return EntityType
     */
    public function getEntityType(&$entity)
    {
        $entityType = new EntityType();
        $entityType->name = $this->replaceSpaces(
            $this->getEntityTypeName($entity->getName())
        );
        $entityType->key = new Key();
        $isPrimaryKey = $this->isPrimaryKeyExist($entity->getName());
        if ($isPrimaryKey) {
            $entityType->key->propertyRefs = $entity->getPrimaryKey()->getColumns();
        }
        $entityType->properties = $this->getProperties($entity);
        $entityType->navigationProperties = $this->getNavigationProperties($entity);
        unset($entity);
        return $entityType;
    }

    /**
     * To check is Primary key exist in Table or not.
     * 
     * @param sring $entityName Entity name of the table.
     * 
     * @return bool
     */
    public function isPrimaryKeyExist ($entityName)
    {
        $conn = mysql_connect(
            $this->connectionParams['host'], 
            $this->connectionParams['user'], 
            $this->connectionParams['password']
        ); 
        mysql_select_db($this->connectionParams['dbname'], $conn); 
        $query = "SHOW INDEX FROM ". $entityName." WHERE key_name ='primary'";
        $result = mysql_query($query, $conn) or die (mysql_error());
        $primaryKey = mysql_num_rows($result);
        if ($primaryKey > 0) {
            return true;
        }
        return false;
    }

    /**
     * Get entity type properties from database schema.
     * 
     * @param EntityType &$entity Entity type of the schema.
     *
     * @return array<Property> $properties Entity types of the schema.
     */
    public function getProperties(&$entity)
    {
        $properties = array();
        $columns = $this->schemaManager->listTableColumns($entity->getName());
        foreach ($columns as $column) {
            $properties[$column->getName()] = $this->getProperty($column);
            unset($column);
        }
        unset($entity);
        return $properties;
    }

    /**
     * Get entity type property from database schema.
     * 
     * @param Column &$column Column object of Entity type. 
     *
     * @return Property $property Entity type property of the schema.
     */
    public function getProperty(&$column)
    {
        $property = new Property();
        $property->name = $column->getName();
        $property->type = $column->getType();
        $property->nullable = $column->getNotnull() ? false : true;
        if ($column->getDefault() != null) {
            $property->defaultValue = $column->getDefault();
        }
        if ($column->getLength() != null) {
            $property->maxLength = $column->getLength();
        }
        if ($column->getFixed() != null) {
            $property->fixedLength = $column->getFixed() ? true : false;
        }
        if ($column->getPrecision() != null) {
            $property->precision = $column->getPrecision();
        }
        if ($column->getScale() != null) {
            $property->scale = $column->getScale();
        }
        if ($column->getUnsigned() != null) {
            $property->unicode = $column->getUnsigned() ? true : false;
        }
        unset($column);
        return $property;
    }

    /**
     * Get entity type navigation properties from database schema.
     * 
     * @param EntityType &$entity Entity type of the schema.
     *
     * @return array<NavigationProperty> $navigationProperties Navigation properties
     *                                                         of entity type.
     */
    public function getNavigationProperties(&$entity)
    {
        $navigationProperties = array();
        $foreignKeys = $this->schemaManager->listTableForeignKeys(
            $entity->getName()
        );
        foreach ($foreignKeys as $foreignKey) {
            $navigationProperties[$foreignKey->getName()] 
                = $this->getNavigationProperty($foreignKey, $entity->getName());
            unset($foreignKey);
        }
        unset($entity);
        return $navigationProperties;
    }

    /**
     * Get entity type navigation property.
     * 
     * @param ForeignKey &$foreignKey    foreign key of entity type.
     * @param string     $localTableName local table name of foreign key.
     *
     * @return NavigationProperty $navigationProperty Entity type navigation 
     *                                                property.
     */
    public function getNavigationProperty(&$foreignKey, $localTableName)
    {
        $foreignTableName = $foreignKey->getForeignTableName();
        $navigationProperty = new NavigationProperty();
        $navigationProperty->name = $this->replaceSpaces($foreignTableName);
        $navigationProperty->relationship = $foreignKey->getName();
        $navigationProperty->toRole = $this->replaceSpaces($foreignTableName);
        $navigationProperty->fromRole = $this->replaceSpaces($localTableName);
        unset($foreignKey);
        return $navigationProperty;
    }

    /**
     * Get associations of the schema.
     *
     * @return array<Association> $associations Associations of the schema.
     */
    public function getAssociations()
    {
        $associations = array();
        $entities = $this->schemaManager->listTables();
        foreach ($entities as $entity) {
            $foreignKeys = $this->schemaManager->listTableForeignKeys(
                $entity->getName()
            );
            foreach ($foreignKeys as $foreignKey) {
                $associations[$foreignKey->getName()] 
                    = $this->getAssociation($foreignKey, $entity->getName());
                unset($foreignKey);
            }
            unset($entity);
        }
        return $associations;
    }

    /**
     * Get association of the schema.
     *
     * @param ForeignKey &$foreignKey    foreign key of entity type.
     * @param string     $localTableName local table name of foreign key.
     * 
     * @return Association $association Association of the schema.
     */
    public function getAssociation(&$foreignKey, $localTableName)
    {
        $associations = new Association();
        $associations->name = $foreignKey->getName();
        $associations->end1 = new AssociationEnd();
        $associations->end1->type = $this->schema->namespace.".".$this
            ->replaceSpaces(
                $this->getEntityTypeName(
                    $foreignKey->getForeignTableName()
                )
            );
        $associations->end1->multiplicity = $this
            ->getForeignMultiplicity($foreignKey)  
            ? $this->getForeignMultiplicity($foreignKey) : "*";
        $associations->end1->role = $this->replaceSpaces(
            $foreignKey->getForeignTableName()
        );
        $associations->end2 = new AssociationEnd();
        $associations->end2->type = $this->schema->namespace.".".$this
            ->replaceSpaces($this->getEntityTypeName($localTableName));
        $associations->end2->multiplicity = $this
            ->getLocalMultiplicity($foreignKey, $localTableName) 
            ? $this->getLocalMultiplicity($foreignKey, $localTableName) : "*";
        if ($foreignKey->getForeignTableName() == $localTableName) {
            $associations->end2->role = $this->replaceSpaces($localTableName) . "1";
        } else {
            $associations->end2->role = $this->replaceSpaces($localTableName);
        }
        
        $associations->referentialConstraint = new ReferentialConstraint();
        $associations->referentialConstraint->principal = new Principal();
        $associations->referentialConstraint->principal->role 
            = $this->replaceSpaces($foreignKey->getForeignTableName());
        $associations->referentialConstraint->principal->propertyRefs 
            = $foreignKey->getForeignColumns();
        $associations->referentialConstraint->dependent = new Dependent();
        $associations->referentialConstraint->dependent->role 
            = $this->replaceSpaces($localTableName);
        $associations->referentialConstraint->dependent->propertyRefs 
            = $foreignKey->getLocalColumns();
        unset($foreignKey);
        return $associations;
    }

    /**
     * Get entity container of the schema.
     *
     * @return EntityContainer $entityContainer Entity container of the schema.
     */
    public function getEntityContainer()
    {
        $entityContainer = new EntityContainer();
        $entityContainer->name = $this->schema->namespace."Entities";
        $entityContainer->extends = null;
        $entityContainer->entitySets = $this->getEntitySets();
        $entityContainer->associationSets = $this->getAssociationSets();
        return $entityContainer;
    }

    /**
     * Get entity sets of container of the schema.
     *
     * @return array<EntitySet> $entitySets Entity sets of container of the schema.
     */
    public function getEntitySets()
    {
        $entitySets = array();
        $entities = $this->schemaManager->listTables();
        foreach ($entities as $entity) {
            $entitySets[$this->replaceSpaces($entity->getName())] 
                = $this->getEntitySet($entity);
            unset($entity);
        }
        return $entitySets;
    }

    /**
     * Get entity set of container of the schema.
     * 
     * @param EntityType &$entity Entity type object.
     *
     * @return EntitySet $entitySet Entity set of container of the schema.
     */
    public function getEntitySet(&$entity)
    {
        $entitySet = new EntitySet();
        $entitySet->name 
            = $this->replaceSpaces($this->getEntitySetName($entity->getName()));
        $entitySet->entityType = $this->schema->namespace."."
            .$this->replaceSpaces($this->getEntityTypeName($entity->getName()));
        unset($entity);
        return $entitySet;
    }

    /**
     * Get Association sets of container of the schema.
     *
     * @return $associationSets array<AssociationSet> Association sets of container
     *                                                of the schema.
     */
    public function getAssociationSets()
    {
        $associationSets = array();
        $entities = $this->schemaManager->listTables();
        foreach ($entities as $entity) {
            $foreignKeys = $this->schemaManager
                ->listTableForeignKeys($entity->getName());
            foreach ($foreignKeys as $foreignKey) {
                $associationSets[$foreignKey->getName()]
                    = $this->getAssociationSet($foreignKey, $entity->getName());
                unset($foreignKey);
            }
            unset($entity);
        }
        return $associationSets;
    }

    /**
     * Get Association set of container of the schema.
     * 
     * @param ForeignKey &$foreignKey    foreign key of entity type.
     * @param string     $localTableName local table name of foreign key.
     *
     * @return AssociationSet $associationSet Association set of container 
     *                                        of the schema.
     */
    public function getAssociationSet(&$foreignKey, $localTableName)
    {
        $associationSet = new AssociationSet();
        $associationSet->name = $foreignKey->getName();
        $associationSet->association = $this->schema->namespace."."
            .$foreignKey->getName();
        $associationSet->end1 = new AssociationSetEnd();
        $associationSet->end1->role 
            = $this->replaceSpaces($foreignKey->getForeignTableName());
        $associationSet->end1->entitySet = $this->replaceSpaces(
            $this->getEntitySetName($foreignKey->getForeignTableName())
        );
        $associationSet->end2 = new AssociationSetEnd();
        if ($foreignKey->getForeignTableName() == $localTableName) {
            $associationSet->end2->role 
                = $this->replaceSpaces($localTableName) . "1";
        } else {
            $associationSet->end2->role = $this->replaceSpaces($localTableName);
        }
        $associationSet->end2->entitySet = $this->replaceSpaces(
            $this->getEntitySetName($localTableName)
        );
        unset($foreignKey);
        return $associationSet;
    }

    /**
     * Modify schema by adding navigation from second end. And checks for many to
     * many multiplicity, if exists replace two one(1) to many(*) relations to one
     * many(*) to many(*) relationship.
     *
     * @param Schema &$schema Schema of the database after first pass.
     *
     * @return Schema $schema modified schema after second pass.
     */
    public function modifySchema(Schema &$schema)
    {
        try
        {
            foreach ($schema->entityTypes as $entityType) {
                foreach ($entityType->navigationProperties as $navigationProperty) {
                    $schema = $this->addNavigationProperty($navigationProperty);
                }
            }
            if ($this->serviceInfo['viewManyToManyRelationship'] == "true") {
                $this->findManyToManyRelationship();
            }
            if ($this->serviceInfo['followPularizeSingularizeRule'] == "true") {
                $this->convertToPularizeSingularize();
            }
            return $schema;
        }
        catch (Exception $e)
        {
            ODataConnectorForMySQLException::createInternalServerError();
        }
        
    }

    /**
     * Add navigation property in foreign table's entity type.
     *
     * @param NavigationProperty &$navigationProperty navigation property of 
     *                                                local table's entity type.
     *
     * @return void
     */
    public function addNavigationProperty(NavigationProperty &$navigationProperty)
    {
        if ($navigationProperty->fromRole != $navigationProperty->toRole) {
            $this->schema->entityTypes["$navigationProperty->name"]
                ->navigationProperties["$navigationProperty->relationship"] 
                    = new NavigationProperty();
            $this->schema->entityTypes["$navigationProperty->name"]
                ->navigationProperties["$navigationProperty->relationship"] 
                ->name = $navigationProperty->fromRole;
            $this->schema->entityTypes["$navigationProperty->name"]
                ->navigationProperties["$navigationProperty->relationship"]
                ->relationship = $navigationProperty->relationship;
            $this->schema->entityTypes["$navigationProperty->name"]
                ->navigationProperties["$navigationProperty->relationship"]
                ->fromRole = $navigationProperty->toRole;
            $this->schema->entityTypes["$navigationProperty->name"]
                ->navigationProperties["$navigationProperty->relationship"]
                ->toRole = $navigationProperty->fromRole;
        } else {
            $navigationPropertyName = $navigationProperty->name;
            $navigationPropertyRelationship = $navigationProperty->relationship;
            $navigationPropertyFromRole = $navigationProperty->fromRole . "1";
            $navigationPropertyToRole = $navigationProperty->toRole;
            
            $this->schema->entityTypes["$navigationPropertyName"]
                ->navigationProperties["$navigationPropertyRelationship"]
                ->name = $this->getEntitySetName($navigationPropertyName) . "1";
            $this->schema->entityTypes["$navigationPropertyName"]
                ->navigationProperties["$navigationPropertyRelationship"]
                ->relationship = $navigationPropertyRelationship;
            $this->schema->entityTypes["$navigationPropertyName"]
                ->navigationProperties["$navigationPropertyRelationship"]
                ->toRole = $navigationPropertyFromRole;
            $this->schema->entityTypes["$navigationPropertyName"]
                ->navigationProperties["$navigationPropertyRelationship"]
                ->fromRole = $navigationPropertyToRole;
            
            $this->schema->entityTypes["$navigationPropertyName"]
                ->navigationProperties["$navigationPropertyRelationship".'1'] 
                    = new NavigationProperty();
            $this->schema->entityTypes["$navigationPropertyName"]
                ->navigationProperties["$navigationPropertyRelationship".'1']
                ->name = $this->getEntityTypeName($navigationPropertyName) . "1";
            $this->schema->entityTypes["$navigationPropertyName"]
                ->navigationProperties["$navigationPropertyRelationship".'1']
                ->relationship = $navigationPropertyRelationship;
            $this->schema->entityTypes["$navigationPropertyName"]
                ->navigationProperties["$navigationPropertyRelationship".'1']
                ->fromRole = $navigationPropertyFromRole;
            $this->schema->entityTypes["$navigationPropertyName"]
                ->navigationProperties["$navigationPropertyRelationship".'1']
                ->toRole = $navigationPropertyToRole;
        }
        return $this->schema;
    }

    /**
     * Finds Many to Many relationships from the MySQL schema objects.
     *
     * @return void
     */
    public function findManyToManyRelationship()
    {
        $entities = $this->schemaManager->listTables();
        foreach ($entities as $entity) {
            $primaryKeys = $entity->getPrimaryKey()->getColumns();
            $columns = $entity->getColumns();
            $columnNames = array();
            foreach ($columns as $column) {
                $columnNames[] = $column->getName();
            }
            if (array_diff($columnNames, $primaryKeys) == null) {
                $this->addManyToManyRelationShip(
                    $this->replaceSpaces(
                        $entity->getName()
                    )
                );
            }
            unset($entity);
        }
    }

    /**
     * Adds many to many relationship in the schema and removes one to many 
     * relation ships.
     *
     * @param string $entity EntityType name on which many to many relationship
     *                       is added.
     *
     * @return void
     */
    public function addManyToManyRelationShip($entity)
    {
        foreach ($this->schema->entityTypes["$entity"]->navigationProperties 
            as $navigationProperty) {
                $changeEntityType[] = $navigationProperty->toRole;
                $navigationName[] = $navigationProperty->relationship;
        }
        $this->schema->entityTypes["{$changeEntityType['0']}"]
            ->navigationProperties["{$navigationName['0']}"]->viewable = false;
        $this->schema->entityTypes["{$changeEntityType['0']}"]
            ->navigationProperties["$entity"] = new NavigationProperty();
        $this->schema->entityTypes["{$changeEntityType['0']}"]
            ->navigationProperties["$entity"]->name = $changeEntityType['1'];
        $this->schema->entityTypes["{$changeEntityType['0']}"]
            ->navigationProperties["$entity"]->toRole = $changeEntityType['1'];
        $this->schema->entityTypes["{$changeEntityType['0']}"]
            ->navigationProperties["$entity"]->fromRole = $changeEntityType['0'];
        $this->schema->entityTypes["{$changeEntityType['0']}"]
            ->navigationProperties["$entity"]->relationship = $entity;

        $this->schema->entityTypes["{$changeEntityType['1']}"]
            ->navigationProperties["{$navigationName['1']}"]->viewable = false;
        $this->schema->entityTypes["{$changeEntityType['1']}"]
            ->navigationProperties["$entity"] = new NavigationProperty();
        $this->schema->entityTypes["{$changeEntityType['1']}"]
            ->navigationProperties["$entity"]->name = $changeEntityType['0'];
        $this->schema->entityTypes["{$changeEntityType['1']}"]
            ->navigationProperties["$entity"]->toRole = $changeEntityType['0'];
        $this->schema->entityTypes["{$changeEntityType['1']}"]
            ->navigationProperties["$entity"]->fromRole = $changeEntityType['1'];
        $this->schema->entityTypes["{$changeEntityType['1']}"]
            ->navigationProperties["$entity"]->relationship = $entity;

        $this->schema->entityTypes["$entity"]->viewable = false;

        $this->schema->associations["$entity"] = new Association();
        $this->schema->associations["$entity"]->name = $entity;
        $this->schema->associations["$entity"]->end1 = new AssociationEnd();
        $this->schema->associations["$entity"]->end1->type 
            = $this->schema->namespace."."
            .$this->getEntityTypeName($changeEntityType['0']);
        $this->schema->associations["$entity"]->end1->multiplicity = "*";
        $this->schema->associations["$entity"]->end1->role = $changeEntityType['0'];
        $this->schema->associations["$entity"]->end2 = new AssociationEnd();
        $this->schema->associations["$entity"]->end2->type 
            = $this->schema->namespace."."
            .$this->getEntityTypeName($changeEntityType['1']);
        $this->schema->associations["$entity"]->end2->multiplicity = "*";
        $this->schema->associations["$entity"]->end2->role = $changeEntityType['1'];
        $this->schema->associations["$entity"]->viewable = true;
        $this->schema->associations["{$navigationName['0']}"]->viewable = false;
        $this->schema->associations["{$navigationName['1']}"]->viewable = false;

        $this->schema->entityContainer->entitySets["$entity"]->viewable = false;

        $this->schema->entityContainer->associationSets["$entity"] 
            = new AssociationSet();
        $this->schema->entityContainer->associationSets["$entity"]->name = $entity;
        $this->schema->entityContainer->associationSets["$entity"]->association 
            = $this->schema->namespace.".".$entity;
        $this->schema->entityContainer->associationSets["$entity"]->viewable = true;
        $this->schema->entityContainer->associationSets["$entity"]->end1 
            = new AssociationSetEnd();
        $this->schema->entityContainer->associationSets["$entity"]->end1->role 
            = $changeEntityType['0'];
        $this->schema->entityContainer->associationSets["$entity"]->end1->entitySet 
            = $this->getEntitySetName($changeEntityType['0']);
        $this->schema->entityContainer->associationSets["$entity"]->end2 
            = new AssociationSetEnd();
        $this->schema->entityContainer->associationSets["$entity"]->end2->role 
            = $changeEntityType['1'];
        $this->schema->entityContainer->associationSets["$entity"]->end2->entitySet 
            = $this->getEntitySetName($changeEntityType['1']);
        $this->schema->entityContainer->associationSets["$entity"]->viewable = true;
        $this->schema->entityContainer->associationSets["{$navigationName['0']}"]
            ->viewable = false;
        $this->schema->entityContainer->associationSets["{$navigationName['1']}"]
            ->viewable = false;
    }

    /**
     * Convert entity type and entity set name as follows singularize or pluralize 
     * naming conventions.
     *
     * @return void
     */
    public function convertToPularizeSingularize()
    {
        foreach ($this->schema->associations as $association) {
            if ($association->viewable == true 
                and $association->end1->type != $association->end2->type
            ) {
                if ($association->end1->multiplicity == "1" 
                    or $association->end1->multiplicity == "0..1"
                ) {
                    $this->schema->entityTypes["{$association->end1->role}"]
                        ->navigationProperties["{$association->name}"]->fromRole 
                            = $this->getEntityTypeName($association->end1->role);
                    $this->schema->entityTypes["{$association->end2->role}"]
                        ->navigationProperties["{$association->name}"]->toRole 
                            = $this->getEntityTypeName($association->end1->role);
                } else {
                    $this->schema->entityTypes["{$association->end1->role}"]
                        ->navigationProperties["{$association->name}"]->fromRole 
                            = $this->getEntitySetName($association->end1->role);
                    $this->schema->entityTypes["{$association->end2->role}"]
                        ->navigationProperties["{$association->name}"]->toRole 
                            = $this->getEntitySetName($association->end1->role);
                }
                if ($association->end2->multiplicity == "1" 
                    or $association->end2->multiplicity == "0..1"
                ) {
                    $this->schema->entityTypes["{$association->end2->role}"]
                        ->navigationProperties["{$association->name}"]->fromRole 
                            = $this->getEntityTypeName($association->end2->role);
                    $this->schema->entityTypes["{$association->end1->role}"]
                        ->navigationProperties["{$association->name}"]->toRole 
                            = $this->getEntityTypeName($association->end2->role);
                } else {
                    $this->schema->entityTypes["{$association->end2->role}"]
                        ->navigationProperties["{$association->name}"]->fromRole 
                            = $this->getEntitySetName($association->end2->role);
                    $this->schema->entityTypes["{$association->end1->role}"]
                        ->navigationProperties["{$association->name}"]->toRole 
                            = $this->getEntitySetName($association->end2->role);
                }
    
                if ($association->end1->multiplicity == "1" 
                    or $association->end1->multiplicity == "0..1"
                ) {
                    $association->end1->role 
                        = $this->getEntityTypeName($association->end1->role);
                    $this->schema->entityContainer
                        ->associationSets["{$association->name}"]->end1->role 
                            = $association->end1->role;
                } else {
                    $association->end1->role 
                        = $this->getEntitySetName($association->end1->role);
                    $this->schema->entityContainer
                        ->associationSets["{$association->name}"]->end1->role 
                            = $association->end1->role;
                }
                if ($association->referentialConstraint->principal!= null) {
                    $association->referentialConstraint->principal->role 
                        = $association->end1->role;
                }
                if ($association->end2->multiplicity == "1" 
                    or $association->end2->multiplicity == "0..1"
                ) {
                    $association->end2->role 
                        = $this->getEntityTypeName($association->end2->role);
                    $this->schema->entityContainer
                        ->associationSets["{$association->name}"]->end2->role 
                            = $association->end2->role;
                } else {
                    $association->end2->role 
                        = $this->getEntitySetName($association->end2->role);
                    $this->schema->entityContainer
                        ->associationSets["{$association->name}"]->end2->role 
                            = $association->end2->role;
                }
                if ($association->referentialConstraint->dependent != null) {
                    $association->referentialConstraint->dependent->role 
                        = $association->end2->role;
                }
            } else if ($association->viewable == true 
                and $association->end1->type == $association->end2->type
            ) {
                if ($association->end1->multiplicity == "1" 
                    or $association->end1->multiplicity == "0..1"
                ) {
                    $entityType = explode('.', $association->end1->type);
                    $association->end1->role 
                        = $this->getEntityTypeName($entityType['1']);
                    $this->schema->entityContainer
                        ->associationSets["{$association->name}"]->end1->role 
                            = $association->end1->role;
                } else {
                    $entityType = explode('.', $association->end1->type);
                    $association->end1->role 
                        = $this->getEntitySetName($entityType['1']);
                    $this->schema->entityContainer
                        ->associationSets["{$association->name}"]->end1->role 
                            = $association->end1->role;
                }
                if ($association->referentialConstraint->principal!= null) {
                    $association->referentialConstraint->principal->role 
                        = $association->end1->role;
                }
                if ($association->end2->multiplicity == "1" 
                    or $association->end2->multiplicity == "0..1"
                ) {
                    $entityType = explode('.', $association->end2->type);
                    $association->end2->role 
                        = $this->getEntityTypeName($entityType['1']);
                    $this->schema->entityContainer
                        ->associationSets["{$association->name}"]->end2->role 
                            = $association->end2->role;
                } else {
                    $entityType = explode('.', $association->end2->type);
                    $association->end2->role 
                        = $this->getEntitySetName($entityType['1']);
                    $this->schema->entityContainer
                        ->associationSets["{$association->name}"]->end2->role 
                            = $association->end2->role;
                }
                if ($association->referentialConstraint->dependent != null) {
                    $association->referentialConstraint->dependent->role 
                        = $association->end2->role;
                }
            }
        }
    }

    /**
     * adds entity information in the EDMX file.
     * 
     * @return EntityNameInformation
     */
    public function addEntityInfo()
    {
        $entityNameInformation = new EntityNameInformation();
        $entities = $this->schemaManager->listTableNames();
        $entityNameInformation->entityNames = array();
        foreach ($entities as $entity) {
            $entityNameInformation->entityNames["$entity"] 
                = $this->getEntityName($entity);
        }
        return $entityNameInformation;
    }

    /**
     * get entity name from database, entity type and entity set.
     * 
     * @param string $entity Entity type name from database.
     * 
     * @return EntityName
     */
    public function getEntityName($entity)
    {
        $entityName = new EntityName();
        $entityName->dbName = $entity;
        $entityName->entityTypeName 
            = $this->replaceSpaces($this->getEntityTypeName($entity));
        $entityName->entitySetName 
            = $this->replaceSpaces($this->getEntitySetName($entity));
        return $entityName;
    }

    /**
     * adds mapping details of table and column names. 
     * 
     * @return MappingDetails
     */
    public function addMappingDetails()
    {
        $mappingDetails = new MappingDetails();
        $mappingDetails->mapEntities = array();
        $entities = $this->schemaManager->listTables();
        foreach ($entities as $entity) {
            $entityName = $entity->getName();
            $mappingDetails->mapEntities["$entityName"] = new MapEntity();
            $mappingDetails->mapEntities["$entityName"]->usrName = $entityName;
            $mappingDetails->mapEntities["$entityName"]->dbName = $entityName;
            $columns = $this->schemaManager->listTableColumns("$entityName");
            foreach ($columns as $column) {
                $columnName = $column->getName();
                $mappingDetails->mapEntities["$entityName"]
                    ->mapProperties["$columnName"] = new MapProperty();
                $mappingDetails->mapEntities["$entityName"]
                    ->mapProperties["$columnName"]->entityName = $entityName;
                $mappingDetails->mapEntities["$entityName"]
                    ->mapProperties["$columnName"]->usrPropertyName = $columnName;
                $mappingDetails->mapEntities["$entityName"]
                    ->mapProperties["$columnName"]->dbPropertyName = $columnName;
            }
            unset ($columns);
        }
        unset ($entities);
        return $mappingDetails;
    }
    /**
     * Start Generating EDMX file.
     *
     * @return EDMX xml object.
     */
    public function generateEDMX()
    {
        $this->xmlWriter->startElementNS(
            ODataConnectorForMySQLConstants::EDMX_NAMESPACE_PREFIX, 
            ODataConnectorForMySQLConstants::EDMX_ELEMENT, 
            ODataConnectorForMySQLConstants::EDMX_NAMESPACE_1_0
        );
        $this->xmlWriter->writeAttribute(
            ODataConnectorForMySQLConstants::EDMX_VERSION, 
            ODataConnectorForMySQLConstants::EDMX_VERSION_VALUE
        );
        $this->xmlWriter->endAttribute();
        $this->xmlWriter->startElementNS(
            ODataConnectorForMySQLConstants::EDMX_NAMESPACE_PREFIX, 
            ODataConnectorForMySQLConstants::EDMX_DATASERVICES_ELEMENT, null
        );
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::SCHEMA);
        $this->xmlWriter->writeAttribute(
            ODataConnectorForMySQLConstants::NAMESPACE1, $this->schema->namespace
        );
        $this->xmlWriter->writeAttributeNS(
            ODataConnectorForMySQLConstants::XMLNS_NAMESPACE_PREFIX, 
            ODataConnectorForMySQLConstants::ODATA_NAMESPACE_PREFIX, null, 
            ODataConnectorForMySQLConstants::ODATA_NAMESPACE
        );
        $this->xmlWriter->writeAttributeNS(
            ODataConnectorForMySQLConstants::XMLNS_NAMESPACE_PREFIX, 
            ODataConnectorForMySQLConstants::ODATA_METADATA_NAMESPACE_PREFIX, 
            null, ODataConnectorForMySQLConstants::ODATA_METADATA_NAMESPACE
        );
        $this->xmlWriter->writeAttribute(
            ODataConnectorForMySQLConstants::XMLNS_NAMESPACE_PREFIX, 
            ODataConnectorForMySQLConstants::CSDL_VERSION_1_0
        );
        $this->xmlWriter->endAttribute();
        try
        {
            $this->writeEntityType();
            $this->writeAssociations();
            $this->writeEntityContainer();
            $this->writeEntityInfo();
            $this->writeMappingDetails();
        }
        catch (Exception $e)
        {
            ODataConnectorForMySQLException::createInternalServerError();
        }
        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();
        return $this->xmlWriter->outputMemory(true);
    }

    /**
     * Write Tables in EDMX format from object model.
     *
     * @return void
     */
    public function writeEntityType()
    {
        foreach ($this->schema->entityTypes as $entityType) {
            if ($entityType->viewable == true) {
                $this->xmlWriter->startElement(
                    ODataConnectorForMySQLConstants::ENTITY_TYPE
                );
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::NAME, $entityType->name
                );
                $this->writeKey($entityType->key);
                $this->writeProperty($entityType->properties);
                $this->writeNavigationProperty($entityType->navigationProperties);
                $this->xmlWriter->endElement();
            }
        }
    }

    /**
     * Write primary keys for Tables in EDMX format from object model.
     *
     * @param array<Key> &$entityKeys array of key object of object model.
     *
     * @return void
     */
    public function writeKey(&$entityKeys)
    {
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::KEY);
        foreach ($entityKeys->propertyRefs as $propertyRef) {
            $this->xmlWriter->startElement(
                ODataConnectorForMySQLConstants::PROPERTY_REF
            );
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::NAME, $propertyRef
            );
            $this->xmlWriter->endAttribute();
            $this->xmlWriter->endElement();
        }
        $this->xmlWriter->endElement();
    }

    /**
     * Write properties of Tables in EDMX format from object model.
     *
     * @param array<Property> &$properties array of property object of object model.
     *
     * @return void
     */
    public function writeProperty(&$properties)
    {
        foreach ($properties as $property) {
            $this->xmlWriter->startElement(
                ODataConnectorForMySQLConstants::PROPERTY
            );
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::NAME, $property->name
            );
            $doctrineType = $property->type;
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::TYPE1, 
                $this->mapODataType["$doctrineType"]
            );
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::NULLABLE, 
                $property->nullable ? "true" : "false"
            );
            if ($property->maxLength) {
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::MAX_LENGTH, 
                    $property->maxLength > 255 ? "Max" : $property->maxLength
                );
            }
            if ($property->precision) {
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::PRECISION, 
                    $property->precision > 255 ? "Max" : $property->precision
                );
            }
            if ($property->scale) {
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::SCALE, $property->scale
                );
            }
            if ($property->unicode) {
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::UNSIGNED, $property->unicode
                );
            }
            if ($property->fixedLength) {
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::FIXED, $property->fixedLength
                );
            }
            if ($property->defaultValue) {
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::DEFAULT1, 
                    $property->defaultValue
                );
            }
            $this->xmlWriter->endAttribute();
            $this->xmlWriter->endElement();
        }
    }

    /**
     * Writes navigation Property of the Entity type.
     *
     * @param array<NavigationProperty> &$navigationProperties array of navigation 
     *                                                         property of object 
     *                                                         model.
     *
     * @return void
     */
    public function writeNavigationProperty(&$navigationProperties)
    {
        //print_r($navigationProperties);
        foreach ($navigationProperties as $navigationProperty) {
            if ($navigationProperty->viewable == true) {
                $this->xmlWriter->startElement(
                    ODataConnectorForMySQLConstants::NAVIGATION_PROPERTY
                );
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::NAME, $navigationProperty->name
                );
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::RELATIONSHIP, 
                    $navigationProperty->relationship
                );
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::FROM_ROLE, 
                    $navigationProperty->fromRole
                );
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::TO_ROLE, 
                    $navigationProperty->toRole
                );
                $this->xmlWriter->endAttribute();
                $this->xmlWriter->endElement();
            }
        }
    }

    /**
     * Write Associations between entity types for the given database.
     *
     * @return void
     */
    public function writeAssociations()
    {

        foreach ($this->schema->associations as $association) {
            $this->writeAssociation($association);
        }
    }

    /**
     * Write Associations between entity types for the given database.
     *
     * @param Association &$association Association object of object model.
     *
     * @return void
     */
    public function writeAssociation(Association &$association)
    {
        if ($association->viewable == true) {
            $this->xmlWriter->startElement(
                ODataConnectorForMySQLConstants::ASSOCIATION
            );
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::NAME, $association->name
            );
            $this->xmlWriter->endAttribute();
            $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::END);
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::ROLE, $association->end1->role
            );
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::TYPE1, $association->end1->type
            );
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::MULTIPLICITY, 
                $association->end1->multiplicity
            );
            $this->xmlWriter->endAttribute();
            $this->xmlWriter->endElement();
            $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::END);
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::ROLE, $association->end2->role
            );
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::TYPE1, $association->end2->type
            );
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::MULTIPLICITY, 
                $association->end2->multiplicity
            );
            $this->xmlWriter->endAttribute();
            $this->xmlWriter->endElement();
            if ($association->referentialConstraint != null) {
                $this->xmlWriter->startElement(
                    ODataConnectorForMySQLConstants::REFERENTIAL_CONSTRAINT
                );
                if ($association->referentialConstraint->principal != null) {
                    $this->xmlWriter->startElement(
                        ODataConnectorForMySQLConstants::PRINCIPAL
                    );
                    $this->xmlWriter->writeAttribute(
                        ODataConnectorForMySQLConstants::ROLE, 
                        $association->referentialConstraint->principal->role
                    );
                    $this->xmlWriter->endAttribute();
                    if ($association->referentialConstraint->principal->propertyRefs != null) {
                        foreach ($association->referentialConstraint->principal
                            ->propertyRefs as $propertyRef
                        ) {
                            $this->xmlWriter->startElement(
                                ODataConnectorForMySQLConstants::PROPERTY_REF
                            );
                            $this->xmlWriter->writeAttribute(
                                ODataConnectorForMySQLConstants::NAME, $propertyRef
                            );
                            $this->xmlWriter->endAttribute();
                            $this->xmlWriter->endElement();
                        }
                    }
                    $this->xmlWriter->endElement();
                }
                if ($association->referentialConstraint->dependent !=null) {
                    $this->xmlWriter->startElement(
                        ODataConnectorForMySQLConstants::DEPENDENT
                    );
                    $this->xmlWriter->writeAttribute(
                        ODataConnectorForMySQLConstants::ROLE, 
                        $association->referentialConstraint->dependent->role
                    );
                    $this->xmlWriter->endAttribute();
                    if ($association->referentialConstraint->dependent->propertyRefs != null) {
                        foreach ($association->referentialConstraint
                            ->dependent->propertyRefs as $propertyRef
                        ) {
                            $this->xmlWriter->startElement(
                                ODataConnectorForMySQLConstants::PROPERTY_REF
                            );
                            $this->xmlWriter->writeAttribute(
                                ODataConnectorForMySQLConstants::NAME, $propertyRef
                            );
                            $this->xmlWriter->endAttribute();
                            $this->xmlWriter->endElement();
                        }
                    }
                    $this->xmlWriter->endElement();
                }
                $this->xmlWriter->endElement();
            }
            $this->xmlWriter->endElement();
        }
    }

    /**
     * Write Entity Container for given database.
     *
     * @return void
     */
    public function writeEntityContainer()
    {
        $this->xmlWriter->startElement(
            ODataConnectorForMySQLConstants::ENTITY_CONTAINER
        );
        $this->xmlWriter->writeAttribute(
            ODataConnectorForMySQLConstants::NAME, 
            $this->schema->entityContainer->name
        );
        $this->xmlWriter->endAttribute();
        $this->writeEntitySet();
        $this->writeAssociationSets();
        $this->xmlWriter->endElement();
    }

    /**
     * Write Entity Set for given database.
     *
     * @return void
     */
    public function writeEntitySet()
    {
        foreach ($this->schema->entityContainer->entitySets as $entitySet) {
            if ($entitySet->viewable == true) {
                $this->xmlWriter->startElement(
                    ODataConnectorForMySQLConstants::ENTITY_SET
                );
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::NAME, 
                    $entitySet->name
                );
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::ENTITY_TYPE, 
                    $entitySet->entityType
                );
                $this->xmlWriter->endAttribute();
                $this->xmlWriter->endElement();
            }
        }
    }

    /**
     * Write Association Sets for given database.
     *
     * @return void
     */
    public function writeAssociationSets()
    {
        foreach ($this->schema->entityContainer->associationSets 
            as $associationSet
        ) {
            $this->writeAssociationSet($associationSet);
        }
    }

    /**
     * Write Association Set for each association.
     *
     * @param AssociationSet &$associationSet Association set object of Object model.
     *
     * @return void
     */
    public function writeAssociationSet(AssociationSet &$associationSet)
    {
        if ($associationSet->viewable == true) {
            $this->xmlWriter->startElement(
                ODataConnectorForMySQLConstants::ASSOCIATION_SET
            );
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::NAME, $associationSet->name
            );
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::ASSOCIATION, 
                $associationSet->association
            );
            $this->xmlWriter->endAttribute();
            $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::END);
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::ROLE, 
                $associationSet->end1->role
            );
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::ENTITY_SET, 
                $associationSet->end1->entitySet
            );
            $this->xmlWriter->endAttribute();
            $this->xmlWriter->endElement();
            $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::END);
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::ROLE, 
                $associationSet->end2->role
            );
            $this->xmlWriter->writeAttribute(
                ODataConnectorForMySQLConstants::ENTITY_SET, 
                $associationSet->end2->entitySet
            );
            $this->xmlWriter->endAttribute();
            $this->xmlWriter->endElement();
            $this->xmlWriter->endElement();
        }
    }

    /**
     * Writes entity information in EDMX.
     * 
     * @return void
     */
    public function writeEntityInfo()
    {
        $this->xmlWriter->startElement(
            ODataConnectorForMySQLConstants::ENTITY_NAME_INFO
        );
        foreach ($this->schema->entityNameInformation->entityNames as $entityName) {
            $name = $this->replaceSpaces($entityName->dbName);
            if ($this->schema->entityTypes["$name"]->viewable == true) {
                $this->xmlWriter->startElement(
                    ODataConnectorForMySQLConstants::ENTITY_NAME
                );
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::DB_NAME, $entityName->dbName
                );
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::ENTITY_TYPE_NAME, 
                    $entityName->entityTypeName
                );
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::ENTITY_SET_NAME, 
                    $entityName->entitySetName
                );
                $this->xmlWriter->endAttribute();
                $this->xmlWriter->endElement();
            }
        }
        $this->xmlWriter->endElement();
    }

    /**
     * Writes entity mapping details in EDMX. 
     * 
     * @return void
     */
    public function writeMappingDetails()
    {
        $this->xmlWriter->startElement(
            ODataConnectorForMySQLConstants::MAPPING_DETAILS
        );
        foreach ($this->schema->mappingDetails->mapEntities as $mapEntity) {
            $entityTypeName = $this->replaceSpaces($mapEntity->dbName);
            if ($this->schema->entityTypes["$entityTypeName"]->viewable == true) {
                $this->xmlWriter->startElement(
                    ODataConnectorForMySQLConstants::MAP_ENTITY
                );
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::USER_ENTITY_NAME, 
                    $mapEntity->usrName
                );
                $this->xmlWriter->writeAttribute(
                    ODataConnectorForMySQLConstants::DB_ENTITY_NAME, 
                    $mapEntity->dbName
                );
                foreach ($mapEntity->mapProperties as $mapProperty) {
                    $this->xmlWriter->startElement(
                        ODataConnectorForMySQLConstants::MAP_PROPERTY
                    );
                    $this->xmlWriter->writeAttribute(
                        ODataConnectorForMySQLConstants::USER_ENTITY_NAME, 
                        $mapProperty->entityName
                    );
                    $this->xmlWriter->writeAttribute(
                        ODataConnectorForMySQLConstants::USER_PROPERTY_NAME, 
                        $mapProperty->usrPropertyName
                    );
                    $this->xmlWriter->writeAttribute(
                        ODataConnectorForMySQLConstants::DB_PROPERTY_NAME, 
                        $mapProperty->dbPropertyName
                    );
                    $this->xmlWriter->endElement();
                }
                $this->xmlWriter->endElement();
            }
        }
        $this->xmlWriter->endElement();
    }
    /**
     * Get entity type name in singular and replace spaces if any.
     *
     * @param string $entity name of table in database.
     *
     * @return string $entity singular name with out spaces.
     */
    public function getEntityTypeName($entity)
    {
        return \Inflector::singularize($entity);
    }

    /**
     * Get entity set name in prural and replace spaces if any.
     *
     * @param string $entity name of table in database.
     *
     * @return string $entity prural name with out spaces.
     */
    public function getEntitySetName($entity)
    {
        return \Inflector::pluralize($entity);
    }

    /**
     * Get Multiplicity of the local table in Association.
     *
     * @param ForeignKeyConstraint &$foreignKey Foreign key object of table.
     * @param string               $entity      Local table name.
     *
     * @return string multiplicity of local table zero or one, one or many.
     */
    public function getLocalMultiplicity(&$foreignKey, $entity)
    {
        $indexes = $this->schemaManager->listTableIndexes($entity);
        $localColumns = $foreignKey->getLocalColumns();
        foreach ($indexes as $index) {
            $indexColumns = $index->getColumns();
            if (array_diff($indexColumns, $localColumns) == null) {
                if ($index->isUnique()) {
                    if ($index->isPrimary()) {
                        return "1";
                    } else {
                        return "0..1";
                    }
                } else {
                    return "*";
                }
            }
        }
    }

    /**
     * Get Multiplicity of the foreign table in Association.
     *
     * @param ForeignKeyConstraint &$foreignKey Foreign key object of table.
     *
     * @return string multiplicity of foreign table zero or one, one or many.
     */
    public function getForeignMultiplicity(&$foreignKey)
    {
        $foreignTableName = $foreignKey->getForeignTableName();
        $indexes = $this->schemaManager->listTableIndexes($foreignTableName);
        $foreignColumns = $foreignKey->getForeignColumns();
        foreach ($indexes as $index) {
            $indexColumns = $index->getColumns();
            if (array_diff($indexColumns, $foreignColumns) == null) {
                if ($index->isUnique()) {
                    if ($index->isPrimary()) {
                        return "1";
                    } else {
                        return "0..1";
                    }
                } else {
                    return "*";
                }
            }
        }
    }

    /**
     * Remove spaces from the name of entity type.
     *
     * @param string $entityType name of the entity type from which spaces are 
     *                           to be replaced by underscores.
     *
     * @return string $entityType name after spaces replaced by underscores.
     */
    public function replaceSpaces($entityType)
    {
        return str_replace(" ", "_", $entityType);
    }
}
?>