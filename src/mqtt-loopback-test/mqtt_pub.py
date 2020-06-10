import paho.mqtt.client as mqtt

from config import init
from models import MQTTMessage
from util import parse_message


model = MQTTMessage()

def on_publish(client, userdata, msg):
    topic, id, val = parse_message(msg)

    if id:
        print(f"[SND] {topic} : {msg.message}")
    else:
        print(f"[SND] {topic}/{id} : {msg.message}")


# public a message to the MQTT broker
def main(args, connSettings):
    global model
    
    # set dirty globals
    model.topic = args.t
    model.message = args.m

    # build client
    client = mqtt.Client()
    client.on_publish = on_publish
    client.username_pw_set(connSettings.un, connSettings.pw)

    # publish message
    client.connect(connSettings.ip)
    client.publish(model.topic, payload=model.message)


if __name__ == "__main__":
    args, connSettings = init()
    main(args, connSettings)