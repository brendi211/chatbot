<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        @vite(['resources/css/app.css'])
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>

        <div id="app" class="page">
            <div>
                <chat-component api-endpoint="/botman"
                                  user-id="{{ Session::getId() }}"
                                  :smessages="{{$messages}}"
                >

                </chat-component>
            </div>
        </div>
        @vite(['resources/js/app.js'])
    </body>
</html>
