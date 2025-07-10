
<?php
require_once 'config/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!$email || !$password) {
        $errors[] = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $fullName, $hashedPass, $role);
            $stmt->fetch();

            if (password_verify($password, $hashedPass)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['full_name'] = $fullName;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;

                header("Location: index.php");
                exit;
            } else {
                $errors[] = "Mật khẩu không đúng.";
            }
        } else {
            $errors[] = "Email không tồn tại.";
        }
        $stmt->close();
    }
}
?>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger" style="color: red; margin: 10px 0;">
    <?php foreach ($errors as $e): ?>
      <p><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
