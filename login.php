<?php
    
    session_start();
    include_once "database.php";
    if(isset($_SESSION['access'])){
        if($_SESSION['access'] === "Student"){
            header("Location: Student/dashboard.php");
        }else if($_SESSION['access'] === "Teacher"){
            header("Location: Teacher/tchrs_db.php");
        }
    }

    if(isset($_POST['login-button'])){
        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = "SELECT * FROM users  WHERE users.username = '$username' AND users.password = '$password'";

        $result = $conn->query($query);
        
        if($result->num_rows == 1){
            $row = $result->fetch_assoc();
            
            if($row['access'] === "Student"){
                $query = "SELECT * FROM students INNER JOIN users ON students.std_id = users.username WHERE users.user_id = {$row['user_id']}";
                $result = $conn->query($query);

                $row = $result->fetch_assoc();

                foreach($row as $key => $value){
                    $_SESSION[$key] = $value;
                }
                $_SESSION['access'] = "Student";
                header("Location: Student/dashboard.php");

            }else if($row['access'] === "Teacher"){
                $query = "SELECT * FROM teachers INNER JOIN users ON teachers.teachers_id = users.username WHERE users.user_id = {$row['user_id']}";
                $result = $conn->query($query);

                $row = $result->fetch_assoc();

                foreach($row as $key => $value){
                    $_SESSION[$key] = $value;
                }
                $_SESSION['access'] = "Teacher";
                header("Location: Teacher/tchrs_db.php");
            }
        }else{
            header("Location: login.php?error=Account not found");
        }


    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System | Login</title>
    <link rel="shortcut icon" href="img/my-favicon.png" type="image/png" sizes="64x64 32x32 24x24 16x16">
    <link rel="stylesheet" href="login-style.css">
    <link rel="stylesheet" href="animation.css">
</head>
<body>
    <div class="main-login-form">
        <div class="ams-logo">
            <h1>AS</h1>
            <h4><span class="as">A</span>ttendance <span class="as">S</span>ystem</h4>
        </div>
        <form action="login.php" method="post" class="login-form">
            <h1 class="login">Login</h1>
            <?php  if(isset($_GET['error'])){
                echo'<p class="error">'.$_GET['error'].'</p>';
            } ?>
            <div class="label"><label for="username">Username</label></div>
            <input class="text-field" type="text" id="username" placeholder="Username" name="username" required>
            <div class="label"><label for="password">Password</label></div>
            <input class="text-field" type="password" id="password" placeholder="Password" name="password" required>
            <input type="submit" class="login-btn" name="login-button" value="Login">
            <a href="" class="forget-pass">Forgot password?</a>
        </form>
    </div>
    
</body>
</html>
