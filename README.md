# csv_parser
This is a project to create a PHP script, 
that is executed from the command line, 
which accepts a CSV file as input and processes the CSV file. 
The parsed file data is to be inserted into a MySQL database.
The script must be able to process this file appropriately.

# Requirement
php 7.2.x or greater, MySQL , Database Name users 

# How to use
Use command line to run the program

List of command

1. To parse csv file and store in db 
php user_upload --file [fileName] -u [MySQLUsername] -h [MySQLHost]
note: if the Default MySQLPassword is not empty, -p [MySQLPassword] is required 
2. To parse only without saving in db
php user_upload --file [fileName] --dry_run

3. To parse create table
php user_upload --create_table -u [MySQLUsername] -h [MySQLHost]
note: if the Default MySQLPassword id not empty, -p [MySQLPassword] is required 

4. For help
php user_upload --help

