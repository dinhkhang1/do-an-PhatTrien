<?php
session_start();

// 1. Xóa tất cả các biến trong session
session_unset();

// 2. Hủy hoàn toàn session trên server
session_destroy();

// 3. Chuyển hướng về trang login
header("Location: ../Index.php");
exit(); // Luôn thêm exit sau header để ngăn code phía sau chạy tiếp
