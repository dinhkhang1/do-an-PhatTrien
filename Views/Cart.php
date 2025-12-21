<?php
session_start();
include("../Database/database.php");

$user_id = null;
$items = [];
$is_logged_in = isset($_SESSION["user_id"]);
$display_message = "";
$cus_name = '';

if ($is_logged_in) {
    $user_id = $_SESSION["user_id"];
}

if (isset($_SESSION['cus_name']) && $_SESSION['cus_name'] != "") {
    $cus_name = htmlspecialchars($_SESSION["cus_name"]);
}

// XỬ LÝ XÓA SẢN PHẨM
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

// XỬ LÝ CẬP NHẬT SỐ LƯỢNG
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

// HIỂN THỊ THANH USER
if ($cus_name != "") {
    echo "<div class='user-bar'> WELCOME TO DE'SHOP
    " . " " . $cus_name . " " . "<a href='../Authen/logout.php' style='text-decoration: none; color: #fff;'>Log out</a></div>";
} else {
    echo "<div class='user-bar' style='text-decoration: none;color: white;'>WELCOME TO DE'SHOP</div>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/scrollreveal"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
                    if (isset($_POST["search_item"]) && !empty($_POST['search'])) {
                        $search = $_POST['search'];
                        $sql_search = "SELECT * FROM product WHERE product_tag LIKE :search";
                        $stmt = $conn->prepare($sql_search);
                        $stmt->execute(['search' => "%$search%"]);
                        $result_search = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($result_search && count($result_search) > 0) {
                            if (strtolower($search) == 'ao') header("Location: ./Filter/Ao.php");
                            elseif (strtolower($search) == 'quan') header("Location: ./Filter/Quan.php");
                            elseif (strtolower($search) == 'aounisex') header("Location: ./Filter/AoUnisex.php");
                            elseif (strtolower($search) == 'quanunisex') header("Location: ./Filter/QuanUnisex.php");
                        } else {
                            header("Location: ./Filter/NotFound.php");
                        }
                        exit();
                    }

                    echo "<form action='' method='post' class='seacrh-form'>
                        <input type='text' name='search'>
                        <button type='submit' name='search_item' class='search'>Search</button>
                    </form>";
                    ?>
                </div>
                <a href="../Index.php"><img src="../assets/Uad.png" alt="" width="190px" class="logo"></a>
                <div class="nav-user">
                    <?php if ($cus_name == "") echo "<a href=\"../Authen/login.php\"><i class=\"fa-regular fa-user\"></i></a>"; ?>
                    <a href="./Cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
                </div>
            </div>
        </div>

        <div style="padding: 40px 0; text-align:center; font-size:30px;">
            <?php
            if (!$is_logged_in) {
                $display_message = "<div class='cart-message'>
                        <p>Bạn phải đăng nhập để xem giỏ hàng.</p>
                        <a href='../Authen/login.php' class='cart-message-btn'>Đăng nhập ngay</a>
                    </div>";
            } else {
                $sql = "SELECT c.cart_id, c.product_id, c.quantity,
                        p.product_name, p.image, p.item_price
                    FROM cart c
                    JOIN product p ON c.product_id = p.product_id
                    WHERE c.user_id = :user_id";

                $stmt = $conn->prepare($sql);
                $stmt->execute([':user_id' => $user_id]);
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$items) {
                    $display_message = "<div style='text-align:center; padding: 350px 50px 90px;'>
                        Giỏ hàng trống.
                    </div>";
                }
            }
            echo $display_message;
            ?>
        </div>

        <?php if ($items): ?>
            <div style="padding: 50px; text-align:center; font-size:15px; margin-top:20px;">
                <h2 style='text-align:center;padding:40px;'>Giỏ hàng của bạn</h2>

                <?php
              
                $total = 0;
                foreach ($items as $item) {
                   
                    $sub = $item["item_price"] * 1;
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
                            Tổng: <?php echo number_format($item["item_price"] * 1); ?> VNĐ
                        </div>
                    </div>
                <?php } ?>

                <div class="cart-total">
                    <h3>Tổng cộng: <?php echo number_format($total); ?> VNĐ</h3> 
                    <a href="../Filter/Hoadon.php" class="checkout-button">Thanh toán</a>
                </div>
            </div>
        <?php endif; ?>
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

<script>
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

</html>
