<?php
/** 
 * Representation of an association.
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
use ODataConnectorForMySQL\ObjectModel\AssociationEnd;
use ODataConnectorForMySQL\ObjectModel\ReferentialConstraint;
/**
 * Type to represent an association.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class Association
{
    /**
     * 
     * association name
     * @var string
     */
    public $name;
    /**
     * 
     * association end1
     * @var AssociationEnd
     */
    public $end1;
    /**
     * 
     * association end2
     * @var AssociationEnd
     */
    public $end2;
    /**
     * 
     * association Referential Constraint 
     * @var ReferentialConstraint
     */
    public $referentialConstraint;
    /**
     * Association is viewable or not
     * @var boolean
     */
    public $viewable;
    /**
     * Constructor to Initialize Objects and array.
     */
    function __construct()
    {
        $this->end1 = null;
        $this->end2 = null;
        $this->referentialConstraint = null;
        $this->viewable = true;
    }
}
?>