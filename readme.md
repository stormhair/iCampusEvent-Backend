# Introduction
CSCI587 Course Porject - iCampusEvents
# Requirements
...
# Crawler
The crawler is run once every day. Actually it submits a query to _www.usc.edu/calendar?c=Search_, seaching all the new events from now to one month ahead. A events hash table is used to avoid inserting duplicate events into the database.

|Name          |Description                                                      |
|--------------|-----------------------------------------------------------------|
|crawler.php   |reguarly submits search queries                                  |
|save_to_db.php|called by crawler.php, convert format to insert into the database|

# Database
All the events are stored in the database. There are 2 tables: _events_ and _locations_. The columns in each table are shown below:

**Events**

|Column         |Description                                |
|---------------|-------------------------------------------|
|event_id       |it is the number at the end of the page url|
|event_title    |                                           |
|event_subtitle |                                           |
|event_desc     |the description of the event               |
|start_timestamp|unix timestamp of the start time           |
|end_timestamp  |-1 means that no end time is provided      |
|location       |location id in the table _locations_       |
|page_url       |url of the event page                      |
|image_url      |url of the image, maybe null               |

**Locations**

|Column       |Description                             |
|-------------|----------------------------------------|
|location_id  |primary key of the location in the table|
|location_name|                                        |
|lat          |latitude, float                         |
|lng          |longitude, float                        |
|abbr         |abbreviation of the location (bldg code)|

# APIs
####get_all_events
Return all the events in the database
####get_future_events
Return all the events held in the future (from now)
####get_events_after
Return all the events held after a specific time stamp, the parameter _start_time_ must be provided
####get_events_related_to
Return all the events that are related to the given keyword, the parameter _keyword_ must be provided
####get_events_at
Return all the events held in the location (bldg code submitted)
####get_events_keyword_location_within_one_day/week/month
Return all the related events held in the location within one day/week/month
####get_events_keyword_location
Return all the related events held in the specific location (ignore time)
#####Return Value
Query results are returned in JSON. If a query is performed successfully, the result usually has two fields - _error_code_(usually 0, means OK) and _events_ (the campus events satisfied to the submitted constrains). A normal _event_ usually has below fields:

|Name             |Description                                        |
|-----------------|---------------------------------------------------|
|_event_title_    |                                                   |
|_event_subtitle_ |                                                   |
|_event_desc_     |                                                   |
|_start_timestamp_|                                                   |
|_end_timestamp_  |-1 means the end time of the event is not specified|
|_page_url_       |                                                   |
|_image_url_      |                                                   |
|_location_name_  |the name of the locaiton                           |
|_abbr_           |location abbreviation                              |
|_lat_            |latitude                                           |
|_lng_            |longitude                                          |
|_start_timestr_  |string of the start time of the event              |
|_end_timestr_    |string of the end time of the event                |

An example is:


_{"events":[{"event_title":"Late Night Yoga Class","event_subtitle":"","event_desc":"Every 1st Thursday of the month, join USC Rec Sports for Late Night Yoga. The class is held at the Lyon Center in the Group Exercise Room.","start_timestamp":"1449203400","end_timestamp":"1449206400","page_url":"https:\/\/studentaffairs.usc.edu\/events\/details\/?event_id=918216","image_url":"","location_name":"Lyon Center","abbr":"LRC","lat":"34.0243912","lng":"-118.2882462","start_timestr":"12\/03 23:30","end_timestr":"12\/04 00:20"}],"error_code":0}_

#####Error Code
|Error Code|Label                |Description|
|----------|---------------------|-----------|
|0         |OK                   |           |
|1         |ACTION_ERROR         |           |
|2         |DB_CONNECTION_ERROR  |           |
|3         |ACTION_NOT_EXIST     |           |
|4         |PARAMETER_NOT_SET    |           |
|5         |PARAMETER_LOGIC_ERROR|           |
