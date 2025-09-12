/*
 * Kode Arduino untuk Sistem Presensi RFID dengan Integrasi Website
 * Menggunakan sensor PN532 dengan koneksi SPI
 * Fitur: LCD Display, Buzzer, WiFi, HTTP Request ke Database
 * 
 * OPTIMISASI:
 * - WiFi hanya terhubung di awal dan saat terputus
 * - Health check WiFi setiap 30 detik
 * - HTTP timeout 5 detik untuk responsivitas
 * - Delay dikurangi untuk proses yang lebih cepat
 * 
 * PERBAIKAN STACK OVERFLOW:
 * - Buffer global untuk mencegah stack overflow
 * - JSON buffer dikurangi dari 1024 ke 512 bytes
 * - Memory monitoring dengan ESP.getFreeHeap()
 * - Error handling yang lebih robust
 * - Response length validation
 * 
 * Hardware yang dibutuhkan:
 * - ESP32
 * - PN532 NFC Module
 * - LCD 16x2 dengan I2C
 * - Buzzer
 * - LED (optional)
 */

 #include <SPI.h>
 #include <WiFi.h>
 #include <HTTPClient.h>
 #include <ArduinoJson.h>
 #include <LiquidCrystal_I2C.h>
 #include <Adafruit_PN532.h>
 #include <NTPClient.h>
 #include <WiFiUdp.h>
 
 // ====== Pin Configuration ======
 // PN532 SPI Pins
 #define PN532_SCK  (18)
 #define PN532_MISO (19)
 #define PN532_MOSI (23)
 #define PN532_SS   (27)
 
 // Other Components
 #define BUZZER_PIN 26
 #define LED_PIN 2  // Built-in LED
 
 // ====== WiFi Configuration ======
 const char* ssid = "kontrakancempaka";           // Ganti dengan nama WiFi Anda
 const char* password = "singertideswal";    // Ganti dengan password WiFi Anda
 
 // ====== Server Configuration ======
 const char* serverURL = "http://192.168.1.9:8000/api/attendance/record"; // Ganti dengan IP server Anda
 // Contoh: "http://192.168.1.100:8000/api/attendance/record"
 // atau: "https://domain-anda.com/api/attendance/record"
 
 // ====== Object Initialization ======
 Adafruit_PN532 nfc(PN532_SCK, PN532_MISO, PN532_MOSI, PN532_SS);
 LiquidCrystal_I2C lcd(0x27, 16, 2);  // Alamat I2C LCD biasanya 0x27 atau 0x3F
 
// NTP Client untuk mendapatkan waktu
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, "pool.ntp.org", 0, 60000); // UTC time, offset akan dihitung manual
bool ntpInitialized = false;
unsigned long lastNtpUpdate = 0;
const unsigned long NTP_UPDATE_INTERVAL = 300000; // Update NTP setiap 5 menit

