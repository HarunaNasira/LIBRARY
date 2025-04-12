<?php
require_once '../config/db_connect.php';
require_once '../config/email_config.php';

// Get all users who have email reminders enabled and have books due in the next 3 days
$sql = "SELECT DISTINCT u.user_id, u.email, u.first_name, u.last_name, 
        b.title, b.author, l.due_date, l.loan_id
        FROM users u
        JOIN book_loans l ON u.user_id = l.user_id
        JOIN books b ON l.book_id = b.book_id
        WHERE u.email_reminder = 1 
        AND l.status = 'borrowed'
        AND l.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)
        ORDER BY u.user_id, l.due_date";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $to = $row['email'];
        $subject = "Book Return Reminder";
        
        $message = "Dear " . $row['first_name'] . ",\n\n";
        $message .= "This is a reminder that you have the following book due for return:\n\n";
        $message .= "Book: " . $row['title'] . " by " . $row['author'] . "\n";
        $message .= "Due Date: " . date('F j, Y', strtotime($row['due_date'])) . "\n\n";
        $message .= "Please return the book on or before the due date to avoid any late fees.\n\n";
        $message .= "Thank you,\nNAS Library Management System";

        $headers = "From: " . EMAIL_FROM . "\r\n";
        $headers .= "Reply-To: " . EMAIL_FROM . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        if (mail($to, $subject, $message, $headers)) {
            // Log successful email
            $log_sql = "INSERT INTO email_logs (user_id, loan_id, email_type, sent_date) 
                       VALUES (?, ?, 'reminder', NOW())";
            $stmt = $conn->prepare($log_sql);
            $stmt->bind_param("ii", $row['user_id'], $row['loan_id']);
            $stmt->execute();
        }
    }
} 