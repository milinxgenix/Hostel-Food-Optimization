<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "hostel_food_waste");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

 // Collect POST data safely
$student_name = htmlspecialchars($_POST['student_name']);
$registration_number = htmlspecialchars($_POST['registration_number']);
$college_email = htmlspecialchars($_POST['college_email']);
$phone = htmlspecialchars($_POST['phone']);
// accept controlled mess type (preferred) or legacy free-text mess_name
$allowed_mess_types = ['Veg','Non-Veg','Special','All'];
if (isset($_POST['mess_type']) && in_array($_POST['mess_type'], $allowed_mess_types)) {
  $mess_name = htmlspecialchars($_POST['mess_type']);
} else {
  $mess_name = isset($_POST['mess_name']) ? htmlspecialchars($_POST['mess_name']) : '';
}
$hostel_block = htmlspecialchars($_POST['hostel_block']);
$religious_event = htmlspecialchars($_POST['religious_event']);
$special_diet = htmlspecialchars($_POST['special_diet']);
$diet_days = !empty($_POST['diet_days']) ? (int)$_POST['diet_days'] : 0;
$no_eat_days = !empty($_POST['no_eat_days']) ? (int)$_POST['no_eat_days'] : 0;
$switch_mess = htmlspecialchars($_POST['switch_mess']);


    // Insert into database
    $stmt = $conn->prepare("INSERT INTO mess_preferences
        (student_name, registration_number, college_email, phone, mess_name, hostel_block, religious_event, special_diet, diet_days, no_eat_days, switch_mess)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssiss", 
        $student_name, $registration_number, $college_email, $phone, 
        $mess_name, $hostel_block, $religious_event, $special_diet, $diet_days, $no_eat_days, $switch_mess);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    $submitted = true; // set flag to show Thank You message
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Mess Preference Form</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Floating animation for background images */
    .float-anim {
      animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
      100% { transform: translateY(0px); }
    }
  </style>
    <!-- particles script moved to the bottom to avoid duplicate initialization and DOM timing issues -->
</head>
<body class="min-h-screen p-6 bg-gradient-to-br from-yellow-200 via-orange-100 to-red-200 relative">
  <div id="tsparticles"></div>
<?php if ($submitted): ?>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-yellow-200 via-orange-100 to-red-200 animate-fade-in">
    <h1 class="text-5xl font-extrabold text-orange-700 mb-6">🎉 Thank You!</h1>
    <p class="text-2xl text-gray-700 mb-8">Your mess preferences have been submitted successfully.</p>
    <a href="form.php" class="px-6 py-3 bg-orange-500 text-white rounded-xl font-bold shadow-lg hover:bg-red-500 hover:scale-105 transition transform duration-300">Submit Another Response</a>
  </div>
<?php else: ?>

