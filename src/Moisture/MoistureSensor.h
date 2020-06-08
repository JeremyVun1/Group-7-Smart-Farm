#ifndef MOISTURE_SENSOR
#define MOISTURE_SENSOR

class MoistureSensor {
  private:
    int _pin;
    int _sampleSize;
    int _maxVal;
  public:
    MoistureSensor(int pin, int maxVal);
    int readValue();
};

#endif
