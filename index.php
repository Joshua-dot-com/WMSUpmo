<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WMSU Equipment Management System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            wmsu: {
              red: '#B22222',
              black: '#000000',
            }
          },
          fontFamily: {
            poppins: ['Poppins', 'sans-serif']
          }
        }
      }
    };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-900">

  <!-- Navigation -->
  <nav class="bg-wmsu-red shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
      <div class="flex items-center space-x-3">
        <img src="Assets/wmsu.png" alt="WMSU Logo" class="h-10 w-10 rounded-full">
        <span class="text-white font-semibold text-xl">WMSU</span>
      </div>
      <div class="hidden md:flex items-center space-x-6">
        <a href="Login.php" class="bg-wmsu-black text-white px-4 py-2 rounded-md hover:bg-gray-800">Login</a>
      </div>
    </div>
  </nav>

  <!-- Hero -->
  <section class="relative bg-wmsu-red text-white">
    <div class="absolute inset-0 opacity-10">
      <img src="Assets/wmsu.png?height=800&width=1600" alt="Campus" class="w-full h-full object-cover">
    </div>
    <div class="relative max-w-7xl mx-auto px-4 py-24 sm:px-6 lg:px-8 text-center">
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-4">Equipment Management System</h1>
      <p class="text-lg sm:text-xl max-w-2xl mx-auto text-gray-200 mb-8">Western Mindanao State University</p>
      <div class="flex flex-col sm:flex-row justify-center gap-4">
        <a href="#" class="bg-white text-wmsu-red font-semibold px-6 py-3 rounded-md hover:bg-gray-100 transition">Get Started</a>
        <a href="#" class="bg-wmsu-black text-white font-semibold px-6 py-3 rounded-md hover:bg-gray-900 transition">Learn More</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-900 text-gray-300">
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
          <h3 class="text-white text-lg font-semibold mb-4">Contact Us</h3>
          <ul class="space-y-4 text-sm">
            <li class="flex items-center gap-2">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 0a4 4 0 00-8 0 4 4 0 008 0zm0 0v2m0 0H8m8 0v2m0 0H8" />
              </svg>
              wmsuequipment@gmail.com
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Normal Road, Baliwasan, Zamboanga City
            </li>
          </ul>
        </div>
        <div class="flex items-end justify-center md:justify-end">
          <p class="text-sm text-gray-500 text-center md:text-right">&copy; 2025 Western Mindanao State University. All rights reserved.</p>
        </div>
      </div>
    </div>
  </footer>
</body>
</html>
