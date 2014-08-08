<?php
/** 
 * Representation of an entity container entity set.
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
 * Type to represent an entity container entity set.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class EntitySet
{
    /**
     * 
     * Entity set name.
     * @var name
     */
    public $name;
    /**
     * 
     * Entity type name.
     * @var name
     */
    public $entityType;
    /**
     * EntitySet is viewable or not
     * @var boolean
     */
    public $viewable;
    /**
     * Constructor to Initialize Objects and array.
     */
    function __construct()
    {
        $this->viewable = true;
    }
}
?>