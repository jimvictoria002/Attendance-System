<?php
include_once "database.php";
date_default_timezone_set('Asia/Manila');
$sql1 = "SELECT * FROM std_attendance WHERE sched_id = {$_GET['sched_id']} AND std_attendance.date = '".date("Y-m-d")."'";
$result1 = $conn->query($sql1);

if($result1->num_rows>=1){
    header("Location: Teacher/tchrs_classroom.php?sub_id=".$_GET['sub_id']."&section=atd_today");
}else if(isset($_POST['submit'])){
    $sql = "INSERT INTO attendance_sheet (sub_id,sched_id,date) VALUES ({$_GET['sub_id']}, {$_GET['sched_id']}, '".date("Y-m-d")."')";
    $conn->query($sql);
    $sql = "SELECT * FROM `subjects_sched` INNER JOIN subjects ON subjects_sched.sub_id = subjects.sub_id WHERE subjects_sched.sched_id =".$_GET['sched_id'];
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $sql = "SELECT * FROM students WHERE students.course = '{$row['course']}' AND students.year_level = {$row['year_level']} AND students.section = '{$row['section']}' ORDER BY students.lastname, students.firstname";
    $result = $conn->query($sql);
    while($row1 = $result->fetch_assoc()){
        if(!isset($_POST[$row1['std_id']])){
            $query = "INSERT INTO std_attendance (status,std_id,sub_id,date,sched_id) VALUES ('Excused', {$row1['std_id']}, {$_GET['sub_id']}, '".date("Y-m-d")."', {$_GET['sched_id']})";
            $conn->query($query);
        }else{
            $query = "INSERT INTO std_attendance (status,std_id,sub_id,date,sched_id) VALUES ('{$_POST[$row1['std_id']]}', {$row1['std_id']}, {$_GET['sub_id']}, '".date("Y-m-d")."', {$_GET['sched_id']})";
            $conn->query($query);
        }
        
    }
    header("Location: Teacher/tchrs_classroom.php?sub_id=".$_GET['sub_id']."&section=atd_today");
}

?>