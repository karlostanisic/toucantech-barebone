<?php

// Members class. CRUD + school associations
class Member{ 
    
    private $conn; 
    
    public $memberID; 
    public $name; 
    public $emailAddress;
    
    // Simple constructor, just DB connection
    public function __construct($db){ 
        $this->conn = $db;
    }
    
    // Update member
    public function update() {
        // Sanitising user input
        $this->name = htmlspecialchars(strip_tags($this->name));
        if ($this->name === "") {
            return FALSE;
        }
        $this->emailAddress = htmlspecialchars(strip_tags($this->emailAddress));
        
        // Prepared statement for update
        $stmt = $this->conn->prepare("UPDATE members SET Name = ?, EmailAddress = ? WHERE MemberID = ?");
        $stmt->bind_param("ssi", $this->name, $this->emailAddress, $this->memberID);
        
        // Execute statement and simple error handling
        try {
            $stmt->execute();
        } catch (Exception $ex) {
            $stmt->close();
            return FALSE;
        }
        $stmt->close();
        return TRUE;
        
    }
    
    // Delete member
    public function delete() {
        // Prepared statement for delete
        $stmt = $this->conn->prepare("DELETE FROM members WHERE MemberID = ?");
        $stmt->bind_param("i", $this->memberID);
        
        // Execute statement and simple error handling
        try {
            $stmt->execute();
        } catch (Exception $ex) {
            $stmt->close();
            return FALSE;
        }
        $stmt->close();
        
        // Prepared statement for member_schools house cleaning 
        // Tables use MyISAM engine so referential integrity is handled in code
        $stmt = $this->conn->prepare("DELETE FROM member_schools WHERE MemberID = ?");
        $stmt->bind_param("i", $this->memberID);
        
        // Execute statement and simple error handling
        try {
            $stmt->execute();
        } catch (Exception $ex) {
            $stmt->close();
            return FALSE;
        }
        $stmt->close();
        return TRUE;
    }
    
    // Create member
    public function create() {
        // Sanitising user input
        $this->name = htmlspecialchars(strip_tags($this->name));
        if ($this->name === "") {
            return FALSE;
        }
        $this->emailAddress = htmlspecialchars(strip_tags($this->emailAddress));
        
        // Prepared statement for insert
        $stmt = $this->conn->prepare("INSERT INTO members (Name, EmailAddress) VALUES (?, ?)");
        $stmt->bind_param("ss", $this->name, $this->emailAddress);
        
        // Execute statement and simple error handling
        try {
            $stmt->execute();
        } catch (Exception $ex) {
            $stmt->close();
            return FALSE;
        }
        $stmt->close();
        
        // Prepared statement for retrieving ID of inserted record
        $stmt = $this->conn->prepare("SELECT MemberID FROM members ORDER BY MemberID DESC LIMIT 1");
        try {
            $stmt->execute();
        } catch (Exception $ex) {
            $stmt->close();
            return FALSE;
        }
        
        // Assign ID to class property
        $stmt->bind_result($this->memberID);
        $stmt->fetch();
        
        $stmt->close();
        return TRUE;
        
    }
    
    // Retrieve data from DB with given MemeberID
    public function read() {
        // Prepared statement for ID select
        $stmt = $this->conn->prepare("SELECT Name, EmailAddress FROM members WHERE MemberID = ?");
        $stmt->bind_param("i", $this->memberID);
        
        // Execute statement and simple error handling
        try {
            $stmt->execute();
        } catch (Exception $ex) {
            $stmt->close();
            return FALSE;
        }
        
        // Assign record fields to class properties
        $stmt->bind_result(
                $this->name, 
                $this->emailAddress
        );
        
        // Try to fetch record. If not, select did not work.
        if ($stmt->fetch()) {
            $stmt->close();
            return TRUE;
        } else {
            $stmt->close();
            return FALSE;
        } 
    }
    
    // Retrieve all schools associated to member object
    public function retrieveSchools() {
        // Prepared statement for selecting school records
        $stmt = $this->conn->prepare("SELECT schools.SchoolID, schools.Name "
            . "FROM member_schools INNER JOIN schools ON member_schools.SchoolID = schools.SchoolID "
            . "WHERE member_schools.MemberID = ? "
            . "ORDER BY schools.Name");
        $stmt->bind_param("i", $this->memberID);
        
        // Execute statement and simple error handling
        try {
            $stmt->execute();
        } catch (Exception $ex) {
            $stmt->close();
            return FALSE;
        }
        
        // Initialise array to return
        $res = [];
        
        // Push school record to resulting array
        $result = $stmt->get_result();
        while($data = $result->fetch_assoc()) {
            $res[] = $data;
        }
        $stmt->close();
        
        // Return array of associated arrays of school's data
        return $res;
    }
    
    // Associate school with member object via school id
    public function addSchool($schoolID) {
        if ($this->memberID === "") {
            return FALSE;
        }
        
        // Prepared statement for checking if association already exists. We don't wan't double records.
        $stmt = $this->conn->prepare("SELECT ID FROM member_schools WHERE MemberID = ? AND SchoolID = ?");
        $stmt->bind_param("ii", $this->memberID, $schoolID);

        // Execute statement and simple error handling
        try {
            $stmt->execute();
        } catch (Exception $ex) {
            $stmt->close();
            return FALSE;
        }

        // If result is not empty, we already have association. Don't do anything.
        if ($stmt->fetch()) {
            $stmt->close();
            return FALSE;
        } 
        // If empty, insert new association
        else {
            $stmt->close();

            // Prepared statement for inserting new association
            $stmt = $this->conn->prepare("INSERT INTO member_schools (MemberID, SchoolID) VALUES (?, ?)");
            $stmt->bind_param("ii", $this->memberID, $schoolID);

            // Execute statement and simple error handling
            try {
                $stmt->execute();
            } catch (Exception $ex) {
                $stmt->close();
                return FALSE;
            }

            $stmt->close();
            return TRUE;
        }
        
        
    }
    
    // Remove school's association with member object via school id
    public function removeSchool($schoolID) {
        
        // Prepared statement for removing association
        $stmt = $this->conn->prepare("DELETE FROM member_schools WHERE MemberID = ? AND SchoolID = ?");
        $stmt->bind_param("ii", $this->memberID, $schoolID);
        
        // Execute statement and simple error handling
        try {
            $stmt->execute();
        } catch (Exception $ex) {
            $stmt->close();
            return FALSE;
        }
        $stmt->close();
        return TRUE;
    }
    
    // Retrieve all members. This could've been done as static function too.
    public function readAll() {
        
        // Prepared statement for selecting all members from DB
        $stmt = $this->conn->prepare("SELECT MemberID, Name, EmailAddress FROM members ORDER BY Name");
        
        // Execute statement and simple error handling
        try {
            $stmt->execute();
        } catch (Exception $ex) {
            $stmt->close();
            return FALSE;
        }
        
        // Store all results as array of associtive arrays and return
        $res = [];
        $result = $stmt->get_result();
        while($data = $result->fetch_assoc()) {
            $res[] = $data;
        }
        
        $stmt->close();
        return $res;
    }
}