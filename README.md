# Group 7 Smart Farm

## Overview

## Components

## API
### Web Server
API for smart farming data
**Base URI:** *http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/Web-Server/*

<br>

## Temperature
> /api/temperature

<br>

### Authentication
>None

<br>

### GET
Returns a list of temperatures within the given range
| Params | 				|
|--------|--------------|
| start  | {start date} |
| end    |  {end date}  |


<br>
 
### POST
Adds a temperature reading
>Content-type: application/json
> { "id" : "sensor id" , "reading" : "sensor reading" }

<br>

### Example
> **GET:** http://aws/api/temperature?start=2020-05-20&end=2020-05-21



<br><br>

## Water Level


> /api/water

<br>

### Authentication
>None

<br>

### GET
Returns a list of water levels from water tank within the given range
| Params | 				 |
|--------|---------------|
| id     | { tank id }   |
| start  | { start date }|
| end    | { end date }  |


<br>
 
### POST
Adds a water level reading
>Content-type: application/json
> { "id" : "sensor id" , "reading" : "sensor reading" }

<br>

### Example
> **GET:** http://aws/api/water?start=2020-05-20&end=2020-05-21



<br><br>

## Soil Moisture


> /api/soil

<br>

### Authentication
>None

<br>

### GET
Returns a list of soil moisture levels from a sensor within the given range
| Params | 				  |
|--------|----------------|
| id     | { device id }  |
| start  | { start date } |
| end    | { end date }   |


<br>
 
### POST
Adds a soil moisture reading
>Content-type: application/json
> { "id" : "sensor id" , "reading" : "sensor reading" }

<br>

### Example
> **GET:** http://aws/api/soil?start=2020-05-20&end=2020-05-21




### Client
