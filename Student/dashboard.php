<?php
    session_start();
    date_default_timezone_set('Asia/Manila');
    if(!isset($_SESSION['access'])){
        header("Location: ../login.php");
    }else{
        if($_SESSION['access']==="Teacher"){
            header("Location: ../Teacher/tchrs_db.php");
        }
    }
    $current_date = date('l, F j, Y');
    include_once "../database.php";
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../animation.css">
    <link rel="shortcut icon" href="../img/my-favicon.png" type="image/png" sizes="64x64 32x32 24x24 16x16">
</head>
<body>
    
    <nav class="nav">
    <div class="menu-container"></div>
        <div class="logo">
            <div class="logo-container">
                <a href="dashboard.php" class="child-logo">
                    <h1>AS</h1>
                </a>
            </div>
        </div>
        
        <div class="drop" id="dropper">
            <div class="profile-container">
                    <div class="user-fullname" id="user-fullname">
                        <h4> <?php echo"{$_SESSION['firstname']} {$_SESSION['lastname']}";?> </h4>
                        <p class="year-section"><?php echo"{$_SESSION['course']} {$_SESSION['year_level']}-{$_SESSION['section']}"; ?> </p>
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
    
    <div class="main-container">
        <div class="main">
            <h3 class="ats">Attendance System</h3>
            <h2 class="ec">E-Classrooms</h2>
            <div class="classroom-container">
                    <?php
                    $query = "SELECT * FROM subjects INNER JOIN teachers ON subjects.teachers_id = teachers.teachers_id WHERE subjects.course = '{$_SESSION['course']}' AND subjects.year_level = '{$_SESSION['year_level']}' AND subjects.section = '{$_SESSION['section']}'";
                    $result = $conn->query($query);
                    while($row = $result->fetch_assoc()){
                    ?>
                        <a href="classroom.php?sub_id=<?php echo"{$row['sub_id']}"?>&section=atd_today" class="classroom">
                            <div class="classroom-content">
                                <h1 class="course-code"><?php echo"{$row['sub_code']}";?></h1>
                                <p class="course-name"><?php echo"{$row['sub_name']}";?></p>
                                <p class="classroom-teacher"><?php echo"{$row['teachers_firstname']} {$row['teachers_lastname']} ";?></p>
                             </div>
                        </a>
                    <?php }?>
                
            </div>
        </div>

        <div class="sched-container">
            <div class="sched-today">
                <h2>Schedule Today</h2>
                <p><?php echo $current_date;?></p>
            </div>
            <div class="subject-sched">
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2">Course Code</th>
                            <th colspan="2">Time</th>
                            <th rowspan="2">Instructor</th>
                        </tr>
                        <tr>
                            <th>Start</th>
                            <th>End</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        
                        $query = "SELECT * FROM `subjects_sched` INNER JOIN subjects ON subjects_sched.sub_id = subjects.sub_id INNER JOIN teachers ON subjects.teachers_id = teachers.teachers_id WHERE subjects.year_level = {$_SESSION['year_level']} AND subjects.course = '{$_SESSION['course']}' AND subjects.section = '{$_SESSION['section']}' AND subjects_sched.weeks = '".date('l')."' ORDER BY STR_TO_DATE(subjects_sched.start_time, '%h:%i %p')";
                        $result = $conn->query($query);

                        while($row = $result->fetch_assoc()){
                        ?>
                        <tr>
                            <td><?php echo$row['sub_code'];?></td>
                            <td class="start-time"><?php echo"{$row['start_time']}";?></td>
                            <td class="end-time"><?php echo"{$row['end_time']}";?></td>
                            <td><?php echo"{$row['teachers_firstname']} {$row['teachers_lastname']}";?></td>
                        </tr>
                        <?php }?>
                        
                    </tbody>
                </table>
            </div>
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


