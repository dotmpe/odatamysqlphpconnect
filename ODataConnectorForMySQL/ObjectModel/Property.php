<?php
/** 
 * Representation of an entity type property.
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
 * Type to represent an entity type property.
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_ObjectModel
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class Property
{
    /**
     * 
     * Entity type property name
     * @var string
     */
    public $name;
    /**
     * 
     * Entity type property type
     * @var string
     */
    public $type;
    /**
     * 
     * Entity type property nullable
     * @var boolean
     */
    public $nullable;
    /**
     * 
     * Entity type property default value
     * @var string
     */
    public $defaultValue;
    /**
     * 
     * Entity type property max length
     * @var int
     */
    public $maxLength;
    /**
     * 
     * Entity type property fixed length
     * @var boolean
     */
    public $fixedLength;
    /**
     * 
     * Entity type property precision
     * @var int
     */
    public $precision;
    /**
     * 
     * Entity type property scale
     * @var int
     */
    public $scale;
    /**
     * 
     * Entity type property unicode
     * @var boolean
     */
    public $unicode;
    /**
     * 
     * Entity type property collation
     * @var string
     */
    public $collation;
    /**
     * 
     * Entity type property concurrency mode
     * @var boolean
     */
    public $concurrencyMode;
    /**
     * Constructor to Initialize Objects and array.
     */
    function __construct()
    {
    }
}
?>