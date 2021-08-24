<?php

// Variables to get options from users
$fileCSV = "";
$fileIncluded = false;
$createTable = false;
$dryRun = false;
$username = "";
$password = "";
$hostName = "";

//If the user select help
if (in_array("--help", $argv)) {
    displayOptions();
    die();
}

//If the user run program with out parameter
if (!isset($argv[1])) {
    echo "Directive is required";
    displayOptions();
    die();
}

//Checking csv file
if (in_array("--file", $argv)) {
    $indexOfParameterFile = array_search("--file", $argv);
    if (isset($argv[$indexOfParameterFile + 1])) {
        $fileCSV = $argv[$indexOfParameterFile + 1];
        if (!(file_exists($fileCSV) && is_file($fileCSV))) {
            echo("Invalid file name provided\n");
            die();
        }
    } else {
        echo "Provide File Name";
        die();
    }
}

//Checking for create table or dryRun
if (in_array("--create_table", $argv)) {
    $createTable = true;
}

if (in_array("--dry_run", $argv)) {
    $dryRun = true;
}

//Checking if the user uses both create table and dry run
if($createTable&&$dryRun){
    echo "Please select --create_table or __dry_run";
    displayOptions();
    die();
}

//Checking for username,password and host from user and storing in variables
if (in_array("-u", $argv)) {
    $index = array_search("-u", $argv);
    if (isset($argv[$index + 1])) {
        $username = $argv[$index + 1];
    }
}

if (in_array("-p", $argv)) {
    $index = array_search("-p", $argv);
    if (isset($argv[$index + 1])) {
        $password = $argv[$index + 1];
    }
}

if (in_array("-h", $argv)) {
    $index = array_search("-h", $argv);
    if (isset($argv[$index + 1])) {
        $hostName = $argv[$index + 1];
    }
}

//if the user selects create_table option only
if($createTable&($username=="" || $hostName=="")){
    echo "The parameters Username and Host are required. Please try again.\n";
    displayOptions();
    die();
}

//if the user selects dry_run option only
if($dryRun){
    if($fileCSV==""){
        echo "Please use --file with file name";
        die();
    }
}

//if the user input file name with out username and host
if(!($fileCSV=="")&($username=="" || $hostName=="")){
    echo "The parameters Username and Host are required. Please try again.\n";
    displayOptions();
    die();
}


function displayOptions()
{
    echo "
 --file [csv file name] – this is the name of the CSV to be parsed
 --create_table – this will cause the MySQL users table to be built (and no further action will be taken)
 --dry_run – this will be used with the --file directive in case we want to run the
            script but not insert into the DB. All other functions will be executed, but the
             database won't be altered
 -u – MySQL username
 -p – MySQL password
 -h – MySQL host
 --help – which will output the above list of directives with details.
    ";
}

?>