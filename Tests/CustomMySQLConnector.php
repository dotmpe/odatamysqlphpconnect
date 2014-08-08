<?php
/**
 * This will generate EDMX file from database connection provider.
 * And from EDMX file to ServiceProvider, MetadataProvider and QueryProvider.
 *
 * PHP version 5.3
 *
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com >
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   SVN: 1.0
 * @link      http://odataphpproducer.codeplex.com
 *
 */
//use Doctrine\Common\ClassLoader;
//require 'Doctrine/Common/ClassLoader.php';
//use ODataConnectorForMySQL\Common\ClassAutoLoader;
//require 'ODataConnectorForMySQL/Common/ClassAutoLoader.php';
use Doctrine\DBAL\Configuration;
use ODataConnectorForMySQL\EDMXGenerator\IEDMXGenerator;
use ODataConnectorForMySQL\EDMXGenerator\EDMXGenerator;
use ODataConnectorForMySQL\EDMXGenerator\ConnectionParams;
use ODataConnectorForMySQL\Common\ServiceConfig;
use ODataConnectorForMySQL\Common\ODataConnectorForMySQLConstants;
//$classLoader = new ClassLoader('Doctrine', '');
//$classLoader->register();
//ODataConnectorForMySQL\Common\ClassAutoLoader::register();


/**
 * Connector for MySQL.
 *
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version   Release: 1.0
 * @link
 */
class MySQLConnector
{
	protected $_validOptions = array('/db', '/h', '/u', '/pw', '/p', '/srvc');
	protected $_stageOneMustOptions = array('/db', '/h', '/u', '/pw', '/srvc');
	protected $_stageTwoMustOptions = array('/srvc');
	protected $_cmdArgs;
	public  $_options;
	public static $_messages = array(
                            'Cannot_Repeat_Option' => 'Option cannot be repeated: ',
                            'Invalid_Option_Format' => 'Make sure the format of all commandline options are \'parameter=value\'',
	);
	/**
	 * Construct PHPSvUtil instance.
	 *
	 * @param array $options
	 */
	public function __construct($options)
	{
		unset($options[0]);
		$this->_cmdArgs = $options;
	}

	/**
	 *
	 * @return array
	 * Retruns options (command line and additional options)
	 */
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	 * To display message, usage for stage1 and exit
	 *
	 * @param string $message
	 */
	public function showUsage($message, $stage=0)
	{
		if($stage)
		{
			if($stage==1) {
				if(!is_null($message)) {
					print("\n\n$message");
				}
				print("\nStage-1 : Tool generates the EDMX file in this stage.");
				print "\nUsage:";
				print "\nphp MySQLConnector.php /db=<dataBase name> /u=<user name> /pw=<password> /h=<host name> /srvc=<service name> [/p=<port>]\n";
				print "\n  /db\t= <Databse name of the MySQL>";
				print "\n  /u\t= <MySQL User name>";
				print "\n  /pw\t= <Password of the MySQL user>";
				print "\n  /h\t= <Host name of the MySQL Server>";
				print "\n  /srvc\t= <OData service-name>";
				print "\n  /p\t= [Optional]<Port number of the MySQL Server >";
			} elseif ($stage==2) {
				if(!is_null($message)) {
					print("\n\n$message");
				}
				print("\nStage-2 : Tool generates all the required providers in this stage.");
				print "\nUsage:";
				print "\nphp MySQLConnector.php  /srvc=<service name>\n";
				print "\n  /srvc\t= <OData service-name>";
			}
		} else {
			if(!is_null($message)) {
				print("\n\n$message");
			}
			print("\nStage-1 : Tool generates the EDMX file in this stage.");
			print "\nUsage:";
			print "\nphp MySQLConnector.php /db=<dataBase name> /u=<user name> /pw=<password> /h=<host name> /srvc=<service name> [/p=<port>]\n";
			print "\n  /db\t= <Databse name of the MySQL>";
			print "\n  /u\t= <MySQL User name>";
			print "\n  /pw\t= <Password of the MySQL user>";
			print "\n  /h\t= <Host name of the MySQL Server>";
			print "\n  /srvc\t = <OData service-name>";
			print "\n  /p\t= [Optional]<Port number of the MySQL Server >";
			print("\n\n\nStage-2 : Tool generates all the required providers in this stage.");
			print "\nUsage:";
			print "\nphp MySQLConnector.php  /srvc=<service name>\n";
			print "\n  /srvc\t= <OData service-name>";
		}
		exit;
	}

