import configparser
import argparse

from models import MQTTConnectionSettings


def get_config():
    config = configparser.ConfigParser()
    config.read("config.ini")
    return config


def parse_args():
    parser = argparse.ArgumentParser(prog="MQTT-REST ADAPTER", usage="python adapter.py -ip {MQTT Broker IP}", description="For pushing MQTT message events to a REST API")

    parser.add_argument("-t", help="Specify an MQTT topic", type=str, required=True)
    parser.add_argument("-m", help="Specify an MQTT mnessage", type=str, default="hello world!")

    return parser.parse_args()


def init():
    config = get_config()["MQTT"]

    connectionSettings = MQTTConnectionSettings(
        ip=config["MQTT_URL"],
        un=config["SERVICE_ACCOUNT"],
        pw=config["SERVICE_PASSWORD"],
        port=config["MQTT_PORT"],
        timeout=config["KEEP_ALIVE_TIME"]
    )

    args = parse_args()
    return args, connectionSettings