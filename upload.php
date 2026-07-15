<?php

include "dbconnect.php";

if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
    $fileTmpPath = $_FILES['csvFile']['tmp_name'];
    $fileName    = $_FILES['csvFile']['name'];

    if (!preg_match('/\.csv$/i', $fileName)) {
        echo "invalid_file_type";
        exit;
    }

    $checkQuery = "SELECT * FROM tbl_import_log WHERE file_name = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $fileName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "file_exists";
        $stmt->close();
        $conn->close();
        exit;
    }

    $handle = fopen($fileTmpPath, "r");
    if ($handle === FALSE) {
        echo "Error opening file.";
        $stmt->close();
        $conn->close();
        exit;
    }

    // The exact headers expected, in the exact order they must appear.
    // This is what actually traps "missing columns" / "columns in wrong order",
    // which a simple column-count check cannot catch.
    $EXPECTED_HEADERS = [
        "CONTROL NUMBER",
        "LAST NAME",
        "FIRST NAME",
        "MIDDLE NAME",
        "EXTENSION NAME",
        "BIRTH DAY",
        "BIRTH MONTH",
        "BIRTH YEAR",
        "SEX",
        "GCASH",
        "PCN",
        "PROVINCE",
        "CITY/MUNICIPALITY",
        "DATE LAST SERVED",
        "LAST SERVED LOCATION",
        "PROGRAM",
        "PARTNERS",
        "SDO INCHARGE",
        "OTHER REMARKS",
    ];
    $EXPECTED_COLUMN_COUNT = count($EXPECTED_HEADERS); // 19

    // Normalize a header cell for comparison: trim, collapse inner whitespace, uppercase.
    function normalizeHeader($value)
    {
        $value = trim((string)$value);
        $value = preg_replace('/\s+/', ' ', $value);
        return strtoupper($value);
    }

    $header = fgetcsv($handle);

    if ($header === FALSE || count(array_filter($header, fn($v) => trim((string)$v) !== '')) === 0) {
        fclose($handle);
        echo "invalid_format";
        $stmt->close();
        $conn->close();
        exit;
    }

    // Must have exactly the expected number of columns.
    if (count($header) !== $EXPECTED_COLUMN_COUNT) {
        fclose($handle);
        echo "invalid_format";
        $stmt->close();
        $conn->close();
        exit;
    }

    // Must match the expected header names, in the expected order.
    // Catches: missing columns, extra columns, and columns in the wrong order.
    $normalizedHeader = array_map('normalizeHeader', $header);
    foreach ($EXPECTED_HEADERS as $index => $expected) {
        if (!isset($normalizedHeader[$index]) || $normalizedHeader[$index] !== $expected) {
            fclose($handle);
            echo "invalid_format";
            $stmt->close();
            $conn->close();
            exit;
        }
    }

    $rows = [];
    $formatValid = true;

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Skip fully blank trailing lines
        if (count($data) === 1 && trim((string)$data[0]) === '') {
            continue;
        }

        // Every data row must line up exactly with the validated header structure.
        if (count($data) !== $EXPECTED_COLUMN_COUNT) {
            $formatValid = false;
            break;
        }

        // Skip rows that are effectively empty (all cells blank)
        if (count(array_filter($data, fn($v) => trim((string)$v) !== '')) === 0) {
            continue;
        }

        $rows[] = $data;
    }

    fclose($handle);

    if (!$formatValid || empty($rows)) {
        echo "invalid_format";
        $stmt->close();
        $conn->close();
        exit;
    }

    $conn->begin_transaction();

    try {
        foreach ($rows as $data) {

            $col0  = $conn->real_escape_string($data[0]  ?? ''); // control_number
            $col1  = $conn->real_escape_string($data[1]  ?? ''); // last_name
            $col2  = $conn->real_escape_string($data[2]  ?? ''); // first_name
            $col3  = $conn->real_escape_string($data[3]  ?? ''); // middle_name
            $col4  = $conn->real_escape_string($data[4]  ?? ''); // extension_name
            $col5  = $conn->real_escape_string($data[5]  ?? ''); // birth_day
            $col6  = $conn->real_escape_string($data[6]  ?? ''); // birth_month
            $col7  = $conn->real_escape_string($data[7]  ?? ''); // birth_year
            $col8  = isset($data[8])  ? $conn->real_escape_string(trim($data[8]))  : NULL; // sex
            $col9  = isset($data[9])  ? $conn->real_escape_string(trim($data[9]))  : NULL; // gcash
            $col10 = isset($data[10]) ? $conn->real_escape_string(trim($data[10])) : NULL; // pcn
            $col11 = $conn->real_escape_string($data[11] ?? ''); // province
            $col12 = $conn->real_escape_string($data[12] ?? ''); // city_municipality

            $col13raw = !empty($data[13]) ? trim($data[13]) : NULL; // date_last_served
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

            // Combined single-value columns per source file (SDO INCHARGE / OTHER REMARKS)
            $col17 = isset($data[17]) ? $conn->real_escape_string(trim($data[17])) : NULL; // sdo (= "SDO INCHARGE" combined value)
            $col18 = isset($data[18]) ? $conn->real_escape_string(trim($data[18])) : NULL; // other (= "OTHER REMARKS" combined value)

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
                         NULL,
                         " . ($col18 !== NULL ? "'$col18'" : "NULL") . ",
                         NULL)";

            if (!$conn->query($sql)) {
                throw new Exception($conn->error);
            }
        }

        // Log the uploaded file
        $logQuery = "INSERT INTO tbl_import_log (file_name, import_date) VALUES (?, NOW())";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bind_param("s", $fileName);
        $logStmt->execute();
        $logStmt->close();

        $conn->commit();
        echo "success";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error importing data.";
    }

    $stmt->close();
} else {
    echo "Error uploading file.";
}

$conn->close();
?>