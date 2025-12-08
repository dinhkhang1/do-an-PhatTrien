<?php
include("../Database/database.php");

$id = $_GET["id"];



$sql_select = "SELECT * FROM product WHERE product_id = :product_id";
$stmt = $conn->prepare($sql_select);
$stmt->bindParam(':product_id', $id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$product_name = $row["product_name"];
$quantity = $row["quantity"];

$item_price = $row["item_price"];
$image = $row["image"];


?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/product.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Document</title>
</head>

<body>
    <div class="side-bar">
        <i class="fa-solid fa-x" onClick="onClickSideBar()" style="color: white;"></i>
        <ul>
            <li><a href="../Views/Home.php" style="color: white;">Home</a></li>
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
                    $sql_search = "SELECT * from product where product_name like :search";
                    $stmt_search = $conn->prepare($sql_search);
                    $stmt_search->execute(['search' => "%$search%"]);
                    $result_search = $stmt_search->fetchAll(PDO::FETCH_ASSOC);

                    if ($result_search && count($result_search) > 0) {
                        if ($search == 'Ao') header("Location: ../Filter/Ao.php");
                        else if ($search == 'Quan') header("Location: ../Filter/Quan.php");
                        else if ($search == 'Aounisex') header("Location: ../Filter/AoUnisex.php");
                        else if ($search == 'Quanunisex') header("Location: ../Filter/QuanUnisex.php");
                    } else {
                        header("Location: ../Filter/NotFound.php");
                    }
                }

                echo "
                <form action='' method='post' class='seacrh-form'>
                    <input type='text' name='search' id=''>
                    <button type='submit' name='search_item' class='search'>Search</button>
                </form>";
                ?>
            </div>
            <img src="../assets/Uad.png" alt="" width="200px" class="logo">
            <div class="nav-user">
                <i class="fa-regular fa-user"></i>
                <a href="../Views/Cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
            </div>
        </div>
    </div>
    <h4>Home/Shop/New & Now/ <?php echo $row["product_name"] ?></h4>
    <section>
        <div class="product-image">
            <img src="<?php echo $row["image"] ?>" alt="">
        </div>
        <div class="product-info">
            <div class="product_name">
                <?php echo $row["product_name"] ?>
            </div>
            <div class="product_price">
                $ <?php echo $row["item_price"] ?>
            </div>
            <div class="product_quantity">
                <label for="quantity-input">Số lượng:</label>
                <div class="quantity-control">
                    <button type="button" class="quantity-btn" id="minus-btn">-</button>
                    <input type="text" name="item_quantity" id="quantity-input" value="1" min="1" max="<?php echo $quantity; ?>" readonly>
                    <button type="button" class="quantity-btn" id="plus-btn">+</button>
                </div>
            </div>
            <div class="product_descrip">
                Mo ta: <?php echo $row["decription"] ?>
            </div>
            <div class="product-detail">
            <p>Tag: <?php echo $row["product_tag"] ?></p>
            </div>
            <?php
            try {
                if (isset($_POST["add_to_cart"])) {
                    $selected_quantity = isset($_POST['item_quantity']) ? (int)$_POST['item_quantity'] : 1;

                    // Kiểm tra xem sản phẩm có còn hàng không (Tùy chọn)
                    if ($selected_quantity > $quantity) {
                        echo "Error: Số lượng bạn chọn vượt quá số lượng trong kho!";
                        exit();
                    }
                    $sql_insert = "INSERT INTO cart (id,item_name,image,item_price,item_tag,quantity)
                        values (:id, :item_name, :image, :item_price, :item_tag, :quantity)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt_insert->bindParam(':item_name', $item_name, PDO::PARAM_STR);
                    $stmt_insert->bindParam(':image', $image, PDO::PARAM_STR);
                    $stmt_insert->bindParam(':item_price', $item_price, PDO::PARAM_STR);
                    $stmt_insert->bindParam(':quantity', $selected_quantity, PDO::PARAM_INT);

                    $stmt_insert->execute();

                    if ($stmt_insert) {
                        echo "Add successful!!!";
                    }
                }
            } catch (PDOException $e) {
                echo "The Item is already in Cart";
            }
            ?>
            <form action='' method='post'>
                <button type='submit' class='add' name='add_to_cart' style='height: 55px;'>ADD TO CART</button>
            </form>
            <button class="buy"><a>BUY NOW</a></button>
            
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
<script>
    // ... (Code script onClickSideBar và menu có sẵn) ...

    const quantityInput = document.getElementById('quantity-input');
    const minusBtn = document.getElementById('minus-btn');
    const plusBtn = document.getElementById('plus-btn');
    const maxQuantity = parseInt(quantityInput.getAttribute('max')); // Lấy số lượng tối đa từ PHP

    minusBtn.addEventListener('click', () => {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    });

    plusBtn.addEventListener('click', () => {
        let currentValue = parseInt(quantityInput.value);
        // Kiểm tra với số lượng tối đa lấy từ database
        if (currentValue < maxQuantity) {
            quantityInput.value = currentValue + 1;
        } else {
            // Tùy chọn: thông báo cho người dùng
            alert('Đã đạt số lượng tối đa (' + maxQuantity + ') có sẵn trong kho.');
        }
    });
</script>
<?php
$conn = null; // Close the PDO connection
?>