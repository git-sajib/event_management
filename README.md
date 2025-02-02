**Event Management System**

**Overview**
The Event Management System is a web-based application that allows users to create, manage, and register for events. It includes user authentication, event CRUD operations, attendee registration, and event reporting.

**Features**
•	User Authentication: Secure login and registration with password hashing.
•	Event Management: Users can create, update, delete, and view events.
•	Attendee Registration: Allows users to register for events while ensuring maximum capacity is not exceeded.
•	Event Dashboard: Events are displayed in a paginated, sortable, and filterable format.
•	Event Reports: Admins can download attendee lists from admin dashboard in CSV format for specific events with registered attendees list.
•	Search Functionality: Implemented search functionality event name or attendee email.
•	JSON API: Implemented JSON API for fetching event details after creating event or attendee lists of each event. 
          -> Example: https://eventmanagement.texagig.com/api/events.php?id=34

Installation Instructions

**Clone the repository**:
•	git clone https://github.com/git-sajib/event_management

*Hosting*
The project is hosted on CPanel (Shared Hosting)

**Live Project URL**:
•	https://eventmanagement.texagig.com/

****Testing Credentials*****

  **User Credentials**:
    • Email: testuser
    • Password: user123
  
  **Admin Credential**:
    • Username: samiul
    • Password: admin123

**Local Setup**
1.	Run the project locally:
  Start Apache and MySQL via XAMPP/WAMP.
  Place the project in the htdocs folder.
  Open http://localhost/event_management/dashboard.php in a browser.
2.	Set up the database:
  Import event_management.sql into your MySQL database.
  Configure database credentials in includes/db.php

