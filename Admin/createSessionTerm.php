<?php 
session_start();
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../Includes/dbcon.php';
include '../Includes/session.php';

// Function to set toast messages
function setToastMessage($message, $type) {
    $_SESSION['toast'] = ['message' => $message, 'type' => $type];
}

//------------------------SAVE--------------------------------------------------
if (isset($_POST['save'])) {
    try {
        $sessionName = mysqli_real_escape_string($conn, trim($_POST['sessionName']));
        $dateCreated = date("Y-m-d");

        // Validation for school year format (YYYY/YYYY)
        $schoolYearPattern = '/^\d{4}\/\d{4}$/';
        
        if (empty($sessionName)) {
            setToastMessage('School Year field cannot be empty!', 'error');
        } elseif (!preg_match($schoolYearPattern, $sessionName)) {
            setToastMessage('Invalid school year format! Please use YYYY/YYYY format (e.g., 2024/2025)', 'error');
        } else {
            // Additional validation to ensure second year is one more than the first
            $years = explode('/', $sessionName);
            if (count($years) == 2 && (intval($years[1]) - intval($years[0]) != 1)) {
                setToastMessage('Invalid school year! The second year should be one more than the first year (e.g., 2024/2025)', 'error');
            } else {
                $query = mysqli_query($conn, "SELECT * FROM tblsessionterm WHERE sessionName ='$sessionName'");
                if (!$query) {
                    throw new Exception("Database error: " . mysqli_error($conn));
                }
                
                if (mysqli_num_rows($query) > 0) {
                    setToastMessage('This School Year Already Exists!', 'error');
                } else {
                    // Check if tblsessionterm has termId column
                    $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM tblsessionterm LIKE 'termId'");
                    $termIdExists = mysqli_num_rows($checkColumn) > 0;
                    
                    // Get the highest ID currently in use
                    $getMaxId = mysqli_query($conn, "SELECT MAX(Id) as maxId FROM tblsessionterm");
                    $maxRow = mysqli_fetch_assoc($getMaxId);
                    $nextId = ($maxRow['maxId'] ? intval($maxRow['maxId']) : 0) + 1;
                    
                    // Insert with explicit ID to avoid duplicate key issues
                    if ($termIdExists) {
                        $insertQuery = "INSERT INTO tblsessionterm (Id, sessionName, termId, isActive, dateCreated) 
                                       VALUES ($nextId, '$sessionName', '1', '0', '$dateCreated')";
                    } else {
                        $insertQuery = "INSERT INTO tblsessionterm (Id, sessionName, isActive, dateCreated) 
                                       VALUES ($nextId, '$sessionName', '0', '$dateCreated')";
                    }

                    $query = mysqli_query($conn, $insertQuery);
                    
                    if (!$query) {
                        throw new Exception("Insert failed: " . mysqli_error($conn));
                    }
                    
                    // After successful insert, reset the auto_increment value to avoid future conflicts
                    mysqli_query($conn, "ALTER TABLE tblsessionterm AUTO_INCREMENT = " . ($nextId + 1));
                    
                    setToastMessage("School Year Created Successfully with ID: $nextId!", 'success');
                }
            }
        }
    } catch (Exception $e) {
        setToastMessage('An error occurred: ' . $e->getMessage(), 'error');
    }
    
    header("Location: createSessionTerm.php");
    exit();
}

//--------------------EDIT------------------------------------------------------------
if (isset($_GET['Id'], $_GET['action']) && $_GET['action'] == "edit") {
    $Id = intval($_GET['Id']);
    
    // Get existing school year data
    $query = mysqli_query($conn, "SELECT * FROM tblsessionterm WHERE Id='$Id'");
    $row = mysqli_fetch_assoc($query);

    if (isset($_POST['update'])) {
        $sessionName = mysqli_real_escape_string($conn, trim($_POST['sessionName']));
        
        // Validation for school year format (YYYY/YYYY)
        $schoolYearPattern = '/^\d{4}\/\d{4}$/';
        
        if (empty($sessionName)) {
            setToastMessage('School Year field cannot be empty!', 'error');
        } elseif (!preg_match($schoolYearPattern, $sessionName)) {
            setToastMessage('Invalid school year format! Please use YYYY/YYYY format (e.g., 2024/2025)', 'error');
        } else {
            // Additional validation to ensure second year is one more than the first
            $years = explode('/', $sessionName);
            if (count($years) == 2 && (intval($years[1]) - intval($years[0]) != 1)) {
                setToastMessage('Invalid school year! The second year should be one more than the first year (e.g., 2024/2025)', 'error');
            } else {
                // Check if another session with same name and term exists
                $checkQuery = mysqli_query($conn, "SELECT * FROM tblsessionterm WHERE sessionName ='$sessionName' AND Id != '$Id'");
                if (mysqli_num_rows($checkQuery) > 0) {
                    setToastMessage('This School Year Already Exists!', 'error');
                } else {
                    $query = mysqli_query($conn, "UPDATE tblsessionterm SET sessionName='$sessionName' WHERE Id='$Id'");
                    setToastMessage($query ? 'School Year Updated Successfully!' : 'An error Occurred!', $query ? 'success' : 'error');
                }
            }
        }
        header("Location: createSessionTerm.php");
        exit();
    }
}

