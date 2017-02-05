<!DOCTYPE html>
<?php

require_once 'partials/header.php';

if (isset($_GET['errorMsg'])) {
    $errorMsg = $_GET['errorMsg'];
} else {
    $errorMsg = "";
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>ToucanTech</title>
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
            <p><?php echo $errorMsg; ?></p>
            <h2>Members</h2>
        
<!--        Link for create new member form-->
            <a href="member_form.php?action=add"><button>New Member</button></a>
            
            <table border="1">
                <thead>
                    <tr>
                        <th>Member ID</th>
                        <th>Name</th>
                        <th>Email Address</th>
                        <th>Schools</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
<?php
$errorMsg = "";

$member = new Member($connection);

// Retrieve all members from DB
$allMembers = $member->readAll();
if ($allMembers !== FALSE) {
    
    // Loop all members
    for ($i = 0; $i < count($allMembers); $i++) {
        echo "<tr>";
            echo "<td>" . $allMembers[$i]["MemberID"] . "</td>";
            echo "<td>" . $allMembers[$i]["Name"] . "</td>";
            echo "<td>" . $allMembers[$i]["EmailAddress"] . "</td>";
            echo "<td>";
            $member->memberID =  $allMembers[$i]["MemberID"];

            // All schools associated with current member
            $memberSchools = $member->retrieveSchools();
            if ($memberSchools !== FALSE) {
                for ($j = 0; $j < count($memberSchools); $j++) {
                    // School names are gonna be links to School view page
                    echo "<a href='school_members.php?schoolID=" . $memberSchools[$j]["SchoolID"] . "'>" . $memberSchools[$j]["Name"] . "</a><br>";
                }
            } else {
                $errorMsg .= "Unable to retrieve associated schools for member (member ID = $member->memberID).<br>";
            }
            echo "</td>";

            // Links for edit / delete
            echo "<td><a href='member_form.php?action=edit&memberID=" . $allMembers[$i]["MemberID"] . "'>edit</a></td>";
            echo "<td><a href='member_form.php?action=delete&memberID=" . $allMembers[$i]["MemberID"] . "'>delete</a></td>";
        echo "</tr>";
    }
} else {
    $errorMsg .= "Unable to retrieve members.<br>";
}
echo "</tbody>";
echo "</table>";
echo "<p>$errorMsg</p>";
echo "</section>";

require_once 'partials/footer.php';