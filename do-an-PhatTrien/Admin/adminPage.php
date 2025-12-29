<?php
include("../Database/database.php");

session_start();
if (isset($_SESSION['username']) && $_SESSION['username'] != "") {
    $username = $_SESSION["username"];
}
if (isset($_SESSION['password']) && $_SESSION['password'] != "") {
    $password = $_SESSION["password"];
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/adminPage.css?v=3">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Document</title>
</head>

<body>
    <a href="../Admin/adminPageUser.php"><button>Manage Users</button></a>
    <button class="add"><a href="../Admin/add.php">Add new items</a></button>
    <a href="../Authen/logout.php"><button>Thoát</button></a>

    <table>
        <h1>Thông Tin Sản Phẩm</h1>
        <tr>
            <th>ID</th>
            <th>name</th>
            <th>quantity</th>
            <th>decription</th>
            <th>image</th>
            <th>price</th>
            <th>categoryID</th>
            <th>Tag</th>
            <th>Edit</th>
        </tr>

        <?php
        $sql = "SELECT * FROM product";
        $stmt = $conn->query($sql);

        if ($stmt->rowCount() > 0) {
            while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
                <tr>
                    <td data-label="ID"><span><?php echo $rows["product_id"] ?></span></td>
                    <td data-label="Tên sản phẩm"><span><?php echo $rows["product_name"] ?></span></td>
                    <td data-label="Số lượng"><span><?php echo $rows["quantity"] ?></span></td>
                    <td data-label="Mô tả"><span><?php echo $rows["decription"] ?></span></td>
                    <td data-label="Hình ảnh"><img src="<?php echo $rows["image"] ?>" alt="" class="img"></td>
                    <td data-label="Giá"><span><?php echo number_format($rows["item_price"], 0, ',', '.') ?> VNĐ</span></td>
                    <td data-label="Danh mục"><span><?php echo $rows["category_id"] ?></span></td>
                    <td data-label="Tag"><span><?php echo $rows["product_tag"] ?></span></td>
                    <td data-label="Hành động">
                        <button class="update"><a href="../Admin/update.php?id=<?php echo $rows["product_id"] ?>">Update</a></button>
                        <form action='' method='post' style="display:inline;">
                            <input type='hidden' name='delete_id' value='<?php echo $rows["product_id"] ?>'>
                            <button class='delete' type='submit' name='delete' onclick="return confirm('Xóa sản phẩm này?')">Delete</button>
                        </form>
                    </td>
                </tr>
        <?php
            }
        }
        ?>
    </table>
</body>

</html>