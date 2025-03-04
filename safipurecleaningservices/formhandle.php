<?php
$name = $_POST['name'];
$visitor_email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];

$email_from = 'here add your domain name';

$email_subject = 'Here you can write anything e.g New form submission';

$email_body = "user name: $name.\n".
              "user email: $visitor_email.\n".
               "subject: $subject.\n".
                "user subject: $message.\n";

$to = 'gideon1mwangi@gmail.com';

$headers = "From: $email_from \r\n";

$headers .= "Reply-To: $visitor_email \r\n";

mail($to,$email_subject,$email_body,$headers);

header("Location: contact.html");
?>