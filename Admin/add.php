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

    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form">

        <div>
            <label for="">ID</label>
            <input type="text" name="product_id" id="">
        </div>
        <div>
            <label for="">name</label>
            <input type="text" name="product_name" id="">
        </div>
        <div>
            <label for="">quantity</label>
            <input type="text" name="quantity" id="">
        </div>
        <div>
            <label for="">decription</label>
            <input type="text" name="decription" id="">
        </div>
        <div>
            <label for="">price</label>
            <input type="text" name="item_price" id="">
        </div>
        <div>
            <label for="">image</label>
            <input type="text" name="image" id="">
        </div>
        <div>
            <label for="">category_ID</label>
            <input type="text" name="category_id" id="">
        </div>
        <div>
            <label for="">Tag</label>
            <input type="text" name="product_tag" id="">
        </div>
        <button>Save</button>

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