<?php
date_default_timezone_set('Europe/Prague');
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once 'config.php';

// Cache for municipality data to avoid repeated queries
$municipalityCache = [];

/**
 * Get municipality original name with caching
 */
function getMunicipalityOrig($kod_obce, $link) {
    global $municipalityCache;
    
    if (!isset($municipalityCache[$kod_obce])) {
        $query = "SELECT orig FROM obce WHERE kod = ?";
        $stmt = mysqli_prepare($link, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $kod_obce);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_row($result);
            $municipalityCache[$kod_obce] = $row ? $row[0] : '';
            mysqli_stmt_close($stmt);
        } else {
            $municipalityCache[$kod_obce] = '';
        }
    }
    
    return $municipalityCache[$kod_obce];
}

/**
 * Format address components
 */
function formatAddress($prijmeni, $nazev_ulice, $cislo_popisne, $cislo_orientacni, $nazev_obce, $nazev_casti_obce) {
    $domovni = ($cislo_orientacni != "") ? "{$cislo_popisne}/{$cislo_orientacni}" : $cislo_popisne;
    $mesto = ($nazev_obce == $nazev_casti_obce) ? $nazev_obce : "{$nazev_obce}-{$nazev_casti_obce}";
    return [$domovni, $mesto];
}

/**
 * Format phone number display with underscores and spaces every 3 characters
 */
function formatPhoneDisplay($phone, $level) {
    // Create the full string: phone + underscores
    $underscores = str_repeat('_', 9 - $level);
    $fullString = $phone . $underscores;
    
    // Add spaces every 3 characters in the entire string
    $formatted = '';
    for ($i = 0; $i < strlen($fullString); $i++) {
        if ($i > 0 && $i % 3 == 0) {
            $formatted .= ' ';
        }
        $formatted .= $fullString[$i];
    }
    
    return $formatted;
}

/**
 * Group records by address and find consecutive ranges
 */
function groupRecordsByAddress($records) {
    $grouped = [];
    foreach ($records as $record) {
        // Create address key for grouping
        $addressKey = $record['nazev_ulice'] . '|' . $record['cislo_popisne'] . '|' . 
                     $record['cislo_orientacni'] . '|' . $record['nazev_obce'] . '|' . 
                     $record['nazev_casti_obce'] . '|' . $record['kod_obce'];
        
        if (!isset($grouped[$addressKey])) {
            $grouped[$addressKey] = [];
        }
        $grouped[$addressKey][] = $record;
    }
    
    // Process each address group to find consecutive ranges
    $result = [];
    foreach ($grouped as $addressKey => $addressRecords) {
        // Sort by phone number (numerically)
        usort($addressRecords, function($a, $b) {
            return intval($a['tel_cislo']) - intval($b['tel_cislo']);
        });
        
        $ranges = [];
        $currentRange = [$addressRecords[0]];
        
        for ($i = 1; $i < count($addressRecords); $i++) {
            $prevPhone = $addressRecords[$i-1]['tel_cislo'];
            $currentPhone = $addressRecords[$i]['tel_cislo'];
            
            // Debug: show phone numbers being compared
            echo "<!-- DEBUG: Comparing $prevPhone and $currentPhone -->\n";
            
            // Check if numbers are consecutive
            if (isConsecutivePhoneNumbers($prevPhone, $currentPhone)) {
                echo "<!-- DEBUG: Consecutive! -->\n";
                $currentRange[] = $addressRecords[$i];
            } else {
                echo "<!-- DEBUG: Not consecutive, starting new range -->\n";
                // End current range and start new one
                $ranges[] = $currentRange;
                $currentRange = [$addressRecords[$i]];
            }
        }
        $ranges[] = $currentRange; // Add the last range
        
        $result[$addressKey] = $ranges;
    }
    
    return $result;
}

/**
 * Check if two phone numbers are consecutive
 */
function isConsecutivePhoneNumbers($phone1, $phone2) {
    // Convert to integers and check if difference is 1
    $num1 = intval($phone1);
    $num2 = intval($phone2);
    return ($num2 - $num1) == 1;
}

