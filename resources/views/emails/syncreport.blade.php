<h3>
    Hi, here's the report for the sync job ran for ClanHR vacation days to Google Calendar at {{$emailInfo["timeOfSync"]}}.
</h3>

@if(count($emailInfo["createdEvents"]) > 0)
<h4>Created Events on Google Calendar</h4>
<ul>
    @foreach($emailInfo["createdEvents"] as $event)
        <li>
            @if($event["absence-type"] == "vacation")
                Ferías:
            @else
                Ausencia:
            @endif
            {{$event["name"]}} : {{$event["start-date"]}} - {{$event["end-date"]}}
        </li>
    @endforeach
</ul>
@else
    <p>No events we're created</p>
@endif

@if(count($emailInfo["skippedEventsIds"]) > 0)
<h4>Skipped Events because they are already created</h4>
<ul>
    @foreach($emailInfo["skippedEventsIds"] as $event)
        <li>
            ID: {{$event}}
        </li>
    @endforeach
</ul>
@else
    <p>No events we're skipped</p>
@endif

@if(count($emailInfo["deletedEventsIds"]) > 0)
<h4>Deleted events because they no longer exist</h4>
<ul>
    @foreach($emailInfo["deletedEventsIds"] as $event)
        <li>
            ID: {{$event}}
        </li>
    @endforeach
</ul>
@else
    <p>No events we're deleted</p>
@endif
