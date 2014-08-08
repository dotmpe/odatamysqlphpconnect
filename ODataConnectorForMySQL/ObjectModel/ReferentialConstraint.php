<?php
/** 
 * Representation of an association Referential Constraint.
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
use ODataConnectorForMySQL\ObjectModel\Principal;
use ODataConnectorForMySQL\ObjectModel\Dependent;
/**
 * Type to represent an association Referential Constraint.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class ReferentialConstraint
{
    /**
     * 
     * association Referential Constraint Principal
     * @var Principal
     */
    public $principal;
    /**
     * 
     * association Referential Constraint Dependent
     * @var Dependent
     */
    public $dependent;
    /**
     * Constructor to Initialize Objects and array.
     */
    function __construct()
    {
        $this->principal = null;
        $this->dependent = null;
    }
}
?>