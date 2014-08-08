<?php

/** 
 * Implementation of IDataServiceMetadataProvider.
 * 
 * PHP version 5.3
 * 
 * @category  Service
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   SVN: 1.0
 * @link      http://odataphpproducer.codeplex.com
 * 
 */
use ODataProducer\Providers\Metadata\ResourceStreamInfo;
use ODataProducer\Providers\Metadata\ResourceAssociationSetEnd;
use ODataProducer\Providers\Metadata\ResourceAssociationSet;
use ODataProducer\Common\NotImplementedException;
use ODataProducer\Providers\Metadata\Type\EdmPrimitiveType;
use ODataProducer\Providers\Metadata\ResourceSet;
use ODataProducer\Providers\Metadata\ResourcePropertyKind;
use ODataProducer\Providers\Metadata\ResourceProperty;
use ODataProducer\Providers\Metadata\ResourceTypeKind;
use ODataProducer\Providers\Metadata\ResourceType;
use ODataProducer\Common\InvalidOperationException;
use ODataProducer\Providers\Metadata\IDataServiceMetadataProvider;
require_once 'ODataProducer/Providers/Metadata/IDataServiceMetadataProvider.php';
use ODataProducer\Providers\Metadata\ServiceBaseMetadata;
//Begin Resource Classes

/**
 * category entity type.
 * 
 * @category  Service
 * @package   Service_NorthWind
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      http://odataphpproducer.codeplex.com
 */
class category
{
    //Edm.Boolean
    public $CategoryID;
            
    //Edm.String
    public $CategoryName;
            
    //Edm.String
    public $Description;
            
    //Edm.String
    public $Picture;
            
    //Navigation Property NorthWind.products
    public $products;
    
}

/**
 * customer entity type.
 * 
 * @category  Service
 * @package   Service_NorthWind
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      http://odataphpproducer.codeplex.com
 */
class customer
{
    //Edm.String
    public $CustomerID;
            
    //Edm.String
    public $CompanyName;
            
    //Edm.String
    public $ContactName;
            
    //Edm.String
    public $ContactTitle;
            
    //Edm.String
    public $Address;
            
    //Edm.String
    public $City;
            
    //Edm.String
    public $Region;
            
    //Edm.String
    public $PostalCode;
            
    //Edm.String
    public $Country;
            
    //Edm.String
    public $Phone;
            
    //Edm.String
    public $Fax;
            
    //Navigation Property NorthWind.orders
    public $orders;
    
}

/**
 * employee entity type.
 * 
 * @category  Service
 * @package   Service_NorthWind
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      http://odataphpproducer.codeplex.com
 */
class employee
{
    //Edm.Int32
    public $EmployeeID;
            
    //Edm.String
    public $LastName;
            
    //Edm.String
    public $FirstName;
            
    //Edm.String
    public $Title;
            
    //Edm.String
    public $TitleOfCourtesy;
            
    //Edm.DateTime
    public $BirthDate;
            
    //Edm.DateTime
    public $HireDate;
            
    //Edm.String
    public $Address;
            
    //Edm.String
    public $City;
            
    //Edm.String
    public $Region;
            
    //Edm.String
    public $PostalCode;
            
    //Edm.String
    public $Country;
            
    //Edm.String
    public $HomePhone;
            
    //Edm.String
    public $Extension;
            
    //Edm.String
    public $Photo;
            
    //Edm.String
    public $Notes;
            
    //Edm.Int32
    public $ReportsTo;
            
    //Navigation Property NorthWind.employees1
    public $employees1;
    
    //Navigation Property NorthWind.employees
    public $employees;
    
    //Navigation Property NorthWind.orders
    public $orders;
    
}

/**
 * order_detail entity type.
 * 
 * @category  Service
 * @package   Service_NorthWind
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      http://odataphpproducer.codeplex.com
 */
class order_detail
{
    //Edm.Int32
    public $ID;
            
    //Edm.Int32
    public $OrderID;
            
    //Edm.Int32
    public $ProductID;
            
    //Edm.Double
    public $UnitPrice;
            
    //Edm.Int16
    public $Quantity;
            
    //Edm.Double
    public $Discount;
            
    //Navigation Property NorthWind.order
    public $order;
    
    //Navigation Property NorthWind.product
    public $product;
    
}

/**
 * order entity type.
 * 
 * @category  Service
 * @package   Service_NorthWind
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      http://odataphpproducer.codeplex.com
 */
class order
{
    //Edm.Int32
    public $OrderID;
            
    //Edm.String
    public $CustomerID;
            
