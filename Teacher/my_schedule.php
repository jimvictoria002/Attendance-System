<?php
    include_once "../database.php";
    session_start();
    if(!isset($_SESSION['access'])){
        header("Location: ../login.php");
    }else{
        if($_SESSION['access']==="Student"){
            header("Location: ../Student/dashboard.php");
        }
    }
    $week = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My schedule</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../sched-style.css">
    <link rel="stylesheet" href="../animation.css">
    <link rel="shortcut icon" href="../img/my-favicon.png" type="image/png" sizes="64x64 32x32 24x24 16x16">
 </head>
<body>
    <nav class="nav">
        <div class="menu-container"></div>

                <div class="logo">
                    <div class="logo-container">
                        <a href="tchrs_db.php" class="child-logo">
                            <h1>AS</h1>
                            
                        </a>
                    </div>
                </div>
            
            <div class="drop" id="dropper">
                <div class="profile-container">
                        <div class="user-fullname" id="user-fullname">
                            <h4> <?php echo"{$_SESSION['teachers_firstname']} {$_SESSION['teachers_lastname']}";?> </h4>
                            <p class="year-section"><?php echo"Teacher ID: {$_SESSION['teachers_id']}"; ?> </p>
                        </div>

                        <img src="../img/user (4).png" alt="user-avatar" class="user-avatar">
                    </div>
                <div class="drop-down" id="drop-down">
                    <a href="my_schedule.php">My schedule</a>
                    <a href="">Change password</a>
                    <a href="../logout.php" class="logout">Logout</a>
                </div>
        </div>
    </nav>

    <div class="sched-main">
        <div class="my-sched-sign">
            <h1>My schedule</h1>
        </div>
        <div class="my-sched-tbl">
            
            <table>
                <tbody>
                    <?php
                    foreach ($week as $day) {
                    echo"<tr>";
                    echo "<th>{$day}</th>";
                    $sql = "SELECT * FROM `subjects_sched` INNER JOIN subjects ON subjects_sched.sub_id = subjects.sub_id INNER JOIN teachers ON subjects.teachers_id = teachers.teachers_id WHERE teachers.teachers_id = {$_SESSION['teachers_id']} AND subjects_sched.weeks = '$day' ORDER BY STR_TO_DATE(subjects_sched.start_time, '%h:%i %p')";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()){
                    ?>
                        <td style="white-space: nowrap;">
                            <p class="sub-code"><?php echo$row['sub_code'];?></p>
                            <p class="time-sched"><?php echo"{$row['start_time']} to {$row['end_time']}";?></p>
                            <p class="intructor"><?php echo"{$row['course']} {$row['year_level']} - {$row['section']}";?></p>
                        </td>
                    <?php
                    }
                    echo"<tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const dropper = document.getElementById('dropper');

        const todrop = document.getElementById('drop-down');

        const user = document.getElementById('user-fullname');

        

        dropper.addEventListener('click', function(){
            if(todrop.style.display === "block"){
                todrop.style.display = "none";
                user.style.color = "#ffffff";
            }else{
                todrop.style.display = "block";
                user.style.color = "#b1afaf";
            }
        });
    </script>
</body>
</html>