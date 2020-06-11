#include "WaterLevelSensor.h"
#include <arduino.h>

WaterLevelSensor::WaterLevelSensor(int trigPin, int echoPin, int towerSize)
  : _towerSize(towerSize), _trigPin(trigPin), _echoPin(echoPin) { }

int WaterLevelSensor::readValue() {
  // send the ultrasonic pulse
  digitalWrite(_trigPin, LOW);
  delayMicroseconds(10);
  digitalWrite(_trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(_trigPin, LOW);

  // read the ultrasonic pulse and calculate a distance
  int duration = pulseIn(_echoPin, HIGH);
  int distance = (duration / 29) / 2; // convert to cm
  distance = distance * 4; // compensate for 4x processor frequency downclock

  // get the water level as a % of the water tower size
  float waterLevelPct = (_towerSize - distance) / _towerSize;
  int result = (int)(waterLevelPct * 100);

  // clamp between 0 and 100
  if (result < 0) result = 0;
  else if (result > 100) result = 100;

  return result;
}
