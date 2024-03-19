<?php
// Mysql connection;
$server = "127.0.0.1:3306";
$database = "mobile";
$user = "mobile";
$password = "mobile";
$mysqli = new mysqli($server, $user, $password, $database);

// Filling the array of manufacturer IDs and titles
$manufacturers = array();

$manufacturer_handle = $mysqli->query("select id, title from manufacturers order by title");

while ($row = $manufacturer_handle->fetch_assoc()) {
    $manufacturers [$row["id"]] = $row["title"];
}

// Filling the array of country IDs and titles
$countries = array();

$countries_handle = $mysqli->query("select id, title from countries order by title");

while ($row = $countries_handle->fetch_assoc()) {
    $countries [$row["id"]] = $row["title"];
}

// collecting and sanitizing the current inputs from GET data
$error = "";

if (isset($_GET["manufacturer"]) && is_string($_GET["manufacturer"])) {
    $manufacturer = $_GET["manufacturer"];
} else {
    $error .= "Manufacturer parameter is missing or of wrong type. ";
}

if (isset($_GET["country"]) && is_string($_GET["country"])) {
    $country = $_GET["country"];
} else {
    $error .= "Country parameter is missing or of wrong type. ";
}

if (isset($_GET["year"]) && is_numeric($_GET["year"])) {
    $year = $_GET["year"];
} else {
    $error .= "Year parameter is missing or of wrong type. ";
}

// connecting to database, making a query, collecting results, saving it to $results array as objects
$results = array();

if (isset($_GET['manufacturer']) && isset($_GET['country']) && isset($_GET['year'])) {
    $results_handle = $mysqli->prepare("select
    manufacturers.title as manufacturer,
    models.title as model,
    colors.title as color,
    count(*) as count
    from
        manufacturers
            inner join models on manufacturer_id = manufacturers.id
            inner join cars on cars.model_id = models.id
            inner join countries on cars.source_country_id = countries.id
            inner join colors on cars.color_id = colors.id
    where
            manufacturer_id = ?
      and countries.id = ?
      and cars.registration_year = ?
    group by
        manufacturers.title,
        models.title,
        colors.title
    order by
        manufacturer,
        model,
        color,
        count desc;");

    $results_handle->bind_param("iii", $_GET['manufacturer'], $_GET['country'], $_GET['year']);

    $results_handle->execute();

    $results_handle = $results_handle->get_result();

    while ($row = $results_handle->fetch_assoc()) {
        $results[] = $row;
    }
}

require("view.php");

require("../task2/logger.php");
$logger = new Logger("../temp/logfile.txt");
if ($error) {
    $logger->log($error);
} else {
    $logger->log("OK");
}