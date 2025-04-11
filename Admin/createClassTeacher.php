<?php 
session_start();
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE--------------------------------------------------

if (isset($_POST['save'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $emailAddress = $_POST['emailAddress'];
    $phoneNo = $_POST['phoneNo'];
    $dateCreated = date("Y-m-d");

    $query = mysqli_query($conn, "SELECT * FROM tblclassteacher WHERE emailAddress = '$emailAddress'");
    $ret = mysqli_fetch_array($query);

    $sampPass = "pass123";
    $sampPass_2 = md5($sampPass);

    if ($ret > 0) {
        $statusMsg = "<div class='alert alert-danger'>This Email Address Already Exists!</div>";
    } else {
        // Insert only the teacher details without class assignment
        $query = mysqli_query($conn, "INSERT INTO tblclassteacher(firstName, lastName, emailAddress, password, phoneNo, dateCreated) 
            VALUES ('$firstName', '$lastName', '$emailAddress', '$sampPass_2', '$phoneNo', '$dateCreated')");

        if ($query) {
            $statusMsg = "<div class='alert alert-success'>Teacher Created Successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger'>An error Occurred: " . mysqli_error($conn) . "</div>";
        }
    }
}

//--------------------EDIT------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = $_GET['Id'];

    $query = mysqli_query($conn, "SELECT * FROM tblclassteacher WHERE Id ='$Id'");
    $row = mysqli_fetch_array($query);

    //------------UPDATE-----------------------------
    if (isset($_POST['update'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $emailAddress = $_POST['emailAddress'];
        $phoneNo = $_POST['phoneNo'];

        $query = mysqli_query($conn, "UPDATE tblclassteacher SET firstName='$firstName', lastName='$lastName',
            emailAddress='$emailAddress', phoneNo='$phoneNo' WHERE Id='$Id'");

        if ($query) {
            $statusMsg = "<div class='alert alert-success'>Teacher Updated Successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger'>An error Occurred: " . mysqli_error($conn) . "</div>";
        }
    }
}

//--------------------------------DELETE------------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = $_GET['Id'];

    // First check if teacher is assigned to any class arm
    $checkAssignmentQuery = mysqli_query($conn, "SELECT * FROM tblclassarms WHERE teacherId='$Id'");
    if(mysqli_num_rows($checkAssignmentQuery) > 0) {
        $statusMsg = "<div class='alert alert-danger'>Cannot delete! This teacher is assigned to a class section. Unassign first.</div>";
    } else {
        $query = mysqli_query($conn, "DELETE FROM tblclassteacher WHERE Id='$Id'");

        if ($query) {
            $statusMsg = "<div class='alert alert-success'>Teacher Deleted Successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger'>An error Occurred: " . mysqli_error($conn) . "</div>";
        }
    }
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
            <h1 class="h3 mb-0 text-gray-800">Create Class Teachers</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Class Teachers</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Create Class Teachers</h6>
                    <?php echo $statusMsg ?? ""; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                   <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Firstname<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" required name="firstName" value="<?php echo isset($row['firstName']) ? $row['firstName'] : '';?>" id="exampleInputFirstName">
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">Lastname<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" required name="lastName" value="<?php echo isset($row['lastName']) ? $row['lastName'] : '';?>" id="exampleInputFirstName" >
                        </div>
                    </div>
                     <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Email Address<span class="text-danger ml-2">*</span></label>
                        <input type="email" class="form-control" required name="emailAddress" value="<?php echo isset($row['emailAddress']) ? $row['emailAddress'] : '';?>" id="exampleInputFirstName" >
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">Phone No<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" name="phoneNo" value="<?php echo isset($row['phoneNo']) ? $row['phoneNo'] : '';?>" id="exampleInputFirstName" >
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
                    <button type="submit" name="save" class="btn btn-primary">Save</button>
                    <div class="mt-3 alert alert-info">
                      <i class="fas fa-info-circle"></i> Default password for new teachers: <strong>pass123</strong>
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
                  <h6 class="m-0 font-weight-bold text-primary">All Class Teachers</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email Address</th>
                        <th>Phone No</th>
                        <th>Assignment Status</th>
                        <th>Date Created</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </tr>
                    </thead>
                   
                    <tbody>

                  <?php
                      $query = "SELECT t.Id, t.firstName, t.lastName, t.emailAddress, t.phoneNo, t.dateCreated, 
                              (SELECT COUNT(*) FROM tblclassarms WHERE tblclassarms.teacherId = t.Id) as isAssigned
                              FROM tblclassteacher t ORDER BY t.firstName ASC";
                      $rs = $conn->query($query);
                      $num = $rs->num_rows;
                      $sn=0;
                      if($num > 0)
                      { 
                        while ($rows = $rs->fetch_assoc())
                          {
                             $sn = $sn + 1;
                             $assignStatus = $rows['isAssigned'] > 0 ? 
                                '<span class="badge badge-success">Assigned</span>' : 
                                '<span class="badge badge-warning">Not Assigned</span>';
                            echo"
                              <tr>
                                <td>".$sn."</td>
                                <td>".$rows['firstName']."</td>
                                <td>".$rows['lastName']."</td>
                                <td>".$rows['emailAddress']."</td>
                                <td>".$rows['phoneNo']."</td>
                                <td>".$assignStatus."</td>
                                <td>".$rows['dateCreated']."</td>
                                <td><a href='?action=edit&Id=".$rows['Id']."'><i class='fas fa-fw fa-edit'></i></a></td>
                                <td><a href='?action=delete&Id=".$rows['Id']."'><i class='fas fa-fw fa-trash'></i></a></td>
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
      $('#dataTableHover').DataTable();
    });
  </script>
</body>

</html>