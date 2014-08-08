<?php
/** 
 * Contains the mapping details of the entity from DB.
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
 * Type to represent mapping details of the entity from DB.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class MapEntity
{
    /**
     * 
     * User define Entity type name
     * @var string
     */
    public $usrName;
    /**
     * 
     * Entity type name of DB
     * @var string
     */
    public $dbName;
    /**
     * 
     * Mapping details of Entity type properties
     * @var array<MapProperty>
     */
    public $mapProperties;
    /**
     * Constructor to Initialize Objects and array.
     */
    function __construct()
    {
        $this->mapProperties = array();
    }
}
?>