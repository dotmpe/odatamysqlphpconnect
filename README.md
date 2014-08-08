OData Connector for MySQL
========================

Just a copy of https://odatamysqlphpconnect.codeplex.com/ published as a Composer package.


The OData Connector for MySQL is a code generator tool that works with the OData Producer 
library for PHP to create the code to implement OData provider feeds for any MySQL database. 
The tool is open source (BSD license) and written in 100% cross-platform PHP that can be 
run on Linux, Windows, or Mac OS environments.

How to use the OData Connector for MySQL

Step 0. Instal composer if you don't have it: `curl -sS https://getcomposer.org/installer | php`

Step 1: download install the OData Producer library for PHP (For detailed instructions on 
installing and using the OData Producer, see the installation steps in Anu Chandy's blog 
post on the OData PHP Producer.)

Step 2: download and install the OData Connector for MySQL. (See instructions in the 
User Guide in the \docs folder of this site.)

Step 3: run this command to create an entity data model (EDMX file) that describes 
the structure of your database:

`php MySQLConnector.php /db=mysqldb_name /srvc=odata_service_name /u=db_user_name 
/pw=db_password /h=db_host_name`

Step 4: you will see an option to stop processing and edit the generated EDMX file 
before the code generation step. This is optional, and if you do this step then 
you'll need one more command to finish code generation:

`php MySQLConnector.php /srvc=odata_service_name`

That's all there is to it. After you've generated the code for the necessary interfaces 
used by the OData Producer Library for PHP, you can deploy your OData feeds as covered 
in the documentation for the OData Producer library for PHP.





