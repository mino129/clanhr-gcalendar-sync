# Clan HR -> Google Calendar Sync

## Description
Small Laravel app to sync Clan HR with Google Calendar via command.

This app synchronizes info in one way meaning it syncs from clanhr via it's internal API (not oficially supported since clanhr does not provide a public API).

You can get the token found in the .env file `CLANHR_API_TOKEN` by inspecting the network traffic in clanhr and looking for the token in the requests `X-Clanhr-Auth-Token`

### How the sync works
- It takes all the events from clanhr and creates them in google calendar and tracks the event id and absence id in the database.
- Creates the events on the google calendar
- Deletes all non-existent events in clanhr from the google calendar (if it gets deleted)
- Only approved absences are synced
- Half days are properly synced too.

### Other Info
I built this sync with a very specific use case in mind, so it might not work for you. If you want to use it, you will probably have to modify the code to fit your needs. Also feel free to leave any suggestions or issues.
