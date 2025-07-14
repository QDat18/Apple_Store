<?php
session_start();
// No longer requiring db.php if no DB interaction is needed specifically for contact form
// require_once 'config/db.php'; // Uncomment if other parts of your site, like header.php, rely on this

// PHPMailer namespaces must be at the top level of the file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['submit_contact'])) {
    // Path to PHPMailer's autoloader, adjust if different
    require 'vendor/autoload.php';

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $errors = [];

    // Basic server-side validation
    if (empty($name)) {
        $errors[] = 'Họ và tên không được để trống.';
    }
    if (empty($email)) {
        $errors[] = 'Email không được để trống.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Địa chỉ email không hợp lệ.';
    }
    if (empty($phone)) {
        $errors[] = 'Số điện thoại không được để trống.';
    } elseif (!preg_match('/^[0-9]{9,15}$/', $phone)) { // Adjusted regex for common phone number lengths (9-15 digits)
        $errors[] = 'Số điện thoại không hợp lệ.';
    }
    if (empty($message)) {
        $errors[] = 'Nội dung không được để trống.';
    }

    if (!empty($errors)) {
        echo "<script>alert('Lỗi: " . implode("\\n", $errors) . "');</script>";
    } else {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'dathoami2k5@gmail.com'; // Replace with your Gmail address
            $mail->Password = 'pmmy ddcn xulj ruvb'; // Replace with your Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
            $mail->Port = 587; // Port for TLS

            // Recipients
            $mail->setFrom($email, $name); // Sender's email and name from the form
            $mail->addAddress('dathoami2k5@gmail.com', 'Admin'); // Recipient (your email)

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Liên hệ từ khách hàng mới: ' . htmlspecialchars($name);
            $mail->Body    = '
                <h2>Thông tin liên hệ mới</h2>
                <p><strong>Tên:</strong> ' . htmlspecialchars($name) . '</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                <p><strong>Số điện thoại:</strong> ' . htmlspecialchars($phone) . '</p>
                <p><strong>Nội dung:</strong></p>
                <p>' . nl2br(htmlspecialchars($message)) . '</p>
            ';
            $mail->AltBody = 'Tên: ' . htmlspecialchars($name) . '\nEmail: ' . htmlspecialchars($email) . '\nSố điện thoại: ' . htmlspecialchars($phone) . '\nNội dung: ' . htmlspecialchars($message);

            $mail->send();
            echo "<script>alert('Gửi liên hệ thành công! Chúng tôi đã nhận được tin nhắn của bạn và sẽ phản hồi sớm.');</script>";
            // Clear form fields after successful submission
            $_POST = array(); // Clears all POST data
        } catch (Exception $e) {
            echo "<script>alert('Gửi liên hệ thất bại. Lỗi: " . $mail->ErrorInfo . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Liên Hệ | Anh Em Rọt Store</title>
    <link rel="icon" href="assets/logo/logo.png" type="image/png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/chatbot.css">
    <link rel="stylesheet" href="css/header.css"> <style>
        /* Keeping old colors from previous contact.php root, if it existed and was intended to be preserved */
        :root {
            --primary-color: #1a1a1a;
            --secondary-color: #2d3748;
            --accent-color: #667eea; /* This is a purple-blue */
            --success-color: #48bb78;
            --danger-color: #f56565;
            --warning-color: #ed8936;
            --info-color: #4299e1;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --border-color: #e2e8f0;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

            /* Default colors from contact.html - these will be overridden if the above are used
               or used for elements not covered by the above.
               You might need to adjust them if you want specific elements to follow contact.html's original red/blue */
            /*
            --primary: #FF0000;
            --primary-hover: #CC0000;
            --secondary: #000000;
            --accent: #1969A6;
            --background: #f8f9fa;
            --card-background: #ffffff;
            */
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* General layout for the contact section */
        .contact-us-section {
            padding: 4rem 0;
            background-color: var(--background-color);
        }

        /* Contact Info Column */
        .contact-info {
            background: var(--card-background);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem; /* Spacing for smaller screens */
            height: 100%; /* Ensure both columns are same height */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .contact-info h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent-color); /* Using old accent color from previous contact.php */
            margin-bottom: 1.5rem;
        }

        .contact-info p {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .contact-info p i {
            margin-right: 0.8rem;
            color: var(--accent-color); /* Using old accent color */
        }

        .social-links {
            margin-top: 1.5rem;
        }

        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background-color: var(--accent-color); /* Using old accent color */
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            font-size: 1.2rem;
            margin-right: 10px;
            transition: var(--transition);
        }

        .social-links a:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-sm);
        }

        /* Contact Form Column */
        .contact-form-container {
            background: var(--card-background);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            height: 100%; /* Ensure both columns are same height */
        }

        .contact-form-container h3 {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        /* Styling for Bootstrap form controls */
        .form-control {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: var(--transition);
            width: 100%; /* Ensure it takes full width of its parent */
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25); /* Bootstrap-like focus shadow with old accent color */
        }

        /* Custom submit button styling, preserving the old gradient */
        .submit-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--accent-color), var(--success-color)); /* Old gradient */
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-sm); /* Add subtle shadow */
            text-decoration: none; /* In case it's an <a> tag */
            justify-content: center; /* Center content if button is wider */
            width: auto; /* Allow button to size naturally */
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: white; /* Ensure text color remains white on hover */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .contact-info {
                margin-bottom: 1.5rem;
                padding: 1.5rem;
            }
            .contact-form-container {
                padding: 1.5rem;
            }
            .contact-info h2 {
                font-size: 1.75rem;
            }
            .contact-form-container h3 {
                font-size: 1.5rem;
            }
            .contact-us-section {
                padding: 2rem 0;
            }
            .submit-btn {
                width: 100%; /* Full width on small screens */
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main class="main-content">
        <section class="contact-us-section animate__animated animate__fadeInUp">
            <div class="container">
                <div class="row align-items-stretch">
                    <div class="col-md-6 animate__animated animate__fadeInLeft">
                        <div class="contact-info">
                            <h2>Liên Hệ Trực Tiếp</h2>
                            <p><i class="fas fa-map-marker-alt"></i> Số 12 Chùa Bộc, Đống Đa, Hà Nội</p>
                            <p><i class="fas fa-phone-alt"></i> (+84) 827592304</p>
                            <p><i class="fas fa-envelope"></i> anhemrotstore12chuaboc@hvnh.edu.vn</p>
                            <div class="social-links">
                                <a href="https://web.facebook.com/quys.hokage" target="_blank"><i class="fab fa-facebook"></i></a>
                                <a href="https://www.instagram.com/_yud.gnauq/" target="_blank"><i class="fab fa-instagram"></i></a>
                                <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 animate__animated animate__fadeInRight">
                        <div class="contact-form-container">
                            <h3>Gửi Tin Nhắn Cho Chúng Tôi</h3>
                            <form method="post" action="">
                                <div class="form-group">
                                    <label for="name">Tên của bạn *</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên của bạn" required
                                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email của bạn *</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email của bạn" required
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Số điện thoại *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Nhập số điện thoại của bạn" required
                                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="message">Lời nhắn *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Nhập lời nhắn của bạn" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                                </div>
                                <button type="submit" name="submit_contact" class="submit-btn"><i class="fas fa-paper-plane"></i> Gửi Tin Nhắn</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const animateElements = document.querySelectorAll('.animate__animated');

            function checkIfInView() {
                const windowHeight = window.innerHeight;
                const windowTopPosition = window.scrollY;
                const windowBottomPosition = windowTopPosition + windowHeight;

                animateElements.forEach(element => {
                    const elementHeight = element.offsetHeight;
                    const elementTopPosition = element.getBoundingClientRect().top + window.scrollY;
                    const elementBottomPosition = elementTopPosition + elementHeight;

                    // Check if element is in viewport
                    if ((elementBottomPosition >= windowTopPosition) &&
                        (elementTopPosition <= windowBottomPosition)) {

                        // Get animation class
                        let animationClass = 'animate__fadeIn';

                        if (element.classList.contains('animate__fadeInLeft')) {
                            animationClass = 'animate__fadeInLeft';
                        } else if (element.classList.contains('animate__fadeInRight')) {
                            animationClass = 'animate__fadeInRight';
                        } else if (element.classList.contains('animate__fadeInUp')) {
                            animationClass = 'animate__fadeInUp';
                        }

                        element.classList.add(animationClass);
                    }
                });
            }

            window.addEventListener('scroll', checkIfInView);
            window.addEventListener('load', checkIfInView); // Run on load to animate elements already in view
        });
    </script>
</body>

</html>