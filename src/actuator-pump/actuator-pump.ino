#include <avr/power.h>

#include "WaterPump.h"

// Water pump pins
#define PIN 3
#define DIR1 2
#define DIR2 4

WaterPump actuator(PIN, DIR1, DIR2);

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

  pinMode(PIN, OUTPUT);
  pinMode(DIR1, OUTPUT);
  pinMode(DIR2, OUTPUT);

  Serial.begin(9600);
}

void executeCommand(int cmd) {
  switch(cmd) {
    case 0:
      actuator.off();
      break;
    case 1:
      actuator.on();
      break;
  };
};

void handleCommands() {
  if (Serial.available()) {
    int cmd = Serial.read();
    if (cmd < 0)
      return;
    else executeCommand(cmd);
  }
};

void loop() {
  //actuator.on();
  handleCommands();
  delay(250);
}
