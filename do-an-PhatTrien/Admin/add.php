<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/add.css?v=2">
    <title>Document</title>
</head>

<body>
    <div class="overlay">
        <i class="fa-solid fa-x" onCLick="closeX()"></i>
    </div>
    <button class="add"><a href="../Admin/adminPage.php">Quay về</a></button>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="forrm">
        <h2>Thêm sản phẩm mới</h2>

        <?php
        // Hiển thị lỗi duplicate ID đẹp hơn
        if (isset($e) && $e->errorInfo[1] == 1062) {
            echo '<p class="error">Lỗi: ID sản phẩm đã tồn tại! Vui lòng chọn ID khác.</p>';
        }
        ?>

        <div>
            <label for="product_id">ID sản phẩm *</label>
            <input type="text" name="product_id" required placeholder="Ví dụ: SP001">
        </div>
        <div>
            <label for="product_name">Tên sản phẩm *</label>
            <input type="text" name="product_name" required placeholder="Áo thun cotton basic">
        </div>
        <div>
            <label for="quantity">Số lượng *</label>
            <input type="text" name="quantity" required placeholder="100">
        </div>
        <div>
            <label for="decription">Mô tả</label>
            <input type="text" name="decription" placeholder="Mô tả ngắn về sản phẩm...">
        </div>
        <div>
            <label for="item_price">Giá (VNĐ) *</label>
            <input type="text" name="item_price" required placeholder="250000">
        </div>
        <div>
            <label for="image">Link hình ảnh *</label>
            <input type="text" name="image" required placeholder="https://example.com/image.jpg">
        </div>
        <div>
            <label for="category_id">Mã danh mục *</label>
            <input type="text" name="category_id" required placeholder="1 (Áo), 2 (Quần), ...">
        </div>
        <div>
            <label for="product_tag">Tag</label>
            <input type="text" name="product_tag" placeholder="new, sale, hot">
        </div>

        <button type="submit">Lưu sản phẩm</button>
    </form>
    <?php
    include("../Database/database.php");
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $product_id = $_POST["product_id"];
            $product_name = $_POST["product_name"];
            $quantity = $_POST["quantity"];
            $decription = $_POST["decription"];
            $item_price = $_POST["item_price"];
            $image = $_POST["image"];
            $category_id = $_POST["category_id"];
            $product_tag = $_POST["product_tag"];
            // $pdo = new PDO("mysql:host=localhost;dbname:dbqa","root","");
            // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql_insert = $conn->prepare("INSERT INTO product (product_id,product_name,quantity,decription,item_price,image,category_id,product_tag)
            values (:product_id,:product_name,:quantity,:decription,:item_price,:image,:category_id,:product_tag)");
            $sql_insert->bindParam(':product_id', $product_id);
            $sql_insert->bindParam(':product_name', $product_name);
            $sql_insert->bindParam(':quantity', $quantity);
            $sql_insert->bindParam(':decription', $decription);
            $sql_insert->bindParam(':item_price', $item_price);
            $sql_insert->bindParam(':image', $image);
            $sql_insert->bindParam(':category_id', $category_id);
            $sql_insert->bindParam(':product_tag', $product_tag);


            $sql_insert->execute();
            header("Location: ../Admin/adminPage.php");
            exit();
        }
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            // Error code 1062 is for duplicate entry
            echo "DUPLICATE ID !!!!";
            echo "ADD AGAIN!!";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }

    ?>
</body>

</html>