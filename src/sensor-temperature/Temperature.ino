//initializes/defines the output pin of the LM35 temperature sensor
int tempPIN= 0;
//this sets the ground pin to LOW and the input voltage pin to high
void setup()
{
  Serial.begin(9600);
}
 
//main loop
void loop()
{
  int vol= analogRead(tempPIN);
  float mv= (vol/1023.0) * 5000;
  float cel= mv/100;

  Serial.print(cel);
  Serial.print(" Temparature in Degrees Celsius, ");

  Serial.print((cel * 9)/5 + 32);
  Serial.println(" Temp in degrees Fahrenheit");
  delay(1000);
 
}