/**
 * Create range display for consecutive phone numbers
 */
function createRangeDisplay($records) {
    if (count($records) == 1) {
        $phone = $records[0]['tel_cislo'];
        return formatPhoneDisplay($phone, strlen($phone));
    }
    
    // Sort records by phone number
    usort($records, function($a, $b) {
        return strcmp($a['tel_cislo'], $b['tel_cislo']);
    });
    
    // Find common prefix and create range
    $firstPhone = $records[0]['tel_cislo'];
    $lastPhone = $records[count($records) - 1]['tel_cislo'];
    
    // Find the position where they differ
    $commonLength = 0;
    $minLength = min(strlen($firstPhone), strlen($lastPhone));
    
    for ($i = 0; $i < $minLength; $i++) {
        if ($firstPhone[$i] == $lastPhone[$i]) {
            $commonLength++;
        } else {
            break;
        }
    }
    
    // Create range display
    $commonPrefix = substr($firstPhone, 0, $commonLength);
    $remainingLength = 9 - $commonLength;
    $underscores = str_repeat('_', $remainingLength);
    $fullString = $commonPrefix . $underscores;
    
    // Add spaces every 3 characters
    $formatted = '';
    for ($i = 0; $i < strlen($fullString); $i++) {
        if ($i > 0 && $i % 3 == 0) {
            $formatted .= ' ';
        }
        $formatted .= $fullString[$i];
    }
    
    return $formatted;
}

/**
 * Display phone range data with address grouping and consecutive ranges
 */
function displayPhoneData($phone, $records, $link) {
    // Debug: show what records we're processing
    echo "<!-- DEBUG: Processing " . count($records) . " records for prefix $phone -->\n";
    
    $groupedRecords = groupRecordsByAddress($records);
    
    // Debug: show grouping results
    echo "<!-- DEBUG: Found " . count($groupedRecords) . " address groups -->\n";
    
    foreach ($groupedRecords as $addressKey => $ranges) {
        echo "<!-- DEBUG: Address group has " . count($ranges) . " ranges -->\n";
        
        foreach ($ranges as $rangeRecords) {
            echo "<!-- DEBUG: Range has " . count($rangeRecords) . " records -->\n";
            
            // Get address info from first record in range
            $firstRecord = $rangeRecords[0];
            $prijmeni = $firstRecord['prijmeni'];
            $nazev_ulice = $firstRecord['nazev_ulice'];
            $cislo_popisne = $firstRecord['cislo_popisne'];
            $cislo_orientacni = $firstRecord['cislo_orientacni'];
            $nazev_obce = $firstRecord['nazev_obce'];
            $nazev_casti_obce = $firstRecord['nazev_casti_obce'];
            $kod_obce = $firstRecord['kod_obce'];
            $OpID = $firstRecord['OpID'];

            if ($OpID == "0") {
                $OpID = "777";
            }

            $orig = getMunicipalityOrig($kod_obce, $link);
            [$domovni, $mesto] = formatAddress($prijmeni, $nazev_ulice, $cislo_popisne, $cislo_orientacni, $nazev_obce, $nazev_casti_obce);
            
            // Create range display
            $display = createRangeDisplay($rangeRecords);
            
            if (count($rangeRecords) == 1) {
                // Single phone number
                echo "{$display} = ({$orig}) [{$OpID}] {$prijmeni}, {$nazev_ulice} {$domovni}, {$mesto}<br/>";
            } else {
                // Range of consecutive phone numbers
                $count = count($rangeRecords);
                echo "{$display} = ({$orig}) [{$OpID}] {$prijmeni}, {$nazev_ulice} {$domovni}, {$mesto}";
                echo " <span style='color: #666;'>({$count} čísel)</span><br/>";
            }
        }
    }
}

/**
 * Recursively display phone ranges
 */
