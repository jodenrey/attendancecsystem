<?php 
session_start();
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Function to set toast messages
function setToastMessage($message, $type) {
    $_SESSION['toast'] = ['message' => $message, 'type' => $type];
}

//------------------------SAVE--------------------------------------------------

if (isset($_POST['save'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $otherName = $_POST['otherName'];
    $admissionNumber = $_POST['admissionNumber'];
    $classId = $_POST['classId'];
    $classArmId = $_POST['classArmId'];
    $dateCreated = date("Y-m-d");

    $query = mysqli_query($conn, "SELECT * FROM tblstudents WHERE admissionNumber = '$admissionNumber'");
    $ret = mysqli_fetch_array($query);

    if ($ret > 0) {
        setToastMessage('This Admission Number Already Exists!', 'error');
    } else {
        $query = mysqli_query($conn, "INSERT INTO tblstudents (firstName, lastName, otherName, admissionNumber, password, classId, classArmId, dateCreated) 
            VALUES ('$firstName', '$lastName', '$otherName', '$admissionNumber', '12345', '$classId', '$classArmId', '$dateCreated')");

        if ($query) {
            setToastMessage('Student Created Successfully!', 'success');
        } else {
            setToastMessage('An error Occurred!', 'error');
        }
    }
    header("Location: createStudents.php");
    exit();
}

//--------------------EDIT------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = $_GET['Id'];

    $query = mysqli_query($conn, "SELECT * FROM tblstudents WHERE Id ='$Id'");
    $row = mysqli_fetch_array($query);

    //------------UPDATE-----------------------------
    if (isset($_POST['update'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $otherName = $_POST['otherName'];
        $admissionNumber = $_POST['admissionNumber'];
        $classId = $_POST['classId'];
        $classArmId = $_POST['classArmId'];

        $query = mysqli_query($conn, "UPDATE tblstudents SET firstName='$firstName', lastName='$lastName',
            otherName='$otherName', admissionNumber='$admissionNumber', classId='$classId', classArmId='$classArmId'
            WHERE Id='$Id'");

        if ($query) {
            setToastMessage('Student Updated Successfully!', 'success');
        } else {
            setToastMessage('An error Occurred!', 'error');
        }
        header("Location: createStudents.php");
        exit();
    }
}

//--------------------------------DELETE------------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = $_GET['Id'];

    $query = mysqli_query($conn, "DELETE FROM tblstudents WHERE Id='$Id'");

    if ($query) {
        setToastMessage('Student Deleted Successfully!', 'success');
    } else {
        setToastMessage('An error Occurred!', 'error');
    }
    header("Location: createStudents.php");
    exit();
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
<?php include 'includes/title.php';?>
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
            <h1 class="h3 mb-0 text-gray-800">Create Students</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Students</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-info">Create Students</h6>
                    <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                   <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Firstname<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="firstName" value="<?php echo $row['firstName'];?>" id="exampleInputFirstName" >
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">Lastname<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" name="lastName" value="<?php echo $row['lastName'];?>" id="exampleInputFirstName" >
                        </div>
                    </div>
                     <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Other Name<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="otherName" value="<?php echo $row['otherName'];?>" id="exampleInputFirstName" >
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">ID Number<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" required name="admissionNumber" value="<?php echo $row['admissionNumber'];?>" id="exampleInputFirstName" >
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Select Class<span class="text-danger ml-2">*</span></label>
                         <?php
                        $qry= "SELECT * FROM tblclass ORDER BY className ASC";
                        $result = $conn->query($qry);
                        $num = $result->num_rows;		
                        if ($num > 0){
                          echo ' <select required name="classId" onchange="classArmDropdown(this.value)" class="form-control mb-3">';
                          echo'<option value="">--Select Class--</option>';
                          while ($rows = $result->fetch_assoc()){
                          echo'<option value="'.$rows['Id'].'" >'.$rows['className'].'</option>';
                              }
                                  echo '</select>';
                              }
                            ?>  
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">Class Arm<span class="text-danger ml-2">*</span></label>
                            <?php
                                echo"<div id='txtHint'></div>";
                            ?>
                        </div>
                    </div>
                      <?php
                    if (isset($Id))
                    {
                    ?>
                    <button type="submit" name="update" class="btn btn-warning">Update</button>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                    } else {           
                    ?>
                    <button type="submit" name="save" class="btn btn-info">Save</button>
                    <div class="mt-3 alert alert-info">
                      <i class="fas fa-info-circle"></i> Default password for new students: <strong>12345</strong>
                    </div>
                    <?php
                    }         
                    ?>
                  </form>
                </div>
              </div>

              <!-- Input Group -->
                 <div class="row">
              <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-info">All Student</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Other Name</th>
                        <th>ID No</th>
                        <th>Class</th>
                        <th>Class Section</th>
                        <th>Date Created</th>
                         <th>Edit</th>
                        
                      </tr>
                    </thead>
                
                    <tbody>

                  <?php
                      $query = "SELECT tblstudents.Id,tblclass.className,tblclassarms.classArmName,tblclassarms.Id AS classArmId,tblstudents.firstName,
                      tblstudents.lastName,tblstudents.otherName,tblstudents.admissionNumber,tblstudents.dateCreated
                      FROM tblstudents
                      INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
                      INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId";
                      $rs = $conn->query($query);
                      $num = $rs->num_rows;
                      $sn=0;
                      $status="";
                      if($num > 0)
                      { 
                        while ($rows = $rs->fetch_assoc())
                          {
                             $sn = $sn + 1;
                            echo"
                              <tr>
                                <td>".$sn."</td>
                                <td>".$rows['firstName']."</td>
                                <td>".$rows['lastName']."</td>
                                <td>".$rows['otherName']."</td>
                                <td>".$rows['admissionNumber']."</td>
                                <td>".$rows['className']."</td>
                                <td>".$rows['classArmName']."</td>
                                 <td>".$rows['dateCreated']."</td>
                                <td><a href='?action=edit&Id=".$rows['Id']."'>Edit</a></td>
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
          <!--Row-->

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

  <script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log("Toast script loaded!"); // Debugging

        <?php if (isset($_SESSION['toast'])): ?>
            console.log("Toast message detected!"); // Debugging
            
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.style.position = 'fixed';
                toastContainer.style.bottom = '20px';
                toastContainer.style.left = '20px';
                toastContainer.style.zIndex = '1000';
                document.body.appendChild(toastContainer);
            }
            
            const toast = document.createElement('div');
            toast.innerText = "<?php echo $_SESSION['toast']['message']; ?>";
            toast.style.background = "<?php echo $_SESSION['toast']['type'] === 'success' ? '#28a745' : '#dc3545'; ?>";
            toast.style.color = 'white';
            toast.style.padding = '15px 20px';
            toast.style.borderRadius = '5px';
            toast.style.marginTop = '10px';
            toast.style.boxShadow = '0px 4px 10px rgba(0,0,0,0.2)';
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.5s ease-in-out';

            toastContainer.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '1'; }, 100);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 3000); }, 3000);

            console.log("Toast displayed:", toast.innerText); // Debugging
            
            <?php unset($_SESSION['toast']); ?> // Clear toast message after displaying
        <?php endif; ?>
    });
</script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
  </script>
</body>

</html>