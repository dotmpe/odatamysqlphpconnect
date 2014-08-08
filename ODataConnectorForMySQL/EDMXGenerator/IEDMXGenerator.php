<?php
/**
 * Contains ISchemaReader class is interface of Schema Reader.
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
use ODataConnectorForMySQL\ObjectModel\Association;
use ODataConnectorForMySQL\ObjectModel\AssociationSet;
/** 
 * OData writer interface.
 *
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_EDMXGenerator
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      
 */

interface IEDMXGenerator
{
    /**
     * Start Generating EDMX file.
     *
     * @return EDMX xml object.
     */
    public function generateEDMX();

    /**
     * Write Tables in EDMX format from object model.
     *
     * @return void
     */
    public function writeEntityType();
    
    /**
     * Write primary keys for Tables in EDMX format from object model.
     *
     * @param array<Key> &$entityKeys array of key object of object model.
     *
     * @return void
     */
    public function writeKey(&$entityKeys);
    
    /**
     * Write properties of Tables in EDMX format from object model.
     *
     * @param array<Property> &$properties array of property object of object model.
     *
     * @return void
     */
    public function writeProperty(&$properties);
    
    /**
     * Writes navigation Property of the Entity type.
     *
     * @param array<NavigationProperty> &$navigationProperties array of navigation 
     *                                                         property of object 
     *                                                         model.
     *
     * @return void
     */
    public function writeNavigationProperty(&$navigationProperties);
    
    /**
     * Write Associations between entity types for the given database.
     *
     * @return void
     */
    public function writeAssociations();
        
    /**
     * Write Associations between entity types for the given database.
     *
     * @param Association &$association Association object of object model.
     *
     * @return void
     */
    public function writeAssociation(Association &$association);
    
    /**
     * Write Entity Container for given database.
     *
     * @return void
     */
    public function writeEntityContainer();
    
    /**
     * Write Entity Set for given database.
     *
     * @return void
     */
    public function writeEntitySet();
    
    /**
     * Write Association Sets for given database.
     *
     * @return void
     */
    public function writeAssociationSets();
    
    /**
     * Write Association Set for each association.
     *
     * @param AssociationSet &$associationSet Association set object of Object model.
     *
     * @return void
     */
    public function writeAssociationSet(AssociationSet &$associationSet);
}
?>