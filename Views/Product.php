<?php
session_start();
include("../Database/database.php");

$user_id = null;
$is_logged_in = false;
if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
    $is_logged_in = true;
}

$cus_name = $_SESSION['cus_name'] ?? "";

// Lấy product_id từ URL
if (!isset($_GET["id"])) {
    echo "Không tìm thấy sản phẩm.";
    exit();
}

$product_id = (int)$_GET["id"];

// Lấy thông tin sản phẩm
$sql_select = "SELECT * FROM product WHERE product_id = :product_id";
$stmt = $conn->prepare($sql_select);
$stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "Sản phẩm không tồn tại.";
    exit();
}

$product_name = $row["product_name"];
$quantity = $row["quantity"];
$item_price = $row["item_price"];
$image = $row["image"];  //hh
$product_tag = $row["product_tag"];
$description = $row["decription"] ?? "Không có mô tả.";

if ($cus_name != "") {
    echo "<div class='user-bar'> WELCOME TO DE'SHOP
    " . "  " .  $cus_name . " " . "<a href='../Authen/logout.php' style='text-decoration: none; color: #fff;'>Log out</a></div>";
} else {
    echo "<div class='user-bar' style='text-decoration: none;color: white;'>WELCOME TO DE'SHOP</div>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/product.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title><?php echo htmlspecialchars($product_name); ?></title>
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
                            else header("Location: ../Views/Shop.php");
                        } else {
                            header("Location: ./Filter/NotFound.php");
                        }
                        exit();
                    }

                    echo "
                    <form action='' method='post' class='seacrh-form'>
                        <input type='text' name='search' placeholder='Tìm kiếm...'>
                        <button type='submit' name='search_item' class='search'>Search</button>
                    </form>";
                    ?>
                </div>
                <a href="../Index.php"><img src="../assets/Uad.png" alt="Logo De'Shop" width="190px" class="logo"></a>
                <div class="nav-user">
                    <?php
                    if ($cus_name == "") {
                        echo "<a href=\"../Authen/login.php\"><i class=\"fa-regular fa-user\"></i></a>";
                    }
                    ?>
                    <a href="../Views/Cart.php"><i class=\"fa-solid fa-cart-shopping\"></i></a>
                </div>
            </div>
        </div>

        <div class="product-container">
            <?php if (!$is_logged_in): ?>
                <div class='cart-message'>
                    <p>Bạn phải đăng nhập để xem chi tiết và mua hàng.</p>
                    <a href='../Authen/login.php' class='cart-message-btn'>Đăng nhập ngay</a>
                </div>
            <?php else: ?>
                <div style="text-align:center; font-size:100px;">Sản Phẩm: <?php echo htmlspecialchars($product_name); ?></div>
                <section>
                    <div class="product-image">

                        <img src="https://wrong-domain.com/<?php echo htmlspecialchars($image); ?>"
                            alt="">


                    </div>

                    <div class="product-info">
                        <div class="product_name"><?php echo htmlspecialchars($product_name); ?></div>
                        <div class="product_price"><?php echo number_format($item_price); ?> VND</div>

                        <div class="product_quantity">
                            <label for="quantity-input">Số lượng:</label>
                            <div class="quantity-control">
                                <button type="button" class="quantity-btn" id="minus-btn">-</button>
                                <input type="text" id="quantity-input" value="1" min="1" max="<?php echo $quantity; ?>" readonly>
                                <button type="button" class="quantity-btn" id="plus-btn">+</button>
                            </div>
                        </div>

                        <div class="product_descrip">
                            Mô tả: <?php echo nl2br(htmlspecialchars($description)); ?>
                        </div>

                        <div class="product-detail">
                            <p>Tag: <?php echo htmlspecialchars($product_tag); ?></p>
                        </div>

                        <?php
                        if (isset($_POST["add_to_cart"])) {
                            $selected_quantity = (int)$_POST["quantity"];
                            if ($selected_quantity < 1) $selected_quantity = 1;
                            if ($selected_quantity > $quantity) {
                                echo "<p style='color:red;'>Số lượng vượt quá kho.</p>";
                            } else {
                                $check = $conn->prepare("SELECT * FROM cart WHERE user_id = :uid AND product_id = :pid");
                                $check->execute(['uid' => $user_id, 'pid' => $product_id]);

                                if ($check->rowCount() > 0) {
                                    echo "<p style='color:red;'>Sản phẩm đã có trong giỏ hàng.</p>";
                                } else {
                                    $sql_insert = "INSERT INTO cart (user_id, product_id, product_name, quantity, image, item_price, product_tag)
                                       VALUES (:user_id, :product_id, :product_name, :quantity, :image, :item_price, :product_tag)";

                                    $stmt_insert = $conn->prepare($sql_insert);
                                    $stmt_insert->execute([
                                        ':user_id' => $user_id,
                                        ':product_id' => $product_id,
                                        ':product_name' => $product_name,
                                        ':quantity' => $selected_quantity,
                                        ':image' => $image,
                                        ':item_price' => $item_price,
                                        ':product_tag' => $product_tag
                                    ]);

                                    echo "<p style='color:green;'>Thêm vào giỏ hàng thành công.</p>";
                                }
                            }
                        }
                        ?>

                        <form action="" method="post">
                            <input type="hidden" name="quantity" id="hidden-quantity" value="1">
                            <button type="submit" class="add" name="add_to_cart">ADD TO CART</button>
                        </form>
                        <a href="../Filter/Hoadon.php">
                            <button class="buy">BUY NOW</button>
                        </a>
                    </div>
                </section>
            <?php endif; ?>
        </div>
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

<script>
    const quantityInput = document.getElementById('quantity-input');
    const hiddenQuantity = document.getElementById('hidden-quantity');
    const minusBtn = document.getElementById('minus-btn');
    const plusBtn = document.getElementById('plus-btn');
    const maxQuantity = parseInt(quantityInput.getAttribute('max'));

    minusBtn.addEventListener('click', () => {
        let val = parseInt(quantityInput.value);
        if (val > 1) {
            quantityInput.value = val - 1;
            hiddenQuantity.value = quantityInput.value;
        }
    });

    plusBtn.addEventListener('click', () => {
        let val = parseInt(quantityInput.value);
        if (val < maxQuantity) {
            quantityInput.value = val + 1;
            hiddenQuantity.value = quantityInput.value;
        } else {
            alert("Đã đạt số lượng tối đa trong kho.");
        }
    });
</script>

</html>

<?php $conn = null; ?>