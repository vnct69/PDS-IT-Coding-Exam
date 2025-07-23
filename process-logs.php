<?php

$inputFile = "sample-log.txt";
$outputFile = "output.txt";

// Read file lines
$lines = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$pipeLogs = [];
$idList = [];
$userIDs = [];

foreach ($lines as $line) {
    $id = trim(substr($line, 0, 12));
    $userID = trim(substr($line, 12, 6));
    $bytesTX = number_format((int)trim(substr($line, 18, 8)));
    $bytesRX = number_format((int)trim(substr($line, 26, 8)));
    $dateRaw = trim(substr($line, 34, 17));
    
    // Convert (Required format)
    $dateFormatted = date("D, F d Y, H:i:s", strtotime($dateRaw));

    // Store entries
    $pipeLogs[] = "$userID|$bytesTX|$bytesRX|$dateFormatted|$id";
    $idList[] = $id;
    $userIDs[] = $userID;
}

// Sort ID list properly
natsort($idList);
$idList = array_values($idList); // reset keys

// Get unique and sorted userIDs
$userIDs = array_unique($userIDs);
sort($userIDs);

// Write to output.txt
file_put_contents($outputFile, implode(PHP_EOL, $pipeLogs) . PHP_EOL . PHP_EOL);
file_put_contents($outputFile, implode(PHP_EOL, $idList) . PHP_EOL . PHP_EOL, FILE_APPEND);

foreach ($userIDs as $i => $uid) {
    file_put_contents($outputFile, "[" . ($i+1) . "] $uid" . PHP_EOL, FILE_APPEND);
}

echo "Output written to $outputFile\n";
