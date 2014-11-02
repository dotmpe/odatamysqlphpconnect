<?php
/**
 * This will generate EDMX file from database connection provider.
 * And from EDMX file to ServiceProvider, MetadataProvider and QueryProvider
 * also DSExpression Provider if Query Provider version is 2.
 *
 * PHP version 5.3
 *
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @author    Neelesh Vijaivargia <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   SVN: 1.0
 * @link      
 *
 */

require 'vendor/autoload.php';

require_once 'ODataConnectorForMySQL/Common/ClassAutoLoader.php';
use Doctrine\DBAL\Configuration;
use ODataConnectorForMySQL\EDMXGenerator\IEDMXGenerator;
use ODataConnectorForMySQL\EDMXGenerator\EDMXGenerator;
use ODataConnectorForMySQL\EDMXGenerator\ConnectionParams;
use ODataConnectorForMySQL\Common\ServiceConfig;
use ODataConnectorForMySQL\Common\ODataConnectorForMySQLConstants;
use ODataConnectorForMySQL\Common\ODataConnectorForMySQLException;
ODataConnectorForMySQL\Common\ClassAutoLoader::register();
//set_include_path('/var/www/doctrine-orm');
define('OUT_DIR', '/ODataConnectorForMySQL/OutputFiles');
define('XSL_DIR', '/ODataConnectorForMySQL/ProvidersGenerator');
define('CONFIG_FILE', '/ODataConnectorForMySQL/service.config.xml');

try
{
    $util = new MySQLConnector($argv);
    $options = $util->getOptions();
    $stage = $util->validateAndBuidOptions();
    $currentDir = str_replace("\\", "/", dirname(__FILE__));
    $serviceOutDir = $currentDir.OUT_DIR."/".$util->options['serviceName'];
    $serviceXslDir = $currentDir.XSL_DIR;
    $configFileName = $currentDir.CONFIG_FILE;

    if ($stage === 1 or $stage === 2) {
        $util->options['driver'] = 'pdo_mysql';
        if (!is_dir($serviceOutDir)) {
            mkdir($serviceOutDir, 0777);
        }
        if ($stage === 1) {
            $util->generateEDMX($serviceOutDir);
        }
        $util->generateProviders(
            $serviceOutDir, $serviceXslDir, $configFileName, $stage
        );
    } else {
        $util->showUsage(
            'Some arguments are missing, please enter all required parameters.'
        );
    }
}
catch (Exception $e)
{
    $util->showUsage($e->getMessage(), $stage);
}



/**
 * Connector for MySQL. Generates EDMX files and Service Providers.
 *
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @author    Neelesh Vijaivargia <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link
 */
class MySQLConnector
{
    protected $validOptions = array('/db', '/h', '/u', '/pw', '/p', '/srvc');
    protected $stageOneMustOptions = array('/db', '/h', '/u', '/pw', '/srvc');
    protected $stageTwoMustOptions = array('/srvc');
    protected $cmdArgs;
    public  $options;
    public static $messages = array(
        'Cannot_Repeat_Option' => 'Option cannot be repeated: ',
        'Invalid_Option_Format' => 
        'Make sure the format of all commandline options are \'parameter=value\'',
    );
    /**
     * Construct MySQL Connector.
     *
     * @param array $options command line arguments.
     * 
     * @return void
     */
    public function __construct($options)
    {
        unset($options[0]);
        $this->cmdArgs = $options;

        //set error handler
        set_error_handler(
            array($this, "customError"), E_ALL & ~E_DEPRECATED & ~E_NOTICE
        );
    }

