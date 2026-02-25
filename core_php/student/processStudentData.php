<?php
require_once("data/db.php");

session_start();
session_regenerate_id();

$entryURL = $_SERVER['HTTP_REFERER'];

if($_POST && isset($_POST['clearEntries'])) {
    $_SESSION['input']['studentID'] = null;
    $_SESSION['input']['studentLastName'] = null;
    $_SESSION['input']['studentFirstName'] = null;
    $_SESSION['messages']['createSuccess'] = "";
    $_SESSION['messages']['createError'] = "";    

    $_SESSION['errors']['studentID'] = "";
    $_SESSION['errors']['studentLastName'] = "";
    $_SESSION['errors']['studentFirstName'] = "";

    header("Location: $entryURL", true, 301);
}

if($_POST && isset($_POST['saveNewStudentEntry'])) { 
    $studentID = $_POST['studentID'];
    $studentLastName = $_POST['studentLastName'];
    $studentFirstName = $_POST['studentFirstName'];

    $_SESSION['input']['studentID'] = $studentID;
    $_SESSION['input']['studentLastName'] = $studentLastName;
    $_SESSION['input']['studentFirstName'] = $studentFirstName;

    if(!$_SESSION['errors']) {
        $_SESSION['errors'] = [];
    }

    if(filter_input(INPUT_POST,'studentID', FILTER_VALIDATE_INT) === false) {
        $_SESSION['errors']['studentID'] = "Invalid ID entry or format";
    } else {
        $_SESSION['errors']['studentID'] = "";
    } 

    if(filter_input(INPUT_POST,'studentLastName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false) {
        $_SESSION['errors']['studentLastName'] = "Invalid Last Name entry or format";
    } else {
        $_SESSION['errors']['studentLastName'] = "";
    }

    if(filter_input(INPUT_POST,'studentFirstName', FILTER_VALIDATE_REGEXP, ["options"=>["regexp"=>"/^[A-z\s\-]+$/"]]) === false) {
        $_SESSION['errors']['studentFirstName'] = "Invalid First Name entry or format";
    } else {
        $_SESSION['errors']['studentFirstName'] = "";
    }

    if(empty($_SESSION['errors']['studentID']) && empty($_SESSION['errors']['studentLastName']) && empty($_SESSION['errors']['studentFirstName'])) {
        $dbStatement = $db->prepare("INSERT INTO students (studid, studlastname, studfirstname) VALUES (:studid, :studlastname, :studfirstname)");
        $dbResult = $dbStatement->execute([
            'studid' => $studentID,
            'studlastname' => $studentLastName,
            'studfirstname' => $studentFirstName
        ]);

        if($dbResult) {
            $_SESSION['messages']['createSuccess'] = "Student entry added successfully";
            $_SESSION['messages']['createError'] = "";
        } else {
            $_SESSION['messages']['createError'] = "Failed to add student entry";
            $_SESSION['messages']['createSuccess'] = "";
        }        

        header("Location: $entryURL", true, 301);
    } else {
        header("Location: $entryURL", true, 301);
    }
}