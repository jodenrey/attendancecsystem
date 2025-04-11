<?php 
session_start();
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get all school years
$querySchoolYears = mysqli_query($conn, "SELECT * FROM tblsessionterm ORDER BY Id DESC");
$schoolYears = array();
while($row = mysqli_fetch_assoc($querySchoolYears)) {
    $schoolYears[] = $row;
}

// Get all grades/classes
$queryClasses = mysqli_query($conn, "SELECT * FROM tblclass ORDER BY Id ASC");

// Get selected filters
$selectedSchoolYear = isset($_GET['schoolYear']) ? $_GET['schoolYear'] : '';
$selectedGrade = isset($_GET['grade']) ? $_GET['grade'] : '';
$selectedSection = isset($_GET['section']) ? $_GET['section'] : '';
$viewType = isset($_GET['viewType']) ? $_GET['viewType'] : 'all';
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$selectedStudent = isset($_GET['student']) ? $_GET['student'] : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Attendance Records</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <style>
    .grade-box {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 20px;
        margin: 10px;
        text-align: center;
        transition: all 0.3s ease;
        background-color: #f8f9fc;
    }
    .grade-box:hover {
        transform: scale(1.05);
        cursor: pointer;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        background-color: #eaecf4;
    }
    .grade-box.selected {
        background-color: #4e73df;
        color: white;
    }
    .section-box {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 15px;
        margin: 8px;
        text-align: center;
        transition: all 0.3s ease;
        background-color: #f8f9fc;
    }
    .section-box:hover {
        transform: scale(1.05);
        cursor: pointer;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        background-color: #eaecf4;
    }
    .section-box.selected {
        background-color: #1cc88a;
        color: white;
    }
    .view-type-btn {
        margin-right: 10px;
        margin-bottom: 10px;
    }
    .attendance-present {
        background-color: #1cc88a;
        color: white;
    }
    .attendance-absent {
        background-color: #e74a3b;
        color: white;
    }
  </style>
  <script src="../vendor/jquery/jquery.min.js"></script>
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php"; ?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php"; ?>
        <!-- Topbar -->

        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Manage Attendance Records</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Attendance Records</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Search and Filter Card -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Filter Attendance Records</h6>
                </div>
                <div class="card-body">
                  <form method="get" action="">
                    <div class="form-row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="schoolYear">School Year:</label>
                          <select class="form-control" name="schoolYear" id="schoolYear">
                            <option value="">--Select School Year--</option>
                            <?php 
                            // Add debugging information
                            if(empty($schoolYears)) {
                                echo '<option value="" disabled>No school years found in database</option>';
                            }
                            
                            foreach($schoolYears as $year): 
                                $selected = ($selectedSchoolYear == $year['Id']) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $year['Id']; ?>" <?php echo $selected; ?>>
                                    <?php echo $year['sessionName']; ?>
                                </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6 d-flex align-items-end">
                        <div class="form-group">
                          <button type="submit" class="btn btn-primary">Apply</button>
                          <a href="manageAttendance.php" class="btn btn-secondary ml-2">Reset</a>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <?php if($selectedSchoolYear): ?>
          <!-- If a school year is selected, show the grades -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Select Grade Level</h6>
                </div>
                <div class="card-body">
                  <div class="row">
                    <?php
                    while($classRow = mysqli_fetch_assoc($queryClasses)) {
                        $isSelected = ($selectedGrade == $classRow['Id']) ? 'selected' : '';
                        echo '<div class="col-md-2">';
                        echo '<div class="grade-box '.$isSelected.'" data-grade-id="'.$classRow['Id'].'" onclick="selectGrade(\''.$classRow['Id'].'\')">';
                        echo '<h4>'.$classRow['className'].'</h4>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if($selectedGrade): ?>
          <!-- If a grade is selected, show the sections -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Select Section</h6>
                </div>
                <div class="card-body">
                  <div class="row">
                    <?php
                    $querySections = mysqli_query($conn, "SELECT * FROM tblclassarms WHERE classId = '$selectedGrade'");
                    while($sectionRow = mysqli_fetch_assoc($querySections)) {
                        $isSelected = ($selectedSection == $sectionRow['Id']) ? 'selected' : '';
                        echo '<div class="col-md-2">';
                        echo '<div class="section-box '.$isSelected.'" data-section-id="'.$sectionRow['Id'].'" onclick="selectSection(\''.$sectionRow['Id'].'\')">';
                        echo '<h5>'.$sectionRow['classArmName'].'</h5>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if($selectedSection): ?>
          <!-- If a section is selected, show the view options and attendance records -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">View Options</h6>
                </div>
                <div class="card-body">
                  <div class="btn-group" role="group">
                    <button type="button" class="btn <?php echo ($viewType == 'all') ? 'btn-primary' : 'btn-outline-primary'; ?> view-type-btn" onclick="setViewType('all')">All Records</button>
                    <button type="button" class="btn <?php echo ($viewType == 'single') ? 'btn-primary' : 'btn-outline-primary'; ?> view-type-btn" onclick="setViewType('single')">Single Student</button>
                    <button type="button" class="btn <?php echo ($viewType == 'month') ? 'btn-primary' : 'btn-outline-primary'; ?> view-type-btn" onclick="setViewType('month')">By Month</button>
                  </div>
                  
                  <?php if($viewType == 'single'): ?>
                  <!-- Student Selection for Single View -->
                  <div class="mt-3">
                    <form method="get" action="">
                      <input type="hidden" name="schoolYear" value="<?php echo $selectedSchoolYear; ?>">
                      <input type="hidden" name="grade" value="<?php echo $selectedGrade; ?>">
                      <input type="hidden" name="section" value="<?php echo $selectedSection; ?>">
                      <input type="hidden" name="viewType" value="single">
                      <div class="form-group">
                        <label>Select Student:</label>
                        <select class="form-control" name="student" onchange="this.form.submit()">
                          <option value="">--Select Student--</option>
                          <?php 
                          $queryStudents = mysqli_query($conn, "SELECT * FROM tblstudents WHERE classId = '$selectedGrade' AND classArmId = '$selectedSection' ORDER BY firstName, lastName");
                          while($studentRow = mysqli_fetch_assoc($queryStudents)) {
                              $selected = ($selectedStudent == $studentRow['admissionNumber']) ? 'selected' : '';
                              echo '<option value="'.$studentRow['admissionNumber'].'" '.$selected.'>'.$studentRow['firstName'].' '.$studentRow['lastName'].' ('.$studentRow['admissionNumber'].')</option>';
                          }
                          ?>
                        </select>
                      </div>
                    </form>
                  </div>
                  <?php endif; ?>
                  
                  <?php if($viewType == 'month'): ?>
                  <!-- Month Selection for Monthly View -->
                  <div class="mt-3">
                    <form method="get" action="">
                      <input type="hidden" name="schoolYear" value="<?php echo $selectedSchoolYear; ?>">
                      <input type="hidden" name="grade" value="<?php echo $selectedGrade; ?>">
                      <input type="hidden" name="section" value="<?php echo $selectedSection; ?>">
                      <input type="hidden" name="viewType" value="month">
                      <div class="form-row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Select Month:</label>
                            <select class="form-control" name="month">
                              <?php 
                              $months = [
                                  '01' => 'January', '02' => 'February', '03' => 'March', 
                                  '04' => 'April', '05' => 'May', '06' => 'June', 
                                  '07' => 'July', '08' => 'August', '09' => 'September', 
                                  '10' => 'October', '11' => 'November', '12' => 'December'
                              ];
                              foreach($months as $num => $name) {
                                  $selected = ($selectedMonth == $num) ? 'selected' : '';
                                  echo '<option value="'.$num.'" '.$selected.'>'.$name.'</option>';
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Select Year:</label>
                            <select class="form-control" name="year">
                              <?php 
                              $currentYear = date('Y');
                              for($year = $currentYear - 5; $year <= $currentYear + 1; $year++) {
                                  $selected = ($selectedYear == $year) ? 'selected' : '';
                                  echo '<option value="'.$year.'" '.$selected.'>'.$year.'</option>';
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <button type="submit" class="btn btn-primary">Apply</button>
                      </div>
                    </form>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Attendance Records Table -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">
                    <?php 
                    // Get class and section names
                    $classQuery = mysqli_query($conn, "SELECT className FROM tblclass WHERE Id = '$selectedGrade'");
                    $classRow = mysqli_fetch_assoc($classQuery);
                    $className = $classRow ? $classRow['className'] : '';
                    
                    $sectionQuery = mysqli_query($conn, "SELECT classArmName FROM tblclassarms WHERE Id = '$selectedSection'");
                    $sectionRow = mysqli_fetch_assoc($sectionQuery);
                    $sectionName = $sectionRow ? $sectionRow['classArmName'] : '';
                    
                    echo 'Attendance Records for '.$className.' - '.$sectionName;
                    
                    // Create view type info for printing
                    $viewTypeInfo = '';
                    if($viewType == 'single' && $selectedStudent) {
                        $studentQuery = mysqli_query($conn, "SELECT firstName, lastName FROM tblstudents WHERE admissionNumber = '$selectedStudent'");
                        $studentRow = mysqli_fetch_assoc($studentQuery);
                        if($studentRow) {
                            echo ' - '.$studentRow['firstName'].' '.$studentRow['lastName'];
                            $viewTypeInfo = ' - '.$studentRow['firstName'].' '.$studentRow['lastName'];
                        }
                    } else if($viewType == 'month') {
                        $monthNames = [
                            '01' => 'January', '02' => 'February', '03' => 'March', 
                            '04' => 'April', '05' => 'May', '06' => 'June', 
                            '07' => 'July', '08' => 'August', '09' => 'September', 
                            '10' => 'October', '11' => 'November', '12' => 'December'
                        ];
                        echo ' - '.$monthNames[$selectedMonth].' '.$selectedYear;
                        $viewTypeInfo = ' - '.$monthNames[$selectedMonth].' '.$selectedYear;
                    }
                    ?>
                  </h6>
                  <div>
                    <button class="btn btn-sm btn-primary" onclick="printAttendanceRecord()">
                      <i class="fas fa-print"></i> Print
                    </button>
                    <button class="btn btn-sm btn-success" onclick="exportToExcel()">
                      <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <div class="table-responsive" id="printArea">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th>#</th>
                          <?php if($viewType != 'single'): ?>
                          <th>Student ID</th>
                          <th>Name</th>
                          <?php endif; ?>
                          <th>Date</th>
                          <th>Status</th>
                          <?php if($viewType == 'all'): ?>
                          <th>Taken By</th>
                          <?php endif; ?>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $whereClause = "WHERE a.classId = '$selectedGrade' AND a.classArmId = '$selectedSection' AND a.sessionTermId = '$selectedSchoolYear'";
                        
                        if($viewType == 'single' && $selectedStudent) {
                            $whereClause .= " AND a.admissionNo = '$selectedStudent'";
                        } else if($viewType == 'month') {
                            $startDate = $selectedYear.'-'.$selectedMonth.'-01';
                            $endDate = date('Y-m-t', strtotime($startDate));
                            $whereClause .= " AND a.dateTimeTaken BETWEEN '$startDate' AND '$endDate'";
                        }
                        
                        $query = "SELECT a.*, s.firstName, s.lastName, s.otherName, 
                                  t.firstName as teacherFirstName, t.lastName as teacherLastName
                                  FROM tblattendance a
                                  LEFT JOIN tblstudents s ON a.admissionNo = s.admissionNumber
                                  LEFT JOIN tblclassarms ca ON a.classArmId = ca.Id
                                  LEFT JOIN tblclassteacher t ON ca.teacherId = t.Id
                                  $whereClause
                                  ORDER BY a.dateTimeTaken DESC, s.firstName, s.lastName";
                                  
                        $rs = $conn->query($query);
                        $num = $rs->num_rows;
                        $sn = 0;
                        
                        if($num > 0) {
                            while ($rows = $rs->fetch_assoc()) {
                                $sn++;
                                echo "<tr>";
                                echo "<td>".$sn."</td>";
                                
                                if($viewType != 'single') {
                                    echo "<td>".$rows['admissionNo']."</td>";
                                    echo "<td>".$rows['firstName']." ".$rows['lastName']."</td>";
                                }
                                
                                echo "<td>".date('M d, Y', strtotime($rows['dateTimeTaken']))."</td>";
                                
                                $statusClass = ($rows['status'] == '1') ? 'attendance-present' : 'attendance-absent';
                                $statusText = ($rows['status'] == '1') ? 'Present' : 'Absent';
                                echo "<td><span class='badge ".$statusClass."'>".$statusText."</span></td>";
                                
                                if($viewType == 'all') {
                                    echo "<td>".$rows['teacherFirstName']." ".$rows['teacherLastName']."</td>";
                                }
                                
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No attendance records found</td></tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
      <?php include "Includes/footer.php"; ?>
      <!-- Footer -->
    </div>
  </div>

  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
  
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable();
      $('#searchSchoolYear').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#schoolYear option").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });
      
      // Remove click handlers from jQuery and rely on the onclick attributes in the HTML
      // This prevents the conflict that was causing the error
    });
    
    // Create global variables from PHP for use in JavaScript functions
    <?php if (isset($className)): ?>
    var className = "<?php echo $className; ?>";
    var sectionName = "<?php echo $sectionName; ?>";
    var viewTypeInfo = "<?php echo $viewTypeInfo; ?>";
    <?php else: ?>
    var className = "";
    var sectionName = "";
    var viewTypeInfo = "";
    <?php endif; ?>
    
    function selectGrade(gradeId) {
      // Make sure we get the current URL to preserve the school year parameter
      const url = new URL(window.location);
      url.searchParams.set('grade', gradeId);
      // Remove any lower-level selections
      url.searchParams.delete('section');
      url.searchParams.delete('viewType');
      url.searchParams.delete('student');
      url.searchParams.delete('month');
      url.searchParams.delete('year');
      window.location.href = url.toString();
    }
    
    function selectSection(sectionId) {
      const url = new URL(window.location);
      url.searchParams.set('section', sectionId);
      url.searchParams.set('viewType', 'all');
      url.searchParams.delete('student');
      url.searchParams.delete('month');
      url.searchParams.delete('year');
      window.location.href = url.toString();
    }
    
    function setViewType(type) {
      const url = new URL(window.location);
      url.searchParams.set('viewType', type);
      
      if (type !== 'single') {
        url.searchParams.delete('student');
      }
      
      if (type !== 'month') {
        url.searchParams.delete('month');
        url.searchParams.delete('year');
      }
      
      window.location.href = url.toString();
    }
    
    function printAttendanceRecord() {
      const printContents = document.getElementById('printArea').innerHTML;
      const originalContents = document.body.innerHTML;
      
      // Use backticks for template literal but escape PHP tags properly
      document.body.innerHTML = `
        <div style="padding: 20px;">
          <h2 style="text-align: center;">Attendance Record</h2>
          <h4 style="text-align: center;">
            ${className} - ${sectionName}
            ${viewTypeInfo}
          </h4>
          ${printContents}
        </div>
      `;
      
      window.print();
      document.body.innerHTML = originalContents;
    }
    
    function exportToExcel() {
      // In a real implementation, you would add code to export to Excel
      // This usually requires server-side processing or a library
      alert("Export to Excel functionality would be implemented here");
    }
  </script>
</body>

</html>