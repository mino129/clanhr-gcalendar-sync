<?php

namespace App\Console\Commands;

use App\Libs\GoogleCalendar;
use App\Mail\SyncReport;
use App\Services\SyncClanGoogle;
use App\Services\CalendarEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SyncCalendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-calendar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    private GoogleCalendar $googleCalendar;

    private CalendarEvent $calendarEvent;

    private array $allCurrentAbsences = [];


    public function handle(
        SyncClanGoogle $syncClanGoogle,
        GoogleCalendar $googleCalendar,
        CalendarEvent $calendarEvent
    ) : void
    {
        $this->googleCalendar = $googleCalendar;
        $this->calendarEvent = $calendarEvent;
        $lookupTable = $syncClanGoogle->eventsThatExistDB;
        $createdEvents = [];

        $this->withProgressBar($syncClanGoogle->clanHRData["data"], function ($event) use ($lookupTable) {
            if(!array_key_exists($event["absence-id"], $lookupTable)){
                $this->createEvent($event);
                $createdEvents[] = $event;
            }else{
                $this->allCurrentAbsences[] = $event["absence-id"];
                $this->line(sprintf('Event %s already exists', $event["absence-id"]));
            }
        });

        $deletedEvents = $this->deleteAllDeletedEventsClan($lookupTable, $this->allCurrentAbsences);

        if(count($deletedEvents) > 0){
            $this->newLine(2);
            $this->info("Deleting events from Google Calendar");
            $this->withProgressBar($deletedEvents, function ($eventId) {
                $this->deleteEvent($eventId);
            });
        }

        $this->newLine(3);

        $this->sendEmailReport(
            [
                "createdEvents" => $createdEvents,
                "skippedEventsIds" => $this->allCurrentAbsences,
                "deletedEventsIds" => $deletedEvents,
                "timeOfSync" => date("s:i:H d-m-Y")
            ]
        );

    }

    private function createEvent(array $event): void{
        $lastEvent = $this->googleCalendar->createCalendarEvent($event);
        if($lastEvent === null) return;
        $this->calendarEvent->addCalendarEvent($lastEvent->id, $event["absence-id"]);
        //to prevent rate limits from the API
        sleep(2);
    }

    private function deleteEvent($eventId): void{
        $googleIdToDelete = $this->calendarEvent->deleteCalendarEvent($eventId);
        $this->googleCalendar->deleteEvent($googleIdToDelete);
    }

    private function deleteAllDeletedEventsClan($lookupTable, $allCurrentAbsences){
        $clanDBAbsences = array_keys($lookupTable);
        return array_diff($clanDBAbsences, $allCurrentAbsences);
    }

    private function sendEmailReport(array $data): void{
        $this->info("Sending email report");
        Mail::to(env("EMAILS_SENT_TO"))->send(new SyncReport($data));
    }

}
