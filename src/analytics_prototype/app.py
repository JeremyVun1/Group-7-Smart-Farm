from flask import Flask, render_template, url_for
import requests
import json
import copy
import math

app = Flask(__name__)

class DataRow:
    def __init__(self, label, x, y):
        self.label = label
        self.x = x
        self.y = y
        self.count = 1


class DataSeries:
    def __init__(self, chart_type, chart_name, chart_datapoints):
        self.name = chart_name
        self.type = chart_type
        self.datapoints = chart_datapoints
        self.mean = self.y_mean()
        self.variance = self.calc_variance()
        self.std_dev = self.calc_std_dev()

    def calc_std_dev(self):
        return math.sqrt(self.variance)
    
    def calc_variance(self):
        variances = []
        for dp in self.datapoints:
            x = dp.y - self.mean
            variances.append(x * x)

        return sum(variances)

    def x_mean(self):
        result = self.datapoints[0].x

        count = 1
        for i in range(0, len(self.datapoints)):
            result = rolling_average(result, self.datapoints[i].x, count)
            count = count + 1
        return result

    def y_mean(self):
        result = self.datapoints[0].y

        count = 1
        for i in range(0, len(self.datapoints)):
            result = rolling_average(result, self.datapoints[i].y, count)
            count = count + 1
        return result

    def get_vars(self, x_mean, y_mean):
        x_vars = []
        y_vars = []

        for item in self.datapoints:
            x_vars.append(item.x - x_mean)
            y_vars.append(item.y - y_mean)

        return x_vars, y_vars


def get_data():
    url = "http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/src/Web-Server/api/temperature/get_readings.php?start=2020-05-20"
    data = requests.get(url)
    data = json.loads(data.text)
    return data

def format_data(data):
    # parse out the different sensors into their own data series
    dataseries = {}
    
    for item in data:
        if item["sensor_id"] not in dataseries:
            dataseries[item["sensor_id"]] = []
        
        dataseries[item["sensor_id"]].append(item)

    # build data rows from each sensor data series
    # print(dataseries)
    result = []
    for sensor_id in dataseries:
        datapoints = []

        series = dataseries[sensor_id]
        for i, item in enumerate(series):
            # print(item)
            datapoints.append(DataRow(label=item["datetime"], x=int(i), y=float(item["temperature"])))

        result.append(DataSeries(chart_type="line", chart_name=sensor_id, chart_datapoints=datapoints))

    return result


def multiply_arrays(arr1, arr2):
    if (len(arr1) != len(arr2)):
        raise Exception("cannoto multiply arrays that do not match in size")

    result = []
    for i in range(len(arr1)):
        result.append(arr1[i] * arr2[i])

    return sum(result)


def linear_regression(dataseries):
    x_mean = dataseries.x_mean()
    y_mean = dataseries.y_mean()
    x_vars, y_vars = dataseries.get_vars(x_mean, y_mean)

    m = multiply_arrays(x_vars, y_vars) / multiply_arrays(x_vars, x_vars)
    c = y_mean - (m * x_mean)

    return m, c


def rolling_average(avg, new_val, n):
    if n == 1:
        return new_val
    else:
        avg = avg - (avg / n)
        avg = avg + new_val / n
        return avg


# array of DataSeries
def aggregate(dataseries):
    if len(dataseries) == 0:
        return

    # grab the first data series
    result = DataSeries(dataseries[0].type, "aggregate", copy.deepcopy(dataseries[0].datapoints))

    for i in range(0, len(dataseries)):
        ds = dataseries[i]

        # roll in datapoints from ds into result
        for j in range(0, len(dataseries[i].datapoints)): 
            # the data series to roll in has a greater length than the data series we are triyng to roll into
            if j >= len(result.datapoints):
                result.datapoints.append(DataRow(label=ds.datapoints[j].label, x=ds.datapoints[j].x, y=ds.datapoints[j].y))
            else:
                result.datapoints[j].x = rolling_average(result.datapoints[j].x, ds.datapoints[j].x, result.datapoints[j].count)
                result.datapoints[j].y = rolling_average(result.datapoints[j].y, ds.datapoints[j].y, result.datapoints[j].count)
                result.datapoints[j].count = result.datapoints[j].count + 1

    return result


def build_regression_series(m, c, count):
    dp = []
    for x in range(count):
        y = m*x + c
        # TODO - need to build correct datetime labels going into the future
        dp.append(DataRow(label="", x=x, y=y))
    result = DataSeries(chart_type="line", chart_name="regression",  chart_datapoints=dp)

    return result


@app.route('/')
def index():
    # make the api call and get the data
    data = get_data()

    # sort the data by sensor id
    dataseries = format_data(data)

    # combine all sensor id's into an aggregate dataseries
    aggregated_dataseries = aggregate(dataseries)

    # build a linear regression line
    m, c = linear_regression(aggregated_dataseries)
    regression_series = build_regression_series(
        m,
        c,
        len(aggregated_dataseries.datapoints) + 10 # predict 10 more samples into the future
    )

    dataseries.append(aggregated_dataseries)
    dataseries.append(regression_series)
    return render_template("index.html", series=dataseries)



app.run(debug=True, host='0.0.0.0', use_reloader=False)