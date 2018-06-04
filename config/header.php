<?php
//Page Included in Header
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
require $serverurl.'vendor/autoload.php';

if(isset($_POST["submitenquiry"])){
  //validate_me();
  $validate = true; 
  $email    = stripcleantohtml($_POST["email"]);
  $subject  = "Jagannath University Admission Enquiry - Jaipur";
  $name     = stripcleantohtml($_POST["name"]);
  $phone    = stripcleantohtml($_POST["phone"]);
  $course   = stripcleantohtml($_POST["course"]);
  $city   = stripcleantohtml($_POST["city"]);
  $message  = stripcleantohtml($_POST["message"]);

    if (empty($name) OR (!preg_match("/^[a-zA-Z ]*$/",$name))) {
      $validate = false;
    }
    if (empty($email) OR (!filter_var($email, FILTER_VALIDATE_EMAIL))) {
      $validate = false;
    }
    if (empty($phone) OR (!preg_match("/^[789]\d{9}$/",$phone))) {
      $validate = false;
    }
    /*if (empty($message) OR (!preg_match("/^([a-z]|[A-Z]|[0-9]| |_|-)+$/",$message))) {
      $validate = false;
    }*/
  
  if($validate == true) {
    if($course == 'hm') {
      $coursename = 'Hotel Management';
    } else {
      $query = "SELECT * FROM courses WHERE course_id='$course' LIMIT 1";
      $result = mysqli_query($con,$query);
      $courselist = mysqli_fetch_assoc($result);  
      $coursename = $courselist['course'];
    }
    
    $time = time();

  $sql= "insert into enquiry(name,email,phone,course,city,time,message, source,created_at,updated_at) values( '".$name."', '".$email."', '".$phone."', '".$course."', '".$city."', '".$time."', '".$message."', 'Website', '".$currenttimestamp."', '".$currenttimestamp."')";
  $res=mysqli_query($con,$sql);
  $mail             = new PHPMailer(); // defaults to using php "mail()"
  $mail->SetFrom('noreply@jagannathuinversity.org', 'Jagannath University Admin');
  $mail->AddReplyTo("admission@jagannathuniversity.org","Admin");
  $mail->AddAddress('admission@jagannathuniversity.org', "Admin");
  $mail->addBCC('manish.arora@jimsindia.org');
  $mail->addBCC('rajkamal@jimsindia.org');
  $mail->addBCC('admission@jagannathuniversity.org');
  $mail->Subject    = "Jagannath University Admission Form Enquiry";
  $mail->MsgHTML($message = "  <html><head></head><body>  <h3>Mail From Jagannath University Jaipur Admission Enquiry Form</h3><hr><br><br><b>Full Name :  </b>".$name."  <br><br><b>Email :  </b>".$email."    <br><br><b>Phone No :  </b>".$phone."    <br><br><b>Course :  </b>".$coursename."    <br><br><b>Course :  </b>".$city."    <br><br> <b>Message :  </b>".$message."</body></html>");
  

  $error = "";
  if(!$mail->Send()) {

    header('Location: '.$baseurlval.'thankyou-enquiry.php');
    //Redirect
    
  }
  else{
    //Redirect
    header('Location: '.$baseurlval.'thankyou-enquiry.php');
  }
  }
  else {
    $error = "* Invalid Values in Form.";
  }

}
?>