// Alternatif NTP servers untuk Indonesia
const char* ntpServers[] = {
  "pool.ntp.org",
  "time.nist.gov", 
  "time.google.com",
  "time.cloudflare.com",
  "id.pool.ntp.org"  // NTP server khusus Indonesia
};
int currentNtpServer = 0;
 
 // ====== Global Variables ======
 String lastCardId = "";
 unsigned long lastScanTime = 0;
 const unsigned long SCAN_DELAY = 3000; // Delay 3 detik antar scan kartu yang sama
 bool wifiConnected = false;
 unsigned long lastWifiCheck = 0;
 const unsigned long WIFI_CHECK_INTERVAL = 30000; // Cek WiFi setiap 30 detik
 
 // Buffer untuk mencegah stack overflow
 char cardIdBuffer[32];
 char timestampBuffer[32];
 char jsonBuffer[256];
 
 void setup() {
   Serial.begin(9600);
   Serial.println("=== Sistem Presensi RFID ===");
   
   // ====== Memory Management ======
   Serial.println("Free heap: " + String(ESP.getFreeHeap()));
   
   // ====== Pin Setup ======
   pinMode(BUZZER_PIN, OUTPUT);
   pinMode(LED_PIN, OUTPUT);
   digitalWrite(LED_PIN, LOW);
   
   // ====== LCD Setup ======
   lcd.init();
   lcd.backlight();
   lcd.clear();
   printCenter(0, "SISTEM PRESENSI");
   printCenter(1, "Memulai...");
   
   // ====== NFC Setup ======
   Serial.println("Inisialisasi PN532...");
   nfc.begin();
   
   uint32_t versiondata = nfc.getFirmwareVersion();
   if (!versiondata) {
     Serial.println("Error: PN532 tidak ditemukan!");
     lcd.clear();
     printCenter(0, "ERROR!");
     printCenter(1, "PN532 tidak ada");
     while (1) {
       digitalWrite(LED_PIN, HIGH);
       delay(200);
       digitalWrite(LED_PIN, LOW);
       delay(200);
     }
   }
   
   Serial.print("Found chip PN5"); 
   Serial.println((versiondata >> 24) & 0xFF, HEX);
   Serial.print("Firmware ver. "); 
   Serial.print((versiondata >> 16) & 0xFF, DEC);
   Serial.print('.'); 
   Serial.println((versiondata >> 8) & 0xFF, DEC);
   
   nfc.SAMConfig();
   
   // ====== WiFi Setup ======
   connectToWiFi();
   
  // ====== NTP Setup ======
  initializeNTP();
  
  // Debug NTP status
  Serial.println("=== NTP Status Debug ===");
  Serial.println("NTP Initialized: " + String(ntpInitialized ? "YES" : "NO"));
  if (ntpInitialized) {
    Serial.println("Current NTP Server: " + String(ntpServers[currentNtpServer]));
    Serial.println("Epoch Time: " + String(timeClient.getEpochTime()));
    Serial.println("Formatted Time: " + timeClient.getFormattedTime());
    String testTimestamp = getCurrentTimestamp();
    Serial.println("Generated Timestamp: " + testTimestamp);
  }
  Serial.println("========================");
   
   // ====== Ready ======
   playStartupSound();
   lcd.clear();
   printCenter(0, "SISTEM SIAP");
   printCenter(1, "Tempelkan Kartu");
   Serial.println("Sistem siap! Tempelkan kartu NFC...");
 }
 
 void loop() {
   // ====== WiFi Health Check ======
   checkWiFiConnection();
   
   uint8_t success;
   uint8_t uid[] = { 0, 0, 0, 0, 0, 0, 0 };
   uint8_t uidLength;
   
   // Coba baca kartu NFC
   success = nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, uid, &uidLength);
   
   if (success) {
     // Konversi UID ke string dengan buffer aman
     memset(cardIdBuffer, 0, sizeof(cardIdBuffer));
     int pos = 0;
     for (byte i = 0; i < uidLength && pos < sizeof(cardIdBuffer) - 1; i++) {
       if (uid[i] < 0x10) {
         cardIdBuffer[pos++] = '0';
       }
       pos += sprintf(cardIdBuffer + pos, "%X", uid[i]);
     }
     
     String cardId = String(cardIdBuffer);
     
     // Cek apakah kartu sama dengan scan sebelumnya
     if (cardId != lastCardId || (millis() - lastScanTime) > SCAN_DELAY) {
       lastCardId = cardId;
       lastScanTime = millis();
       
       Serial.println("Kartu terdeteksi: " + cardId);
       Serial.println("Free heap: " + String(ESP.getFreeHeap()));
       
       // Tampilkan di LCD
       lcd.clear();
       printCenter(0, "KARTU TERDETEKSI");
       String displayCard = cardId;
       if (displayCard.length() > 16) {
         displayCard = displayCard.substring(0, 16);
       }
       printCenter(1, displayCard);
       
       // Bunyi buzzer
       playBeepSound();
       
      // Update waktu dari NTP (hanya jika WiFi terhubung)
      if (wifiConnected) {
        updateNTPTime();
      }
       
       // Kirim ke server dengan error handling
       sendAttendanceData(cardId);
       
       // Kembali ke tampilan awal (delay sudah dihandle di sendAttendanceData)
       lcd.clear();
       printCenter(0, "SISTEM SIAP");
       printCenter(1, "Tempelkan Kartu");
     }
   }
   
   delay(100); // Delay kecil untuk mengurangi beban CPU
 }
 
 // ====== WiFi Functions ======
 void connectToWiFi() {
   Serial.print("Menghubungkan ke WiFi: ");
   Serial.println(ssid);
   
   lcd.clear();
   printCenter(0, "KONEKSI WIFI");
   printCenter(1, "Menghubungkan...");
   
   WiFi.begin(ssid, password);
   
   int attempts = 0;
   while (WiFi.status() != WL_CONNECTED && attempts < 30) {
     delay(1000);
     Serial.print(".");
     attempts++;
     
     // Update LCD dengan titik-titik
     String dots = "";
     for (int i = 0; i < (attempts % 4); i++) {
       dots += ".";
     }
     lcd.setCursor(0, 1);
     lcd.print("Menghubungkan" + dots + "   ");
   }
   
   if (WiFi.status() == WL_CONNECTED) {
     Serial.println();
     Serial.println("WiFi terhubung!");
     Serial.print("IP Address: ");
     Serial.println(WiFi.localIP());
     
     wifiConnected = true;
     lastWifiCheck = millis();
     
     lcd.clear();
     printCenter(0, "WIFI TERHUBUNG");
     lcd.setCursor(0, 1);
     lcd.print(WiFi.localIP());
     delay(2000);
   } else {
     Serial.println("Gagal terhubung ke WiFi!");
     wifiConnected = false;
     
     lcd.clear();
     printCenter(0, "WIFI GAGAL");
     printCenter(1, "Cek koneksi");
     delay(3000);
   }
 }
 
 // ====== WiFi Health Check Function ======
 void checkWiFiConnection() {
   unsigned long currentTime = millis();
   
   // Cek WiFi hanya setiap WIFI_CHECK_INTERVAL
   if (currentTime - lastWifiCheck >= WIFI_CHECK_INTERVAL) {
     lastWifiCheck = currentTime;
     
     if (WiFi.status() != WL_CONNECTED) {
       if (wifiConnected) {
         Serial.println("WiFi terputus! Mencoba reconnect...");
         wifiConnected = false;
         
         // Tampilkan notifikasi di LCD
         lcd.clear();
         printCenter(0, "WIFI TERPUTUS");
         printCenter(1, "Reconnecting...");
         delay(1000);
       }
       
       // Coba reconnect
       WiFi.reconnect();
       delay(2000); // Tunggu sebentar untuk reconnect
       
       if (WiFi.status() == WL_CONNECTED) {
         Serial.println("WiFi berhasil reconnect!");
         wifiConnected = true;
         
        // Update NTP time
        updateNTPTime();
         
         // Tampilkan notifikasi reconnect
         lcd.clear();
         printCenter(0, "WIFI TERHUBUNG");
         printCenter(1, "Lagi");
         delay(1000);
         
         // Kembali ke tampilan normal
         lcd.clear();
         printCenter(0, "SISTEM SIAP");
         printCenter(1, "Tempelkan Kartu");
       }
     } else {
       // WiFi masih terhubung, update flag
       if (!wifiConnected) {
         wifiConnected = true;
         Serial.println("WiFi status: Connected");
       }
     }
   }
 }
 
 // ====== HTTP Functions ======
 void sendAttendanceData(String nfcId) {
   // Cek memori sebelum memulai
   Serial.println("Free heap sebelum HTTP: " + String(ESP.getFreeHeap()));
   
   if (!wifiConnected || WiFi.status() != WL_CONNECTED) {
     Serial.println("WiFi tidak terhubung!");
     lcd.clear();
     printCenter(0, "ERROR WIFI");
     printCenter(1, "Tidak terhubung");
     playErrorSound();
     delay(2000);
     return;
   }
   
   // Cek panjang NFC ID untuk mencegah buffer overflow
   if (nfcId.length() > 20) {
     Serial.println("NFC ID terlalu panjang!");
     lcd.clear();
     printCenter(0, "ERROR");
     printCenter(1, "NFC ID invalid");
     playErrorSound();
     delay(2000);
     return;
   }
   
   HTTPClient http;
   http.begin(serverURL);
   http.addHeader("Content-Type", "application/json");
   http.setTimeout(5000); // Timeout 5 detik
   
   // Buat timestamp dengan buffer aman
   String timestamp = getCurrentTimestamp();
   
   // Buat JSON payload dengan buffer yang lebih kecil
   DynamicJsonDocument doc(512); // Kurangi dari 1024 ke 512
   doc["nfc_id"] = nfcId;
   doc["timestamp"] = timestamp;
   
   // Gunakan buffer global untuk mencegah stack overflow
   memset(jsonBuffer, 0, sizeof(jsonBuffer));
   serializeJson(doc, jsonBuffer, sizeof(jsonBuffer));
   String jsonString = String(jsonBuffer);
   
   Serial.println("Mengirim data ke server...");
   Serial.println("URL: " + String(serverURL));
   Serial.println("Data: " + jsonString);
   
   // Tampilkan di LCD
   lcd.clear();
   printCenter(0, "MENGIRIM DATA");
   printCenter(1, "Tunggu...");
   
   // Kirim POST request dengan error handling
   int httpResponseCode = http.POST(jsonString);
   Serial.println("Free heap setelah POST: " + String(ESP.getFreeHeap()));
   
   if (httpResponseCode > 0) {
     String response = http.getString();
     Serial.println("HTTP Response code: " + String(httpResponseCode));
     Serial.println("Response length: " + String(response.length()));
     
     // Cek panjang response untuk mencegah overflow
     if (response.length() > 500) {
       Serial.println("Response terlalu panjang, potong!");
       response = response.substring(0, 500);
     }
     
     Serial.println("Response: " + response);
     
     // Parse response JSON dengan buffer yang lebih kecil
     DynamicJsonDocument responseDoc(512); // Kurangi dari 1024 ke 512
     DeserializationError error = deserializeJson(responseDoc, response);
     
     if (!error) {
       bool success = responseDoc["success"];
       String message = responseDoc["message"];
       
       if (success) {
         // Presensi berhasil
         String studentName = responseDoc["student"]["name"];
         String studentClass = responseDoc["student"]["class"];
         String status = responseDoc["student"]["status"];
         String time = responseDoc["student"]["time"];
         
         Serial.println("✅ Presensi berhasil!");
         Serial.println("Nama: " + studentName);
         Serial.println("Kelas: " + studentClass);
         Serial.println("Status: " + status);
         Serial.println("Waktu: " + time);
         
         // Tampilkan di LCD
         lcd.clear();
         printCenter(0, "BERHASIL!");
         
         // Tampilkan nama (potong jika terlalu panjang)
         String displayName = studentName;
         if (displayName.length() > 16) {
           displayName = displayName.substring(0, 13) + "...";
         }
         printCenter(1, displayName);
         
         // Bunyi sukses
         playSuccessSound();
         delay(2000); // Kurangi delay
         
         // Tampilkan info tambahan
         lcd.clear();
         printCenter(0, studentClass);
         String statusTime = status + " " + time.substring(0, 5); // Jam:menit saja
         if (statusTime.length() > 16) {
           statusTime = status;
         }
         printCenter(1, statusTime);
         delay(2000); // Kurangi delay
         
       } else {
         // Presensi gagal
         Serial.println("❌ Presensi gagal!");
         Serial.println("Pesan: " + message);
         
         lcd.clear();
         printCenter(0, "GAGAL!");
         
         // Tampilkan pesan error (potong jika terlalu panjang)
         String errorMsg = message;
         if (errorMsg.length() > 16) {
           errorMsg = errorMsg.substring(0, 13) + "...";
         }
         printCenter(1, errorMsg);
         
         playErrorSound();
         delay(2000); // Kurangi delay
       }
     } else {
       Serial.println("Error parsing JSON response");
       lcd.clear();
       printCenter(0, "ERROR PARSE");
       printCenter(1, "Response invalid");
       playErrorSound();
       delay(2000);
     }
   } else {
     Serial.println("Error pada HTTP request");
     Serial.println("Error code: " + String(httpResponseCode));
     
     lcd.clear();
     printCenter(0, "ERROR HTTP");
     printCenter(1, "Code: " + String(httpResponseCode));
     playErrorSound();
     delay(2000);
   }
   
   // Cleanup
   http.end();
   
   // Force garbage collection
   Serial.println("Free heap setelah cleanup: " + String(ESP.getFreeHeap()));
   
   // Small delay untuk stabilitas
   delay(100);
 }
 
 // ====== NTP Functions ======
