<?php
include("./Database/database.php");

session_start();
$cus_name = $password = '';

if (isset($_SESSION['cus_name']) && $_SESSION['cus_name'] != "") {
    $cus_name = $_SESSION["cus_name"];
}

if (isset($_SESSION['password']) && $_SESSION['password'] != "") {
    $password = $_SESSION["password"];
}

if ($cus_name != "") {
    echo "<div class='user-bar'> WELCOME TO DE'SHOP
    " . "  " .  $cus_name . " " . "<a href='./Authen/logout.php' style='text-decoration: none; color: #fff;' ; >Log out</a></div>";
} else {
    echo "<div class='user-bar'style='text-decoration: none;color: white;'>WELCOME TO DE'SHOP</div>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/home.css?v=">
    <script src="https://unpkg.com/scrollreveal"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>De'Shop - Trang chủ</title>
</head>

<body>
    <div class="side-bar">
        <i class="fa-solid fa-x" onClick="onClickSideBar()" style="color: white;"></i>
        <ul>
            <li><a href="./Index.php" style="color: white;">Home</a></li>
            <li><a href="./Views/Shop.php" style="color: white;">Shop</a></li>
            <li><a href="./Views/Aboutus.php" style="color: white;">About us</a></li>
            <li><a href="./Views/Contact.php" style="color: white;">Contact us</a></li>
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
                        $search = trim($_POST['search']); 

                      
                        if ($search == 'Ao') {
                            header("Location: ./Filter/Ao.php");
                            exit();
                        } else if ($search == 'Quan') {
                            header("Location: ./Filter/Quan.php");
                            exit();
                        } else if ($search == 'Aounisex') {
                            header("Location: ./Filter/AoUnisex.php");
                            exit();
                        } else if ($search == 'Quanunisex') {
                            header("Location: ./Filter/QuanUnisex.php");
                            exit();
                        } else {
                      
                            header("Location: ./Filter/NotFound.php");
                            exit();
                        }
                    }

                    echo "
                    <form action='' method='post' class='seacrh-form'>
                        <input type='text' name='search' placeholder='Tìm kiếm sản phẩm...'>
                        <button type='submit' name='search_item' class='search'>Search</button>
                    </form>";
                    ?>
                </div>
                <a href="./Index.php"><img src="./assets/Uad.png" alt="Logo De'Shop" width="190px" class="logo"></a>
                <div class="nav-user">
                    <?php if ($cus_name == "") echo "<a href=\"./Authen/login.php\"><i class=\"fa-regular fa-user\"></i></a>"; ?>
                    <a href="./Views/Cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
                </div>
            </div>
        </div>

        <!-- Phần nội dung trang chủ giữ nguyên -->
        <div class="home-content">
            <div class="content-text">
                <h4>HÀNG MỚI </h4>
                <h3>CÓ THỂ BẠN THÍCH</h3>
                <span>GIAO HÀNG MIỄN PHÍ VỚI 50.000 VNĐ</span>
                <a>MUA NGAY</a>
            </div>
            <div class="content-image"></div>
        </div>
    </section>


    <section class="section-2">
   
    </section>

    <div class="banner" style="text-align: center; color: white;">
        <p>10%</p>
        <div>
            <h3>Giảm Giá</h3>
            <span>Đơn hàng trên 100.000.000 + mã giảm giá: <b>FANMU01</b></span>
        </div>
    </div>

    <section class="section-3">
        <div class="section-3_container">
            <?php
            $sql = "SELECT * FROM product";
            $stmt = $conn->query($sql);
            if ($stmt) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $rows) {
            ?>
                    <a href="./Views/Product.php?id=<?php echo $rows["product_id"] ?>">
                        <div class="product">
                            <img src="<?php echo htmlspecialchars($rows["image"]); ?>" alt="<?php echo htmlspecialchars($rows["product_name"]); ?>" class="img" width="280px" height="400px">
                            <div class="item_name" style="font-size: 20px; position: relative; top: 10px; color:black">
                                <?php echo htmlspecialchars($rows["product_name"]); ?>
                            </div>
                            <div class="item_price" style="font-weight: bold; position: relative; top: 15px; color:black">
                                <?php echo number_format($rows["item_price"], 0, ',', '.'); ?> VNĐ
                            </div>
                        </div>
                    </a>
            <?php
                }
            }
            ?>
        </div>
    </section>

    <section class="section-4">
        <video autoplay loop muted>
            <source type="video/mp4" src="./assets/hehe.mp4">
        </video>
    </section>

    <section class="section-5" style="color: white;">
        <!-- Footer giữ nguyên -->
    </section>
</body>

<script>
    // Script ScrollReveal và menu giữ nguyên
    var slideUp = {
        distance: '150%',
        origin: 'bottom',
        opacity: null,
        delay: 500
    };
    var slideRight = {
        distance: '120%',
        origin: 'left',
        opacity: null,
        delay: 1000
    };
    ScrollReveal().reveal('.content-text', slideUp);
    ScrollReveal().reveal('.content-image', slideRight);
    // ... các ScrollReveal khác ...

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

<?php $conn = null; ?>
