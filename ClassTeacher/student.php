
<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Dashboard</title>
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
            <h1 class="h3 mb-0 text-gray-800">Student Dashboard  </h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>

          <div class="row mb-3">
            <!-- New User Card Example -->
            <?php 
            $admissionNumber = $_SESSION['admissionNumber'];
            $query1 = mysqli_query($conn, "SELECT * FROM tblparent");
            $parent = null;

            while ($row = mysqli_fetch_assoc($query1)) {
              $students = explode(',', $row['student']);
              if (in_array($admissionNumber, $students)) {
                $parent = $row;
                break;
              }
            }

            if ($parent) {
            ?>
            <div class="col-xl-6 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Parent Name</div>
                      <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $parent['fn'] . ' ' . $parent['mn'] . ' ' . $parent['ln'];?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-user fa-2x text-info"></i>
                    </div>
                  </div>
                </div>
                </div>
              </div>
              <?php 
                  }

                  $query2 = mysqli_query($conn, "SELECT COUNT(*) as totalAttendance FROM tblattendance WHERE admissionNo = '$admissionNumber'");
                  $attendanceData = mysqli_fetch_assoc($query2);
                  $totAttendance = $attendanceData['totalAttendance'];

                  $query3 = mysqli_query($conn, "SELECT COUNT(*) as totalAbsent FROM tblattendance WHERE admissionNo = '$admissionNumber' AND status = 0");
                  $absentData = mysqli_fetch_assoc($query3);
                  $totalAbsent = $absentData['totalAbsent'];

                  $query4 = mysqli_query($conn, "SELECT COUNT(*) as totalPresent FROM tblattendance WHERE admissionNo = '$admissionNumber' AND status = 1");
                  $presentData = mysqli_fetch_assoc($query4);
                  $totalPresent = $presentData['totalPresent'];
                  ?>
                  <!-- Total Attendance Card Example -->
                  <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card h-100">
                    <div class="card-body">
                    <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Total Student Attendance</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totAttendance; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs"> 
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar fa-2x text-warning"></i>
                    </div>
                    </div>
                    </div>
                    </div>
                  </div>

                  <!-- Total Absent Card Example -->
                  <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card h-100">
                    <div class="card-body">
                    <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Total Student Absences</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalAbsent; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs"> 
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar-times fa-2x text-danger"></i>
                    </div>
                    </div>
                    </div>
                    </div>
                  </div>

                  <!-- Total Present Card Example -->
                  <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card h-100">
                    <div class="card-body">
                    <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Total Student Presents</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalPresent; ?></div>
                      <div class="mt-2 mb-0 text-muted text-xs"> 
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar-check fa-2x text-success"></i>
                    </div>
                    </div>
                    </div>
                    </div>
                  </div>
          </div>
          
          <!--Row-->

          <!-- <div class="row">
            <div class="col-lg-12 text-center">
              <p>Do you like this template ? you can download from <a href="https://github.com/indrijunanda/RuangAdmin"
                  class="btn btn-primary btn-sm" target="_blank"><i class="fab fa-fw fa-github"></i>&nbsp;GitHub</a></p>
            </div>
          </div> -->

        </div>

   
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
      <?php include 'includes/footer.php';?>
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
  <script src="../vendor/chart.js/Chart.min.js"></script>
  <script src="js/demo/chart-area-demo.js"></script>  
</body>

</html>