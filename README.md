# Virtual Conference Management System
This virtual conference management system is part of my bachelor thesis. 
It uses Jitsi as a Service for virtual meetings and provides an interactive schedule that is highly customizable.
Some of the parts used within a full fledged VCMS are only present as mockups. 
Mistakes and errors reserved. 

## Schedule
The schedule consists of timeslots, tracks and segments. 
Timeslots use a fixed start and end time to construct the schedule.
The tracks bundle segments into a clear sequence and provide the segments with a virtual meeting room. 
A segment may be supervised by a session chair, and given a virtual meeting room that deviates from the assigned track.
To customize the schedule for sets of users each track and segment may be locked behind permissions based on authentication provided by the tickets of users.

As the schedule is a central part of the project, it is available via a modal window from anywhere within the system. A printable version was also attempted using MPDF.
By default the schedule will guess the timezone of the user and adjust the timeslots accordingly. To convey the current proceedings the schedule will be filled with color to symbolize the passing of time within each segment.

## Notifications and News
Notifications are provided in a modal view, while news are provided on a separate site. New entries will be loaded dynamically. 

## Users
The users are alloted one of five possible authorization levels. These grant access to functionality parts of the website and regulate permissions during virtual meetings.
Every user is additionally allotted a ticket authenticating access to parts of the schedule.

### Authentication levels
The authorization levels used in the system are:
- Non-presenters / Viewers
- Presenters / Authors
- Session Chairs
- Program Chairs
- Administrative Staff

Viewers are granted access to participate in meetings.
Authors are assigned functionality during meetings they are schedule to present.
Session Chairs and higher authorization levels are granted full functionality during virtual meetings.
Program chairs and administrative staff are allowed access to the backend section.

## Participation hub
This is the entry point after a successful login.
This section informs the user about upcoming responsibilities and events, as well as a link to the social area, where users are free to communicate with each other.
Additionally administrators and program chairs find quick access to the management sections.

## Backend section
In this section the management of the components is performed.
Program chairs are allowed to modify and create news and notifications, as well as manage information about papers. Furthermore the segments, tracks and timeslots used in the schedule may be edited.
Administrative staff gains further access to the authorization system, user and ticket management, as well as the statistics.

### Statistics
The system gathers events of the JaaS integration to provide a statistic for rooms and users.
Statistics for users are split into the different rooms visited. Additionally the total amount of time spent within virtual rooms is listed.
Room statistics inform about the time spans of attendees, as well as the total amount of time the room has been accessed.
This little statistic can be extended thoroughly and may be used to present attendees of the virtual conference with certificates of participation.

## Mockup components
Some mockup components are present to present what can be added to the project.