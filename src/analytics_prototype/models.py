class DataRow:
    def __init__(self, x, y):
        self.x = x
        self.y = y


class DataSeries:
    def __init__(self, chart_type, chart_name, chart_datapoints):
        self.name = chart_name
        self.type = chart_type
        self.datapoints = chart_datapoints