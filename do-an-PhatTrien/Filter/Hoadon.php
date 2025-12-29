<?php
session_start();
include("../Database/database.php");

$cus_name = $password = '';
if (isset($_SESSION['cus_name']) && $_SESSION['cus_name'] != "") {
    $cus_name = $_SESSION["cus_name"];
}

if (isset($_SESSION['password']) && $_SESSION['password'] != "") {
    $password = $_SESSION["password"];
}
if ($cus_name != "") {
    echo "<div class='user-bar'> WELCOME TO DE'SHOP
    " . "  " .  $cus_name . " " . "<a href='../Authen/logout.php' style='text-decoration: none; color: #fff;' ; >Log out</a></div>";
} else {
    echo "<div class='user-bar'style='text-decoration: none;color: white;'>WELCOME TO DE'SHOP</div>";
}



$user_id = $_SESSION["user_id"];

// Lấy giỏ hàng của user
$sql_cart = "SELECT cart.*, product.product_name, product.image, product.item_price 
             FROM cart 
             JOIN product ON cart.product_id = product.product_id
             WHERE cart.user_id = :uid";
$stmt = $conn->prepare($sql_cart);
$stmt->execute(['uid' => $user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$cart_items) {
    echo "<h2>Giỏ hàng trống</h2>";
    echo "<a href='../Index.php'>Quay lại mua hàng</a>";
    exit();
}

// Tính tổng tiền
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += ($item["quantity"] * $item["item_price"]);
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/shop.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Hoa don</title>
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
    <div class="nav-side">
        <div class="nav-bar">
            <div class="nav-menu">
                <i class="fa-solid fa-bars" onClick="menu()"></i>
                <i class="fa-solid fa-magnifying-glass"></i>
                <?php
                include("../Database/database.php");

                if (isset($_POST["search_item"]) && !empty($_POST['search'])) {
                    $search =  $_POST['search'];
                    $sql_search = "SELECT * from product where product_tag like :search";
                    $stmt = $conn->prepare($sql_search);
                    $stmt->execute(['search' => "%$search%"]);
                    $result_search = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($result_search && count($result_search) > 0) {
                        if ($search == 'Ao') header("Location: ./Ao.php");
                        else if ($search == 'Quan') header("Location: ./Quan.php");
                        else if ($search == 'Aounisex') header("Location: ./AoUnisex.php");
                        else if ($search == 'Quanunisex') header("Location: ./QuanUnisex.php");
                    } else {
                        header("Location: ./NotFound.php");
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
                    echo "   <a href=\"../Authen/login.php\"><i class=\"fa-regular fa-user\"></i></a>";
                }
                ?>
                <a href="../Views/Cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
            </div>
        </div>
    </div>




    <!-- FORM CHECKOUT -->
    <form action="quytrinhTT.php" method="POST">

        <div class="checkout-info">
            <h2>Thanh toán</h2>
            <h3>Thông tin khách hàng</h3>

            <label>Họ và tên</label>
            <input type="text" name="fullname" required placeholder="Ví dụ: Nguyễn Văn A">

            <label>Số điện thoại</label>
            <input type="text" name="phone" required placeholder="Ví dụ: 0901234567">

            <label>Địa chỉ nhận hàng</label>
            <textarea name="address" required placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố"></textarea>
        </div>

        <div class="checkout-cart">
            <h3>Đơn hàng của bạn</h3>

            <table>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                </tr>

                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item["product_name"]) ?></td>
                        <td><?= $item["quantity"] ?></td>
                        <td><?= number_format($item["item_price"], 0, ',', '.') ?> VNĐ</td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <h3>
                Tổng tiền:
                <span style="color: red; font-size: 24px; font-weight: bold;">
                    <?= number_format($total_price, 0, ',', '.') ?> VNĐ
                </span>
            </h3>

            <input type="hidden" name="total_price" value="<?= $total_price ?>">

            <button type="submit" name="checkout" class="btn-checkout">
                Xác nhận thanh toán
            </button>
        </div>

    </form>
    </div>
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
    const sideBar = document.getElementsByClassName("side-bar")[0];
    const overlay = document.getElementsByClassName("overlay")[0];
    const navSide = document.getElementsByClassName("nav-side")[0];

    const onClickSideBar = () => {
        sideBar.style.left = "-300px";
        navSide.style.display = "flex";
        overlay.style.opacity = 0;
        overlay.style.zIndex = -2;
    }

    const menu = () => {
        sideBar.style.left = "0px";
        overlay.style.opacity = 1;
        overlay.style.zIndex = 2;
        navSide.style.display = "none";
    }
</script>