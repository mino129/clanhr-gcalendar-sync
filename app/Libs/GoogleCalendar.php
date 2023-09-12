<?php

namespace App\Libs;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class GoogleCalendar
{

    public function createCalendarEvent($clanEvent){
        //disable this on first calendar sync because some vacations days we're never approved lol
        if($clanEvent['state'] === 'pendent' || $clanEvent['state'] === 'rejected' ) return;
        $event = new Event;
        $event->name = $this->eventNameFormmatingforGoogle($clanEvent['name'], $clanEvent["absence-type"]);
        $duration = $this->getDatesBasedDuration((float) $clanEvent['duration'], $clanEvent['start-date'], $clanEvent['end-date']);
        if($clanEvent['duration-type'] == 'partial-day'){
            $event->startDateTime = $duration["startDate"];
            $event->endDateTime = $duration["endDate"];
        }else{
            $event->startDate = $duration["startDate"];
            $event->endDate = $duration["endDate"];
        }
        $event->description = 'ID Férias:'.$clanEvent['absence-id'];
        return $event->save();
    }

    private function getDatesBasedDuration(float $duration, $startDate, $endDate) : array{
        if($duration == 0.5) {
            $startDate = Carbon::parse($startDate)->setHour(8);
            $endDate = Carbon::parse($startDate)->addHours(5);
            return ["startDate" => $startDate, "endDate" => $endDate];
        }
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        return ["startDate" => $startDate, "endDate" => $endDate];
    }

    public function getAllGCalendarEvents(){
        return Event::get(
            Carbon::createFromDate(2022, 1, 1),
            Carbon::createFromDate( (int) date("Y"), 12, 31)
        )->toArray();
    }

    public function deleteEvent($eventId) : void{
        $event = Event::find($eventId);
        $event->delete();
    }

    private function eventNameFormmatingforGoogle(string $eventName, string $eventType): string{
        $eventTypeName = ($eventType == "vacations") ? "Férias" : "Ausência";
        $nameParts = explode(' ', $eventName);
        $name = $eventName;
        if (count($nameParts) > 2) $name = $nameParts[0] . ' ' . end($nameParts);
        return $name . ' - ' . $eventTypeName;
    }

}
