#include "TemperatureSensor.h"
#include <Arduino.h>

TemperatureSensor::TemperatureSensor(int pin) : _tempPin(pin) {}

int TemperatureSensor::readValue() {
  int readData = DHT.read11(_tempPin);
  float temp = DHT.temperature;
  return (int)temp;
}
