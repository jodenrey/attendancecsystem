<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';

// We've updated the query to reflect the new database structure
// Teachers are now associated with classes through the tblclassarms table
$query = "SELECT tblclass.className, tblclassarms.classArmName, tblclassteacher.firstName, tblclassteacher.lastName
    FROM tblclassarms
    INNER JOIN tblclass ON tblclass.Id = tblclassarms.classId
    INNER JOIN tblclassteacher ON tblclassteacher.Id = tblclassarms.teacherId
    WHERE tblclassarms.isAssigned = '1'";

$rs = $conn->query($query);
$num = $rs->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>DM-Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <style>
    /* Custom styles for cards with smaller elements */
    .dashboard-card {
      height: 200px !important;
      margin-bottom: 30px;
      transition: all 0.3s ease;
      overflow: visible;
    }
    
    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .dashboard-card .card-body {
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      height: 100%;
    }
    
    .dashboard-card .icon-container {
      font-size: 2.5rem;
      margin-bottom: 0.5rem;
    }
    
    .dashboard-card .text-container {
      text-align: center;
      width: 100%;
    }
    
    .dashboard-card .card-title {
      font-size: 1.2rem;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 1rem;
    }
    
    .dashboard-card .card-value {
      font-size: 3rem;
      font-weight: bold;
      line-height: 1;
    }
    
    /* Card border colors */
    .border-info-left {
      border-left: 5px solid #36b9cc;
    }
    
    .border-success-left {
      border-left: 5px solid #1cc88a;
    }
    
    .border-danger-left {
      border-left: 5px solid #e74a3b;
    }
    
    .border-warning-left {
      border-left: 5px solid #f6c23e;
    }
    
    /* Ensure the numbers are fully visible */
    .card-value-container {
      width: 100%;
      margin-top: 0.5rem;
      text-align: center;
    }
  </style>

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
            <h1 class="h3 mb-0 text-gray-800">Administrator Dashboard</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>

          <div class="row mb-3">
          <!-- Students Card -->
          <?php 
$query1=mysqli_query($conn,"SELECT * from tblstudents");                       
$students = mysqli_num_rows($query1);
?>
            <div class="col-xl-6 col-md-6 mb-4">
              <div class="card dashboard-card border-info-left shadow">
                <div class="card-body">
                  <div class="icon-container text-info">
                    <i class="fas fa-users"></i>
                  </div>
                  <div class="text-container">
                    <div class="card-title text-info">STUDENTS</div>
                  </div>
                  <div class="card-value-container">
                    <div class="card-value text-gray-800"><?php echo $students;?></div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Section (Class Arms) Card -->
             <?php 
$query1=mysqli_query($conn,"SELECT * from tblclassarms");                       
$classArms = mysqli_num_rows($query1);
?>
            <div class="col-xl-6 col-md-6 mb-4">
              <div class="card dashboard-card border-success-left shadow">
                <div class="card-body">
                  <div class="icon-container text-success">
                    <i class="fas fa-code-branch"></i>
                  </div>
                  <div class="text-container">
                    <div class="card-title text-success">SECTIONS</div>
                  </div>
                  <div class="card-value-container">
                    <div class="card-value text-gray-800"><?php echo $classArms;?></div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Teachers Card  -->
            <?php 
            $query1=mysqli_query($conn,"SELECT * from tblclassteacher");                       
            $classTeacher = mysqli_num_rows($query1);
            ?>
            <div class="col-xl-6 col-md-6 mb-4">
              <div class="card dashboard-card border-danger-left shadow">
                <div class="card-body">
                  <div class="icon-container text-danger">
                    <i class="fas fa-chalkboard-teacher"></i>
                  </div>
                  <div class="text-container">
                    <div class="card-title text-danger">CLASS TEACHERS</div>
                  </div>
                  <div class="card-value-container">
                    <div class="card-value text-gray-800"><?php echo $classTeacher;?></div>
                  </div>
                </div>
              </div>
            </div>
          
            <!-- School Years Card  -->
            <?php 
            $query1=mysqli_query($conn,"SELECT * from tblsessionterm");                       
            $sessTerm = mysqli_num_rows($query1);
            ?>
            <div class="col-xl-6 col-md-6 mb-4">
              <div class="card dashboard-card border-warning-left shadow">
                <div class="card-body">
                  <div class="icon-container text-warning">
                    <i class="fas fa-calendar-alt"></i>
                  </div>
                  <div class="text-container">
                    <div class="card-title text-warning">SCHOOL YEARS</div>
                  </div>
                  <div class="card-value-container">
                    <div class="card-value text-gray-800"><?php echo $sessTerm;?></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

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