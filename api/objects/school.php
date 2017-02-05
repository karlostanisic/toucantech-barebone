<?php

// School class. No need for full CRUD. Easliy added if needed
class School{ 
    
    private $conn; 
    
    public $schoolID; 
    public $name;
 
    // Simple constructor, just DB connection
    public function __construct($db){ 
        $this->conn = $db;
    }
    
    // Retrieve school data given school ID
    public function read() {
        // Prepared statement for ID select
        $stmt = $this->conn->prepare("SELECT Name FROM schools WHERE SchoolID = ?");
        $stmt->bind_param("i", $this->schoolID);
        
        // Execute statement and simple error handling
        try {
            $stmt->execute();
        } catch (Exception $ex) {
            $stmt->close();
            return FALSE;
        }
        
        // Assign record fields to class properties
        $stmt->bind_result(
                $this->name
        );
        
        // Try to fetch record. If not, result is empty i.e. select did not work.
        if ($stmt->fetch()) {
            $stmt->close();
            return TRUE;
        } else {
            $stmt->close();
            return FALSE;
        } 
    }
    
    // Retrieve all members for given school object
    public function retrieveMembers() {
        
        // Prepared statement for selecting members records
        $stmt = $this->conn->prepare("SELECT members.MemberID, members.Name, members.EmailAddress "
            . "FROM member_schools INNER JOIN members ON member_schools.MemberID = members.MemberID "
            . "WHERE member_schools.SchoolID = ? "
            . "ORDER BY members.Name");
        $stmt->bind_param("i", $this->schoolID);
        
        // Execute statement and simple error handling
        try {
            $stmt->execute();
        } catch (Exception $ex) {
            $stmt->close();
            return FALSE;
        }
        
        // Initialise array to return
        $res = [];
        
        // Push member record to resulting array
        $result = $stmt->get_result();
        while($data = $result->fetch_assoc()) {
            $res[] = $data;
        }
        $stmt->close();
        
        // Return array of associated arrays of member's data
        return $res;
    }
    
    // Retrieve all schools. This could've been done as static function too.
    public function readAll() {
        
        // Prepared statement for selecting all schools from DB
        $stmt = $this->conn->prepare("SELECT SchoolID, Name FROM schools ORDER BY Name");
        
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