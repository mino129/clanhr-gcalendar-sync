<?php

namespace App\Services;
use App\Models\CalendarEvents;
class CalendarEvent
{
    public function addCalendarEvent($lastEventId, $absenceId){
        $calendarEvent = new CalendarEvents;
        $calendarEvent->google_id = $lastEventId;
        $calendarEvent->clahnhr_id = $absenceId;
        $calendarEvent->save();
        return $calendarEvent->id ?? false;
    }

    public function deleteCalendarEvent($eventId){
        $calendarEvent = CalendarEvents::where('clahnhr_id', $eventId)->first();
        $googleId = $calendarEvent->google_id;
        $calendarEvent->delete();
        return $googleId;
    }

    public function getEventsLookup() : array{
        $calendarEvents = CalendarEvents::all();
        $eventsLookup = [];
        foreach($calendarEvents as $event){
            $eventsLookup[$event->clahnhr_id] = $event->google_id;
        }
        return $eventsLookup;
    }

}
