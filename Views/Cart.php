<?php
session_start();
include("../Database/database.php"); // Đảm bảo đường dẫn này đúng

// =======================================================
// A. KHỞI TẠO BIẾN VÀ KIỂM TRA ĐĂNG NHẬP SỚM
// =======================================================
$user_id = null;
$items = []; // Danh sách sản phẩm trong giỏ hàng
$is_logged_in = isset($_SESSION["user_id"]);
$display_message = ""; // Biến chứa thông báo lỗi/trống giỏ hàng
$cus_name = '';

if ($is_logged_in) {
    $user_id = $_SESSION["user_id"];
}

if (isset($_SESSION['cus_name']) && $_SESSION['cus_name'] != "") {
    // Sử dụng htmlspecialchars để bảo mật khi hiển thị tên người dùng
    $cus_name = htmlspecialchars($_SESSION["cus_name"]);
}

// 1. Kiểm tra Đăng nhập cho các thao tác XÓA/CẬP NHẬT
// Nếu chưa đăng nhập, không cho thực hiện thao tác xóa/cập nhật và thoát để tránh lỗi


// =======================================================
// B. XỬ LÝ XÓA SẢN PHẨM (YÊU CẦU ĐĂNG NHẬP)
// =======================================================
if (isset($_POST["delete_button"]) && $is_logged_in) {
    $remove_id = intval($_POST["remove_id"]);
    $sql_delete = "DELETE FROM cart WHERE cart_id = :cart_id AND user_id = :user_id";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->execute([
        ":cart_id" => $remove_id,
        ":user_id" => $user_id
    ]);
    header("Location: Cart.php");
    exit();
}
// =======================================================
// C. XỬ LÝ CẬP NHẬT SỐ LƯỢNG (YÊU CẦU ĐĂNG NHẬP)
// =======================================================
if (isset($_POST["update_button"]) && $is_logged_in) {
    foreach ($_POST["update"] as $product_id => $quantity) {
        $quantity = intval($quantity);
        $product_id = intval($product_id);

        if ($quantity > 0) {
            $sql = "UPDATE cart SET quantity = :quantity 
                    WHERE product_id = :product_id AND user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':quantity' => $quantity,
                ':product_id' => $product_id,
                ':user_id' => $user_id
            ]);
        } else {
            // Xóa sản phẩm nếu số lượng <= 0
            $sql = "DELETE FROM cart WHERE product_id = :product_id AND user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':product_id' => $product_id,
                ':user_id' => $user_id
            ]);
        }
    }

    header("Location: Cart.php");
    exit();
}

