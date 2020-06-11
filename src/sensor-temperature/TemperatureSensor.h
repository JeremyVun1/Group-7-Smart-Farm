#include <dht.h>

class TemperatureSensor {
  private:
    int _tempPin;
    dht DHT;
  public:
    TemperatureSensor(int pin);
    int readValue();
};
