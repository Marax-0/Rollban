# Display Monitor System

ระบบแสดงผลข้อมูลการตรวจรักษาผู้ป่วยแบบ Real-time

## โครงสร้างโปรเจค

```
src/
├── app/
│   ├── api/
│   │   ├── data/           # API สำหรับ query ข้อมูล visit
│   │   └── setting/        # API สำหรับจัดการ setting
│   └── single/[id]/        # หน้าแสดงผลข้อมูล
├── lib/
│   └── db-config.ts        # Database configuration
└── ...
```

## การติดตั้ง

1. ติดตั้ง dependencies:
```bash
npm install
```

2. สร้างไฟล์ `.env.local` และกำหนดค่า database:
```env
DB_USER=your_username
DB_PASSWORD=your_password
DB_SERVER=your_server_ip
DB_NAME=your_database_name
DB_PORT=1433
DB_TIMEOUT=30000
DB_POOL_MIN=0
DB_POOL_MAX=10
```

3. รันโปรเจค:
```bash
npm run dev
```

## API Endpoints

### 1. GET `/api/setting/[id]`
ดึงข้อมูล setting ตาม ID

### 2. PUT `/api/setting/[id]`
อัปเดตข้อมูล setting

### 3. DELETE `/api/setting/[id]`
ลบข้อมูล setting

### 4. POST `/api/data`
Query ข้อมูลจาก `monitor_visit_info` โดยใช้ `department_load` และ `visit_date`

**Request Body:**
```json
{
  "department_load": "001,002,003",
  "visit_date": "2024-01-15"
}
```

**Parameters:**
- `department_load`: รหัสแผนก (คั่นด้วย comma)
- `visit_date`: วันที่ตรวจ (รูปแบบ yyyy-mm-dd)

**Response:**
```json
{
  "success": true,
  "data": [...],
  "count": 3,
  "departmentIds": ["001", "002", "003"],
  "visit_date": "2024-01-15"
}
```

## การทำงานของระบบ

1. **หน้า Single Page** (`/single/[id]`):
   - โหลดข้อมูล setting จาก API
   - ถ้ามี `department_load` จะเรียกใช้ API ทั้งสองตัว:
     - `/api/data` - ข้อมูลทั่วไป
     - `/api/data/active` - ข้อมูลที่มี status = 'กำลัง'
   - แสดงข้อมูลในตารางและ right column

2. **API Data**:
   - `/api/data`: Query ข้อมูลทั่วไป (ไม่มีเงื่อนไข status)
   - `/api/data/active`: Query ข้อมูลที่มี `status = 'กำลัง'`
   - รับค่า `department_load` และแยกด้วย comma
   - รับค่า `visit_date` ในรูปแบบ yyyy-mm-dd
   - ส่งข้อมูลกลับมาแสดงผล

3. **การแสดงผล**:
   - **Left Column**: แสดงข้อมูลทั่วไปจาก `/api/data`
   - **Right Column**: แสดงโต๊ะบริการและข้อมูลผู้ป่วยที่กำลังรับบริการ
   - ระบบจะเช็ค `station` ในข้อมูล active ว่าตรงกับโต๊ะไหน แล้วแสดงข้อมูลผู้ป่วย

## Database Schema

### Table: `setting`
- `id`: Primary key
- `department_load`: รหัสแผนก (คั่นด้วย comma)
- `n_table`: ชื่อจุดซักประวัติ
- และอื่นๆ

### Table: `monitor_visit_info`
- `code_dept_id`: รหัสแผนก
- `patient_name`: ชื่อผู้ป่วย
- `queue_number`: หมายเลขคิว
- และอื่นๆ

## การใช้งาน

1. เข้าสู่หน้า `/single/[id]` โดย `id` คือ ID ของ setting
2. ระบบจะโหลดข้อมูลและแสดงผลในตาราง
3. ถ้ามีข้อมูลจาก `monitor_visit_info` จะแสดงในตาราง
4. ถ้าไม่มีข้อมูลจะแสดงข้อความ "ไม่มีข้อมูล"

## การพัฒนา

- ใช้ Next.js 15 + TypeScript
- Database: Microsoft SQL Server
- UI: CSS Modules + Lucide React Icons
- Responsive Design สำหรับทุกขนาดหน้าจอ
