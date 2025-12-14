<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/add.css?v=9"> <!-- Tăng version để clear cache -->
    <title>Cập nhật sản phẩm</title>
</head>

<body>

    <?php
    include("../Database/database.php");

    $error_message = "";
    $product = null;

    if (!isset($_GET["id"]) || empty($_GET["id"])) {
        $error_message = "Không tìm thấy ID sản phẩm!";
    } else {
        $id = $_GET["id"];

        // Lấy dữ liệu sản phẩm hiện tại
        try {
            $sql_select = "SELECT * FROM product WHERE product_id = :product_id";
            $stmt_select = $conn->prepare($sql_select);
            $stmt_select->bindParam(':product_id', $id, PDO::PARAM_STR);
            $stmt_select->execute();
            $product = $stmt_select->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                $error_message = "Không tìm thấy sản phẩm với ID: " . htmlspecialchars($id);
            }
        } catch (PDOException $e) {
            $error_message = "Lỗi kết nối dữ liệu: " . $e->getMessage();
        }
    }

    // Xử lý cập nhật khi submit form
    if ($_SERVER["REQUEST_METHOD"] === "POST" && $product) {
        $product_id     = trim($_POST["product_id"]);
        $product_name   = trim($_POST["product_name"]);
        $quantity       = trim($_POST["quantity"]);
        $decription     = trim($_POST["decription"]);
        $item_price     = trim($_POST["item_price"]);
        $image          = trim($_POST["image"]);
        $category_id    = trim($_POST["category_id"]);
        $product_tag    = trim($_POST["product_tag"]);

        try {
            $sql_update = "UPDATE product SET 
                       product_name = :product_name,
                       quantity = :quantity,
                       decription = :decription,
                       item_price = :item_price,
                       image = :image,
                       category_id = :category_id,
                       product_tag = :product_tag
                       WHERE product_id = :product_id";

            $stmt = $conn->prepare($sql_update);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':product_name', $product_name);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':decription', $decription);
            $stmt->bindParam(':item_price', $item_price);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':product_tag', $product_tag);

            $stmt->execute();

            // Thành công → quay về trang danh sách
            header("Location: ../Admin/adminPage.php");
            exit();
        } catch (PDOException $e) {
            $error_message = "Cập nhật thất bại: " . $e->getMessage();
        }
    }
    ?>
    <a href="../Admin/adminPage.php" class="add">Quay về</a>
    <form action="" method="post" class="form">
        <h2>Cập nhật sản phẩm</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <?php if ($product): ?>
            <div>
                <label for="product_id">ID sản phẩm</label>
                <input type="text" name="product_id" required readonly
                    value="<?= htmlspecialchars($product['product_id']) ?>"
                    placeholder="ID không thể thay đổi">
            </div>

            <div>
                <label for="product_name">Tên sản phẩm *</label>
                <input type="text" name="product_name" required
                    value="<?= htmlspecialchars($product['product_name']) ?>">
            </div>

            <div>
                <label for="quantity">Số lượng *</label>
                <input type="text" name="quantity" required
                    value="<?= htmlspecialchars($product['quantity']) ?>">
            </div>

            <div>
                <label for="decription">Mô tả</label>
                <input type="text" name="decription"
                    value="<?= htmlspecialchars($product['decription']) ?>">
            </div>

            <div>
                <label for="item_price">Giá (VNĐ) *</label>
                <input type="text" name="item_price" required
                    value="<?= htmlspecialchars($product['item_price']) ?>">
            </div>

            <div>
                <label for="image">Link hình ảnh *</label>
                <input type="text" name="image" required
                    value="<?= htmlspecialchars($product['image']) ?>">
            </div>

            <div>
                <label for="category_id">Mã danh mục *</label>
                <input type="text" name="category_id" required
                    value="<?= htmlspecialchars($product['category_id']) ?>">
            </div>

            <div>
                <label for="product_tag">Tag</label>
                <input type="text" name="product_tag"
                    value="<?= htmlspecialchars($product['product_tag']) ?>"
                    placeholder="new, sale, hot">
            </div>

            <button type="submit">Lưu thay đổi</button>
        <?php endif; ?>

        <!-- Nút quay về -->
    </form>

</body>

</html>