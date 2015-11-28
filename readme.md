# Introduction
CSCI587 Course Porject - iCampusEvents
# Requirements
# Crawler
# APIs
####get_all_events
Return all the events in the database
####get_future_events
Return all the events held in the future (from now)
####get_events_after
Return all the events held after a specific time stamp, the parameter _start_time_ must be provided
####get_events_related_to
Return all the events that are related to the given keyword, the parameter _keyword_ must be provided
#####Return Value
Query results are returned in JSON. If a query is performed successfully, the result usually has two fields - _error_code_(usually 0, means OK) and _events_ (the campus events satisfied to the submitted constrains). A normal _event_ usually has below fields:

|Name             |Description                                        |
|-----------------|---------------------------------------------------|
|_event_title_    |                                                   |
|_event_subtitle_ |                                                   |
|_start_timestamp_|                                                   |
|_end_timestamp_  |-1 means the end time of the event is not specified|
|_page_url_       |                                                   |
|_image_url_      |                                                   |
|_location_name_  |the name of the locaiton                           |
|_lat_            |latitude                                           |
|_lng_            |longitude                                          |

An example is:


_{"events":[{"event_title":"Lunch with a Leader: LUCY JONES","event_subtitle":"Bedrosian Center","start_timestamp":"1448913600","end_timestamp":"-1","page_url":"https:\/\/www.usc.edu\/calendar\/event\/917387","image_url":"https:\/\/web-app.usc.edu\/event-images\/105\/917387\/i_2015-08-21.jpg","location_name":"University Park Campus","lat":"34.0205688","lng":"-118.2854385"}],"error_code":0}_

#####Error Code
|Error Code|Label                |Description|
|----------|---------------------|-----------|
|0         |OK                   |           |
|1         |ACTION_ERROR         |           |
|2         |DB_CONNECTION_ERROR  |           |
|3         |ACTION_NOT_EXIST     |           |
|4         |PARAMETER_NOT_SET    |           |
|5         |PARAMETER_LOGIC_ERROR|           |
