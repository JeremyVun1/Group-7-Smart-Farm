# Group 7 Smart Farm

## Overview
This repository contains all the code and documentation for an IoT smart farm implementation

## Example
An existing example project with infrastructure has been set up.
View the web server at:
*http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/src/Web-Server/*

## How to set up your own IoT Smartfarm
### Set up Arduino sensors
```
1. Upload any of the following sketches to arduino
    - Moisture-sensor
    - WaterLevel-sensor
    - Temperature-sensor

2. Make sure pins are correctly set
```

### Set up Mosquitto MQTT Broker
```
1. Run mosquitto MQTT with the supplied mosquitto.conf. For example, from the mosquitto folder
./mosquitto -c mosquitto.conf -v

2. To configure MQTT user accounts
./mosquitto_passwd -b "users.txt" <username> <password>
```

### Set up Serial-to-MQTT gateway
```
1. Install node.js and node-red
https://nodered.org/docs/faq/node-versions
https://nodered.org/docs/getting-started/local

2. Run Node-Red with following command,
node-red

4. Go to the node-red web app and import flows.json from the "src/node-red" folder (menu button top right)
http://localhost:1880 (default url)

5. Double click the "MQTT Send" node and add your MQTT broker with correct url and port
Under security, use a username/password:
username: "user"
pasword: "password"

5. Connect arduino via USB Serial, make sure correct COM port is selected in the "COM node"

6. Press the deploy button in node-red
- Arduino should now be sending json strings through serial, parsed into MQTT JSON structure and sent to the mosquitto server
- Change MQTT node to point to where you set up your MQTT broker as necessary
```

### Set up the MQTT-REST adapter
```
The MQTT-REST adapter listens for updates from the MQTT broker and pushes them to REST API in the cloud
1. Install python 3 and pipenv

2. In "mqtt-rest-adapter" folder run,
pipenv install

3. Set configuration options in config.ini

4. Run the adapter,
pipenv run python adapter.py -h <MQTTBroker IP>
- If you do not specify an MQTTBroker IP, it will use the default that is set in config.ini
```

## API
### MQTT broker
| MQTT Topic | Description | Payload |
| --- | --- | --- |
| m/<sensor_id> | Soil Moisture data is published to this topic | int value |
| t/<sensor_id> | Temperature data is published to this topic | int value |
| w/<sensor_id> | Water tower level data is published to this topic | int value |
| v/<sensor_id> | Diagnostic internal voltage level for monitoring battery level is published to this topic | int value |

### Web Server
API for smart farming data

**Base URI:** *http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/src/Web-Server/*

#### GET Endpoints
- /api/soil/get_readings.php
- /api/water/get_readings.php
- /api/temperature/get_readings.php
- /api/voltage/get_readings.php

Example
http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/Web-Server/api/temperature/get_readings.php?id=Temp+Sensor+A&start=2020-05-20&end=2020-05-28

PARAMS

| Key    | Value        | Optional  | Format                | Default            |
|--------|--------------|:---------:|-----------------------|--------------------|
| id     | {sensor id}  | Yes       | String                | all                |
| start  | {start date} | Yes       | YYYY-MM-DD hh:mm:ss   | 2020-05-01 00:00:00|
| end    | {end date}   | Yes       | YYYY-MM-DD hh:mm:ss   | now                |

#### POST Endpoints
- /api/soil/add_reading.php
- /api/water/add_reading.php
- /api/temperature/add_reading.php
- /api/voltage/add_reading.php

| Content-type | Data |
| --- | --- |
| application/json | {"id": "sensor_id", "reading": "sensor_reading" | 
