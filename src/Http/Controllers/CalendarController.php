<?php

/*
 * © Copyright 2022 · Willem Vervuurt, Studio Delfuego
 * 
 * You can modify, use and distribute this package under one of two licenses:
 * 1. GNU AGPLv3
 * 2. A perpetual, non-revocable and 100% free (as in beer) do-what-you-want 
 *    license that allows both non-commercial and commercial use, under conditions.
 *    See LICENSE.md for details.
 * 
 *    (it boils down to: do what you want as long as you're building and/or
 *     using calendar views, but don't embed this package or a modified version
 *     of it in free or paid-for software libraries and packages aimed at developers).
 */

namespace Wdelfuego\NovaCalendar\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Laravel\Nova\Http\Requests\NovaRequest;
use Wdelfuego\NovaCalendar\DataProvider\Calendar;
use Wdelfuego\NovaCalendar\Interface\CalendarDataProviderInterface;

class CalendarController extends BaseController
{
    private $request;
    private $dataProvider;

    public function __construct(NovaRequest $request, CalendarDataProviderInterface $dataProvider)
    {
        $this->request = $request;
        $this->dataProvider = $dataProvider;
    }

    public function getCalendarViews() : array
    {
        return [
            'calendar_views' => $this->sanitizeCalendarViews($this->dataProvider->calendarViews())
        ];
    }
    
    public function getMonthCalendarData($year = null, $month = null)
    {
        $year  = is_null($year)  || !is_numeric($year)  ? now()->year  : intval($year);
        $month = is_null($month) || !is_numeric($month) ? now()->month : intval($month);
        
        while($month > 12) { $year += 1; $month -= 12; }
        while($month < 1)  { $year -= 1; $month += 12; }
        
        $this->dataProvider->setRequest($this->request)->setYearAndMonth($year, $month);
            
        return [
            'year' => $year,
            'month' => $month,
            'title' => $this->dataProvider->title(),
            'columns' => $this->dataProvider->daysOfTheWeek(),
            'weeks' => $this->dataProvider->calendarWeeks(),
            'styles' => array_replace_recursive($this->defaultStyles(), $this->dataProvider->eventStyles()),
        ];
    }
    
    public function defaultStyles() : array
    {
        return [
            'default' => [
                'color' => '#fff',
                'background-color' => 'rgba(var(--colors-primary-500), 0.9)',
            ]
        ];
    }

    private function sanitizeCalendarViews(array $cv): array
    {
        $out = [];
        if ($cv == Calendar::A_AVAILABLE_VIEWS) {
            $out = $cv;
        } else {
            foreach ($cv as $view) {
                if (in_array($view, Calendar::A_AVAILABLE_VIEWS) && !in_array($view, $out)) {
                    $out[] = $view;
                }
            }
        }

        return $out;
    }
}
