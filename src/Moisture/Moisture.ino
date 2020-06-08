#include <avr/sleep.h>
#include <avr/wdt.h>
#include <avr/power.h>

#include <SpritzCipher.h>

#include "MoistureSensor.h"

const int LOW_VOLTAGE_VALUE = 400;
const int SENSOR_MAX = 760;
const int MOISTURE_PIN = A0;
const int SLEEP_CYCLES = 2;
const bool USE_SERIAL = true;
const long INTERNAL_REFERENCE_VOLTAGE = 1125300L;
const char WAKING_MSG[10] = "waking up";
const char BATTERY_LOW_MSG[12] = "battery low";
const char guid[5] = "4444";

const byte key[3] = { 0x00, 0x01, 0x02 };

volatile int sleepCount = 1;

MoistureSensor sensor(MOISTURE_PIN, SENSOR_MAX);
spritz_ctx spritz;

/*
 * SETUP
 */
void setup() {
  // downclock to reduce power consumption
  noInterrupts();
  CLKPR = bit(CLKPCE);
  CLKPR = clock_div_2;
  interrupts();

  // set pins to input mode to decrease power consumption
  for (int i=2; i<13; i++) {
    pinMode(i, INPUT);
  }

  Serial.begin(9600); // actual output is 2400 baud with clock_div_4
}

void encrypt(char* in, char* out) {
  spritz_setup(&spritz, key, strlen(key));
  spritz_crypt(&spritz, in, strlen(in), out);
}

void decrypt(char* in, char* out) {
  spritz_setup(&spritz, key, strlen(key));
  spritz_crypt(&spritz, in, strlen(out), out);
}

/*
 * Output methods
 */
void serialOutput(char* data, int dataSize) {
  Serial.flush();
  char out[128] = "";
  strcat(out, "{\"topic\": \"moisture/");
  strcat(out, guid);
  strcat(out, "\",\"data\":\"");
  Serial.print(out);

  for (int i = 0; i < dataSize; i++) {
    if (data[i] < 0x10)
      Serial.write('0');
    Serial.print(data[i]);
  }

  Serial.print("\"}");
  Serial.println();
  Serial.flush();
}

void wifiOutput(char* data, int dataSize) {
  Serial.println("wifi");
}

void sendData(int data) {
  char str[5];
  snprintf(str, 16, "%d", data);
  //Serial.println("raw in:");
  //Serial.println(str);
  //Serial.flush();

  char buf[30];
  encrypt(str, buf);

  /*
  Serial.println("Encrypted:");
  Serial.flush();

  
  */

  //decrypt(buf, str);

  Serial.flush();
  if (USE_SERIAL) {
    serialOutput(buf, strlen(str));
  } else {
    wifiOutput(buf, strlen(str));
  }
  Serial.flush();
}

// the interupt routine handler
ISR (WDT_vect) {
  wdt_disable();
  //Serial.println(WAKING_MSG);
  sleepCount++;
}

// returns internal voltage 
int getBandGap()
{
  // REFS0 : Selects AVcc external reference
  // MUX3 MUX2 MUX1 : Selects 1.1V (VBG)
   ADMUX = bit (REFS0) | bit (MUX3) | bit (MUX2) | bit (MUX1);
   delay(2);
   ADCSRA |= bit( ADSC );  // start conversion
   while (ADCSRA & bit (ADSC))
     { }  // wait for conversion to complete
   int results = ((INTERNAL_REFERENCE_VOLTAGE / ADC) + 5) / 10;
   return results;
}

void loop() {
  set_sleep_mode(SLEEP_MODE_PWR_DOWN);
  sleep_enable();
  sleep_bod_disable();

  // disable ADC
  static byte prevADCSRA = ADCSRA;
  ADCSRA = 0;

  //Serial.println("sleeping");
  //Serial.flush();

  while (sleepCount < 4) {
    noInterrupts();
    MCUSR = 0;

    WDTCSR = bit(WDCE) | bit(WDE);
    WDTCSR = bit(WDIE) | bit(WDP2) | bit(WDP1);
    //WDTCSR = bit(WDIE) | bit(WDP3) | bit(WDP0);
    wdt_reset();

    interrupts();

    sleep_cpu();
  }

  sleep_disable();
  
  //Serial.println("waking up");
  sleepCount = 0;
  ADCSRA = prevADCSRA; // restore ADC

  int val = sensor.readValue();
  sendData(val);

  // check for low voltage
  //int internalVoltage = getBandGap();
  //if (internalVoltage < LOW_VOLTAGE_VALUE) {
    //sendData(batteryLowMsg);
  //}
  /*

  set_sleep_mode(SLEEP_MODE_PWR_DOWN);
  sleep_enable();

  // Disable the ADC (Analog to digital converter, pins A0 [14] to A5 [19])
  // don't need ADC while we are sleeping
  static byte prevADCSRA = ADCSRA;
  ADCSRA = 0;

  // Sleep for several times the duration of the watch dog timer
  while (sleepCount < SLEEP_MULTI) {
    sleep_bod_disable();

    // no interupts before we are sleeping
    noInterrupts();

    MCUSR = 0;
    WDTCSR = bit(WDCE) | bit(WDE);
    // 2 | 1 - 1 second
    // 2 | 1 | 0 - 2 seconds
    // 3 - 4 seconds
    // 3 | 0 - 8 seconds
    WDTCSR = bit(WDIE) | bit(WDP2) | bit(WDP1);
    //WDTCSR = bit(WDIE) | bit(WDP3) | bit(WDP0);
    wdt_reset();

    interrupts();
    sleep_cpu();
  }

  // lock from sleeping while we run our logic
  sleep_disable();
  sleepCount = 0;
  ADCSRA = prevADCSRA; // re-enable ADC so that we can poll our analog sensors

  //int val = sensor.readValue();
  //sendData(val);

  // check for low voltage
  //int internalVoltage = getBandGap();
  //if (internalVoltage < LOW_VOLTAGE_VALUE) {
    //sendData(batteryLowMsg);
  //}
  */
}
