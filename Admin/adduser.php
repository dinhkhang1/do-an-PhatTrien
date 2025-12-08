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
            <input type="text" name="user_id" id="">
        </div>
        <div>
            <label for="">name</label>
            <input type="text" name="cus_name" id="">
        </div>

        <div>
            <label for="">Pass</label>
            <input type="text" name="password" id="">
        </div>
        <div>
            <label for="">email</label>
            <input type="email" name="email" id="">
        </div>
        <div>
            <label for="">so dien thoai</label>
            <input type="text" name="phone" id="">
        </div>
        <div>
            <label for="">dia chi</label>
            <input type="text" name="diachi" id="">
        </div>
        <button>Save</button>

    </form>
    <?php
    include("../Database/database.php");
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_id = $_POST["user_id"];
            $cus_name = $_POST["cus_name"];
            $pass = $_POST["password"];
            $email = $_POST["email"];
            $sdt = $_POST["phone"];
            $diachi = $_POST["diachi"];
            // $pdo = new PDO("mysql:host=localhost;dbname:dbqa","root","");
            // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql_insert = $conn->prepare("INSERT INTO users (user_id,cus_name,password,email,phone,diachi)
            values (:user_id,:cus_name,:password,:phone,:email,:diachi)");
            $sql_insert->bindParam(':user_id', $user_id);
            $sql_insert->bindParam(':cus_name', $cus_name);
            $sql_insert->bindParam(':password', $pass);
            $sql_insert->bindParam(':email', $email);
            $sql_insert->bindParam(':phone', $sdt);
            $sql_insert->bindParam(':diachi', $diachi);


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