<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "hostel_food_waste"; 

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin_register WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();


        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $row['email'];
            header("Location: dashboard.php"); // redirect page
            exit();
        } else {
            $error = "Invalid Password!";
        }
    } else {
        $error = "Invalid Email!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Hostel Food Waste Optimization</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col min-h-screen bg-gradient-to-r from-green-300 via-teal-300 to-cyan-400 relative overflow-hidden">

  <div id="tsparticles"></div>

  <!-- Decorative Images -->
  <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092" class="absolute top-10 left-5 w-44 h-44 rounded-2xl shadow-2xl object-cover opacity-70 transform rotate-[-10deg]" />
  <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836" class="absolute bottom-10 right-5 w-48 h-48 rounded-2xl shadow-2xl object-cover opacity-70 transform rotate-[10deg]" />

  <!-- Main Content Wrapper -->
<main class="flex-grow flex items-center justify-center relative z-10">



  <!-- Form -->
  <div class="w-full max-w-md p-10 bg-white/30 backdrop-blur-md rounded-2xl shadow-3xl border border-white/60">
    <h1 class="text-4xl font-extrabold text-center text-green-600 mb-6 animate-bounce">Admin Login</h1>
    <p class="text-center text-gray-600 mb-6">Hostel Food Waste Optimization System</p>

    <?php if ($error) { ?><p class="text-red-600 font-semibold text-center mb-4"><?= $error ?></p><?php } ?>

    <form action="" method="POST" class="space-y-6">
      <div>
        <label class="block text-lg font-semibold">Email</label>
        <input type="email" name="email" required class="w-full mt-2 px-5 py-3 text-lg border border-gray-300 rounded-xl focus:ring-4 focus:ring-green-300">
      </div>
      <div>
        <label class="block text-lg font-semibold">Password</label>
        <input type="password" name="password" required class="w-full mt-2 px-5 py-3 text-lg border border-gray-300 rounded-xl focus:ring-4 focus:ring-orange-300">
      </div>
      <button type="submit" class="w-full py-3 text-xl font-bold text-white bg-green-500 rounded-xl shadow-lg hover:bg-green-600 transform hover:scale-105 transition duration-300">Login</button>
    </form>
    <p class="text-center mt-6 text-gray-600">Don't have an account? <a href="register.php" class="text-green-600 font-semibold hover:underline">Register</a></p>
  </div>
  </main>

  <!-- tsparticles -->
  <script src="https://cdn.jsdelivr.net/npm/tsparticles@2.11.1/tsparticles.bundle.min.js"></script>
  <script>
    (function(){
      const droplet = 'data:image/svg+xml;utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"%3E%3Cpath fill="%230b2545" d="M32 2C20 16 8 28 8 40a24 24 0 0048 0C56 28 44 16 32 2z"/%3E%3C/svg%3E';

      // Droplets (subtle, image-based)
      tsParticles.load('tsparticles', {
        fpsLimit: 60,
        background: { color: 'transparent' },
        particles: {
          number: { value: 30, density: { enable: true, area: 900 } },
          shape: { type: ['image'], image: [{ src: droplet, width: 12, height: 12 }] },
          opacity: { value: 0.9, random: { enable: true, minimumValue: 0.45 } },
          size: { value: { min: 3, max: 7 }, random: true },
          move: { enable: true, speed: 0.6, direction: 'none', outModes: { default: 'out' } }
        },
        interactivity: { events: { onHover: { enable: true, mode: 'repulse' }, onClick: { enable: true, mode: 'push' } }, modes: { repulse: { distance: 100 }, push: { quantity: 4 } } },
        detectRetina: true
      });

      // Create secondary container for rising bubbles
      if (!document.getElementById('tsparticles2')) {
        const el = document.createElement('div');
        el.id = 'tsparticles2';
        document.body.appendChild(el);
      }
      tsParticles.load('tsparticles2', {
        fpsLimit: 60,
        background: { color: 'transparent' },
        particles: {
          number: { value: 45, density: { enable: true, area: 800 } },
          color: { value: ['#ffffff', '#dbeafe'] },
          shape: { type: 'circle' },
          opacity: { value: 0.25, random: { enable: true, minimumValue: 0.12 } },
          size: { value: { min: 6, max: 18 }, random: true },
          move: { enable: true, speed: 0.9, direction: 'top', outModes: { default: 'out' } }
        },
        interactivity: { events: { onHover: { enable: true, mode: 'bubble' }, onClick: { enable: true, mode: 'repulse' } }, modes: { bubble: { distance: 120, size: 30 }, repulse: { distance: 120 } } },
        detectRetina: true
      });
    })();
  </script>

      <!-- Footer -->
<footer class="bg-blue-900/60 backdrop-blur-md text-white text-center py-6 mt-16 shadow-inner w-full absolute bottom-0 left-0">
    <p class="text-sm md:text-base">
      © 2025 Hostel Food Optimization System. All rights reserved. | Licensed under <span class="font-semibold">VIT License</span>
    </p>
    <p class="text-sm mt-1">
      Developed by <span class="font-semibold text-yellow-300">KUMAR MILIND</span> & <span class="font-semibold text-yellow-300">AMRIT ANAND</span>
    </p>
  </footer>

</body>
</html>