    //Edm.Int32
    public $EmployeeID;
            
    //Edm.DateTime
    public $OrderDate;
            
    //Edm.DateTime
    public $RequiredDate;
            
    //Edm.DateTime
    public $ShippedDate;
            
    //Edm.Int32
    public $ShipVia;
            
    //Edm.Double
    public $Freight;
            
    //Edm.String
    public $ShipName;
            
    //Edm.String
    public $ShipAddress;
            
    //Edm.String
    public $ShipCity;
            
    //Edm.String
    public $ShipRegion;
            
    //Edm.String
    public $ShipPostalCode;
            
    //Edm.String
    public $ShipCountry;
            
    //Navigation Property NorthWind.customer
    public $customer;
    
    //Navigation Property NorthWind.employee
    public $employee;
    
    //Navigation Property NorthWind.shipper
    public $shipper;
    
    //Navigation Property NorthWind.order_details
    public $order_details;
    
}

/**
 * product entity type.
 * 
 * @category  Service
 * @package   Service_NorthWind
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      http://odataphpproducer.codeplex.com
 */
class product
{
    //Edm.Int32
    public $ProductID;
            
    //Edm.String
    public $ProductName;
            
    //Edm.Int32
    public $SupplierID;
            
    //Edm.Boolean
    public $CategoryID;
            
    //Edm.String
    public $QuantityPerUnit;
            
    //Edm.Double
    public $UnitPrice;
            
    //Edm.Int16
    public $UnitsInStock;
            
    //Edm.Int16
    public $UnitsOnOrder;
            
    //Edm.Int16
    public $ReorderLevel;
            
    //Edm.String
    public $Discontinued;
            
    //Navigation Property NorthWind.category
    public $category;
    
    //Navigation Property NorthWind.supplier
    public $supplier;
    
    //Navigation Property NorthWind.order_details
    public $order_details;
    
}

/**
 * shipper entity type.
 * 
 * @category  Service
 * @package   Service_NorthWind
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      http://odataphpproducer.codeplex.com
 */
class shipper
{
    //Edm.Int32
    public $ShipperID;
            
    //Edm.String
    public $CompanyName;
            
    //Edm.String
    public $Phone;
            
    //Navigation Property NorthWind.orders
    public $orders;
    
}

/**
 * supplier entity type.
 * 
 * @category  Service
 * @package   Service_NorthWind
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      http://odataphpproducer.codeplex.com
 */
class supplier
{
    //Edm.Int32
    public $SupplierID;
            
    //Edm.String
    public $CompanyName;
            
    //Edm.String
    public $ContactName;
            
    //Edm.String
    public $ContactTitle;
            
    //Edm.String
    public $Address;
            
    //Edm.String
    public $City;
            
    //Edm.String
    public $Region;
            
    //Edm.String
    public $PostalCode;
            
    //Edm.String
    public $Country;
            
    //Edm.String
    public $Phone;
            
    //Edm.String
    public $Fax;
            
    //Edm.String
    public $HomePage;
            
    //Navigation Property NorthWind.products
    public $products;
    
}


/**
 * Create NorthWind metadata.
 * 
 * @category  Service
 * @package   Service_NorthWind
 * @author    Yash K. Kothari <odataphpproducer_alias@microsoft.com>
 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @version   Release: 1.0
 * @link      http://odataphpproducer.codeplex.com
 */
