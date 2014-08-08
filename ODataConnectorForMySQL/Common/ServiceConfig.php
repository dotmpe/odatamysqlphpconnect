<?php
/**
 * Defines the ServiceConfig class
 * 
 * PHP version 5.3
 * 
 * @category  ODataConnectorForMySQLException
 * @package   ODataConnectorForMySQLException_Common
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com> 
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   SVN: 1.0
 * @link      
 * 
 */
namespace ODataConnectorForMySQL\Common;
use ODataConnectorForMySQL\Common\ODataConnectorForMySQLException;
/**
 * Helper class to read and velidate the service config file
 * 
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_Common
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com> 
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */
class ServiceConfig
{
    /**
     * Read and validates the configuration for the given service.
     * 
     * @param string $configFile config filename for all the services
     * 
     * @return void
     * 
     * @throws MySQLProducerException If configuration file 
     * does not exists or malformed.
     */
    public static function validateAndGetsServiceInfo(
        $configFile = '../service.config.xml'
    ) {
        $xml = simplexml_load_file(
            dirname(__FILE__)."/".$configFile, 
            null, 
            LIBXML_NOCDATA
        );
        if (!$xml) {
            die('service.config file is not in proper XML format');
        }

        if (count($xml->children()) != 1) {
            die("Config file has more than one root entries");
        }

        $pathResult = $xml->xpath("/configuration/rules");
        if (empty($pathResult)) {
             die("No rules info found in service config");
        }
                
        $pathResult = $xml->xpath("/configuration/rules/viewManyToManyRelationship");
        if (empty($pathResult)) {
            die("The mendatory configuration info 'viewManyToManyRelationship' is"
                ." missing in the config file");
        } else if ((strtolower(strval($pathResult[0]))) != "true" 
            and (strtolower(strval($pathResult[0]))) != "false"
        ) {
            die("Value: ".strval($pathResult[0])." is not valid for "
                ."'viewManyToManyRelationship' option");
        } else {
            $serviceInfo['viewManyToManyRelationship'] = strval($pathResult[0]);
        }
        unset($pathResult);
        $serviceInfo['followPularizeSingularizeRule'] = "true";
        $pathResult = $xml->xpath(
            "/configuration/rules/queryProviderVersion"
        );
        if (empty($pathResult)) {
            die("The mendatory configuration info 'queryProviderVersion' is missing"
                ." in the config file");
        } else if (strval($pathResult[0]) != "1" and strval($pathResult[0]) != "2") {
            die("Value: ".strval($pathResult[0])." is not valid for "
                ."'queryProviderVersion' option");
        } else {
            $serviceInfo['queryProviderVersion'] = strval($pathResult[0]);
        }
        unset($pathResult);
        $pathResult = $xml->xpath(
            "/configuration/rules/acceptCountRequest"
        );
        if (!empty($pathResult) and strtolower(strval($pathResult[0])) != "true" 
            and strtolower(strval($pathResult[0])) != "false" 
            and strtolower(strval($pathResult[0])) != "null"
        ) {
            die("Value: ".strval($pathResult[0])." is not valid for "
                ."'acceptCountRequest' option");
        }
        unset($pathResult);
        $pathResult = $xml->xpath(
            "/configuration/rules/acceptProjectionRequest"
        );
        if (!empty($pathResult) and  strtolower(strval($pathResult[0])) != "true" 
            and strtolower(strval($pathResult[0])) != "false" 
            and strtolower(strval($pathResult[0])) != "null"
        ) {
            die("Value: ".strval($pathResult[0])." is not valid for" 
                ."'acceptProjectionRequest' option");
        }
        unset($pathResult);
        $pathResult = $xml->xpath(
            "/configuration/rules/maxDataServiceVersion"
        );
        if ( !empty($pathResult) and strval($pathResult[0]) != "V1" 
            and strval($pathResult[0]) != "V2" and strval($pathResult[0]) != "V3"
        ) {
            die("Value: ".strval($pathResult[0])." is not valid for" 
                ."'maxDataServiceVersion' option");
        }
        unset($pathResult);
        return $serviceInfo;
    }
}