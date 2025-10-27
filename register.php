<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";   // change if needed
$pass = "";       // change if needed
$db   = "hostel_food_waste"; 

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id     = $_POST['admin_id'];
    $name         = $_POST['name'];
    $email        = $_POST['email'];
    $mess_name    = $_POST['mess_name'];
    $hostel_block = $_POST['hostel_block'];
    $password     = $_POST['password'];
    $phone        = $_POST['phone'];

    // hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // check email duplicate
    $check = $conn->query("SELECT * FROM admin_register WHERE email='$email'");
    if ($check->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        $sql = "INSERT INTO admin_register (admin_id, name, email, mess_name, hostel_block, password, phone) 
                VALUES ('$admin_id', '$name', '$email', '$mess_name', '$hostel_block', '$hashedPassword', '$phone')";
        
        if ($conn->query($sql) === TRUE) {
            $success = "Registration successful! Redirecting to login...";
            header("refresh:2;url=login.php");
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Registration - Hostel Food Waste Optimization</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-yellow-300 via-green-300 to-orange-400 relative overflow-hidden">
  <div id="tsparticles"></div>

  <!-- Decorative Images -->
  <img src="https://images.unsplash.com/photo-1600891964092-4316c288032e" class="absolute top-10 left-5 w-44 h-44 rounded-2xl shadow-2xl object-cover opacity-70 transform rotate-[-10deg]" />
  <img src="https://images.unsplash.com/photo-1525755662778-989d0524087e" class="absolute bottom-10 right-5 w-48 h-48 rounded-2xl shadow-2xl object-cover opacity-70 transform rotate-[10deg]" />

  <!-- Form -->
  <div class="w-full max-w-2xl p-10 bg-white rounded-2xl shadow-2xl relative z-10">
    <h1 class="text-4xl font-extrabold text-center text-green-600 mb-6 animate-bounce">Admin Registration</h1>

    <?php if ($error) { ?><p class="text-red-600 font-semibold text-center mb-4"><?= $error ?></p><?php } ?>
    <?php if ($success) { ?><p class="text-green-600 font-semibold text-center mb-4"><?= $success ?></p><?php } ?>

    <form action="" method="POST" class="grid grid-cols-2 gap-6">
      <div>
        <label class="block text-lg font-semibold">Admin ID</label>
        <input type="text" name="admin_id" required class="w-full mt-2 px-5 py-3 text-lg border border-gray-300 rounded-xl focus:ring-4 focus:ring-green-300">
      </div>
      <div>
        <label class="block text-lg font-semibold">Name</label>
        <input type="text" name="name" required class="w-full mt-2 px-5 py-3 text-lg border border-gray-300 rounded-xl focus:ring-4 focus:ring-green-300">
      </div>
      <div>
        <label class="block text-lg font-semibold">Email</label>
        <input type="email" name="email" required class="w-full mt-2 px-5 py-3 text-lg border border-gray-300 rounded-xl focus:ring-4 focus:ring-orange-300">
      </div>
      <div>
        <label class="block text-lg font-semibold">Mess Name</label>
        <input type="text" name="mess_name" required class="w-full mt-2 px-5 py-3 text-lg border border-gray-300 rounded-xl focus:ring-4 focus:ring-orange-300">
      </div>
      <div>
        <label class="block text-lg font-semibold">Hostel Block</label>
        <input type="text" name="hostel_block" required class="w-full mt-2 px-5 py-3 text-lg border border-gray-300 rounded-xl focus:ring-4 focus:ring-green-300">
      </div>
      <div>
        <label class="block text-lg font-semibold">Phone</label>
        <input type="text" name="phone" required class="w-full mt-2 px-5 py-3 text-lg border border-gray-300 rounded-xl focus:ring-4 focus:ring-green-300">
      </div>
      <div class="col-span-2">
        <label class="block text-lg font-semibold">Password</label>
        <input type="password" name="password" required class="w-full mt-2 px-5 py-3 text-lg border border-gray-300 rounded-xl focus:ring-4 focus:ring-orange-300">
      </div>
      <div class="col-span-2">
        <button type="submit" class="w-full py-3 text-xl font-bold text-white bg-green-500 rounded-xl shadow-lg hover:bg-green-600 transform hover:scale-105 transition duration-300">Register</button>
      </div>
    </form>
  </div>
  <!-- tsparticles -->
  <script src="https://cdn.jsdelivr.net/npm/tsparticles@2.11.1/tsparticles.bundle.min.js"></script>
  <script>
    (function(){
      const droplet = 'data:image/svg+xml;utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"%3E%3Cpath fill="%230b2545" d="M32 2C20 16 8 28 8 40a24 24 0 0048 0C56 28 44 16 32 2z"/%3E%3C/svg%3E';
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
</body>
</html>
