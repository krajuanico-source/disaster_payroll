<?php
//error_reporting(0);
include "dbconnect.php"; // Database connection

if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
    $fileTmpPath = $_FILES['csvFile']['tmp_name'];
    $fileName = $_FILES['csvFile']['name'];

    // Check if the file already exists in tbl_import_log
    $checkQuery = "SELECT * FROM tbl_import_log WHERE file_name = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $fileName);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "file_exists"; // Notify JavaScript
    } else {
        // Process the CSV file
        if (($handle = fopen($fileTmpPath, "r")) !== FALSE) {
            fgetcsv($handle); // Skip header row (if exists)

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $col0  = $conn->real_escape_string($data[0]);
                $col1  = $conn->real_escape_string($data[1]);
                $col2  = $conn->real_escape_string($data[2]);
                $col3  = $conn->real_escape_string($data[3]);
                $col4  = $conn->real_escape_string($data[4]);
                $col5  = $conn->real_escape_string($data[5]);
                $col6  = $conn->real_escape_string($data[6]);
                $col7  = $conn->real_escape_string($data[7]);
                $col8  = $conn->real_escape_string($data[8]);
                $col9  = $conn->real_escape_string($data[9]);

                // col10 - date_last_served (date parsing)
                $col10 = !empty($data[10]) ? trim($data[10]) : NULL;
                if (!empty($col10)) {
                    $dateFormats = [
                        "m/d/Y h:i:s A", "m/d/Y H:i:s", "m/d/Y", "d-M-y", "d/m/Y",
                        "d-m-Y", "Y-m-d", "m/d/Y g:i", "F j, Y"
                    ];

                    $date = null;
                    foreach ($dateFormats as $format) {
                        $date = DateTime::createFromFormat($format, $col10);
                        if ($date) break;
                        $date = null;
                    }

                    if (!$date) {
                        $timestamp = strtotime($col10);
                        if ($timestamp !== false) {
                            $date = new DateTime();
                            $date->setTimestamp($timestamp);
                        }
                    }

                    $col10 = $date ? $date->format("Y-m-d") : NULL;
                } else {
                    $col10 = NULL;
                }

                $col11 = $conn->real_escape_string($data[11]);
                $col12 = isset($data[12]) ? $conn->real_escape_string(trim($data[12])) : NULL; // sex
                $col13 = isset($data[13]) ? $conn->real_escape_string(trim($data[13])) : NULL; // gcash
                $col14 = isset($data[14]) ? $conn->real_escape_string(trim($data[14])) : NULL; // pcn

                $lastName  = mb_convert_encoding(str_replace(['Ã', 'Ã±'], 'Ñ', $col1), "UTF-8", "auto");
                $firstName = mb_convert_encoding(str_replace(['Ã', 'Ã±'], 'Ñ', $col2), "UTF-8", "auto");
                $midName   = mb_convert_encoding(str_replace(['Ã', 'Ã±'], 'Ñ', $col3), "UTF-8", "auto");
                $loc       = mb_convert_encoding(str_replace(['Ã', 'Ã±'], 'Ñ', $col11), "UTF-8", "auto");

                $sql = "INSERT INTO ect_served_database 
                            (control_number, last_name, first_name, middle_name, extension_name, 
                            birth_day, birth_month, birth_year, sex, gcash, pcn,
                            province, city_municipality, 
                            date_last_served, last_served_location) 
                        VALUES 
                            ('$col0', '$lastName', '$firstName', '$midName', '$col4', 
                            '$col5', '$col6', '$col7',
                            " . ($col12 ? "'$col12'" : "NULL") . ",
                            " . ($col13 ? "'$col13'" : "NULL") . ",
                            " . ($col14 ? "'$col14'" : "NULL") . ",
                            '$col8', '$col9', 
                            " . ($col10 ? "'$col10'" : "NULL") . ", '$loc')";

                $conn->query($sql);
            }

            fclose($handle);

            // Log the uploaded file in tbl_import_log
            $logQuery = "INSERT INTO tbl_import_log (file_name, import_date) VALUES (?, NOW())";
            $stmt = $conn->prepare($logQuery);
            $stmt->bind_param("s", $fileName);
            $stmt->execute();

            echo "success";
        } else {
            echo "Error opening file.";
        }
    }

    $stmt->close();
} else {
    echo "Error uploading file.";
}

$conn->close();
?>