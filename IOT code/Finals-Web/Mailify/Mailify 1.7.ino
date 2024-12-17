#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiManager.h>

long duration;
int distance;
#define TRIG_PIN 14
#define ECHO_PIN 12
const char* serverUrl = "http://192.168.1.7:5500/notify.php";
WiFiClient wifiClient;
bool hasMail = false;
void setup() {
  // put your setup code here, to run once:
  Serial.begin(115200);
  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);

  WiFiManager wifiManager;

  // Start Wi-Fi setup portal
  if (!wifiManager.autoConnect("MAIL-LBAnAl", "password")) {
        Serial.println("Failed to connect to WiFi. Starting AP mode...");
        delay(3000); // Wait for debugging message to be sent
        ESP.restart(); // Restart the ESP to retry or continue in AP mode
    }
}

void loop() {
  digitalWrite(TRIG_PIN, LOW);
  delayMicroseconds(2);
  digitalWrite(TRIG_PIN, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG_PIN, LOW);

   if (WiFi.status() != WL_CONNECTED) {
        Serial.println("WiFi disconnected. Restarting AP mode...");
        WiFiManager wifiManager;
        wifiManager.startConfigPortal("MAIL-LBAnAl", "password"); // Manually start AP
    }

  duration = pulseIn(ECHO_PIN, HIGH);
  distance = duration * 0.034 / 2;
  Serial.println(distance);
  if (distance < 15 && !hasMail) { 
    sendNotification();
    hasMail = true;
    delay(20000);
  } 
  else if ((distance >= 23 && distance <= 26) && hasMail) { 
      Serial.println("Mail removed. Ready to detect again.");
      hasMail = false;
  }

    delay(1000); // Wait before the next reading
  }

void sendNotification() {
  if (WiFi.status() == WL_CONNECTED) {
     HTTPClient http;

    http.begin(wifiClient, serverUrl);
    
    String postData = "mailID=MAIL-LBAnAl";
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    int httpResponseCode = http.POST(postData);

    if (httpResponseCode > 0) {
      Serial.println("Notification sent successfully! " + postData);
    } else {
      Serial.println("Error sending notification");
      Serial.println("HTTP Response Code: " + String(httpResponseCode));
    }
    http.end();
  } else {
    Serial.println("Wi-Fi not connected");
  }
}