// =======================================================
// D. HIỂN THỊ THANH USER (WELCOME)
// =======================================================
if ($cus_name != "") {
    echo "<div class='user-bar'> WELCOME TO DE'SHOP
    " . " " . $cus_name . " " . "<a href='../Authen/logout.php' style='text-decoration: none; color: #fff;' ; >Log out</a></div>";
} else {
    echo "<div class='user-bar'style='text-decoration: none;color: white;'>WELCOME TO DE'SHOP</div>";
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/scrollreveal"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/cart.css?v=10">
    <title>Giỏ hàng</title>
</head>

<body>
    <div class="side-bar">
        <i class="fa-solid fa-x" onClick="onClickSideBar()" style="color: white;"></i>
        <ul>
            <li><a href="../Index.php" style="color: white;">Home</a></li>
            <li><a href="../Views/Shop.php" style="color: white;">Shop</a></li>
            <li><a href="../Views/Aboutus.php" style="color: white;">About us</a></li>
            <li><a href="../Views/Contact.php" style="color: white;">Contact us</a></li>
        </ul>
    </div>
    <div class="overlay"></div>
    <section class="section-1">
        <div class="nav-side">
            <div class="nav-bar">
                <div class="nav-menu">
                    <i class="fa-solid fa-bars" onClick="menu()"></i>
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <?php
                    // Lỗi: include database 2 lần, chỉ cần 1 lần ở đầu file là đủ.
                    // include("../Database/database.php"); // Đã loại bỏ include thứ 2

                    if (isset($_POST["search_item"]) && !empty($_POST['search'])) {
                        $search =  $_POST['search'];
                        $sql_search = "SELECT * from product where product_tag like :search";
                        $stmt = $conn->prepare($sql_search);
                        $stmt->execute(['search' => "%$search%"]);
                        $result_search = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($result_search && count($result_search) > 0) {
                            if ($search == 'Ao') header("Location: ./Filter/Ao.php");
                            else if ($search == 'Quan') header("Location: ./Filter/Quan.php");
                            else if ($search == 'Aounisex') header("Location: ./Filter/AoUnisex.php");
                            else if ($search == 'Quanunisex') header("Location: ./Filter/QuanUnisex.php");
                        } else {
                            header("Location: ./Filter/NotFound.php");
                        }
                    }

                    echo "
                    <form action='' method='post' class='seacrh-form'>
                        <input type='text' name='search' id=''>
                        <button type='submit' name='search_item' class='search'>Search</button>
                    </form>";
                    ?>
                </div>
                <a href="../Index.php"><img src="../assets/Uad.png" alt="" width="190px" class="logo"></a>
                <div class="nav-user">
                    <?php
                    if ($cus_name != "") {
                    } else {
                        echo "<a href=\"./Authen/login.php\"><i class=\"fa-regular fa-user\"></i></a>";
                    }
                    ?>
                    <a href="./Views/Cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
                </div>
            </div>
        </div>

        <div style="padding: 40px 0; text-align:center; font-size:30px;">
            <?php
            // Lấy dữ liệu giỏ hàng và thiết lập thông báo
            if (!$is_logged_in) {
                // Trường hợp chưa đăng nhập: Hiển thị thông báo
                $display_message = "<div class='cart-message'>
                        <p>Bạn phải đăng nhập để xem giỏ hàng.</p>
                        <a href='../Authen/login.php' class='cart-message-btn'>Đăng nhập ngay</a>
                    </div>";
            } else {
                // Trường hợp Đã đăng nhập: Tiến hành truy vấn DB

                $sql = "SELECT c.cart_id, c.product_id, c.quantity,
                        p.product_name, p.image, p.item_price
                    FROM cart c
                    JOIN product p ON c.product_id = p.product_id
                    WHERE c.user_id = :user_id";

                $stmt = $conn->prepare($sql);
                $stmt->execute([':user_id' => $user_id]);
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 2. Kiểm tra Giỏ hàng trống
                if (!$items) {
                    $display_message = "<div style='text-align:center; padding: 350px 50px 90px;'>
                        Giỏ hàng trống.
                    </div>";
                }
            }

            // **IN THÔNG BÁO ĐĂNG NHẬP HOẶC GIỎ HÀNG TRỐNG**
            echo $display_message;
            ?>
        </div>

        <?php if ($items) { ?>
            <div style="padding: 50px; text-align:center; font-size:15px; margin-top:20px;">
                <h2 style='text-align:center;padding:40px;'>Giỏ hàng của bạn</h2>

                <?php
                $total = 0;

                foreach ($items as $item) {
                    $sub = $item["item_price"] * $item["quantity"];
                    $total += $sub;
                ?>
                    <div class="cart-item">

                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item["product_name"]); ?>" width="120">

                        <div class="product-info">
                            <strong><?php echo htmlspecialchars($item["product_name"]); ?></strong><br>
                            Giá: <?php echo number_format($item["item_price"]); ?> VNĐ
                        </div>

                        <form method="post" class="quantity-form">
                            <input type="number" name="update[<?php echo $item['product_id']; ?>]"
                                value="<?php echo $item['quantity']; ?>" min="1">
                            <button type="submit" name="update_button">Cập nhật</button>
                        </form>
                        <form method="post" class="delete-form">
                            <input type="hidden" name="remove_id" value="<?php echo $item['cart_id']; ?>">
                            <button type="submit" name="delete_button" class="remove-btn">Xóa</button>
                        </form>
                        <div class="subtotal">
                            Tổng: <?php echo number_format($sub); ?> VNĐ
                        </div>
                    </div>
                <?php } ?>

                <div class="cart-total">
                    <h3>Tổng cộng: <?php echo number_format($total); ?> VNĐ</h3>
                    <a href="../Filter/Hoadon.php" class="checkout-button">Thanh toán</a>
                </div>
            </div>
        <?php } ?>
    </section>
    <section class="section-5" style="color: white;">
        <div>
            <h3>De'Shop</h3>
            <p>Text: +00(234)23-45-666</p>
            <p>Mon – Fri: 8 am – 8 pm</p>
            <p>Sat – Sun: 8 am – 7 pm</p>
        </div>
        <div>
            <h3>ABOUT</h3>
            <p>Our Story</p>
            <p>Careers</p>
            <p>Influencers</p>
            <p>Join our team</p>
        </div>
        <div>
            <h3>CUSTOMER SERVICES</h3>
            <p>Contact Us</p>
            <p>Customer Service</p>
            <p>Find Store</p>
            <p>Shipping & Returns</p>
        </div>
        <div>
            <h3>Address</h3>
            <p>180 - Cao Lo</p>
            <p>Quan 8 - TP. HCM</p>
        </div>
    </section>
</body>

</html>
<script>
    //menu
    const sideBar = document.getElementsByClassName("side-bar")[0];
    const overlay = document.getElementsByClassName("overlay")[0];
    const navSide = document.getElementsByClassName("nav-side")[0];

    const onClickSideBar = () => {
        sideBar.style.left = "-300px";
        navSide.style.display = "flex";
        overlay.style.opacity = 0;
        overlay.style.zIndex = -2;
    };

    const menu = () => {
        sideBar.style.left = "0px";
        overlay.style.opacity = 1;
        overlay.style.zIndex = 2;
        navSide.style.display = "none";
    };
</script>