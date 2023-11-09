<?php
    session_start();
    if(!isset($_SESSION['access'])){
        header("Location: ../login.php");
    }else{
        if($_SESSION['access']==="Student"){
            header("Location: ../Student/classroom.php");
        }
    }
    include_once "../database.php";
    date_default_timezone_set('Asia/Manila');
    $sql = "SELECT * FROM subjects WHERE sub_id = {$_GET['sub_id']}";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if($_SESSION['teachers_id'] != $row['teachers_id']){
        header("Location: tchrs_db.php");
    }
    
    $currentDate = date("F j, Y");

    if(isset($_POST['submit-btn'])){

        $sql = "SELECT * FROM students INNER JOIN subjects ON students.course = subjects.course AND students.year_level = subjects.year_level AND students.section = subjects.section WHERE subjects.sub_id = {$_GET['sub_id']} ORDER BY students.lastname,students.firstname";
                
        $result = $conn->query($sql);

        while($row = $result->fetch_assoc()){
            $sql1 = "INSERT INTO std_attendance (status, std_id, sub_id, date, keyword) VALUES ('Absent',{$row['std_id']}, {$_GET['sub_id']}, '".date('Y-m-d')."', '{$keyword}')";
            $conn->query($sql1);
        }

        header("Location: tchrs_classroom.php?sub_id=".$_GET['sub_id']."&section=atd_today");

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
    <!-- NAV BAR -->
    <nav class="classroom-nav">
        <ul class="nav-bar" id="nav-bar">
            <div class="logo">
                <div class="logo-container">
                    <a href="tchrs_db.php" class="child-logo">
                        <h1>AS</h1>
                    </a>
                </div>
            </div>
            <?php
                if($_GET['section'] === 'atd_today'){
                    echo'
                    <li class="liactive"><a href="tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today" class="btn active">Today</a></li>
                    <li><a href="tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=std_atd" class="btn">Record</a></li>
                    <li><a href="tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=std" class="btn">Students</a></li>
                    ';
                } else if($_GET['section'] === 'std_atd'){
                    echo'
                    <li><a href="tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today" class="btn">Today</a></li>
                    <li class="liactive"><a href="tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=std_atd" class="btn active">Record</a></li>
                    <li><a href="tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=std" class="btn">Students</a></li>
                    ';
                }else if($_GET['section'] === 'std'){
                    echo'
                    <li><a href="tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today" class="btn">Today</a></li>
                    <li><a href="tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=std_atd" class="btn">Record</a></li>
                    <li class="liactive"><a href="tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=std" class="btn active">Students</a></li>
                    ';
                }else{
                    header("Location: tchrs_db.php");
                }
            ?>
        </ul>
    </nav>

    <div class="tc-main">
        <div class="main-child">
            <?php if($_GET['section'] === 'atd_today'){?>    
                <div class="today-container">
                    <?php
                    $query = "SELECT * FROM `subjects_sched` INNER JOIN subjects ON subjects_sched.sub_id = subjects.sub_id INNER JOIN teachers ON subjects.teachers_id = teachers.teachers_id WHERE subjects_sched.sub_id = {$_GET['sub_id']} AND subjects_sched.weeks = '".date('l')."'";
                    $result = $conn->query($query);
                    $meetingCount = $result->num_rows;
                    $meetings = "meeting";
                    if($meetingCount > 1){
                        $meetings = "meetings";
                    }else if($meetingCount < 1){
                        $meetingCount = 'no';
                    ?><h1 id="date-tod-tc"><?php echo date('l, F j, Y');?></h1>
                    <?php
                    $sql = "SELECT * FROM subjects WHERE subjects.sub_id = {$_GET['sub_id']}";
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    ?>
                    <p class="meet-today">you have <?php echo$meetingCount." ".$meetings;?> in <?php echo"{$row['course']} {$row['year_level']}-{$row['section']} ";?> today</p>
                    <?php
                    }
                    ?>
                </div>
                <div class="attendance-container">
                    <!-- VIEWING ATTENDACE TABLE -->
                    <?php
                    if(isset($_GET['sched_id']) && $_GET['action'] == "view"){
                    ?>
                    <div class="search-container">
                        <form action="<?php echo'../search.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$_GET['sched_id'].'&action=view';?>" method="post">
                            <input type="text" name="search" placeholder="Search" style="text-align: center;">
                            <input type="submit" class="search-view" value="" name="btn-search">
                        </form>
                    </div>
                    <table>
                            <thead>
                                <tr>
                                    <th>Lastname</th>
                                    <th>Firstname</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ATTENDANCE TODAY SEARCH VIEW TABLE -->
                                <?php
                                if(isset($_GET['search'])){
                                    $sql = "SELECT * FROM `std_attendance` INNER JOIN students ON std_attendance.std_id = students.std_id WHERE std_attendance.sched_id = {$_GET['sched_id']} AND std_attendance.sub_id = {$_GET['sub_id']} AND std_attendance.date = '".date("Y-m-d")."' AND (students.lastname LIKE '%{$_GET['search']}%' OR students.firstname LIKE '%{$_GET['search']}%') ORDER BY students.lastname, students.lastname";
                                    $result = $conn->query($sql);
                                    while($row = $result->fetch_assoc()){
                                ?>
                                <tr>
                                    <td class="name"><?php echo"{$row['lastname']}";?></td>
                                    <td class="name"><?php echo"{$row['firstname']}";?></td>
                                    <td class="name"><?php echo"{$row['gender']}";?></td>
                                    <td class="stat-container">
                                        <?php
                                        if($row['status'] == 'Present'){
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Present" id="<?php echo"{$row['std_id']}Present"?>" checked>
                                            <label onclick="updateSearch(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Present';?>',<?php echo $row['attendance_id'];?>,'search','<?php echo$_GET['search'];?>')" class="present" for="<?php echo "{$row['std_id']}Present" ?>">Present</label>
                                            <?php
                                        }else{
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Present" id="<?php echo"{$row['std_id']}Present"?>">
                                            <label onclick="updateSearch(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Present';?>',<?php echo $row['attendance_id'];?>,'search','<?php echo$_GET['search'];?>')" class="present" for="<?php echo "{$row['std_id']}Present" ?>">Present</label>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        if($row['status'] == 'Absent'){
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Absent" id="<?php echo"{$row['std_id']}Absent"?>" checked>
                                            <label onclick="updateSearch(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Absent';?>',<?php echo $row['attendance_id'];?>,'search','<?php echo$_GET['search'];?>')" class="absent" for="<?php echo"{$row['std_id']}Absent"?>">Absent</label>
                                            <?php
                                        }else{
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Absent" id="<?php echo"{$row['std_id']}Absent"?>">
                                            <label onclick="updateSearch(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Absent';?>',<?php echo $row['attendance_id'];?>,'search','<?php echo$_GET['search'];?>')" class="absent" for="<?php echo"{$row['std_id']}Absent"?>">Absent</label>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        if($row['status'] == 'Late'){
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Late" id="<?php echo"{$row['std_id']}Late"?>" checked>
                                            <label onclick="updateSearch(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Late';?>',<?php echo $row['attendance_id'];?>,'search','<?php echo$_GET['search'];?>')" class="late" for="<?php echo"{$row['std_id']}Late"?>">Late</label>
                                            <?php
                                        }else{
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Late" id="<?php echo"{$row['std_id']}Late"?>">
                                            <label onclick="updateSearch(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Late';?>',<?php echo $row['attendance_id'];?>,'search','<?php echo$_GET['search'];?>')" class="late" for="<?php echo"{$row['std_id']}Late"?>">Late</label>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        if($row['status'] == 'Excused'){
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Excused" id="<?php echo"{$row['std_id']}Excused"?>" checked>
                                            <label onclick="updateSearch(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Excused';?>',<?php echo $row['attendance_id'];?>,'search','<?php echo$_GET['search'];?>')" class="excused" for="<?php echo"{$row['std_id']}Excused"?>">Excused</label>
                                            <?php
                                        }else{
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Excused" id="<?php echo"{$row['std_id']}Excused"?>">
                                            <label onclick="updateSearch(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Excused';?>',<?php echo $row['attendance_id'];?>,'search','<?php echo$_GET['search'];?>')" class="excused" for="<?php echo"{$row['std_id']}Excused"?>">Excused</label>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <script>
                                    function updateSearch(std_id, sub_id, sched_id, status, attendance_id, from,search){
                                        window.location.href="../update_attendance.php?std_id="+std_id+"&sub_id="+sub_id+"&sched_id="+sched_id+"&status="+status+"&attendance_id="+attendance_id+"&from="+from+"&search="+search;
                                    }
                                </script>

                                <!-- ATTENDANCE TODAY NO SEARCH VIEW TABLE -->
                                <?php
                                }
                                }else{
                                $sql = "SELECT * FROM `std_attendance` INNER JOIN students ON std_attendance.std_id = students.std_id WHERE std_attendance.sched_id = {$_GET['sched_id']} AND std_attendance.sub_id = {$_GET['sub_id']} AND std_attendance.date = '".date("Y-m-d")."' ORDER BY students.lastname, students.lastname";
                                $result = $conn->query($sql);
                                while($row = $result->fetch_assoc()){
                                ?>
                                <tr>
                                    <td class="name"><?php echo"{$row['lastname']}";?></td>
                                    <td class="name"><?php echo"{$row['firstname']}";?></td>
                                    <td class="name"><?php echo"{$row['gender']}";?></td>
                                    <td class="stat-container">
                                        <?php
                                        if($row['status'] == 'Present'){
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Present" id="<?php echo"{$row['std_id']}Present"?>" checked>
                                            <label onclick="update(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Present';?>',<?php echo $row['attendance_id'];?>,'view')" class="present" for="<?php echo "{$row['std_id']}Present" ?>">Present</label>
                                            <?php
                                        }else{
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Present" id="<?php echo"{$row['std_id']}Present"?>">
                                            <label onclick="update(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Present';?>',<?php echo $row['attendance_id'];?>,'view')" class="present" for="<?php echo "{$row['std_id']}Present" ?>">Present</label>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        if($row['status'] == 'Absent'){
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Absent" id="<?php echo"{$row['std_id']}Absent"?>" checked>
                                            <label onclick="update(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Absent';?>',<?php echo $row['attendance_id'];?>,'view')" class="absent" for="<?php echo"{$row['std_id']}Absent"?>">Absent</label>
                                            <?php
                                        }else{
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Absent" id="<?php echo"{$row['std_id']}Absent"?>">
                                            <label onclick="update(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Absent';?>',<?php echo $row['attendance_id'];?>,'view')" class="absent" for="<?php echo"{$row['std_id']}Absent"?>">Absent</label>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        if($row['status'] == 'Late'){
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Late" id="<?php echo"{$row['std_id']}Late"?>" checked>
                                            <label onclick="update(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Late';?>',<?php echo $row['attendance_id'];?>,'view')" class="late" for="<?php echo"{$row['std_id']}Late"?>">Late</label>
                                            <?php
                                        }else{
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Late" id="<?php echo"{$row['std_id']}Late"?>">
                                            <label onclick="update(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Late';?>',<?php echo $row['attendance_id'];?>,'view')" class="late" for="<?php echo"{$row['std_id']}Late"?>">Late</label>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        if($row['status'] == 'Excused'){
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Excused" id="<?php echo"{$row['std_id']}Excused"?>" checked>
                                            <label onclick="update(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Excused';?>',<?php echo $row['attendance_id'];?>,'view')" class="excused" for="<?php echo"{$row['std_id']}Excused"?>">Excused</label>
                                            <?php
                                        }else{
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Excused" id="<?php echo"{$row['std_id']}Excused"?>">
                                            <label onclick="update(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Excused';?>',<?php echo $row['attendance_id'];?>,'view')" class="excused" for="<?php echo"{$row['std_id']}Excused"?>">Excused</label>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                }
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php
                    }else if(isset($_GET['sched_id']) && $_GET['action'] == "set"){
                    ?>
                    <!-- SET ATTENDANCE TABLE -->
                    <?php
                    if(isset($_GET['select'])){
                        ?>
                        <form action="../set_attendance.php?sched_id=<?php echo$_GET['sched_id'];?>&sub_id=<?php echo$_GET['sub_id'];?>" class="no-search" method="post">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Lastname</th>
                                        <th>Firstname</th>
                                        <th>Gender</th>
                                        <th>
                                        Status <br>
                                        <select name="Status" class="selector" onchange="window.location.href=this.value">
                                           <option value="" disabled selected>Select all </option>
                                            <option value="<?php echo'tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$_GET['sched_id'].'&action=set&select=Present';?>" <?php echo($_GET['select'] == 'Present')? 'selected': ''?>>Present all</option>
                                            <option value="<?php echo'tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$_GET['sched_id'].'&action=set&select=Absent';?>" <?php echo($_GET['select'] == 'Absent')? 'selected': ''?>>Absent all</option>
                                            <option value="<?php echo'tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$_GET['sched_id'].'&action=set&select=Late';?>" <?php echo($_GET['select'] == 'Late')? 'selected': ''?>>Late all</option>
                                            <option value="<?php echo'tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$_GET['sched_id'].'&action=set&select=Excused';?>" <?php echo($_GET['select'] == 'Excused')? 'selected': ''?>>Excused all</option>
                                            <option value="<?php echo'tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$_GET['sched_id'].'&action=set';?>" >Remove all</option>
                                        </select>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM `subjects` INNER JOIN students ON subjects.course = students.course AND subjects.year_level = students.year_level AND subjects.section = students.section WHERE subjects.sub_id = ".$_GET['sub_id']." ORDER BY students.lastname, students.firstname";
                                    $result = $conn->query($sql);
                                    while($row = $result->fetch_assoc()){
                                    ?>
                                    <tr>
                                        <td class="name"><?php echo"{$row['lastname']}";?></td>
                                        <td class="name"><?php echo"{$row['firstname']}";?></td>
                                        <td class="name"><?php echo"{$row['gender']}";?></td>
                                        <td class="stat-container">
    
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Present" id="<?php echo"{$row['std_id']}Present"?>" <?php echo ($_GET['select']==='Present')? "checked":"";?>>
                                            <label class="present" for="<?php echo"{$row['std_id']}Present"?>">Present</label>
    
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Absent" id="<?php echo"{$row['std_id']}Absent"?>" <?php echo ($_GET['select']==='Absent')? "checked":"";?>>
                                            <label class="absent" for="<?php echo"{$row['std_id']}Absent"?>">Absent</label>
                                        
                                            
                                        
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Late" id="<?php echo"{$row['std_id']}Late"?>" <?php echo ($_GET['select']==='Late')? "checked":"";?>>
                                            <label class="late" for="<?php echo"{$row['std_id']}Late"?>">Late</label>
                                        
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Excused" id="<?php echo"{$row['std_id']}Excused"?>" <?php echo ($_GET['select']==='Excused')? "checked":"";?>>
                                            <label class="excused" for="<?php echo"{$row['std_id']}Excused"?>">Excused</label>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <input type="submit" name="submit" id="lonely-btn">
    
                        </form>
                        <?php
                    }else{
                    ?>
                    <form action="../set_attendance.php?sched_id=<?php echo$_GET['sched_id'];?>&sub_id=<?php echo$_GET['sub_id'];?>" class="no-search" method="post">
                        <table>
                            <thead>
                                <tr>
                                    <th>Lastname</th>
                                    <th>Firstname</th>
                                    <th>Gender</th>
                                    <th>
                                        Status <br>
                                        <select name="Status" class="selector" onchange="window.location.href=this.value">
                                            <option value="" disabled selected>Select all</option>
                                            <option value="<?php echo'tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$_GET['sched_id'].'&action=set&select=Present';?>">Present all</option>
                                            <option value="<?php echo'tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$_GET['sched_id'].'&action=set&select=Absent';?>">Absent all</option>
                                            <option value="<?php echo'tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$_GET['sched_id'].'&action=set&select=Late';?>">Late all</option>
                                            <option value="<?php echo'tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$_GET['sched_id'].'&action=set&select=Excused';?>">Excused all</option>
                                        </select>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM `subjects` INNER JOIN students ON subjects.course = students.course AND subjects.year_level = students.year_level AND subjects.section = students.section WHERE subjects.sub_id = ".$_GET['sub_id']." ORDER BY students.lastname, students.firstname";
                                $result = $conn->query($sql);
                                while($row = $result->fetch_assoc()){
                                ?>
                                <tr>
                                    <td class="name"><?php echo"{$row['lastname']}";?></td>
                                    <td class="name"><?php echo"{$row['firstname']}";?></td>
                                    <td class="name"><?php echo"{$row['gender']}";?></td>
                                    <td class="stat-container">

                                        <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Present" id="<?php echo"{$row['std_id']}Present"?>">
                                        <label class="present" for="<?php echo"{$row['std_id']}Present"?>">Present</label>

                                        <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Absent" id="<?php echo"{$row['std_id']}Absent"?>">
                                        <label class="absent" for="<?php echo"{$row['std_id']}Absent"?>">Absent</label>
                                    
                                        
                                    
                                        <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Late" id="<?php echo"{$row['std_id']}Late"?>">
                                        <label class="late" for="<?php echo"{$row['std_id']}Late"?>">Late</label>
                                    
                                        <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Excused" id="<?php echo"{$row['std_id']}Excused"?>">
                                        <label class="excused" for="<?php echo"{$row['std_id']}Excused"?>">Excused</label>
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <input type="submit" name="submit" id="lonely-btn">

                    </form>
                    <?php
                    }
                    }else {
                    ?>
                    <?php
                    $query = "SELECT * FROM `subjects_sched` WHERE subjects_sched.sub_id = {$_GET['sub_id']} AND subjects_sched.weeks = '".date('l')."' ORDER BY STR_TO_DATE(subjects_sched.start_time, '%h:%i %p')";
                    $result = $conn->query($query);
                    while($row = $result->fetch_assoc()){
                    ?>
                    <div class="attendance-content">
                        <h1 class="atd-content"><?php echo$row['weeks'];?> class</h1>
                        <h3 class="atd-content" id="str-end"><?php echo"{$row['start_time']} to {$row['end_time']}";?></h3>
                        <?php  
                        $sql1 = "SELECT * FROM std_attendance WHERE sched_id = {$row['sched_id']} AND std_attendance.date = '".date("Y-m-d")."'";
                        $result1 = $conn->query($sql1);
                        if($result1->num_rows >= 1){
                            echo'<a href="tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$row['sched_id'].'&action=view" class="atd_btn" class="atd-content">VIEW</a>';
                        }else{
                            echo'<a href="tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$row['sched_id'].'&action=set" class="atd_btn" class="atd-content">SET</a>';
                        }
                        ?>
                    </div>
                    <?php
                    }
                }
                    ?>
                </div>
                
            <?php }else if($_GET['section'] === 'std_atd'){?>

            <?php
                if(isset($_GET['action']) && $_GET['action'] == "view-total"){
                ?>
                    <div class="atd-record-tbl">
                        <table>
                            <thead class="total-thead">
                                <tr>
                                    <th rowspan="2">Lastname</th>
                                    <th rowspan="2">Firstname</th>
                                    <th colspan="4">Classroom Attendance</th>
                                </tr>
                                <tr>
                                    <th class="stat">Present</th>
                                    <th class="stat">Absent</th>
                                    <th class="stat">Late</th>
                                    <th class="stat">Excused</th>
                                </tr>
                            </thead>
                        <tbody>
                            <?php 
                            $sql2 = "SELECT * FROM subjects WHERE subjects.sub_id = {$_GET['sub_id']}";
                            $result2 = $conn->query($sql2);
                            $row2 = $result2->fetch_assoc();

                            $sql1 = "SELECT * FROM students WHERE students.course = '{$row2['course']}' AND students.year_level = {$row2['year_level']} AND students.section = '{$row2['section']}' ORDER BY students.lastname, students.firstname";

                            $result1 = $conn->query($sql1);
                            while($row1 = $result1->fetch_assoc()){
                                $sql = "SELECT std_id, SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent_count, SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present_count, SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) AS late_count, SUM(CASE WHEN status = 'Excused' THEN 1 ELSE 0 END) AS excused_count FROM std_attendance WHERE std_attendance.std_id = {$row1['std_id']} AND sub_id = {$_GET['sub_id']} GROUP BY std_id";

                                $result = $conn->query($sql);

                                $row = $result->fetch_assoc();
                            ?>
                            <tr>
                                <td><?php echo"{$row1['lastname']}";?></td>
                                <td><?php echo"{$row1['firstname']}";?></td>
                                <td><p class="Present"><?php echo (isset($row['present_count'])) ? $row['present_count'] : "0"; ?></p></td>
                                <td><p class="Absent"><?php echo (isset($row['absent_count'])) ? $row['absent_count'] : "0"; ?></p></td>
                                <td><p class="Late"><?php echo (isset($row['late_count'])) ? $row['late_count'] : "0"; ?></p></td>
                                <td><p class="Excused"><?php echo (isset($row['excused_count'])) ? $row['excused_count'] : "0"; ?></p></td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                        </table>
                    </div> 
                <?php
                }else if(isset($_GET['sched_id']) && $_GET['action'] == "view-record"){
            ?>
                <!-- VIEWING STUDENT ATTENDANCE RECORD -->
                <div class="atd-record-tbl">
                    <div>
                        <form action="<?php echo'../search.php?sub_id='.$_GET['sub_id'].'&section=std_atd&sched_id='.$_GET['sched_id'].'&action=view-record&date='.$_GET['date'];?>" method="post">
                            <input type="text" name="search" placeholder="Search" style="text-align: center;">
                            <input type="submit" value="" name="btn-search">
                        </form>
                    </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Lastname</th>
                                    <th>Firstname</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- RECORD TABLE SEARCHING -->
                                <?php
                                if(isset($_GET['search'])){
                                    $sql = "SELECT * FROM `std_attendance` INNER JOIN students ON std_attendance.std_id = students.std_id WHERE std_attendance.sched_id = {$_GET['sched_id']} AND std_attendance.sub_id = {$_GET['sub_id']} AND std_attendance.date = '".$_GET['date']."' AND (students.lastname LIKE '%{$_GET['search']}%' OR students.firstname LIKE '%{$_GET['search']}%') ORDER BY students.lastname, students.lastname";
                                    $result = $conn->query($sql);
                                    while($row = $result->fetch_assoc()){
                                        ?>
                                        <tr>
                                            <td class="name"><?php echo"{$row['lastname']}";?></td>
                                            <td class="name"><?php echo"{$row['firstname']}";?></td>
                                            <td class="name"><?php echo"{$row['gender']}";?></td>
                                            <td class="stat-container">
                                                <?php
                                                
                                                if($row['status'] == 'Present'){
                                                    ?>
                                                    <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Present" id="<?php echo"{$row['std_id']}Present"?>" checked>
                                                    <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Present';?>',<?php echo $row['attendance_id'];?>,'search-record',<?php echo$row['date'];?>,'<?php echo$_GET['search'];?>')" class="present" for="<?php echo "{$row['std_id']}Present" ?>">Present</label>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Present" id="<?php echo"{$row['std_id']}Present"?>">
                                                    <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Present';?>',<?php echo $row['attendance_id'];?>,'search-record',<?php echo$row['date'];?>,'<?php echo$_GET['search'];?>')" class="present" for="<?php echo "{$row['std_id']}Present" ?>">Present</label>
                                                    <?php
                                                }
                                                ?>
        
                                                <?php
                                                if($row['status'] == 'Absent'){
                                                    ?>
                                                    <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Absent" id="<?php echo"{$row['std_id']}Absent"?>" checked>
                                                    <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Absent';?>',<?php echo $row['attendance_id'];?>,'search-record',<?php echo$row['date'];?>,'<?php echo$_GET['search'];?>')" class="absent" for="<?php echo"{$row['std_id']}Absent"?>">Absent</label>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Absent" id="<?php echo"{$row['std_id']}Absent"?>">
                                                    <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Absent';?>',<?php echo $row['attendance_id'];?>,'search-record',<?php echo$row['date'];?>,'<?php echo$_GET['search'];?>')" class="absent" for="<?php echo"{$row['std_id']}Absent"?>">Absent</label>
                                                    <?php
                                                }
                                                ?>
        
                                                <?php
                                                if($row['status'] == 'Late'){
                                                    ?>
                                                    <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Late" id="<?php echo"{$row['std_id']}Late"?>" checked>
                                                    <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Late';?>',<?php echo $row['attendance_id'];?>,'search-record',<?php echo$row['date'];?>,'<?php echo$_GET['search'];?>')" class="late" for="<?php echo"{$row['std_id']}Late"?>">Late</label>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Late" id="<?php echo"{$row['std_id']}Late"?>">
                                                    <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Late';?>',<?php echo $row['attendance_id'];?>,'search-record',<?php echo$row['date'];?>,'<?php echo$_GET['search'];?>')" class="late" for="<?php echo"{$row['std_id']}Late"?>">Late</label>
                                                    <?php
                                                }
                                                ?>
        
                                                <?php
                                                if($row['status'] == 'Excused'){
                                                    ?>
                                                    <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Excused" id="<?php echo"{$row['std_id']}Excused"?>" checked>
                                                    <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Excused';?>',<?php echo $row['attendance_id'];?>,'search-record',<?php echo$row['date'];?>,'<?php echo$_GET['search'];?>')" class="excused" for="<?php echo"{$row['std_id']}Excused"?>">Excused</label>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Excused" id="<?php echo"{$row['std_id']}Excused"?>">
                                                    <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Excused';?>',<?php echo $row['attendance_id'];?>,'search-record',<?php echo$row['date'];?>,'<?php echo$_GET['search'];?>')" class="excused" for="<?php echo"{$row['std_id']}Excused"?>">Excused</label>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                        
                                        <script>
                                            function updateRecord(std_id, sub_id, sched_id, status, attendance_id, from,search){
                                                
                                                window.location.href="../update_attendance.php?std_id="+std_id+"&sub_id="+sub_id+"&sched_id="+sched_id+"&status="+status+"&attendance_id="+attendance_id+"&from="+from+"&date=<?php echo$_GET['date'];?>&search="+'<?php echo $_GET['search'];?>';
                                                }
                                        </script>
                                        
                                        
                                <!-- RECORD TABLE NOT SEARCHING --><?php
                                }else{
                                $sql = "SELECT * FROM `std_attendance` INNER JOIN students ON std_attendance.std_id = students.std_id WHERE std_attendance.sched_id = {$_GET['sched_id']} AND std_attendance.sub_id = {$_GET['sub_id']} AND std_attendance.date = '".$_GET['date']."' ORDER BY students.lastname, students.lastname";
                                $result = $conn->query($sql);
                                while($row = $result->fetch_assoc()){
                                ?>
                                <tr>
                                    <td class="name"><?php echo"{$row['lastname']}";?></td>
                                    <td class="name"><?php echo"{$row['firstname']}";?></td>
                                    <td class="name"><?php echo"{$row['gender']}";?></td>
                                    <td class="stat-container">
                                        <?php
                                        
                                        if($row['status'] == 'Present'){
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Present" id="<?php echo"{$row['std_id']}Present"?>" checked>
                                            <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Present';?>',<?php echo $row['attendance_id'];?>,'record',<?php echo$row['date'];?>)" class="present" for="<?php echo "{$row['std_id']}Present" ?>">Present</label>
                                            <?php
                                        }else{
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Present" id="<?php echo"{$row['std_id']}Present"?>">
                                            <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Present';?>',<?php echo $row['attendance_id'];?>,'record',<?php echo$row['date'];?>)" class="present" for="<?php echo "{$row['std_id']}Present" ?>">Present</label>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        if($row['status'] == 'Absent'){
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Absent" id="<?php echo"{$row['std_id']}Absent"?>" checked>
                                            <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Absent';?>',<?php echo $row['attendance_id'];?>,'record',<?php echo$row['date'];?>)" class="absent" for="<?php echo"{$row['std_id']}Absent"?>">Absent</label>
                                            <?php
                                        }else{
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Absent" id="<?php echo"{$row['std_id']}Absent"?>">
                                            <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Absent';?>',<?php echo $row['attendance_id'];?>,'record',<?php echo$row['date'];?>)" class="absent" for="<?php echo"{$row['std_id']}Absent"?>">Absent</label>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        if($row['status'] == 'Late'){
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Late" id="<?php echo"{$row['std_id']}Late"?>" checked>
                                            <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Late';?>',<?php echo $row['attendance_id'];?>,'record',<?php echo$row['date'];?>)" class="late" for="<?php echo"{$row['std_id']}Late"?>">Late</label>
                                            <?php
                                        }else{
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Late" id="<?php echo"{$row['std_id']}Late"?>">
                                            <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Late';?>',<?php echo $row['attendance_id'];?>,'record',<?php echo$row['date'];?>)" class="late" for="<?php echo"{$row['std_id']}Late"?>">Late</label>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        if($row['status'] == 'Excused'){
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Excused" id="<?php echo"{$row['std_id']}Excused"?>" checked>
                                            <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Excused';?>',<?php echo $row['attendance_id'];?>,'record',<?php echo$row['date'];?>)" class="excused" for="<?php echo"{$row['std_id']}Excused"?>">Excused</label>
                                            <?php
                                        }else{
                                            ?>
                                            <input type="radio" name="<?php echo"{$row['std_id']}"?>" value="Excused" id="<?php echo"{$row['std_id']}Excused"?>">
                                            <label onclick="updateRecord(<?php echo $row['std_id'];?>,<?php echo $_GET['sub_id'];?>,<?php echo $_GET['sched_id'];?>,'<?php echo 'Excused';?>',<?php echo $row['attendance_id'];?>,'record',<?php echo$row['date'];?>)" class="excused" for="<?php echo"{$row['std_id']}Excused"?>">Excused</label>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>

                                    <script>
                                        function updateRecord(std_id, sub_id, sched_id, status, attendance_id, from){
                                            window.location.href="../update_attendance.php?std_id="+std_id+"&sub_id="+sub_id+"&sched_id="+sched_id+"&status="+status+"&attendance_id="+attendance_id+"&from="+from+"&date=<?php echo$_GET['date'];?>";
                                            }
                                    </script>

                                <?php
                            }
                                ?>
                            </tbody>
                        </table>
                    
                </div>
            <?php
                }else{
            ?>
            <!-- ATTENDANCE SHEET TABLE -->
            <div class="atd-record">
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2">Day</th>
                            <th rowspan="2">Date</th>
                            <th colspan="2">Time</th>
                            <th rowspan="2">View attendance</th>
                            
                        </tr>
                        <tr>
                            <th>Start</th>
                            <th>End</th>
                        </tr>
                    </thead>
                    <tbody>
                <?php
                $sql = "SELECT * FROM `attendance_sheet` INNER JOIN subjects_sched ON attendance_sheet.sched_id = subjects_sched.sched_id WHERE attendance_sheet.sub_id = {$_GET['sub_id']} ORDER BY STR_TO_DATE(attendance_sheet.date, '%Y-%m-%e') desc, STR_TO_DATE(subjects_sched.start_time, '%h:%i %p') ";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()){
                ?>
                    <tr>
                        <td><?php echo$row['weeks'];?></td>
                        <td><p class="date"><?php echo$row['date'];?></p></td>
                        <td><p class="date"><?php echo$row['start_time'];?></p></td>
                        <td><p class="date"><?php echo$row['end_time'];?></p></td>
                        <td><?php echo'<a href="tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=std_atd&sched_id='.$row['sched_id'].'&action=view-record&date='.$row['date'].'" class="atd-btn" class="atd-content">VIEW</a>';?></td>
                    </tr>
                    
                <?php 
                }
                ?>
                
                    </tbody>
                </table>
                <a href="<?php echo'tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=std_atd&action=view-total';?>" class="atd-btn">TOTAL</a>
            </div>
            <?php }?>
            <?php }else if($_GET['section'] === 'std'){?>
                <!-- ALL STUDENTS TABLE -->
                <div class="attendance-container">
                    <div class="student">
                        <table>
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Lastname</th>
                                    <th>Firstname</th>
                                    <th>Gender</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $sql = "SELECT * FROM `subjects` INNER JOIN students ON subjects.course = students.course AND subjects.year_level = students.year_level AND subjects.section = students.section WHERE subjects.sub_id = ".$_GET['sub_id']." ORDER BY students.lastname, students.firstname";
                            $result = $conn->query($sql);
                            $count = 1;
                            while($row = $result->fetch_assoc()){
                            ?>
                                <tr>
                                    <td><?php echo$count;?></td>
                                    <td><?php echo"{$row['lastname']}";?></td>
                                    <td><?php echo"{$row['firstname']}";?></td>
                                    <td><?php echo"{$row['gender']}";?></td>
                                </tr>
                                <?php
                                $count++;
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        } 
                    ?>
                    </div>
                </div>
        </div>
        
    </div>
    <script>
        function update(std_id, sub_id, sched_id, status, attendance_id, from){
            window.location.href="../update_attendance.php?std_id="+std_id+"&sub_id="+sub_id+"&sched_id="+sched_id+"&status="+status+"&attendance_id="+attendance_id+"&from="+from;
        }
    </script>
</body>
</html>
