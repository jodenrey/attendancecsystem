<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

$query = "SELECT tblclass.className,tblclassarms.classArmName 
    FROM tblclassteacher
    INNER JOIN tblclass ON tblclass.Id = tblclassteacher.classId
    INNER JOIN tblclassarms ON tblclassarms.Id = tblclassteacher.classArmId
    Where tblclassteacher.Id = '$_SESSION[userId]'";

    $rs = $conn->query($query);
    $num = $rs->num_rows;
    $rrw = $rs->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>View Student</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">



   <script>
    function classArmDropdown(str) {
    if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","ajaxClassArms2.php?cid="+str,true);
        xmlhttp.send();
    }
}
</script>
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
            <h1 class="h3 mb-0 text-gray-800">All Student in (<?php echo $rrw['className'].' - '.$rrw['classArmName'];?>) Class</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">All Student in Class</li>
            </ol>
          </div>

          <div class="form-group row mb-3">
            <div class="col-xl-6">
              <label class="form-control-label">Grade Level<span class="text-danger ml-2">*</span></label>
              <select required name="gradeLevel" class="form-control mb-3" onchange="filterStudents()">
                <option value="">--Select Grade Level--</option>
                <?php
                $qry = "SELECT * FROM tblclass ORDER BY className ASC";
                $result = $conn->query($qry);
                while ($rows = $result->fetch_assoc()) {
                  echo '<option value="'.$rows['Id'].'">'.$rows['className'].'</option>';
                }
                ?>
              </select>
            </div>
            <div class="col-xl-6">
              <label class="form-control-label">School Year<span class="text-danger ml-2">*</span></label>
              <select required name="schoolYear" class="form-control mb-3" onchange="filterStudents()">
                <option value="">--Select School Year--</option>
                <?php
                $qry = "SELECT * FROM tblsessionterm WHERE isActive = 1 ORDER BY dateCreated DESC";
                $result = $conn->query($qry);
                while ($rows = $result->fetch_assoc()) {
                  echo '<option value="'.$rows['Id'].'">'.$rows['sessionName'].'</option>';
                }
                ?>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Students</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Other Name</th>
                        <th>ID No.</th>
                        <th>Grade Level</th>
                        <th>Section</th>
                        <th>School Year</th>
                      </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                      <?php
                      $query = "SELECT s.*, c.className, ca.classArmName 
                               FROM tblstudents s
                               INNER JOIN tblclass c ON c.Id = s.classId
                               INNER JOIN tblclassarms ca ON ca.Id = s.classArmId
                               WHERE s.classId = '$_SESSION[classId]' AND s.classArmId = '$_SESSION[classArmId]'
                               ORDER BY s.firstName ASC";
                      $rs = $conn->query($query);
                      $num = $rs->num_rows;
                      $sn = 0;
                      if($num > 0) { 
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
                            <td>".getCurrentSessionName($conn)."</td>
                          </tr>";
                        }
                      } else {
                        echo "<tr><td colspan='8' class='text-center'>No Record Found!</td></tr>";
                      }
                      
                      // Helper function to get current session name
                      function getCurrentSessionName($conn) {
                        $query = "SELECT sessionName FROM tblsessionterm WHERE isActive = '1' LIMIT 1";
                        $result = $conn->query($query);
                        if ($result && $row = $result->fetch_assoc()) {
                            return $row['sessionName'];
                        }
                        return "N/A";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Documentation Link -->
          <!-- <div class="row">
            <div class="col-lg-12 text-center">
              <p>For more documentations you can visit<a href="https://getbootstrap.com/docs/4.3/components/forms/"
                  target="_blank">
                  bootstrap forms documentations.</a> and <a
                  href="https://getbootstrap.com/docs/4.3/components/input-group/" target="_blank">bootstrap input
                  groups documentations</a></p>
            </div>
          </div> -->

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

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });

    function filterStudents() {
      const gradeLevel = document.querySelector('select[name="gradeLevel"]').value;
      const schoolYear = document.querySelector('select[name="schoolYear"]').value;
      
      fetch(`ajaxFilterStudents.php?gradeLevel=${gradeLevel}&schoolYear=${schoolYear}`)
        .then(response => response.text())
        .then(html => {
          document.getElementById('studentsTableBody').innerHTML = html;
        })
        .catch(error => {
          console.error('Error:', error);
          document.getElementById('studentsTableBody').innerHTML = '<tr><td colspan="8" class="text-center">Error loading students</td></tr>';
        });
    }
  </script>
</body>

</html>