class CreateNorthWindMetadata
{
    /**
     * create metadata
     * 
     * @return NorthWindMetadata
     */
    public static function create()
    {
        $metadata = new ServiceBaseMetadata('NorthWindEntities', 'NorthWind');
        
        //Register the entity (resource) type 'category'
        $categoryEntityType = $metadata->addEntityType(
            new ReflectionClass('category'), 'category', 'NorthWind'
        );
        $metadata->addKeyProperty(
            $categoryEntityType, 'CategoryID', EdmPrimitiveType::BOOLEAN
        );
        $metadata->addPrimitiveProperty(
            $categoryEntityType, 'CategoryName', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $categoryEntityType, 'Description', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $categoryEntityType, 'Picture', EdmPrimitiveType::STRING
        );
        
        //Register the entity (resource) type 'customer'
        $customerEntityType = $metadata->addEntityType(
            new ReflectionClass('customer'), 'customer', 'NorthWind'
        );
        $metadata->addKeyProperty(
            $customerEntityType, 'CustomerID', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $customerEntityType, 'CompanyName', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $customerEntityType, 'ContactName', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $customerEntityType, 'ContactTitle', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $customerEntityType, 'Address', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $customerEntityType, 'City', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $customerEntityType, 'Region', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $customerEntityType, 'PostalCode', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $customerEntityType, 'Country', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $customerEntityType, 'Phone', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $customerEntityType, 'Fax', EdmPrimitiveType::STRING
        );
        
        //Register the entity (resource) type 'employee'
        $employeeEntityType = $metadata->addEntityType(
            new ReflectionClass('employee'), 'employee', 'NorthWind'
        );
        $metadata->addKeyProperty(
            $employeeEntityType, 'EmployeeID', EdmPrimitiveType::INT32
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'LastName', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'FirstName', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'Title', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'TitleOfCourtesy', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'BirthDate', EdmPrimitiveType::DATETIME
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'HireDate', EdmPrimitiveType::DATETIME
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'Address', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'City', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'Region', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'PostalCode', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'Country', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'HomePhone', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'Extension', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'Photo', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'Notes', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $employeeEntityType, 'ReportsTo', EdmPrimitiveType::INT32
        );
        
        //Register the entity (resource) type 'order_detail'
        $order_detailEntityType = $metadata->addEntityType(
            new ReflectionClass('order_detail'), 'order_detail', 'NorthWind'
        );
        $metadata->addKeyProperty(
            $order_detailEntityType, 'ID', EdmPrimitiveType::INT32
        );
        $metadata->addPrimitiveProperty(
            $order_detailEntityType, 'OrderID', EdmPrimitiveType::INT32
        );
        $metadata->addPrimitiveProperty(
            $order_detailEntityType, 'ProductID', EdmPrimitiveType::INT32
        );
        $metadata->addPrimitiveProperty(
            $order_detailEntityType, 'UnitPrice', EdmPrimitiveType::DOUBLE
        );
        $metadata->addPrimitiveProperty(
            $order_detailEntityType, 'Quantity', EdmPrimitiveType::INT16
        );
        $metadata->addPrimitiveProperty(
            $order_detailEntityType, 'Discount', EdmPrimitiveType::DOUBLE
        );
        
        //Register the entity (resource) type 'order'
        $orderEntityType = $metadata->addEntityType(
            new ReflectionClass('order'), 'order', 'NorthWind'
        );
        $metadata->addKeyProperty(
            $orderEntityType, 'OrderID', EdmPrimitiveType::INT32
        );
        $metadata->addPrimitiveProperty(
            $orderEntityType, 'CustomerID', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $orderEntityType, 'EmployeeID', EdmPrimitiveType::INT32
        );
        $metadata->addPrimitiveProperty(
            $orderEntityType, 'OrderDate', EdmPrimitiveType::DATETIME
        );
        $metadata->addPrimitiveProperty(
            $orderEntityType, 'RequiredDate', EdmPrimitiveType::DATETIME
        );
        $metadata->addPrimitiveProperty(
            $orderEntityType, 'ShippedDate', EdmPrimitiveType::DATETIME
        );
        $metadata->addPrimitiveProperty(
            $orderEntityType, 'ShipVia', EdmPrimitiveType::INT32
        );
        $metadata->addPrimitiveProperty(
            $orderEntityType, 'Freight', EdmPrimitiveType::DOUBLE
        );
        $metadata->addPrimitiveProperty(
            $orderEntityType, 'ShipName', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $orderEntityType, 'ShipAddress', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $orderEntityType, 'ShipCity', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $orderEntityType, 'ShipRegion', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $orderEntityType, 'ShipPostalCode', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $orderEntityType, 'ShipCountry', EdmPrimitiveType::STRING
        );
        
        //Register the entity (resource) type 'product'
        $productEntityType = $metadata->addEntityType(
            new ReflectionClass('product'), 'product', 'NorthWind'
        );
        $metadata->addKeyProperty(
            $productEntityType, 'ProductID', EdmPrimitiveType::INT32
        );
        $metadata->addPrimitiveProperty(
            $productEntityType, 'ProductName', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $productEntityType, 'SupplierID', EdmPrimitiveType::INT32
        );
        $metadata->addPrimitiveProperty(
            $productEntityType, 'CategoryID', EdmPrimitiveType::BOOLEAN
        );
        $metadata->addPrimitiveProperty(
            $productEntityType, 'QuantityPerUnit', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $productEntityType, 'UnitPrice', EdmPrimitiveType::DOUBLE
        );
        $metadata->addPrimitiveProperty(
            $productEntityType, 'UnitsInStock', EdmPrimitiveType::INT16
        );
        $metadata->addPrimitiveProperty(
            $productEntityType, 'UnitsOnOrder', EdmPrimitiveType::INT16
        );
        $metadata->addPrimitiveProperty(
            $productEntityType, 'ReorderLevel', EdmPrimitiveType::INT16
        );
        $metadata->addPrimitiveProperty(
            $productEntityType, 'Discontinued', EdmPrimitiveType::STRING
        );
        
        //Register the entity (resource) type 'shipper'
        $shipperEntityType = $metadata->addEntityType(
            new ReflectionClass('shipper'), 'shipper', 'NorthWind'
        );
        $metadata->addKeyProperty(
            $shipperEntityType, 'ShipperID', EdmPrimitiveType::INT32
        );
        $metadata->addPrimitiveProperty(
            $shipperEntityType, 'CompanyName', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $shipperEntityType, 'Phone', EdmPrimitiveType::STRING
        );
        
        //Register the entity (resource) type 'supplier'
        $supplierEntityType = $metadata->addEntityType(
            new ReflectionClass('supplier'), 'supplier', 'NorthWind'
        );
        $metadata->addKeyProperty(
            $supplierEntityType, 'SupplierID', EdmPrimitiveType::INT32
        );
        $metadata->addPrimitiveProperty(
            $supplierEntityType, 'CompanyName', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $supplierEntityType, 'ContactName', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $supplierEntityType, 'ContactTitle', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $supplierEntityType, 'Address', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $supplierEntityType, 'City', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $supplierEntityType, 'Region', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $supplierEntityType, 'PostalCode', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $supplierEntityType, 'Country', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $supplierEntityType, 'Phone', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $supplierEntityType, 'Fax', EdmPrimitiveType::STRING
        );
        $metadata->addPrimitiveProperty(
            $supplierEntityType, 'HomePage', EdmPrimitiveType::STRING
        );
        
        $categoriesResourceSet = $metadata->addResourceSet(
            'categories', $categoryEntityType
        );
        $customersResourceSet = $metadata->addResourceSet(
            'customers', $customerEntityType
        );
        $employeesResourceSet = $metadata->addResourceSet(
            'employees', $employeeEntityType
        );
        $order_detailsResourceSet = $metadata->addResourceSet(
            'order_details', $order_detailEntityType
        );
        $ordersResourceSet = $metadata->addResourceSet(
            'orders', $orderEntityType
        );
        $productsResourceSet = $metadata->addResourceSet(
            'products', $productEntityType
        );
        $shippersResourceSet = $metadata->addResourceSet(
            'shippers', $shipperEntityType
        );
        $suppliersResourceSet = $metadata->addResourceSet(
            'suppliers', $supplierEntityType
        );

        //Register the assoications (navigations)
        
        $metadata->addResourceSetReferenceProperty(
            $employeeEntityType, 'employees', $employeesResourceSet
        );
        $metadata->addResourceReferenceProperty(
            $order_detailEntityType, 'order', $ordersResourceSet
        );
        $metadata->addResourceSetReferenceProperty(
            $orderEntityType, 'order_details', $order_detailsResourceSet
        );
        $metadata->addResourceReferenceProperty(
            $order_detailEntityType, 'product', $productsResourceSet
        );
        $metadata->addResourceSetReferenceProperty(
            $productEntityType, 'order_details', $order_detailsResourceSet
        );
        $metadata->addResourceReferenceProperty(
            $orderEntityType, 'customer', $customersResourceSet
        );
        $metadata->addResourceSetReferenceProperty(
            $customerEntityType, 'orders', $ordersResourceSet
        );
        $metadata->addResourceReferenceProperty(
            $orderEntityType, 'employee', $employeesResourceSet
        );
        $metadata->addResourceSetReferenceProperty(
            $employeeEntityType, 'orders', $ordersResourceSet
        );
        $metadata->addResourceReferenceProperty(
            $orderEntityType, 'shipper', $shippersResourceSet
        );
        $metadata->addResourceSetReferenceProperty(
            $shipperEntityType, 'orders', $ordersResourceSet
        );
        $metadata->addResourceReferenceProperty(
            $productEntityType, 'category', $categoriesResourceSet
        );
        $metadata->addResourceSetReferenceProperty(
            $categoryEntityType, 'products', $productsResourceSet
        );
        $metadata->addResourceReferenceProperty(
            $productEntityType, 'supplier', $suppliersResourceSet
        );
        $metadata->addResourceSetReferenceProperty(
            $supplierEntityType, 'products', $productsResourceSet
        );
        
        return $metadata;
    }
}
?>
