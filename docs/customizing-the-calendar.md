[⬅️ Back to Documentation overview](/nova-calendar)

---

# Customizing the calendar

### Changing the calendar timezone
By default, calendar events are displayed in your app's timezone as specified under `timezone` in `config/app.php`.

As of version 1.5, you can override this by implementing the `timezone()` method in your `CalendarDataProvider`.

To hardcode the timezone:
```php
public function timezone(): string
{
    return 'Europe/Lisbon';
}
```

To set the calendar timezone on a per-user basis: 
```php
public function timezone(): string
{
    return $this->request->user()->timezone;
}
```

This example assumes the user has a `timezone` attribute.

As you can see, you can rely on the internal `request` attribute to get to the user. 

### Adding badges to calendar day cells
In your `CalendarDataProvider`, implement the `customizeCalendarDay()` method as follows:

```php
protected function customizeCalendarDay(CalendarDay $day) : CalendarDay
{
    if($day->start->format('d') % 2 == 0)
    {
        $day->addBadge('even');
    }
    else
    {
        $day->addBadge('odd');
    }
    
    return $day;
}
```

As badges, you could use any combination of short words or single letters, symbols or 
even emoji 🤯 to make certain calendar days stand out visually.

You can also use html in your badge, so you can use mark-up or include hero icons using svg tags:

```php
    // Html mark-up
    $event->addBadge($count .'/<b>' .$total .'</b>');
    
    // Hero icon
    $event->addBadge('<svg xmlns="http://www.w3.org/2000/svg" style="display:inline-block" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>');
```

### Changing the default menu icon and label
In your `NovaServiceProvider`, update the `tools()` method as follows:
```php
public function tools()
{
    return [
        (new NovaCalendar)->withMenuLabel('Label')->withMenuIcon('HeroIcon'),
    ];
}    
```

### Changing the first day of the week
In your calendar data provider, implement the `initialize` method and make a call to `startWeekOn()` to let the weeks start on the day of your choice. You can use the constants defined in `NovaCalendar` to specify the day.

For example, to start your weeks on wednesday:
```php
use Wdelfuego\NovaCalendar\NovaCalendar;

public function initialize(): void
{
    $this->startWeekOn(NovaCalendar::WEDNESDAY);
}
    
```

### Adding events from other sources
If the events you want to show don't have a related Nova resource, you can still add them to the calendar. In your calendar data provider, implement the `nonNovaEvents` method to push any kind of event data you want to the frontend.

The method should return an array of `Event` objects:

```php
use Wdelfuego\NovaCalendar\Event;

protected function nonNovaEvents() : array
{
    return [
        (new Event("Now until tomorrow", now(), now()->addDays(1)))
            ->addBadges('D')
            ->withNotes('This is a dynamically created event')
    ];
}
    
```

If you are going to return a long list of events here, or do a request to an external service, you can use the `startOfCalendar()` and `endOfCalendar()` methods inherited from `Wdelfuego\NovaCalendar\DataProvider\MonthCalendar` to limit the scope of your event generation to the date range that is currently being requested by the frontend. 

Any events you return here that fall outside that date range are never displayed, so it's a waste of your and your users' resources if you still generate them.

---

[⬅️ Back to Documentation overview](/nova-calendar)