<?php
include '../Includes/dbcon.php';

// Basic error reporting to help debug
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Search for students
if (isset($_GET['term'])) {
    $searchTerm = $_GET['term'];
    $classFilter = isset($_GET['classId']) ? $_GET['classId'] : '';
    $classArmFilter = isset($_GET['classArmId']) ? $_GET['classArmId'] : '';
    
    // Build the query with proper error handling
    $query = "SELECT s.Id, s.admissionNumber, s.firstName, s.lastName, s.otherName, 
              c.className, ca.classArmName 
              FROM tblstudents s
              LEFT JOIN tblclass c ON s.classId = c.Id
              LEFT JOIN tblclassarms ca ON s.classArmId = ca.Id
              WHERE (s.firstName LIKE ? OR s.lastName LIKE ? OR s.admissionNumber LIKE ?)";
    
    // Parameters for prepared statement
    $params = array("%$searchTerm%", "%$searchTerm%", "%$searchTerm%");
    $types = "sss";
    
    // Add filters if provided
    if (!empty($classFilter)) {
        $query .= " AND s.classId = ?";
        $types .= "s";
        $params[] = $classFilter;
    }
    
    if (!empty($classArmFilter)) {
        $query .= " AND s.classArmId = ?";
        $types .= "s";
        $params[] = $classArmFilter;
    }
    
    $query .= " ORDER BY s.firstName ASC LIMIT 20";
    
    try {
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        
        // Bind parameters dynamically
        if (count($params) > 0) {
            // Using bind_param with argument unpacking (compatible with PHP 5.6+)
            $bindParams = array($types);
            foreach ($params as &$param) {
                $bindParams[] = &$param;
            }
            call_user_func_array(array($stmt, 'bind_param'), $bindParams);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $students = array();
        while ($row = $result->fetch_assoc()) {
            // Make sure everything is properly escaped for JSON
            $displayName = $row['firstName'] . ' ' . $row['lastName'];
            if (!empty($row['otherName'])) {
                $displayName .= ' ' . $row['otherName'];
            }
            $displayName .= ' (' . $row['admissionNumber'];
            
            if (!empty($row['className']) && !empty($row['classArmName'])) {
                $displayName .= ' - ' . $row['className'] . ' ' . $row['classArmName'];
            }
            $displayName .= ')';
            
            $students[] = array(
                'id' => $row['Id'],
                'admissionNumber' => $row['admissionNumber'],
                'firstName' => $row['firstName'],
                'lastName' => $row['lastName'],
                'otherName' => $row['otherName'] ?? '',
                'className' => $row['className'] ?? 'Unknown',
                'classArmName' => $row['classArmName'] ?? 'Unknown',
                'fullName' => $displayName,
                'displayName' => $displayName
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($students);
        
    } catch (Exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json');
        echo json_encode(array('error' => $e->getMessage()));
    }
    exit;
}

// Get classes for filtering
if (isset($_GET['getClasses'])) {
    try {
        $query = "SELECT Id, className FROM tblclass ORDER BY className";
        $result = $conn->query($query);
        
        if ($result === false) {
            throw new Exception("Error fetching classes: " . $conn->error);
        }
        
        $classes = array();
        while ($row = $result->fetch_assoc()) {
            $classes[] = array(
                'id' => $row['Id'],
                'name' => $row['className']
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($classes);
        
    } catch (Exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json');
        echo json_encode(array('error' => $e->getMessage()));
    }
    exit;
}

// Get class arms for a specific class
if (isset($_GET['getClassArms']) && !empty($_GET['classId'])) {
    try {
        $classId = $_GET['classId'];
        
        $query = "SELECT Id, classArmName FROM tblclassarms WHERE classId = ? ORDER BY classArmName";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        
        $stmt->bind_param("s", $classId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $classArms = array();
        while ($row = $result->fetch_assoc()) {
            $classArms[] = array(
                'id' => $row['Id'],
                'name' => $row['classArmName']
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($classArms);
        
    } catch (Exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json');
        echo json_encode(array('error' => $e->getMessage()));
    }
    exit;
}
?>