<?php
/** 
 * Representation of an association end.
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
 * Type to represent an association end.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class AssociationEnd
{
    /**
     * 
     * association end type.
     * @var string
     */
    public $type;
    /**
     * 
     * association end multiplicity.
     * @var string
     */
    public $multiplicity;
    /**
     * 
     * association end role.
     * @var string
     */
    public $role;
    /**
     * Constructor to Initialize Objects and array.
     */
    function __construct()
    {
    }
}
?>