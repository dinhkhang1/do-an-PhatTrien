<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/add.css?v=6"> <!-- Tăng version để clear cache -->
    <title>Thêm người dùng mới</title>
</head>

<body>
    <button class="add"><a href="../Admin/adminPage.php">Quay về</a></button>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form">

        <h2>Thêm người dùng mới</h2>

        <?php
        // Hiển thị thông báo lỗi nếu có
        include("../Database/database.php");

        $error_message = "";
        try {
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $user_id   = trim($_POST["user_id"]);
                $cus_name  = trim($_POST["cus_name"]);
                $password  = $_POST["password"];         // Nên hash sau này: password_hash($password, PASSWORD_DEFAULT)
                $email     = trim($_POST["email"]);
                $phone     = trim($_POST["phone"]);
                $diachi    = trim($_POST["diachi"]);

                // Validate cơ bản
                if (empty($user_id) || empty($cus_name) || empty($password) || empty($email)) {
                    $error_message = "Vui lòng điền đầy đủ các trường bắt buộc!";
                } else {
                    $sql_insert = $conn->prepare("INSERT INTO users (user_id, cus_name, password, email, phone, diachi)
                                              VALUES (:user_id, :cus_name, :password, :email, :phone, :diachi)");
                    $sql_insert->bindParam(':user_id', $user_id);
                    $sql_insert->bindParam(':cus_name', $cus_name);
                    $sql_insert->bindParam(':password', $password); // Sau này nên hash
                    $sql_insert->bindParam(':email', $email);
                    $sql_insert->bindParam(':phone', $phone);
                    $sql_insert->bindParam(':diachi', $diachi);

                    $sql_insert->execute();

                    // Thành công → quay về trang quản lý user
                    header("Location: ../Admin/adminPageUser.php");
                    exit();
                }
            }
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $error_message = "Lỗi: ID người dùng đã tồn tại! Vui lòng chọn ID khác.";
            } else {
                $error_message = "Có lỗi xảy ra: " . $e->getMessage();
            }
        }

        // Hiển thị lỗi nếu có
        if (!empty($error_message)) {
            echo '<p class="error">' . htmlspecialchars($error_message) . '</p>';
        }
        ?>

        <div>
            <label for="user_id">ID người dùng *</label>
            <input type="text" name="user_id" required placeholder="Ví dụ: USER001" value="<?php echo isset($_POST['user_id']) ? htmlspecialchars($_POST['user_id']) : ''; ?>">
        </div>

        <div>
            <label for="cus_name">Họ và tên *</label>
            <input type="text" name="cus_name" required placeholder="Nguyễn Văn A" value="<?php echo isset($_POST['cus_name']) ? htmlspecialchars($_POST['cus_name']) : ''; ?>">
        </div>

        <div>
            <label for="password">Mật khẩu *</label>
            <input type="password" name="password" required placeholder="Ít nhất 6 ký tự">
        </div>

        <div>
            <label for="email">Email *</label>
            <input type="email" name="email" required placeholder="example@gmail.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>

        <div>
            <label for="phone">Số điện thoại</label>
            <input type="text" name="phone" placeholder="0901234567" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
        </div>

        <div>
            <label for="diachi">Địa chỉ</label>
            <input type="text" name="diachi" placeholder="Số nhà, đường, quận/huyện..." value="<?php echo isset($_POST['diachi']) ? htmlspecialchars($_POST['diachi']) : ''; ?>">
        </div>

        <button type="submit">Lưu người dùng</button>
    </form>

</body>

</html>