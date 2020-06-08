#include "MoistureSensor.h"
#include "Util.h"

MoistureSensor::MoistureSensor(int pin, int maxVal) : _pin(pin), _maxVal(maxVal), _sampleSize(5) { }

int MoistureSensor::readValue() {
  double val = 0;

  // get average over a number of samples
  for (int i=0; i < _sampleSize; i++) {
    val = rollingAverage(val, analogRead(_pin), i+1);
  };
  
  return rescale(val, _maxVal, 1024);
}
