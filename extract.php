<?php

/**
 * Extract into a CSV file a synthesis of iOS applications published on the Apple Store.
 * @Author : Ludovic Toinel
 */

$limit = 200;

// City parameter check
$city = $_GET['search'];
if (!isset($search)){
        echo "missing search parameter";
        exit();
}

// CSV Header with UTF8 support
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
header("Content-Disposition: attachment; filename=appstore-".$city.".csv");
header("Pragma: no-cache");
header("Expires: 0");

echo "\xEF\xBB\xBF"; // UTF-8 BOM

// CSV Header
echo "id;trackCensoredName;description;genres;sellerName\n";


$offset = 0;
$id = 0;

// We extract each page of result until the result count is != to the limit
while ($offset == 0 || (isset($data) && $data->resultCount == $limit)){

        // Calling the apple Webservice
    $json = file_get_contents('https://itunes.apple.com/search?term='.$city.'&country=fr&entity=software&limit='.$limit.'&offset='.$offset);
        $data = json_decode($json);

        // Generate the CSV line
    foreach ($data->results as $app) {
                echo $id++.";";
        echo addslashes($app->trackCensoredName).';';
        echo addslashes(preg_replace("/(\r\n|\n|\r|;)/",'|', $app->description)).';';
        echo addslashes(implode(",", $app->genres)).';';
        echo addslashes($app->sellerName)."\n";
    }

    $offset += $limit;
}
