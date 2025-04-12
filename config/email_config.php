<?php
// Email configuration
define('EMAIL_FROM', ''); // I will put my rgu email here
define('EMAIL_FROM_NAME', 'Library Management System');

// Gmail SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', ''); // I will put my rgu email here
define('SMTP_PASSWORD', ''); // Get app password from google account
define('SMTP_ENCRYPTION', 'tls');

// Enable SMTP debugging (0 = off, 1 = client messages, 2 = client and server messages)
define('SMTP_DEBUG', 2); 


// Downloaded and installed Composer
// Then run this command: composer require phpmailer/phpmailer
// Then run test_reminders script directly through http://localhost/LIBRARY/test_reminders.php