    /**
     * Retruns options (command line and additional options)
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * To display message, usage for stage1 and stage2 and exit
     *
     * @param string  $message Error message
     * @param integer $stage   Stage number 
     * 
     * @return void
     */
    public function showUsage($message, $stage = 0)
    {
        if ($stage) {
            if ($stage==1) {
                if (!is_null($message)) {
                    print("\n\n$message");
                }
                print("\nStage-1 : Tool generates the EDMX file in this stage.");
                print "\nUsage:";
                print "\nphp MySQLConnector.php /db=<dataBase name> /u=<user name> "
                    ."/pw=<password> /h=<host name> /srvc=<service name> [/p=<port>]"
                    ."\n";
                print "\n  /db\t= <Databse name of the MySQL>";
                print "\n  /u\t= <MySQL User name>";
                print "\n  /pw\t= <Password of the MySQL user>";
                print "\n  /h\t= <Host name of the MySQL Server>";
                print "\n  /srvc\t= <OData service-name>";
                print "\n  /p\t= [Optional]<Port number of the MySQL Server >";
            } else if ($stage==2) {
                if (!is_null($message)) {
                    print("\n\n$message");
                }
                print("\nStage-2 : "
                    ."Tool generates all the required providers in this stage.");
                print "\nUsage:";
                print "\nphp MySQLConnector.php  /srvc=<service name>\n";
                print "\n  /srvc\t= <OData service-name>";
            }
        } else {
            if (!is_null($message)) {
                print("\n\n$message");
            }
            print("\nStage-1 : Tool generates the EDMX file in this stage.");
            print "\nUsage:";
            print "\nphp MySQLConnector.php /db=<dataBase name> /u=<user name> "
                ."/pw=<password> /h=<host name> /srvc=<service name> [/p=<port>]\n";
            print "\n  /db\t= <Databse name of the MySQL>";
            print "\n  /u\t= <MySQL User name>";
            print "\n  /pw\t= <Password of the MySQL user>";
            print "\n  /h\t= <Host name of the MySQL Server>";
            print "\n  /srvc\t = <OData service-name>";
            print "\n  /p\t= [Optional]<Port number of the MySQL Server >";
            print("\n\n\nStage-2 : Tool generates all the required providers "
                ."in this stage.");
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
    public function validateAndBuidOptions()
    {
        $this->options = array();
        if (count($this->cmdArgs) == 0) {
            $this->showUsage("No command line arguments found");
        }
        foreach ($this->cmdArgs as $option) {
            $pieces = explode('=', $option, 2);
            /*if(empty($pieces[0]) || empty($pieces[1]))
             {
             $this->showUsage(self::$messages['Invalid_Option_Format']);
             }*/
            if (!in_array($pieces[0], $this->validOptions)) {
                $this->showUsage("The option '$pieces[0]', is not valid");
            }
            if (array_key_exists($pieces[0], $this->options)) {
                $this->showUsage(
                    self::$messages['Cannot_Repeat_Option'] . $pieces[0]
                );
            }
            $stageOptions[] = $pieces[0];
            switch ($pieces[0]) {
            case '/db':
                $this->options['dbname'] = $pieces[1];
                break;
            case '/u':
                $this->options['user'] = $pieces[1];
                break;
            case '/pw':
                $this->options['password'] = $pieces[1];
                break;
            case '/h':
                $this->options['host'] = $pieces[1];
                break;
            case '/p':
                $this->options['port'] = $pieces[1];
                break;
            case '/srvc':
                $this->options['serviceName'] = $pieces[1];
                break;
            }
        }
        if (count(array_diff($this->stageOneMustOptions, $stageOptions)) == 0) {
            return 1;
        } elseif (
            count(array_diff($stageOptions, $this->stageTwoMustOptions)) == 0
        ) {
            return 2;
        } else {
            return 0;
        }
    }


    /**
     * Read the connection params from XML file created in stage-1 for current 
     * service
     * 
     * @param String $serviceOutDir Dir of output-files for the current service
     *
     * @return void
     */
    public function readConfigParamsFromFile($serviceOutDir)
    {
        $inFile = $serviceOutDir."/".$this->options['serviceName']
            ."ConnectionParams.xml";
        if (file_exists($inFile)) {
            $xml = simplexml_load_file($inFile, null, LIBXML_NOCDATA);
        } else {
            trigger_error(
                "\n\nError: ".$this->options['serviceName']
                ."ConnectionParams.xml file not found in Output service Folder.\n"
            );
        }
        if (!$xml) {
            trigger_error(
                "\n\nError: ".$this->options['serviceName']
                ."ConnectionParams.xml file is not in proper format.\n"
            );
        }
        $host     = $xml->xpath("/ConnectionParams/Host");
        if (isset($host)) {
            $this->options['host'] = strval($host[0]);
        }
        $port     = $xml->xpath("/ConnectionParams/Port");
        if (isset($port)) {
            $this->options['port'] = strval($port[0]);
        }
        $database    = $xml->xpath("/ConnectionParams/Database");
        if (isset($database)) {
            $this->options['dbname'] = strval($database[0]);
        }
        $user        = $xml->xpath("/ConnectionParams/User");
        if (isset($user)) {
            $this->options['user'] = strval($user[0]);
        }
        $password    = $xml->xpath("/ConnectionParams/Password");
        if (isset($password)) {
            $this->options['password'] = strval($password[0]);
        }
        return;
    }

    /**
     * Handles errors.
     *
     * @param string $errno  error number code.
     * @param string $errstr error message.
     *
     * @return void
     */
    public function customError($errno, $errstr)
    {
        die("\n\nError: $errstr..!!!\n");
    }

    /**
     * Genearte the EDMX
     * 
     * @param String $serviceOutDir Path of output-files for current service
     *
     * @return void
     */
    public function generateEDMX($serviceOutDir)
    {
    	ob_start();
        $eDMXGenerator = new EDMXGenerator($this->options);
        ob_end_clean();
        $xml = $eDMXGenerator->generateEDMX();
        $edmxPath = $serviceOutDir."/".$this->options['serviceName']."EDMX.xml";
        $fp = fopen($edmxPath, "w");
        chmod($edmxPath, 0777);
        fwrite($fp, $xml);
        fclose($fp);
        unset($xml);
        $connectionParams = new ConnectionParams($this->options);
        $xml = $connectionParams->saveConnectionParams();

        $connectionParamFile = $serviceOutDir."/".$this->options['serviceName']
            ."ConnectionParams.xml";
        $fp = fopen($connectionParamFile, "w");
        chmod($connectionParamFile, 0777);
        fwrite($fp, $xml);
        fclose($fp);

        echo "\nEDMX file is successfully generated in the output folder.\n";
        echo "\nDo you want to modify the EDMX file-$edmxPath(y/n):";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (strtolower(trim($line)) == 'y') {
            $this->showUsage(
                "After modifying the EDMX,execute following command "
                ."for further processing", 2
            );
        } else if (strtolower(trim($line)) != 'n') {
            $this->showUsage("Invalid input ...", 1);
        } else {
            return;
        }
    }

    /**
     * Genearte the providers
     * 
     * @param String $serviceOutDir Path of output-files for current service
     * @param String $serviceXslDir Path of xsl-files for generating providers
     * @param string $configFile    path of service.config file
     * @param int    $stage         Identifier to judge ehether we need to take 
     *                              connection perameters from the .xml file or we 
     *                              need to pick values from the command line 
     *                              arguments
     *
     * @return void
     */
    public function generateProviders(
        $serviceOutDir, $serviceXslDir, $configFile, $stage
    ) {
        try {
            $serviceInfo = ServiceConfig::validateAndGetsServiceInfo();
            $edmxPath = $serviceOutDir."/".$this->options['serviceName']."EDMX.xml";
            $xdoc = new DomDocument;
            if (file_exists($edmxPath)) {
                if (!$xdoc->load($edmxPath)) {
                    die("Error while loading service EDMX xml file.");
                }
            } else {
                die(
                    "Erroe: XML file for metadata generation is not found."
                );
            }
            $xmlschema = "ODataConnectorForMySQL/EDMXmodel/EDMXModel.xsd";
            /*if (!$xdoc->schemaValidate($xmlschema)) {
                die("$edmxPath is invalid.\n");
            }*/
            $this->generateMetadataProvider(
                &$serviceInfo, $serviceOutDir, $serviceXslDir, $edmxPath
            );
            $this->generateQueryProvider(
                &$serviceInfo, $serviceOutDir, $serviceXslDir, $edmxPath, $stage
            );
            $this->generateServiceProvider(
                &$serviceInfo, $serviceOutDir, $serviceXslDir, $configFile
            );
            $this->generateExpressionProvider(
                &$serviceInfo, $serviceOutDir, $serviceXslDir, $configFile
            );
            $this->generateServiceConfig(
                $serviceOutDir, $this->options['serviceName']
            );
            $connectionParamFile = $serviceOutDir."/".$this->options['serviceName']
                ."ConnectionParams.xml";
            unlink($connectionParamFile);
        }
        catch(\Exception $e)
        {
            die("\n\nError: $e->getMessage()..!!!\n");
        }
    }

    /**
     * Genearte metadata providers
     * 
     * @param Object $serviceInfo   ServiceInfo object
     * @param String $serviceOutDir Path of output-files for current service
     * @param String $serviceXslDir Path of xsl-files for generating providers
     * @param String $edmxPath      Path of EDMX file
     * 
     * @return void
     */
    public function generateMetadataProvider(
        $serviceInfo, $serviceOutDir, $serviceXslDir, $edmxPath
    ) {
        //Metadata Provider Generation.
        $xsl_path = $serviceXslDir."/EDMXToMetadataProvider.xsl";
        $xslDoc = new DOMDocument();
        if (file_exists($xsl_path)) {
            if (!$xslDoc->load($xsl_path)) {
                die("Error while loading Metadata provider xsl file.");
            }
        } else {
            die("Error: XLS file for metadata generation is not found.");
        }
        $proc = new XSLTProcessor();
        try {
            $proc->importStylesheet($xslDoc);
        }
        catch (\Exception $e) {
            die("Error: ".$e->getMessage());
        }
        $proc->setParameter(
            '', 'QueryProviderVersion', $serviceInfo['queryProviderVersion']
        );

        $metadataPath = $serviceOutDir."/".$this->options['serviceName'].
            "Metadata.php";
        $metadataDoc = new DOMDocument();
        if (file_exists($edmxPath)) {
            if (!$metadataDoc->load($edmxPath)) {
                die("Error while loading EDMX.xml file.");
            }
        } else {
            die("Error: EDMX file for metadata generation is not found.");
        }
        $file= fopen($metadataPath, "w");
        chmod($metadataPath, 0777);
        $proc->transformToURI($metadataDoc, $metadataPath);
        unset($xslDoc);
        unset($proc);
        unset($metadataDoc);

        system("PHP -l $metadataPath  1> ".$serviceOutDir."/msg.tmp 2>&1", $temp);
        unlink($serviceOutDir . "/msg.tmp");
        if ($temp == 0 or $temp == 127) {
            echo "\nMetadataProvider class has generated Successfully.";
        } else {
            $this->showUsage(
                "Error in generation of MetadataProvider class ... \nPlease check "
                ."the syntax of file:"
                .$metadataPath
            );
        }
    }

    /**
     * Genearte query providers
     *
     * @param Object $serviceInfo   ServiceInfo Object
     * @param String $serviceOutDir Path of output-files for current service
     * @param String $serviceXslDir Path of xsl-files for generating providers
     * @param String $edmxFile      Path of EDMX file
     * @param Int    $stage         Stage-identifier
     *
     * @return void
     */
    public function generateQueryProvider(
        $serviceInfo, $serviceOutDir, $serviceXslDir, $edmxFile, $stage
    ) {
        //Query Provider Generation.
        if ($serviceInfo['queryProviderVersion'] == 1) {
            $xsl_path = $serviceXslDir."/EDMXToQueryProvider.xsl";
        } else {
            $xsl_path = $serviceXslDir."/EDMXToQueryProvider2.xsl";
        }
        if ($stage === 2) {
            $this->readConfigParamsFromFile($serviceOutDir);
        }
        $xslDoc = new DOMDocument();
        if (file_exists($xsl_path)) {
            if (!$xslDoc->load($xsl_path)) {
                die("Error while loading Query provider xsl file.");
            }
        } else {
            die("Error: XLS file for Query provider generatior is not found.");
        }
        $xslDoc->load($xsl_path);
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xslDoc);

        $proc->setParameter(
            '', 'IDSQP_Version', $serviceInfo['queryProviderVersion']
        );

        $proc->setParameter('', 'ServiceName', $this->options['serviceName']);
        $proc->setParameter('', 'DBName', $this->options['dbname']);
        $proc->setParameter('', 'DBHost', $this->options['host']);
        $proc->setParameter('', 'DBUser', $this->options['user']);
        $proc->setParameter('', 'DBPass', $this->options['password']);
        if (isset($this->options['port'])) {
            $proc->setParameter('', 'DBPort', $this->options['port']);
        }
        $proc->setParameter(
            '', 'followPularizeSingularizeRule',
            $serviceInfo['followPularizeSingularizeRule']
        );
        $queryProviderPath = $serviceOutDir."/".$this->options['serviceName'].
            "QueryProvider.php";
        $metadataDoc = new DOMDocument();
        if (file_exists($edmxFile)) {
            if (!$metadataDoc->load($edmxFile)) {
                die("Error while loading EDMX.xml file.");
            }
        } else {
            die("Error: EDMX file for query generatior is not found.");
        }
        $file= fopen($queryProviderPath, "w");
        chmod($queryProviderPath, 0777);
        $proc->transformToURI($metadataDoc, $queryProviderPath);
        unset($xslDoc);
        unset($proc);
        unset($metadataDoc);
        system(
            "PHP -l $queryProviderPath  1> ".$serviceOutDir."/msg.tmp 2>&1", $temp
        );
        unlink($serviceOutDir . "/msg.tmp");
        if ($temp == 0 or $temp == 127) {
            echo "\nQueryProvider class has generated Successfully.";
        } else {
            $this->showUsage(
                "Error in generation of  QueryProvider class ... \nPlease check the"
                ." syntax of file:".$queryProviderPath
            );
        }
    }

    /**
     * Genearte service providers
     *
     * @param Object $serviceInfo   ServiceInfo Object
     * @param String $serviceOutDir Path of output-files for current service
     * @param String $serviceXslDir Path of xsl-files for generating providers
     * @param String $configFile    config file name
     *
     * @return void
     */
    public function generateServiceProvider(
        $serviceInfo, $serviceOutDir, $serviceXslDir, $configFile
    ) {
        //DataService Provider Generation.
        $xsl_path = $serviceXslDir."/EDMXToDataServiceProvider.xsl";
        $xslDoc = new DOMDocument();
        if (file_exists($xsl_path)) {
            if (!$xslDoc->load($xsl_path)) {
                die("Error while loading Data Service provider xsl file.");
            }
        } else {
            die("Error: XLS file for data service generator is not found.");
        }

        $xslDoc->load($xsl_path);
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xslDoc);

        $proc->setParameter('', 'serviceName', $this->options['serviceName']);
        $metadataDoc = new DOMDocument();
        $serviceProviderPath = $serviceOutDir."/".$this->options['serviceName'].
            "DataService.php";
        if (file_exists($configFile)) {
            if (!$metadataDoc->load($configFile)) {
                die(
                    "Error: while loading service.config.xml file for service "
                    ."provider generatior."
                );
            }
        } else {
            die(
                "Error: service.config.xml file is not found for service provider ".
                "generatior."
            );
        }
        $file= fopen($serviceProviderPath, "w");
        chmod($serviceProviderPath, 0777);
        $proc->transformToURI($metadataDoc, $serviceProviderPath);
        unset($xslDoc);
        unset($proc);
        unset($metadataDoc);
        system(
            "PHP -l $serviceProviderPath 1> " . $serviceOutDir . "/msg.tmp 2>&1", 
            $temp
        );
        unlink($serviceOutDir . "/msg.tmp");
        if ($temp == 0 or $temp == 127) {
            echo "\nDataServiceProvider class has generated Successfully.";
        } else {
            //Throw exception
            $this->showUsage(
                "Error in generation of  DataServiceProvider class ... \nPlease "
                ."check the syntax of file:".$serviceProviderPath
            );
        }
    }