	/**
	 * Validate the commandline arguments and return the options and additional
	 * details as array of key value pair.
	 *
	 * @return array
	 */
	public function _validateAndBuidOptions()
	{
		$this->_options = array();
		if(count($this->_cmdArgs) == 0)
		{
			$this->showUsage("No command line arguments found");
		}
		foreach ($this->_cmdArgs as $option) {
			$pieces = explode('=', $option, 2);
			/*if(empty($pieces[0]) || empty($pieces[1]))
			 {
			 $this->showUsage(self::$_messages['Invalid_Option_Format']);
			 }*/
			if(!in_array($pieces[0], $this->_validOptions))
			{
				$this->showUsage("The option '$pieces[0]', is not valid");
			}
			if(array_key_exists($pieces[0], $this->_options))
			{
				$this->showUsage(self::$_messages['Cannot_Repeat_Option'] . $pieces[0]);
			}
			$stageOptions[] = $pieces[0];
			switch ($pieces[0]) {
				case '/db':
					$this->_options['dbname'] = $pieces[1];
					break;
				case '/u':
					$this->_options['user'] = $pieces[1];
					break;
				case '/pw':
					$this->_options['password'] = $pieces[1];
					break;
				case '/h':
					$this->_options['host'] = $pieces[1];
					break;
				case '/p':
					$this->_options['port'] = $pieces[1];
					break;
				case '/srvc':
					$this->_options['serviceName'] = $pieces[1];
					break;

			}
		}
		if (count(array_diff($this->_stageOneMustOptions, $stageOptions)) == 0) {
			return 1;
		} else if (count(array_diff($this->_stageTwoMustOptions, $stageOptions)) == 0) {
			return 2;
		} else {
			return 0;
		}
	}


	/**
	 * Read the connection params from XML file created in stage-1 for current service
	 * @param String $serviceOutDir Dir of output-files for the current service
	 *
	 * @return void
	 */
	public function readConfigParamsFromFile($serviceOutDir)
	{
		$inFile = $serviceOutDir."/".$this->_options['serviceName']."ConnectionParams.xml";
		//print "File:".$inFile;
		$xml = simplexml_load_file($inFile, null, LIBXML_NOCDATA);
		if (!$xml) {
			//Connection.xml file is not in proper XML format
			return false;
		}
		$host     = $xml->xpath("/ConnectionParams/Host");
		if(isset($host)) {
			$this->_options['host'] = strval($host[0]);
		}
		$port     = $xml->xpath("/ConnectionParams/Port");
		if(isset($port)) {
			$this->_options['port'] = strval($port[0]);
		}
		$database    = $xml->xpath("/ConnectionParams/Database");
		if(isset($database)) {
			$this->_options['dbname'] = strval($database[0]);
		}
		$user        = $xml->xpath("/ConnectionParams/User");
		if(isset($user)) {
			$this->_options['user'] = strval($user[0]);
		}
		$password    = $xml->xpath("/ConnectionParams/Password");
		if(isset($password)) {
			$this->_options['password'] = strval($password[0]);
		}
		return;
	}

	/**
	 * Genearte the EDMX
	 * @param1 String $serviceOutDir Path of output-files for current service
	 *
	 * @return void
	 */
	public function generateEDMX($serviceOutDir, $line = '')
	{
		ob_start();
		$eDMXGenerator = new EDMXGenerator($this->_options);
		ob_end_clean();
		$xml = $eDMXGenerator->generateEDMX();
		$edmxPath = $serviceOutDir."/".$this->_options['serviceName']."EDMX.xml";
		$fp = fopen($edmxPath, "w");
		fwrite($fp, $xml);
		fclose($fp);
		unset($xml);
		$connectionParams = new ConnectionParams($this->_options);
		$xml = $connectionParams->saveConnectionParams();

		$connectionParamFile = $serviceOutDir."/".$this->_options['serviceName']."ConnectionParams.xml";
		//print "\nConnectionParamFile:".$connectionParamFile;
		//exit;
		$fp = fopen($connectionParamFile, "w");
		fwrite($fp,$xml);
		fclose($fp);

		if(!$line) {
			echo "\nEDMX file is successfully generated in the output folder.\n";
			echo "\nDo you want to modify the EDMX file-$edmxPath(y/n):";
			$handle = fopen ("php://stdin","r");
			$line = fgets($handle);
		}
		if (strtolower(trim($line)) == 'y') {
			$this->showUsage("After modifying the EDMX,execute following command for further processing",2);
		} elseif (strtolower(trim($line)) != 'n') {
			$this->showUsage("Invalid input ...",1);
		} else {
			return;
		}
	}

