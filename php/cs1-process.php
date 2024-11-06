<?php
$name = $_POST["cs1Name"];
$email = $_POST["cs1Email"];
$phone_number = $_POST["cs1PhoneNum"];
 
$EmailTo = "surajwithawp.com";
$Subject = "Message from " . $name;
 
// prepare email body text
$Body = "Name: ";
$Body .= $name;
$Body .= "\n";
 
$Body .= "Email: ";
$Body .= $email;
$Body .= "\n";
 
$Body .= "Phone Number: ";
$Body .= $phone_number;
$Body .= "\n";
  
// send email
$success = mail($EmailTo, $Subject, $Body, "From:".$email);
 
// redirect to success page
if ($success){
   echo "Email send successfully";
}else{
    echo "Something went wrong";
}
 
?>