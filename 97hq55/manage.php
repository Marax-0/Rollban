<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting</title>
    <link rel="icon" href="./src/image/logo_hospitol.png">
    <link rel="stylesheet" href="./style/main.css">
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
</style>
<body class="bg-[#f0f0f0] p-6">
    <div class="flex gap-3">
        <aside class="bg-white p-4 rounded-[20px] w-[300px] flex flex-col justify-start gap-20" style="height: calc(100vh - 50px);">
            <div class="flex flex-col items-center justify-center gap-2">
                <img src="./src/image/logo_hospitol.png" alt="" class="w-14">
                <h1 class="text-[#020f24] font-medium text-2xl">SETTING</h1>
            </div>
        
            <ul class="flex flex-col gap-4 w-full">
                <li>
                    <a href="./index.php" class="block text-gray-700  px-4 py-2 w-full hover:bg-[#f1f5f9] rounded-lg transition-colors duration-200">หน้าแรก</a>
                </li>
                <li>
                    <a href="./setting.php" class="block text-gray-700  px-4 py-2 w-full hover:bg-[#f1f5f9] rounded-lg transition-colors duration-200">การตั้งค่า</a>
                </li>
                <li>
                    <a href="" class="block rounded-xl text-white bg-[#011d4a] px-4 py-2 w-full hover:bg-[#1a2533] transition-colors duration-200">การจัดการ</a>
                </li>
                <li>
                    <a href="./contact.php" class="block text-gray-700  px-4 py-2 w-full hover:bg-[#f1f5f9] rounded-lg transition-colors duration-200">ติดต่อเจ้าหน้าที่</a>
                </li>
                <li>
                    <a href="./ads.php" class="block text-gray-700  px-4 py-2 w-full hover:bg-[#f1f5f9] rounded-lg transition-colors duration-200">โฆษณา</a>
                </li>
            </ul>
        </aside>
        
        <section class="bg-white w-full p-4 rounded-[20px]">
            <div class="flex items-center justify-end px-4 py-2">
                <h1 class="font-semibold">การจัดการ | Manage</h1>
            </div>
        <h1 class="text-center mt-20 text-2xl border py-6 rounded-2xl text-gray-400">รอการอัพเดท</h1>
        </section>
    </div>
</body>
</html>