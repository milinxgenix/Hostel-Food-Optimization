<?php
// Simple landing page with particle background and navigation buttons
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Hostel Mess Preferences - Home</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Ensure content sits above particles */
    #content { position: relative; z-index: 10; }
    /* small decorative corner images */
    .corner-img { position: absolute; width: 180px; opacity: 0.9; filter: drop-shadow(0 10px 26px rgba(2,6,23,0.35)); }
    .top-left { left: 12px; top: 12px; transform: rotate(-8deg); }
    .top-right { right: 12px; top: 12px; transform: rotate(6deg); }
    .bottom-left { left: 12px; bottom: 12px; transform: rotate(6deg); }
    .bottom-right { right: 12px; bottom: 12px; transform: rotate(-6deg); }
    /* Header buttons (top right) */
    .header-actions { position: fixed; right: 20px; top: 18px; z-index: 60; }
  </style>
</head>
<body class="min-h-screen relative overflow-x-hidden" style="background: linear-gradient(135deg, #fff4d9, #ffe6cc);">


  <!-- Particles container (primary) -->
  <div id="tsparticles"></div>

  <!-- Main content -->
  <div id="content" class="min-h-screen flex flex-col justify-center max-w-7xl mx-auto px-8 py-28">
    <!-- Header row: title centered, login/register at top-right -->
    <div class="relative">
  <h1 id="typing-text" class="text-6xl md:text-7xl font-extrabold text-center text-black-800"></h1>
      <div class="header-actions flex gap-3">
        <a href="login.php" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold shadow hover:bg-blue-700">Login</a>
        <a href="register.php" class="px-6 py-3 bg-white border border-blue-600 text-blue-600 rounded-lg font-semibold shadow hover:bg-blue-50">Register</a>
        <a href="dashboard.php" class="px-6 py-3 bg-yellow-500 text-white rounded-lg font-semibold shadow hover:bg-yellow-600">Dashboard</a>
      </div>
    </div>

    <!-- Decorative intro + description -->
    <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-10 items-stretch">
      <div class="bg-white/30 backdrop-blur-md rounded-2xl p-12 shadow-2xl border border-white/40 flex flex-col justify-center">
        <h2 class="text-3xl md:text-4xl font-semibold text-blue-800 mb-6">How this site reduces hostel food waste</h2>

        <!-- Small SVG infographic: Students -> Form -> DB -> Dashboard -> Less Waste -->
        <div class="mb-6 flex justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 900 140" class="w-full max-w-3xl">
            <defs>
              <linearGradient id="g1" x1="0" x2="1">
                <stop offset="0%" stop-color="#60a5fa"/>
                <stop offset="100%" stop-color="#34d399"/>
              </linearGradient>
            </defs>
            <style> .s{fill:#0b2545;font-family:Arial,sans-serif;font-size:14px;} .b{fill:#fff;stroke:#0b2545;stroke-width:2;} .icon{fill:url(#g1);} </style>

            <!-- Student -->
            <rect x="10" y="20" width="140" height="100" rx="14" class="b" fill="#f8fafc"/>
            <g transform="translate(30,50)">
              <circle cx="30" cy="10" r="18" class="icon" />
              <text x="65" y="16" class="s">Students</text>
            </g>

            <!-- Arrow 1 -->
            <path d="M160 70 L230 70" stroke="#94a3b8" stroke-width="4" fill="none" marker-end="url(#arr)" />

            <!-- Form -->
            <rect x="240" y="20" width="140" height="100" rx="14" class="b" fill="#f8fafc"/>
            <g transform="translate(260,50)">
              <rect x="0" y="-18" width="36" height="36" rx="6" fill="#ffd166" stroke="#0b2545" stroke-width="2" />
              <text x="48" y="0" class="s">Form submissions</text>
            </g>

            <!-- Arrow 2 -->
            <path d="M380 70 L450 70" stroke="#94a3b8" stroke-width="4" fill="none" marker-end="url(#arr)" />

            <!-- DB -->
            <rect x="470" y="20" width="140" height="100" rx="14" class="b" fill="#f8fafc"/>
            <g transform="translate(490,50)">
              <ellipse cx="30" cy="0" rx="28" ry="12" fill="#f472b6" stroke="#0b2545" stroke-width="1.5" />
              <text x="68" y="4" class="s">Database</text>
            </g>

            <!-- Arrow 3 -->
            <path d="M610 70 L680 70" stroke="#94a3b8" stroke-width="4" fill="none" marker-end="url(#arr)" />

            <!-- Dashboard -->
            <rect x="690" y="20" width="180" height="100" rx="14" class="b" fill="#f8fafc"/>
            <g transform="translate(710,36)">
              <rect x="0" y="0" width="28" height="28" rx="4" fill="#60a5fa" />
              <rect x="36" y="0" width="28" height="18" rx="4" fill="#34d399" />
              <text x="0" y="60" class="s">Admin Dashboard → Actionable insights → Less Waste</text>
            </g>

            <defs>
              <marker id="arr" markerWidth="10" markerHeight="10" refX="6" refY="5" orient="auto">
                <path d="M0 0 L10 5 L0 10 z" fill="#94a3b8" />
              </marker>
            </defs>
          </svg>
        </div>

        <p class="text-gray-800 mb-6 text-lg">This application collects student mess preferences and dietary requirements to help hostel kitchens plan meals more accurately and reduce leftover food. By centralizing choices and visualizing trends, the system enables data-driven decisions that cut waste, save money, and improve resident satisfaction.</p>

        <h3 class="text-xl font-semibold text-gray-800 mt-2 mb-2">Key features that drive optimization</h3>
        <ul class="list-disc list-inside text-gray-700 space-y-3 text-lg">
          <li><strong>Real-time preference capture:</strong> Students submit mess choices and special diets so kitchens know exact demand.</li>
          <li><strong>Admin analytics dashboard:</strong> Aggregated charts show popular messes, diet counts, and daily trends to guide portioning and procurement.</li>
          <li><strong>Filterable reports:</strong> Slice data by mess, block, or time period to detect local patterns and adjust menus accordingly.</li>
          <li><strong>Switch tracking:</strong> Track how many students switch messes to avoid overproduction at any outlet.</li>
        </ul>

        <h3 class="text-xl font-semibold text-gray-800 mt-4 mb-2">Operational benefits</h3>
        <ul class="list-disc list-inside text-gray-700 space-y-2 text-lg">
          <li>Reduce daily overcooking by aligning production with actual demand.</li>
          <li>Lower food procurement costs through better forecasting.</li>
          <li>Improve satisfaction by respecting dietary needs and minimizing shortages.</li>
        </ul>

        <p class="text-gray-600 mt-6 text-base">Designed for easy local deployment (XAMPP / Apache + MySQL). Admins log in to view the analysis; students use the form to submit preferences. Over time, the collected data allows you to refine menus and portion sizes to meaningfully reduce food waste.</p>
      </div>

      <div class="rounded-2xl p-8 flex flex-col items-center justify-center">
        <img src="images/1.png" alt="Decor" class="w-full md:w-[640px] rounded-2xl shadow-xl mb-8">
        <div class="bg-white/30 backdrop-blur-md rounded-2xl p-8 shadow-2xl border border-white/40 w-full">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">Quick actions</h3>
          <div class="flex flex-col gap-4">
            <a href="login.php" class="px-6 py-4 bg-blue-600 text-white rounded-lg text-center font-semibold hover:bg-blue-700">Admin Login</a>
            <a href="register.php" class="px-6 py-4 bg-white border border-blue-600 text-blue-600 rounded-lg text-center font-semibold hover:bg-blue-50">Admin Register</a>
              <a href="form.php" class="px-6 py-4 bg-green-600 text-white rounded-lg text-center font-semibold hover:bg-green-700">Open Student Form</a>
              <a href="dashboard.php" class="px-6 py-4 bg-yellow-500 text-white rounded-lg text-center font-semibold hover:bg-yellow-600">Open Dashboard</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Extra decorative images in corners -->
  <img src="images/1.png" class="corner-img top-left" alt="decor">
  <img src="images/1.png" class="corner-img bottom-left" alt="decor">
  <img src="images/1.png" class="corner-img bottom-right" alt="decor">

    <!-- Footer call-to-action: Form button at bottom center -->
    <div class="mt-20 flex justify-center">
      <a href="form.php" class="px-14 py-6 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full text-2xl font-bold shadow-2xl hover:from-green-600 hover:to-green-700">Fill the Form (Students)</a>
    </div>
  </div>

  <!-- Secondary particle container appended by script; pointer events disabled -->
  <script src="https://cdn.jsdelivr.net/npm/tsparticles@2.11.1/tsparticles.bundle.min.js"></script>
  <script>
    (function(){
      const droplet = 'data:image/svg+xml;utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"%3E%3Cpath fill="%230b2545" d="M32 2C20 16 8 28 8 40a24 24 0 0048 0C56 28 44 16 32 2z"/%3E%3C/svg%3E';

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

      if (!document.getElementById('tsparticles2')) {
        const el = document.createElement('div');
        el.id = 'tsparticles2';
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
<script>
document.addEventListener("DOMContentLoaded", function() {
  const text = "Hostel Food Optimization";
  const element = document.getElementById("typing-text");
  let index = 0;

  function type() {
    if (index < text.length) {
      element.textContent += text.charAt(index);
      index++;
      setTimeout(type, 100); // typing speed in ms
    }
  }

  type();
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
