<?php
/** 
 * Entity names in the db and metadata.
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
 * Entity names in the db and metadata.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class EntityName
{
    /**
     * 
     * DB name of the Entity.
     * @var string
     */
    public $dbName;
    /**
     * 
     * entity type name of the entity.
     * @var string
     */
    public $entityTypeName;
    /**
     * 
     * Entity set name of the entity.
     * @var string
     */
    public $entitySetName;
    /**
     * Constructor to Initialize Objects and array.
     */
    function __construct()
    {
    }
}
?>