<?php
require_once 'config/db_connect.php';
require_once 'config/email_config.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Start session
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Handle manual trigger
if (isset($_POST['trigger_reminders'])) {
    try {
        // Get users with email reminders enabled and books due within 3 days
        $query = "SELECT DISTINCT u.user_id, u.email, u.full_name, u.email_reminder
                 FROM users u
                 JOIN book_loans l ON u.user_id = l.user_id
                 WHERE u.email_reminder = 1
                 AND l.status = 'borrowed'
                 AND l.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
        
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            throw new Exception("Database query failed: " . mysqli_error($conn));
        }
        
        $reminders_sent = 0;
        $errors = [];
        
        while ($user = mysqli_fetch_assoc($result)) {
            // Get user's due books
            $books_query = "SELECT b.title, b.author, l.due_date, DATEDIFF(l.due_date, CURDATE()) as days_remaining
                          FROM book_loans l
                          JOIN books b ON l.book_id = b.book_id
                          WHERE l.user_id = '{$user['user_id']}'
                          AND l.status = 'borrowed'
                          AND l.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
            
            $books_result = mysqli_query($conn, $books_query);
            $due_books = [];
            
            while ($book = mysqli_fetch_assoc($books_result)) {
                $due_books[] = $book;
            }
            
            if (!empty($due_books)) {
                // Create email content
                $subject = "Library Book Return Reminder";
                $message = "Dear {$user['full_name']},<br><br>";
                $message .= "This is a reminder that you have the following books due soon:<br><br>";
                
                foreach ($due_books as $book) {
                    $days = $book['days_remaining'];
                    $due_text = $days == 0 ? "today" : "in {$days} days";
                    $message .= "- {$book['title']} by {$book['author']} (Due {$due_text})<br>";
                }
                
                $message .= "<br>Please return these books on time to avoid late fees.<br>";
                $message .= "Thank you for using our library system!";
                
                // Send email
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USERNAME;
                $mail->Password = SMTP_PASSWORD;
                $mail->SMTPSecure = SMTP_ENCRYPTION;
                $mail->Port = SMTP_PORT;
                
                $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
                $mail->addAddress($user['email']);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;
                
                if ($mail->send()) {
                    $reminders_sent++;
                } else {
                    $errors[] = "Failed to send email to {$user['email']}";
                }
            }
        }
        
        if ($reminders_sent > 0) {
            $_SESSION['success'] = "Successfully sent {$reminders_sent} reminder emails.";
        } else {
            $_SESSION['info'] = "No reminder emails needed to be sent at this time.";
        }
        if (!empty($errors)) {
            $_SESSION['error'] = implode("<br>", $errors);
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    // Redirect back to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email Reminders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Test Email Reminders</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <?php 
                                echo $_SESSION['success'];
                                unset($_SESSION['success']);
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php 
                                echo $_SESSION['error'];
                                unset($_SESSION['error']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['info'])): ?>
                            <div class="alert alert-info">
                                <?php 
                                echo $_SESSION['info'];
                                unset($_SESSION['info']);
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="text-center">
                                <button type="submit" name="trigger_reminders" class="btn btn-primary">
                                    Send Reminder Emails
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 