<?php
include("../Database/database.php");
/** @var PDO $conn */
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Document</title>
</head>

<body>
    <a href="../Admin/adminPage.php"><button>Manage Products</button></a>
    <button class="add"><a href="../Admin/adduser.php">Add new users</a></button>
    <table>
        <tr>
            <th>id</th>
            <th>customerName</th>
            <th>Password</th>
            <th>mail</th>
            <th>phone</th>
            <th>diachi</th>
            <th>Edit</th>
        </tr>
        <?php
        $sql = "SELECT * FROM users";
        $stmt = $conn->query($sql);

        if ($stmt->rowCount() > 0) {
            while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
                <tr>
                    <td><span><?php echo $rows["user_id"] ?></span></td>
                    <td><span><?php echo $rows["cus_name"] ?></span></td>
                    <td><span><?php echo $rows["password"] ?></span></td>
                    <td><span><?php echo $rows["email"] ?></span></td>
                    <td><span><?php echo $rows["phone"] ?></span></td>
                    <td><span><?php echo $rows["diachi"] ?></span></td>
                    <td>
                        <button class="update"><a
                                href="../Admin/updateuser.php?id=<?php echo $rows["user_id"] ?>">Update</a></button>
                        <?php
                        $id = $rows["user_id"];
                        if (isset($_POST["delete"])) {
                            // Get the ID of the user to delete
                            $delete_id = isset($_POST["delete_id"]) ? $_POST["delete_id"] : '';

                            // Delete the user from the database
                            if (!empty($delete_id)) {
                                $sql_delete = "DELETE FROM users WHERE user_id = :delete_id";
                                $stmt_delete = $conn->prepare($sql_delete);
                                $stmt_delete->bindValue(':delete_id', $delete_id, PDO::PARAM_INT);
                                $stmt_delete->execute();
                            }
                        }
                        echo "<form action='' method='post'>
                        <input type='hidden' name='delete_id' value='$id'>
                        <button class='delete' type='submit' name= 'delete' onclick=\"return confirm('Xóa sản phẩm này?')\">Delete</button>
                        </form>";
                        ?>
                    </td>
                </tr>
        <?php
            }
        }
        ?>
    </table>
</body>

</html>