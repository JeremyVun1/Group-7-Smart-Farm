#include <avr/sleep.h>
#include <avr/wdt.h>
#include <avr/power.h>

#include "MoistureSensor.h"

const int MOISTURE_PIN = A0;

const int SENSOR_MAX = 760;
const int SLEEP_CYCLES = 2;
const bool USE_SERIAL = true;
const long INTERNAL_REFERENCE_VOLTAGE = 1125300L;
const int LOW_VOLTAGE_VALUE = 400;

const char guid[10] = "soil_b";

volatile int sleepCount = 1;
volatile char readBuffer[10];

MoistureSensor sensor(MOISTURE_PIN, SENSOR_MAX);

/*
 * SETUP
 */
void setup() {
  // downclock to reduce power consumption
  noInterrupts();
  CLKPR = bit(CLKPCE);
  CLKPR = clock_div_4;
  interrupts();

  // set pins to input mode to decrease power consumption
  for (int i=2; i<13; i++) {
    pinMode(i, INPUT);
  }

  Serial.begin(9600); // actual output is 2400 baud with clock_div_4
}

/*
 * OUTPUT JSON TO SERIAL
 */
void serialOutput(char* data, char* topic) {
  Serial.flush();
  char out[128];
  strcpy(out, "{\"t\":\"");
  strcat(out, topic);
  strcat(out, "/");
  strcat(out, guid);
  strcat(out, "\",\"p\":\"");
  strcat(out, data);
  strcat(out, "\"}");
  Serial.println(out);
  Serial.flush();
}

void sendInt(int val, char* topic) {
  snprintf(readBuffer, 10, "%d", val);
  serialOutput(readBuffer, topic);
}

// the interupt routine handler
ISR (WDT_vect) {
  wdt_disable();
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
   
   while (ADCSRA & bit (ADSC)) { } // wait for conversion to complete

   int results = ((INTERNAL_REFERENCE_VOLTAGE / ADC) + 5) / 10;
   return results;
}

void loop() {
  set_sleep_mode(SLEEP_MODE_PWR_DOWN);
  sleep_enable();
  sleep_bod_disable(); // disable brownout detection

  // disable ADC when we go to sleep
  static byte prevADCSRA = ADCSRA;
  ADCSRA = 0;

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

  sleepCount = 0;
  ADCSRA = prevADCSRA; // restore ADC for sensor

  /*
   * Do the sensor logic stuff here
   */
  int val = sensor.readValue();
  sendInt(val, "m");

  int internalVoltage = getBandGap();
  sendInt(internalVoltage, "v");
}
