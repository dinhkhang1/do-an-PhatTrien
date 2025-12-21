<?php
session_start();
include("../Database/database.php");
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login.css">
    <title>Login</title>
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
                <div class="login-title"><a href="../Index.php">De'Shop</a></div>
                <form action="" method="post">
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

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $username = $_POST["username"];
        $password = $_POST["password"];



        try {
            if (!$conn) {
                throw new Exception("Database connection failed");
            }

            // Check admin
            $sqlAdmin = "SELECT username, password FROM admin WHERE username = :username";
            $stmtAdmin = $conn->prepare($sqlAdmin);
            $stmtAdmin->execute(['username' => $username]);
            $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

            if ($admin && $admin['password'] === $password) {
                $_SESSION["admin_username"] = $admin["username"];
                $_SESSION["is_admin"] = true;
                header("Location: ../Admin/adminPage.php");
                exit();
            }

            // Check user
            $sqlUser = "SELECT user_id, cus_name FROM users 
                        WHERE cus_name = :cus_name AND password = :password";
            $stmtUser = $conn->prepare($sqlUser);
            $stmtUser->execute([
                ':cus_name' => $username,
                ':password' => $password,
            ]);

            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["cus_name"] = $user["cus_name"];
                $_SESSION["is_admin"] = false;

                header("Location: ../Index.php");
                exit();
            }

            echo "<span style='color:red; text-align:center; display:block; margin-top:10px;'>Wrong username or password!</span>";
        } catch (PDOException $e) {
            echo "<span class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</span>";
        }
    }
    ?>
</body>

</html>