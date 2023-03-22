<?php

$databaseName = 'diplomski_radovi';

$backupDirectoryPath = "/home/npavkovic/PhpstormProjects/LV2/$databaseName";

$time = time();
$date = date('d-m-y h:i:s');

$databaseConnection = @mysqli_connect('localhost:3306', 'root', 'admin', $databaseName)
    OR die("<p>Error connecting to database</p>");


$showTablesResult = mysqli_query($databaseConnection, 'SHOW TABLES');


if(!is_dir($backupDirectoryPath)) {
    if(!@mkdir($backupDirectoryPath)) {
        die("<p>Error creating directory</p>");
    }
}


if(mysqli_num_rows($showTablesResult) > 0) {

    while (list($table) = mysqli_fetch_array($showTablesResult, MYSQLI_NUM)) {
        $query = "SELECT * FROM $table";

        $selectAllResult = mysqli_query($databaseConnection, $query);

        $columns = $selectAllResult->fetch_fields();

        if(mysqli_num_rows($selectAllResult) > 0) {
            if($fp = fopen ("$backupDirectoryPath/{$table}_{$date}.txt", 'w9')) {
                while ($row = mysqli_fetch_array($selectAllResult, MYSQLI_NUM)) {

                    fwrite($fp, "INSERT INTO $databaseName (");

                    foreach($columns as $column) {
                        fwrite($fp, "$column->name");

                        if($column != end($columns)) {
                            fwrite($fp, ", ");
                        }
                    }

                    fwrite($fp, ")\r\nVALUES (");

                    foreach ($row as $value) {
                        $value = addslashes($value);
                        fwrite ($fp, "'$value'");
                        if($value != end($row)) {
                            fwrite($fp, ", ");
                        } else {
                            fwrite($fp, ")\";");
                        }
                    }

                    fwrite ($fp, "\r\n");

                }

                fclose($fp);

                echo "<p>Created $table backup</p>";

                if($fp2 = gzopen("$backupDirectoryPath/{$table}_{$date}.sql.gz", 'w9')) {


                    gzwrite($fp2, file_get_contents("$backupDirectoryPath/{$table}_{$date}.txt"));
                    gzclose($fp2);

                } else {
                    echo "<p>File $backupDirectoryPath/{$table}_{$date}.sql.gz can't be opened</p>";
                    break;
                }
            } else {
                echo "<p>File $backupDirectoryPath/{$table}_{$date}.txt can't be opened</p>";
                break;
            }
        }
    }
} else {

    echo "<p>No tables found in database</p>";

}

?>