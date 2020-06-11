#include "TemperatureSensor.h"
#include <Arduino.h>

TemperatureSensor::TemperatureSensor(int pin) : _tempPin(pin) {}

int TemperatureSensor::readValue() {
  // Activate the DHT11 to take a temperature and humidity reading
  int readData = DHT.read11(_tempPin);

  // Ask for the temperature only
  return (int)DHT.temperature;
}
