<?php
include_once "database.php";

$times = array("7:00 AM","7:30 AM","8:00 AM","8:30 AM","9:00 AM","9:30 AM","10:00 AM","10:30 AM","11:00 AM","11:30 AM","12:00 PM","12:30 PM","1:00 PM","1:30 PM","2:00 PM","2:30 PM","3:00 PM","3:30 PM","4:00 PM","4:30 PM","5:00 PM","5:30 PM","6:00 PM","6:30 PM");

$week = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");


function getRowspan($start_time, $end_time) {
    $start_datetime = strtotime($start_time);
    $end_datetime = strtotime($end_time);

    $difference_minutes = round(abs($end_datetime - $start_datetime) / 60);

    $rowspan = $difference_minutes / 30;

    $rowspan = ceil($rowspan);

    return max(1, $rowspan);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Schedule</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Time</th>
                    <?php
                        foreach ($week as $day) {
                            echo "<th>{$day}</th>";
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($times as $time) {
                ?>
                <tr>
                    <th><?php echo "{$time} - " . date('h:i A', strtotime($time) + 1800); ?></th>
                    
                    <?php
                    foreach ($week as $day) {
                        $sql = "SELECT * FROM `subjects_sched` INNER JOIN subjects ON subjects_sched.sub_id = subjects.sub_id WHERE subjects.course = 'BSIT' AND subjects.year_level = 2 AND subjects.section = 'A' AND subjects_sched.weeks = '{$day}' AND subjects_sched.start_time = '{$time}'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $rowspan = getRowspan($row['start_time'], $row['end_time']);
                            echo '<td rowspan="' . $rowspan . '" class="bg-secondary text-white">' . $row['sub_code'] . '</td>';
                        } else {
                            $sql = "SELECT * FROM `subjects_sched` INNER JOIN subjects ON subjects_sched.sub_id = subjects.sub_id WHERE subjects.course = 'BSIT' AND subjects.year_level = 2 AND subjects.section = 'A' AND subjects_sched.weeks = '{$day}' AND '{$time}' BETWEEN subjects_sched.start_time AND subjects_sched.end_time ORDER BY STR_TO_DATE(subjects_sched.start_time, '%h:%i %p')";
                            $result = $conn->query($sql);
                            if($result->num_rows > 0){
                                continue;
                            }else{
                                echo"<td></td>";
                            }
                        }
                    }
                    ?>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>