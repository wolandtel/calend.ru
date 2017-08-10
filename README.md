# MOTIVATION
calend.ru send reminders one day before a date. And I always forget about the date next day. This simple tool whill remind you about the date in a day.

# REQUIREMENTS

* heirloom-mailx
* php-curl

# INSTALLATION

* `git clone https://git.woland.me//calend.ru /usr/local && cd /usr/local/calend.ru`.
* Copy **config.sample.php** to **config.php**.
* Fill out **config.php** with the actual values (username and password — your _calend.ru_ credentials).
* Change the user in the **cron.d/calend.ru** to appropriate one. Also check _php_ executable name in this file.
* Copy **cron.d/calend.ru** to **/etc/cron.d**
* Turn off email notifications on _http://www.calend.ru_.
