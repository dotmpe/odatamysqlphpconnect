<?php
/** 
 * Representation of an entity type.
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
use ODataConnectorForMySQL\ObjectModel\Key;
use ODataConnectorForMySQL\ObjectModel\Property;
use ODataConnectorForMySQL\ObjectModel\NavigationProperty;
/**
 * Type to represent an entity type.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class EntityType
{
    /**
     * 
     * Entity type name
     * @var string
     */
    public $name;
    /**
     * 
     * Entity type primary key
     * @var Key
     */
    public $key;
    /**
     * 
     * Entity type properties
     * @var array<Property>
     */
    public $properties;
    /**
     * 
     * Entity type navigation properties 
     * @var array<NavigationProperty>
     */
    public $navigationProperties;
    /**
     * Entity type is viewable or not
     * @var boolean
     */
    public $viewable;
    /**
     * Constructor to Initialize Objects and array.
     */
    function __construct()
    {
        $this->properties = array();
        $this->navigationProperties = array();
        $this->key = null;
        $this->viewable = true;
    }
}
?>