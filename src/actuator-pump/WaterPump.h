class WaterPump {
  // drive using controller
  private:
    int _pin;
    int _dir1;
    int _dir2;
    int _mSpeed;
    bool _isOn;
  public:
    WaterPump(int pin, int dir1, int dir2, int mSpeed = 255);
    void on();
    void off();
};