    /**
     * Genearte expression providers
     * 
     * @param Object $serviceInfo   ServiceInfo Object
     * @param String $serviceOutDir Path of output-files for current service
     * @param String $serviceXslDir Path of xsl-files for generating providers
     * @param String $configFile    config file name
     *
     * @return void
     */
    public function generateExpressionProvider(
        $serviceInfo, $serviceOutDir, $serviceXslDir, $configFile
    ) {
        //DSExpression Provider Generation.
        if ($serviceInfo['queryProviderVersion'] == 2) {
            $xsl_path = $serviceXslDir."/EDMXToDSExpressionProvider.xsl";
            $xslDoc = new DOMDocument();
            if (file_exists($xsl_path)) {
                if (!$xslDoc->load($xsl_path)) {
                    die("Error while loading xls file for Expression Provider.");
                }
            } else {
                die(
                    "Error: xls file for DSExpression provider generatior not found"
                );
            }
            $proc = new XSLTProcessor();
            $proc->importStylesheet($xslDoc);

            $proc->setParameter('', 'serviceName', $this->options['serviceName']);
            $metadataDoc = new DOMDocument();
            $expressionProviderPath = $serviceOutDir."/".
                $this->options['serviceName']."DSExpressionProvider.php";
            if (file_exists($configFile)) {
                if (!$metadataDoc->load($configFile)) {
                    die(
                        "Error while loading service.config.xml file for "
                        ."DSExpression provider."
                    );
                }
            } else {
                die(
                    "Error: service.config.xml file is not found for "
                    ."DSExpression Provider."
                );
            }
            $file= fopen($expressionProviderPath, "w");
            chmod($expressionProviderPath, 0777);
            $proc->transformToURI($metadataDoc, $expressionProviderPath);
            unset($xslDoc);
            unset($proc);
            unset($metadataDoc);
            system(
                "PHP -l $expressionProviderPath  1> ".$serviceOutDir.
                "/msg.tmp 2>&1", $temp
            );
            unlink($serviceOutDir."/msg.tmp");
            if ($temp == 0 or $temp == 127) {
                echo "\nDSExpressionProvider class has generated Successfully.";
            } else {
                //Throw exception
                $this->showUsage(
                    "Error in generation of  ExpressionProvider class ... \nPlease "
                    ."check the syntax of file:"
                    .$expressionProviderPath
                );
            }
        }
    }

    /**
     * Generates sample service.config file. from which service tag can be copied
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
        $this->xmlWriter->startElement(
            ODataConnectorForMySQLConstants::CONFIGURATION
        );
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::SERVICES);
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::SERVICE);
        $this->xmlWriter->writeAttribute(
            ODataConnectorForMySQLConstants::NAME, $serviceName.".svc"
        );
        $this->xmlWriter->endAttribute();
        $this->xmlWriter->startElement(ODataConnectorForMySQLConstants::PATH);
        $this->xmlWriter->text(
            "Services\\".$serviceName."\\".$serviceName."DataService.php"
        );
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
        chmod($serviceOutDir."/service.config.xml", 0777);
        fwrite($fp, $xml);
        fclose($fp);
        if (file_exists($serviceOutDir."/service.config.xml")) {
            echo "\nService.config file has generated Successfully.\n";
        } else {
            $this->showUsage("Error in generation of service.config.xml.");
        }
    }
}
?>
