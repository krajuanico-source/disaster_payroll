<?php

include "dbconnect.php"; 

if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
    $fileTmpPath = $_FILES['csvFile']['tmp_name'];
    $fileName    = $_FILES['csvFile']['name'];

    $checkQuery = "SELECT * FROM tbl_import_log WH ERE file_name = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $fileName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "file_exists"; 
    } else {

        if (($handle = fopen($fileTmpPath, "r")) !== FALSE) {
            fgetcsv($handle); 

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                $col0  = $conn->real_escape_string($data[0]  ?? '');
                $col1  = $conn->real_escape_string($data[1]  ?? '');
                $col2  = $conn->real_escape_string($data[2]  ?? '');
                $col3  = $conn->real_escape_string($data[3]  ?? '');
                $col4  = $conn->real_escape_string($data[4]  ?? '');
                $col5  = $conn->real_escape_string($data[5]  ?? '');
                $col6  = $conn->real_escape_string($data[6]  ?? '');
                $col7  = $conn->real_escape_string($data[7]  ?? '');
                $col8  = isset($data[8])  ? $conn->real_escape_string(trim($data[8]))  : NULL; 
                $col9  = isset($data[9])  ? $conn->real_escape_string(trim($data[9]))  : NULL; 
                $col10 = isset($data[10]) ? $conn->real_escape_string(trim($data[10])) : NULL; 
                $col11 = $conn->real_escape_string($data[11] ?? ''); 
                $col12 = $conn->real_escape_string($data[12] ?? '');

                $col13raw = !empty($data[13]) ? trim($data[13]) : NULL;
                $col13 = NULL;
                if (!empty($col13raw)) {
                    $dateFormats = [
                        "m-d-Y",     
                        "m/d/Y",     
                        "m/d/Y h:i:s A",
                        "m/d/Y H:i:s",
                        "m/d/Y g:i",
                        "Y-m-d",       
                        "d-M-y",       
                        "d-m-Y",      
                        "d/m/Y",      
                        "F j, Y"        
];
                    $date = null;
                    foreach ($dateFormats as $format) {
                        $date = DateTime::createFromFormat($format, $col13raw);
                        if ($date) break;
                        $date = null;
                    }
                    if (!$date) {
                        $timestamp = strtotime($col13raw);
                        if ($timestamp !== false) {
                            $date = new DateTime();
                            $date->setTimestamp($timestamp);
                        }
                    }
                    $col13 = $date ? $date->format("Y-m-d") : NULL;
                }

                $col14 = $conn->real_escape_string($data[14] ?? ''); // last_served_location
                $col15 = isset($data[15]) ? $conn->real_escape_string(trim($data[15])) : NULL; // program
                $col16 = isset($data[16]) ? $conn->real_escape_string(trim($data[16])) : NULL; // partners
                $col17 = isset($data[17]) ? $conn->real_escape_string(trim($data[17])) : NULL; // sdo
                $col18 = isset($data[18]) ? $conn->real_escape_string(trim($data[18])) : NULL; // incharge
                $col19 = isset($data[19]) ? $conn->real_escape_string(trim($data[19])) : NULL; // other
                $col20 = isset($data[20]) ? $conn->real_escape_string(trim($data[20])) : NULL; // remarks

                // Fix encoding for name fields and location
                $lastName  = mb_convert_encoding(str_replace(['Ã', 'Ã±'], 'Ñ', $col1),  "UTF-8", "auto");
                $firstName = mb_convert_encoding(str_replace(['Ã', 'Ã±'], 'Ñ', $col2),  "UTF-8", "auto");
                $midName   = mb_convert_encoding(str_replace(['Ã', 'Ã±'], 'Ñ', $col3),  "UTF-8", "auto");
                $loc       = mb_convert_encoding(str_replace(['Ã', 'Ã±'], 'Ñ', $col14), "UTF-8", "auto");

                $sql = "INSERT INTO ect_served_database 
                            (control_number, last_name, first_name, middle_name, extension_name,
                             birth_day, birth_month, birth_year, sex, gcash, pcn,
                             province, city_municipality,
                             date_last_served, last_served_location,
                             program, partners, sdo, incharge, other, remarks)
                        VALUES 
                            ('$col0', '$lastName', '$firstName', '$midName', '$col4',
                             '$col5', '$col6', '$col7',
                             " . ($col8  !== NULL ? "'$col8'"  : "NULL") . ",
                             " . ($col9  !== NULL ? "'$col9'"  : "NULL") . ",
                             " . ($col10 !== NULL ? "'$col10'" : "NULL") . ",
                             '$col11', '$col12',
                             " . ($col13 !== NULL ? "'$col13'" : "NULL") . ",
                             '$loc',
                             " . ($col15 !== NULL ? "'$col15'" : "NULL") . ",
                             " . ($col16 !== NULL ? "'$col16'" : "NULL") . ",
                             " . ($col17 !== NULL ? "'$col17'" : "NULL") . ",
                             " . ($col18 !== NULL ? "'$col18'" : "NULL") . ",
                             " . ($col19 !== NULL ? "'$col19'" : "NULL") . ",
                             " . ($col20 !== NULL ? "'$col20'" : "NULL") . ")";

                $conn->query($sql);
            }

            fclose($handle);

            // Log the uploaded file
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