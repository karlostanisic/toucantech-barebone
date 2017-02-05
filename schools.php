<!DOCTYPE html>
<?php

require_once 'partials/header.php';

$errorMsg = "";

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
        <h2>Schools</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>School ID</th>
                    <th>Name</th>
                    <th>Members</th>
                </tr>
            </thead>
            <tbody>
<?php
$school = new School($connection);

// Fetch all schools from DB
$allSchools = $school->readAll();
if ($allSchools !== FALSE) {
    for ($i = 0; $i < count($allSchools); $i++) {
        echo "<tr>";
            echo "<td>" . $allSchools[$i]["SchoolID"] . "</td>";
            echo "<td>" . $allSchools[$i]["Name"] . "</td>";

            // Link to school members page
            echo "<td><a href='school_members.php?schoolID=" . $allSchools[$i]["SchoolID"] . "'>view members</a></td>";
        echo "</tr>";
    }
} else {
    $errorMsg .= "Unable to retrieve schools.<br>";
}
echo "</tbody>";
echo "</table>";
echo "<p>$errorMsg</p>";
echo "</section>";

require_once 'partials/footer.php';