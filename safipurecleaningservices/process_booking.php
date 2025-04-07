<?php

require_once 'db_connect.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $service_type = filter_input(INPUT_POST, 'service_type', FILTER_SANITIZE_STRING);
    $service_date = filter_input(INPUT_POST, 'service_date', FILTER_SANITIZE_STRING);
    $service_time = filter_input(INPUT_POST, 'service_time', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
    
   
    if (empty($name) || empty($email) || empty($service_type) || empty($service_date)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
        exit;
    }
    
    try {
        
        $stmt = $conn->prepare("INSERT INTO service_bookings (customer_name, customer_email, customer_phone, service_type, service_date, service_time, address, additional_notes) 
        VALUES (:name, :email, :phone, :service_type, :service_date, :service_time, :address, :notes)");
        
        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':service_type', $service_type);
        $stmt->bindParam(':service_date', $service_date);
        $stmt->bindParam(':service_time', $service_time);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':notes', $notes);
        
        /
        $stmt->execute();
        
        
        $checkStmt = $conn->prepare("SELECT id FROM customers WHERE email = :email");
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() == 0) {
            
            $custStmt = $conn->prepare("INSERT INTO customers (name, email, phone, address) VALUES (:name, :email, :phone, :address)");
            $custStmt->bindParam(':name', $name);
            $custStmt->bindParam(':email', $email);
            $custStmt->bindParam(':phone', $phone);
            $custStmt->bindParam(':address', $address);
            $custStmt->execute();
        } else {
         
            $updateStmt = $conn->prepare("UPDATE customers SET name = :name, phone = :phone, address = :address WHERE email = :email");
            $updateStmt->bindParam(':name', $name);
            $updateStmt->bindParam(':phone', $phone);
            $updateStmt->bindParam(':address', $address);
            $updateStmt->bindParam(':email', $email);
            $updateStmt->execute();
        }
        
        
        $to = "mkaranjab@gmail.com"; 
        $subject = "New Service Booking";
        $email_message = "Name: $name\n";
        $email_message .= "Email: $email\n";
        $email_message .= "Phone: $phone\n";
        $email_message .= "Service: $service_type\n";
        $email_message .= "Date: $service_date\n";
        $email_message .= "Time: $service_time\n";
        $email_message .= "Address: $address\n\n";
        $email_message .= "Additional Notes:\n$notes";
        $headers = "From: website@safipurecleaningservices.com";
        
        mail($to, $subject, $email_message, $headers);
        
        
        echo json_encode(['success' => true, 'message' => 'Thank you for your booking. We will confirm your appointment soon!']);
        
    } catch(PDOException $e) {
       
        error_log("Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again later.']);
    }
    
   
    $conn = null;
} else {
    
    header("Location: index.html");
    exit;
}
?>