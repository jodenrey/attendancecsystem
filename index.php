<?php 
include 'Includes/dbcon.php';
session_start();

  if(isset($_POST['login'])){

    $userType = $_POST['userType'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password = md5($password);

    if($userType == "Administrator"){

      $query = "SELECT * FROM tbladmin WHERE emailAddress = '$username' AND password = '$password'";
      $rs = $conn->query($query);
      $num = $rs->num_rows;
      $rows = $rs->fetch_assoc();

      if($num > 0){

        $_SESSION['userId'] = $rows['Id'];
        $_SESSION['firstName'] = $rows['firstName'];
        $_SESSION['lastName'] = $rows['lastName'];
        $_SESSION['emailAddress'] = $rows['emailAddress'];

        echo "<script type = \"text/javascript\">
        window.location = (\"Admin/index.php\")
        </script>";
      }

      else{

        echo "<div class='alert alert-danger' role='alert'>
        Invalid Username/Password!
        </div>";

      }
    }
    else if($userType == "ClassTeacher"){

      $query = "SELECT * FROM tblclassteacher WHERE emailAddress = '$username' AND password = '$password'";
      $rs = $conn->query($query);
      $num = $rs->num_rows;
      $rows = $rs->fetch_assoc();

      if($num > 0){

        $_SESSION['userId'] = $rows['Id'];
        $_SESSION['firstName'] = $rows['firstName'];
        $_SESSION['lastName'] = $rows['lastName'];
        $_SESSION['emailAddress'] = $rows['emailAddress'];
        $_SESSION['classId'] = $rows['classId'];
        $_SESSION['classArmId'] = $rows['classArmId'];
        $_SESSION['userType'] = 'Teacher';
        
        // Check if using default password (pass123)
        $defaultPass = md5("pass123");
        if($rows['password'] == $defaultPass){
          $_SESSION['default_password'] = true;
        }

        echo "<script type = \"text/javascript\">
        window.location = (\"ClassTeacher/index.php\")
        </script>";
      }

      else{

        echo "<div class='alert alert-danger' role='alert'>
        Invalid Username/Password!
        </div>";

      }
    }
    else if($userType == "Parent"){

      $query = "SELECT * FROM tblparent WHERE emailAddress = '$username'  AND password = '".$_POST['password']."'";
      $rs = $conn->query($query);
      $num = $rs->num_rows;
      $rows = $rs->fetch_assoc();

      if($num > 0){

        $_SESSION['userId'] = $rows['id'];
        $_SESSION['firstName'] = $rows['fn'];
        $_SESSION['lastName'] = $rows['ln'];
        $_SESSION['emailAddress'] = $rows['emailAddress'];
        $_SESSION['userType'] = $userType;

        echo "<script type = \"text/javascript\">
        window.location = (\"ClassTeacher/parent.php\")
        </script>";
      }

      else{

        echo "<div class='alert alert-danger' role='alert'>
        Invalid Username/Password!
        </div>";

      }
    }
    else if($userType == "Student"){

      $query = "SELECT * FROM tblstudents WHERE admissionNumber = '$username' AND password = '".$_POST['password']."'";
      $rs = $conn->query($query);
      $num = $rs->num_rows;
      $rows = $rs->fetch_assoc();

      if($num > 0){
        $_SESSION['userId'] = $rows['Id'];
        $_SESSION['firstName'] = $rows['firstName'];
        $_SESSION['lastName'] = $rows['lastName'];
        $_SESSION['admissionNumber'] = $rows['admissionNumber'];
        $_SESSION['classId'] = $rows['classId'];
        $_SESSION['classArmId'] = $rows['classArmId'];
        $_SESSION['userType'] = $userType;
        
        // Check if using default password (12345)
        if($rows['password'] == '12345'){
          $_SESSION['default_password'] = true;
        }

        echo "<script type = \"text/javascript\">
        window.location = (\"ClassTeacher/student.php\")
        </script>";
      } 
      else {

        echo "<div class='alert alert-danger' role='alert'>
        Invalid Admission Number/Password!
        </div>";

      }

    }
    else{

        echo "<div class='alert alert-danger' role='alert'>
        Invalid Username/Password!
        </div>";

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
    <title>Daily Mark - Login</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-login" style="background-image: url('img/logo/loral1.jpe00g');">
    <!-- Login Content -->
    <div class="container-login">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-form">
                                    <h2 align="center">DailyMark</h2>
                                    <div class="text-center">
                                    <h6>Advance Attendance Solution</h6>
                                        <br><br>
                                        <h1 class="h4 text-gray-900 mb-4">Login</h1>
                                    </div>
                                    <form class="user" method="Post" action="">
                                        <div class="form-group">
                                            <select required name="userType" class="form-control mb-3">
                                                <option value="">--Select User Roles--</option>
                                                <option value="Administrator">Administrator</option>
                                                <option value="ClassTeacher">ClassTeacher</option>
                                                <option value="Student">Student</option>
                                                <option value="Parent">Parent</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" required name="username" id="exampleInputEmail" placeholder="Enter Email Address / Admission Number">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" required class="form-control" id="exampleInputPassword" placeholder="Enter Password">
                                        </div>
                                        
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-info btn-block" value="Login" name="login" />
                                        </div>
                                    </form>

                                   
                                   



                                    <div class="text-center">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Login Content -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
</body>

</html>