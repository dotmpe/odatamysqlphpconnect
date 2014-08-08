<?php  	
 

	/** 
	 * Implementation of IDataServiceQueryProvider.
	 * 
	 * PHP version 5.3
	 * 
	 * @category  Service
	 * @package   Northwind;
	 * @author    MySQLConnector <odataphpproducer_alias@microsoft.com>
	 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
	 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
	 * @version   SVN: 1.0
	 * @link      http://odataphpproducer.codeplex.com
	 */     
	use ODataProducer\UriProcessor\ResourcePathProcessor\SegmentParser\KeyDescriptor;
	use ODataProducer\Providers\Metadata\ResourceSet;
	use ODataProducer\Providers\Metadata\ResourceProperty;
	use ODataProducer\Providers\Query\IDataServiceQueryProvider2;
	require_once "NorthwindMetadata.php";
	require_once "ODataProducer/Providers/Query/IDataServiceQueryProvider2.php";
	
	/** The name of the database for Northwind*/
	define('DB_NAME', "northwind");
	/** MySQL database username */
	define('DB_USER', "root");
	/** MySQL database password */
	define('DB_PASSWORD', "");
	/** MySQL hostname */
	define('DB_HOST', "localhost");
			
   			
	/**
     * NorthwindQueryProvider implemetation of IDataServiceQueryProvider2.
	 * @category  Service
	 * @package   Northwind;
	 * @author    MySQLConnector <odataphpproducer_alias@microsoft.com>
	 * @copyright 2011 Microsoft Corp. (http://www.microsoft.com)
	 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
	 * @version   Release: 1.0
	 * @link      http://odataphpproducer.codeplex.com
	 */
	class NorthwindQueryProvider implements IDataServiceQueryProvider2
	{
    	/**
     	 * Handle to connection to Database     
     	 */
    	private $_connectionHandle = null;

    	/**
     	 * Constructs a new instance of NorthwindQueryProvider
     	 * 
     	 */
	    public function __construct()
    	{
        	$this->_connectionHandle = @mysql_connect(DB_HOST, DB_USER, DB_PASSWORD, true);
        	if ( $this->_connectionHandle ) {
        		mysql_select_db(DB_NAME, $this->_connectionHandle);
        	} else {             
            	die(mysql_error());
        	} 
    	}

	    /**
    	 * Gets collection of entities belongs to an entity set
     	 * 
     	 * @param ResourceSet $resourceSet The entity set whose entities needs to be fetched
     	 * 
     	 * @return array(Object)
     	 */
    	public function getResourceSet(ResourceSet $resourceSet, $filterOption = null, 
        	$select=null, $orderby=null, $top=null, $skip=null)
    	{   
        	$resourceSetName =  $resourceSet->getName();
			if( $resourceSetName !== 'categories'
        				 
        	and $resourceSetName !== 'customers'
        				 
        	and $resourceSetName !== 'employees'
        				 
        	and $resourceSetName !== 'order_details'
        				 
        	and $resourceSetName !== 'orders'
        				 
        	and $resourceSetName !== 'products'
        				 
        	and $resourceSetName !== 'shippers'
        				 
        	and $resourceSetName !== 'suppliers'
        				)	       		
        	{
        		die('(NorthwindQueryProvider) Unknown resource set ' . $resourceSetName);
        	}
			        	
        	$query = "SELECT * FROM $resourceSetName";
	        if ($filterOption != null) {
    	        $query .= ' WHERE ' . $filterOption;
        	}
        	$stmt = mysql_query($query);
        	if ($stmt === false) {
            	die(print_r(mysql_error(), true));
        	}

        	$returnResult = array();
        	switch ($resourceSetName) {
        		
				case 'categories':
	        		
	        		$returnResult = $this->_serializecategories($stmt);
       				break;
				
				case 'customers':
	        		
	        		$returnResult = $this->_serializecustomers($stmt);
       				break;
				
				case 'employees':
	        		
	        		$returnResult = $this->_serializeemployees($stmt);
       				break;
				
				case 'order_details':
	        		
	        		$returnResult = $this->_serializeorder_details($stmt);
       				break;
				
				case 'orders':
	        		
	        		$returnResult = $this->_serializeorders($stmt);
       				break;
				
				case 'products':
	        		
	        		$returnResult = $this->_serializeproducts($stmt);
       				break;
				
				case 'shippers':
	        		
	        		$returnResult = $this->_serializeshippers($stmt);
       				break;
				
				case 'suppliers':
	        		
	        		$returnResult = $this->_serializesuppliers($stmt);
       				break;
				
        	}
        	mysql_free_result($stmt);
        	return $returnResult;        
		} 


	    /**
    	 * Gets an entity instance from an entity set identifed by a key
	     * 
    	 * @param ResourceSet   $resourceSet   The entity set from which 
	     *                                     an entity needs to be fetched
    	 * @param KeyDescriptor $keyDescriptor The key to identify the entity to be fetched
     	 * 
	     * @return Object/NULL Returns entity instance if found else null
    	 */
	    public function getResourceFromResourceSet(ResourceSet $resourceSet, KeyDescriptor $keyDescriptor)
    	{   
        	$resourceSetName =  $resourceSet->getName();
        	if( $resourceSetName !== 'categories'
        				 
        	and $resourceSetName !== 'customers'
        				 
        	and $resourceSetName !== 'employees'
        				 
        	and $resourceSetName !== 'order_details'
        				 
        	and $resourceSetName !== 'orders'
        				 
        	and $resourceSetName !== 'products'
        				 
        	and $resourceSetName !== 'shippers'
        				 
        	and $resourceSetName !== 'suppliers'
        				)	       		
        	{
	        	die('(NorthwindQueryProvider) Unknown resource set ' . $resourceSetName);
    	    }
    	    
    	
        	$namedKeyValues = $keyDescriptor->getValidatedNamedValues();
        	$condition = null;
        	foreach ($namedKeyValues as $key => $value) {
	            $condition .= $key . ' = ' . $value[0] . ' and ';
    	    }
	
    	    $len = strlen($condition);
        	$condition = substr($condition, 0, $len - 5); 
	        $query = "SELECT * FROM $resourceSetName WHERE $condition";
    	    $stmt = mysql_query($query);
        	if ($stmt === false) {
            	die(print_r(mysql_error(), true));
        	}

        	//If resource not found return null to the library
        	if (!mysql_num_rows($stmt)) {
            	return null;
        	}

	        $result = null;
        	while ( $record = mysql_fetch_array($stmt, MYSQL_ASSOC)) {
    	    	switch ($resourceSetName) {
    	    		
				case 'categories':
	        		
	        		$returnResult = $this->_serializecategory($record);
       				break;
				
				case 'customers':
	        		
	        		$returnResult = $this->_serializecustomer($record);
       				break;
				
				case 'employees':
	        		
	        		$returnResult = $this->_serializeemployee($record);
       				break;
				
				case 'order_details':
	        		
	        		$returnResult = $this->_serializeorder_detail($record);
       				break;
				
				case 'orders':
	        		
	        		$returnResult = $this->_serializeorder($record);
       				break;
				
				case 'products':
	        		
	        		$returnResult = $this->_serializeproduct($record);
       				break;
				
				case 'shippers':
	        		
	        		$returnResult = $this->_serializeshipper($record);
       				break;
				
				case 'suppliers':
	        		
	        		$returnResult = $this->_serializesupplier($record);
       				break;
				
        		}
        	}	
        	mysql_free_result($stmt);
        	return $returnResult;        
    	}
    	
	    /**
    	 * Gets a related entity instance from an entity set identifed by a key
	     * 
    	 * @param ResourceSet      $sourceResourceSet    The entity set related to
	     *                                               the entity to be fetched.
    	 * @param object           $sourceEntityInstance The related entity instance.
     	 * @param ResourceSet      $targetResourceSet    The entity set from which
     	 *                                               entity needs to be fetched.
     	 * @param ResourceProperty $targetProperty       The metadata of the target 
     	 *                                               property.
     	 * @param KeyDescriptor    $keyDescriptor        The key to identify the entity 
     	 *                                               to be fetched.
     	 * 
     	 * @return Object/NULL Returns entity instance if found else null
     	 */
    	public function  getResourceFromRelatedResourceSet(ResourceSet $sourceResourceSet, 
        	$sourceEntityInstance, 
        	ResourceSet $targetResourceSet,
        	ResourceProperty $targetProperty,
        	KeyDescriptor $keyDescriptor
    	) {
        	$result = array();
        	$srcClass = get_class($sourceEntityInstance);
        	$navigationPropName = $targetProperty->getName();
        	$key = null;
        	foreach ($keyDescriptor->getValidatedNamedValues() as $keyName => $valueDescription) {
	            $key = $key . $keyName . '=' . $valueDescription[0] . ' and ';
    	    }
        	$key = rtrim($key, ' and ');
       		if($srcClass === 'category')
			{		
				if($navigationPropName === 'products') 
				{			
							
					$query = "SELECT * FROM products WHERE CategoryID = '$sourceEntityInstance->CategoryID' and $key";
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeproducts($stmt);
				}
									
				else {
					die('category does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
			else if($srcClass === 'customers')
			{		
				if($navigationPropName === 'orders') 
				{			
							
					$query = "SELECT * FROM orders WHERE CustomerID = '$sourceEntityInstance->CustomerID' and $key";
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeorders($stmt);
				}
									
				else {
					die('customers does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
			else if($srcClass === 'employee')
			{		
				if($navigationPropName === 'employees') 
				{			
							
					$query = "SELECT * FROM employees WHERE ReportsTo = '$sourceEntityInstance->EmployeeID' and $key";
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeemployees($stmt);
				}
																						
				else if($navigationPropName === 'orders') 
				{			
							
					$query = "SELECT * FROM orders WHERE EmployeeID = '$sourceEntityInstance->EmployeeID' and $key";
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeorders($stmt);
				}
									
				else {
					die('employee does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
			else if($srcClass === 'order_detail')
			{		
				
			}	
			
			else if($srcClass === 'order')
			{		
				if($navigationPropName === 'order_details') 
				{			
							
					$query = "SELECT * FROM order_details WHERE OrderID = '$sourceEntityInstance->OrderID' and $key";
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeorder_details($stmt);
				}
									
				else {
					die('order does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
			else if($srcClass === 'product')
			{		
				if($navigationPropName === 'order_details') 
				{			
							
					$query = "SELECT * FROM order_details WHERE ProductID = '$sourceEntityInstance->ProductID' and $key";
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeorder_details($stmt);
				}
									
				else {
					die('product does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
			else if($srcClass === 'shipper')
			{		
				if($navigationPropName === 'orders') 
				{			
							
					$query = "SELECT * FROM orders WHERE ShipVia = '$sourceEntityInstance->ShipperID' and $key";
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeorders($stmt);
				}
									
				else {
					die('shipper does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
			else if($srcClass === 'supplier')
			{		
				if($navigationPropName === 'products') 
				{			
							
					$query = "SELECT * FROM products WHERE SupplierID = '$sourceEntityInstance->SupplierID' and $key";
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeproducts($stmt);
				}
									
				else {
					die('supplier does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
       		return empty($result) ? null : $result[0];	
		}
		
    
	    /**
    	 * Get related resource set for a resource
     	* 
     	* @param ResourceSet      $sourceResourceSet    The source resource set
     	* @param mixed            $sourceEntityInstance The resource
     	* @param ResourceSet      $targetResourceSet    The resource set of 
     	*                                               the navigation property
     	* @param ResourceProperty $targetProperty       The navigation property to be 
     	*                                               retrieved
     	*                                               
     	* @return array(Objects)/array() Array of related resource if exists, if no 
     	*                                related resources found returns empty array
     	*/
    	public function  getRelatedResourceSet(ResourceSet $sourceResourceSet, 
        	$sourceEntityInstance, 
        	ResourceSet $targetResourceSet,
        	ResourceProperty $targetProperty,
	        $filterOption = null,
    	    $select=null, $orderby=null, $top=null, $skip=null
    	) {
	        $result = array();
    	    $srcClass = get_class($sourceEntityInstance);
	        $navigationPropName = $targetProperty->getName();
       		if($srcClass === 'category')
			{		
				if($navigationPropName === 'products') 
				{			
							
					$query = "SELECT * FROM products WHERE CategoryID = '$sourceEntityInstance->CategoryID'";
	                if ($filterOption != null) {
    	                $query .= ' AND ' . $filterOption;
        	        }
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeproducts($stmt);
				}
									
				else {
					die('category does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
			else if($srcClass === 'customers')
			{		
				if($navigationPropName === 'orders') 
				{			
							
					$query = "SELECT * FROM orders WHERE CustomerID = '$sourceEntityInstance->CustomerID'";
	                if ($filterOption != null) {
    	                $query .= ' AND ' . $filterOption;
        	        }
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeorders($stmt);
				}
									
				else {
					die('customers does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
			else if($srcClass === 'employee')
			{		
				if($navigationPropName === 'employees') 
				{			
							
					$query = "SELECT * FROM employees WHERE ReportsTo = '$sourceEntityInstance->EmployeeID'";
	                if ($filterOption != null) {
    	                $query .= ' AND ' . $filterOption;
        	        }
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeemployees($stmt);
				}
																						
				else if($navigationPropName === 'orders') 
				{			
							
					$query = "SELECT * FROM orders WHERE EmployeeID = '$sourceEntityInstance->EmployeeID'";
	                if ($filterOption != null) {
    	                $query .= ' AND ' . $filterOption;
        	        }
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeorders($stmt);
				}
									
				else {
					die('employee does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
			else if($srcClass === 'order_detail')
			{		
				
			}	
			
			else if($srcClass === 'order')
			{		
				if($navigationPropName === 'order_details') 
				{			
							
					$query = "SELECT * FROM order_details WHERE OrderID = '$sourceEntityInstance->OrderID'";
	                if ($filterOption != null) {
    	                $query .= ' AND ' . $filterOption;
        	        }
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeorder_details($stmt);
				}
									
				else {
					die('order does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
			else if($srcClass === 'product')
			{		
				if($navigationPropName === 'order_details') 
				{			
							
					$query = "SELECT * FROM order_details WHERE ProductID = '$sourceEntityInstance->ProductID'";
	                if ($filterOption != null) {
    	                $query .= ' AND ' . $filterOption;
        	        }
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeorder_details($stmt);
				}
									
				else {
					die('product does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
			else if($srcClass === 'shipper')
			{		
				if($navigationPropName === 'orders') 
				{			
							
					$query = "SELECT * FROM orders WHERE ShipVia = '$sourceEntityInstance->ShipperID'";
	                if ($filterOption != null) {
    	                $query .= ' AND ' . $filterOption;
        	        }
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeorders($stmt);
				}
									
				else {
					die('shipper does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
			else if($srcClass === 'supplier')
			{		
				if($navigationPropName === 'products') 
				{			
							
					$query = "SELECT * FROM products WHERE SupplierID = '$sourceEntityInstance->SupplierID'";
	                if ($filterOption != null) {
    	                $query .= ' AND ' . $filterOption;
        	        }
			        $stmt = mysql_query($query);
        			if ($stmt === false) {            
        				die(print_r(mysql_error(), true));
	    			}
	    			$result = $this->_serializeproducts($stmt);
				}
									
				else {
					die('supplier does not have navigation porperty with name: ' . $navigationPropName);
				}
				
			}	
			
       		return $result;	        
    	}    
    	
	    /**
    	 * Get related resource for a resource
     	* 
     	* @param ResourceSet      $sourceResourceSet    The source resource set
     	* @param mixed            $sourceEntityInstance The source resource
     	* @param ResourceSet      $targetResourceSet    The resource set of 
     	*                                               the navigation property
     	* @param ResourceProperty $targetProperty       The navigation property to be 
     	*                                               retrieved
     	* 
     	* @return Object/null The related resource if exists else null
     	*/
    	public function getRelatedResourceReference(ResourceSet $sourceResourceSet, 
        	$sourceEntityInstance, 
        	ResourceSet $targetResourceSet,
        	ResourceProperty $targetProperty
    	) {
        	$result = null;
        	$srcClass = get_class($sourceEntityInstance);
        	$navigationPropName = $targetProperty->getName();
			if($srcClass==='category')
			{
										
			}
				
			else if($srcClass==='customers')
			{
										
			}
				
			else if($srcClass==='employee')
			{
										
			}
				
			else if($srcClass==='order_detail')
			{
					 if($navigationPropName === 'order')
				{
					if (empty($sourceEntityInstance->OrderID))
					{
                		$result = null;
					} else {
						$query = "SELECT * FROM orders WHERE OrderID = '$sourceEntityInstance->OrderID'";
						$stmt = mysql_query($query);
						if ($stmt === false) {
							die(print_r(mysql_error(), true));
						}
						if (!mysql_num_rows($stmt)) {
							$result =  null;
						}
						$result = $this->_serializeorder(mysql_fetch_array($stmt, MYSQL_ASSOC));
					}
				}
								
				else if($navigationPropName === 'product')
				{
					if (empty($sourceEntityInstance->ProductID))
					{
                		$result = null;
					} else {
						$query = "SELECT * FROM products WHERE ProductID = '$sourceEntityInstance->ProductID'";
						$stmt = mysql_query($query);
						if ($stmt === false) {
							die(print_r(mysql_error(), true));
						}
						if (!mysql_num_rows($stmt)) {
							$result =  null;
						}
						$result = $this->_serializeproduct(mysql_fetch_array($stmt, MYSQL_ASSOC));
					}
				}
								
				else {
					die('order_detail does not have navigation porperty with name: ' . $navigationPropName);
				}
											
			}
				
			else if($srcClass==='order')
			{
					 if($navigationPropName === 'customer')
				{
					if (empty($sourceEntityInstance->CustomerID))
					{
                		$result = null;
					} else {
						$query = "SELECT * FROM customers WHERE CustomerID = '$sourceEntityInstance->CustomerID'";
						$stmt = mysql_query($query);
						if ($stmt === false) {
							die(print_r(mysql_error(), true));
						}
						if (!mysql_num_rows($stmt)) {
							$result =  null;
						}
						$result = $this->_serializecustomer(mysql_fetch_array($stmt, MYSQL_ASSOC));
					}
				}
								
				else if($navigationPropName === 'employee')
				{
					if (empty($sourceEntityInstance->EmployeeID))
					{
                		$result = null;
					} else {
						$query = "SELECT * FROM employees WHERE EmployeeID = '$sourceEntityInstance->EmployeeID'";
						$stmt = mysql_query($query);
						if ($stmt === false) {
							die(print_r(mysql_error(), true));
						}
						if (!mysql_num_rows($stmt)) {
							$result =  null;
						}
						$result = $this->_serializeemployee(mysql_fetch_array($stmt, MYSQL_ASSOC));
					}
				}
								
				else if($navigationPropName === 'shipper')
				{
					if (empty($sourceEntityInstance->ShipVia))
					{
                		$result = null;
					} else {
						$query = "SELECT * FROM shippers WHERE ShipVia = '$sourceEntityInstance->ShipVia'";
						$stmt = mysql_query($query);
						if ($stmt === false) {
							die(print_r(mysql_error(), true));
						}
						if (!mysql_num_rows($stmt)) {
							$result =  null;
						}
						$result = $this->_serializeshipper(mysql_fetch_array($stmt, MYSQL_ASSOC));
					}
				}
								
				else {
					die('order does not have navigation porperty with name: ' . $navigationPropName);
				}
											
			}
				
			else if($srcClass==='product')
			{
					 if($navigationPropName === 'category')
				{
					if (empty($sourceEntityInstance->CategoryID))
					{
                		$result = null;
					} else {
						$query = "SELECT * FROM categories WHERE CategoryID = '$sourceEntityInstance->CategoryID'";
						$stmt = mysql_query($query);
						if ($stmt === false) {
							die(print_r(mysql_error(), true));
						}
						if (!mysql_num_rows($stmt)) {
							$result =  null;
						}
						$result = $this->_serializecategory(mysql_fetch_array($stmt, MYSQL_ASSOC));
					}
				}
								
				else if($navigationPropName === 'supplier')
				{
					if (empty($sourceEntityInstance->SupplierID))
					{
                		$result = null;
					} else {
						$query = "SELECT * FROM suppliers WHERE SupplierID = '$sourceEntityInstance->SupplierID'";
						$stmt = mysql_query($query);
						if ($stmt === false) {
							die(print_r(mysql_error(), true));
						}
						if (!mysql_num_rows($stmt)) {
							$result =  null;
						}
						$result = $this->_serializesupplier(mysql_fetch_array($stmt, MYSQL_ASSOC));
					}
				}
								
				else {
					die('product does not have navigation porperty with name: ' . $navigationPropName);
				}
											
			}
				
			else if($srcClass==='shipper')
			{
										
			}
				
			else if($srcClass==='supplier')
			{
										
			}
				
			return $result;
		}
			
		
		/**
    	 * Serialize the sql result array into category objects
		 * 	
     	 * @param array(array) $result result of the sql query
     	 * 
     	 * @return array(Object)
     	 */
    	private function _serializecategories($result)
    	{
        	$categories = array();
        	while ($record = mysql_fetch_array($result, MYSQL_ASSOC)) {         
            	$categories[] = $this->_serializecategory($record);
        	}
        	return $categories;
    	}
    	
    	/**
    	 * Serialize the sql row into category object
	     * 
    	 * @param array $record each row of category
	     * 
    	 * @return Object
	     */
	    private function _serializecategory($record)
    	{
        	$category = new category();
        	
			$category->CategoryID = $record['CategoryID'];							
								
			$category->CategoryName = $record['CategoryName'];							
								
			$category->Description = $record['Description'];							
								
			$category->Picture = $record['Picture'];							
								
    		return $category;
		}										
			
		/**
    	 * Serialize the sql result array into customer objects
		 * 	
     	 * @param array(array) $result result of the sql query
     	 * 
     	 * @return array(Object)
     	 */
    	private function _serializecustomers($result)
    	{
        	$customers = array();
        	while ($record = mysql_fetch_array($result, MYSQL_ASSOC)) {         
            	$customers[] = $this->_serializecustomer($record);
        	}
        	return $customers;
    	}
    	
    	/**
    	 * Serialize the sql row into customer object
	     * 
    	 * @param array $record each row of customer
	     * 
    	 * @return Object
	     */
	    private function _serializecustomer($record)
    	{
        	$customer = new customer();
        	
			$customer->CustomerID = $record['CustomerID'];							
								
			$customer->CompanyName = $record['CompanyName'];							
								
			$customer->ContactName = $record['ContactName'];							
								
			$customer->ContactTitle = $record['ContactTitle'];							
								
			$customer->Address = $record['Address'];							
								
			$customer->City = $record['City'];							
								
			$customer->Region = $record['Region'];							
								
			$customer->PostalCode = $record['PostalCode'];							
								
			$customer->Country = $record['Country'];							
								
			$customer->Phone = $record['Phone'];							
								
			$customer->Fax = $record['Fax'];							
								
    		return $customer;
		}										
			
		/**
    	 * Serialize the sql result array into employee objects
		 * 	
     	 * @param array(array) $result result of the sql query
     	 * 
     	 * @return array(Object)
     	 */
    	private function _serializeemployees($result)
    	{
        	$employees = array();
        	while ($record = mysql_fetch_array($result, MYSQL_ASSOC)) {         
            	$employees[] = $this->_serializeemployee($record);
        	}
        	return $employees;
    	}
    	
    	/**
    	 * Serialize the sql row into employee object
	     * 
    	 * @param array $record each row of employee
	     * 
    	 * @return Object
	     */
	    private function _serializeemployee($record)
    	{
        	$employee = new employee();
        	
			$employee->EmployeeID = $record['EmployeeID'];							
								
			$employee->LastName = $record['LastName'];							
								
			$employee->FirstName = $record['FirstName'];							
								
			$employee->Title = $record['Title'];							
								
			$employee->TitleOfCourtesy = $record['TitleOfCourtesy'];							
								
			$employee->BirthDate = $record['BirthDate'];							
								
			$employee->HireDate = $record['HireDate'];							
								
			$employee->Address = $record['Address'];							
								
			$employee->City = $record['City'];							
								
			$employee->Region = $record['Region'];							
								
			$employee->PostalCode = $record['PostalCode'];							
								
			$employee->Country = $record['Country'];							
								
			$employee->HomePhone = $record['HomePhone'];							
								
			$employee->Extension = $record['Extension'];							
								
			$employee->Photo = $record['Photo'];							
								
			$employee->Notes = $record['Notes'];							
								
			$employee->ReportsTo = $record['ReportsTo'];							
								
    		return $employee;
		}										
			
		/**
    	 * Serialize the sql result array into order_detail objects
		 * 	
     	 * @param array(array) $result result of the sql query
     	 * 
     	 * @return array(Object)
     	 */
    	private function _serializeorder_details($result)
    	{
        	$order_details = array();
        	while ($record = mysql_fetch_array($result, MYSQL_ASSOC)) {         
            	$order_details[] = $this->_serializeorder_detail($record);
        	}
        	return $order_details;
    	}
    	
    	/**
    	 * Serialize the sql row into order_detail object
	     * 
    	 * @param array $record each row of order_detail
	     * 
    	 * @return Object
	     */
	    private function _serializeorder_detail($record)
    	{
        	$order_detail = new order_detail();
        	
			$order_detail->ID = $record['ID'];							
								
			$order_detail->OrderID = $record['OrderID'];							
								
			$order_detail->ProductID = $record['ProductID'];							
								
			$order_detail->UnitPrice = $record['UnitPrice'];							
								
			$order_detail->Quantity = $record['Quantity'];							
								
			$order_detail->Discount = $record['Discount'];							
								
    		return $order_detail;
		}										
			
		/**
    	 * Serialize the sql result array into order objects
		 * 	
     	 * @param array(array) $result result of the sql query
     	 * 
     	 * @return array(Object)
     	 */
    	private function _serializeorders($result)
    	{
        	$orders = array();
        	while ($record = mysql_fetch_array($result, MYSQL_ASSOC)) {         
            	$orders[] = $this->_serializeorder($record);
        	}
        	return $orders;
    	}
    	
    	/**
    	 * Serialize the sql row into order object
	     * 
    	 * @param array $record each row of order
	     * 
    	 * @return Object
	     */
	    private function _serializeorder($record)
    	{
        	$order = new order();
        	
			$order->OrderID = $record['OrderID'];							
								
			$order->CustomerID = $record['CustomerID'];							
								
			$order->EmployeeID = $record['EmployeeID'];							
								
			$order->OrderDate = $record['OrderDate'];							
								
			$order->RequiredDate = $record['RequiredDate'];							
								
			$order->ShippedDate = $record['ShippedDate'];							
								
			$order->ShipVia = $record['ShipVia'];							
								
			$order->Freight = $record['Freight'];							
								
			$order->ShipName = $record['ShipName'];							
								
			$order->ShipAddress = $record['ShipAddress'];							
								
			$order->ShipCity = $record['ShipCity'];							
								
			$order->ShipRegion = $record['ShipRegion'];							
								
			$order->ShipPostalCode = $record['ShipPostalCode'];							
								
			$order->ShipCountry = $record['ShipCountry'];							
								
    		return $order;
		}										
			
		/**
    	 * Serialize the sql result array into product objects
		 * 	
     	 * @param array(array) $result result of the sql query
     	 * 
     	 * @return array(Object)
     	 */
    	private function _serializeproducts($result)
    	{
        	$products = array();
        	while ($record = mysql_fetch_array($result, MYSQL_ASSOC)) {         
            	$products[] = $this->_serializeproduct($record);
        	}
        	return $products;
    	}
    	
    	/**
    	 * Serialize the sql row into product object
	     * 
    	 * @param array $record each row of product
	     * 
    	 * @return Object
	     */
	    private function _serializeproduct($record)
    	{
        	$product = new product();
        	
			$product->ProductID = $record['ProductID'];							
								
			$product->ProductName = $record['ProductName'];							
								
			$product->SupplierID = $record['SupplierID'];							
								
			$product->CategoryID = $record['CategoryID'];							
								
			$product->QuantityPerUnit = $record['QuantityPerUnit'];							
								
			$product->UnitPrice = $record['UnitPrice'];							
								
			$product->UnitsInStock = $record['UnitsInStock'];							
								
			$product->UnitsOnOrder = $record['UnitsOnOrder'];							
								
			$product->ReorderLevel = $record['ReorderLevel'];							
								
			$product->Discontinued = $record['Discontinued'];							
								
    		return $product;
		}										
			
		/**
    	 * Serialize the sql result array into shipper objects
		 * 	
     	 * @param array(array) $result result of the sql query
     	 * 
     	 * @return array(Object)
     	 */
    	private function _serializeshippers($result)
    	{
        	$shippers = array();
        	while ($record = mysql_fetch_array($result, MYSQL_ASSOC)) {         
            	$shippers[] = $this->_serializeshipper($record);
        	}
        	return $shippers;
    	}
    	
    	/**
    	 * Serialize the sql row into shipper object
	     * 
    	 * @param array $record each row of shipper
	     * 
    	 * @return Object
	     */
	    private function _serializeshipper($record)
    	{
        	$shipper = new shipper();
        	
			$shipper->ShipperID = $record['ShipperID'];							
								
			$shipper->CompanyName = $record['CompanyName'];							
								
			$shipper->Phone = $record['Phone'];							
								
    		return $shipper;
		}										
			
		/**
    	 * Serialize the sql result array into supplier objects
		 * 	
     	 * @param array(array) $result result of the sql query
     	 * 
     	 * @return array(Object)
     	 */
    	private function _serializesuppliers($result)
    	{
        	$suppliers = array();
        	while ($record = mysql_fetch_array($result, MYSQL_ASSOC)) {         
            	$suppliers[] = $this->_serializesupplier($record);
        	}
        	return $suppliers;
    	}
    	
    	/**
    	 * Serialize the sql row into supplier object
	     * 
    	 * @param array $record each row of supplier
	     * 
    	 * @return Object
	     */
	    private function _serializesupplier($record)
    	{
        	$supplier = new supplier();
        	
			$supplier->SupplierID = $record['SupplierID'];							
								
			$supplier->CompanyName = $record['CompanyName'];							
								
			$supplier->ContactName = $record['ContactName'];							
								
			$supplier->ContactTitle = $record['ContactTitle'];							
								
			$supplier->Address = $record['Address'];							
								
			$supplier->City = $record['City'];							
								
			$supplier->Region = $record['Region'];							
								
			$supplier->PostalCode = $record['PostalCode'];							
								
			$supplier->Country = $record['Country'];							
								
			$supplier->Phone = $record['Phone'];							
								
			$supplier->Fax = $record['Fax'];							
								
			$supplier->HomePage = $record['HomePage'];							
								
    		return $supplier;
		}										
			
	    /**
    	 * The destructor     
     	 */
    	public function __destruct()
    	{
        	if ($this->_connectionHandle) {
            	mysql_close($this->_connectionHandle);
        	}
    	}		
    }	    
	
?>
	