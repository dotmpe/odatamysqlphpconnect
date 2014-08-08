<?php
/** 
 * Representation of an entity container.
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
use ODataConnectorForMySQL\ObjectModel\EntitySet;
use ODataConnectorForMySQL\ObjectModel\AssociationSet;
/**
 * Type to represent an entity container.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class EntityContainer
{
    /**
     * 
     * Entity container name
     * @var string
     */
    public $name;
    /**
     * 
     * Entity container Extends
     * @var string
     */
    public $extends;
    /**
     * 
     * Entity container Entity sets
     * @var array<EntitySet>
     */
    public $entitySets;
    /**
     * 
     * Entity container Association Sets 
     * @var array<AssociationSet>
     */
    public $associationSets;
    /**
     * Constructor to Initialize Objects and array.
     */
    function __construct()
    {
        $this->entitySets = array();
        $this->associationSets = array();
    }
}
?>