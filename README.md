# Sistem Presensi Sekolah dengan NFC

Sistem presensi digital untuk sekolah menggunakan kartu NFC dan ESP32 dengan panel admin yang lengkap.

## Fitur Utama

### 🔐 Autentikasi Admin
- Login dengan username dan password
- Multi-level admin (Super Admin & Wali Kelas)
- Session management yang aman
- Wali kelas hanya dapat mengakses data kelasnya

### 👥 Manajemen Siswa
- Pendaftaran siswa baru
- Pengaturan ID NFC untuk setiap siswa
- Data siswa (NIS, nama, kelas)
- Aktivasi/deaktivasi siswa
- Wali kelas hanya dapat mengelola siswa di kelasnya

### ⏰ Pengaturan Presensi
- Konfigurasi jam masuk sekolah
- Pengaturan batas waktu terlambat
- Sistem hanya mencatat presensi masuk

### 📊 Dashboard & Laporan
- Dashboard real-time dengan statistik
- Grafik kehadiran 7 hari terakhir
- Laporan presensi per periode
- Filter berdasarkan kelas, tanggal, dan status

### 📱 Integrasi ESP32
- API endpoint untuk menerima data dari ESP32
- Validasi kartu NFC
- Pencatatan otomatis dengan timestamp

## Teknologi yang Digunakan

- **Backend**: Laravel 10
- **Database**: MySQL
- **Frontend**: Bootstrap 5, Chart.js
- **Hardware**: ESP32 + RFID Reader
- **Authentication**: Laravel Session

## Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd Website-Presensi
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=presensi_sekolah
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migration & Seeder
```bash
php artisan migrate
php artisan db:seed
```

### 6. Serve Application
```bash
php artisan serve
```

## Akun Default

Setelah menjalankan seeder, Anda dapat login dengan:

**Super Admin:**
- Username: `admin`
- Password: `admin123`

**Wali Kelas X IPA 1:**
- Username: `walikelas1`
- Password: `admin123`

**Wali Kelas XI IPA 1:**
- Username: `walikelas2`
- Password: `admin123`

## Struktur Database

### Tabel `admins`
- id, name, email, username, password, role, is_active, timestamps

### Tabel `students`
- id, nis, name, nfc_id, class, gender, phone, address, parent_name, parent_phone, is_active, timestamps

### Tabel `attendance_settings`
- id, entry_time, late_threshold, exit_time, is_active, timestamps

### Tabel `attendances`
- id, student_id, date, entry_time, exit_time, status, notes, nfc_id, timestamps

## API Endpoint untuk ESP32

### POST /api/attendance/record
Menerima data presensi dari ESP32

**Request Body:**
```json
{
    "nfc_id": "1234567890",
    "timestamp": "2024-01-15 07:30:00"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Presensi berhasil dicatat",
    "student": {
        "name": "Ahmad Siswa",
        "class": "XII IPA 1",
        "status": "present"
    }
}
```

**Response Error:**
```json
{
    "success": false,
    "message": "Kartu NFC tidak terdaftar atau siswa tidak aktif"
}
```

## Kode ESP32 (Arduino)

```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include <MFRC522.h>

// Konfigurasi WiFi
const char* ssid = "NAMA_WIFI";
const char* password = "PASSWORD_WIFI";

// Konfigurasi RFID
#define RST_PIN 22
#define SS_PIN 21
MFRC522 rfid(SS_PIN, RST_PIN);

// URL API
const char* apiUrl = "http://localhost:8000/api/attendance/record";

void setup() {
  Serial.begin(115200);
  
  // Inisialisasi RFID
  SPI.begin();
  rfid.PCD_Init();
  
  // Koneksi WiFi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Menghubungkan ke WiFi...");
  }
  Serial.println("Terhubung ke WiFi");
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    if (rfid.PICC_IsNewCardPresent() && rfid.PICC_ReadCardSerial()) {
      String nfcId = "";
      for (byte i = 0; i < rfid.uid.size; i++) {
        nfcId += String(rfid.uid.uidByte[i], HEX);
      }
      
      // Kirim data ke server
      sendAttendanceData(nfcId);
      
      rfid.PICC_HaltA();
      delay(2000);
    }
  }
  delay(100);
}

void sendAttendanceData(String nfcId) {
  HTTPClient http;
  http.begin(apiUrl);
  http.addHeader("Content-Type", "application/json");
  
  String timestamp = getCurrentTimestamp();
  String jsonData = "{\"nfc_id\":\"" + nfcId + "\",\"timestamp\":\"" + timestamp + "\"}";
  
  int httpResponseCode = http.POST(jsonData);
  
  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println("Response: " + response);
  } else {
    Serial.println("Error: " + http.errorToString(httpResponseCode));
  }
  
  http.end();
}

String getCurrentTimestamp() {
  // Implementasi timestamp sesuai kebutuhan
  // Bisa menggunakan RTC atau NTP
  return "2024-01-15 07:30:00";
}
```

## Fitur Tambahan yang Bisa Dikembangkan

1. **Export Data**: Export laporan ke Excel/PDF
2. **Notifikasi**: Email/SMS untuk orang tua
3. **Mobile App**: Aplikasi mobile untuk monitoring
4. **Multi-sekolah**: Support untuk multiple sekolah
5. **Backup Otomatis**: Backup database otomatis
6. **API Documentation**: Dokumentasi API yang lengkap

## Kontribusi

Silakan berkontribusi dengan:
1. Fork repository
2. Buat branch fitur baru
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## Lisensi

MIT License

## Support

Untuk pertanyaan atau bantuan, silakan buat issue di repository ini.