function displayPhoneRanges($prefix = '', $level = 0, $data, $link) {
    if ($level >= 9) return;
    
    // Group data by next digit
    $currentLevel = [];
    foreach ($data as $record) {
        if (strpos($record['tel_cislo'], $prefix) === 0) {
            $nextDigit = substr($record['tel_cislo'], strlen($prefix), 1);
            if ($nextDigit !== false && is_numeric($nextDigit)) {
                // Only exclude if this is the first digit and it's 0 or 1
                if (strlen($prefix) == 0 && ($nextDigit == '0' || $nextDigit == '1')) {
                    continue; // Skip numbers starting with 0 or 1
                }
                $currentLevel[$nextDigit][] = $record;
            }
        }
    }
    
    // Process each digit 0-9
    for ($i = 0; $i < 10; $i++) {
        // Skip if this is the first digit and it's 0 or 1
        if (strlen($prefix) == 0 && ($i == 0 || $i == 1)) {
            continue;
        }
        
        $currentPrefix = $prefix . $i;
        
        if (isset($currentLevel[$i])) {
            // Get all records with this prefix (exact matches and longer numbers)
            $allRecordsWithPrefix = array_filter($currentLevel[$i], function($record) use ($currentPrefix) {
                return strpos($record['tel_cislo'], $currentPrefix) === 0;
            });
            
            if (!empty($allRecordsWithPrefix)) {
                // Check if there are longer numbers with this prefix
                $hasLongerNumbers = false;
                foreach ($allRecordsWithPrefix as $record) {
                    if (strlen($record['tel_cislo']) > strlen($currentPrefix)) {
                        $hasLongerNumbers = true;
                        break;
                    }
                }
                
                if ($hasLongerNumbers) {
                    // Check for exact matches at this level
                    $exactMatches = array_filter($allRecordsWithPrefix, function($record) use ($currentPrefix) {
                        return strlen($record['tel_cislo']) == strlen($currentPrefix);
                    });
                    
                    if (!empty($exactMatches)) {
                        // Display exact matches
                        displayPhoneData($currentPrefix, $exactMatches, $link);
                    }
                    
                    // Recursively process next level for longer numbers
                    $longerNumbers = array_filter($allRecordsWithPrefix, function($record) use ($currentPrefix) {
                        return strlen($record['tel_cislo']) > strlen($currentPrefix);
                    });
                    
                    if (!empty($longerNumbers)) {
                        displayPhoneRanges($currentPrefix, $level + 1, $longerNumbers, $link);
                    }
                } else {
                    // No longer numbers, group and display all records with this prefix
                    displayPhoneData($currentPrefix, $allRecordsWithPrefix, $link);
                }
            }
        } else {
            // No data for this prefix, display empty range
            $display = formatPhoneDisplay($currentPrefix, strlen($currentPrefix));
            echo "{$display}<br/>";
        }
    }
}

/**
 * Fetch all phone data with single optimized query
 */
function fetchAllPhoneData($link) {
    $query = "
        SELECT 
            tel_cislo,
            prijmeni, nazev_ulice, cislo_popisne, cislo_orientacni, 
            nazev_obce, nazev_casti_obce, kod_obce, OpID
        FROM (
            SELECT tel_cislo, prijmeni, nazev_ulice, cislo_popisne, 
                   cislo_orientacni, nazev_obce, nazev_casti_obce, 
                   kod_obce, OpID
            FROM stanice 
            UNION ALL
            SELECT tel_cislo, typ as prijmeni, silnice as nazev_ulice, 
                   kilometr as cislo_popisne, smer as cislo_orientacni, 
                   obecNazev as nazev_obce, castObceNazev as nazev_casti_obce, 
                   obecKod as kod_obce, archiv as OpID
            FROM hlasky 
            WHERE archiv = '0'
        ) t
        ORDER BY tel_cislo
    ";
    
    $result = mysqli_query($link, $query);
    if (!$result) {
        die("Query failed: " . mysqli_error($link));
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    mysqli_free_result($result);
    return $data;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>Rozsahy INFO35</title>
    <style>
        body {
            font-family: monospace;
        }
    </style>
</head>

<body>
    <?php
    PageHeader();

    try {
        // Fetch all data with single query
        $allData = fetchAllPhoneData($link);
        
        // Display phone ranges recursively
        displayPhoneRanges('', 0, $allData, $link);
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        mysqli_close($link);
    }
    ?>
