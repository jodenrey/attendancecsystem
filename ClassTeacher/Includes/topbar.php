<?php 
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include($_SERVER['DOCUMENT_ROOT'] . '/hamby/includes/dbcon.php');

// Default full name placeholder
$fullName = "";

// Ensure user is logged in
if (!empty($_SESSION['userType']) && !empty($_SESSION['userId'])) {
    // Get user type and ID from session
    $userType = $_SESSION['userType'];
    $userId = $_SESSION['userId'];

    // Prepare SQL query based on user type
    $query = "";
    if ($userType === 'Student') {
        $query = "SELECT firstName, lastName FROM tblstudents WHERE Id = ?";
    } elseif ($userType === 'Parent') {
        $query = "SELECT fn, mn, ln FROM tblparent WHERE id = ?";
    } elseif ($userType === 'Teacher') {
        $query = "SELECT firstName, lastName FROM tblclassteacher WHERE Id = ?";
    }

    // Execute query if it's valid
    if (!empty($query)) {
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch user data
            if ($row = $result->fetch_assoc()) {
                if ($userType === 'Parent') {
                    // Handle middle name if present
                    $fullName = $row['fn'] . " " . (!empty($row['mn']) ? $row['mn'] . " " : "") . $row['ln'];
                } else {
                    $fullName = $row['firstName'] . " " . $row['lastName'];
                }
            }
            $stmt->close();
        }
    }
}

?>

<!-- NAVIGATION BAR -->
<nav class="navbar navbar-expand navbar-light bg-info topbar mb-4 static-top">
    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <div class="text-white big" style="margin-left:100px;"></div>
    <ul class="navbar-nav ml-auto">
        <!-- SEARCH DROPDOWN -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-1 small"
                            placeholder="What do you want to look for?" aria-label="Search"
                            aria-describedby="basic-addon2" style="border-color: #3f51b5;">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- USER DROPDOWN -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="ml-2 d-none d-lg-inline text-white small">
                    <b>Welcome <?php echo htmlspecialchars($fullName); ?></b>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">
                    <i class="fas fa-power-off fa-fw mr-2 text-danger"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
