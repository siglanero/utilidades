<?php


/**
 * restoreDatabase
 *
 * @author Samuel Prado Almeida
 * @link GitHub: https://github.com/worldvisual
 *
 *
 * @param $hostname = 'localhost'
 * @param $username = 'root'
 * @param $pass     = ''
 * @param $database = 'myDatabase'
 * @param $sql_Dir  = 'folder/mySql.sql'
 *
 **/


function restoreDatabase($hostname, $username, $pass, $database, $sql_Dir, $op_data = ''){

    $conn = new mysqli($hostname, $username, $pass, $database);

    //Drop all tables
    $conn->query('SET foreign_key_checks = 0');
    if ($result = $conn->query("SHOW TABLES"))
    {
        while($row = $result->fetch_array(MYSQLI_NUM))
        {
            $conn->query('DROP TABLE IF EXISTS '.$row[0]);
            //echo $row[0].",\n";
            $row[0].",\n";
        }
    }
    $conn->query('SET foreign_key_checks = 1');
    //end

    //Insert $sql_Dir
    $lines = file($sql_Dir);

    foreach ($lines as $line){
        if (substr($line, 0, 2) == '--' || $line == ''){
            continue;
        }
        $op_data .= $line;
        if (substr(trim($line), -1, 1) == ';'){
            $conn->query($op_data);
            $op_data = '';
        }
    }

    $conn->close();

    return "restoration completed successfully : [$database] ";
}


$sql_Dir = 'folder/mySql.sql';

echo restoreDatabase('localhost', 'root', '', 'myDatabase', $sql_Dir);
