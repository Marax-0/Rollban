<?php
include '../configdb.php';
session_start();

$conn = getDbConnection();

$status = isset($_SESSION['status']);
$message = isset($_SESSION['message']);

if ($status === 'success') {
  echo '
    <div class="bg-[#1eb510] rounded-md px-6 py-2 w-40">
      <span class="text-white font-medium">' . htmlspecialchars($message) . '</span>
    </div>';
} else if ($status === 'error') {
  echo '
    <div class="bg-[#cc0606] rounded-md px-6 py-2 w-40">
      <span class="text-white font-medium">' . htmlspecialchars($message) . '</span>
    </div>';
} else {
  echo '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Setting</title>
  <link rel="icon" href="./src/image/logo_hospitol.png" />
  <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
  @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&display=swap');

  * {
    font-family: "IBM Plex Sans Thai", serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  
  /* สไตล์สำหรับ multiple select */
  select[multiple] {
    scrollbar-width: thin;
    scrollbar-color: #9ca3af #f3f4f6;
  }
  
  select[multiple]::-webkit-scrollbar {
    width: 8px;
  }
  
  select[multiple]::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 4px;
  }
  
  select[multiple]::-webkit-scrollbar-thumb {
    background: #9ca3af;
    border-radius: 4px;
  }
  
  select[multiple]::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
  }
  
  select[multiple] option:checked {
    background: linear-gradient(0deg, #3b82f6 0%, #3b82f6 100%);
    color: white;
  }
  
  select[multiple] option:hover {
    background: #e5e7eb;
  }
  
  /* Smooth transitions */
  * {
    transition: all 0.2s ease-in-out;
  }
  
  /* Hover effects for cards */
  .bg-gray-50:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  }
  
  /* Focus states */
  input:focus, select:focus {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.1), 0 4px 6px -2px rgba(59, 130, 246, 0.05);
  }
</style>

