<?php

// Variables to get options from users
$fileCSV = "";
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
            echo("Invalid file provided\n");
            die();
        }
    } else {
        echo "Please Provide File Name\n";
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
    echo "The parameters Username and Host are required.\n";
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

//if the user select dry_run with file name
if($dryRun&&!($fileCSV=="")){
    //connection is not needed for dry run
    parseCSV($fileCSV, true, '');
    die;
}


//if the user input file name with out username and host
if(!($fileCSV=="")&($username==""|| $hostName=="")){
    echo "The parameters Username, Password and Host are required.\n";
    displayOptions();
    die();
}

//if username, host and password is present, make connection to database
//assuming that the user have users database
$connection = "";
$dbName = "users";
//assuming that the default password is empty
if(!($username==""|| $hostName=="")){
    $connection = mysqli_connect($hostName, $username, $password, $dbName);
    // Check connection
    if (!$connection) {
        echo(" Connection failed: ". mysqli_connect_errno() . "\n");
        die();
    };
}

//if the user does not select dry run
if(!$dryRun){
    //Drop the table if similar name table is there in database
    $sql ="DROP TABLE IF EXISTS users.users";
    if(!$connection->query($sql)){
        echo "Error: " . $connection->error . "\n";
        die();
    };
    // Create table users with the attribute to store the data from csv
    $sql = "CREATE TABLE if not exists users (
				name VARCHAR(255),
				surname VARCHAR(255),
				email VARCHAR(255) NOT NULL,
				UNIQUE KEY(email)
				)";

    if ($connection->query($sql)) {
        echo "Created Table Users  Successfully\n";
    } else {
        echo "Error creating table: " . $connection->error . "\n";
        die();
    }
    //if the user select create table option only
    if($createTable){
        die();
    }
}

parseCSV($fileCSV,$dryRun,$connection);

function parseCSV($fileCSV,$dryRun,$connection){
    $file = fopen($fileCSV, 'r');
    //Check if the file is valid or not
    if($file){
        //ignoring first line
        $firstLine= fgetcsv($file);

        echo "Record Details____\n\n";
        while ($row = fgetcsv($file)) {
            $name = ucfirst(strtolower(trim($row[0])));
            $surname = ucfirst(strtolower(trim($row[1])));
            $email= strtolower(trim($row[2]));

            //check if all the fields is not empty
           if(!($name==""&&$surname==""&&$email=="")){
                if (filter_var($email, FILTER_VALIDATE_EMAIL)){
                    if (!$dryRun){
                        // prepare and bind
                        $stmt = $connection->prepare("INSERT INTO users (name, surname, email) VALUES (?, ?, ?)");
                        if($stmt){
                            $stmt->bind_param("sss", $name, $surname, $email);
                            $stmt ->execute();
                        }else{
                            echo "ERROR: query prepare fail. " . $mysqli->error . "\n";
                        }
                        echo $name .", ". $surname .", ". $email . "\n";;
                    } else {
                        echo $name .", ". $surname .", ". $email . "\n";;
                    }
                } else {
                    echo "ERROR: Invalid email. Record ignored: ".$name .", ". $surname .", ". $email . "\n";
                }
            }

        }
    }

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