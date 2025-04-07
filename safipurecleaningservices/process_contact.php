<?php

require_once 'db_connect.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
   
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
        exit;
    }
    
    try {
        
        $stmt = $conn->prepare("INSERT INTO contact_submissions (name, email, message) VALUES (:name, :email, :message)");
        
       
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);
        
       
        $stmt->execute();
        
        
        $checkStmt = $conn->prepare("SELECT id FROM customers WHERE email = :email");
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() == 0) {
            // add customers kama hawaexist
            $custStmt = $conn->prepare("INSERT INTO customers (name, email) VALUES (:name, :email)");
            $custStmt->bindParam(':name', $name);
            $custStmt->bindParam(':email', $email);
            $custStmt->execute();
        }
        
        // Send email notification to admin
        $to = "mkaranjab@gmail.com"; 
        $subject = "New Contact Form Submission";
        $email_message = "Name: $name\n";
        $email_message .= "Email: $email\n\n";
        $email_message .= "Message:\n$message";
        $headers = "From: website@safipurecleaningservices.com";
        
        mail($to, $subject, $email_message, $headers);
        
       
        echo json_encode(['success' => true, 'message' => 'Thank you for your message. We will get back to you soon!']);
        
    } catch(PDOException $e) {
     
        error_log("Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again later.']);
    }
    
    
    $conn = null;
} else {
   
    header("Location: contact.html");
    exit;
}
?>