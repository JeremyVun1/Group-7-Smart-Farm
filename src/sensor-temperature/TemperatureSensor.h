class TemperatureSensor {
  private:
    int _tempPin;
  public:
    TemperatureSensor(int pin);
    int readValue();
};
