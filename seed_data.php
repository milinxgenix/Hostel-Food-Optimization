<?php
// seed_data.php
// Run this script once (open in browser or CLI) to insert dummy rows into mess_preferences.

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'hostel_food_waste';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die('Connection failed: ' . $conn->connect_error);
}

// Quick safety: do not run in production accidentally
if (php_sapi_name() !== 'cli' && !isset($_GET['confirm'])) {
  echo "This will insert dummy rows into `mess_preferences`.\n";
  echo "To proceed open this URL with ?confirm=1 or run via CLI: php seed_data.php\n";
  exit;
}

$messNames = ['Veg', 'Non-Veg', 'Special', 'All'];
$blocks = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','P','Q','R','S','T'];
$specials = ['', 'Vegan', 'Gluten-Free', 'Halal', 'No Onions', 'Lactose-Free'];

$hasCreated = false;
$colRes = $conn->query("SHOW COLUMNS FROM mess_preferences LIKE 'created_at'");
if ($colRes && $colRes->num_rows > 0) {
  $hasCreated = true;
}

// We'll insert using the same columns used by the student form (safe default)
if ($hasCreated) {
  $sql = "INSERT INTO mess_preferences (student_name, registration_number, college_email, phone, mess_name, hostel_block, religious_event, special_diet, diet_days, no_eat_days, switch_mess, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
} else {
  $sql = "INSERT INTO mess_preferences (student_name, registration_number, college_email, phone, mess_name, hostel_block, religious_event, special_diet, diet_days, no_eat_days, switch_mess) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
}

$stmt = $conn->prepare($sql);
if (!$stmt) { die('Prepare failed: ' . $conn->error); }

$now = new DateTime();
$count = 0;

// Configuration: how many initial rows and minimums to ensure good chart coverage
$initial_rows = 350; // base rows to generate
$min_per_mess = 30;  // ensure at least this many rows per mess type
$min_per_block = 20; // ensure at least this many rows per hostel block

