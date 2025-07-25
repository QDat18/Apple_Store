<?php
session_start();
require_once 'config/db.php'; // Ensure this file correctly sets up $conn

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$first_login = isset($_GET['first_login']) && $_GET['first_login'] == 1;
$errors = [];
$success = false;
$current_avatar_path = ''; // To store the current avatar path for deletion if a new one is uploaded

// Check and display messages from session
if (isset($_SESSION['update_success'])) {
    $success = true;
    unset($_SESSION['update_success']);
}

if (isset($_SESSION['update_errors'])) {
    $errors = $_SESSION['update_errors'];
    unset($_SESSION['update_errors']);
}

$upload_dir = __DIR__ . '/assets/avatars/';
if (!is_dir($upload_dir)) {
    // Ensure directory is created with appropriate permissions
    if (!mkdir($upload_dir, 0755, true)) {
        $errors[] = "Không thể tạo thư mục tải lên avatar. Vui lòng liên hệ quản trị viên.";
    }
}

// Fetch user details from user_detail table
$stmt = $conn->prepare("SELECT first_name, last_name, avatar, phone_number, address, gender, date_of_birth FROM user_detail WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

$first_name = '';
$last_name = '';
$phone_number = '';
$address = '';
$gender = '';
$date_of_birth = '';
$avatar = '';

if ($stmt->num_rows === 1) {
    $stmt->bind_result($first_name, $last_name, $avatar, $phone_number, $address, $gender, $date_of_birth);
    $stmt->fetch();
    $current_avatar_path = $avatar; // Store current avatar path
}
$stmt->close();

// Fetch email from users table
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_email);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get POST data
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';

    // --- Validation ---
    if (empty($first_name)) {
        $errors[] = "Họ không được để trống.";
    }
    if (empty($last_name)) {
        $errors[] = "Tên không được để trống.";
    }
    if ($first_login && empty($phone_number)) {
        $errors[] = "Số điện thoại không được để trống.";
    } elseif (!empty($phone_number) && !preg_match('/^[0-9]{10,11}$/', $phone_number)) {
        $errors[] = "Số điện thoại không hợp lệ (chỉ chấp nhận 10-11 chữ số).";
    }
    if ($first_login && empty($address)) {
        $errors[] = "Địa chỉ không được để trống.";
    }
    if (!empty($date_of_birth) && !strtotime($date_of_birth)) {
        $errors[] = "Ngày sinh không hợp lệ.";
    }

    $new_avatar = $current_avatar_path; // Assume no change in avatar initially

    // --- Avatar Upload Handling ---
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['avatar']['name'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_size = $_FILES['avatar']['size'];
        $file_type = $_FILES['avatar']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $max_file_size = 5 * 1024 * 1024; // 5MB

        // Validate file extension
        if (!in_array($file_ext, $allowed_extensions)) {
            $errors[] = "Chỉ chấp nhận file ảnh JPG, JPEG, PNG.";
        }

        // Validate file size
        if ($file_size > $max_file_size) {
            $errors[] = "Kích thước ảnh tối đa là 5MB.";
        }

        // Validate actual image type using getimagesize
        $image_info = getimagesize($file_tmp);
        if ($image_info === false) {
            $errors[] = "File tải lên không phải là ảnh hợp lệ.";
        }

        if (empty($errors)) {
            // Generate unique filename to prevent overwriting and enhance security
            $unique_filename = md5(uniqid(rand(), true)) . '.' . $file_ext;
            $destination = $upload_dir . $unique_filename;

            if (move_uploaded_file($file_tmp, $destination)) {
                // Delete old avatar if a new one is successfully uploaded and it's not a default placeholder
                if ($current_avatar_path && file_exists(__DIR__ . '/' . $current_avatar_path) && strpos($current_avatar_path, 'assets/avatars/') !== false) {
                    unlink(__DIR__ . '/' . $current_avatar_path);
                }
                $new_avatar = '/Apple_Store/' . $unique_filename;
            } else {
                $errors[] = "Có lỗi xảy ra khi tải ảnh lên. Vui lòng thử lại.";
            }
        }
    } elseif (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle other upload errors
        switch ($_FILES['avatar']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = "Kích thước file quá lớn.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $errors[] = "File chỉ được tải lên một phần.";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $errors[] = "Thiếu thư mục tạm thời.";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $errors[] = "Không thể ghi file vào đĩa.";
                break;
            case UPLOAD_ERR_EXTENSION:
                $errors[] = "Một extension của PHP đã dừng quá trình upload file.";
                break;
            default:
                $errors[] = "Lỗi không xác định khi tải file.";
                break;
        }
    }

    // --- Update Information if no errors ---
    if (empty($errors)) {
        // Check if user_detail record exists
        $check_stmt = $conn->prepare("SELECT id FROM user_detail WHERE user_id = ?");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // Update existing record
            $stmt = $conn->prepare("UPDATE user_detail SET first_name = ?, last_name = ?, phone_number = ?, address = ?, gender = ?, date_of_birth = ?, avatar = ? WHERE user_id = ?");
            $stmt->bind_param("sssssssi", $first_name, $last_name, $phone_number, $address, $gender, $date_of_birth, $new_avatar, $user_id);
        } else {
            // Insert new record
            $stmt = $conn->prepare("INSERT INTO user_detail (user_id, first_name, last_name, phone_number, address, gender, date_of_birth, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $user_id, $first_name, $last_name, $phone_number, $address, $gender, $date_of_birth, $new_avatar);
        }
        $check_stmt->close();

        if ($stmt->execute()) {
            // Update is_updated in users table
            $update_stmt = $conn->prepare("UPDATE users SET is_updated = 1 WHERE id = ?");
            $update_stmt->bind_param("i", $user_id);
            $update_stmt->execute();
            $update_stmt->close();

            // Store success message and user details in session
            $_SESSION['update_success'] = true;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['avatar'] = $new_avatar; // Update avatar in session

            if ($first_login) {
                header("Location: index.php?welcome=1");
            } else {
                header("Location: update_profile.php"); // Redirect to self to show success message and clear POST data
            }
            exit;
        } else {
            $errors[] = "Có lỗi xảy ra khi cập nhật thông tin. Vui lòng thử lại. Lỗi: " . $stmt->error;
        }
        $stmt->close();
    }

    // If there are errors, save them to session and redirect
    if (!empty($errors)) {
        $_SESSION['update_errors'] = $errors;
        header("Location: update_profile.php?first_login=" . ($first_login ? '1' : '0')); // Preserve first_login status
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $first_login ? 'Hoàn tất đăng ký' : 'Cập nhật thông tin' ?> | Anh Em Rọt Store</title>
    <link rel="icon" href="assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <style>
        /* Variables for consistent styling */
        :root {
            --primary-color: #3498db; /* Blue */
            --secondary-color: #2ecc71; /* Green for success */
            --error-color: #e74c3c; /* Red for errors */
            --text-color: #333;
            --light-text-color: #666;
            --bg-color-light: #f4f7f6;
            --bg-color-card: #ffffff;
            --border-color: #e0e0e0;
            --shadow-light: 0 4px 15px rgba(0, 0, 0, 0.08);
            --border-radius: 10px;
            --spacing-medium: 20px;
            --spacing-large: 30px;
        }

        /* General body styling for better spacing */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Modern, clean font */
            background-color: var(--bg-color-light);
            margin: 0;
            padding: 0;
            color: var(--text-color);
            line-height: 1.6;
        }

        /* Header (assuming header.css handles this, but adding minimal top margin for content below) */
        header {
            margin-bottom: var(--spacing-large);
        }

        /* Styles for the two-column layout */
        .main-container {
            display: flex; /* Use flexbox for layout */
            justify-content: center; /* Center content horizontally */
            gap: var(--spacing-large); /* Space between columns */
            padding: var(--spacing-large) var(--spacing-medium); /* Padding around the columns */
            max-width: 1400px; /* Max width for content */
            margin: var(--spacing-medium) auto; /* Center the main container */
            align-items: flex-start; /* Align items to the top */
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
        }

        .profile-section {
            flex: 2; /* Takes 2 parts of the available space */
            min-width: 350px; /* Minimum width to prevent squishing */
            background-color: var(--bg-color-card);
            padding: var(--spacing-large);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
        }

        .info-section {
            flex: 1; /* Takes 1 part of the available space */
            min-width: 280px; /* Minimum width */
            display: flex;
            flex-direction: column;
            gap: var(--spacing-medium);
        }

        .profile-info {
            background-color: var(--bg-color-card);
            padding: var(--spacing-medium);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
        }

        .profile-info h3 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .profile-info p {
            margin-bottom: 10px;
            font-size: 0.95rem;
            color: var(--light-text-color);
        }

        .profile-info p strong {
            color: var(--text-color);
        }

        /* Form specific styles */
        h1 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: var(--spacing-large);
            font-size: 1.8rem;
            text-align: center; /* Center the main title */
        }

        .form-group {
            margin-bottom: var(--spacing-medium);
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600; /* Slightly bolder */
            color: var(--text-color);
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
            font-size: 1rem;
            color: var(--text-color);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
        }

        .form-2col {
            display: flex;
            gap: var(--spacing-medium);
            margin-bottom: var(--spacing-medium);
        }

        .form-2col .form-group {
            flex: 1;
            margin-bottom: 0; /* Remove extra margin from individual groups in 2-col */
        }

        /* Buttons */
        .auth-button {
            width: 90%;
            padding: 15px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: var(--spacing-medium);
            text-decoration: none; /* For the anchor tag styled as a button */
        }

        .auth-button:hover {
            background-color: #2980b9; /* Darker blue */
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .auth-button.secondary { /* For the change password button */
            background-color: var(--secondary-color);
            margin-top: 0;
        }

        .auth-button.secondary:hover {
            background-color: #27ae60; /* Darker green */
        }

        /* Avatar styles */
        .avatar-center {
            text-align: center;
            margin-bottom: var(--spacing-large);
        }

        .avatar-preview {
            width: 120px; /* Slightly smaller, more refined */
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 15px auto;
            border: 3px solid var(--primary-color); /* Highlight with primary color */
            box-shadow: 0 0 0 5px rgba(52, 152, 219, 0.1); /* Subtle ring shadow */
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f0f0; /* Background for empty avatar */
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-input p {
            font-size: 0.85rem;
            color: var(--light-text-color);
            margin-top: 5px;
        }

        /* Welcome message and required field indicator */
        .welcome-message {
            background-color: #e8f4f8; /* Light blue background */
            border: 1px solid #b3dceb;
            padding: var(--spacing-medium);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-large);
            color: var(--text-color);
        }

        .welcome-message h2 {
            margin-top: 0;
            color: var(--primary-color);
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .welcome-message p {
            margin-bottom: 0;
            line-height: 1.6;
        }

        .required-field {
            color: var(--error-color);
            font-weight: normal; /* Keep normal weight for asterisk */
        }

        /* Notifications (from previous version) */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); /* Stronger shadow for pop-up */
            z-index: 1000;
            animation: slideIn 0.5s forwards, fadeOut 0.5s forwards 3.5s; /* Slightly longer fadeOut */
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }

        .notification.success {
            background: var(--secondary-color);
        }

        .notification.error {
            background: var(--error-color);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        /* Modal specific styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 2000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.6); /* Black w/ opacity */
            display: none; /* Use flex to center content */
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 30px;
            border-radius: var(--border-radius);
            width: 90%; /* Smaller width on small screens */
            max-width: 500px; /* Max width for larger screens */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative; /* Needed for close button positioning */
            animation: fadeIn 0.3s ease-out; /* Simple fade in animation */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-content h2 {
            margin-top: 0;
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 25px;
            text-align: center;
        }

        .modal-content .form-group {
            margin-bottom: 15px;
        }

        .modal-close-btn {
            color: #aaa;
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .modal-close-btn:hover,
        .modal-close-btn:focus {
            color: var(--error-color);
            text-decoration: none;
            cursor: pointer;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end; /* Align buttons to the right */
            gap: 10px;
            margin-top: 25px;
        }

        .modal-actions .auth-button {
            width: auto; /* Override full width */
            padding: 10px 20px;
            font-size: 1rem;
            margin-top: 0; /* Remove top margin */
        }

        .modal-actions .auth-button.cancel {
            background-color: #95a5a6; /* Grey for cancel */
        }
        .modal-actions .auth-button.cancel:hover {
            background-color: #7f8c8d;
        }


        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column; /* Stack columns vertically on smaller screens */
                padding: 20px;
                margin: 15px auto;
                gap: var(--spacing-medium);
            }

            .profile-section, .info-section {
                flex: none; /* Remove flex sizing */
                width: 100%; /* Take full width */
                min-width: unset; /* Remove min-width restriction */
                padding: var(--spacing-medium); /* Reduce padding on small screens */
            }

            .form-2col {
                flex-direction: column; /* Stack 2-column form groups vertically */
                gap: 0; /* Remove gap */
            }

            h1 {
                font-size: 1.5rem;
                margin-bottom: var(--spacing-medium);
            }

            .profile-info h3 {
                font-size: 1.15rem;
            }

            .auth-button {
                font-size: 1rem;
                padding: 12px 15px;
            }

            .welcome-message {
                padding: calc(var(--spacing-medium) - 5px); /* Adjust padding */
            }

            .notification {
                top: 15px;
                right: 15px;
                padding: 12px 15px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <?php if ($success): ?>
        <div class="notification success">
            <i class="fas fa-check-circle"></i>
            <span>Thông tin đã được cập nhật thành công!</span>
        </div>
    <?php elseif (!empty($errors)): ?>
        <div class="notification error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <main class="main-container">
        <div class="profile-section">
            <?php if ($first_login): ?>
                <div class="welcome-message">
                    <h2><i class="fas fa-user-plus"></i> Chào mừng bạn đến với Anh Em Rọt Store!</h2>
                    <p>Để hoàn tất quá trình đăng ký, vui lòng cập nhật thông tin cá nhân của bạn.</p>
                </div>
            <?php endif; ?>

            <h1>
                <?= $first_login ? 'Hoàn tất đăng ký' : 'Cập nhật thông tin' ?>
            </h1>

            <form method="POST" enctype="multipart/form-data">
                <div class="avatar-center">
                    <div class="avatar-preview">
                        <?php
                        // Check if avatar path exists and file exists on server
                        $display_avatar = ($avatar && file_exists(__DIR__ . '/' . $avatar)) ? htmlspecialchars($avatar) : 'assets/avatars/default_avatar.png'; // Provide a default if no avatar or file not found
                        ?>
                        <img src="<?= $display_avatar ?>" alt="Avatar">
                    </div>
                    <div class="avatar-input">
                        <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/jpg">
                        <p>Chọn ảnh (JPG, PNG, tối đa 5MB)</p>
                    </div>
                </div>

                <div class="form-2col">
                    <div class="form-group">
                        <label for="first_name" class="form-label">Họ <span class="required-field">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="form-input"
                            value="<?= htmlspecialchars($first_name) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name" class="form-label">Tên <span class="required-field">*</span></label>
                        <input type="text" name="last_name" id="last_name" class="form-input"
                            value="<?= htmlspecialchars($last_name) ?>" required>
                    </div>
                </div>

                <div class="form-2col">
                    <div class="form-group">
                        <label for="phone_number" class="form-label">
                            Số điện thoại <?= $first_login ? '<span class="required-field">*</span>' : '' ?>
                        </label>
                        <input type="tel" name="phone_number" id="phone_number" class="form-input"
                            value="<?= htmlspecialchars($phone_number) ?>" <?= $first_login ? 'required' : '' ?>>
                    </div>
                    <div class="form-group">
                        <label for="gender" class="form-label">Giới tính</label>
                        <select name="gender" id="gender" class="form-input">
                            <option value="">Chọn giới tính</option>
                            <option value="male" <?= $gender == 'male' ? 'selected' : '' ?>>Nam</option>
                            <option value="female" <?= $gender == 'female' ? 'selected' : '' ?>>Nữ</option>
                            <option value="other" <?= $gender == 'other' ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="date_of_birth" class="form-label">Ngày sinh</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-input"
                        value="<?= htmlspecialchars($date_of_birth ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">
                        Địa chỉ <?= $first_login ? '<span class="required-field">*</span>' : '' ?>
                    </label>
                    <textarea name="address" id="address" class="form-input" rows="3" <?= $first_login ? 'required' : '' ?>><?= htmlspecialchars($address) ?></textarea>
                </div>

                <button type="submit" class="auth-button">
                    <i class="fas fa-save"></i> <?= $first_login ? 'Hoàn tất đăng ký' : 'Cập nhật thông tin' ?>
                </button>
            </form>
        </div>

        <div class="info-section">
            <div class="profile-info">
                <h3><i class="fas fa-user-circle"></i> Thông tin tài khoản</h3>
                <p><strong>Họ tên:</strong> <?= htmlspecialchars($first_name . ' ' . $last_name) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($current_email) ?></p>
                <p><strong>Vai trò:</strong> <?= $_SESSION['role'] == 'admin' ? 'Quản trị viên' : 'Khách hàng' ?></p>
            </div>

            <div class="profile-info">
                <h3><i class="fas fa-cog"></i> Tùy chọn tài khoản</h3>
                <button class="auth-button secondary" id="changePasswordBtn">
                    <i class="fas fa-key"></i> Đổi mật khẩu
                </button>
            </div>

            <?php if (!$first_login): ?>
                <div class="profile-info">
                    <h3><i class="fas fa-home"></i> Quay về</h3>
                    <a href="index.php" class="auth-button" style="background: #f8fafc; color: var(--primary-color); border: 1px solid var(--primary-color);">
                        <i class="fas fa-arrow-left"></i> Trang chủ
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <div id="changePasswordModal" class="modal">
        <div class="modal-content">
            <span class="modal-close-btn">&times;</span>
            <h2><i class="fas fa-key"></i> Đổi mật khẩu</h2>
            <form id="changePasswordForm" method="POST" action="change_password_process.php">
                <div class="form-group">
                    <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" id="current_password" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                    <input type="password" name="new_password" id="new_password" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="confirm_new_password" class="form-label">Xác nhận mật khẩu mới</label>
                    <input type="password" name="confirm_new_password" id="confirm_new_password" class="form-input" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="auth-button cancel" id="cancelChangePassword">Hủy</button>
                    <button type="submit" class="auth-button">Lưu mật khẩu mới</button>
                </div>
            </form>
        </div>
    </div>

    
    <script>
        // Auto-hide notifications after 3 seconds
        setTimeout(() => {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(notification => {
                notification.style.animationPlayState = 'running'; // Start fadeOut animation
                // After fadeOut animation, hide element
                notification.addEventListener('animationend', () => {
                    if (notification.style.animationName === 'fadeOut') {
                        notification.style.display = 'none';
                    }
                });
            });
        }, 3000); // 3 seconds delay before fadeOut starts

        // Modal Logic
        const changePasswordBtn = document.getElementById('changePasswordBtn');
        const changePasswordModal = document.getElementById('changePasswordModal');
        const closeModalBtn = document.querySelector('.modal-close-btn');
        const cancelChangePasswordBtn = document.getElementById('cancelChangePassword');

        // Event listener to open the modal
        if (changePasswordBtn) {
            changePasswordBtn.addEventListener('click', () => {
                changePasswordModal.style.display = 'flex'; 
            });
        }

        // Event listeners to close the modal
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', () => {
                changePasswordModal.style.display = 'none'; // Hide the modal
            });
        }

        if (cancelChangePasswordBtn) {
            cancelChangePasswordBtn.addEventListener('click', () => {
                changePasswordModal.style.display = 'none'; // Hide the modal
            });
        }

        // Close modal if clicked outside of the modal content
        if (changePasswordModal) {
            changePasswordModal.addEventListener('click', (event) => {
                if (event.target === changePasswordModal) {
                    changePasswordModal.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>