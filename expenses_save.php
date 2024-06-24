<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$ui = $_SESSION['SESS_MEMBER_ID'];
$un = $_SESSION['SESS_FIRST_NAME'];


$now = date('Y-m-d');
$time = date('H:i:s');

$unit = $_POST['unit'];

if ($unit == 1) {

    $invo = "exp" . date("ymdhis");

    $vendor = $_POST['vendor'];
    $paycose = $_POST['paycose'];
    $type = $_POST['type'];
    $comment = $_POST['comment'];
    $amount = $_POST['amount'];
    $sub_id = $_POST['sub_type'];

    $pay = 0;
    $due = 0;
    $term_amount = 0;
    $load_id = 0;
    $util_id = 0;
    $util_date = '';
    $util_invo = '';
    $util_amount = 0;
    $util_blc = 0;
    $util_name = '';
    $sub_name = '';
    $lorry = 0;
    $lorry_no = '';
    $vendor_name = '';
    $emp_id = 0;
    $salary_pay_type = '';


    $re = $db->prepare("SELECT * FROM vendor WHERE id=:id ");
    $re->bindParam(':id', $vendor);
    $re->execute();
    for ($i = 0; $r = $re->fetch(); $i++) {
        $vendor_name = $r['name'];
    }

    $re = $db->prepare("SELECT * FROM expenses_types WHERE sn=:id ");
    $re->bindParam(':id', $type);
    $re->execute();
    for ($i = 0; $r = $re->fetch(); $i++) {
        $type_name = $r['type_name'];
    }

    $re = $db->prepare("SELECT * FROM expenses_sub_type WHERE id=:id ");
    $re->bindParam(':id', $sub_id);
    $re->execute();
    for ($i = 0; $r = $re->fetch(); $i++) {
        $sub_name = $r['name'];
    }

    if ($type == 1) {
        $util_id = $_POST['util_id'];
        $util_date = $_POST['util_date'];
        $util_invo = $_POST['util_invo'];
        $util_amount = $amount;
        $util_blc = $amount;
        $sub_id = 0;
        $sub_name = '';

        $re = $db->prepare("SELECT * FROM utility_bill WHERE id=:id ");
        $re->bindParam(':id', $util_id);
        $re->execute();
        for ($i = 0; $r = $re->fetch(); $i++) {
            $util_name = $r['name'];
        }
    }

    if ($type == 2) {
        $load_id = $_POST['load_id'];
        $sub_id = $_POST['sub_type'];

        $re = $db->prepare("SELECT * FROM expenses_sub_type WHERE id=:id ");
        $re->bindParam(':id', $sub_id);
        $re->execute();
        for ($i = 0; $r = $re->fetch(); $i++) {
            $sub_name = $r['name'];
        }

        $re = $db->prepare("SELECT * FROM loading WHERE transaction_id=:id ");
        $re->bindParam(':id', $load_id);
        $re->execute();
        for ($i = 0; $r = $re->fetch(); $i++) {
            $lorry = $r['lorry_id'];
            $lorry_no = $r['lorry_no'];
        }
    }

    if ($type == 3) {
        $lorry = $_POST['lorry'];
        $sub_id = $_POST['sub_type'];

        $re = $db->prepare("SELECT * FROM expenses_sub_type WHERE id=:id ");
        $re->bindParam(':id', $sub_id);
        $re->execute();
        for ($i = 0; $r = $re->fetch(); $i++) {
            $sub_name = $r['name'];
        }

        $re = $db->prepare("SELECT * FROM lorry WHERE lorry_id=:id ");
        $re->bindParam(':id', $lorry);
        $re->execute();
        for ($i = 0; $r = $re->fetch(); $i++) {
            $lorry_no = $r['lorry_no'];
        }
    }

    if ($type == 4) {
        $emp_id = $_POST['emp_id'];
        $salary_pay_type = $_POST['pay_type'];

        if ($salary_pay_type == 'lorry_collection') {

            $load_id = $_POST['load_id'];

            $re = $db->prepare("SELECT * FROM loading WHERE transaction_id=:id ");
            $re->bindParam(':id', $load_id);
            $re->execute();
            for ($i = 0; $r = $re->fetch(); $i++) {
                $lorry = $r['lorry_id'];
                $lorry_no = $r['lorry_no'];
            }
        }
    }

    $util_fw_blc = 0;
    if ($type == 1) {

        $sql = "UPDATE  utility_bill SET credit=credit+? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($amount, $util_id));

        $re = $db->prepare("SELECT * FROM utility_bill WHERE id = :id");
        $re->bindParam(':id', $util_id);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $util_fw_blc = $r['credit'];
        }
    }

    if ($paycose == 'asset') {
        $due = $_POST['due'];
        $term_amount = $amount / $due;
    }


    $sql = "INSERT INTO expenses_records (term,term_due,term_amount,date,type_id,type,invoice_no,comment,amount,user,loading_id,util_id,util_name,util_date,util_invoice,util_bill_amount,util_balance,util_forward_balance,pay_type,sub_type,sub_type_name,lorry_id,lorry_no,credit_balance,paycose,vendor_id,vendor_name,action,emp_id,salary_pay_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($due, $due, $term_amount, $now, $type, $type_name, $invo, $comment, $amount, $ui, $load_id, $util_id, $util_name, $util_date, $util_invo, $util_amount, $util_blc, $util_fw_blc, 'credit', $sub_id, $sub_name, $lorry, $lorry_no, $amount, $paycose, $vendor, $vendor_name, 1, $emp_id, $salary_pay_type));

    if ($type == 2 | $salary_pay_type == 'lorry_collection') {

        $pay = 1;

        $result = $db->prepare("SELECT * FROM expenses_records WHERE invoice_no=:id AND action = 1 ");
        $result->bindParam(':id', $invo);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $id = $row['id'];
        }

        $sql = "UPDATE  expenses_records SET pay_amount = pay_amount + ?, credit_balance = credit_balance - ?, close_date = ? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($amount, $amount, $now, $id));

        $sql = "INSERT INTO expenses_records (date,type_id,type,invoice_no,pay_amount,amount,user,loading_id,util_id,util_name,util_date,util_invoice,util_bill_amount,util_balance,util_forward_balance,pay_type,sub_type,sub_type_name,lorry_id,lorry_no,credit_id,paycose,vendor_id,vendor_name,close_date,emp_id,salary_pay_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $q = $db->prepare($sql);
        $q->execute(array($now, $type, $type_name, $invo, $amount, $amount, $ui, $load_id, $util_id, $util_name, $util_date, $util_invo, $util_amount, $util_blc, $util_fw_blc, 'lorry_collection', $sub_id, $sub_name, $lorry, $lorry_no, $id, 'payment', $vendor, $vendor_name, $now, $emp_id, $salary_pay_type));
    }

    if ($type == 4) {

        $result = $db->prepare("SELECT * FROM employee WHERE id=:id ");
        $result->bindParam(':id', $emp_id);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $name = $row['name'];
        }


        $sql = "INSERT INTO salary_advance (emp_id,name,amount,date,now,note) VALUES (?,?,?,?,?,?)";
        $q = $db->prepare($sql);
        $q->execute(array($emp_id, $name, $amount, $now, $now, $comment));
    }
}

