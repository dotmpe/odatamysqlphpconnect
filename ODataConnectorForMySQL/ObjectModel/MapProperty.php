<?php
/** 
 * Contains the mapping details of the property of entity from DB.
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
/**
 * Type to represent mapping details of the property of entity from DB.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class MapProperty
{
    /**
     * 
     * Entity type name of the property.
     * @var string
     */
    public $entityName;
    /**
     * 
     * User define property name.
     * @var string
     */
    public $usrPropertyName;
    /**
     * 
     * Property name from the db.
     * @var string
     */
    public $dbPropertyName;
    
    /**
     * Constructor to Initialize Objects and array.
     */
    function __construct()
    {
    }
}
?>