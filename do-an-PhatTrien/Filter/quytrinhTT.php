<?php
session_start();
include("../Database/database.php");

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// require '../PHPMailer/src/Exception.php';
// require '../PHPMailer/src/PHPMailer.php';
// require '../PHPMailer/src/SMTP.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION["user_id"])) {
    die("Bạn cần <a href='../login.php'>đăng nhập</a> để thanh toán.");
}

// Kiểm tra nút submit
if (!isset($_POST["checkout"])) {
    die("Truy cập không hợp lệ.");
}

$user_id   = $_SESSION["user_id"];
$cus_name  = trim($_POST["fullname"]);
$cus_phone = trim($_POST["phone"]);
$diachi    = trim($_POST["address"]);
$tong_tien = (int)$_POST["total_price"];

if (empty($cus_name) || empty($cus_phone) || empty($diachi)) {
    die("Vui lòng điền đầy đủ thông tin.");
}

// Bắt đầu transaction để đảm bảo toàn vẹn dữ liệu
try {
    $conn->beginTransaction();

    // Lấy giỏ hàng + thông tin sản phẩm (join để lấy tên và số lượng tồn kho)
    $sql_cart = "
        SELECT c.*, p.product_name, p.quantity AS stock_quantity 
        FROM cart c 
        JOIN product p ON c.product_id = p.product_id 
        WHERE c.user_id = :uid
    ";
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->execute(['uid' => $user_id]);
    $cart_items = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cart_items)) {
        die("Giỏ hàng của bạn đang trống.");
    }

    // Kiểm tra tồn kho
    foreach ($cart_items as $item) {
        if ($item['quantity'] > $item['stock_quantity']) {
            $conn->rollBack();
            die("Sản phẩm <b>{$item['product_name']}</b> không đủ hàng (chỉ còn {$item['stock_quantity']} sản phẩm).");
        }
    }

    // Tạo đơn hàng
    $sql_order = "INSERT INTO `order` (user_id, cus_name, cus_phone, diachi, tong_tien) 
                  VALUES (:uid, :cus_name, :cus_phone, :diachi, :tong_tien)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->execute([
        'uid'       => $user_id,
        'cus_name'  => $cus_name,
        'cus_phone' => $cus_phone,
        'diachi'    => $diachi,
        'tong_tien' => $tong_tien
    ]);

    $order_id = $conn->lastInsertId();

    // Chuẩn bị câu lệnh insert order_items
    $sql_item = "INSERT INTO order_detail (order_id, product_id, quantity, price) 
                 VALUES (:order_id, :product_id, :quantity, :price)";
    $stmt_item = $conn->prepare($sql_item);

    // Chuẩn bị bảng email
    $email_body = "
    <h2 style='color:#28a745;'>CẢM ƠN BẠN ĐÃ ĐẶT HÀNG TẠI DE'SHOP!</h2>
    <p>Mã đơn hàng: <b>#{$order_id}</b></p>
    <p>Khách hàng: <b>{$cus_name}</b> | SĐT: {$cus_phone}</p>
    <p>Địa chỉ giao hàng: {$diachi}</p>
    <hr>
    <table border='1' cellpadding='10' cellspacing='0' style='width:100%; border-collapse:collapse;'>
        <thead style='background:#007bff; color:white;'>
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Giá</th>
            </tr>
        </thead>
        <tbody>
    ";

    // Lưu chi tiết + trừ kho
    foreach ($cart_items as $item) {
        $stmt_item->execute([
            'order_id'   => $order_id,
            'product_id' => $item["product_id"],
            'quantity'   => $item["quantity"],
            'price'      => $item["item_price"]
        ]);

        // Trừ kho
        $sql_update = "UPDATE product SET quantity = quantity - :qty WHERE product_id = :pid";
        $conn->prepare($sql_update)->execute([
            'qty' => $item["quantity"],
            'pid' => $item["product_id"]
        ]);

        // Thêm vào email
        $item_total = number_format($item["item_price"], 0, ',', '.');
        $email_body .= "
        <tr>
            <td>{$item['product_name']}</td>
            <td style='text-align:center;'>{$item['quantity']}</td>
            <td style='text-align:right;'>{$item_total} VNĐ</td>
        </tr>";
    }

    $tong_tien_fmt = number_format($tong_tien, 0, ',', '.');
    $email_body .= "
        </tbody>
    </table>
    <h3 style='text-align:right; color:#dc3545;'>Tổng tiền: {$tong_tien_fmt} VNĐ</h3>
    <p>Chúng mình sẽ liên hệ sớm để xác nhận đơn hàng nhé! ❤️</p>
    ";

    // Xóa giỏ hàng
    $conn->prepare("DELETE FROM cart WHERE user_id = :uid")->execute(['uid' => $user_id]);

    // Commit transaction
    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack();
    die("Có lỗi xảy ra khi xử lý đơn hàng: " . $e->getMessage());
}

// Gửi email thông báo (cho admin và khách hàng nếu cần)
// $mail = new PHPMailer(true);

// try {
//     $mail->isSMTP();
//     $mail->Host       = 'smtp.gmail.com';
//     $mail->SMTPAuth   = true;
//     $mail->Username   = 'lekhang.mn@gmail.com';                    // Email của bạn
//     $mail->Password   = 'mật_khẩu_ứng_dụng_16_ký_tự';              // App Password Gmail
//     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//     $mail->Port       = 587;
//     $mail->CharSet    = 'UTF-8';

//     $mail->setFrom('lekhang.mn@gmail.com', 'De\'Shop');
//     $mail->addAddress('lekhang.mn@gmail.com');                     // Admin nhận đơn
//     // $mail->addAddress('email_khách_hàng@gmail.com');            // Nếu muốn gửi cho khách

//     $mail->isHTML(true);
//     $mail->Subject = "Đơn hàng mới từ De'Shop - Mã #$order_id";
//     $mail->Body    = $email_body;

//     $mail->send();
//     $email_status = "Email xác nhận đã được gửi!";
// } catch (Exception $e) {
//     $email_status = "Đơn hàng thành công nhưng gửi email thất bại: " . $mail->ErrorInfo;
// }
$email_status = "Đơn hàng đã được ghi nhận! (Chức năng gửi email đang phát triển ⚙️)";
$conn = null;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thành công - De'Shop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 150px 20px 50px;
        }

        .success-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #28a745;
            font-size: 36px;
        }

        .order-id {
            font-size: 28px;
            color: #007bff;
            margin: 20px 0;
        }

        .btn-home {
            display: inline-block;
            margin-top: 30px;
            padding: 15px 30px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 18px;
        }

        .btn-home:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <div class="success-container">
        <h1>🎉 Thanh toán thành công!</h1>
        <p>Cảm ơn bạn đã tin tưởng De'Shop – dự án nhỏ của nhóm sinh viên chúng mình ❤️</p>

        <div class="order-id">Mã đơn hàng: #<?= htmlspecialchars($order_id) ?></div>

        <p><?= htmlspecialchars($email_status) ?></p>
        <p>Chúng mình sẽ liên hệ xác nhận qua Zalo/SĐT trong vòng 24h.</p>

        <a href="../Index.php" class="btn-home">Quay lại trang chủ</a>
    </div>
</body>

</html>