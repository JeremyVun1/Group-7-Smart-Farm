def parse_message(msg):
    header = msg.topic.split("/")
    topic = header[0]

    if len(header) == 1:
        id = None
    elif len(header) == 2:
        id = header[1]
    
    val = msg.payload.decode('utf-8')
    
    return topic, id, val
