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
  $fn = $_POST['fn'];
  $mn = $_POST['mn'];
  $ln = $_POST['ln'];
  $emailAddress = $_POST['emailAddress'];
  $student = implode(',', $_POST['student']);
  $password = $_POST['password'];
  $dateCreated = date("Y-m-d H:i:s");

  $query = mysqli_query($conn, "SELECT * FROM tblparent WHERE emailAddress = '$emailAddress'");
  $ret = mysqli_fetch_array($query);

  if ($ret > 0) {
    setToastMessage('This Email Address Already Exists!', 'error');
  } else {
    $query = mysqli_query($conn, "INSERT INTO tblparent (fn, mn, ln, emailAddress, student, password, created_at) 
      VALUES ('$fn', '$mn', '$ln', '$emailAddress', '$student', '$password', '$dateCreated')");

    if ($query) {
      setToastMessage('Parent Created Successfully!', 'success');
    } else {
      setToastMessage('An error Occurred!', 'error');
    }
  }
  header("Location: createParent.php");
  exit();
}

//--------------------EDIT------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
  $Id = $_GET['Id'];

  $query = mysqli_query($conn, "SELECT * FROM tblparent WHERE id ='$Id'");
  $row = mysqli_fetch_array($query);

  //------------UPDATE-----------------------------
  if (isset($_POST['update'])) {
    $fn = $_POST['fn'];
    $mn = $_POST['mn'];
    $ln = $_POST['ln'];
    $emailAddress = $_POST['emailAddress'];
    $student = implode(',', $_POST['student']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "UPDATE tblparent SET fn='$fn', mn='$mn', ln='$ln', emailAddress='$emailAddress', student='$student', password='$password', updated_at=NOW() WHERE id='$Id'");

    if ($query) {
      setToastMessage('Parent Updated Successfully!', 'success');
    } else {
      setToastMessage('An error Occurred!', 'error');
    }
    header("Location: createParent.php");
    exit();
  }
}

