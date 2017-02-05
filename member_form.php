<!DOCTYPE html>

<?php
require_once 'partials/header.php';

$errorMsg = "";

// Check if it is post call
if (isset($_POST['action'])) {
    
    // Store post parameters in variables
    $action = $_POST['action'];
    $memberID = $_POST['memberID'];
    $name = $_POST['name'];
    $emailAddress = $_POST['emailAddress'];
    $schoolID = $_POST['schoolID'];
    
    // This one is not present if creating new member
    if (isset($_POST['removeSchoolID'])) {
        $removeSchoolID = $_POST['removeSchoolID'];
    } else {
        $removeSchoolID = [];
    }
    
    // Same form for three different actions - ADD, DELETE and EDIT
    switch ($action) {
        case "add": 
            $member = new Member($connection);
            $member->name = $name;
            $member->emailAddress = $emailAddress;
            
            // Insert new record in DB. Data validation and input sanitization is done inside the class
            if (!$member->create()) {
                $errorMsg .= "Unable to create new member.<br>"; 
            }
            
            // Add school if selected
            if ($schoolID !== "0") {
                if(!$member->addSchool($schoolID)) {
                    $errorMsg .= "Unable to add school to member (school ID = $schoolID).<br>";
                }
            }
            break;
        case "edit":
            $member = new Member($connection);
            $member->memberID = $memberID;
            $member->name = $name;
            $member->emailAddress = $emailAddress;
            
            // Update record in DB. Data validation and input sanitization is done inside the class
            if (!$member->update()) {
                $errorMsg .= "Unable to update member.<br>";
            }
            
            // Add school if selected
            if ($schoolID !== "0") {
                if(!$member->addSchool($schoolID)) {
                    $errorMsg .= "Unable to add school to member (school ID = $schoolID).<br>";
                }
            }
            
            // Remove schools if checked
            for ($i = 0; $i < count($removeSchoolID); $i++) {
                if($member->removeSchool($removeSchoolID[$i])) {
                    $errorMsg .= "Unable to remove school from member (school ID = $removeSchoolID[$i]).<br>";
                }
            }
            break;
        case "delete": 
            $member = new Member($connection);
            $member->memberID = $memberID;
            
            // Deletes record from DB. Referential integrity is handled iside the class
            if (!$member->delete()) {
                $errorMsg .= "Unable to delete member.<br>";
            }
            break;
    }
    
    // Redirect to home page
    header('Location: index.php?errorMsg=' . $errorMsg, true, ($permanent === true) ? 301 : 302);
    die();
}

// Check if it is get request
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    // Initialise variables
    $name = $emailAddress = $memberID = "";
    $memberSchools = [];
    
    // Check if member ID is sent
    if (isset($_GET['memberID'])) {
        $memberID = $_GET['memberID'];
        $member = new Member($connection);
        $member->memberID = $memberID;
        
        // Fetch other member attributes
        if($member->read()) {
            $name = $member->name;
            $emailAddress = $member->emailAddress;
            
            // Fetch all schools member is member of :)
            $memberSchools = $member->retrieveSchools();
            if ($memberSchools === FALSE) {
                $errorMsg .= "Unable to retrieve schools for member.<br>";
                $memberSchools = [];
            }
        } else {
            $errorMsg .= "Unable to retrieve member.<br>";
        }
    } elseif ($action !== "add") {
        $errorMsg .= "Member ID is not set.<br>";
    }
    
    // Just for title and button caption
    switch ($action) {
        case "add":
            $title = "Create";
            break;
        case "edit":
            $title = "Update"; 
            break;
        case "delete": 
            $title = "Delete";
            break;
        default:
            $title = "Create";
            $action = "add";
    }
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
        <h2><?php echo "$title Member"; ?></h2>
        <form name="member" action="member_form.php" method="post">
            
            <input type='hidden' name='action' value='<?php echo $action ?>'/>
            <input type='hidden' name='memberID' value='<?php echo $memberID ?>'/>
            
            <div>
                <label for="frm-input-name">Name:</label>
                <input type="text" id="frm-input-name" name="name" value="<?php echo $name; ?>">
            </div>
            <div>
                <label for="frm-input-emailAddress">Email Addres:</label>
                <input type="text" id="frm-input-emailAddress" name="emailAddress" value="<?php echo $emailAddress; ?>">
            </div>
            <fieldset>
                <legend>Schools</legend>
                <label for="frm-input-schoolID">Add school:</label>
                <select name="schoolID" id="frm-input-schoolID">
                    <option value="0" selected="">--</option>
<?php

$school = new School($connection);

// Fetch all schools from DB and populate drop list
$allSchools = $school->readAll();
if($allSchools !== FALSE) {
    for ($i = 0; $i < count($allSchools); $i++) {
        echo "<option value='" . $allSchools[$i]["SchoolID"] . "'>" . $allSchools[$i]["Name"] . "</option>";
    }
} else {
    $errorMsg .= "Unable to retrieve schools.<br>";
}
echo "</select>";

// If member is associated to at least one school, offer options for removing association
if (count($memberSchools) > 0) {
    echo "<fieldset>";
    echo "<legend>Remove school(s)</legend>";
    
    // Array of checkboxes
    for ($i = 0; $i < count($memberSchools); $i++) {
        echo "<div>";
            echo "<input type='checkbox' name='removeSchoolID[]' id='frm-remove-school-" . $memberSchools[$i]["SchoolID"] . "' value='" . $memberSchools[$i]["SchoolID"] . "'>";
            echo "<label for='frm-remove-school-" . $memberSchools[$i]["SchoolID"] . "'>" . $memberSchools[$i]["Name"] . "</label>";
        echo "</div>";
    }
    echo "</fieldset>";
}
echo "</fieldset>";
echo "<input type='submit' value='$title Member' />";
echo "<a href='javascript:history.go(-1)'><button>Cancel</button></a>";
echo "</form>";
echo "<p>$errorMsg</p>";
echo "</section>";

require_once 'partials/footer.php';