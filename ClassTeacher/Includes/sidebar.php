<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$userType = isset($_SESSION['userType']) ? $_SESSION['userType'] : '';
$using_default_password = isset($_SESSION['default_password']) && $_SESSION['default_password'] === true;
?>

<?php if ($using_default_password): ?>
<!-- Default Password Warning Banner -->
<div class="alert alert-danger text-center" style="margin-bottom: 0; border-radius: 0;">
  <strong>WARNING:</strong> You must change your default password before continuing.
</div>
<?php endif; ?>

<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
<?php if ($using_default_password): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // This function runs when the page has loaded
  // Disable all navigation links except Change Password
  document.querySelectorAll('#accordionSidebar a.nav-link:not([href="changePassword.php"])').forEach(function(link) {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      alert('Please change your default password before accessing other features.');
      window.location.href = 'changePassword.php';
    });
    link.classList.add('text-muted');
    link.style.cursor = 'not-allowed';
  });
});
</script>
<?php endif; ?>
  <a class="sidebar-brand d-flex align-items-center bg-info justify-content-center" href="index.php">
    <div class="sidebar-brand-text mx-3">DailyMark</div>
  </a>
  <hr class="sidebar-divider my-0">
  <li class="nav-item active">
    <?php if ($userType == 'Student') { ?>
      <a class="nav-link" href="student.php">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span>
      </a>
    <?php } elseif ($userType == 'Parent') { ?>
      <a class="nav-link" href="parent.php">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span>
      </a>
    <?php } else { ?>
      <a class="nav-link" href="index.php">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span>
      </a>
    <?php } ?>
  </li>

  <?php if ($userType != 'Student' && $userType != 'Parent') { ?>
    <hr class="sidebar-divider">
  <div class="sidebar-heading">
    Students
  </div>
    <li class="nav-item active">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrap2"
         aria-expanded="true" aria-controls="collapseBootstrap2">
        <i class="fas fa-user-graduate"></i>
        <span>Manage Students</span>
      </a>
      <div id="collapseBootstrap2" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Manage Students</h6>
          <a class="collapse-item" href="viewStudents.php">View Students</a>
        </div>
      </div>
    </li>
  <?php } ?>
  <hr class="sidebar-divider">
  <div class="sidebar-heading">
    Attendance
  </div>
  <li class="nav-item active">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrapcon"
       aria-expanded="true" aria-controls="collapseBootstrapcon">
      <i class="fa fa-calendar-alt"></i>
      <span>Manage Attendance</span>
    </a>
    <div id="collapseBootstrapcon" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Manage Attendance</h6>
        <?php if ($userType != 'Student' && $userType != 'Parent') { ?>
          <a class="collapse-item" href="takeAttendance.php">Take Attendance</a>
          <a class="collapse-item" href="viewAttendance.php">View Class Attendance</a>
        <?php } ?>
        <a class="collapse-item" href="viewStudentAttendance.php">View Student Attendance</a>
        <?php if ($userType != 'Student' && $userType != 'Parent') { ?>
          <a class="collapse-item" href="downloadRecord.php">Today's Report (xls)</a>
        <?php } ?>
      </div>
    </div>
  </li>
  <hr class="sidebar-divider">
  <div class="sidebar-heading">
    Account
  </div>
  <li class="nav-item active">
    <a class="nav-link" href="changePassword.php">
      <i class="fas fa-key"></i>
      <span>Change Password</span>
    </a>
  </li>
  <hr class="sidebar-divider">
</ul>
