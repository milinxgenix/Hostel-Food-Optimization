<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header('Location: login.php');
  exit();
}
$admin_name = isset($_SESSION['admin']) ? $_SESSION['admin'] : 'Admin';
?>
<?php
// Fetch some quick stats for mini-charts on dashboard
$host = "localhost";
$user = "root";
$pass = "";
$db   = "hostel_food_waste";
$conn = new mysqli("sql208.hstn.me", "mseet_40264447", "Alohomora", "mseet_40264447_hostel_food");

if ($conn->connect_error) { /* silent fallback */ }
// Allowed lists (same as data_analysis)
$allowed_mess = ['Veg', 'Non-Veg', 'Special', 'All'];
$blocks = [];
foreach (range('A','T') as $ch) { if ($ch === 'O') continue; $blocks[] = $ch; }

$sel_mess = isset($_GET['mess_type']) ? $_GET['mess_type'] : '';
$sel_block = isset($_GET['hostel_block']) ? $_GET['hostel_block'] : '';
$where = [];
if ($sel_mess && $sel_mess !== 'All' && in_array($sel_mess, $allowed_mess)) {
  $where[] = "mess_name='".$conn->real_escape_string($sel_mess)."'";
}
if ($sel_block && $sel_block !== 'All' && in_array($sel_block, $blocks)) {
  $where[] = "hostel_block='".$conn->real_escape_string($sel_block)."'";
}
$where_sql = $where ? ('WHERE '.implode(' AND ', $where)) : '';

