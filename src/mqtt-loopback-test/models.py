
# stores MQTT message payload
class MQTTMessage:
    def __init__(self, topic="", message=""):
        self.topic = topic
        self.message = message


class MQTTConnectionSettings:
    def __init__(self, ip, un, pw, port=1883, timeout=60):
        self.ip = ip
        self.un = un
        self.pw = pw
        self.port = int(port)
        self.timeout = int(timeout)