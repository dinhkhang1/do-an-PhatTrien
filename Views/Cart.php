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
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
if ($cus_name != "") {
        echo "<div class='user-bar'> WELCOME TO DE'SHOP
        " . "  " .  $cus_name . "  " . "<a href='../Authen/logout.php' style='text-decoration: none; color: #fff;' ; > Log out</a></div>";
} else {
        echo "<div class='user-bar'style='text-decoration: none;color: white;'>WELCOME TO DE'SHOP</div>";
}
  
    if (!$conn) {
        die("Database connection failed.");
    }

    if (isset($_POST["search_item"]) && !empty($_POST['search'])) {
        $search = $_POST['search'];
        // Search the product table using product_name
        $sql_search = "SELECT * FROM product WHERE product_name LIKE :search";
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

    ?>

    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/cart.css?v=4">
        <script src="https://unpkg.com/scrollreveal"></script>
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

                    <form action='' method='post' class='seacrh-form'>
                        <input type='text' name='search' id=''>
                        <button type='submit' name='search_item' class='search'>Search</button>
                    </form>

                </div>
                <img src="../assets/Uad.png" alt="" width="200px" class="logo">
                <div class="nav-user">
                    <i class="fa-regular fa-user"></i>
                    <a href="../Views/Cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
                </div>
            </div>
        </div>

        <div class="item_cart" style="height: 70px">
            <div class="item" style="font-size: 20px; position: relative; top: 0px; left:30px">
                Prodcuct
                <div class="item_name" style="font-size: 20px; position: relative; top: 0px; left:80px">
                    Prodcuct Name
                </div>
                <div class="item_price" style="font-weight: bold; position: relative; top: 0px; left:50px">
                    Prodcuct Price
                </div>
                <div class="quantity" style="font-weight: bold; position: relative; top: 0px; left:50px">quantity</div>
            </div>

            <div style="font-weight: bold; position: relative; top: 0px; left:340px">
                Modify
            </div>
        </div>
        <?php
        // If you want to show product catalog here, select from product
        $sql = "SELECT * FROM product";
        $stmt = $conn->query($sql);

        if ($stmt) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $rows) {
                // Move $new_price inside the loop
                $new_price = isset($rows["item_price"]) && isset($rows["quantity"]) ? $rows["item_price"] * $rows["quantity"] : 0;
        ?>
                <a href="../Views/Product.php?id=<?php echo $rows["product_id"] ?>">
                    <div class="item_cart">
                        <div class="item">
                            <img src="<?php echo $rows["image"] ?>" alt="" class="img" width="200px" height="200px">
                            <div class="item_name" style="font-size: 20px; position: relative; top: 10px;">
                                <?php echo $rows["product_name"] ?>
                            </div>
                </a>
                <div style="font-size: 20px; position: relative; top: 10px; left:250px;">
                    <?php
                    $quan = filter_input(INPUT_POST, "quan", FILTER_SANITIZE_SPECIAL_CHARS);
                    if (isset($_POST["update_button"])) {
                        foreach ($_POST["update"] as $id => $quantity) {
                            $quan = filter_var($quantity, FILTER_SANITIZE_SPECIAL_CHARS);

                            $sql_plus = "UPDATE product SET quantity = :quantity WHERE product_id = :product_id";
                            $stmt_plus = $conn->prepare($sql_plus);
                            $stmt_plus->execute(['quantity' => $quan, 'product_id' => $id]);

                            if ($stmt_plus) {
                                // Update the quantity on the page without refreshing
                                $new_price = $rows["item_price"] * $quan;
                            }
                        }
                    }

                    ?>
                    <div class="item_price" style="font-weight: bold; position: relative; top: 40px; left:-240px">
                        <?php echo $new_price ?>
                    </div>
                    <form action="" method='post'>
                        <input type="number" name="update[<?php echo $rows["product_id"]; ?>]" value="<?php echo $rows["quantity"]; ?>">
                        <button type="submit" name="update_button">Update</button>
                    </form>

                </div>
                </div>
                </div>
                <?php
                $id = $rows["product_id"];
                if (isset($_POST["delete"])) {
                    $delete_id = $_POST["delete_id"];
                    if (isset($_POST["delete"])) {
                        $delete_id = $_POST["delete_id"];
                        $sql_delete = "DELETE FROM product WHERE product_id = :product_id";
                        $stmt_delete = $conn->prepare($sql_delete);
                        $stmt_delete->execute(['product_id' => $delete_id]);

                        // Redirect the user back to the cart page
                        header("Location: Cart.php");
                        exit(); // Make sure to exit after a header redirect
                    }
                }
                echo "<form action='' method='post'>
                        <input type='hidden' name='delete_id' value='$id'>
                    <button class='delete' type='submit' name= 'delete' style='height:55px; left: 1450px'>Delete</button>
                    </form>";
                ?>
        <?php
            }
        }
        ?>

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