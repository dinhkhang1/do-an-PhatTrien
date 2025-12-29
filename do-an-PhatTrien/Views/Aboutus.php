<?php
include("../Database/database.php");

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
    " . "  " .  $cus_name . " " . "<a href='../Authen/logout.php' style='text-decoration: none; color: #fff;' ; >Log out</a></div>";
} else {
    echo "<div class='user-bar'style='text-decoration: none;color: white;'>WELCOME TO DE'SHOP</div>";
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/home.css?v=">
    <script src="https://unpkg.com/scrollreveal"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Document</title>
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
                    include("../Database/database.php");

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
                <a href="../Index.php"><img src="../assets/Uad.png" alt="" width="190px" class="logo"></a>s
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
        <div class="about-us">
            <div class="about-us-header">
                <h2>Giới thiệu về chúng mình</h2>
                <p>Chào mừng bạn đến với De'Shop – cửa hàng thời trang trực tuyến nhỏ được xây dựng bởi một nhóm sinh viên đam mê lập trình và yêu thích thời trang.</p>
                <p>De'Shop ra đời vào năm 2025 như một dự án học tập môn lập trình web. Ban đầu chỉ là ý tưởng đơn giản, nhưng nhờ sự nhiệt huyết và nỗ lực học hỏi, chúng mình đã biến nó thành một website hoàn chỉnh để bán các sản phẩm thời trang cơ bản.</p>
                <p>Mục tiêu của chúng mình là mang đến những sản phẩm chất lượng ở mức giá hợp lý, đồng thời rèn luyện kỹ năng lập trình và hiểu thêm về cách vận hành một cửa hàng trực tuyến. Hiện tại shop chủ yếu phục vụ khách hàng ở Việt Nam.</p>
                <p>Chúng mình rất mong bạn sẽ thích các sản phẩm trên De'Shop. Nếu có bất kỳ câu hỏi, góp ý hay phản hồi nào, đừng ngần ngại liên hệ nhé – mọi ý kiến của bạn đều giúp tụi mình cải thiện rất nhiều!</p>
                <p>Trân trọng,</p>
                <p>Nhóm sinh viên thực hiện De'Shop</p>
            </div>
            <div class="about-us-image">
                <img src="../assets/Project.jpg" alt="About Us Image" width="600px">
            </div>
            <!-- body -->
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