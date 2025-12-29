<?php
include("../Database/database.php");
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque&family=Cabin:wght@500&family=Josefin+Sans&family=Lato&family=Montserrat&family=Odibee+Sans&family=Pixelify+Sans&family=Tilt+Neon&display=swap" rel="stylesheet">
    <title>Document</title>
</head>

<body>
    <section class="container">
        <div class="login-container">
            <div class="login-background">
                <video autoplay loop muted>
                    <source type="video/mp4" src="../assets/video_background.mp4">
                </video>
            </div>
            <div class="login-form">
                <a href="../Index.php" style="text-decoration: none;">
                    <div class="login-title">De'Shop</div>
                </a>
                <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <h4>Welcome to De'Shop</h4>
                    <div class="login-side">
                        <div class="login-input">
                            <span>Username</span>
                            <input type="text" name="username" id="">
                        </div>
                        <div class="login-input">
                            <span>Password</span>
                            <input type="password" name="password" id="">
                        </div>
                        <div class="login-input">
                            <span>Email</span>
                            <input type="email" name="email" id="">
                        </div>
                        <div class="login-input">
                            <span>Phone</span>
                            <input type="text" name="phone" id="">
                        </div>
                        <div class="login-input">
                            <span>Dia chi</span>
                            <input type="text" name="address" id="">
                        </div>
                    </div>
                    <input type="submit" value="Sign up" class="submit">
                    <div class="register-side">
                        <span>Have an Account?</span><a href="../Authen/login.php">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</body>

</html>
<?php
include("../Database/database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cus_name = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, "phone", FILTER_SANITIZE_SPECIAL_CHARS);
    $address = filter_input(INPUT_POST, "address", FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($cus_name)) {
        echo "Please enter your username";
    } else if (empty($password)) {
        echo "Please enter your password";
    } else if (empty($email)) {
        echo "Please enter your email";
    } else if (empty($phone)) {
        echo "Please enter your phone number";
    } else if (empty($address)) { 
        echo "Please enter your address";
    } else {
        try {
            if ($conn === null) {
                throw new Exception("Database connection failed");
            }
            // Sử dụng kết nối $conn từ database.php
            $stmt = $conn->prepare("INSERT INTO users (cus_name, password, email, phone, diachi) VALUES (:cus_name, :password, :email, :phone, :diachi)");
            $stmt->bindParam(':cus_name', $cus_name);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':diachi', $address);
            $stmt->execute();
            session_start();
            $_SESSION["cus_name"] = $cus_name;
            header("Location: ../Index.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>