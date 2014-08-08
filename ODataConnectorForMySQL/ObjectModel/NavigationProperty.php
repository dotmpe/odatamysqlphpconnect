<?php
/** 
 * Representation of an entity type navigation property.
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
 * Type to represent an entity type navigation property.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class NavigationProperty
{
    /**
     * 
     * Entity type navigation property name
     * @var string
     */
    public $name;
    /**
     * 
     * Entity type navigation property relationship
     * @var string
     */
    public $relationship;
    /**
     * 
     * Entity type navigation property to role
     * @var string
     */
    public $toRole;
    /**
     * 
     * Entity type navigation property from role
     * @var string
     */
    public $fromRole;
    /**
     * NavigationProperty is viewable or not
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