<?php 
session_start();
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE CLASS--------------------------------------------------
if(isset($_POST['saveClass'])){
    $className = $_POST['className'];
    
    $query = mysqli_query($conn, "SELECT * FROM tblclass WHERE className ='$className'");
    $ret = mysqli_fetch_array($query);

    if ($ret > 0) { 
        $statusMsg = "<div class='alert alert-danger' role='alert'>This Class Already Exists!</div>";
    } else {
        $query = mysqli_query($conn, "INSERT INTO tblclass(className) VALUES('$className')");

        if ($query) {
            $statusMsg = "<div class='alert alert-success' role='alert'>Class Created Successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger' role='alert'>An error Occurred: " . mysqli_error($conn) . "</div>";
        }
    }
}

//------------------------EDIT CLASS--------------------------------------------------
if (isset($_GET['classId']) && isset($_GET['action']) && $_GET['action'] == "editClass") {
    $Id = $_GET['classId'];

    $query = mysqli_query($conn, "SELECT * FROM tblclass WHERE Id ='$Id'");
    $row = mysqli_fetch_array($query);

    if (isset($_POST['updateClass'])) {
        $className = $_POST['className'];
        
        $query = mysqli_query($conn, "UPDATE tblclass SET className='$className' WHERE Id='$Id'");

        if ($query) {
            $statusMsg = "<div class='alert alert-success' role='alert'>Class Updated Successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger' role='alert'>An error Occurred: " . mysqli_error($conn) . "</div>";
        }
    }
}

//------------------------DELETE CLASS--------------------------------------------------
if (isset($_GET['classId']) && isset($_GET['action']) && $_GET['action'] == "deleteClass") {
    $Id = $_GET['classId'];

    $query = mysqli_query($conn, "DELETE FROM tblclass WHERE Id='$Id'");

    if ($query) {
        $statusMsg = "<div class='alert alert-success' role='alert'>Class Deleted Successfully!</div>";
    } else {
        $statusMsg = "<div class='alert alert-danger' role='alert'>An error Occurred: " . mysqli_error($conn) . "</div>";
    }
}