// Generate initial rows across several months to create trends
for ($i = 1; $i <= $initial_rows; $i++) {
  $reg = sprintf('MIS%04d', 1000 + $i);
  $name = 'Student ' . $i;
  $email = strtolower('student' . $i . '@college.edu');
  $phone = '9' . str_pad((string)rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
  $mess = $messNames[array_rand($messNames)];
  $block = $blocks[array_rand($blocks)];
  $religious = (rand(0, 10) > 8) ? 'Yes' : 'No';
  // diet_days 0-7 (0 means no special recurring diet)
  $diet_days = rand(0, 7);
  $no_eat_days = rand(0, 3);
  $special = (rand(0, 8) > 6) ? $specials[array_rand($specials)] : '';
  $switch = (rand(0, 10) > 8) ? 'Yes' : 'No';

  // spread dates over the last 120 days
  $d = clone $now;
  $d->sub(new DateInterval('P' . rand(0, 120) . 'D'));
  $created = $d->format('Y-m-d H:i:s');

  if ($hasCreated) {
    $stmt->bind_param('sssssssiiiss', $name, $reg, $email, $phone, $mess, $block, $religious, $special, $diet_days, $no_eat_days, $switch, $created);
  } else {
    $stmt->bind_param('sssssssiiis', $name, $reg, $email, $phone, $mess, $block, $religious, $special, $diet_days, $no_eat_days, $switch);
  }

  if (!$stmt->execute()) {
    echo "Insert failed at row $i: " . $stmt->error . "\n";
  } else {
    $count++;
  }
}

echo "Inserted $count dummy rows into mess_preferences.\n";

// Ensure at least 10 entries per mess type
$extra = 0;
$nextIndex = $i;
foreach ($messNames as $m) {
  $safe = $conn->real_escape_string($m);
  $res = $conn->query("SELECT COUNT(*) as c FROM mess_preferences WHERE mess_name = '$safe'");
  $row = $res->fetch_assoc();
  $c = (int)$row['c'];
  if ($c < $min_per_mess) {
    $need = $min_per_mess - $c;
    for ($k = 0; $k < $need; $k++) {
      $reg = sprintf('MIS%04d', 2000 + $nextIndex);
      $name = 'Student ' . ($nextIndex);
      $email = strtolower('student' . $nextIndex . '@college.edu');
      $phone = '9' . str_pad((string)rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
      $mess = $m;
      $block = $blocks[array_rand($blocks)];
      $religious = (rand(0, 10) > 8) ? 'Yes' : 'No';
      $diet_days = rand(0, 7);
      $no_eat_days = rand(0, 3);
      $special = (rand(0, 8) > 6) ? $specials[array_rand($specials)] : '';
      $switch = (rand(0, 10) > 8) ? 'Yes' : 'No';
      $d = clone $now;
      $d->sub(new DateInterval('P' . rand(0, 120) . 'D'));
      $created = $d->format('Y-m-d H:i:s');

      if ($hasCreated) {
        $stmt->bind_param('sssssssiiiss', $name, $reg, $email, $phone, $mess, $block, $religious, $special, $diet_days, $no_eat_days, $switch, $created);
      } else {
        $stmt->bind_param('sssssssiiis', $name, $reg, $email, $phone, $mess, $block, $religious, $special, $diet_days, $no_eat_days, $switch);
      }
      if (!$stmt->execute()) {
        echo "Extra insert failed for mess $m at idx $nextIndex: " . $stmt->error . "\n";
      } else {
        $extra++; $nextIndex++;
      }
    }
  }
}

// Ensure at least 10 entries per hostel block
foreach ($blocks as $b) {
  $safe = $conn->real_escape_string($b);
  $res = $conn->query("SELECT COUNT(*) as c FROM mess_preferences WHERE hostel_block = '$safe'");
  $row = $res->fetch_assoc();
  $c = (int)$row['c'];
  if ($c < $min_per_block) {
    $need = $min_per_block - $c;
    for ($k = 0; $k < $need; $k++) {
      $reg = sprintf('MIS%04d', 3000 + $nextIndex);
      $name = 'Student ' . ($nextIndex);
      $email = strtolower('student' . $nextIndex . '@college.edu');
      $phone = '9' . str_pad((string)rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
      $mess = $messNames[array_rand($messNames)];
      $block = $b;
      $religious = (rand(0, 10) > 8) ? 'Yes' : 'No';
      $diet_days = rand(0, 7);
      $no_eat_days = rand(0, 3);
      $special = (rand(0, 8) > 6) ? $specials[array_rand($specials)] : '';
      $switch = (rand(0, 10) > 8) ? 'Yes' : 'No';
      $d = clone $now;
      $d->sub(new DateInterval('P' . rand(0, 120) . 'D'));
      $created = $d->format('Y-m-d H:i:s');

      if ($hasCreated) {
        $stmt->bind_param('sssssssiiiss', $name, $reg, $email, $phone, $mess, $block, $religious, $special, $diet_days, $no_eat_days, $switch, $created);
      } else {
        $stmt->bind_param('sssssssiiis', $name, $reg, $email, $phone, $mess, $block, $religious, $special, $diet_days, $no_eat_days, $switch);
      }
      if (!$stmt->execute()) {
        echo "Extra insert failed for block $b at idx $nextIndex: " . $stmt->error . "\n";
      } else {
        $extra++; $nextIndex++;
      }
    }
  }
}

if ($extra > 0) {
  echo "Inserted an additional $extra rows to ensure >=10 per mess/block.\n";
}

// Final summary for convenience
$summary = $conn->query("SELECT mess_name, COUNT(*) as c FROM mess_preferences GROUP BY mess_name");
echo "\nFinal counts by mess type:\n";
while ($r = $summary->fetch_assoc()) {
  echo " - " . $r['mess_name'] . ": " . $r['c'] . "\n";
}

$summary2 = $conn->query("SELECT hostel_block, COUNT(*) as c FROM mess_preferences GROUP BY hostel_block ORDER BY hostel_block");
echo "\nFinal counts by hostel block:\n";
while ($r = $summary2->fetch_assoc()) {
  echo " - " . $r['hostel_block'] . ": " . $r['c'] . "\n";
}

$stmt->close();
$conn->close();

?>
