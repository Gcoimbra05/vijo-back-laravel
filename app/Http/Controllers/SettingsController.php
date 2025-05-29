<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class SettingsController extends Controller
{
    public static function getTimezones()
    {
        return [
            [
                "region" => "America",
                "timezones" => [
                    [
                        "key" => "America/Anchorage",
                        "value" => "UTC/GMT -08:00 - Alaska"
                    ],
                    [
                        "key" => "America/Chicago",
                        "value" => "UTC/GMT -05:00 - Central Time, US & Canada"
                    ],
                    [
                        "key" => "America/Dawson_Creek",
                        "value" => "UTC/GMT -07:00 - Arizona"
                    ],
                    [
                        "key" => "America/Denver",
                        "value" => "UTC/GMT -06:00 - Mountain Time, US & Canada"
                    ],
                    [
                        "key" => "America/Glace_Bay",
                        "value" => "UTC/GMT -03:00 - Atlantic Time, Canada"
                    ],
                    [
                        "key" => "America/Los_Angeles",
                        "value" => "UTC/GMT -07:00 - Pacific Time, US & Canada"
                    ],
                    [
                        "key" => "America/New_York",
                        "value" => "UTC/GMT -04:00 - Eastern Time, US & Canada"
                    ]
                ]
            ],
            [
                "region" => "Asia",
                "timezones" => [
                    [
                        "key" => "Asia/Kolkata",
                        "value" => "UTC/GMT +05:30 - Chennai, Kolkata, Mumbai, New Delhi"
                    ],
                    [
                        "key" => "Asia/Manila",
                        "value" => "UTC/GMT +08:00 - Asia/Manila"
                    ]
                ]
            ],
            [
                "region" => "UTC",
                "timezones" => [
                    [
                        "key" => "UTC",
                        "value" => "UTC/GMT +00:00 - Coordinated Universal Time"
                    ]
                ]
            ]
        ];
    }
}