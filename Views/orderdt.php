<?php
session_start();
include("../Database/database.php");
/** @var PDO $conn */
if (!isset($_SESSION["user_id"])) {
    echo "Bạn phải đăng nhập để xem đơn hàng.";
    exit();
}

if (!isset($_GET["id"])) {
    echo "Thiếu mã đơn hàng.";
    exit();
}

$order_id = intval($_GET["id"]);
$user_id  = $_SESSION["user_id"];

// Kiểm tra quyền
$sql = "SELECT * FROM `order`
        WHERE order_id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Không tìm thấy đơn hàng.";
    exit();
}

// Lấy chi tiết sản phẩm
$sql_items = "
    SELECT p.product_name,
           d.price,
           d.quantity
    FROM order_detail d
    JOIN product p ON p.product_id = d.product_id
    WHERE d.order_id = ?
";

$stmt2 = $conn->prepare($sql_items);
$stmt2->execute([$order_id]);
$items = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng</title>
    <link rel="stylesheet" href="../css/OD.css">
</head>

<body>

    <div class="container">

        <h2>Chi tiết đơn hàng #<?= $order["order_id"] ?></h2>
        <hr>

        <p>
            Người nhận: <?= $order["cus_name"] ?><br>
            SĐT: <?= $order["cus_phone"] ?><br>
            Địa chỉ: <?= $order["diachi"] ?><br>
            Tổng tiền:
            <strong><?= number_format($order["tong_tien"]) ?> đ</strong>
        </p>

        <h3>Sản phẩm</h3>

        <?php if (count($items) === 0): ?>
            <p>Đơn hàng không có chi tiết.</p>

        <?php else: ?>
            <table>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>

                <?php foreach ($items as $it): ?>
                    <tr>
                        <td><?= $it["product_name"] ?></td>
                        <td><?= number_format($it["price"]) ?></td>
                        <td><?= $it["quantity"] ?></td>
                        <td><?= number_format($it["price"] * $it["quantity"]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <br>
        <a href="DH.php"> Quay lại danh sách</a>

    </div>

</body>

</html>