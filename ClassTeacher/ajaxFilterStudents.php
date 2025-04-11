<?php
include '../Includes/dbcon.php';

$gradeLevel = isset($_GET['gradeLevel']) ? $_GET['gradeLevel'] : '';
$schoolYear = isset($_GET['schoolYear']) ? $_GET['schoolYear'] : '';

$query = "SELECT s.*, c.className, ca.classArmName, st.sessionName 
          FROM tblstudents s
          INNER JOIN tblclass c ON c.Id = s.classId
          INNER JOIN tblclassarms ca ON ca.Id = s.classArmId
          INNER JOIN tblsessionterm st ON st.Id = s.sessionTermId
          WHERE 1=1";

if (!empty($gradeLevel)) {
    $query .= " AND s.classId = '$gradeLevel'";
}

if (!empty($schoolYear)) {
    $query .= " AND s.sessionTermId = '$schoolYear'";
}

$query .= " ORDER BY s.firstName ASC";

$rs = $conn->query($query);
$num = $rs->num_rows;
$sn = 0;

if ($num > 0) {
    while ($rows = $rs->fetch_assoc()) {
        $sn++;
        echo "<tr>
            <td>".$sn."</td>
            <td>".$rows['firstName']."</td>
            <td>".$rows['lastName']."</td>
            <td>".$rows['otherName']."</td>
            <td>".$rows['admissionNumber']."</td>
            <td>".$rows['className']."</td>
            <td>".$rows['classArmName']."</td>
            <td>".$rows['sessionName']."</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center'>No Record Found!</td></tr>";
}
?> 