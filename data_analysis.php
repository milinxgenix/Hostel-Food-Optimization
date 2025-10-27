<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header('Location: login.php');
  exit();
}
$host = "localhost";
$user = "root";
$pass = "";
$db   = "hostel_food_waste";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$selected_mess_type = isset($_GET['mess_type']) ? $_GET['mess_type'] : '';
$selected_block = isset($_GET['hostel_block']) ? $_GET['hostel_block'] : '';
// Restrict mess type filter to the allowed set and provide an 'All' option
$allowed_mess = ['Veg', 'Non-Veg', 'Special', 'All'];
// Blocks A..T excluding O
$blocks = [];
foreach (range('A', 'T') as $ch) {
  if ($ch === 'O') continue;
  $blocks[] = $ch;
}

$where = [];
if ($selected_mess_type && $selected_mess_type !== 'All' && in_array($selected_mess_type, $allowed_mess)) {
  $where[] = "mess_name='".$conn->real_escape_string($selected_mess_type)."'";
}
if ($selected_block && $selected_block !== 'All' && in_array($selected_block, $blocks)) {
  $where[] = "hostel_block='".$conn->real_escape_string($selected_block)."'";
}
$where_sql = $where ? ('WHERE '.implode(' AND ', $where)) : '';
// Mess type distribution (filtered)
// Mess type distribution (filtered) - normalize DB mess_name values into the allowed categories
$mess_counts = ['Veg'=>0,'Non-Veg'=>0,'Special'=>0];
$result = $conn->query("SELECT mess_name, COUNT(*) as count FROM mess_preferences $where_sql GROUP BY mess_name");
while ($row = $result->fetch_assoc()) {
  $raw = trim($row['mess_name']);
  $lc = strtolower($raw);
  $cnt = (int)$row['count'];
  if ($lc === '') continue;
  if (strpos($lc, 'non') !== false) {
    $mess_counts['Non-Veg'] += $cnt;
  } elseif (strpos($lc, 'veg') !== false) {
    $mess_counts['Veg'] += $cnt;
  } elseif (strpos($lc, 'special') !== false) {
    $mess_counts['Special'] += $cnt;
  } else {
    // try other heuristics: words like 'north','south','east','west' map to Veg by default
    if (preg_match('/north|south|east|west|mess/', $lc)) {
      $mess_counts['Veg'] += $cnt; // assume default Veg when unsure
    } else {
      // unknowns: increment Veg to keep charts simple (or ignore). We'll add to Veg.
      $mess_counts['Veg'] += $cnt;
    }
  }
}
// Hostel block distribution (filtered)
// Hostel block distribution (filtered) - normalize block values to single-letter blocks A..T excluding O
$block_counts = array_fill_keys($blocks, 0);
$result = $conn->query("SELECT hostel_block, COUNT(*) as count FROM mess_preferences $where_sql GROUP BY hostel_block");
while ($row = $result->fetch_assoc()) {
  $raw = trim($row['hostel_block']);
  if ($raw === '') continue;
  // extract first letter A-Z
  $letter = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $raw), 0, 1));
  if ($letter === '' || $letter === 'O') continue; // skip invalid or excluded
  if (in_array($letter, $blocks)) {
    $block_counts[$letter] += (int)$row['count'];
  }
}
// Diet days histogram (filtered)
$diet_days = [];
$result = $conn->query("SELECT diet_days FROM mess_preferences $where_sql");
while ($row = $result->fetch_assoc()) {
  $diet_days[] = $row['diet_days'];
}
// Special diet distribution (filtered)
$special_diet_counts = [];
$result = $conn->query("SELECT special_diet, COUNT(*) as count FROM mess_preferences $where_sql GROUP BY special_diet HAVING special_diet != ''");
while ($row = $result->fetch_assoc()) {
  $special_diet_counts[$row['special_diet']] = $row['count'];
}
// Switch mess stats (filtered)
$switch_counts = ['Yes' => 0, 'No' => 0];
$result = $conn->query("SELECT switch_mess, COUNT(*) as count FROM mess_preferences $where_sql GROUP BY switch_mess");
while ($row = $result->fetch_assoc()) {
  $switch_counts[$row['switch_mess']] = $row['count'];
}
// Summary stats
$total = 0;
$popular_mess = '';
$popular_block = '';
$result = $conn->query("SELECT COUNT(*) as total FROM mess_preferences $where_sql");
if ($row = $result->fetch_assoc()) $total = $row['total'];
$result = $conn->query("SELECT mess_name, COUNT(*) as c FROM mess_preferences $where_sql GROUP BY mess_name ORDER BY c DESC LIMIT 1");
if ($row = $result->fetch_assoc()) $popular_mess = $row['mess_name'];
$result = $conn->query("SELECT hostel_block, COUNT(*) as c FROM mess_preferences $where_sql GROUP BY hostel_block ORDER BY c DESC LIMIT 1");
if ($row = $result->fetch_assoc()) $popular_block = $row['hostel_block'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Data Analysis Charts</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-300 min-h-screen flex flex-col justify-between">
  <div id="tsparticles"></div>
  <h1 class="text-3xl font-bold text-center mb-8 text-blue-700">Admin: Mess Preferences Data Analysis</h1>
  <!-- Summary Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto mb-8">
    <div class="bg-gradient-to-br from-green-200 to-green-400 rounded-xl shadow p-6 text-center">
      <div class="text-4xl font-bold text-green-900 mb-2"><?= $total ?></div>
      <div class="font-semibold text-green-800">Total Responses</div>
    </div>
    <div class="bg-gradient-to-br from-blue-200 to-blue-400 rounded-xl shadow p-6 text-center">
      <div class="text-2xl font-bold text-blue-900 mb-2"><?= htmlspecialchars($popular_mess) ?: 'N/A' ?></div>
      <div class="font-semibold text-blue-800">Most Popular Mess</div>
    </div>
    <div class="bg-gradient-to-br from-yellow-200 to-yellow-400 rounded-xl shadow p-6 text-center">
      <div class="text-2xl font-bold text-yellow-900 mb-2"><?= htmlspecialchars($popular_block) ?: 'N/A' ?></div>
      <div class="font-semibold text-yellow-800">Most Popular Block</div>
    </div>
  </div>
  <!-- tsparticles -->
  <script src="https://cdn.jsdelivr.net/npm/tsparticles@2.11.1/tsparticles.bundle.min.js"></script>
  <script>
    (function(){
      const droplet = 'data:image/svg+xml;utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"%3E%3Cpath fill="%230b2545" d="M32 2C20 16 8 28 8 40a24 24 0 0048 0C56 28 44 16 32 2z"/%3E%3C/svg%3E';

      // Primary: subtle droplets (image-based)
      tsParticles.load('tsparticles', {
        fpsLimit: 60,
        background: { color: 'transparent' },
        particles: {
          number: { value: 28, density: { enable: true, area: 900 } },
          shape: { type: ['image'], image: [{ src: droplet, width: 12, height: 12 }] },
          opacity: { value: 0.9, random: { enable: true, minimumValue: 0.45 } },
          size: { value: { min: 3, max: 8 }, random: true },
          move: { enable: true, speed: 0.5, direction: 'none', outModes: { default: 'out' } }
        },
        interactivity: { events: { onHover: { enable: true, mode: 'repulse' }, onClick: { enable: true, mode: 'push' } }, modes: { repulse: { distance: 100 }, push: { quantity: 3 } } },
        detectRetina: true
      });

      // Secondary: translucent rising bubbles
      if (!document.getElementById('tsparticles2')) {
        const el = document.createElement('div');
        el.id = 'tsparticles2';
        // place second container above footer but behind content
        el.style.position = 'fixed';
        el.style.left = '0';
        el.style.top = '0';
        el.style.width = '100%';
        el.style.height = '100%';
        el.style.zIndex = '0';
        el.style.pointerEvents = 'none';
        document.body.appendChild(el);
      }
      tsParticles.load('tsparticles2', {
        fpsLimit: 60,
        background: { color: 'transparent' },
        particles: {
          number: { value: 40, density: { enable: true, area: 900 } },
          color: { value: ['#ffffff', '#cfeafe'] },
          shape: { type: 'circle' },
          opacity: { value: 0.22, random: { enable: true, minimumValue: 0.08 } },
          size: { value: { min: 6, max: 20 }, random: true },
          move: { enable: true, speed: 0.9, direction: 'top', outModes: { default: 'out' } }
        },
        interactivity: { events: { onHover: { enable: true, mode: 'bubble' }, onClick: { enable: true, mode: 'repulse' } }, modes: { bubble: { distance: 120, size: 34 }, repulse: { distance: 120 } } },
        detectRetina: true
      });
    })();
  </script>
  <div class="max-w-xl mx-auto mb-8 bg-white rounded-xl shadow p-6">
    <form method="get" class="flex flex-col md:flex-row gap-4 items-center justify-center">
      <div>
        <label class="font-semibold">Mess Type:</label>
        <select name="mess_type" class="border rounded px-2 py-1">
          <option value="All" <?= ($selected_mess_type=='' || $selected_mess_type=='All')? 'selected' : '' ?>>All</option>
          <?php foreach($allowed_mess as $m): if($m==='All') continue; ?>
            <option value="<?= htmlspecialchars($m) ?>" <?= $selected_mess_type==$m?'selected':'' ?>><?= htmlspecialchars($m) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="font-semibold">Block:</label>
        <select name="hostel_block" class="border rounded px-2 py-1">
          <option value="All" <?= ($selected_block=='' || $selected_block=='All')? 'selected' : '' ?>>All</option>
          <?php foreach($blocks as $b): ?>
            <option value="<?= htmlspecialchars($b) ?>" <?= $selected_block==$b?'selected':'' ?>><?= htmlspecialchars($b) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded font-bold hover:bg-blue-700 transition">Show Charts</button>
    </form>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
    <!-- Pie Chart: Mess Type Distribution -->
    <div class="bg-white rounded-xl shadow p-6">
      <h2 class="text-xl font-semibold mb-4 text-center">Mess Type Distribution</h2>
      <canvas id="messPie"></canvas>
    </div>
    <!-- Bar Chart: Hostel Block Distribution -->
    <div class="bg-white rounded-xl shadow p-6">
      <h2 class="text-xl font-semibold mb-4 text-center">Hostel Block Distribution</h2>
      <canvas id="blockBar"></canvas>
    </div>
    <!-- Doughnut Chart: Special Diets -->
    <div class="bg-white rounded-xl shadow p-6">
      <h2 class="text-xl font-semibold mb-4 text-center">Special Diets</h2>
      <canvas id="dietDoughnut"></canvas>
    </div>
    <!-- Stacked Bar: Switch Mess -->
    <div class="bg-white rounded-xl shadow p-6">
      <h2 class="text-xl font-semibold mb-4 text-center">Switch Mess Preference</h2>
      <canvas id="switchBar"></canvas>
    </div>
    <!-- Histogram: Diet Days -->
    <div class="bg-white rounded-xl shadow p-6 col-span-1 md:col-span-2">
      <h2 class="text-xl font-semibold mb-4 text-center">Diet Days Histogram</h2>
      <canvas id="dietHist"></canvas>
    </div>
  </div>
  <div class="text-center mt-8 mb-0">
    <a href="logout.php" class="block text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-transform transform hover:scale-105 mt-6 mb-8 mx-auto w-fit">Logout</a>

  </div>
  <script>
    // Vibrant color palettes
    const palette1 = ['#34d399', '#fbbf24', '#60a5fa', '#f87171', '#a78bfa', '#f472b6', '#38bdf8', '#facc15', '#4ade80', '#f87171'];
    const palette2 = ['#6366f1', '#f59e42', '#10b981', '#f43f5e', '#f472b6', '#fbbf24', '#818cf8', '#fcd34d', '#fca5a5', '#a3e635'];

    // Mess Pie Chart
    const messData = {
      labels: <?= json_encode(array_keys($mess_counts)) ?>,
      datasets: [{
        data: <?= json_encode(array_values($mess_counts)) ?>,
        backgroundColor: palette1,
      }]
    };
    new Chart(document.getElementById('messPie'), {
      type: 'pie',
      data: messData,
      options: { plugins: { legend: { position: 'bottom' } } }
    });

    // Block Bar Chart
    const blockData = {
      labels: <?= json_encode(array_keys($block_counts)) ?>,
      datasets: [{
        label: 'Students',
        data: <?= json_encode(array_values($block_counts)) ?>,
        backgroundColor: palette2
      }]
    };
    new Chart(document.getElementById('blockBar'), {
      type: 'bar',
      data: blockData,
      options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
    });

    // Doughnut Chart: Special Diets
    const dietDoughnutData = {
      labels: <?= json_encode(array_keys($special_diet_counts)) ?>,
      datasets: [{
        data: <?= json_encode(array_values($special_diet_counts)) ?>,
        backgroundColor: palette2,
      }]
    };
    new Chart(document.getElementById('dietDoughnut'), {
      type: 'doughnut',
      data: dietDoughnutData,
      options: { plugins: { legend: { position: 'bottom' } } }
    });

    // Stacked Bar: Switch Mess
    const switchBarData = {
      labels: ['Switch Mess'],
      datasets: [
        {
          label: 'Yes',
          data: [<?= (int)$switch_counts['Yes'] ?>],
          backgroundColor: '#f87171'
        },
        {
          label: 'No',
          data: [<?= (int)$switch_counts['No'] ?>],
          backgroundColor: '#34d399'
        }
      ]
    };
    new Chart(document.getElementById('switchBar'), {
      type: 'bar',
      data: switchBarData,
      options: {
        plugins: { legend: { position: 'bottom' } },
        responsive: true,
        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
      }
    });

    // Diet Days Histogram
    const dietDays = <?= json_encode($diet_days) ?>;
    const histLabels = Array.from({length: 8}, (_, i) => i);
    const histData = histLabels.map(d => dietDays.filter(x => x == d).length);
    new Chart(document.getElementById('dietHist'), {
      type: 'line',
      data: {
        labels: histLabels,
        datasets: [{
          label: 'Number of Students',
          data: histData,
          backgroundColor: '#fbbf24',
          borderColor: '#f59e42',
          fill: true,
          tension: 0.4
        }]
      },
      options: { scales: { y: { beginAtZero: true } } }
    });
  </script>

      <!-- Footer -->
<footer class="bg-blue-900/60 backdrop-blur-md text-white text-center py-3 shadow-inner w-full text-sm">
    <p class="text-sm md:text-base">
      © 2025 Hostel Food Optimization System. All rights reserved. | Licensed under <span class="font-semibold">VIT License</span>
    </p>
    <p class="text-sm mt-1">
      Developed by <span class="font-semibold text-yellow-300">KUMAR MILIND</span> & <span class="font-semibold text-yellow-300">AMRIT ANAND</span>
    </p>
  </footer>
  
</body>
</html>
