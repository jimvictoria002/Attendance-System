<?php

include_once "database.php";

$sql = "UPDATE std_attendance SET status = '{$_GET['status']}' WHERE std_attendance.attendance_id = {$_GET['attendance_id']}";

$conn->query($sql);

if($_GET['from'] == 'view'){
    header("Location: Teacher/tchrs_classroom.php?sub_id=".$_GET['sub_id']."&section=atd_today&sched_id=".$_GET['sched_id']."&action=view");
}else if($_GET['from'] == 'record'){
    header("Location: Teacher/tchrs_classroom.php?sub_id=".$_GET['sub_id']."&section=std_atd&sched_id=".$_GET['sched_id']."&action=view-record&date=".$_GET['date']);
}else if ($_GET['from'] == 'search'){
    header('Location: Teacher/tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$_GET['sched_id'].'&action=view&search='.$_GET['search']);
}else if ($_GET['from'] == 'search-record'){
    header("Location: Teacher/tchrs_classroom.php?sub_id=".$_GET['sub_id']."&section=std_atd&sched_id=".$_GET['sched_id']."&action=view-record&date=".$_GET['date'].'&search='.$_GET['search']);
}

?>