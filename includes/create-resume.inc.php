<?php
include '../config.php';
session_start();

if (isset($_POST['submit'])) {

    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $objective = mysqli_real_escape_string($conn, $_POST['objective']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $place = mysqli_real_escape_string($conn, $_POST['place']);
    $photo = $_FILES['photo'];
    $signature = $_FILES['signature'];

    //-----Check if form datas are not filled-----
    if (empty($address) || empty($city) || empty($state) || empty($pincode)  || empty($country) ||  empty($objective) || empty($photo) || empty($signature)  || empty($phone)) {
        header("Location:../create-resume?error=empty");
        exit();
    }

    //-----End Check if form datas are not filled-----

    else {
        $sql_resume = "INSERT INTO resume (user_id, objective, place)
            VALUES('$_SESSION[user_id]', '$objective', '$place');";
        $result_resume = $conn->query($sql_resume);
        $resume_id = $conn->insert_id;

        $sql_address = "INSERT INTO address ( resume_id, address, city, pincode, state, country, phone)
        			VALUES ('$resume_id','$address', '$city', '$pincode', '$state', '$country', '$phone');";
        $result_address = $conn->query($sql_address);

        //add coures
        $courses = $_POST['course'];
        $years = $_POST['year'];
        $institutes = $_POST['institute'];

        foreach ($courses as $index => $course) {
            $year = $years[$index];
            $institute = $institutes[$index];
            if ($course != "") {
                $sql_course = "INSERT INTO education ( resume_id, course, institute, year)
                VALUES ('$resume_id','$course', '$institute', '$year');";
                $result_course = $conn->query($sql_course);
            }
        }

        //add experiences
        $positions = $_POST['position'];
        $organizations = $_POST['organization'];
        $from_dates = $_POST['from'];
        $to_dates = $_POST['to'];

        foreach ($positions as $index => $position) {
            $organization = $organizations[$index];
            $from_date = $from_dates[$index];
            $to_date = $to_dates[$index];
            if ($position != "") {
                $sql_experience = "INSERT INTO experience ( resume_id, position, organization, from_date, to_date)
                VALUES ('$resume_id','$position', '$organization', '$from_date', '$to_date');";
                $result_experience = $conn->query($sql_experience);
            }
        }

        //add languages
        $sql_language = "INSERT INTO languages ( resume_id, lang1, lang2, lang3, lang4)
        VALUES ('$resume_id','$_POST[lang1]', '$_POST[lang2]', '$_POST[lang3]', '$_POST[lang4]');";
        $result_language = $conn->query($sql_language);

        //add skills
        $sql_skill = "INSERT INTO skills ( resume_id, skill1, skill2, skill3, skill4)
        VALUES ('$resume_id','$_POST[skill1]', '$_POST[skill2]', '$_POST[skill3]', '$_POST[skill4]');";
        $result_skill = $conn->query($sql_skill);


        //Upload Image
        $file = $_FILES['photo'];
        $fileName = $_FILES['photo']['name'];
        $fileTmpName = $_FILES['photo']['tmp_name'];
        $fileSize = $_FILES['photo']['size'];
        $fileError = $_FILES['photo']['error'];
        $fileType = $_FILES['photo']['type'];

        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));

        $allowed = array('jpg', 'jpeg');
        if (in_array($fileActualExt, $allowed)) {
            if ($fileError === 0) {
                if ($fileSize < 10000000) {
                    $fileNameNew = rand(11111, 99999) . "." . $fileActualExt;
                    $fileLocation = $_SERVER['DOCUMENT_ROOT'] . '/resume-builder/images/photos/' . $fileNameNew;
                    move_uploaded_file($fileTmpName, $fileLocation);
                } else {
                    echo "Your file is too big";
                }
            } else {
                echo "Error uploading your file";
            }
        } else {
            echo "You Cannor upload files of this type";
        }

        //Upload Signature
        $signature = $_FILES['signature'];
        $sigName = $_FILES['signature']['name'];
        $sigTmpName = $_FILES['signature']['tmp_name'];
        $sigSize = $_FILES['signature']['size'];
        $sigError = $_FILES['signature']['error'];
        $sigType = $_FILES['signature']['type'];

        $sigExt = explode('.', $sigName);
        $sigActualExt = strtolower(end($sigExt));

        $allowed = array('jpg', 'jpeg');
        if (in_array($sigActualExt, $allowed)) {
            if ($sigError === 0) {
                if ($sigSize < 10000000) {
                    $sigNameNew = rand(11111, 99999) . "." . $sigActualExt;
                    $sigLocation =  $_SERVER['DOCUMENT_ROOT'] . '/resume-builder/images/signature/' . $sigNameNew;
                    move_uploaded_file($sigTmpName, $sigLocation);
                } else {
                    echo "Your file is too big";
                }
            } else {
                echo "Error uploading your file";
            }
        } else {
            echo "You Cannor upload files of this type";
        }

        $sql_image = "INSERT INTO images ( resume_id, photo, signature)
        VALUES ('$resume_id','$fileNameNew', '$sigNameNew');";
        $result_image = $conn->query($sql_image);

        header("Location: ../dashboard?success=created");
        exit();
    }
} else {
    header("Location:../create-resume?error=submit");
    exit();
}