void initializeNTP() {
  Serial.println("Menginisialisasi NTP...");
  
  timeClient.begin();
  
  // Tunggu sebentar untuk WiFi stabil
  delay(2000);
  
  // Coba update NTP dengan multiple servers
  bool ntpSuccess = false;
  
  for (int serverIndex = 0; serverIndex < 5 && !ntpSuccess; serverIndex++) {
    Serial.println("Mencoba NTP server: " + String(ntpServers[serverIndex]));
    
    // Reinitialize dengan server yang berbeda
    timeClient.end();
    timeClient.begin();
    
    int ntpRetries = 0;
    while (ntpRetries < 3 && !ntpSuccess) {
      Serial.println("NTP update attempt " + String(ntpRetries + 1));
      
      if (timeClient.update()) {
        unsigned long epochTime = timeClient.getEpochTime();
        Serial.println("Epoch time: " + String(epochTime));
        Serial.println("Formatted time: " + timeClient.getFormattedTime());
        
        // Validasi epoch time (harus lebih dari 1 Jan 2020)
        if (epochTime > 1577836800) {
          ntpSuccess = true;
          ntpInitialized = true;
          currentNtpServer = serverIndex;
          lastNtpUpdate = millis();
          Serial.println("NTP berhasil diinisialisasi dengan server: " + String(ntpServers[serverIndex]));
          Serial.println("Waktu NTP: " + timeClient.getFormattedTime());
          
          // Test timestamp generation
          String testTimestamp = getCurrentTimestamp();
          Serial.println("Test timestamp: " + testTimestamp);
          break;
        } else {
          Serial.println("Epoch time tidak valid: " + String(epochTime));
        }
      } else {
        Serial.println("NTP update gagal");
      }
      
      ntpRetries++;
      delay(1000);
    }
    
    if (!ntpSuccess) {
      delay(2000); // Tunggu sebelum coba server berikutnya
    }
  }
  
  if (!ntpSuccess) {
    Serial.println("Gagal menginisialisasi NTP dengan semua server!");
    ntpInitialized = false;
  }
}

