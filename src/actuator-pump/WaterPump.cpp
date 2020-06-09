#include "WaterPump.h"
#include <Arduino.h>

WaterPump::WaterPump(int pin, int dir1, int dir2, int mSpeed)
  : _pin(pin), _dir1(dir1), _dir2(dir2), _mSpeed(mSpeed), _isOn(false)
  {
    this->off();
  }

void WaterPump::on() {
  if (_isOn)
    return;

  digitalWrite(_dir1, HIGH);
  digitalWrite(_dir2, LOW);
  analogWrite(_pin, _mSpeed);

  _isOn = true;
}

void WaterPump::off() {
  if (!_isOn)
    return;

  digitalWrite(_dir1, LOW);
  digitalWrite(_dir2, LOW);
  _isOn = false;
}
