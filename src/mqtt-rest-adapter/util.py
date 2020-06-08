import json

def parse_message(msg):
    header = msg.topic.split("/")
    topic = header[0]
    id = header[1]
    val = int(msg.payload.decode('utf-8'))
    if (val <= 0):
        val = 1
    
    return topic, id, val

def build_topic(topic):
    return f"{topic}/#"