void updateNTPTime() {
  unsigned long currentTime = millis();
  
  // Update NTP hanya jika sudah waktunya atau belum pernah diinisialisasi
  if (!ntpInitialized || (currentTime - lastNtpUpdate) >= NTP_UPDATE_INTERVAL) {
    Serial.println("Updating NTP time...");
    
    if (timeClient.update()) {
      unsigned long epochTime = timeClient.getEpochTime();
      
      // Validasi epoch time
      if (epochTime > 1577836800) {
        ntpInitialized = true;
        lastNtpUpdate = currentTime;
        Serial.println("NTP time updated: " + timeClient.getFormattedTime());
      } else {
        Serial.println("NTP time tidak valid, coba server lain...");
        tryAlternativeNTPServer();
      }
    } else {
      Serial.println("NTP update gagal, coba server lain...");
      tryAlternativeNTPServer();
    }
  }
}

void tryAlternativeNTPServer() {
  // Coba server NTP alternatif
  currentNtpServer = (currentNtpServer + 1) % 5;
  
  Serial.println("Mencoba NTP server alternatif: " + String(ntpServers[currentNtpServer]));
  
  timeClient.end();
  timeClient.begin();
  
  if (timeClient.update()) {
    unsigned long epochTime = timeClient.getEpochTime();
    if (epochTime > 1577836800) {
      ntpInitialized = true;
      lastNtpUpdate = millis();
      Serial.println("NTP berhasil dengan server alternatif: " + String(ntpServers[currentNtpServer]));
    }
  }
}