if ($unit == 6) {

    $invo = $_POST['invo'];
    $amount = $_POST['pay_amount'];
    $pay_type = $_POST['pay_type'];

    $load_id = 0;
    $util_id = 0;
    $util_date = '';
    $util_invo = '';
    $util_amount = 0;
    $util_name = '';
    $sub_id = 0;
    $sub_name = '';
    $lorry = 0;
    $lorry_no = '';

    $acc = 0;
    $bank = 0;

    if ($pay_type == 'cash') {
        $acc = $_POST['acc'];
    }

    if ($pay_type == 'chq') {
        $bank = $_POST['bank'];
    }


    $result = $db->prepare("SELECT * FROM expenses_records WHERE invoice_no=:id AND action = 1 ");
    $result->bindParam(':id', $invo);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $id = $row['id'];
        $type = $row['type_id'];
        $type_name = $row['type'];
        $util_id = $row['util_id'];
        $util_name = $row['util_name'];
        $util_date = $row['util_date'];
        $util_invo = $row['util_invoice'];
        $util_amount = $row['util_bill_amount'];
        $load_id = $row['loading_id'];
        $sub_id = $row['sub_type'];
        $sub_name = $row['sub_type_name'];
        $lorry = $row['lorry_id'];
        $lorry_no = $row['lorry_no'];
        $emp_id = $row['emp_id'];
        $salary_pay_type = $row['salary_pay_type'];
        $vendor = $row['vendor_id'];
        $vendor_name = $row['vendor_name'];
        $credit = $row['credit_balance'];
    }

    if ($pay_type == 'chq') {
        $bn_blc = 0;
        $blc = 0;
        $re = $db->prepare("SELECT * FROM bank_balance WHERE id = :id");
        $re->bindParam(':id', $bank);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $blc = $r['amount'];
            $acc_name = $r['name'];
        }

        $bn_blc = $blc - $amount;

        $acc = $bank;
    }

    if ($pay_type == 'cash') {
        $cr_blc = 0;
        $blc = 0;
        $re = $db->prepare("SELECT * FROM cash WHERE id = :id");
        $re->bindParam(':id', $acc);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $blc = $r['amount'];
            $acc_name = $r['name'];
        }

        $cr_blc = $blc - $amount;
    }

    $util_blc = 0;
    $util_fw_blc = 0;
    if ($type == 1) {
        $util_blc = $util_amount - $amount;

        $sql = "UPDATE  utility_bill SET credit = credit - ? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($amount, $util_id));

        $ut_am = $util_amount;
        if ($util_amount >= $amount) {
            $ut_am = $amount;
        }

        $sql = "UPDATE  expenses_records SET util_balance = util_balance - ? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($ut_am, $id));

        $re = $db->prepare("SELECT * FROM utility_bill WHERE id = :id");
        $re->bindParam(':id', $util_id);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $util_fw_blc = $r['credit'];
        }
    }


    if ($pay_type == 'cash') {
        $chq_no = '';
        $chq_date = '';

        $sql = "UPDATE  cash SET amount=amount-? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($amount, $acc));

        $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $ql = $db->prepare($sql);
        $ql->execute(array('expenses', 'Debit', $id, $amount, 0, 0, '', '', 0, 'expenses_payment', $acc_name, $acc, $cr_blc, $now, $time, $ui, $un));
    }

    if ($pay_type == 'chq') {

        $chq_no = $_POST['chq_no'];
        $chq_date = $_POST['chq_date'];

        $sql = 'INSERT INTO payment (amount,pay_amount,pay_type,date,invoice_no,customer_id,chq_no,chq_bank,bank_id,chq_date,bank_name,type,action,chq_action,paycose) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $q = $db->prepare($sql);
        $q->execute(array($amount, $amount, $pay_type, $now, $invo, 0, $chq_no, $acc_name, $acc, $chq_date, '', $pay_type, 2, 1, 'expenses_issue'));
    }

    $credit = $credit - $amount;

    $close_date = '';
    if ($credit == 0) {
        $close_date = $now;
    }

    $sql = "UPDATE  expenses_records SET pay_amount = pay_amount + ?, credit_balance = credit_balance - ?, close_date = ? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $amount, $close_date, $id));

    $sql = "INSERT INTO expenses_records (date,type_id,type,invoice_no,acc_id,acc_name,pay_amount,amount,user,loading_id,util_id,util_name,util_date,util_invoice,util_bill_amount,util_balance,util_forward_balance,pay_type,chq_no,chq_date,sub_type,sub_type_name,lorry_id,lorry_no,credit_id,paycose,vendor_id,vendor_name,credit_balance,close_date,emp_id,salary_pay_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($now, $type, $type_name, $invo, $acc, $acc_name, $amount, $amount, $ui, $load_id, $util_id, $util_name, $util_date, $util_invo, $util_amount, $util_blc, $util_fw_blc, $pay_type, $chq_no, $chq_date, $sub_id, $sub_name, $lorry, $lorry_no, $id, 'payment', $vendor, $vendor_name, $credit, $close_date, $emp_id, $salary_pay_type));
}

