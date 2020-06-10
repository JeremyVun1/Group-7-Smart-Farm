# Overview
Tools for testing a remote MQTT broker
- Subscribe to a topic on the remote broker
- publish messages to the remote broker

# How to use
```
1. Install python 3

2. Install pipenv

3. Install dependencies
pipenv install

4. Set the MQTT connection details in config.ini
Defaults have already been set for access to the Smart Farm MQTT broker
```

```
1. To subscribe to a topic and listen
pipenv run python mqtt_sub -t <topic>

Message events will now appear in console for the subscribed topic

2. To publish a message to a topic
pipenv run python mqtt_pub -t <topic> -m <message>
```