<?php
/**
 * Class of Bit data type.
 *
 * PHP version 5.3
 *
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_Common_Types
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   SVN: 1.0
 * @link      
 */
namespace ODataConnectorForMySQL\Common\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Registers Bit type.
 *
 * @category  ODataConnectorForMySQL
 * @package   ODataConnectorForMySQL_Common_Types
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @link      
 */
class BitType extends \Doctrine\DBAL\Types\Type
{
    /**
     * returns SQL declaration of type
     * 
     * @param array            $fieldDeclaration field declation
     * @param AbstractPlatform $platform         object of abstract Platform
     * 
     * @return string
     */
    public function getSQLDeclaration(
        array $fieldDeclaration, 
        AbstractPlatform $platform
    ) {
        return 'Bit';
    }

    /**
     * converts value to PHP value
     * 
     * @param string           $value    value to convert
     * @param AbstractPlatform $platform object of abstract platform
     * 
     * @return php value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return (null === $value) ? null : (bool) $value;
    }

    /**
     * returns name of type
     * 
     * @return type 
     */
    public function getName()
    {
        return \Doctrine\DBAL\Types\Type::BIT;
    }
}