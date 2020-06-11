#include "WaterLevelSensor.h"
#include <arduino.h>

WaterLevelSensor::WaterLevelSensor(int trigPin, int echoPin, int towerSize)
  : _towerSize(towerSize), _trigPin(trigPin), _echoPin(echoPin) { }

int WaterLevelSensor::readValue() {
  // send the ultrasonic stuff
  digitalWrite(_trigPin, LOW);
  delayMicroseconds(10);
  digitalWrite(_trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(_trigPin, LOW);

  // read the ultrasonic wave and get distance
  _duration = pulseIn(_echoPin, HIGH);
  _distance = (_duration / 29) / 2; // convert to cm
  _distance = _distance * 4;

  float waterLevel = _towerSize - _distance;
  float waterLevelPct = waterLevel / _towerSize;
  int result = (int)(waterLevelPct * 100);

  if (result < 0)
    result = 0;
  else if (result > 100)
    result = 100;

  return result;
}
