<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

function AddPlayTime($times)
{

    $minutes = 0; //declare minutes either it gives Notice: Undefined variable
    // loop thought all the times

    foreach ($times as $time) {
        list($hour, $minute) = explode('.', $time);
        $minutes += $hour * 60;
        $minutes += $minute;
    }

    $hours = floor($minutes / 60);
    $minutes -= $hours * 60;

    // returns the time already formatted
    return sprintf('%02d.%02d', $hours, $minutes);
}

function TimeSet($times)
{

    $minutes = 0;
    list($hour, $minute) = explode(".", $times);
    $minutes += $minute + $hour * 60;

    return $minutes / 60;
}


$date = $_POST['date'];

$d1 = $date . "-01";
$d2 = $date . "-31";


$result1 = $db->prepare("SELECT * FROM employee");
$result1->bindParam(':userid', $res);
$result1->execute();
for ($i = 0; $row1 = $result1->fetch(); $i++) {
    $id = $row1['id'];
    $name = $row1['name'];
    $rate = $row1['hour_rate'];
    $epf = $row1['epf_amount'];
    $well = $row1['well'];
    $well_amount = $row1['well'];

    $ot = [];
    $hour = [];
    $amount = 0;
    $ot_tot = 0;
    $ot_rate = 0;
    $ot_time = 0;
    $allowances = 0;

    $result = $db->prepare("SELECT work_time,ot FROM attendance WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ORDER BY id ASC");
    $result->bindParam(':userid', $date);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $hour[] = $row['work_time'];
        $ot[] = $row['ot'];
    }

    $result = $db->prepare("SELECT count(id) FROM attendance WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ");
    $result->bindParam(':userid', $date);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $day = $row['count(id)'];
        $att_day = $row['count(id)'];
    }


    $result = $db->prepare("SELECT sum(amount) FROM salary_advance WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ");
    $result->bindParam(':userid', $date);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $adv = $row['sum(amount)'];
    }


    $result = $db->prepare("SELECT sum(amount) FROM hr_allowances WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ");
    $result->bindParam(':userid', $date);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $allowances = $row['sum(amount)'];
    }


    $commission = 0;
    
    $allowances = $allowances + $commission;

    // ------------------------------------Check Advance
    if ($adv == '') {
        $adv = 0;
    }

    //--------------- OT Time -----------------// 
    $ot_tot = ($rate * 142.86) / 100 * AddPlayTime($ot);

    $ot_rate = ($rate * 142.86) / 100;
    $ot_time = TimeSet($ot);

    //--------------- Worck hour -------------//
    $hour = AddPlayTime($hour);

    $basic = $rate * $hour;
    $day = TimeSet($hour);

    $amount = ($ot_tot + $basic + $allowances) - $epf - $adv - $well;

    $empid = 0;
    $result = $db->prepare("SELECT * FROM hr_payroll WHERE emp_id ='$id' AND date='$date' ORDER BY id ASC");
    $result->bindParam(':userid', $date);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $empid = $row['emp_id'];
    }

    if ($att_day > 1) {
        if ($empid == 0) {
            $time = date('H:i:s');
            $sql = "INSERT INTO hr_payroll (name,emp_id,amount,date,time,day_pay,day_rate,ot,ot_rate,commis,advance,epf,day,ot_time,etf) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $q = $db->prepare($sql);
            $q->execute(array($name, $id, $amount, $date, $time, $basic, $rate, $ot_tot, $ot_rate, $allowances, $adv, $epf, $day, $ot_time, $epf / 8 * 3));

            if ($amount < 0) {
                $now = date('Y-m-d');
                $sql = 'INSERT INTO salary_advance (emp_id,name,amount,date,note,now) VALUES (?,?,?,?,?,?)';
                $q = $db->prepare($sql);
                $q->execute(array($id, $name, abs($amount), $now, 'Salary Blance (' . $date . ')', $now));
            }

            $result = $db->prepare("SELECT * FROM hr_payroll WHERE emp_id ='$id' AND date='$date' ");
            $result->bindParam(':userid', $date);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $pay_id = $row['id'];
            }

            $u_id = $_SESSION['SESS_MEMBER_ID'];
            $u_name = $_SESSION['SESS_FIRST_NAME'];
            $time = date('H:i:s');
            $now = date('Y-m-d');
            $cr_blc = 0;
            $blc = 0;
            $cr_id = 4;
            $re = $db->prepare("SELECT * FROM cash WHERE id = '$cr_id' ");
            $re->bindParam(':id', $res);
            $re->execute();
            for ($k = 0; $r = $re->fetch(); $k++) {
                $blc = $r['amount'];
                $cr_name = $r['name'];
            }

            $cr_blc = $blc + $well_amount;

            $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array('welfare', 'Credit', $id, $well_amount, 0, $cr_id, 'Cash', $cr_name, $cr_blc, 'welfare_amount', 'Welfare', $id, 0, $now, $time, $u_id, $u_name));

            $sql = "UPDATE  cash SET amount=? WHERE id=?";
            $ql = $db->prepare($sql);
            $ql->execute(array($cr_blc, $cr_id));
        }
    }
}


header("location: hr_payroll_print.php?id=1&date=$date");
