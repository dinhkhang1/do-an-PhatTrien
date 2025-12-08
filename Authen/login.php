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
                <div class="login-title"><a href="../Views/Home.php">De'Shop</a></div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <h4>Welcome to De'Shop</h4>
                    <div class="login-side">
                        <div class="login-input">
                            <span>Username</span>
                            <input type="text" name="username" required>
                        </div>
                        <div class="login-input">
                            <span>Password</span>
                            <input type="password" name="password" required>
                        </div>
                    </div>
                    <input type="submit" value="Sign in" class="submit">
                    <div class="register-side">
                        <span>New De'Shop member?</span><a href="../Authen/register.php">Create Account</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</body>

</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : '';

    try {
        if (!$conn) {
            throw new Exception("Database connection failed");
        }
        // Check admin login first
        $sqlAdmin = "SELECT * FROM admin WHERE username = :username AND password = :password";
        $stmtAdmin = $conn->prepare($sqlAdmin);
        $stmtAdmin->bindParam(':username', $username, PDO::PARAM_STR);
        $stmtAdmin->bindParam(':password', $password, PDO::PARAM_STR);
        $stmtAdmin->execute();
        $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            session_start();
            $_SESSION["username"] = $username;
            $_SESSION["is_admin"] = true;
            header("Location: ../Admin/adminPage.php");
            exit();
        }

        // Check user login
        $sqlUser = "SELECT * FROM users WHERE cus_name = :cus_name";
        $stmtUser = $conn->prepare($sqlUser);
        $stmtUser->bindParam(':cus_name', $username, PDO::PARAM_STR);
        $stmtUser->execute();
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password']) {
            session_start();
            $_SESSION["cus_name"] = $username;
            $_SESSION["is_admin"] = false;
            header("Location: ../Views/Home.php");
            exit();
        } else {
            echo "<span class='error' style='color:red; display:block; text-align:center;'>Wrong username or password!!</span>";
        }
    } catch (PDOException $e) {
        echo "<span class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</span>";
    }
}
?>