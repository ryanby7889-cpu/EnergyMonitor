/*
====================================================
 MARY Energy Monitor V2
 Sprint 2
 ESP8266 NodeMCU + PZEM004T TTL + MySQL
====================================================
*/

#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <PZEM004Tv30.h>

//==================================================
// WIFI
//==================================================

const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";

//==================================================
// SERVER
// Ganti IP sesuai komputer yang menjalankan XAMPP
//==================================================

String serverName = "http://YOUR_SERVER_IP/energy-monitor/api/save_data.php";

//==================================================
// PZEM
//==================================================

PZEM004Tv30 pzem(Serial);

//==================================================

unsigned long previousMillis = 0;
const unsigned long interval = 5000;

//==================================================

void connectWiFi()
{
  if (WiFi.status() == WL_CONNECTED)
    return;

  Serial.println();
  Serial.println("========================");
  Serial.println("Connecting WiFi...");
  Serial.println("========================");

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  int retry = 0;

  while (WiFi.status() != WL_CONNECTED)
  {
    delay(500);
    Serial.print(".");

    retry++;

    if (retry >= 30)
    {
      Serial.println();
      Serial.println("WiFi Failed");
      ESP.restart();
    }
  }

  Serial.println();
  Serial.println("========================");
  Serial.println("WiFi Connected");
  Serial.print("IP : ");
  Serial.println(WiFi.localIP());
  Serial.println("========================");
}

//==================================================

void sendData(
  float voltage,
  float current,
  float power,
  float energy,
  float frequency,
  float pf)
{

  if (WiFi.status() != WL_CONNECTED)
  {
    Serial.println("WiFi Disconnect");
    return;
  }

  WiFiClient client;
  HTTPClient http;

  http.begin(client, serverName);

  http.addHeader(
      "Content-Type",
      "application/x-www-form-urlencoded");

  String postData =
      "voltage=" + String(voltage, 2) +
      "&current=" + String(current, 3) +
      "&power=" + String(power, 2) +
      "&energy=" + String(energy, 2) +
      "&frequency=" + String(frequency, 2) +
      "&pf=" + String(pf, 2);

  int httpCode =
      http.POST(postData);

  Serial.println();
  Serial.println("=========== HTTP ===========");

  Serial.print("POST : ");
  Serial.println(postData);

  Serial.print("HTTP Code : ");
  Serial.println(httpCode);

  if (httpCode > 0)
  {
    String response =
        http.getString();

    Serial.print("Server : ");
    Serial.println(response);
  }
  else
  {
    Serial.print("HTTP Error : ");
    Serial.println(
        http.errorToString(httpCode));
  }

  Serial.println("============================");

  http.end();
}

//==================================================

void setup()
{
  Serial.begin(9600);

  delay(2000);

  Serial.println();
  Serial.println("==============================");
  Serial.println("MARY ENERGY MONITOR");
  Serial.println("ESP8266 + PZEM");
  Serial.println("==============================");

  connectWiFi();
}

//==================================================

void loop()
{
  connectWiFi();

  unsigned long currentMillis =
      millis();

  if (currentMillis - previousMillis >= interval)
  {
    previousMillis = currentMillis;

    float voltage = pzem.voltage();
    float current = pzem.current();
    float power = pzem.power();
    float energy = pzem.energy();
    float frequency = pzem.frequency();
    float pf = pzem.pf();

    if (isnan(voltage))
    {
      Serial.println();
      Serial.println("PZEM NOT FOUND");
      return;
    }

    Serial.println();
    Serial.println("========== PZEM ==========");

    Serial.print("Voltage   : ");
    Serial.print(voltage);
    Serial.println(" V");

    Serial.print("Current   : ");
    Serial.print(current);
    Serial.println(" A");

    Serial.print("Power     : ");
    Serial.print(power);
    Serial.println(" W");

    Serial.print("Energy    : ");
    Serial.print(energy);
    Serial.println(" kWh");

    Serial.print("Frequency : ");
    Serial.print(frequency);
    Serial.println(" Hz");

    Serial.print("PF        : ");
    Serial.println(pf);

    Serial.println("==========================");

    sendData(
      voltage,
      current,
      power,
      energy,
      frequency,
      pf
    );
  }
}