<body class="bg-[#f0f0f0] p-6">
  <!-- Header -->
  <header class="bg-white shadow-sm border-b border-gray-200 mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center gap-3">
          <img src="./src/image/logo_hospitol.png" alt="Logo" class="w-10 h-10" />
          <h1 class="text-[#020f24] font-semibold text-xl">การตั้งค่า | Setting</h1>
        </div>
        <nav class="hidden md:flex items-center gap-6">
          <a href="./index.php" class="text-gray-700 hover:text-[#011d4a] px-3 py-2 rounded-md text-sm font-medium transition-colors">หน้าแรก</a>
          <a href="./manage.php" class="text-gray-700 hover:text-[#011d4a] px-3 py-2 rounded-md text-sm font-medium transition-colors">การจัดการ</a>
          <a href="./contact.php" class="text-gray-700 hover:text-[#011d4a] px-3 py-2 rounded-md text-sm font-medium transition-colors">ติดต่อเจ้าหน้าที่</a>
          <a href="./ads.php" class="text-gray-700 hover:text-[#011d4a] px-3 py-2 rounded-md text-sm font-medium transition-colors">โฆษณา</a>
        </nav>
        <!-- Mobile menu button -->
        <button class="md:hidden p-2 rounded-md text-gray-700 hover:text-[#011d4a] hover:bg-gray-100" id="mobile-menu-button">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
      </div>
    </div>
    <!-- Mobile menu -->
    <div class="md:hidden hidden" id="mobile-menu">
      <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
        <a href="./index.php" class="block px-3 py-2 text-gray-700 hover:text-[#011d4a] rounded-md text-base font-medium">หน้าแรก</a>
        <a href="./manage.php" class="block px-3 py-2 text-gray-700 hover:text-[#011d4a] rounded-md text-base font-medium">การจัดการ</a>
        <a href="./contact.php" class="block px-3 py-2 text-gray-700 hover:text-[#011d4a] rounded-md text-base font-medium">ติดต่อเจ้าหน้าที่</a>
        <a href="./ads.php" class="block px-3 py-2 text-gray-700 hover:text-[#011d4a] rounded-md text-base font-medium">โฆษณา</a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 lg:p-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">ตั้งค่าหน้าจอ</h1>
        <p class="text-gray-600">จัดการการตั้งค่าหน้าจอและระบบต่างๆ</p>
      </div>

      <div>
        <h1 class="text-2xl font-bold">ตั้งค่าหน้าจอ</h1>
        <div class="mt-10">
          <form class="space-y-8" action="./change.php" method="POST">
            <!-- Monitor Selection -->
            <div class="bg-gray-50 rounded-xl p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">เลือกหน้าจอ</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="monitor" class="block mb-2 text-sm font-medium text-gray-900">ตำแหน่งจอ</label>
                  <select id="typeMonitor" name="typeMonitor"
                    class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3">
                    <option selected>โปรดเลือกหน้าจอ</option>

              <?php
              try {
                $stmt = $conn->prepare("SELECT id, department FROM setting WHERE CAST(id AS INT) BETWEEN :start AND :end ORDER BY CAST(id AS INT)");
                $stmt->execute(['start' => 201, 'end' => 223]);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo '<option style="text-align: start;" value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['id']) . ' | ' . htmlspecialchars($row['department'] ?? 'ไม่มีการตั้งค่าแผนก') . '</option>';
                }
              } catch (PDOException $e) {
                echo '<option disabled>เกิดข้อผิดพลาดในการโหลดข้อมูล</option>';
              }
              ?>
                </select>
                                  <div>
                    <label for="typeSide" class="block mb-2 text-sm font-medium text-gray-900">ประเภทหน้าจอ</label>
                    <select id="typeSide" name="typeSide"
                      class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3">
                      <option selected>ประเภทหน้าจอ</option>
                      <option value="1">หน้าจอที่ 1</option>
                      <option value="2">หน้าจอที่ 2</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <!-- Additional Monitor Selection (แสดงเมื่อเลือก monitor แรกแล้ว) -->
              <div id="additionalMonitorSection" class="hidden mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-800 mb-4">เลือกหน้าจอเพิ่มเติม</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="additionalMonitor" class="block mb-2 text-sm font-medium text-gray-700">หน้าจอที่ 2</label>
                    <select id="additionalMonitor" name="additionalMonitor"
                      class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3">
                      <option selected>เลือกหน้าจอที่ 2</option>
                      <?php
                      try {
                        $stmt = $conn->prepare("SELECT id, department FROM setting WHERE CAST(id AS INT) BETWEEN :start AND :end ORDER BY CAST(id AS INT)");
                        $stmt->execute(['start' => 201, 'end' => 223]);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['id']) . ' | ' . htmlspecialchars($row['department'] ?? 'ไม่มีการตั้งค่าแผนก') . '</option>';
                        }
                      } catch (PDOException $e) {
                        echo '<option disabled>เกิดข้อผิดพลาดในการโหลดข้อมูล</option>';
                      }
                      ?>
                    </select>
                  </div>
                  <div>
                    <label for="additionalMonitorType" class="block mb-2 text-sm font-medium text-gray-700">ประเภทหน้าจอที่ 2</label>
                    <select id="additionalMonitorType" name="additionalMonitorType"
                      class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3">
                      <option selected>ประเภทหน้าจอ</option>
                      <option value="1">หน้าจอที่ 1</option>
                      <option value="2">หน้าจอที่ 2</option>
                    </select>
                  </div>
                </div>
              </div>



              <div class="flex gap-6 w-full items-center justify-center">

              </div>
            <!-- Header Section -->
            <div class="bg-gray-50 rounded-xl p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-blue-500 rounded-full"></span>
                ส่วนหัว
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="n_hospital" class="block text-sm font-medium text-gray-700 mb-2">ชื่อโรงพยาบาล</label>
                  <input type="text" id="n_hospital" name="n_hospital"
                    class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3"
                    placeholder="ชื่อโรงพยาบาล" />
                </div>
                <div>
                  <label for="n_department" class="block text-sm font-medium text-gray-700 mb-2">ชื่อแผนก</label>
                  <input type="text" id="n_department" name="n_department"
                    class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3"
                    placeholder="ชื่อแผนก" />
                </div>
                <div>
                  <label for="head_left" class="block text-sm font-medium text-gray-700 mb-2">ชื่อหัวตาราง (ฝั่งซ้าย)</label>
                  <input type="text" id="head_left" name="head_left"
                    class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3"
                    placeholder="ชื่อหัวตาราง (ฝั่งซ้าย)" />
                </div>
                <div>
                  <label for="head_right" class="block text-sm font-medium text-gray-700 mb-2">ชื่อหัวตาราง (ฝั่งขวา)</label>
                  <input type="text" id="head_right" name="head_right"
                    class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3"
                    placeholder="ชื่อหัวตาราง (ฝั่งขวา)" />
                </div>
              </div>
            </div>
            <!-- Table Section -->
            <div class="bg-gray-50 rounded-xl p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-green-500 rounded-full"></span>
                ส่วนตาราง
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                  <label for="amount_left" class="block text-sm font-medium text-gray-700 mb-2">จำนวนห้อง (ซ้าย)</label>
                  <input type="number" id="amount_left" name="amount_left"
                    class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3"
                    placeholder="จำนวนห้อง (ซ้าย)" />
                </div>
                <div>
                  <label for="amount_right" class="block text-sm font-medium text-gray-700 mb-2">จำนวนห้อง (ขวา)</label>
                  <input type="number" id="amount_right" name="amount_right"
                    class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3"
                    placeholder="จำนวนห้อง (ขวา)" />
                </div>
              </div>

              <!-- Room Names Section -->
              <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center w-full mb-4">
                  <div class="border border-gray-300 flex-grow"></div>
                  <div class="px-3 text-gray-500 text-sm font-medium">แก้ไขชื่อห้อง</div>
                  <div class="border border-gray-300 flex-grow"></div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                  <div id="inputLeft" class="space-y-3"></div>
                  <div id="inputRight" class="space-y-3"></div>
                </div>
              </div>
              <!-- Checkboxes Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="time_col" type="checkbox" value="true" name="time_col"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="time_col" class="ml-3 text-sm font-medium text-gray-900">เปิดแถว เวลาที่รอ</label>
                </div>

                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="all_queue" type="checkbox" value="true" name="all_queue"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="all_queue" class="ml-3 text-sm font-medium text-gray-900">เปิดการรวมคิว</label>
                </div>


                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="lock_position" type="checkbox" value="true" name="lock_position"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="lock_position" class="ml-3 text-sm font-medium text-gray-900">ล็อคตำแหน่งห้อง (ซ้าย)</label>
                </div>

                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="lock_position_right" type="checkbox" value="true" name="lock_position_right"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="lock_position_right" class="ml-3 text-sm font-medium text-gray-900">ล็อคตำแหน่งห้อง (ขวา)</label>
                </div>
                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="arr_l" type="checkbox" value="true" name="arr_l"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="arr_l" class="ml-3 text-sm font-medium text-gray-900">เรียงอันดับล่าสุด (ซ้าย)</label>
                </div>

                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="arr_r" type="checkbox" value="true" name="arr_r"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="arr_r" class="ml-3 text-sm font-medium text-gray-900">เรียงอันดับล่าสุด (ขวา)</label>
                </div>
                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="stem_surname" type="checkbox" value="true" name="stem_surname"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="stem_surname" class="ml-3 text-sm font-medium text-gray-900">ปิดนามสกุลห้อง</label>
                </div>

                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="stem_surname_table" type="checkbox" value="true" name="stem_surname_table"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="stem_surname_table" class="ml-3 text-sm font-medium text-gray-900">ปิดนามสกุลตาราง</label>
                </div>

                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="stem_surname_popup" type="checkbox" value="true" name="stem_surname_popup"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="stem_surname_popup" class="ml-3 text-sm font-medium text-gray-900">ปิดนามสกุลป๊อบอัพ</label>
                </div>



                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="stem_name" type="checkbox" value="true" name="stem_name"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="stem_name" class="ml-3 text-sm font-medium text-gray-900">ปิดชื่อห้อง</label>
                </div>

                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="stem_name_table" type="checkbox" value="true" name="stem_name_table"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="stem_name_table" class="ml-3 text-sm font-medium text-gray-900">ปิดชื่อตาราง</label>
                </div>

                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="stem_name_popup" type="checkbox" value="true" name="stem_name_popup"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="stem_name_popup" class="ml-3 text-sm font-medium text-gray-900">ปิดชื่อป๊อบอัพ</label>
                </div>

                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="urgent_color" type="checkbox" value="true" name="urgent_color"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="urgent_color" class="ml-3 text-sm font-medium text-gray-900">สีแสดงความเร่งด่วน</label>
                </div>

                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="urgent_level" type="checkbox" value="true" name="urgent_level"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="urgent_level" class="ml-3 text-sm font-medium text-gray-900">แสดงความเร่งด่วน</label>
                </div>

                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="status_patient" type="checkbox" value="true" name="status_patient"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="status_patient" class="ml-3 text-sm font-medium text-gray-900">สถานะผู้ป่วย</label>
                </div>

                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="status_check" type="checkbox" value="true" name="status_check"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="status_check" class="ml-3 text-sm font-medium text-gray-900">รูปแบบ รอการตรวจสอบ</label>
                </div>
              </div>
            </div>

            <!-- Sound Section -->
            <div class="bg-gray-50 rounded-xl p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-purple-500 rounded-full"></span>
                เสียง
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="set_descrip" type="checkbox" value="true" name="sound_option"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="set_descrip" class="ml-3 text-sm font-medium text-gray-900">Description</label>
                </div>
                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="set_notice" type="checkbox" value="true" name="sound_option"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                  <label for="set_notice" class="ml-3 text-sm font-medium text-gray-900">Notice</label>
                </div>
                <div>
                  <label for="time_wait" class="block text-sm font-medium text-gray-700 mb-2">ตั้งการ Limit (นาที)</label>
                  <input type="number" id="time_wait" name="time_wait"
                    class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3"
                    placeholder="นาที" />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="a_sound" type="radio" value="true" name="sound_option"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                  <label for="a_sound" class="ml-3 text-sm font-medium text-gray-900">คิว ชื่อ นามสกุล</label>
                </div>

                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="b_sound" type="radio" value="true" name="sound_option"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                  <label for="b_sound" class="ml-3 text-sm font-medium text-gray-900">คิว ชื่อ</label>
                </div>

                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                  <input id="c_sound" type="radio" value="true" name="sound_option"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                  <label for="c_sound" class="ml-3 text-sm font-medium text-gray-900">คิว</label>
                </div>
              </div>
            </div>

            <!-- Additional Settings -->
            <div class="bg-gray-50 rounded-xl p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-orange-500 rounded-full"></span>
                การตั้งค่าเพิ่มเติม
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                  <label for="urgent_setup" class="block text-sm font-medium text-gray-700 mb-2">ระดับกลุ่มผู้รับบริการ</label>
                  <p class="text-xs text-gray-500 mb-2">รูปแบบ เช่น ฉุกเฉิน,เร่งด่วน,ค่อนข้างเร่งด่วน... ตามระดับ A - E</p>
                  <input type="text" id="urgent_setup" name="urgent_setup"
                    class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3"
                    placeholder="A , B , C , D , E" />
                </div>
              </div>
            </div>


            <!-- Form Query Section -->
            <div class="bg-gray-50 rounded-xl p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-indigo-500 rounded-full"></span>
                Form Query
              </h2>
              <p class="text-sm text-gray-600 mb-4">รูปแบบการคิวรี่ เช่น ห้องตรวจ 1,ห้องตรวจ 2</p>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="query_left" class="block text-sm font-medium text-gray-700 mb-2">ข้อมูลฝั่งซ้าย</label>
                  <select id="query_left" name="query_left[]" multiple
                    class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3 min-h-[120px]"
                    size="5">
                    <?php
                    try {
                      $stmt = $conn->prepare("SELECT code, name FROM department ORDER BY code");
                      $stmt->execute();
                      
                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . htmlspecialchars($row['code']) . '">' . htmlspecialchars($row['code']) . ' | ' . htmlspecialchars($row['name']) . '</option>';
                      }
                    } catch (PDOException $e) {
                      echo '<option disabled>เกิดข้อผิดพลาดในการโหลดข้อมูล</option>';
                    }
                    ?>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">กด Ctrl+Click เพื่อเลือกหลายตัวเลือก</p>
                </div>
                
                <div>
                  <label for="query_right" class="block text-sm font-medium text-gray-700 mb-2">ข้อมูลฝั่งขวา</label>
                  <select id="query_right" name="query_right[]" multiple
                    class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3 min-h-[120px]"
                    size="5">
                    <?php
                    try {
                      $stmt = $conn->prepare("SELECT code, name FROM department ORDER BY code");
                      $stmt->execute();
                      
                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . htmlspecialchars($row['code']) . '">' . htmlspecialchars($row['code']) . ' | ' . htmlspecialchars($row['name']) . '</option>';
                      }
                    } catch (PDOException $e) {
                      echo '<option disabled>เกิดข้อผิดพลาดในการโหลดข้อมูล</option>';
                    }
                    ?>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">กด Ctrl+Click เพื่อเลือกหลายตัวเลือก</p>
                </div>
              </div>
              
              <!-- แสดงค่าที่เลือก -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                  <label for="query_left_display" class="block text-sm font-medium text-gray-700 mb-2">ค่าที่เลือก (ฝั่งซ้าย):</label>
                  <input type="text" id="query_left_display" readonly
                    class="w-full bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg p-3"
                    placeholder="ค่าที่เลือกจะแสดงที่นี่" />
                </div>
                <div>
                  <label for="query_right_display" class="block text-sm font-medium text-gray-700 mb-2">ค่าที่เลือก (ฝั่งขวา):</label>
                  <input type="text" id="query_right_display" readonly
                    class="w-full bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg p-3"
                    placeholder="ค่าที่เลือกจะแสดงที่นี่" />
                </div>
              </div>
              
              <!-- ปุ่มควบคุม -->
              <div class="flex gap-2 mt-4">
                <button type="button" onclick="updateQueryDisplay()" 
                  class="px-3 py-2 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                  🔄 อัปเดตการแสดงผล
                </button>
                <button type="button" onclick="clearQuerySelection()" 
                  class="px-3 py-2 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                  🗑️ ล้างการเลือก
                </button>
              </div>
            </div>
            <!-- ADS Section -->
            <div class="bg-gray-50 rounded-xl p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-2 h-6 bg-pink-500 rounded-full"></span>
                ADS
              </h2>
              <p class="text-sm text-gray-600 mb-4">ที่อยู่ของรูปภาพ</p>
              <div class="w-full">
                <input type="text" id="ads" name="ads"
                  class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3"
                  placeholder="ลิงค์หรือ ที่อยู่ของรูปภาพ" />
              </div>
            </div>

            </div>



            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t border-gray-200">
              <button type="submit" id="submit"
                class="bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 text-white font-medium rounded-lg text-sm px-8 py-3 transition-colors duration-200">
                บันทึกการตั้งค่า
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
      const mobileMenu = document.getElementById('mobile-menu');
      mobileMenu.classList.toggle('hidden');
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
      const mobileMenu = document.getElementById('mobile-menu');
      const mobileMenuButton = document.getElementById('mobile-menu-button');
      
      if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
        mobileMenu.classList.add('hidden');
      }
    });

    // Close mobile menu when window is resized to desktop
    window.addEventListener('resize', function() {
      if (window.innerWidth >= 768) { // md breakpoint
        document.getElementById('mobile-menu').classList.add('hidden');
      }
    });

    // เพิ่ม event listeners สำหรับ Form Query
    document.addEventListener('DOMContentLoaded', function() {
      const queryLeftSelect = document.getElementById('query_left');
      const queryRightSelect = document.getElementById('query_right');
      
      if (queryLeftSelect) {
        queryLeftSelect.addEventListener('change', updateQueryDisplay);
      }
      if (queryRightSelect) {
        queryRightSelect.addEventListener('change', updateQueryDisplay);
      }
      
      // อัปเดตการแสดงผลครั้งแรก
      updateQueryDisplay();
    });

    // Function สำหรับอัปเดตการแสดงผล Form Query
    function updateQueryDisplay() {
      const queryLeftSelect = document.getElementById('query_left');
      const queryRightSelect = document.getElementById('query_right');
      
      if (queryLeftSelect && queryRightSelect) {
        const selectedLeftOptions = Array.from(queryLeftSelect.selectedOptions);
        const selectedRightOptions = Array.from(queryRightSelect.selectedOptions);
        
        // แยก code ออกมา (รูปแบบ: "1 | ชื่อแผนก" -> "1")
        const leftCodes = selectedLeftOptions.map(option => option.value);
        const rightCodes = selectedRightOptions.map(option => option.value);
        
        // แสดงผลในรูปแบบ "1,2,3,4"
        const leftDisplay = leftCodes.join(',') || 'ไม่มีการเลือก';
        const rightDisplay = rightCodes.join(',') || 'ไม่มีการเลือก';
        
        document.getElementById('query_left_display').value = leftDisplay;
        document.getElementById('query_right_display').value = rightDisplay;
        
        console.log('Selected Left Codes:', leftCodes);
        console.log('Selected Right Codes:', rightCodes);
      }
    }
    
    // Function สำหรับล้างการเลือก Form Query
    function clearQuerySelection() {
      const queryLeftSelect = document.getElementById('query_left');
      const queryRightSelect = document.getElementById('query_right');
      
      if (queryLeftSelect && queryRightSelect) {
        queryLeftSelect.selectedIndex = -1;
        queryRightSelect.selectedIndex = -1;
        updateQueryDisplay();
        
        // แสดง SweetAlert2
        Swal.fire({
          icon: 'success',
          title: 'ล้างการเลือกแล้ว!',
          text: 'ได้ล้างการเลือกทั้งหมดใน Form Query',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
      }
    }

    document.getElementById('typeMonitor').addEventListener("change", function() {
      const monitorId = this.value;
      const additionalSection = document.getElementById('additionalMonitorSection');

      // แสดง/ซ่อน additional monitor section
      if (monitorId !== 'โปรดเลือกหน้าจอ') {
        additionalSection.classList.remove('hidden');
        additionalSection.classList.add('block');
      } else {
        additionalSection.classList.add('hidden');
        additionalSection.classList.remove('block');
      }

      if (monitorId !== 'โปรดเลือกหน้าจอ') {
        fetch("./datasetting.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `monitorId=${monitorId}`,
          })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              document.getElementById('n_hospital').value = data.hospital;
              document.getElementById('n_department').value = data.department;
              document.getElementById('head_left').value = data.head_left;
              document.getElementById('head_right').value = data.head_right;
              
              // โหลดข้อมูล additional monitor ถ้ามี
              if (data.additionalMonitor) {
                document.getElementById('additionalMonitor').value = data.additionalMonitor;
                document.getElementById('additionalMonitorType').value = data.additionalMonitorType || '1';
                // แสดง additional section
                document.getElementById('additionalMonitorSection').classList.remove('hidden');
                document.getElementById('additionalMonitorSection').classList.add('block');
              }
              // document.getElementById('list_left').value = data.list_left;
              // document.getElementById('list_right').value = data.list_right;
              document.getElementById('amount_left').value = data.amount_left;
              document.getElementById('amount_right').value = data.amount_right;
              // แยกค่าจาก query_left และ query_right ที่เป็น string ให้เป็น array
              console.log('Data received:', data);
              console.log('query_left (department_load):', data.query_left);
              console.log('query_right (department_room_load):', data.query_right);
              
              const queryLeftArray = data.query_left ? data.query_left.split(',').map(code => code.trim()) : [];
              const queryRightArray = data.query_right ? data.query_right.split(',').map(code => code.trim()) : [];
              
              // เลือก option ที่ตรงกับค่าในฐานข้อมูล
              const queryLeftSelect = document.getElementById('query_left');
              const queryRightSelect = document.getElementById('query_right');
              
              // เลือก option ในฝั่งซ้าย
              Array.from(queryLeftSelect.options).forEach(option => {
                if (queryLeftArray.includes(option.value)) {
                  option.selected = true;
                }
              });
              
              // เลือก option ในฝั่งขวา
              Array.from(queryRightSelect.options).forEach(option => {
                if (queryRightArray.includes(option.value)) {
                  option.selected = true;
                }
              });
              
              // อัปเดตการแสดงผล
              updateQueryDisplay();
              document.getElementById('ads').value = data.ads;
              document.getElementById('urgent_setup').value = data.urgent_setup;
              document.getElementById('time_wait').value = data.time_wait;

              document.getElementById('arr_l').checked = data.arr_l === "true";
              document.getElementById('arr_r').checked = data.arr_r === "true";
              document.getElementById('time_col').checked = data.time_col === "true";
              document.getElementById('stem_surname').checked = data.stem_surname === "true";
              document.getElementById('set_descrip').checked = data.set_descrip === "true";
              document.getElementById('set_notice').checked = data.set_notice === "true";
              document.getElementById('stem_name').checked = data.stem_surname === "name";
              document.getElementById('stem_surname_table').checked = data.stem_surname_table === "true";
              document.getElementById('stem_name_table').checked = data.stem_surname_table === "name";
              document.getElementById('stem_surname_popup').checked = data.stem_surname_popup === "true";
              document.getElementById('status_patient').checked = data.status_patient === "true";
              document.getElementById('status_check').checked = data.status_check === "true";
              document.getElementById('lock_position').checked = data.lock_position === "true";
              document.getElementById('all_queue').checked = data.lock_position === "all_queue";
              document.getElementById('lock_position_right').checked = data.lock_position_right === "true";
              document.getElementById('urgent_color').checked = data.urgent_color === "true";
              document.getElementById('stem_name_popup').checked = data.stem_surname_popup === "name";
              document.getElementById('urgent_level').checked = data.urgent_level === "true";
              document.getElementById('a_sound').checked = data.a_sound === "true";
              document.getElementById('b_sound').checked = data.b_sound === "true";
              document.getElementById('c_sound').checked = data.c_sound === "true";

              document.getElementById('set_descrip').checked = data.set_descrip === "true";
              document.getElementById('set_notice').checked = data.set_notice === "true";
              // ✅ โหลดค่าจาก data.lock_position มาแสดงใน checkbox
              document.getElementById('lock_position').checked = data.lock_position === "true";
              document.getElementById('all_queue').checked = data.lock_position === "all_queue";

              document.getElementById('stem_name_popup').addEventListener('change', function() {
                if (this.checked) {
                  document.getElementById('stem_surname_popup').checked = false;
                }
              });

              document.getElementById('stem_surname_popup').addEventListener('change', function() {
                if (this.checked) {
                  document.getElementById('stem_name_popup').checked = false;
                }
              });

              document.getElementById('stem_surname_table').addEventListener('change', function() {
                if (this.checked) {
                  document.getElementById('stem_name_table').checked = false;
                }
              });
              document.getElementById('stem_name_table').addEventListener('change', function() {
                if (this.checked) {
                  document.getElementById('stem_surname_table').checked = false;
                }
              });

              document.getElementById('stem_name').addEventListener('change', function() {
                if (this.checked) {
                  document.getElementById('stem_surname').checked = false;
                }
              });
              document.getElementById('stem_surname').addEventListener('change', function() {
                if (this.checked) {
                  document.getElementById('stem_name').checked = false;
                }
              });


              // ===============สร้า่ง input แบบไดนามิก================
              function createinput(container, amount, prefix, name, values) {
                container.innerHTML = "";
                for (let i = 0; i < amount; i++) {
                  const input = document.createElement("input");
                  input.type = "text";
                  input.name = `${prefix}${i + 1}`;
                  input.id = `${prefix}${i + 1}`;
                  input.placeholder = `${name} ${i + 1}`;
                  input.className = "bg-gray-50 border text-start rounded-lg border-gray-300 w-full";
                  input.value = values && values[i] ? values[i] : "";
                  input.addEventListener('input', mergeInputValues);
                  container.appendChild(input);
                }
              }

              createinput(document.getElementById('inputLeft'), data.amount_left, "station_left", "ชื่อห้องที่ ", data.station_left);
              createinput(document.getElementById('inputRight'), data.amount_right, "station_right", "ชื่อห้องที่ ", data.station_right);

              document.getElementById('amount_left').addEventListener("input", function() {
                const newAmount = parseInt(this.value, 10) || 0;
                const newValues = data.station_left.slice(0, newAmount);
                createinput(document.getElementById('inputLeft'), newAmount, "station_left", "ชื่อห้องที่ ", newValues);
                mergeInputValues();
              });
              document.getElementById('amount_right').addEventListener("input", function() {
                const newAmount = parseInt(this.value, 10) || 0;
                const newValues = data.station_left.slice(0, newAmount);
                createinput(document.getElementById('inputRight'), newAmount, "station_right", "ชื่อห้องที่ ", newValues);
                mergeInputValues();
              });

              let mergeInputLeft = '';
              let mergeInputRight = '';

              function mergeInputValues() {
                mergeInputLeft = '';
                mergeInputRight = '';

                const inputLeftFields = document.querySelectorAll('#inputLeft input');
                inputLeftFields.forEach(input => {
                  if (input.value) {
                    mergeInputLeft += input.value + ',';
                  }
                });

                const inputRightFields = document.querySelectorAll('#inputRight input');
                inputRightFields.forEach(input => {
                  if (input.value) {
                    mergeInputRight += input.value + ',';
                  }
                });

                mergeInputLeft = mergeInputLeft.replace(/,$/, '');
                mergeInputRight = mergeInputRight.replace(/,$/, '');

                console.log('Merged Values Left:', mergeInputLeft);
                console.log('Merged Values Right:', mergeInputRight);
              }

              // ฟังก์ชันสำหรับอัปเดตการแสดงผลของ query dropdowns
              function updateQueryDisplay() {
                const queryLeftSelect = document.getElementById('query_left');
                const queryRightSelect = document.getElementById('query_right');
                const queryLeftDisplay = document.getElementById('query_left_display');
                const queryRightDisplay = document.getElementById('query_right_display');
                
                // รวบรวมค่าที่เลือกจากฝั่งซ้าย
                const selectedLeftValues = [];
                Array.from(queryLeftSelect.selectedOptions).forEach(option => {
                  selectedLeftValues.push(option.value);
                });
                
                // รวบรวมค่าที่เลือกจากฝั่งขวา
                const selectedRightValues = [];
                Array.from(queryRightSelect.selectedOptions).forEach(option => {
                  selectedRightValues.push(option.value);
                });
                
                // แสดงผลในรูปแบบ 1,2,3
                queryLeftDisplay.value = selectedLeftValues.join(',');
                queryRightDisplay.value = selectedRightValues.join(',');
                
                console.log('Selected Left:', selectedLeftValues.join(','));
                console.log('Selected Right:', selectedRightValues.join(','));
              }

                            mergeInputValues();
              
              // เพิ่ม event listener สำหรับ query dropdowns
              document.getElementById('query_left').addEventListener('change', updateQueryDisplay);
              document.getElementById('query_right').addEventListener('change', updateQueryDisplay);
              
              console.log('Data:', monitorId);

              function sendMergedValues() {
                mergeInputValues();
                const formData = new URLSearchParams();

                formData.append('ads', document.getElementById('ads').value);
                formData.append('type', document.getElementById('typeSide').value);
                formData.append('typeMonitor', document.getElementById('typeMonitor').value);
                formData.append('additionalMonitor', document.getElementById('additionalMonitor').value);
                formData.append('additionalMonitorType', document.getElementById('additionalMonitorType').value);
                formData.append('n_hospital', document.getElementById('n_hospital').value);
                formData.append('n_department', document.getElementById('n_department').value);
                formData.append('head_left', document.getElementById('head_left').value);
                formData.append('head_right', document.getElementById('head_right').value);
                formData.append('urgent_setup', document.getElementById('urgent_setup').value);
                formData.append('time_wait', document.getElementById('time_wait').value);
                // formData.append('voice', document.getElementById('voiceSelect').value);
                // formData.append('style_voice', document.getElementById('styleSelect').value);
                // formData.append('list_left', document.getElementById('list_left').value);
                // formData.append('list_right', document.getElementById('list_right').value);
                formData.append('amount_left', document.getElementById('amount_left').value);
                formData.append('amount_right', document.getElementById('amount_right').value);
                formData.append('arr_l', document.getElementById('arr_l').checked ? 'true' : 'false');
                formData.append('arr_r', document.getElementById('arr_r').checked ? 'true' : 'false');
                formData.append('set_descrip', document.getElementById('set_descrip').checked ? 'true' : 'false');
                formData.append('set_notice', document.getElementById('set_notice').checked ? 'true' : 'false');
                // formData.append('stem_surname', document.getElementById('stem_surname').checked);
                formData.append('stem_surname', document.getElementById('stem_name').checked ? "name" : (document.getElementById('stem_surname').checked ? "true" : "false"));
                formData.append('stem_surname_popup', document.getElementById('stem_name_popup').checked ? "name" : (document.getElementById('stem_surname_popup').checked ? "true" : "false"));
                formData.append('stem_surname_table', document.getElementById('stem_name_table').checked ? "name" : (document.getElementById('stem_surname_table').checked ? "true" : "false"));
                // formData.append('stem_name', document.getElementById('stem_name').checked);
                formData.append('urgent_color', document.getElementById('urgent_color').checked);
                formData.append('status_patient', document.getElementById('status_patient').checked);
                formData.append('status_check', document.getElementById('status_check').checked);
                formData.append('lock_position',
                  document.getElementById('all_queue').checked ?
                  "all_queue" :
                  document.getElementById('lock_position').checked ?
                  "true" :
                  "false"
                );
                formData.append('lock_position_right', document.getElementById('lock_position_right').checked);
                formData.append('urgent_level', document.getElementById('urgent_level').checked);
                formData.append('a_sound', document.getElementById('a_sound').checked);
                formData.append('b_sound', document.getElementById('b_sound').checked);
                formData.append('c_sound', document.getElementById('c_sound').checked);
                formData.append('time_col', document.getElementById('time_col').checked);
                formData.append('station_left', mergeInputLeft);
                formData.append('station_right', mergeInputRight);
                // รวบรวมค่าที่เลือกจาก query dropdowns (ส่ง code แทน id)
                const queryLeftSelect = document.getElementById('query_left');
                const queryRightSelect = document.getElementById('query_right');
                
                const selectedLeftCodes = [];
                Array.from(queryLeftSelect.selectedOptions).forEach(option => {
                  selectedLeftCodes.push(option.value); // option.value คือ code
                });
                
                const selectedRightCodes = [];
                Array.from(queryRightSelect.selectedOptions).forEach(option => {
                  selectedRightCodes.push(option.value); // option.value คือ code
                });
                
                formData.append('query_left', selectedLeftCodes.join(','));
                formData.append('query_right', selectedRightCodes.join(','));
                
                console.log('Sending Left Codes (department_load):', selectedLeftCodes.join(','));
                console.log('Sending Right Codes (department_room_load):', selectedRightCodes.join(','));

                console.log('Form Data:', formData.toString());
                fetch('./change.php', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData.toString()
                  })
                  .then(response => response.text())
                  .then(data => {
                    console.log('Success:', data);
                  })
                  .catch(error => console.error('Error:', error));
              }

              document.getElementById('submit').addEventListener("click", function(event) {
                event.preventDefault();
                sendMergedValues();
              });

            } else {
              Swal.fire({
                icon: "error",
                title: "ไม่พบข้อมูลตำแหน่งจอ",
                text: "ไม่พบข้อมูลตำแหน่งจอ ลองใหม่อีกครั้ง"
              });
            }
          })
          .catch((error) => console.log("Error", error));
      }

    });
  </script>
</body>

</html>