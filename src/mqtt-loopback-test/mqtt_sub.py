import paho.mqtt.client as mqtt

from config import init
from models import MQTTMessage
from util import parse_message


model = MQTTMessage()

def on_connect(client, userdata, flags, rc):
    global model

    print(f"Connected with result code {str(rc)}")
    
    client.subscribe(model.topic)
    print(f"subscribed to {model.topic}")


def on_message(client, userdata, msg):
    print("received message!")
    topic, id, val = parse_message(msg)

    if id:
        print(f"[RECV] {topic}/{id} : {val}")
    else:
        print(f"[RECV] {topic} : {val}")


# public a message to the MQTT broker
def main(args, connSettings):
    global model
    
    # set dirty globals
    model.topic = args.t

    # build client
    client = mqtt.Client()
    client.on_connect = on_connect
    client.on_message = on_message
    client.username_pw_set(connSettings.un, connSettings.pw)

    # make connection
    client.connect(connSettings.ip, connSettings.port, connSettings.timeout)

    client.loop_forever()


if __name__ == "__main__":
    args, connSettings = init()
    main(args, connSettings)