<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = ""; // or your MySQL password
$db   = "hostel_food_waste";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name       = $conn->real_escape_string($_POST['student_name']);
    $registration_number = $conn->real_escape_string($_POST['registration_number']);
    $college_email      = $conn->real_escape_string($_POST['college_email']);
    $phone              = $conn->real_escape_string($_POST['phone']);
    // form now sends 'mess_type' (maps to DB column mess_name)
    $mess_name          = $conn->real_escape_string(isset($_POST['mess_type']) ? $_POST['mess_type'] : $_POST['mess_name']);
    $hostel_block       = $conn->real_escape_string($_POST['hostel_block']);
    $religious_event    = $conn->real_escape_string($_POST['religious_event']);
    $special_diet       = $conn->real_escape_string($_POST['special_diet']);
    $diet_days          = intval($_POST['diet_days']);
    $no_eat_days        = intval($_POST['no_eat_days']);
    $switch_mess        = $conn->real_escape_string($_POST['switch_mess']);

    $sql = "INSERT INTO mess_preferences 
        (student_name, registration_number, college_email, phone, mess_name, hostel_block, religious_event, special_diet, diet_days, no_eat_days, switch_mess) 
        VALUES 
        ('$student_name', '$registration_number', '$college_email', '$phone', '$mess_name', '$hostel_block', '$religious_event', '$special_diet', $diet_days, $no_eat_days, '$switch_mess')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Form submitted successfully!'); window.location='index.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!-- tsparticles container (background effect) -->
<div id="tsparticles"></div>

<!-- tsparticles script -->
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

