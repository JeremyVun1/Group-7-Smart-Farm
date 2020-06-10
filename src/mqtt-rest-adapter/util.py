import json

def parse_message(msg):
    header = msg.topic.split("/")
    topic = header[0]
    id = header[1]

    try:
        val = int(msg.payload.decode('utf-8'))
    except:
        val = 0

    print(f"{topic}{id}{val}")
    
    return topic, id, val


def build_topic(topic):
    return f"{topic}/#"
