<?php
/** 
 * Representation of an association set.
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
use ODataConnectorForMySQL\ObjectModel\AssociationSetEnd;
/**
 * Type to represent an association set.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class AssociationSet
{
    /**
     * 
     * association set name
     * @var string
     */
    public $name;
    /**
     * 
     * association set association name
     * @var string
     */
    public $association;
    /**
     * 
     * association set end1
     * @var AssociationSetEnd
     */
    public $end1;
    /**
     * 
     * association set end2
     * @var AssociationSetEnd
     */
    public $end2;
    /**
     * AssociationSet is viewable or not
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
        $this->viewable = true;
    }
}
?>