<?php
include("./Database/database.php");
/** @var PDO $conn */

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

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/home.css?v=">
    <script src="https://unpkg.com/scrollreveal"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Document</title>
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
                    include("./Database/database.php");

                    function vn_normalize($str)
                    {
                        $str = mb_strtolower($str, "UTF-8");
                        $map = [
                            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
                            'd' => 'đ',
                            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
                            'i' => 'í|ì|ỉ|ĩ|ị',
                            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
                            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
                            'y' => 'ý|ỳ|ỷ|ỹ|ỵ'
                        ];
                        foreach ($map as $non => $regex) {
                            $str = preg_replace("/($regex)/i", $non, $str);
                        }
                        return $str;
                    }

                    if (isset($_POST["search_item"]) && !empty($_POST['search'])) {

                        $search_raw = trim($_POST['search']);

                        // Chuẩn hóa tìm kiếm: bỏ dấu + lower
                        $search = vn_normalize($search_raw);

                        // Lấy toàn bộ product_tag để so khớp không dấu
                        $sql = "SELECT product_tag FROM product";
                        $stmt = $conn->query($sql);
                        $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);

                        $matched = false;

                        foreach ($tags as $tag) {

                            $tag_norm = vn_normalize($tag);

                            if (strpos($tag_norm, $search) !== false) {
                                $matched = $tag;
                                break;
                            }
                        }

                        // Mapping đúng theo logic cũ của bạn
                        if ($matched !== false) {

                            $norm = vn_normalize($matched);

                            if ($norm === 'ao') header("Location: ./Filter/Ao.php");
                            else if ($norm === 'quan') header("Location: ./Filter/Quan.php");
                            else if ($norm === 'aounisex') header("Location: ./Filter/AoUnisex.php");
                            else if ($norm === 'quanunisex') header("Location: ./Filter/QuanUnisex.php");
                            else header("Location: ./Filter/NotFound.php");

                            exit();
                        }

                        // Không có kết quả
                        header("Location: ./Filter/NotFound.php");
                        exit();
                    }

                    echo "
                    <form action='' method='post' class='seacrh-form'>
                        <input type='text' name='search' id='' placeholder='nhập từ khóa ...'>
                        <button type='submit' name='search_item' class='search'>Search</button>
                    </form>";

                    ?>
                </div>
                <a href="./Index.php"><img src="./assets/Uad.png" alt="" width="190px" class="logo"></a>
                <div class="nav-user">
                    <?php
                    if ($cus_name != "") {
                    } else {
                        echo "   <a href=\"./Authen/login.php\"><i class=\"fa-regular fa-user\"></i></a>";
                    }
                    ?>
                    <a href="./Views/Cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
                    <a href="./Views/DH.php"><i class="fa-solid fa-calendar-check"></i></a>
                </div>
            </div>
        </div>
        <div class="home-content">
            <div class="content-text">
                <h4>HÀNG MỚI </h4>
                <h3>
                    CÓ THỂ BẠN THÍCH
                </h3>
                <span>GIAO HÀNG MIỄN PHÍ VỚI 50.000 VNĐ</span>
                <a>MUA NGAY</a>
            </div>
            <div class="content-image"></div>
        </div>

    </section>
    <section class="section-2">
        <div class="sale-info__side">
            <div class="sale-info__side-1">
                <div class=sale-1>
                    <p>Giảm tới 40%</p>
                    <span>Giảm giá mùa hè</span><br>
                    <a href="">MUA NGAY</a>
                </div>
                <div class="sale-2" style="color: white;">
                    <p>SẢN PHẨM MỚI</p>
                    <span>Giảm tới 25%</span><br>
                    <a href="" style="color: white;">MUA NGAY</a>
                </div>
            </div>
            <div class="sale-info__side-2">
                <div class="side-2__text">
                    <p class="bigsale">ĐẠI TIỆC GIẢM GIÁ</p><br>
                    <span>Giảm tới 70%</span><br>
                    <a href="">MUA NGAY</a>
                </div>
            </div>
        </div>
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
            // list all products from the product table
            $sql = "SELECT * from product";
            $stmt = $conn->query($sql);

            if ($stmt) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $rows) {
            ?>
                    <a href="./Views/Product.php?id=<?php echo $rows["product_id"] ?>">
                        <div class="product">
                            <img src="<?php echo $rows["image"] ?>" alt="" class="img" width="280px" height="400px">
                            <div class="item_name" style="font-size: 20px; position: relative; top: 10px; color:black">
                                <?php echo $rows["product_name"] ?>
                            </div>
                            <div class="item_price" style="font-weight: bold; position: relative; top: 15px; color:black">
                                <?php echo number_format($rows["item_price"], 0, ',', '.') ?> VNĐ
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
    ScrollReveal().reveal('.section-2');
    ScrollReveal().reveal('.sale-info__side-1', {
        delay: 500
    });
    ScrollReveal().reveal('.sale-info__side-2', {
        delay: 1000
    });
    ScrollReveal().reveal('.banner', {
        delay: 500
    });

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
<?php
$conn = null; // Close the conn connection
?>