// Quick aggregates
$mess_counts = [];
$res = $conn->query("SELECT mess_name, COUNT(*) as c FROM mess_preferences $where_sql GROUP BY mess_name");
$mess_counts = ['Veg'=>0,'Non-Veg'=>0,'Special'=>0];
while ($r = $res->fetch_assoc()) {
  $raw = trim($r['mess_name']);
  $lc = strtolower($raw);
  $cnt = (int)$r['c'];
  if ($lc === '') continue;
  if (strpos($lc, 'non') !== false) {
    $mess_counts['Non-Veg'] += $cnt;
  } elseif (strpos($lc, 'veg') !== false) {
    $mess_counts['Veg'] += $cnt;
  } elseif (strpos($lc, 'special') !== false) {
    $mess_counts['Special'] += $cnt;
  } else {
    // fallback assign to Veg
    $mess_counts['Veg'] += $cnt;
  }
}
$block_counts = [];
$res = $conn->query("SELECT hostel_block, COUNT(*) as c FROM mess_preferences $where_sql GROUP BY hostel_block");
$block_counts = array_fill_keys($blocks, 0);
while ($r = $res->fetch_assoc()) {
  $raw = trim($r['hostel_block']);
  if ($raw === '') continue;
  $letter = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $raw), 0, 1));
  if ($letter === '' || $letter === 'O') continue;
  if (in_array($letter, $blocks)) $block_counts[$letter] += (int)$r['c'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: linear-gradient(135deg, #a7ffeb 0%, #81e1ff 50%, #fbc2eb 100%);
      min-height: 100vh;
      overflow-x: hidden;
      font-family: 'Poppins', sans-serif;
    }

    /* Floating animation for corner images */
    @keyframes float {
      0%, 100% {
        transform: translateY(0px) rotate(0deg);
      }
      50% {
        transform: translateY(-15px) rotate(3deg);
      }
    }

    .float-image {
      animation: float 3.5s ease-in-out infinite; /* Faster float speed */
    }


    /* Shiny glowing heading */
    .shiny-text {
      color: #000;
      font-size: 5.5rem;
      background: linear-gradient(90deg, #000 0%, #222 25%, #444 50%, #000 75%, #000 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-size: 200% auto;
      animation: shine 3s linear infinite;
      text-shadow: 0 0 25px rgba(0, 0, 0, 0.6);
    }

    @keyframes shine {
      0% { background-position: 200% center; }
      100% { background-position: -200% center; }
    }

    /* Enhanced central button glow and hover */
    .glow-btn {
      background: linear-gradient(90deg, #4f46e5, #06b6d4, #10b981, #facc15);
      background-size: 300% 300%;
      animation: gradientMove 5s ease infinite;
      color: white;
      font-weight: 800;
      letter-spacing: 1px;
      text-transform: uppercase;
      box-shadow: 0 0 30px rgba(79, 70, 229, 0.4);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .glow-btn:hover {
      transform: scale(1.08);
      box-shadow: 0 0 40px rgba(16, 185, 129, 0.6);
    }

    @keyframes gradientMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
  </style>
</head>

<body class="relative flex flex-col items-center justify-center">
  <div id="tsparticles"></div>

  <!-- Corner Floating Images -->
  <img src="images/1.png" class="float-image fixed top-4 left-4 w-28 h-28 object-cover rounded-2xl shadow-lg ring-2 ring-blue-200 opacity-90 hover:scale-110 transition duration-500" alt="">
  <img src="images/1.png" class="float-image fixed top-4 right-4 w-28 h-28 object-cover rounded-2xl shadow-lg ring-2 ring-green-200 opacity-90 hover:scale-110 transition duration-500" alt="">
  <img src="images/1.png" class="float-image fixed bottom-4 left-4 w-28 h-28 object-cover rounded-2xl shadow-lg ring-2 ring-pink-200 opacity-90 hover:scale-110 transition duration-500" alt="">
  <img src="images/1.png" class="float-image fixed bottom-4 right-4 w-28 h-28 object-cover rounded-2xl shadow-lg ring-2 ring-yellow-200 opacity-90 hover:scale-110 transition duration-500" alt="">

  <!-- Shiny Animated Heading -->
  <header class="text-center mt-10">
    <h1 id="welcome-text" class="shiny-text font-extrabold drop-shadow-2xl whitespace-nowrap"></h1>
  </header>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const text = "Welcome, Admin!";
      const element = document.getElementById("welcome-text");
      let index = 0;

      function typeEffect() {
        if (index < text.length) {
          element.textContent += text.charAt(index);
          index++;
          setTimeout(typeEffect, 100); // typing speed
        }
      }
      typeEffect();
    });
  </script>

  <!-- Buttons Section -->
  <div class="flex flex-col items-center justify-center mt-28 w-full max-w-xl gap-12 px-6">

    <!-- Filter row for mini-charts -->
    <div class="w-full bg-white/60 p-4 rounded-xl shadow-md mb-6">
      <form method="get" class="flex gap-4 items-center justify-center">
        <div>
          <label class="font-semibold">Mess Type</label>
          <select name="mess_type" class="border rounded px-2 py-1">
            <option value="All" <?= ($sel_mess==''||$sel_mess=='All')? 'selected':'' ?>>All</option>
            <?php foreach($allowed_mess as $m) { if($m==='All') continue; ?>
              <option value="<?= htmlspecialchars($m) ?>" <?= $sel_mess==$m? 'selected':'' ?>><?= htmlspecialchars($m) ?></option>
            <?php } ?>
          </select>
        </div>
        <div>
          <label class="font-semibold">Block</label>
          <select name="hostel_block" class="border rounded px-2 py-1">
            <option value="All" <?= ($sel_block==''||$sel_block=='All')? 'selected':'' ?>>All</option>
            <?php foreach($blocks as $b) { ?>
              <option value="<?= htmlspecialchars($b) ?>" <?= $sel_block==$b? 'selected':'' ?>><?= htmlspecialchars($b) ?></option>
            <?php } ?>
          </select>
        </div>
        <div>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Apply</button>
        </div>
      </form>
    </div>

    <!-- Small charts row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full mb-6">
      <div class="bg-white rounded-xl shadow p-4">
        <h3 class="font-semibold mb-2">Mess Type Distribution</h3>
        <canvas id="dashMessPie"></canvas>
      </div>
      <div class="bg-white rounded-xl shadow p-4">
        <h3 class="font-semibold mb-2">Block Distribution</h3>
        <canvas id="dashBlockBar"></canvas>
      </div>
    </div>

    <!-- Navigation buttons -->
    <a href="data_analysis.php" class="w-full">
      <button class="glow-btn w-full px-16 py-8 text-3xl rounded-3xl border-4 border-transparent shadow-2xl">
        View Data Analysis
      </button>
    </a>

    <!-- Logout Button -->
    <a href="logout.php" class="w-full">
      <button class="w-full px-12 py-6 text-2xl font-bold text-white bg-gradient-to-r from-red-500 to-red-400 rounded-2xl shadow-xl transition duration-300 hover:scale-105 hover:shadow-2xl border-2 border-red-300">
        Logout
      </button>
    </a>

  </div>

  <!-- tsparticles -->
  <script src="https://cdn.jsdelivr.net/npm/tsparticles@2.11.1/tsparticles.bundle.min.js"></script>
  <script>
    tsParticles.load('tsparticles', {
      fpsLimit: 60,
      background: { color: "transparent" },
      particles: {
        number: { value: 45, density: { enable: true, area: 900 } },
        color: { value: ['#ffffff', '#fef3c7', '#fce7f3'] },
        shape: { type: 'circle' },
        opacity: { value: 0.75 },
        size: { value: { min: 1, max: 4 } },
        move: { enable: true, speed: 1.3, direction: 'none', outModes: 'out' }
      },
      interactivity: { events: { onHover: { enable: true, mode: 'repulse' }, onClick: { enable: true, mode: 'push' } }, modes: { repulse: { distance: 100 }, push: { quantity: 4 } } },
      detectRetina: true
    });
  </script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // small dashboard charts
    const dashMessLabels = <?= json_encode(array_keys($mess_counts)) ?>;
    const dashMessData = <?= json_encode(array_values($mess_counts)) ?>;
    const dashBlockLabels = <?= json_encode(array_keys($block_counts)) ?>;
    const dashBlockData = <?= json_encode(array_values($block_counts)) ?>;

    // pie
    const ctxPie = document.getElementById('dashMessPie');
    if (ctxPie) new Chart(ctxPie, {
      type: 'pie',
      data: { labels: dashMessLabels, datasets: [{ data: dashMessData, backgroundColor: ['#34d399','#60a5fa','#f472b6','#fbbf24'] }] },
      options: { plugins: { legend: { display: true, position: 'bottom' } } }
    });

    // bar
    const ctxBar = document.getElementById('dashBlockBar');
    if (ctxBar) new Chart(ctxBar, {
      type: 'bar',
      data: { labels: dashBlockLabels, datasets: [{ label: 'Students', data: dashBlockData, backgroundColor: '#6366f1' }] },
      options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
    });
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
