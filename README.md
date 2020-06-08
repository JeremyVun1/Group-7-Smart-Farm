# Group 7 Smart Farm

## Overview
This repository contains all the code and documentation for an IoT smart farm implementation

## How to use
### AWS Webserver
We have already set up infrastructure for the project. Sensors -> Gateway -> MQTT Broker -> AWS

Our AWS Web Server:
*http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/Web-Server/*


### Set up Mosquitto MQTT Broker
```
1. from mosquitto folder, run
./mosquitto -c mosquitto.conf -v

2. To test the broker using two other shell's
First shell:
./mosquitto_sub -t "topic/test" -u user -P password

Second shell:
./mosquitto_pub -t "topic/test" -m "hello world!" -u user -P password

3. To configure MQTT user accounts
./mosquitto_passwd -b "users.txt" <username> <password>
```

```
### Set up Serial-to-MQTT gateway
1. Install node.js
https://nodered.org/docs/faq/node-versions

2. Install node-red
https://nodered.org/docs/getting-started/local

3. From admin shell run
node-red

4. Go to node-red web app and import flows.json from the "src/node-red" folder (menu button top right)
Default node-red url: http://localhost:1880

5. Double click the MQTT Send node and add an MQTT broker under "Server"
Point it to where your Mosquitto is listening
Under security, use a username/password:
username: "user"
pasword: "password"

5. Connect arduino via USB Serial, and press the deploy button in node-red
Arduino should not be sending json strings through serial, parsed into MQTT JSON structure and sent to the mosquitto server
Change MQTT node to point to where you set up your MQTT broker as necessary
```

```
### Subscribe to the MQTT broker
| MQTT Topic | Description |
| --- | --- |
| moisture/<sensor_id> | Moisture data is published to the moisture topic |
| temperature/<sensor_id> | Temperature data is published to the temperature topic |
| water/<sensor_id> | Water tower level data is published to the water topic |

To subscribe to all topics,
moisture/#
temperuater/#
water/#
```

## Components

## API
### Web Server
API for smart farming data

**Base URI:** *http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/src/Web-Server/*

<br>

---
## Temperature
> /api/temperature

<br>

### Authentication
>None

<br>

### GET
Returns a json list of temperatures within the given range
> /get_readings.php

<br>**Params**

| Key    | Value        | Optional  | Format                | Default            |
|--------|--------------|:---------:|-----------------------|--------------------|
| id     | {sensor id}  | Yes       | String                | all                |
| start  | {start date} | Yes       | YYYY-MM-DD hh:mm:ss   | 2020-05-01 00:00:00|
| end    | {end date}   | Yes       | YYYY-MM-DD hh:mm:ss   | now                |


<br>
 
### POST
Adds a temperature reading
>/add_reading.php

>Content-type: application/json <br>
> { "id" : "sensor id" , "reading" : "sensor reading" }

<br>

### Example
> **GET:** http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/src/Web-Server/api/temperature/get_readings.php?id=Temp+Sensor+A&start=2020-05-20&end=2020-05-28

>**POST:** { "id" : "Temp Sensor A" , "reading" : 12.34 }

<br><br>

---
## Water Level
> /api/water

<br>

### Authentication
>None

<br>

### GET
Returns a list of water levels from water tank within the given range
>/get_readings.php

<br>**Params**

| Key    | Value        | Optional  | Format                | Default            |
|--------|--------------|:---------:|-----------------------|--------------------|
| id     | {sensor id}  | Yes       | String                | all                |
| start  | {start date} | Yes       | YYYY-MM-DD hh:mm:ss   | 2020-05-01 00:00:00|
| end    | {end date}   | Yes       | YYYY-MM-DD hh:mm:ss   | now                |


<br>
 
### POST
Adds a water level reading
>/add_reading.php

>Content-type: application/json
> { "id" : "sensor id" , "reading" : "sensor reading" }

<br>

### Example
> **GET:** http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/src/Web-Server/api/water/get_readings.php?id=Water+Sensor+A&start=2020-05-20&end=2020-05-28

>**POST:** { "id" : "Water Sensor A" , "reading" : 50000 }


<br><br>

---
## Soil Moisture
> /api/soil

<br>

### Authentication
>None

<br>

### GET
Returns a list of soil moisture levels from a sensor within the given range
/get_readings.php

<br>**Params**

| Key    | Value        | Optional  | Format                | Default            |
|--------|--------------|:---------:|-----------------------|--------------------|
| id     | {sensor id}  | Yes       | String                | all                |
| start  | {start date} | Yes       | YYYY-MM-DD hh:mm:ss   | 2020-05-01 00:00:00|
| end    | {end date}   | Yes       | YYYY-MM-DD hh:mm:ss   | now                |


<br>
 
### POST
Adds a soil moisture reading
>/add_reading.php

>Content-type: application/json
> { "id" : "sensor id" , "reading" : "sensor reading" }

<br>

### Example
> **GET:** http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/src/Web-Server/api/soil/get_readings.php?id=Soil+Sensor+A&start=2020-05-20&end=2020-05-28

>**POST:** { "id" : "Soil Sensor A" , "reading" : 500 }




### Client
