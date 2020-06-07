#include <avr/sleep.h>
#include <avr/wdt.h>
#include <avr/power.h>

#include "MoistureSensor.h"

#define USE_SERIAL true
#define SLEEP_MULTI 1 // multiples of 4 * wdt timer duration

#define MOISTURE_PIN A0
#define SENSOR_MAX 760 // maximum sensor value

#define LOW_VOLTAGE_VALUE 400

const long InternalReferenceVoltage = 1125300L;
volatile int sleepCount = 1;

char sleeping[9] = "Sleeping";
char waking[7] = "Waking";

MoistureSensor sensor(MOISTURE_PIN, SENSOR_MAX);

void setup() {
  noInterrupts();
  CLKPR = bit(CLKPCE);
  CLKPR = clock_div_4;
  interrupts();
  
  Serial.begin(9600); // actual output is 2400 baud

  for (int i=2; i<13; i++) {
    pinMode(i, INPUT);
  }

  
}

void serialOutput(int val) {
  Serial.println(val);
}

void wifiOutput(int val) {
  Serial.println(val);
}

void sendData(int val) {
  if (USE_SERIAL) {
    serialOutput(val);
  } else {
    wifiOutput(val);
  }
}

// the interupt routine handler
ISR (WDT_vect) {
  wdt_disable();
  sleepCount++;
  Serial.println(waking);
}

int getBandGap()
{
  // REFS0 : Selects AVcc external reference
  // MUX3 MUX2 MUX1 : Selects 1.1V (VBG)  
   ADMUX = bit (REFS0) | bit (MUX3) | bit (MUX2) | bit (MUX1);
   delay(2);
   ADCSRA |= bit( ADSC );  // start conversion
   while (ADCSRA & bit (ADSC))
     { }  // wait for conversion to complete
   int results = ((InternalReferenceVoltage / ADC) + 5) / 10;
   return results;
}

void loop() {
  /* Set the type of sleep mode we want. Can be one of (in order of power saving):
   SLEEP_MODE_IDLE (Timer 0 will wake up every millisecond to keep millis running)
   SLEEP_MODE_ADC
   SLEEP_MODE_PWR_SAVE (TIMER 2 keeps running)
   SLEEP_MODE_EXT_STANDBY
   SLEEP_MODE_STANDBY (Oscillator keeps running, makes for faster wake-up)
   SLEEP_MODE_PWR_DOWN (Deep sleep)
   */
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
    //WDTCSR = bit(WDIE) | bit(WDP2) | bit(WDP1);
    WDTCSR = bit(WDIE) | bit(WDP3) | bit(WDP0);
    wdt_reset();

    Serial.println(sleeping);
    Serial.flush();

    interrupts();
    sleep_cpu();
  }

  // lock from sleeping while we run our logic
  sleep_disable();

  // re-enable ADC so that we can poll our analog sensors
  ADCSRA = prevADCSRA;

  sleepCount = 0;

  // get the sensor data
  //sendData(analogRead(A0));
  sendData(sensor.readValue());
  int iternalVoltage = getBandGap();
  if (internalVoltage < LOW_VOLTAGE_VALUE) {
    SendData(
  }
}
