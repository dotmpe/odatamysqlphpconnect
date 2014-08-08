<?php
/**
 * Contains parameters for database connections.
 *
 * PHP version 5.3
 *
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_EDMXGenerator
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   SVN: 1.0
 * @link
 *
 */
namespace ODataConnectorForMySQL\EDMXGenerator;
use ODataConnectorForMySQL\Common\ODataConnectorForMySQLConstants;
use ODataConnectorForMySQL\Common\ServiceConfig;
use ODataConnectorForMySQL\Common\ODataConnectorForMySQLException;
require_once 'ODataConnectorForMySQL/Common/Inflector.php';
/**
 * Contains parameters for database connections.
 *
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_EDMXGenerator
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link
 */
class ConnectionParams
{
    /**
     * Writer to which output is to be sent
     *
     * @var XMLWriter
     */
    public $xmlWriter;
    /**
     * Connection parameters of the database.
     *
     * @var array
     */
    public $connectionParams;

    /**
     * Construct a new instance of ConnectionParams.
     *
     * @param array &$connectionParams Connection parameters for database.
     */
    public function __construct(&$connectionParams)
    {
        $this->connectionParams = $connectionParams;
        $this->xmlWriter = new \XMLWriter();
        $this->xmlWriter->openMemory();
        $this->xmlWriter->startDocument('1.0', 'UTF-8', 'yes');
        $this->xmlWriter->setIndent(4);
    }

    /**
     * Start Generating EDMX file.
     *
     * @return EDMX xml object.
     */
    public function saveConnectionParams()
    {
        $this->xmlWriter->startElement(
            ODataConnectorForMySQLConstants::CONNECTION_PARAMS
        );
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::HOST);
        $this->xmlWriter->text($this->connectionParams['host']);
        $this->xmlWriter->endElement();
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::PORT);
        if (isset($this->connectionParams['port'])) {
            $this->xmlWriter->text($this->connectionParams['port']);
        }
        $this->xmlWriter->endElement();
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::DATABASE);
        $this->xmlWriter->text($this->connectionParams['dbname']);
        $this->xmlWriter->endElement();
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::USER);
        $this->xmlWriter->text($this->connectionParams['user']);
        $this->xmlWriter->endElement();
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::PASSWORD);
        $this->xmlWriter->text($this->connectionParams['password']);
        $this->xmlWriter->endElement();
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::SERVICE);
        $this->xmlWriter->text($this->connectionParams['serviceName']);
        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();
        return $this->xmlWriter->outputMemory(true);
    }
}
?>