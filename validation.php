<?php
session_start();

// connect to databa
$db = mysqli_connect('localhost', 'root', '', 'multi_login');

// variable declaration
$name = "";
$email = "";
$errors = array();

// call the register() function if signup is clicked
if (isset($_POST['register'])) {
    register();
}

function register()
{
    // call these variables with the global keyword to make them available in function
    global $db, $errors, $name, $email;

    // receive all input values from the form. Call the e() function
    // defined below to escape form values
    $name = e($_POST['name']);
    $email = e($_POST['email']);
    $pass = e($_POST['password']);
    $repass = e($_POST['repassword']);
    $phone = e($_POST['phone']);
    $address = e($_POST['address']);
    $degree = e($_POST['degree']);
    // $board = e($_POST['board']);
    $institute = e($_POST['institute']);
    // $yearofpassing = e($_POST['yearofpassing']);
    // $percentage = e($_POST['percentage']);
    $department = e($_POST['department']);
    $experience = e($_POST['experience']);
    $skills = e($_POST['skills']);
    $file = $_FILES['file']['name'];
    $tmp_file = $_FILES['file']['tmp_name'];
    $photo = $_FILES['profilephoto']['name'];
    $tmp_name = $_FILES['profilephoto']['tmp_name'];
    $video = $_FILES['video']['name'];
    $tmp_video = $_FILES['video']['tmp_name'];
    $totalfiles = count($_FILES['file']['name']);
    $string='';
    for ($i = 0; $i < $totalfiles; $i++) {
        $filename = $_FILES['file']['name'][$i];
        $string=$string.':'.$filename; 
        if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], 'skilldocuments/' . $filename)) {
            //echo 'uploaded;
        }
    }
    if (isset($video)) {
        if (move_uploaded_file($tmp_video, 'video/' . $video)) {
            // echo 'uploaded';
        }
    }
    if (isset($photo)) {
        if (move_uploaded_file($tmp_name, 'profilephotos/' . $photo)) {
            // echo 'uploaded';
        }
    }
    if (empty($name)) {
        array_push($errors, "name is required");
    }
    if (empty($email)) {
        array_push($errors, "Email is required");
    }
    if (empty($pass)) {
        array_push($errors, "Password is required");
    }
    if (empty($phone)) {
        array_push($errors, "Phone number is required");
    }
    if ($pass != $repass) {
        array_push($errors, "The two passwords do not match");
    }
    if (empty($address)) {
        array_push($errors, "Address is required");
    }
    if (empty($department)) {
        array_push($errors, "please mention your department");
    }
    if (empty($experience)) {
        array_push($errors, "provide your experience");
    }
    if (empty($skills)) {
        array_push($errors, "skills are must");
    }

    // register user if there are no errors in the form
    if (count($errors) == 0) {
        $sql = "SELECT email FROM teachers WHERE email='" . $email . "'";
        $query = mysqli_query($db, $sql);
        if (mysqli_num_rows($query) != 0) {
            echo '<script>alert("User already exists with this email account")</script>';
        } else {
            $password = md5($pass); //encrypt the password before saving in the database
            $current_date = date("Y-m-d H:i:s");
            $query = "INSERT INTO teachers (name, email, password,phone,address,photo,degree,institute,department,experience,skills,skilldocuments,video,created_at)
					VALUES('$name', '$email', '$password', '$phone','$address','$photo','$degree','$institute','$department','$experience','$skills','$string','$video','$current_date')";
            if(!mysqli_query($db, $query)){
                echo mysqli_error($db);
            }    
            $_SESSION['success'] = "successfully registered";
            // echo 'completed';
            header('location: ./teacher_login.php');
        }
    }
}

// return user array from their id
function getUserById($id)
{
    global $db;
    $query = "SELECT * FROM users WHERE id=" . $id;
    $result = mysqli_query($db, $query);

    $user = mysqli_fetch_assoc($result);
    return $user;
}

// escape string
function e($val)
{
    global $db;
    return mysqli_real_escape_string($db, trim($val));
}

function display_error()
{
    global $errors;

    if (count($errors) > 0) {
        echo '<div class="error">';
        foreach ($errors as $error) {
            echo $error . '<br>';
        }
        echo '</div>';
    }
}
function isLoggedIn()
{
    if (isset($_SESSION['user'])) {
        return true;
    } else {
        return false;
    }
}
// log user out if logout button clicked
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['user']);
    header("location: ../signin.php");
}
// // call the login() function if signup is cli`cked
if (isset($_POST['login'])) {
    login();
}

// // LOGIN USER
function login()
{
    global $db, $email, $errors;

    // grap form values
    $email = e($_POST['your_email']);
    $password = e($_POST['your_password']);

    // make sure form is filled properly
    if (empty($email)) {
        array_push($errors, "email is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    // attempt login if no errors on form
    if (count($errors) == 0) {
        $password = md5($password);

        $query = "SELECT * FROM teachers WHERE email='$email' AND password='$password' LIMIT 1";
        $results = mysqli_query($db, $query);

        if (mysqli_num_rows($results) == 1) { // user found
            // check if user is admin or user
            $logged_in_user = mysqli_fetch_assoc($results);
            $_SESSION['user'] = $logged_in_user;
            $_SESSION['success'] = "You are now logged in";
            header('location: ../sidenavigationbar/sidebar.php');
             
        }
    }
}