//--------------------------------DELETE------------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
  $Id = $_GET['Id'];

  $query = mysqli_query($conn, "DELETE FROM tblparent WHERE id='$Id'");

  if ($query) {
    setToastMessage('Parent Deleted Successfully!', 'success');
  } else {
    setToastMessage('An error Occurred!', 'error');
  }
  header("Location: createParent.php");
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
            <h1 class="h3 mb-0 text-gray-800">Create Parents</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Parents</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-info">Create Parents</h6>
                    <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                <form method="post">
                  <div class="form-group row mb-3">
                    <div class="col-xl-6">
                      <label class="form-control-label">First Name<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" name="fn" id="firstName" value="<?php echo isset($row['fn']) ? $row['fn'] : ''; ?>" required>
                    </div>
                    <div class="col-xl-6">
                      <label class="form-control-label">Middle Name</label>
                      <input type="text" class="form-control" name="mn" id="middleName" value="<?php echo isset($row['mn']) ? $row['mn'] : ''; ?>">
                    </div>
                  </div>
                  <div class="form-group row mb-3">
                    <div class="col-xl-6">
                      <label class="form-control-label">Last Name<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" name="ln" id="lastName" value="<?php echo isset($row['ln']) ? $row['ln'] : ''; ?>" required>
                    </div>
                    <div class="col-xl-6">
                      <label class="form-control-label">Email Address</label>
                      <input type="email" class="form-control" name="emailAddress" id="emailAddress" value="<?php echo isset($row['emailAddress']) ? $row['emailAddress'] : ''; ?>">
                    </div>
                  </div>
                  <!-- Search Student Section -->
                  <div class="form-group row mb-3">
                    <div class="col-xl-12">
                      <label class="form-control-label">Student<span class="text-danger ml-2">*</span></label>
                      
                      <!-- Advanced Student Search Section -->
                      <div class="card mb-3">
                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>Search by Name or ID Number</label>
                                <input type="text" class="form-control" id="studentSearch" placeholder="Enter student name or ID number...">
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>Filter by Grade</label>
                                <select id="classFilter" class="form-control">
                                  <option value="">All Grades</option>
                                  <!-- Will be populated via JavaScript -->
                                </select>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>Filter by Section</label>
                                <select id="classArmFilter" class="form-control">
                                  <option value="">All Sections</option>
                                  <!-- Will be populated via JavaScript -->
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <button type="button" class="btn btn-info btn-sm" onclick="searchStudent()">
                                <i class="fas fa-search"></i> Search Students
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Search Results Section -->
                      <div id="searchResults" class="card mb-3" style="display: none;">
                        <div class="card-header py-2">
                          <h6 class="m-0 font-weight-bold text-info">Search Results <span id="resultCount" class="badge badge-info">0</span></h6>
                        </div>
                        <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                          <div id="studentResultList"></div>
                        </div>
                      </div>
                      
                      <!-- Selected Students Section -->
                      <div class="card">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                          <h6 class="m-0 font-weight-bold text-info">Selected Students</h6>
                          <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearSelectedStudents()">
                            <i class="fas fa-times"></i> Clear All
                          </button>
                        </div>
                        <div class="card-body">
                          <div id="selectedStudentsList" class="mb-2">
                            <!-- Selected students will be shown here -->
                          </div>
                          <select name="student[]" id="selectedStudents" class="form-control d-none" multiple required>
                            <?php
                            $selectedStudents = isset($row['student']) ? explode(',', $row['student']) : [];
                            if (!empty($selectedStudents)) {
                              foreach ($selectedStudents as $studentId) {
                                $query = "SELECT s.admissionNumber, s.firstName, s.lastName, c.className, ca.classArmName 
                                        FROM tblstudents s
                                        LEFT JOIN tblclass c ON s.classId = c.Id
                                        LEFT JOIN tblclassarms ca ON s.classArmId = ca.Id
                                        WHERE s.admissionNumber = '$studentId'";
                                
                                $result = $conn->query($query);
                                if ($result && $student = $result->fetch_assoc()) {
                                  $displayName = $student['firstName'] . ' ' . $student['lastName'] . ' (' . $student['admissionNumber'];
                                  if (!empty($student['className']) && !empty($student['classArmName'])) {
                                    $displayName .= ' - ' . $student['className'] . ' ' . $student['classArmName'];
                                  }
                                  $displayName .= ')';
                                  echo '<option value="'.$student['admissionNumber'].'" selected>'.$displayName.'</option>';
                                }
                              }
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row mb-3">
                    <div class="col-xl-6">
                      <label class="form-control-label">Password<span class="text-danger ml-2">*</span></label>
                      <input type="password" class="form-control" name="password" id="password" value="<?php echo isset($row['password']) ? $row['password'] : ''; ?>" required>
                    </div>
                  </div>
                  <button type="submit" name="<?php echo isset($row) ? 'update' : 'save'; ?>" class="btn btn-info"><?php echo isset($row) ? 'Update' : 'Save'; ?></button>
                </form>

                </div>
              </div>
       <!-- Input Group -->
      <div class="row">
        <div class="col-lg-12">
        <div class="card mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-info">All Parents</h6>
          </div>
          <div class="table-responsive p-3">
            <table class="table align-items-center table-flush table-hover" id="dataTableHover">
              <thead class="thead-light">
           <tr>
             <th>#</th>
             <th>First Name</th>
             <th>Middle Name</th>
             <th>Last Name</th>
             <th>Email Address</th>
             <th>Student</th>
             <th>Date Created</th>
             <th>Edit</th>
             
           </tr>
              </thead>
          
              <tbody>

            <?php
           $query = "SELECT * FROM tblparent";
           $rs = $conn->query($query);
           $num = $rs->num_rows;
           $sn=0;
           if($num > 0)
           { 
             while ($rows = $rs->fetch_assoc())
               {
             $sn = $sn + 1;
            echo"
              <tr>
                <td>".$sn."</td>
                <td>".$rows['fn']."</td>
                <td>".$rows['mn']."</td>
                <td>".$rows['ln']."</td>
                <td>".$rows['emailAddress']."</td>
                <td>".$rows['student']."</td>
                <td>".$rows['created_at']."</td>
                <td><a href='?action=edit&Id=".$rows['id']."'>Edit</a></td>
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

  <script>
function searchStudent() {
  const searchTerm = document.getElementById('studentSearch').value;
  const searchResults = document.getElementById('searchResults');
  const selectedStudents = document.getElementById('selectedStudents');
  
  if (searchTerm.length < 2) {
    searchResults.style.display = 'none';
    return;
  }

  // Make AJAX call to search students
  fetch(`ajaxSearchStudents.php?term=${encodeURIComponent(searchTerm)}`)
    .then(response => response.json())
    .then(data => {
      searchResults.innerHTML = '';
      if (data.length > 0) {
        data.forEach(student => {
          const div = document.createElement('div');
          div.className = 'p-2 border-bottom cursor-pointer';
          div.innerHTML = `${student.firstName} ${student.lastName}`;
          div.onclick = () => {
            // Check if student is already selected
            const option = Array.from(selectedStudents.options).find(opt => opt.value === student.admissionNumber);
            if (!option) {
              const newOption = new Option(`${student.firstName} ${student.lastName}`, student.admissionNumber);
              selectedStudents.add(newOption);
            }
            searchResults.style.display = 'none';
            document.getElementById('studentSearch').value = '';
          };
          searchResults.appendChild(div);
        });
        searchResults.style.display = 'block';
      } else {
        searchResults.innerHTML = '<div class="p-2 text-danger">No students found with that name</div>';
        searchResults.style.display = 'block';
      }
    })
    .catch(error => {
      console.error('Error:', error);
      searchResults.innerHTML = '<div class="p-2 text-danger">Error searching students</div>';
      searchResults.style.display = 'block';
    });
}

// Close search results when clicking outside
document.addEventListener('click', function(event) {
  const searchResults = document.getElementById('searchResults');
  const studentSearch = document.getElementById('studentSearch');
  
  if (!searchResults.contains(event.target) && event.target !== studentSearch) {
    searchResults.style.display = 'none';
  }
});
</script>

<script>
// Global variables
let timer = null;

// Document ready function
document.addEventListener('DOMContentLoaded', function() {
  // Load class options for filtering
  loadClassOptions();
  
  // Setup event listeners
  document.getElementById('studentSearch').addEventListener('keyup', function(e) {
    clearTimeout(timer);
    timer = setTimeout(function() {
      searchStudent();
    }, 500); // Debounce search
  });
  
  document.getElementById('classFilter').addEventListener('change', function() {
    loadClassArmOptions(this.value);
    searchStudent();
  });
  
  document.getElementById('classArmFilter').addEventListener('change', searchStudent);
  
  // Initialize selected students display
  updateSelectedStudentsDisplay();
});

// Load class options for filtering dropdown
function loadClassOptions() {
  fetch('ajaxSearchStudents.php?getClasses=1')
    .then(response => response.json())
    .then(data => {
      const classFilter = document.getElementById('classFilter');
      data.forEach(classItem => {
        const option = document.createElement('option');
        option.value = classItem.id;
        option.textContent = classItem.name;
        classFilter.appendChild(option);
      });
    })
    .catch(error => console.error('Error loading classes:', error));
}

// Load class arm options based on selected class
function loadClassArmOptions(classId) {
  const classArmFilter = document.getElementById('classArmFilter');
  
  // Reset options
  classArmFilter.innerHTML = '<option value="">All Sections</option>';
  
  if (!classId) return;
  
  fetch(`ajaxSearchStudents.php?getClassArms=1&classId=${classId}`)
    .then(response => response.json())
    .then(data => {
      data.forEach(armItem => {
        const option = document.createElement('option');
        option.value = armItem.id;
        option.textContent = armItem.name;
        classArmFilter.appendChild(option);
      });
    })
    .catch(error => console.error('Error loading class sections:', error));
}

// Search students with filters
function searchStudent() {
  const searchTerm = document.getElementById('studentSearch').value.trim();
  const classId = document.getElementById('classFilter').value;
  const classArmId = document.getElementById('classArmFilter').value;
  const searchResults = document.getElementById('searchResults');
  const resultsList = document.getElementById('studentResultList');
  const resultCount = document.getElementById('resultCount');
  
  // Hide results if search term is empty and no filters
  if (searchTerm.length < 1 && !classId && !classArmId) {
    searchResults.style.display = 'none';
    return;
  }

  // Show loading state
  resultsList.innerHTML = '<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
  searchResults.style.display = 'block';

  // Build query parameters
  let params = new URLSearchParams();
  params.append('term', searchTerm);
  if (classId) params.append('classId', classId);
  if (classArmId) params.append('classArmId', classArmId);

  // Make AJAX call to search students
  fetch(`ajaxSearchStudents.php?${params.toString()}`)
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      resultsList.innerHTML = '';
      resultCount.textContent = data.length;
      
      if (data.length > 0) {
        const list = document.createElement('div');
        list.className = 'list-group list-group-flush';
        
        data.forEach(student => {
          const item = document.createElement('button');
          item.type = 'button';
          item.className = 'list-group-item list-group-item-action';
          
          // Create student display with proper formatting
          const idBadge = document.createElement('span');
          idBadge.className = 'badge badge-primary mr-2';
          idBadge.textContent = student.admissionNumber;
          
          const nameSpan = document.createElement('span');
          nameSpan.innerHTML = `<strong>${student.firstName} ${student.lastName}</strong>`;
          if (student.otherName) {
            nameSpan.innerHTML += ` ${student.otherName}`;
          }
          
          const classBadge = document.createElement('span');
          classBadge.className = 'badge badge-info float-right';
          classBadge.textContent = `${student.className} ${student.classArmName}`;
          
          item.appendChild(idBadge);
          item.appendChild(nameSpan);
          item.appendChild(classBadge);
          
          item.onclick = () => selectStudent(student);
          
          list.appendChild(item);
        });
        
        resultsList.appendChild(list);
      } else {
        resultsList.innerHTML = '<div class="alert alert-warning m-3">No students found matching your search.</div>';
      }
    })
    .catch(error => {
      console.error('Error:', error);
      resultsList.innerHTML = '<div class="alert alert-danger m-3">Error searching for students. Please try again.</div>';
    });
}

// Select a student from search results
function selectStudent(student) {
  const selectedStudents = document.getElementById('selectedStudents');
  
  // Check if student is already selected
  let isAlreadySelected = false;
  for (let i = 0; i < selectedStudents.options.length; i++) {
    if (selectedStudents.options[i].value === student.admissionNumber) {
      isAlreadySelected = true;
      break;
    }
  }
  
  if (!isAlreadySelected) {
    const option = new Option(student.displayName, student.admissionNumber, false, true);
    selectedStudents.add(option);
    updateSelectedStudentsDisplay();
  }
  
  // Clear search and hide results
  document.getElementById('studentSearch').value = '';
  document.getElementById('searchResults').style.display = 'none';
}

// Update the visual display of selected students
function updateSelectedStudentsDisplay() {
  const selectedStudents = document.getElementById('selectedStudents');
  const selectedStudentsList = document.getElementById('selectedStudentsList');
  
  selectedStudentsList.innerHTML = '';
  
  if (selectedStudents.options.length === 0) {
    selectedStudentsList.innerHTML = '<div class="alert alert-info">No students selected yet</div>';
    return;
  }
  
  for (let i = 0; i < selectedStudents.options.length; i++) {
    const option = selectedStudents.options[i];
    const badge = document.createElement('div');
    badge.className = 'badge badge-secondary p-2 m-1';
    badge.style.fontSize = '0.9rem';
    badge.innerHTML = `
      ${option.text}
      <button type="button" class="close ml-2" aria-label="Remove" 
        onclick="removeSelectedStudent('${option.value}')">
        <span aria-hidden="true">&times;</span>
      </button>
    `;
    selectedStudentsList.appendChild(badge);
  }
}

// Remove a selected student
function removeSelectedStudent(admissionNumber) {
  const selectedStudents = document.getElementById('selectedStudents');
  
  for (let i = 0; i < selectedStudents.options.length; i++) {
    if (selectedStudents.options[i].value === admissionNumber) {
      selectedStudents.remove(i);
      break;
    }
  }
  
  updateSelectedStudentsDisplay();
}

// Clear all selected students
function clearSelectedStudents() {
  const selectedStudents = document.getElementById('selectedStudents');
  selectedStudents.innerHTML = '';
  updateSelectedStudentsDisplay();
}
</script>
</body>

</html>