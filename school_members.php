<!DOCTYPE html>
<?php

require_once 'partials/header.php';

// Init variables
$errorMsg = "";
$schoolMembers = [];
$school = new School($connection);

if (isset($_GET['schoolID'])) {
    $school->schoolID = $_GET['schoolID'];
    if (!$school->read()) {
        $errorMsg .= "Unable to retrieve school data.<br>";
    }
    
    // All school members array
    $schoolMembers = $school->retrieveMembers();
    if ($schoolMembers === FALSE) {
        $errorMsg .= "Unable to retrieve school members.<br>";
    }
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <header>
            <h1>ToucanTech</h1>
        </header>
        <nav>
            <a href="index.php">Members</a> |
            <a href="schools.php">Schools</a>
        </nav>
        <section>
<?php
echo "<p>$errorMsg</p>";
echo "<h2>$school->name</h2>";
echo "<h3>School members:</h3>";

// Display list if school members array is not empty
if ($schoolMembers) {
    echo "<ul>";
    for ($i = 0; $i < count($schoolMembers); $i++) {
        echo "<li>" . $schoolMembers[$i]["Name"] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No members.</p>";
}
echo "</section>";


require_once 'partials/footer.php';