if ($unit == 2) {
    $util_name = $_POST['util_name'];

    $name = trim($util_name);
    $name = ucwords($name);
    $name = str_replace(" ", "_", $name);

    $id = 0;
    $re = $db->prepare("SELECT * FROM utility_bill WHERE name=:id ");
    $re->bindParam(':id', $name);
    $re->execute();
    for ($i = 0; $r = $re->fetch(); $i++) {
        $id = $r['id'];
    }

    if ($id == 0) {
        $sql = "INSERT INTO utility_bill (name) VALUES (?) ";
        $ql = $db->prepare($sql);
        $ql->execute(array($name));
    }
}

if ($unit == 3) {
    $type = $_POST['type'];
    $id = 0;

    $name = trim($type);
    $name = ucwords($name);
    $name = str_replace(" ", "_", $name);

    $id = 0;
    $re = $db->prepare("SELECT * FROM expenses_types WHERE type_name=:id ");
    $re->bindParam(':id', $name);
    $re->execute();
    for ($i = 0; $r = $re->fetch(); $i++) {
        $id = $r['sn'];
    }

    if ($id == 0) {
        $sql = "INSERT INTO expenses_types (type_name) VALUES (?) ";
        $ql = $db->prepare($sql);
        $ql->execute(array($name));
    }
}

if ($unit == 4) {

    $sub_name = $_POST['name'];
    $typeid = $_POST['typeid'];

    $name = trim($sub_name);
    $name = ucwords($name);
    $name = str_replace(" ", "_", $name);

    $id = 0;
    $re = $db->prepare("SELECT * FROM expenses_sub_type WHERE name=:id AND type_id='$typeid' ");
    $re->bindParam(':id', $name);
    $re->execute();
    for ($i = 0; $r = $re->fetch(); $i++) {
        $id = $r['id'];
    }

    $re = $db->prepare("SELECT * FROM expenses_types WHERE sn=:id ");
    $re->bindParam(':id', $typeid);
    $re->execute();
    for ($i = 0; $r = $re->fetch(); $i++) {
        $type_name = $r['type_name'];
    }

    if ($id == 0) {
        $sql = "INSERT INTO expenses_sub_type  (name, type_id, type) VALUES (?,?,?) ";
        $ql = $db->prepare($sql);
        $ql->execute(array($name, $typeid, $type_name));
    }
}

if ($unit == 5) {

    $name = $_POST['name'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $note = $_POST['note'];

    $sql = "INSERT INTO vendor (name,contact,address,date,note,action) VALUES (?,?,?,?,?,?) ";
    $ql = $db->prepare($sql);
    $ql->execute(array($name, $contact, $address, $now, $note, 1));
}

if ($unit == 1) {
    if ($pay == 0) {
        header("location: expenses.php?id=$invo");
    } else {
        header("location: expenses.php");
    }
} else {
    header("location: expenses.php");
}
