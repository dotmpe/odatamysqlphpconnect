<?php
/** 
 * Representation of an association Referential Constraint principal.
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
 * Type to represent an association Referential Constraint principal.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class Principal
{
    /**
     * 
     * Referential Constraint principal role.
     * @var string
     */
    public $role;
    /**
     * 
     * Referential Constraint principal property references.
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