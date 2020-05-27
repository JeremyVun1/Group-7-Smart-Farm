# Group 7 Smart Farm

## Overview

## Components

## API
### Web Server
API for smart farming data
**Base URI:** *http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/Web-Server/*

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
> **GET:** http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/Web-Server/api/temperature/get_readings.php?id=Temp+Sensor+A&start=2020-05-20&end=2020-05-21

>**POST:** { "id" : "Temp Sensor A" , "temp" : 12.34 }

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
> { "id" : "sensor id" , "water" : "sensor reading" }

<br>

### Example
> **GET:** http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/Web-Server/api/water/get_readings.php?id=Water+Sensor+A&start=2020-05-20&end=2020-05-21

>**POST:** { "id" : "Water Sensor A" , "water" : 50000 }


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
?/get_readings.php

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
> **GET:** http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/Web-Server/api/soil/get_readings.php?id=Soil+Sensor+A&start=2020-05-20&end=2020-05-21

>**POST:** { "id" : "Soil Sensor A" , "moisture" : 500 }




### Client