// ====== Utility Functions ======
String getCurrentTimestamp() {
  // Pastikan NTP selalu ter-update untuk waktu realtime
  if (wifiConnected && ntpInitialized) {
    updateNTPTime();
  }
  
  // Dapatkan epoch time terbaru
  unsigned long epochTime = timeClient.getEpochTime();
  
  Serial.println("Debug - Epoch time: " + String(epochTime));
  Serial.println("Debug - Formatted time: " + timeClient.getFormattedTime());
  
  // Validasi epoch time (harus lebih dari tahun 2020)
  if (epochTime < 1577836800) { // 1 Jan 2020 00:00:00 UTC
    Serial.println("Warning: Epoch time tidak valid, menggunakan waktu sistem");
    // Fallback ke waktu sistem jika NTP tidak valid
    epochTime = millis() / 1000 + 1577836800; // Aproksimasi waktu
  }
  
  // Tambahkan offset UTC+7 (7 jam = 25200 detik) ke epoch time
  epochTime += 25200;
  
  // Konversi ke struct tm
  time_t rawTime = (time_t)epochTime;
  struct tm *ptm = gmtime(&rawTime);
  
  if (ptm != NULL) {
    // Format: YYYY-MM-DD HH:MM:SS dengan buffer global
    memset(timestampBuffer, 0, sizeof(timestampBuffer));
    sprintf(timestampBuffer, "%04d-%02d-%02d %02d:%02d:%02d",
            ptm->tm_year + 1900, ptm->tm_mon + 1, ptm->tm_mday,
            ptm->tm_hour, ptm->tm_min, ptm->tm_sec);
    
    Serial.println("Debug - Final timestamp: " + String(timestampBuffer));
  } else {
    Serial.println("Error: gmtime returned NULL");
    // Fallback ke waktu sistem dengan format yang benar
    unsigned long currentMillis = millis();
    unsigned long seconds = currentMillis / 1000;
    unsigned long minutes = seconds / 60;
    unsigned long hours = minutes / 60;
    unsigned long days = hours / 24;
    
    // Aproksimasi waktu (mulai dari 1 Jan 2025)
    unsigned long year = 2025;
    unsigned long month = 1;
    unsigned long day = 1 + (days % 365);
    unsigned long hour = (hours % 24) + 7; // UTC+7
    unsigned long min = minutes % 60;
    unsigned long sec = seconds % 60;
    
    if (hour >= 24) {
      hour -= 24;
      day += 1;
    }
    
    memset(timestampBuffer, 0, sizeof(timestampBuffer));
    sprintf(timestampBuffer, "%04d-%02d-%02d %02d:%02d:%02d",
            year, month, day, hour, min, sec);
  }
  
  return String(timestampBuffer);
}
 
 void printCenter(int row, String text) {
   int len = text.length();
   int pos = (16 - len) / 2;
   if (pos < 0) pos = 0;
   
   lcd.setCursor(pos, row);
   lcd.print(text);
 }
 
 // ====== Sound Functions ======
 void playStartupSound() {
   tone(BUZZER_PIN, 1000, 200);
   delay(250);
   tone(BUZZER_PIN, 1500, 200);
   delay(250);
   tone(BUZZER_PIN, 2000, 200);
   delay(250);
 }
 
 void playBeepSound() {
   tone(BUZZER_PIN, 1500, 100);
   delay(150);
 }
 
 void playSuccessSound() {
   tone(BUZZER_PIN, 1000, 200);
   delay(250);
   tone(BUZZER_PIN, 1500, 200);
   delay(250);
   tone(BUZZER_PIN, 2000, 300);
   delay(350);
 }
 
 void playErrorSound() {
   tone(BUZZER_PIN, 500, 200);
   delay(250);
   tone(BUZZER_PIN, 300, 200);
   delay(250);
   tone(BUZZER_PIN, 200, 300);
   delay(350);
 }
 