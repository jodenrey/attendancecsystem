<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize message variable
$statusMsg = "";

// Check if form is submitted
if (isset($_POST['changePassword'])) {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Check if new passwords match
    if ($newPassword != $confirmPassword) {
        $statusMsg = "<div class='alert alert-danger'>New passwords do not match!</div>";
    } else {
        // Get user information based on userType
        $userType = $_SESSION['userType'];
        $userId = $_SESSION['userId'];
        
        // Different tables and fields based on user type
        if ($userType == 'Teacher') {
            $query = mysqli_query($conn, "SELECT * FROM tblclassteacher WHERE Id='$userId'");
            $table = "tblclassteacher";
        } elseif ($userType == 'Student') {
            $admissionNumber = $_SESSION['admissionNumber'];
            $query = mysqli_query($conn, "SELECT * FROM tblstudents WHERE admissionNumber='$admissionNumber'");
            $table = "tblstudents";
            $idField = "admissionNumber";
            $idValue = $admissionNumber;
        } else {
            $statusMsg = "<div class='alert alert-danger'>Unknown user type!</div>";
            goto skipProcessing;
        }
        
        $row = mysqli_fetch_array($query);
        
        // For teacher, we need to compare hashed password
        if ($userType == 'Teacher') {
            $hashedCurrentPassword = md5($currentPassword);
            if ($row['password'] != $hashedCurrentPassword) {
                $statusMsg = "<div class='alert alert-danger'>Current password is incorrect!</div>";
                goto skipProcessing;
            }
            
            $hashedNewPassword = md5($newPassword);
            $updateQuery = mysqli_query($conn, "UPDATE $table SET password='$hashedNewPassword' WHERE Id='$userId'");
        } 
        // For student, we compare plain password
        else if ($userType == 'Student') {
            if ($row['password'] != $currentPassword) {
                $statusMsg = "<div class='alert alert-danger'>Current password is incorrect!</div>";
                goto skipProcessing;
            }
            
            $updateQuery = mysqli_query($conn, "UPDATE $table SET password='$newPassword' WHERE $idField='$idValue'");
        }
        
        if ($updateQuery) {
            $statusMsg = "<div class='alert alert-success'>Password changed successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger'>An error occurred: " . mysqli_error($conn) . "</div>";
        }
    }
    
    skipProcessing: // Target for goto statements
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Change Password</title>
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
            <h1 class="h3 mb-0 text-gray-800">Change Password</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Change Password</li>
            </ol>
          </div>
          
          <div class="row">
            <div class="col-lg-8">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Change Your Password</h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group">
                      <label for="currentPassword">Current Password</label>
                      <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                    </div>
                    <div class="form-group">
                      <label for="newPassword">New Password</label>
                      <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                    </div>
                    <div class="form-group">
                      <label for="confirmPassword">Confirm New Password</label>
                      <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                    </div>
                    <button type="submit" name="changePassword" class="btn btn-primary">Change Password</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          
          <!--Row-->
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
</body>

</html>