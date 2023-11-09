<?php

    if(!isset($_GET['date'])){
        header('Location: Teacher/tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=atd_today&sched_id='.$_GET['sched_id'].'&action=view&search='.$_POST['search']);
    }else if(isset($_GET['date'])){
        header('Location: Teacher/tchrs_classroom.php?sub_id='.$_GET['sub_id'].'&section=std_atd&sched_id='.$_GET['sched_id'].'&action=view-record&date='.$_GET['date'].'&search='.$_POST['search']);
    }

?>