<!-- Centered Card Wrapper -->
<div class="flex justify-center">
  <div class="w-full max-w-3xl bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl p-10 relative z-10 border border-orange-200 mt-10 mb-10">
    <h1 class="text-4xl font-extrabold text-center text-orange-700 mb-8 drop-shadow-md">
      🍲 Student Mess & Food Preference Form
    </h1>

    <form action="form.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <!-- Student Name -->
      <div class="col-span-2">
        <label class="block text-lg font-semibold text-gray-700">👤 Student Name</label>
        <input type="text" name="student_name" required
          class="w-full mt-2 px-4 py-3 border-2 border-orange-300 rounded-xl shadow-sm focus:ring-4 focus:ring-orange-400 focus:outline-none" />
      </div>

      <!-- Registration Number -->
      <div>
        <label class="block text-lg font-semibold text-gray-700">🆔 Registration Number</label>
        <input type="text" name="registration_number" required
          class="w-full mt-2 px-4 py-3 border-2 border-orange-300 rounded-xl shadow-sm focus:ring-4 focus:ring-orange-400 focus:outline-none" />
      </div>

      <!-- College Email -->
      <div>
        <label class="block text-lg font-semibold text-gray-700">📧 College Email ID</label>
        <input type="email" name="college_email" required
          class="w-full mt-2 px-4 py-3 border-2 border-orange-300 rounded-xl shadow-sm focus:ring-4 focus:ring-orange-400 focus:outline-none" />
      </div>

      <!-- Phone -->
      <div>
        <label class="block text-lg font-semibold text-gray-700">📞 Phone Number</label>
        <input type="tel" name="phone" required
          class="w-full mt-2 px-4 py-3 border-2 border-orange-300 rounded-xl shadow-sm focus:ring-4 focus:ring-orange-400 focus:outline-none" />
      </div>

      <!-- Mess Type -->
      <div>
        <label class="block text-lg font-semibold text-gray-700">🏠 Mess Type</label>
        <select name="mess_type" required
          class="w-full mt-2 px-4 py-3 border-2 border-orange-300 rounded-xl shadow-sm focus:ring-4 focus:ring-orange-400 focus:outline-none">
          <option value="">Select Mess Type</option>
          <option value="Veg">Veg</option>
          <option value="Non-Veg">Non-Veg</option>
          <option value="Special">Special</option>
        </select>
      </div>

      <!-- Hostel Block -->
      <div>
        <label class="block text-lg font-semibold text-gray-700">🏢 Hostel Block</label>
        <input type="text" name="hostel_block"
          class="w-full mt-2 px-4 py-3 border-2 border-orange-300 rounded-xl shadow-sm focus:ring-4 focus:ring-orange-400 focus:outline-none" />
      </div>

      <!-- Religious Event -->
      <div>
        <label class="block text-lg font-semibold text-gray-700">🙏 Religious Event</label>
        <select name="religious_event"
          class="w-full mt-2 px-4 py-3 border-2 border-orange-300 rounded-xl shadow-sm focus:ring-4 focus:ring-orange-400 focus:outline-none">
          <option>No</option>
          <option>Navratri</option>
          <option>Roza</option>
          <option>Sawan</option>
          <option>Jain Paryushan</option>
          <option>Others</option>
        </select>
      </div>

      <!-- Special Diet -->
      <div class="col-span-2">
        <label class="block text-lg font-semibold text-gray-700">🥗 Any Special Food/Dietary or Health Concern</label>
        <textarea name="special_diet" rows="2"
          class="w-full mt-2 px-4 py-3 border-2 border-orange-300 rounded-xl shadow-sm focus:ring-4 focus:ring-orange-400 focus:outline-none"></textarea>
      </div>

      <!-- Number of Days Special Food -->
      <div>
        <label class="block text-lg font-semibold text-gray-700">📅 Days Continuing Special Food Habit</label>
        <input type="number" name="diet_days" min="0"
          class="w-full mt-2 px-4 py-3 border-2 border-orange-300 rounded-xl shadow-sm focus:ring-4 focus:ring-orange-400 focus:outline-none" />
      </div>

      <!-- Won’t Eat Days -->
      <div>
        <label class="block text-lg font-semibold text-gray-700">🚫 Won’t be Eating (Days)</label>
        <input type="number" name="no_eat_days" min="0"
          class="w-full mt-2 px-4 py-3 border-2 border-orange-300 rounded-xl shadow-sm focus:ring-4 focus:ring-orange-400 focus:outline-none" />
      </div>

      <!-- Switching Mess -->
      <div class="col-span-2">
        <label class="block text-lg font-semibold text-gray-700">🔄 Switching to Different Mess</label>
        <select name="switch_mess"
          class="w-full mt-2 px-4 py-3 border-2 border-orange-300 rounded-xl shadow-sm focus:ring-4 focus:ring-orange-400 focus:outline-none">
          <option>No</option>
          <option>Yes</option>
        </select>
      </div>

      <!-- Submit Button -->
      <div class="col-span-2 text-center">
        <button type="submit"
          class="w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-red-500 hover:to-orange-500 text-white font-bold py-3 px-6 rounded-xl shadow-xl transform hover:scale-105 transition duration-300">
          ✅ Submit Form
        </button>
      </div>
    </form>
  </div>
</div>

<?php endif; ?>

<!-- tsparticles container and script (single copy) -->
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.11.1/tsparticles.bundle.min.js"></script>
<script>
  (function(){
    const droplet = 'data:image/svg+xml;utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"%3E%3Cpath fill="%230b2545" d="M32 2C20 16 8 28 8 40a24 24 0 0048 0C56 28 44 16 32 2z"/%3E%3C/svg%3E';
      tsParticles.load('tsparticles', {
        fpsLimit: 60,
        background: { color: 'transparent' },
        particles: {
          number: { value: 55, density: { enable: true, area: 800 } },
          color: { value: ['#0b2545', '#083358', '#1e293b'] },
          shape: { type: ['image'], image: [{ src: droplet, width: 10, height: 10 }] },
          opacity: { value: 0.85, random: { enable: true, minimumValue: 0.4 } },
          size: { value: { min: 2, max: 6 }, random: true },
          move: { enable: true, speed: 1.2, direction: 'none', outModes: 'out' }
        },
        interactivity: { events: { onHover: { enable: true, mode: 'repulse' }, onClick: { enable: true, mode: 'push' } }, modes: { repulse: { distance: 110 }, push: { quantity: 4 } } },
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
