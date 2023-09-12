<?php

namespace App\Services;
use App\Libs\ClanHR;
use App\Libs\GoogleCalendar;

class SyncClanGoogle
{
    public array $clanHRData;
    public array $currentGoogleEvents;

    public array $eventsThatExistDB;

    public function __construct(
        private readonly CalendarEvent $calendarEvent,
        private readonly ClanHR $clanHR,
        private readonly GoogleCalendar $googleCalendar,
    ){
        $this->clanHRData = $this->clanHR->getVacationData();
        $this->validateClanData();
        $this->currentGoogleEvents = $this->googleCalendar->getAllGCalendarEvents();
        $this->eventsThatExistDB = $this->calendarEvent->getEventsLookup();
    }

    private function validateClanData() : void{
        if($this->clanHRData === false) throw new \Exception('Failed to retrieve vacation data from ClanHR API');
    }


}
