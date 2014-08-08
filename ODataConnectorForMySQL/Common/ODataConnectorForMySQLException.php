<?php
/** 
 * Exception class for OData MySQL Connector
 * 
 * PHP version 5.3
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_Common
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   SVN: 1.0
 * @link      
 * 
 */
namespace ODataConnectorForMySQL\Common;
/**
 * Class for ODataConnectorForMySQL Exception
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_Common
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class ODataConnectorForMySQLException extends \Exception
{
    /**
     * The error code
     * 
     * @var int
     */
    private $_errorCode;

    /**
     * The HTTP status code
     * 
     * @var int
     */
    private $_statusCode;
   
    /**
     * Create new instance of ODataConnectorForMySQLException
     * 
     * @param string $message    The error message
     * @param string $statusCode The status code
     * @param string $errorCode  The error code
     * 
     * @return nothing
     */
    public function __construct($message, $statusCode = null, $errorCode = null)
    {
        $this->_errorCode = $errorCode;
        $this->_statusCode = $statusCode;
        parent::__construct($message, $errorCode);
    }

    /**
     * Get the status code
     * 
     * @return int
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * Creates and throws an instance of ODataConnectorForMySQLException 
     * representing HTTP bad request error
     * 
     * @param string $message The error message
     * 
     * @throws ODataConnectorForMySQLException
     * @return nothing
     */
    public static function createBadRequestError($message)
    {
        throw new ODataConnectorForMySQLException($message, 400);
    }

    /**
     * Creates and throws an instance of ODataConnectorForMySQLException 
     * representing syntax error in the query
     * 
     * @param string $message The error message
     * 
     * @throws ODataConnectorForMySQLException
     * @return nothing
     */    
    public static function createSyntaxError($message)
    {
        self::createBadRequestError($message);
    }

    /**
     * Creates and throws an instance of ODataConnectorForMySQLException when a 
     * resouce not found in the data source
     * 
     * @param string $message The error message
     * 
     * @throws ODataConnectorForMySQLException
     * @return nothing
     */
    public static function resourceNotFoundError($message)
    {
        throw new ODataConnectorForMySQLException($message, 404);
    }

    /**
     * Creates and throws an instance of ODataConnectorForMySQLException when some
     * internal error happens in the library
     * 
     * @param string $message The detailed internal error message
     * 
     * @throws ODataConnectorForMySQLException
     * @return nothing
     */
    public static function createInternalServerError($message)
    {
        throw new ODataConnectorForMySQLException($message, 500);
    }

    /**
     * Creates a new exception when requestor ask for a service facility
     * which is not implemented by this library.
     * 
     * @param string $message Error message for this exception
     * 
     * @throws ODataConnectorForMySQLException
     * @return nothing
     */
    public static function createNotImplementedError($message)
    {
        throw new ODataConnectorForMySQLException($message, 501);
    }
}
?>