class WaterLevelSensor {
  private:
    int _towerSize;
    int _trigPin;
    int _echoPin;
  public:
    WaterLevelSensor(int towerSize, int trigPin, int echoPin);
    int readValue();
};
