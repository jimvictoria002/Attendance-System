<?php
    session_start();
    date_default_timezone_set('Asia/Manila');
    include_once "../database.php";
   
    $sql = "SELECT * FROM subjects WHERE sub_id = {$_GET['sub_id']}";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    if($row['course'] != $_SESSION['course'] || $row['year_level'] != $_SESSION['year_level'] || $row['section'] != $_SESSION['section']){
        header("Location: dashboard.php");
    }

    if(!isset($_GET['sub_id'])){
        header("Location: dashboard.php");
    }
    if(!isset($_SESSION['access'])){
        header("Location: ../login.php");
    }else{
        if($_SESSION['access']==="Teacher"){
            header("Location: ../Teacher/tchrs_db.php");
        }
    }
    
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
    <nav class="classroom-nav">
        <ul class="nav-bar">
            <div class="logo">
                <div class="logo-container">
                    <a href="dashboard.php" class="child-logo">
                        <h1>AS</h1>
                    </a>
                </div>
            </div>
            <?php
            if($_GET['section'] === 'atd_today'){
            ?>
                <li class="liactive"><a href="classroom.php?sub_id=<?php echo$_GET['sub_id'];?>&section=atd_today" class="btn active">Today </a></li>
                <li><a href="classroom.php?sub_id=<?php echo$_GET['sub_id'];?>&section=atd_record" class="btn">Record</a></li>
            <?php
            }else if($_GET['section'] === 'atd_record'){
            ?>
                <li><a href="classroom.php?sub_id=<?php echo$_GET['sub_id'];?>&section=atd_today" class="btn">Today </a></li>
                <li class="liactive"><a href="classroom.php?sub_id=<?php echo$_GET['sub_id'];?>&section=atd_record" class="btn active">Record</a></li>
            <?php
            }else{
                header("Location: dashboard.php");
            }
            ?>
            
        </ul>
    </nav>
    <div class="attendance-container">
        <?php
        if($_GET['section']==='atd_record'){
        ?>
        <table class="std-record-tbl">
            <thead>
                <tr>
                    <th rowspan="2" class="th-student">Day</th>
                    <th colspan="2" class="th-student">Time</th>
                    <th rowspan="2" class="th-student">Date</th>
                    <th rowspan="2" class="th-student">Status</th>
                </tr>
                <tr>
                    <th class="th-student">Start</th>
                    <th class="th-student">End</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT * FROM `std_attendance` INNER JOIN subjects ON std_attendance.sub_id = subjects.sub_id INNER JOIN subjects_sched ON std_attendance.sched_id = subjects_sched.sched_id INNER JOIN students ON std_attendance.std_id = students.std_id WHERE std_attendance.std_id = {$_SESSION['std_id']} AND std_attendance.sub_id ={$_GET['sub_id']} ORDER BY STR_TO_DATE(std_attendance.date, '%Y-%m-%e') desc, STR_TO_DATE(subjects_sched.start_time, '%h:%i %p')";
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()){
            ?>
                <tr>
                    <td><?php echo$row['weeks'];?></td>
                    <td><?php echo$row['start_time'];?></td>
                    <td><?php echo$row['end_time'];?></td>
                    <td><p class="date"><?php echo$row['date'];?></p></td>
                    <td class="record-stat-con">
                        <?php
                        if($row['status'] == 'Late'){
                            echo'<p class="'.$row['status'].'">'.$row['status'].'</p>';
                        }else if($row['status'] == 'Present'){
                            echo'<p class="'.$row['status'].'">'.$row['status'].'</p>';
                        }else if($row['status'] == 'Absent'){
                            echo'<p class="'.$row['status'].'">'.$row['status'].'</p>';
                        }else if($row['status'] == 'Excused'){
                            echo'<p class="'.$row['status'].'">'.$row['status'].'</p>';
                        }
                        ?>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
        

        
        <?php
        }else if($_GET['section']==='atd_today'){
            $sql = "SELECT * FROM `subjects_sched` WHERE subjects_sched.sub_id = {$_GET['sub_id']} AND subjects_sched.weeks = '".date('l')."'";
            $result = $conn->query($sql);
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                ?>
                    <div class="attendance-content">
                        <h1 class="atd-content" id="date-tod"><?php echo date('l, F j, Y');?></h1>
                        <h3 class="atd-content" id="str-end"><?php echo"{$row['start_time']} to {$row['end_time']}";?></h3>
                        <?php
                        $sql1 = "SELECT * FROM `std_attendance` WHERE std_attendance.std_id = {$_SESSION['std_id']} AND std_attendance.sub_id = {$_GET['sub_id']} AND std_attendance.date = '".date('Y-m-d')."' AND std_attendance.sched_id = {$row['sched_id']}";
                        $result1 = $conn->query($sql1);
                        if($result1->num_rows > 0){
                            $row1 = $result1->fetch_assoc();
                            echo'<p class="today-'.$row1['status'].'">'.$row1['status'].'</p>';
                        }else{
                            echo'<p class="not-set">Your attendance has not yet been set.</p>';
                        }
                        ?>
                    </div>
                <?php
                }
            }else{
                ?>
                <div class="attendance-content">
                    <h1 class="atd-content" id="date-tod"><?php echo date('l, F j, Y');?></h1>
                    <p class="atd-content" id="no-meet">You have no meeting today</p>
                </div>
                <?php
            }
        ?>
            
            <?php
            
            ?>
        <?php
        }
        ?>
    </div>
</body>
</html>