//--------------------DELETE------------------------------------------------------------
if (isset($_GET['Id'], $_GET['action']) && $_GET['action'] == "delete") {
    $Id = intval($_GET['Id']);
    $query = mysqli_query($conn, "DELETE FROM tblsessionterm WHERE Id='$Id'");
    setToastMessage($query ? 'School Year Deleted Successfully!' : 'An error Occurred!', $query ? 'success' : 'error');
    header("Location: createSessionTerm.php");
    exit();
}

//--------------------ACTIVATE------------------------------------------------------------
if (isset($_GET['Id'], $_GET['action']) && $_GET['action'] == "activate") {
    $Id = intval($_GET['Id']);
    mysqli_query($conn, "UPDATE tblsessionterm SET isActive='0' WHERE isActive='1'");
    $query = mysqli_query($conn, "UPDATE tblsessionterm SET isActive='1' WHERE Id='$Id'");
    setToastMessage($query ? 'School Year Activated Successfully!' : 'An error Occurred!', $query ? 'success' : 'error');
    header("Location: createSessionTerm.php");
    exit();
}

// Get active school year
$activeQuery = mysqli_query($conn, "SELECT * FROM tblsessionterm WHERE isActive = 1");
$activeYear = mysqli_fetch_array($activeQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create School Year</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <style>
    .modal-confirm {
      color: #636363;
    }
    .modal-confirm .modal-content {
      padding: 20px;
      border-radius: 5px;
      border: none;
    }
    .modal-confirm .modal-header {
      border-bottom: none;
      position: relative;
    }
    .modal-confirm .icon-box {
      width: 80px;
      height: 80px;
      margin: 0 auto;
      border-radius: 50%;
      z-index: 9;
      text-align: center;
      border: 3px solid #f8bb86;
    }
    .modal-confirm .icon-box i {
      color: #f8bb86;
      font-size: 46px;
      display: inline-block;
      margin-top: 13px;
    }
    .modal-confirm h4 {
      text-align: center;
      font-size: 26px;
      margin: 30px 0 10px;
    }
    .modal-confirm .modal-footer {
      border: none;
      text-align: center;
      border-radius: 5px;
      font-size: 13px;
    }
  </style>
</head>
<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php"; ?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php"; ?>
        <div class="container-fluid">
          <h1 class="h3 mb-4 text-gray-800">Create School Year</h1>
          
          <!-- Active School Year Display -->
          <?php if ($activeYear) { ?>
          <div class="alert alert-info">
            <strong>Current Active School Year:</strong> <?php echo $activeYear['sessionName']; ?>
          </div>
          <?php } else { ?>
          <div class="alert alert-warning">
            <strong>No active school year set!</strong> Please activate a school year below.
          </div>
          <?php } ?>
          
          <div class="card mb-4">
            <div class="card-header">Create School Year</div>
            <div class="card-body">
              <form method="post" id="schoolYearForm">
                <div class="form-group">
                  <label>School Year Range<span class="text-danger">*</span></label>
                  <input type="text" 
                        class="form-control" 
                        name="sessionName" 
                        id="sessionName"
                        value="<?php echo isset($row['sessionName']) ? $row['sessionName'] : ''; ?>" 
                        placeholder="Enter School Year (YYYY/YYYY format e.g., 2024/2025)"
                        pattern="^\d{4}/\d{4}$"
                        title="Please enter a valid school year in format YYYY/YYYY (e.g., 2024/2025)">
                  <small class="form-text text-muted">Format should be YYYY/YYYY (e.g., 2024/2025)</small>
                </div>
                
                <button type="submit" name="<?php echo isset($Id) ? 'update' : 'save'; ?>" class="btn btn-<?php echo isset($Id) ? 'warning' : 'info'; ?>"> <?php echo isset($Id) ? 'Update' : 'Save'; ?> </button>
              </form>
            </div>
          </div>
          <div class="card mb-4">
            <div class="card-header">All School Year</div>
            <div class="table-responsive p-3">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>School Year</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Activate</th>
                    <th>Edit</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $query = "SELECT * FROM tblsessionterm ORDER BY dateCreated DESC";
                $rs = $conn->query($query);
                if ($rs->num_rows > 0) {
                    $sn = 1;
                    while ($row = $rs->fetch_assoc()) {
                        $isActive = $row['isActive'] == 1;
                        echo "<tr" . ($isActive ? " class='table-success'" : "") . ">
                                <td>{$sn}</td>
                                <td><strong>" . $row['sessionName'] . "</strong>" . ($isActive ? ' <span class="badge badge-success">Current</span>' : '') . "</td>
                                <td>" . ($isActive ? 
                                        '<span class="text-success"><i class="fas fa-check-circle"></i> Active</span>' : 
                                        '<span class="text-secondary">Inactive</span>') . "</td>
                                <td>{$row['dateCreated']}</td>
                                <td>" . (!$isActive ? "<a href='?action=activate&Id={$row['Id']}' class='btn btn-sm btn-success activate-btn' data-id='{$row['Id']}' title='Set as Active'><i class='fas fa-check'></i></a> " : "") . "</td>
                                <td><a href='?action=edit&Id={$row['Id']}' class='btn btn-sm btn-warning' title='Edit'><i class='fas fa-edit'></i></a></td>
                                <td>" . (!$isActive ? "<button class='btn btn-sm btn-danger delete-btn' data-id='{$row['Id']}' data-year='{$row['sessionName']}' title='Delete'><i class='fas fa-trash'></i></button>" : "<button class='btn btn-sm btn-danger' disabled title='Cannot delete active school year'><i class='fas fa-trash'></i></button>") . "</td>
                              </tr>";
                        $sn++;
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center text-danger'>No Record Found!</td></tr>";
                }
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <?php include "Includes/footer.php"; ?>
    </div>
  </div>

  <!-- Activation Confirmation Modal -->
  <div class="modal fade" id="activateModal" tabindex="-1" role="dialog" aria-labelledby="activateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-confirm" role="document">
      <div class="modal-content">
        <div class="modal-header flex-column">
          <div class="icon-box">
            <i class="fas fa-question-circle"></i>
          </div>
          <h4 class="modal-title w-100" id="activateModalLabel">Activate School Year</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p class="text-center">Are you sure you want to activate <strong id="yearToActivate"></strong> as the current school year?</p>
          <p class="text-center text-muted small">This will deactivate the currently active school year.</p>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <a href="#" id="confirmActivate" class="btn btn-success">Yes, Activate</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-confirm" role="document">
      <div class="modal-content">
        <div class="modal-header flex-column">
          <div class="icon-box">
            <i class="fas fa-trash text-danger"></i>
          </div>
          <h4 class="modal-title w-100" id="deleteModalLabel">Delete School Year</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p class="text-center">Are you sure you want to delete <strong id="yearToDelete"></strong>?</p>
          <p class="text-center text-danger">Warning: This will remove all attendance records associated with this school year!</p>
          <p class="text-center text-muted small">This action cannot be undone.</p>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <a href="#" id="confirmDelete" class="btn btn-danger">Yes, Delete</a>
        </div>
      </div>
    </div>
  </div>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Display toast messages
        if (<?php echo isset($_SESSION['toast']) ? 'true' : 'false'; ?>) {
            alert("<?php echo $_SESSION['toast']['message']; ?>");
            <?php unset($_SESSION['toast']); ?>
        }
        
        // Client-side validation for school year format
        document.getElementById('schoolYearForm').addEventListener('submit', function(event) {
            var sessionName = document.getElementById('sessionName').value;
            var pattern = /^\d{4}\/\d{4}$/;
            
            if (!pattern.test(sessionName)) {
                alert('Invalid school year format! Please use YYYY/YYYY format (e.g., 2024/2025)');
                event.preventDefault();
                return false;
            }
            
            var years = sessionName.split('/');
            var firstYear = parseInt(years[0]);
            var secondYear = parseInt(years[1]);
            
            if (secondYear - firstYear !== 1) {
                alert('Invalid school year! The second year should be one more than the first year (e.g., 2024/2025)');
                event.preventDefault();
                return false;
            }
        });

        // Modern confirmation dialog for activating school year
        const activateButtons = document.querySelectorAll('.activate-btn');
        
        activateButtons.forEach(button => {
          button.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Get the school year name from the row
            const row = this.closest('tr');
            const yearName = row.querySelector('td:nth-child(2)').textContent.trim().split(' ')[0];
            
            // Set the year name in the modal
            document.getElementById('yearToActivate').textContent = yearName;
            
            // Set the confirmation button's href
            document.getElementById('confirmActivate').href = this.href;
            
            // Show the modal
            $('#activateModal').modal('show');
          });
        });

        // Modern confirmation dialog for deleting school year
        const deleteButtons = document.querySelectorAll('.delete-btn');
        
        deleteButtons.forEach(button => {
          button.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Get data from button attributes
            const id = this.getAttribute('data-id');
            const yearName = this.getAttribute('data-year');
            
            // Set the year name in the modal
            document.getElementById('yearToDelete').textContent = yearName;
            
            // Set the confirmation button's href
            const deleteUrl = '?action=delete&Id=' + id;
            document.getElementById('confirmDelete').href = deleteUrl;
            
            // Show the modal
            $('#deleteModal').modal('show');
          });
        });
    });
  </script>
</body>
</html>