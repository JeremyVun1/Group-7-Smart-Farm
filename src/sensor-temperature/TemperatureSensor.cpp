#include "TemperatureSensor.h"
#include <Arduino.h>

TemperatureSensor::TemperatureSensor(int pin) : _tempPin(pin) {}

int TemperatureSensor::readValue() {
  float vol = analogRead(_tempPin);
  float cel = vol * 500 / 1024.0;

  return (int)cel;
}
