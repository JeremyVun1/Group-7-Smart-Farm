class WaterLevelSensor {
  private:
    int _towerSize;
    int _trigPin;
    int _echoPin;

    int _duration;
    int _distance;
  public:
    WaterLevelSensor(int towerSize, int trigPin, int echoPin);
    int readValue();
};
