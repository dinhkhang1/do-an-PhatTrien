<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/add.css?v=10"> <!-- Tăng version để clear cache -->
    <title>Cập nhật người dùng</title>
</head>

<body>

    <?php
    include("../Database/database.php");

    $error_message = "";
    $user = null;

    if (!isset($_GET["id"]) || empty($_GET["id"])) {
        $error_message = "Không tìm thấy ID người dùng!";
    } else {
        $id = $_GET["id"];

        // Lấy dữ liệu người dùng hiện tại
        try {
            $sql_select = "SELECT * FROM users WHERE user_id = :user_id";
            $stmt_select = $conn->prepare($sql_select);
            $stmt_select->bindParam(':user_id', $id, PDO::PARAM_STR);
            $stmt_select->execute();
            $user = $stmt_select->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $error_message = "Không tìm thấy người dùng với ID: " . htmlspecialchars($id);
            }
        } catch (PDOException $e) {
            $error_message = "Lỗi kết nối dữ liệu: " . $e->getMessage();
        }
    }

    // Xử lý cập nhật khi submit form
    if ($_SERVER["REQUEST_METHOD"] === "POST" && $user) {
        $user_id   = trim($_POST["user_id"]);
        $cus_name  = trim($_POST["cus_name"]);
        $password  = trim($_POST["password"]); // Gợi ý: sau này dùng password_hash()
        $email     = trim($_POST["email"]);
        $phone     = trim($_POST["phone"]);
        $diachi    = trim($_POST["diachi"]);

        try {
            $sql_update = "UPDATE users SET 
                       cus_name = :cus_name,
                       password = :password,
                       email = :email,
                       phone = :phone,
                       diachi = :diachi
                       WHERE user_id = :user_id";

            $stmt = $conn->prepare($sql_update);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':cus_name', $cus_name);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':diachi', $diachi);

            $stmt->execute();

            // Thành công → quay về trang quản lý user
            header("Location: ../Admin/adminPageUser.php");
            exit();
        } catch (PDOException $e) {
            $error_message = "Cập nhật thất bại: " . $e->getMessage();
        }
    }
    ?>
    <a href="../Admin/adminPageUser.php" class="add">Quay về</a>
    <form action="" method="post" class="form">
        <h2>Cập nhật người dùng</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <?php if ($user): ?>
            <div>
                <label for="user_id">ID người dùng</label>
                <input type="text" name="user_id" required readonly
                    value="<?= htmlspecialchars($user['user_id']) ?>"
                    placeholder="ID không thể thay đổi">
            </div>

            <div>
                <label for="cus_name">Họ và tên *</label>
                <input type="text" name="cus_name" required
                    value="<?= htmlspecialchars($user['cus_name']) ?>">
            </div>

            <div>
                <label for="password">Mật khẩu mới</label>
                <input type="password" name="password"
                    placeholder="Để trống nếu không muốn đổi mật khẩu"
                    autocomplete="new-password">
                <small style="color:#7f8c8d; font-size:13px; display:block; margin-top:5px;">
                    Mật khẩu hiện tại: <?= htmlspecialchars($user['password']) ?> (hiện dạng text để xem)
                </small>
            </div>

            <div>
                <label for="email">Email *</label>
                <input type="email" name="email" required
                    value="<?= htmlspecialchars($user['email']) ?>">
            </div>

            <div>
                <label for="phone">Số điện thoại</label>
                <input type="text" name="phone"
                    value="<?= htmlspecialchars($user['phone']) ?>"
                    placeholder="0901234567">
            </div>

            <div>
                <label for="diachi">Địa chỉ</label>
                <input type="text" name="diachi"
                    value="<?= htmlspecialchars($user['diachi']) ?>"
                    placeholder="Số nhà, đường, quận/huyện...">
            </div>

            <button type="submit">Lưu thay đổi</button>
        <?php endif; ?>

        <!-- Nút quay về -->

    </form>

</body>

</html>