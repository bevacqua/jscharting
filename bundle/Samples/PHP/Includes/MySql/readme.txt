--------------------------------------------------
How to create the sample database in MySQL server
--------------------------------------------------
There are different tools for creating the sample MySql database, some of the options are:


Option 1: Using Control Panel (cPanel) for remote sample database deployment

Most web hosting providers offer a control panel (cPanel) and phpMyAdmin tool for configuring MySQL database.  phpMyAdmin is a free tool to handle the administration of MySQL with the use of a web browser. It can perform various tasks such as creating, modifying or deleting databases. The steps and user interface may differ slightly, but the typical steps are:

1.	Log into cPanel.
2.	Under Databases, click MySQL Databases. 
3.	In the New Database fields, select a host name, type a name (e.g. JSCharting) for the database, enter a username and password
4.	Click Create Database.
5.	For importing database schema and data, go to phpMyAdmin and select the new database you just created
7.	Click Import in the main area of phpMyAdmin
8.	Browse for the JSChartingDump.sql) and click Go


Option 2: MySQL Workbench
 
1.	Go to Server > Data Import
2.	Under 'Import from Disk' tab select 'Import from Self-Contained File'
3.	Browse the bundle to find the samples/php/includes/mysql/jschartingDump.sql file.
4.	Under 'Default Schema to be Imported To' click 'New' the button.
5.	Type 'JSCharting' and click OK
6.	In the bottom left, click 'Start Import'
7.	Click the refresh button by the 'SCHEMAS' section to see the new schema


Option 3: MySQL Command Line Client for local sample database creation

1.	Create a new database:
     	mysql>  create database JSCharting

2.	Set to the new JSCharting database:
	mysql> use JSCharting

3.	Import JSChartingDump.sql to the new created database:
	mysql>  source  C:\Program Files\JSCharting\Samples\PHP\Includes\MySql\JSChartingDump.sql


After creating the sample database, edit the Includes\MySqlConnection.php included with the JSCharting bundle to work with your newly created MySql database. You will need to update MySQL host name, user name and password to match the values you used during database creation.

--------------------------------------------------
Troubleshooting FAQ
--------------------------------------------------

Q. Why doesn’t the sample navigation page load?
A. Make sure index.htm is set as a default document or explicitly included in the url e.g. www.yourdomain.com/jscharting/index.htm

Q. How do I solve the Error: The webpage cannot be found?
A: Check the spelling and case of the path and file name (this is case sensitive on UNIX hosts).

Q. How do I solve the Warning: mysql_connect(): No such file or directory in /home/user/JSCharting/Samples/PHP/Includes/MySqlConnection.php on line 11   Could not connect: No such file or directory
A: If you see this error return when running the PHP samples, verify the correct MySql database information is set in PHP/Includes/MySqlConnection.php file.  For database sample creation, please see the section of this readme on database creation above.

Q. Warning: mysql_connect(): Access denied for user 'mysqluser'@'mysql.server.com' (using password: YES) in /home/user/JSCharting/Samples/PHP/Includes/MySqlConnection.php on line 11
 Could not connect: Access denied for user 'mysqluser'@'mysql.server.com' (using password: YES)
A. Verify your MySql user name and password are correct in Includes/MySqlConnection.php : 
$userdb = 'username';    // MySQL username                     
$passdb = 'password';    // MySQL password

