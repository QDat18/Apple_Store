<?php
session_start();
require_once 'config/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$first_login = isset($_GET['first_login']) && $_GET['first_login'] == 1;
$errors = [];
$success = false;

// Lấy thông tin người dùng hiện tại
$stmt = $conn->prepare("SELECT full_name, email, phone, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_full_name, $current_email, $current_phone, $current_address);
$stmt->fetch();
$stmt->close();

// Khởi tạo giá trị mặc định
$full_name = $current_full_name ?? '';
$phone = $current_phone ?? '';
$address = $current_address ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    // Validation
    if (empty($full_name)) {
        $errors[] = "Họ và tên không được để trống.";
    } elseif (strlen($full_name) < 2) {
        $errors[] = "Họ và tên phải có ít nhất 2 ký tự.";
    } elseif (!preg_match("/^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵýỷỹ\s]+$/u", $full_name)) {
        $errors[] = "Họ và tên chỉ được chứa chữ cái và khoảng trắng.";
    }
    
    if (!empty($phone)) {
        if (!preg_match("/^[0-9+\-\s\(\)]+$/", $phone)) {
            $errors[] = "Số điện thoại không hợp lệ.";
        } elseif (strlen(preg_replace("/[^0-9]/", "", $phone)) < 10) {
            $errors[] = "Số điện thoại phải có ít nhất 10 chữ số.";
        }
    }
    
    if ($first_login) {
        if (empty($phone)) {
            $errors[] = "Số điện thoại không được để trống.";
        }
        if (empty($address)) {
            $errors[] = "Địa chỉ không được để trống.";
        }
    }
    
    if (strlen($address) > 255) {
        $errors[] = "Địa chỉ không được vượt quá 255 ký tự.";
    }
    
    // Cập nhật thông tin nếu không có lỗi
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("sssi", $full_name, $phone, $address, $user_id);
        
        if ($stmt->execute()) {
            $success = true;
            $_SESSION['full_name'] = $full_name;
            
            if ($first_login) {
                // Chuyển hướng về trang chủ sau khi cập nhật thành công lần đầu
                header("Location: index.php?welcome=1");
                exit;
            }
        } else {
            $errors[] = "Có lỗi xảy ra khi cập nhật thông tin. Vui lòng thử lại.";
        }
        $stmt->close();
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
    <link rel="stylesheet" href="css/auth.css">
    <style>
        .required-field {
            color: red;
        }
        .profile-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .profile-info h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .profile-info p {
            margin: 5px 0;
            color: #666;
        }
        .form-textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
            min-height: 80px;
        }
        .form-textarea:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        .welcome-message {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .welcome-message h2 {
            margin: 0 0 10px 0;
        }
        .welcome-message p {
            margin: 0;
            opacity: 0.9;
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main class="container">
        <div class="auth-container" style="max-width: 600px;">
            <?php if ($first_login): ?>
                <div class="welcome-message">
                    <h2><i class="fas fa-user-plus"></i> Chào mừng bạn đến với Anh Em Rọt Store!</h2>
                    <p>Để hoàn tất quá trình đăng ký, vui lòng cập nhật thông tin cá nhân của bạn.</p>
                </div>
            <?php endif; ?>

            <div class="auth-header">
                <h1 class="auth-title">
                    <?= $first_login ? 'Hoàn tất đăng ký' : 'Cập nhật thông tin cá nhân' ?>
                </h1>
                <p class="auth-subtitle">
                    <?= $first_login ? 'Bước cuối cùng để bắt đầu mua sắm' : 'Cập nhật thông tin tài khoản của bạn' ?>
                </p>
            </div>

            <div class="profile-info">
                <h3><i class="fas fa-envelope"></i> Thông tin tài khoản</h3>
                <p><strong>Email:</strong> <?= htmlspecialchars($current_email) ?></p>
                <p><strong>Vai trò:</strong> <?= $_SESSION['role'] == 'admin' ? 'Quản trị viên' : 'Khách hàng' ?></p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="color: red; margin: 10px 0;">
                    <?php foreach ($errors as $e): ?>
                        <p><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($e) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($success): ?>
                <div class="alert alert-success" style="color: green; margin: 10px 0;">
                    <p><i class="fas fa-check-circle"></i> Thông tin đã được cập nhật thành công!</p>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST">
                <div class="form-group">
                    <label for="full_name" class="form-label">
                        Họ và tên <span class="required-field">*</span>
                    </label>
                    <input type="text" name="full_name" id="full_name" class="form-input" 
                           placeholder="Nhập họ và tên đầy đủ" 
                           value="<?= htmlspecialchars($full_name) ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">
                        Số điện thoại <?= $first_login ? '<span class="required-field">*</span>' : '' ?>
                    </label>
                    <input type="tel" name="phone" id="phone" class="form-input" 
                           placeholder="Nhập số điện thoại (VD: 0123456789)" 
                           value="<?= htmlspecialchars($phone) ?>" 
                           <?= $first_login ? 'required' : '' ?>>
                </div>
                
                <div class="form-group">
                    <label for="address" class="form-label">
                        Địa chỉ <?= $first_login ? '<span class="required-field">*</span>' : '' ?>
                    </label>
                    <textarea name="address" id="address" class="form-textarea" 
                              placeholder="Nhập địa chỉ đầy đủ (số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố)" 
                              <?= $first_login ? 'required' : '' ?>><?= htmlspecialchars($address) ?></textarea>
                </div>
                
                <button type="submit" class="auth-button">
                    <i class="fas fa-save"></i> 
                    <?= $first_login ? 'Hoàn tất đăng ký' : 'Cập nhật thông tin' ?>
                </button>
            </form>
            
            <?php if (!$first_login): ?>
                <div class="auth-footer">
                    <a href="index.php" class="form-link">
                        <i class="fas fa-arrow-left"></i> Quay lại trang chủ
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script>
        // Format số điện thoại
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Chỉ giữ lại số
            if (value.length > 0) {
                if (value.startsWith('84')) {
                    value = '+84' + value.substring(2);
                } else if (value.startsWith('0')) {
                    // Giữ nguyên số bắt đầu bằng 0
                }
            }
            e.target.value = value;
        });
        
        // Validation form
        document.querySelector('.auth-form').addEventListener('submit', function(e) {
            const fullName = document.getElementById('full_name').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const address = document.getElementById('address').value.trim();
            
            if (!fullName) {
                e.preventDefault();
                alert('Vui lòng nhập họ và tên!');
                return false;
            }
            
            if (fullName.length < 2) {
                e.preventDefault();
                alert('Họ và tên phải có ít nhất 2 ký tự!');
                return false;
            }
            
            <?php if ($first_login): ?>
            if (!phone) {
                e.preventDefault();
                alert('Vui lòng nhập số điện thoại!');
                return false;
            }
            
            if (!address) {
                e.preventDefault();
                alert('Vui lòng nhập địa chỉ!');
                return false;
            }
            <?php endif; ?>
            
            if (phone && phone.replace(/\D/g, '').length < 10) {
                e.preventDefault();
                alert('Số điện thoại phải có ít nhất 10 chữ số!');
                return false;
            }
        });
        
        // Auto-focus vào trường đầu tiên
        document.getElementById('full_name').focus();
    </script>
</body>

</html>