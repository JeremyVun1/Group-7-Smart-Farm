#include "WaterLevelSensor.h"
#include <arduino.h>

WaterLevelSensor::WaterLevelSensor(int trigPin, int echoPin, int towerSize)
  : _towerSize(towerSize), _trigPin(trigPin), _echoPin(echoPin) {}

int WaterLevelSensor::readValue() {
  // send the ultrasonic stuff
  digitalWrite(_trigPin, HIGH);
  delay(250);
  digitalWrite(_trigPin, LOW);

  // read the ultrasonic wave and get distance
  _duration - pulseIn(_echoPin, HIGH);
  _distance = (_duration / 2) / 29.1; // convert to cm
  return _distance;
}
