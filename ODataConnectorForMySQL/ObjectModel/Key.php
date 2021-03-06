<?php
/** 
 * Representation of an entity type primary key.
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
use ODataConnectorForMySQL\ObjectModel\PropertyRef;
/**
 * Type to represent an entity type primary key.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class Key
{
    /**
     * 
     * Entity type property references.
     * @var array<PropertyRef>
     */
    public $propertyRefs;
    /**
     * Constructor to Initialize Objects and array.
     */
    function __construct()
    {
        $this->propertyRefs = array();
    }
}
?>