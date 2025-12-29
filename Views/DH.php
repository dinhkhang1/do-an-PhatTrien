<?php
session_start();
include("../Database/database.php");
/** @var PDO $conn */
// Kiểm tra đăng nhập
if (!isset($_SESSION["user_id"])) {
    echo "<div style='padding:40px;text-align:center'>
            Bạn phải đăng nhập để xem đơn hàng.<br>
            <a href='../Authen/login.php'>Đăng nhập ngay</a>
          </div>";
    exit();
}

$user_id = $_SESSION["user_id"];


$sql = "SELECT order_id, cus_name, cus_phone, diachi, tong_tien
        FROM `order`
        WHERE user_id = ?
        ORDER BY order_id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đơn hàng của tôi</title>
    <link rel="stylesheet" href="../css/DH.css">
</head>

<body>

    <div class="container">
        <h2>Danh sách đơn hàng</h2>
        <a href="../Index.php" class="btn-detail">Quay lại trang chủ</a>
        <hr>

        <?php if (count($orders) === 0): ?>
            <p>Bạn chưa có đơn hàng nào.</p>

        <?php else: ?>
            <?php foreach ($orders as $row): ?>
                <div class="order-card">

                    <div class="order-header">
                        <span>Đơn hàng #<?= $row["order_id"] ?></span>
                        <span><?= number_format($row["tong_tien"]) ?> đ</span>
                    </div>

                    <div class="order-meta">
                        Người nhận: <?= $row["cus_name"] ?> — <?= $row["cus_phone"] ?><br>
                        Địa chỉ: <?= $row["diachi"] ?>
                    </div>

                    <br>
                    <a class="btn-detail" href="orderdt.php?id=<?= $row["order_id"] ?>">
                        Xem chi tiết
                    </a>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

</body>

</html>