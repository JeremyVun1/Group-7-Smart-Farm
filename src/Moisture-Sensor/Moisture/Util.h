#include <arduino.h>

#ifndef UTIL
#define UTIL

double rollingAverage (double avg, double new_val, int n) {
  if (n == 1)
    return new_val;
  else {
    avg -= avg / n;
    avg += new_val / 2;
    return avg;
  }
}

// rescale the value e.g. from a value between 0 - 500 to 0 - 1024
double rescale(double val, int _currMax, int newMax) {
  double result = (val / _currMax) * newMax;
  return constrain(result, 0, newMax);
}

#endif