//------------------------SAVE CLASS ARM--------------------------------------------------
if(isset($_POST['saveArm'])){
    
    $classId = $_POST['classId'];
    $classArmName = $_POST['classArmName'];
    $teacherId = !empty($_POST['teacherId']) ? $_POST['teacherId'] : null; // Added teacher ID
   
    $query = mysqli_query($conn, "SELECT * FROM tblclassarms WHERE classArmName ='$classArmName' AND classId = '$classId'");
    $ret = mysqli_fetch_array($query);

    if ($ret > 0) { 
        $statusMsg = "<div class='alert alert-danger' role='alert'>This Class Section Already Exists!</div>";
    } else {
        // Check if teacher is already assigned
        if(!empty($teacherId)) {
            $checkTeacher = mysqli_query($conn, "SELECT * FROM tblclassarms WHERE teacherId='$teacherId' AND isAssigned='1'");
            if(mysqli_num_rows($checkTeacher) > 0) {
                $statusMsg = "<div class='alert alert-danger' role='alert'>This teacher is already assigned to another class section!</div>";
                goto skipProcessing;
            }
        }
        
        // Set isAssigned based on whether a teacher was selected
        $isAssigned = !empty($teacherId) ? '1' : '0';
        
        if (!empty($teacherId)) {
            // Insert with teacher assigned
            $query = mysqli_query($conn, "INSERT INTO tblclassarms(classId, classArmName, teacherId, isAssigned) 
                                         VALUES('$classId', '$classArmName', '$teacherId', '1')");
            
            // For debugging
            if (!$query) {
                $statusMsg = "<div class='alert alert-danger' role='alert'>Error: " . mysqli_error($conn) . "</div>";
                goto skipProcessing;
            }
            
            // Get the last inserted ID
            $lastId = mysqli_insert_id($conn);
            
            // Also update the tblclassteacher table for backward compatibility
            $updateTeacher = mysqli_query($conn, "UPDATE tblclassteacher SET classId='$classId', classArmId='$lastId' 
                             WHERE Id='$teacherId'");
                             
            // For debugging
            if (!$updateTeacher) {
                $statusMsg = "<div class='alert alert-danger' role='alert'>Error updating teacher: " . mysqli_error($conn) . "</div>";
                goto skipProcessing;
            }
        } else {
            // Insert without teacher
            $query = mysqli_query($conn, "INSERT INTO tblclassarms(classId, classArmName, isAssigned) 
                                         VALUES('$classId', '$classArmName', '0')");
                                         
            // For debugging
            if (!$query) {
                $statusMsg = "<div class='alert alert-danger' role='alert'>Error: " . mysqli_error($conn) . "</div>";
                goto skipProcessing;
            }
        }

        if ($query) {
            $statusMsg = "<div class='alert alert-success' role='alert'>Class Section Created Successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger' role='alert'>An error Occurred: " . mysqli_error($conn) . "</div>";
        }
    }
    skipProcessing:
}

//--------------------EDIT ARM------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = $_GET['Id'];

    $query = mysqli_query($conn, "SELECT * FROM tblclassarms WHERE Id ='$Id'");
    $row = mysqli_fetch_array($query);

    //------------UPDATE ARM-----------------------------
    if (isset($_POST['update'])) {
        $classId = $_POST['classId'];
        $classArmName = $_POST['classArmName'];
        $teacherId = !empty($_POST['teacherId']) ? $_POST['teacherId'] : null; // Added teacher ID
        
        // Check if teacher is already assigned (but skip checking the current record)
        if(!empty($teacherId)) {
            $checkTeacher = mysqli_query($conn, "SELECT * FROM tblclassarms WHERE teacherId='$teacherId' AND isAssigned='1' AND Id != '$Id'");
            if(mysqli_num_rows($checkTeacher) > 0) {
                $statusMsg = "<div class='alert alert-danger' role='alert'>This teacher is already assigned to another class section!</div>";
                goto skipUpdate;
            }
        }
        
        // If removing a teacher assignment
        if(empty($teacherId) && $row['isAssigned'] == '1') {
            $query = mysqli_query($conn, "UPDATE tblclassarms SET classId = '$classId', classArmName='$classArmName', 
                                         teacherId=NULL, isAssigned='0' WHERE Id='$Id'");
                                         
            // Also remove assignment from tblclassteacher for backward compatibility
            if(isset($row['teacherId'])) {
                mysqli_query($conn, "UPDATE tblclassteacher SET classId=NULL, classArmId=NULL 
                                    WHERE Id='".$row['teacherId']."'");
            }
        } else if(!empty($teacherId)) {
            // Assigning or changing teacher
            $query = mysqli_query($conn, "UPDATE tblclassarms SET classId = '$classId', classArmName='$classArmName', 
                                         teacherId='$teacherId', isAssigned='1' WHERE Id='$Id'");
                                         
            // Also update tblclassteacher for backward compatibility
            mysqli_query($conn, "UPDATE tblclassteacher SET classId='$classId', classArmId='$Id' 
                                WHERE Id='$teacherId'");
        } else {
            // Just updating class/arm info, no teacher changes
            $query = mysqli_query($conn, "UPDATE tblclassarms SET classId = '$classId', classArmName='$classArmName' WHERE Id='$Id'");
        }

        if ($query) {
            $statusMsg = "<div class='alert alert-success' role='alert'>Class Section Updated Successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger' role='alert'>An error Occurred: " . mysqli_error($conn) . "</div>";
        }
    }
    skipUpdate:
}

//--------------------------------DELETE ARM------------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = $_GET['Id'];

    $query = mysqli_query($conn, "DELETE FROM tblclassarms WHERE Id='$Id'");

    if ($query) {
        $statusMsg = "<div class='alert alert-success' role='alert'>Class Section Deleted Successfully!</div>";
    } else {
        $statusMsg = "<div class='alert alert-danger' role='alert'>An error Occurred: " . mysqli_error($conn) . "</div>";
    }
}

// Unassign teacher
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "unassign") {
    $Id = $_GET['Id'];
    
    // Get the teacher ID before removing the assignment
    $getTeacherQuery = mysqli_query($conn, "SELECT teacherId FROM tblclassarms WHERE Id='$Id'");
    $teacherData = mysqli_fetch_assoc($getTeacherQuery);
    $teacherId = $teacherData['teacherId'] ?? null;
    
    // Update the class arm to remove teacher assignment
    $query = mysqli_query($conn, "UPDATE tblclassarms SET teacherId=NULL, isAssigned='0' WHERE Id='$Id'");
    
    // Also update the tblclassteacher record for backward compatibility
    if($teacherId) {
        mysqli_query($conn, "UPDATE tblclassteacher SET classId=NULL, classArmId=NULL WHERE Id='$teacherId'");
    }

    if ($query) {
        $statusMsg = "<div class='alert alert-success' role='alert'>Teacher Unassigned Successfully!</div>";
    } else {
        $statusMsg = "<div class='alert alert-danger' role='alert'>An error Occurred: " . mysqli_error($conn) . "</div>";
    }
}

// Get active school year
$querySession = mysqli_query($conn, "SELECT * FROM tblsessionterm WHERE isActive = 1");
$activeSession = mysqli_fetch_array($querySession);
$activeSessionId = $activeSession ? $activeSession['Id'] : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
<?php include 'includes/title.php';?>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
      <?php include "Includes/sidebar.php";?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
       <?php include "Includes/topbar.php";?>
        <!-- Topbar -->

        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Manage Classes & Sections</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Manage Classes & Sections</li>
            </ol>
          </div>

          <!-- Display Active School Year -->
          <?php if ($activeSession) { ?>
          <div class="alert alert-info mb-4">
            <strong>Current Active School Year:</strong> <?php echo $activeSession['sessionName']; ?>
          </div>
          <?php } else { ?>
          <div class="alert alert-warning mb-4">
            <strong>Warning:</strong> No active school year set! Please <a href="createSessionTerm.php" class="alert-link">activate a school year</a> before creating classes.
          </div>
          <?php } ?>

          <div class="row">
            <!-- Class Management Section -->
            <div class="col-lg-12">
              <!-- Form Basic -->

              <!-- Class Arm Management Section -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Create/Edit Class Section</h6>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                        <div class="col-xl-4">
                        <label class="form-control-label">Select Grade Level<span class="text-danger ml-2">*</span></label>
                         <?php
                        $qry= "SELECT * FROM tblclass ORDER BY className ASC";
                        $result = $conn->query($qry);
                        $num = $result->num_rows;		
                        if ($num > 0){
                          echo ' <select required name="classId" class="form-control mb-3">';
                          echo'<option value="">--Select Grade Level--</option>';
                          while ($rows = $result->fetch_assoc()){
                          echo'<option value="'.$rows['Id'].'" '.($row && $row['classId'] == $rows['Id'] ? 'selected' : '').'>'.$rows['className'].'</option>';
                              }
                                  echo '</select>';
                              }
                            ?>  
                        </div>
                        <div class="col-xl-4">
                        <label class="form-control-label">Class Section Name<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" name="classArmName" value="<?php echo isset($row['classArmName']) ? $row['classArmName'] : '';?>" placeholder="Class Section Name" required>
                        </div>
                        <div class="col-xl-4">
                        <label class="form-control-label">Assign Teacher<span class="text-danger ml-2"></span></label>
                        <?php
                        $teacherQuery = "SELECT * FROM tblclassteacher ORDER BY firstName, lastName ASC";
                        $teacherResult = $conn->query($teacherQuery);
                        $teacherCount = $teacherResult->num_rows;		
                        if ($teacherCount > 0){
                          echo '<select name="teacherId" class="form-control mb-3">';
                          echo '<option value="">--Select Teacher--</option>';
                          while ($teacher = $teacherResult->fetch_assoc()){
                            $selected = ($row && isset($row['teacherId']) && $row['teacherId'] == $teacher['Id']) ? 'selected' : '';
                            echo '<option value="'.$teacher['Id'].'" '.$selected.'>'.$teacher['firstName'].' '.$teacher['lastName'].'</option>';
                          }
                          echo '</select>';
                        } else {
                          echo '<div class="alert alert-warning">No teachers available. <a href="createClassTeacher.php">Create a teacher</a> first.</div>';
                        }
                        ?>
                        </div>
                    </div>
                    <?php if (isset($_GET['Id'])) { ?>
                      <button type="submit" name="update" class="btn btn-warning">Update</button>
                      <?php if(isset($row['teacherId']) && !empty($row['teacherId'])) { ?>
                      <a href="?action=unassign&Id=<?php echo $row['Id']; ?>" class="btn btn-danger">Unassign Teacher</a>
                      <?php } ?>
                    <?php } else { ?>
                      <button type="submit" name="saveArm" class="btn btn-primary">Save</button>
                    <?php } ?>
                  </form>
                </div>
              </div>

              <!-- Class Arms List -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Class Sections</h6>
                  <?php if ($activeSession) { ?>
                    <span class="badge badge-success">School Year: <?php echo $activeSession['sessionName']; ?></span>
                  <?php } ?>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush" id="dataTableArms">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>Grade Level</th>
                        <th>Section</th>
                        <th>Teacher</th>
                        <th>Status</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "SELECT ca.Id, ca.isAssigned, c.className, ca.classArmName, 
                                 t.firstName, t.lastName, t.Id as teacherId
                               FROM tblclassarms ca
                               INNER JOIN tblclass c ON c.Id = ca.classId
                               LEFT JOIN tblclassteacher t ON t.Id = ca.teacherId
                               ORDER BY c.className, ca.classArmName";
                      $rs = $conn->query($query);
                      $num = $rs->num_rows;
                      $sn = 0;
                      if($num > 0)
                      { 
                        while ($rows = $rs->fetch_assoc())
                          {
                          $sn = $sn + 1;
                          $teacherName = !empty($rows['firstName']) ? $rows['firstName'].' '.$rows['lastName'] : '<span class="text-danger">Not Assigned</span>';
                          $statusBadge = $rows['isAssigned'] == '1' ? 
                              '<span class="badge badge-success">Assigned</span>' : 
                              '<span class="badge badge-warning">Not Assigned</span>';
                          
                          echo"
                            <tr>
                              <td>".$sn."</td>
                              <td>".$rows['className']."</td>
                              <td>".$rows['classArmName']."</td>
                              <td>".$teacherName."</td>
                              <td>".$statusBadge."</td>
                              <td><a href='?action=edit&Id=".$rows['Id']."'><i class='fas fa-edit'></i></a></td>
                              <td><a href='?action=delete&Id=".$rows['Id']."'><i class='fas fa-trash'></i></a></td>
                            </tr>";
                          }
                      }
                      else
                      {
                           echo   
                           "<div class='alert alert-danger' role='alert'>
                            No Record Found!
                            </div>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
       <?php include "Includes/footer.php";?>
      <!-- Footer -->
    </div>
  </div>

  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
   <!-- Page level plugins -->
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <script>
    $(document).ready(function () {
      $('#dataTableArms').DataTable();
    });
  </script>
</body>

</html>