	/**
	 * Genearte the providers
	 * @param1 String $serviceOutDir Path of output-files for current service
	 * @param2 String $serviceXslDir Path of xsl-files for generating providers
	 * @param3 int $stage Identifier to judge ehether we need to take connection perameters from the .xml file or we need to pick values from the command line arguments
	 *
	 * @return void
	 */
	public function generateProviders($serviceOutDir, $serviceXslDir, $configFile, $stage)
	{
		try {
			$serviceInfo = ServiceConfig::validateAndGetsServiceInfo();
			$edmxPath = $serviceOutDir."/".$this->_options['serviceName']."EDMX.xml";
			$this->generateMetadataProvider(&$serviceInfo, $serviceOutDir, $serviceXslDir, $edmxPath);
			$this->generateQueryProvider(&$serviceInfo, $serviceOutDir, $serviceXslDir, $edmxPath, $stage);
			$this->generateServiceProvider(&$serviceInfo, $serviceOutDir, $serviceXslDir, $configFile);
			$this->generateExpressionProvider(&$serviceInfo, $serviceOutDir, $serviceXslDir, $configFile);
			$this->generateServiceConfig($serviceOutDir, $this->_options['serviceName']);
			$connectionParamFile = $serviceOutDir."/".$this->_options['serviceName']."ConnectionParams.xml";
			unlink($connectionParamFile);
		}
		catch(Exception $e)
		{
			//Handle excerption over here
		}
	}

	/**
	 * Genearte metadata providers
	 * @param1 Object $serviceInfo ServiceInfo object
	 * @param2 String $serviceOutDir Path of output-files for current service
	 * @param3 String $serviceXslDir Path of xsl-files for generating providers
	 * @param4 String $edmxPath Path of EDMX file
	 * @return void
	 */
	public function generateMetadataProvider($serviceInfo, $serviceOutDir, $serviceXslDir, $edmxPath)
	{
		//Metadata Provider Generation.
		$xsl_path = $serviceXslDir."/EDMXToMetadataProvider.xsl";
		$xslDoc = new DOMDocument();
		$xslDoc->load($xsl_path);
		$proc = new XSLTProcessor();
		$proc->importStylesheet($xslDoc);

		$proc->setParameter('', 'QueryProviderVersion',
		$serviceInfo['queryProviderVersion']);

		$metadataPath = $serviceOutDir."/".$this->_options['serviceName']."Metadata.php";
		$metadataDoc = new DOMDocument();
		$metadataDoc->load($edmxPath);
		$proc->transformToURI($metadataDoc, $metadataPath);
		unset($xslDoc);
		unset($proc);
		unset($metadataDoc);

		system("PHP -l $metadataPath  1> " . $serviceOutDir . "/msg.tmp 2>&1", $temp);
		unlink($serviceOutDir . "/msg.tmp");
	}

	/**
	 * Genearte query providers
	 *
	 * @param1 Object $serviceInfo ServiceInfo Object
	 * @param2 String $serviceOutDir Path of output-files for current service
	 * @param3 String $serviceXslDir Path of xsl-files for generating providers
	 * @param4 String $edmxPath Path of EDMX file
	 * @param5 Int $stage Stage-identifier
	 *
	 * @return void
	 */
	public function generateQueryProvider($serviceInfo, $serviceOutDir, $serviceXslDir, $edmxFile, $stage)
	{
		//Query Provider Generation.
		if ($serviceInfo['queryProviderVersion'] == 1) {
			$xsl_path = $serviceXslDir."/EDMXToQueryProvider.xsl";
		} else {
			$xsl_path = $serviceXslDir."/EDMXToQueryProvider2.xsl";
		}
		if($stage === 2) {
			$this->readConfigParamsFromFile($serviceOutDir);
		}
		$xslDoc = new DOMDocument();
		$xslDoc->load($xsl_path);
		$proc = new XSLTProcessor();
		$proc->importStylesheet($xslDoc);

		$proc->setParameter('', 'IDSQP_Version',
		$serviceInfo['queryProviderVersion']);

		$proc->setParameter('','ServiceName', $this->_options['serviceName']);
		$proc->setParameter('','DBName', $this->_options['dbname']);
		$proc->setParameter('','DBHost', $this->_options['host']);
		$proc->setParameter('','DBUser', $this->_options['user']);
		$proc->setParameter('','DBPass', $this->_options['password']);
		$proc->setParameter('','DBPort', $this->_options['port']);
		$proc->setParameter('','followPularizeSingularizeRule', $serviceInfo['followPularizeSingularizeRule']);
		$queryProviderPath = $serviceOutDir."/".$this->_options['serviceName']."QueryProvider.php";
		$metadataDoc = new DOMDocument();
		$metadataDoc->load($edmxFile);
		$proc->transformToURI($metadataDoc, $queryProviderPath);
		unset($xslDoc);
		unset($proc);
		unset($metadataDoc);
		system("PHP -l $queryProviderPath  1> " . $serviceOutDir . "/msg.tmp 2>&1", $temp);
		unlink($serviceOutDir . "/msg.tmp");
	}

