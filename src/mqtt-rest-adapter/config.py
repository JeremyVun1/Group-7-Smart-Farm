import configparser
import argparse


def get_config():
    config = configparser.ConfigParser()
    config.read("config.ini")
    return config


def get_topics(config):
    return config["TOPIC"]["TOPICS"].split(",")

def build_api_map(config):
    result = {}

    api_config = config["API"]
    base_uri = api_config["BASE_URI"]

    topics = get_topics(config)
    for topic in topics:
        topic_name = f"{topic}_TOPIC"
        api_name = f"{topic}_API"
        api = f"{base_uri}{api_config[api_name]}"

        result[config["TOPIC"][topic_name]] = api

    return result


def build_handler_map(config):
    result = {}

    topics = get_topics(config)
    handler_config = config["TOPIC_HANDLING"]
    rest_topics = handler_config["REST"].split(",")
    actuator_topics = handler_config["ACTUATOR"].split(",")

    for topic in topics:
        topic_name = config["TOPIC"][f"{topic}_TOPIC"]

        if topic in rest_topics:
            result[topic_name] = "rest"
        elif topic in actuator_topics:
            result[topic_name] = "actuator"

    return result


def parse_args(config):
    mqtt_config = config["MQTT"]
    parser = argparse.ArgumentParser(prog="MQTT-REST ADAPTER", usage="python adapter.py -ip {MQTT Broker IP}", description="For pushing MQTT message events to a REST API")

    parser.add_argument("-ip", help="Specify the MQTT broker IP", default=mqtt_config["MQTT_URL"])
    parser.add_argument("-p", help="Specify the MQTT Broker port", default=mqtt_config["MQTT_PORT"], type=int)
    parser.add_argument("-un", help="Specify MQTT username for authentication", default=mqtt_config["SERVICE_ACCOUNT"])
    parser.add_argument("-pw", help="Specify MQTT password for authentication", default=mqtt_config["SERVICE_PASSWORD"])
    parser.add_argument("-t", help="Specify connection keep alive time", default=mqtt_config["KEEP_ALIVE_TIME"], type=int)

    return parser.parse_args()


def init():
    config = get_config()
    topic_api = build_api_map(config)
    handler_map = build_handler_map(config)
    args = parse_args(config)

    return topic_api, handler_map, args