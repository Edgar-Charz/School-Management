 <?php
    session_start();
    include '../includes/db_connection.php';

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Teacher') {
        header("Location: ../php/login.php");
        exit();
    }

    $teacher_id = $_SESSION['id'];
    $msg = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = $_POST['title'];
        $message = $_POST['message'];

        $announcement_stmt = $conn->prepare("INSERT INTO announcements (teacher_id, title, message)
                                                VALUES (?, ?, ?)");
        $announcement_stmt->bind_param("iss", $teacher_id, $title, $message);

        if ($announcement_stmt->execute()) {
            $msg = "Announcement sent successfully";
        } else {
            $msg = "Failed to send Announcement";
        }
    }
    ?>



 <!DOCTYPE html>
 <html lang="en">

 <head>

     <head>
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1">
         <title>Dream School | Dashboard</title>
         <!-- Bootstrap Icons CDN -->
         <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
         <link rel="stylesheet" href="../assets/css/styles.css">
     </head>

 <body>

     <!-- Sidebar -->
     <div class="sidebar" id="sidebar">
         <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
         <div class="sidebar-content">
             <ul class="menu-list">
                 <li><a href="index.php"><i class="bi bi-house-door"></i><span class="menu-text">Dashboard</span></a></li>
                 <li><a href="add_announcement.php"><i class="bi bi-person"></i><span class="menu-text">Add Announcement</span></a></li>
                 <li><a href="view_announcements.php"><i class="bi bi-person"></i><span class="menu-text">View Announcement</span></a></li>
                 <li><a href="view_students.php"><i class="bi bi-gear"></i><span class="menu-text">View Students</span></a></li>
                 <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i><span class="menu-text">Logout</span></a></li>
             </ul>
         </div>
     </div>

     <!-- Main area (Top bar + Content) -->
     <div class="main-area" id="main-area">

         <!-- Top Navbar -->
         <div class="navbar">
             <div class="navbar-left">
                 <!-- <button class="toggle-btn" onclick="toggleSidebar()">☰</button> -->
                 <h2>Dream School</h2>
             </div>
             <div class="nav-links">
                 <a href="logout.php"><i class="bi bi-box-arrow-right"></i></a>
             </div>
         </div>

         <!-- Main Content -->
         <div class="main-content">
             <h2>Send Announcement</h2>
             <?php if ($msg) echo "<p>$msg</p>"; ?>
             <form method="POST">
                 <input type="text" name="title" placeholder="Announcement Title" required><br><br>
                 <textarea name="message" placeholder="Write your message here..." rows="5" required></textarea><br><br>
                 <button type="submit">Send</button>
             </form>

         </div>
     </div>

     <script>
         function toggleSidebar() {
             const sidebar = document.getElementById('sidebar');
             sidebar.classList.toggle('collapsed');
         }
     </script>

 </body>

 </html>