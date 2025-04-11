<?php 
session_start();
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Function to store toast message in session
function setToastMessage($message, $type) {
    $_SESSION['toast'] = ['message' => $message, 'type' => $type];
}

//------------------------SAVE--------------------------------------------------
if (isset($_POST['save'])) {
    $className = $_POST['className'];
    $schoolYear = $_POST['schoolYear'];

    $query = mysqli_query($conn, "SELECT * FROM tblclass WHERE className ='$className' AND schoolYear = '$schoolYear'");
    $ret = mysqli_fetch_array($query);

    if ($ret > 0) {
        setToastMessage('This Class Already Exists!', 'error');
    } else {
        $query = mysqli_query($conn, "INSERT INTO tblclass(className, schoolYear) VALUES('$className', '$schoolYear')");
        if ($query) {
            setToastMessage('Created Successfully!', 'success');
        } else {
            setToastMessage('An error occurred!', 'error');
        }
    }
    header("Location: createClass.php"); // Refresh page to display toast
    exit();
}

//---------------------------------------EDIT-------------------------------------------------------------
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = $_GET['Id'];
    $query = mysqli_query($conn, "SELECT * FROM tblclass WHERE Id ='$Id'");
    $row = mysqli_fetch_array($query);

    if (isset($_POST['update'])) {
        $className = $_POST['className'];
        $schoolYear = $_POST['schoolYear'];

        $query = mysqli_query($conn, "UPDATE tblclass SET className='$className', schoolYear='$schoolYear' WHERE Id='$Id'");
        if ($query) {
            setToastMessage('Updated Successfully!', 'success');
        } else {
            setToastMessage('An error occurred!', 'error');
        }
        header("Location: createClass.php"); // Refresh page to display toast
        exit();
    }
}

//--------------------------------DELETE------------------------------------------------------------------
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = $_GET['Id'];
    $query = mysqli_query($conn, "DELETE FROM tblclass WHERE Id='$Id'");
    if ($query) {
        setToastMessage('Deleted Successfully!', 'success');
    } else {
        setToastMessage('An error occurred!', 'error');
    }
    header("Location: createClass.php"); // Refresh page to display toast
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Manage Classes</title>
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
            <h1 class="h3 mb-0 text-gray-800">Grade Level</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Grade Level</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-info">Add/Edit Grade Level</h6>
                </div>

                <div class="card-body">
                  <form method="post">
                    <div class="form-group">
                      <label>Grade Level<span class="text-danger">*</span></label>
                      <select required name="className" class="form-control">
                        <option value="">--Select Grade Level--</option>
                        <option value="Grade 7" <?php echo (isset($row['className']) && $row['className'] == 'Grade 7') ? 'selected' : ''; ?>>Grade 7</option>
                        <option value="Grade 8" <?php echo (isset($row['className']) && $row['className'] == 'Grade 8') ? 'selected' : ''; ?>>Grade 8</option>
                        <option value="Grade 9" <?php echo (isset($row['className']) && $row['className'] == 'Grade 9') ? 'selected' : ''; ?>>Grade 9</option>
                        <option value="Grade 10" <?php echo (isset($row['className']) && $row['className'] == 'Grade 10') ? 'selected' : ''; ?>>Grade 10</option>
                        <option value="Grade 11" <?php echo (isset($row['className']) && $row['className'] == 'Grade 11') ? 'selected' : ''; ?>>Grade 11</option>
                        <option value="Grade 12" <?php echo (isset($row['className']) && $row['className'] == 'Grade 12') ? 'selected' : ''; ?>>Grade 12</option>
                      </select>
                    </div>

                    <?php if (isset($Id)) { ?>
                      <button type="submit" name="update" class="btn btn-warning">Update</button>
                    <?php } else { ?>
                      <button type="submit" name="save" class="btn btn-info">Save</button>
                    <?php } ?>
                  </form>
                </div>
              </div>

              <!-- Display Table -->
              <div class="card mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-info">All Classes</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>Grade Level</th>
                        <th>Class</th>
                        <th>School Year</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "SELECT tblclass.*, tblsessionterm.sessionName 
                                FROM tblclass 
                                LEFT JOIN tblsessionterm ON tblclass.schoolYear = tblsessionterm.Id";
                      $rs = $conn->query($query);
                      $sn = 0;
                      while ($rows = $rs->fetch_assoc()) {
                          $sn++;
                          echo "
                          <tr>
                              <td>{$sn}</td>
                              <td>{$rows['className']}</td>
                              <td>{$rows['sessionName']}</td>
                              <td><a href='?action=edit&Id={$rows['Id']}' class='btn btn-sm btn-warning'>Edit</a></td>
                              <td><a href='?action=delete&Id={$rows['Id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>
                          </tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
      <?php include "Includes/footer.php";?>
    </div>
  </div>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#dataTableHover').DataTable();
    });
  </script>
</body>
</html>
