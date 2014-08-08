<?php

 /**
 * Implementation of IServiceProvider.
 *
 * PHP version 5.3
 *
 * @category  Service
 * @package   Service_NorthWind
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   SVN: 1.0
 * @link      http://odataphpproducer.codeplex.com
 * 
 */
use ODataProducer\Configuration\EntitySetRights;
require_once 'ODataProducer/IDataService.php';
require_once 'ODataProducer/IRequestHandler.php';
require_once 'ODataProducer/DataService.php';
require_once 'ODataProducer/IServiceProvider.php';
use ODataProducer\Configuration\DataServiceProtocolVersion;
use ODataProducer\Configuration\DataServiceConfiguration;
use ODataProducer\IServiceProvider;
use ODataProducer\DataService;

require_once 'NorthWindMetadata.php';
require_once 'NorthWindQueryProvider.php';
require_once 'NorthWindDSExpressionProvider.php';

/**
 * NorthWindDataService that implements IServiceProvider.
 * 
 * @category  Service
 * @package   Service_NorthWind
 * @author    Yash K Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      http://odataphpproducer.codeplex.com
 */
class NorthWindDataService extends DataService implements IServiceProvider
{
    private $_NorthWindMetadata = null;
    private $_NorthWindQueryProvider = null;
    private $_NorthWindExpressionProvider = null;
    
    /**
     * This method is called only once to initialize service-wide policies
     * 
     * @param DataServiceConfiguration &$config Data service configuration object
     * 
     * @return void
     */
    public function initializeService(DataServiceConfiguration &$config)
    {   
        $config->setEntitySetPageSize('*', 10);
        $config->setEntitySetAccessRule('*', EntitySetRights::ALL);
        $config->setAcceptCountRequests(true);
        $config->setAcceptProjectionRequests(true);
        $config->setMaxDataServiceVersion(DataServiceProtocolVersion::V3);
    }

    /**
     * Get the service like IDataServiceMetadataProvider, IDataServiceQueryProvider,
     * IDataServiceStreamProvider
     * 
     * @param String $serviceType Type of service IDataServiceMetadataProvider, 
     *                            IDataServiceQueryProvider,
     *                            IDataServiceStreamProvider
     * 
     * @see library/ODataProducer/ODataProducer.IServiceProvider::getService()
     * @return object
     */
    public function getService($serviceType)
    {
        
        if (($serviceType === 'IDataServiceMetadataProvider') 
            || ($serviceType === 'IDataServiceQueryProvider2') 
            || ($serviceType === 'IDataServiceStreamProvider')
        ) {
            if (is_null($this->_NorthWindExpressionProvider)) {
                $this->_NorthWindExpressionProvider = new NorthWindDSExpressionProvider();
            }
        }
        
        if ($serviceType === 'IDataServiceMetadataProvider') {
            if (is_null($this->_NorthWindMetadata)) {
                $this->_NorthWindMetadata = CreateNorthWindMetadata::create();
            }
            return $this->_NorthWindMetadata;
        } else if ($serviceType === 'IDataServiceQueryProvider') {
            if (is_null($this->_NorthWindQueryProvider)) {
                $this->_NorthWindQueryProvider = new NorthWindQueryProvider();
            }
            return $this->_NorthWindQueryProvider;
        } else if ($serviceType === 'IDataServiceStreamProvider') {
            return new NorthWindStreamProvider();
        }
        return null;
    }

    /**
     * This method will be called to verify that DSExpressionProvider is 
     * implemented by the end-developer or not
     * 
     * @return object
     */
    public function &getExpressionProvider()
    {
        return $this->_NorthWindExpressionProvider;
    }
}

?>
