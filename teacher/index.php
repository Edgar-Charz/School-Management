 <?php
    session_start();
    include '../includes/db_connection.php';

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Teacher') {
        header("Location: ../php/login.php");
        exit();
    }

    // Check if login was successful
if (isset($_SESSION['login_success']) && $_SESSION['login_success']) {
    unset($_SESSION['login_success']);

    echo "<script>
          document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                //   title: 'Hi !,  " . ucwords(strtolower($_SESSION['name'])) . "',
                  text: 'Welcome back !, " . ucwords(strtolower($_SESSION['name'])) . "',
                  icon: 'success',
                  confirmButtonText: 'OK',
                  timer: 15000, 
                  timerProgressBar: true 
              });
          });
      </script>";
}


    $name = $_SESSION['name'];
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
         <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
         <link rel="stylesheet" href="../assets/css/styles.css">
         <style>
            .swal2-popup {
                font-size: 13px !important;
                width: 300px !important;
                background-color: rgba(255, 255, 255, 0.9) !important;
            }
        </style>
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
                 <li><a href="../php/logout.php"><i class="bi bi-box-arrow-right"></i><span class="menu-text">Logout</span></a></li>
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
             <h1>Welcome, <?= $_SESSION['name']; ?> (Teacher)</h1>

             <div class="card-container">
                 <div class="card">
                     <h3><br><br>

                     </h3>
                 </div>
                 <div class="card">
                     <h3><br><br>

                     </h3>
                 </div>
                 <div class="card">
                     <h3><br><br>

                     </h3>
                 </div>
                 <div class="card">
                     <h3><br><br>

                     </h3>
                 </div>

             </div>
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