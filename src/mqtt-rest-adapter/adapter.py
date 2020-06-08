import paho.mqtt.client as mqtt
import json
import requests

from config import init, get_config
from util import *

topic_api = {}
config = get_config()

def on_connect(client, userdata, flags, rc):
    print(f"Connected with result code {str(rc)}")

    topic_config = config["TOPIC"]

    topics = topic_config["TOPICS"].split(",")
    for topic in topics:
        topic_name = f"{topic}_TOPIC"
        subscribe_name = build_topic(topic_config[topic_name])
        client.subscribe(subscribe_name)
        print(f"subscribed to {subscribe_name}")


def on_message(client, userdata, msg):
    topic, id, val = parse_message(msg)

    api = topic_api[topic]
    json_string = f"\"id\":\"{id}\",\"reading\":{val}"
    json_string = "{" + json_string + "}"
    
    response = requests.post(api, data=json_string)
    print(f"POST {api} : {json_string}")
    print(response.text)


def main():
    global topic_api

    topic_api, args = init()

    client = mqtt.Client()
    client.on_connect = on_connect
    client.on_message = on_message
    client.username_pw_set(args.un, args.pw)

    client.connect(args.ip, args.p, args.t)

    client.loop_forever()


if __name__ == "__main__":
    main()