<?php
/** 
 * Representation of an edmx schema.
 * 
 * PHP version 5.3
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   SVN: 1.0
 * @link      
 * 
 */
namespace ODataConnectorForMySQL\ObjectModel;
use ODataConnectorForMySQL\ObjectModel\EntityType;
use ODataConnectorForMySQL\ObjectModel\Association;
use ODataConnectorForMySQL\ObjectModel\EntityContainer;
/**
 * Type to represent an edmx schema.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class Schema
{
    /**
     * 
     * Schema namespace
     * @var string
     */
    public $namespace;
    /**
     * 
     * Entity types
     * @var array<EntityType>
     */
    public $entityTypes;
    /**
     * 
     * Schema Associations 
     * @var array<Association>
     */
    public $associations;
    /**
     * 
     * Schema Entity Container 
     * @var EntityContainer
     */
    public $entityContainer;
    /**
     * 
     * Entity Name Information 
     * @var EntityNameInformation
     */
    public $entityNameInformation;
    /**
     * 
     * Mapping details of entities and their properties 
     * @var MappingDetails
     */
    public $mappingDetails;
    /**
     * Constructor to Initialize Objects and array.
     */
    function __construct()
    {
        $this->entityTypes = array();
        $this->associations = array();
        $this->entityContainer = null;
        $this->entityNameInformation = null;
        $this->mappingDetails = null;
    }
}
?>