	/**
	 * Genearte service providers
	 *
	 * @param1 Object $serviceInfo ServiceInfo Object
	 * @param2 String $serviceOutDir Path of output-files for current service
	 * @param3 String $serviceXslDir Path of xsl-files for generating providers
	 * @param4 String $configFile config file name
	 *
	 * @return void
	 */
	public function generateServiceProvider($serviceInfo, $serviceOutDir, $serviceXslDir, $configFile)
	{
		//DataService Provider Generation.
		$xsl_path = $serviceXslDir."/EDMXToDataServiceProvider.xsl";
		$xslDoc = new DOMDocument();
		$xslDoc->load($xsl_path);
		$proc = new XSLTProcessor();
		$proc->importStylesheet($xslDoc);

		$proc->setParameter('', 'serviceName', $this->_options['serviceName']);
		$metadataDoc = new DOMDocument();
		$serviceProviderPath = $serviceOutDir."/".$this->_options['serviceName']."DataService.php";
		$metadataDoc->load($configFile);
		$proc->transformToURI($metadataDoc, $serviceProviderPath);
		unset($xslDoc);
		unset($proc);
		unset($metadataDoc);
		system("PHP -l $serviceProviderPath 1> " . $serviceOutDir . "/msg.tmp 2>&1", $temp);
		unlink($serviceOutDir . "/msg.tmp");
	}

	/**
	 * Genearte expression providers
	 * @param1 Object $serviceInfo ServiceInfo Object
	 * @param2 String $serviceOutDir Path of output-files for current service
	 * @param3 String $serviceXslDir Path of xsl-files for generating providers
	 * @param4 String $configFile config file name
	 *
	 * @return void
	 */
	public function generateExpressionProvider($serviceInfo, $serviceOutDir, $serviceXslDir, $configFile)
	{
		//DSExpression Provider Generation.
		if ($serviceInfo['queryProviderVersion'] == 2) {
			$xsl_path = $serviceXslDir."/EDMXToDSExpressionProvider.xsl";
			$xslDoc = new DOMDocument();
			$xslDoc->load($xsl_path);
			$proc = new XSLTProcessor();
			$proc->importStylesheet($xslDoc);

			$proc->setParameter('', 'serviceName', $this->_options['serviceName']);
			$metadataDoc = new DOMDocument();
			$expressionProviderPath = $serviceOutDir."/".$this->_options['serviceName']."DSExpressionProvider.php";
			$metadataDoc->load($configFile);
			$proc->transformToURI($metadataDoc, $expressionProviderPath);
			unset($xslDoc);
			unset($proc);
			unset($metadataDoc);
			system("PHP -l $expressionProviderPath  1> " . $serviceOutDir . "/msg.tmp 2>&1", $temp);
			unlink($serviceOutDir . "/msg.tmp");
		}
	}
	
    /**
     * generates sample service.config file. from which service tag can be copied 
     * in the original service.config file of library.
     * 
     * @param string $serviceOutDir path of the output directory.
     * @param string $serviceName   name of the service.
     * 
     * @return void
     */
    public function generateServiceConfig($serviceOutDir, $serviceName)
    {
        $this->xmlWriter = new \XMLWriter();
        $this->xmlWriter->openMemory();
        $this->xmlWriter->startDocument('1.0');
        $this->xmlWriter->setIndent(4);
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::CONFIGURATION);
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::SERVICES);
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::SERVICE);
        $this->xmlWriter->writeAttribute(ODataConnectorForMySQLConstants::NAME, $serviceName.".svc");
        $this->xmlWriter->endAttribute();
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::PATH);
        $this->xmlWriter->text("Services\\".$serviceName."\\".$serviceName."DataService.php");
        $this->xmlWriter->endElement();
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::CLASSNAME);
        $this->xmlWriter->text($serviceName."DataService");
        $this->xmlWriter->endElement();
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::BASEURL);
        $this->xmlWriter->text("/".$serviceName.".svc");
        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();
        $xml = $this->xmlWriter->outputMemory(true);
        $fp = fopen($serviceOutDir."/service.config.xml", "w");
        fwrite($fp, $xml);
        fclose($fp);
    }
}
?>