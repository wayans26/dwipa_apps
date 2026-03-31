<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>Visitor Registration</title>

    <script src="{{ asset('rukada/js/jquery.min.js') }}"></script>
    <script src="{{ asset('rukada/js/popper.min.js') }}"></script>
    <script src="{{ asset('rukada/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('rukada/plugins/bootstrap-datatable/js/jquery.dataTablesNew.min.js') }}"></script>
    <script src="{{ asset('rukada/plugins/bootstrap-datatable/js/dataTables.rowGroup.min.js') }}"></script>
    <script src="{{ asset('rukada/plugins/bootstrap-datatable/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('rukada/plugins/bootstrap-datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('rukada/plugins/bootstrap-datatable/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('rukada/plugins/alerts-boxes/js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('rukada/plugins/alerts-boxes/js/sweet-alert-script.js') }}"></script>
    <script src="{{ asset('rukada/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('rukada/plugins/material-datepicker/js/moment.min.js') }}"></script>
    <script src="{{ asset('rukada/plugins/material-datepicker/js/bootstrap-material-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('rukada/plugins/material-datepicker/js/ja.js') }}"></script>

    <!-- notifications css -->
    <link rel="stylesheet" href="{{ asset('rukada/plugins/notifications/css/lobibox.min.css') }}" />

    {{-- Css --}}
    <link href="{{ asset('rukada/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('rukada/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('rukada/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('rukada/css/animate.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('rukada/css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('rukada/css/sidebar-menu.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('rukada/css/app-style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('rukada/plugins/bootstrap-datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css">
    <link href="{{ asset('rukada/plugins/bootstrap-datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css">
    <!--material datepicker css-->
    <link rel="stylesheet"
        href="{{ asset('rukada/plugins/material-datepicker/css/bootstrap-material-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <!--Bootstrap Datepicker-->
    <link href="{{ asset('rukada/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&display=swap"
        rel="stylesheet">

    <!--Switchery-->
    <link href="{{ asset('rukada/plugins/switchery/css/switchery.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('rukada/plugins/bootstrap-switch/bootstrap-switch.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --animate-duration: 300ms;
            --animate-delay: 0.9s;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .disabledbutton {
            pointer-events: none;
            opacity: 0.4;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .separator {
            display: flex;
            align-items: center;
            text-align: center;
        }

        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #000;
        }

        .separator:not(:empty)::before {
            margin-right: .25em;
        }

        .separator:not(:empty)::after {
            margin-left: .25em;
        }

        .title-dinas {
            text-align: center;
            width: 100%;
            font-weight: 900;
            font-family: "Cinzel Decorative", serif;
            color: black;
            font-size: 1.1em;
        }

        /* Class for basic checkbox styling */
        .checkbox-custom {
            -webkit-appearance: none;
            /* Removes default styling */
            -moz-appearance: none;
            appearance: none;
            width: 24px;
            /* Size of the checkbox */
            height: 24px;
            border: 2px solid #2dce89;
            /* Green border */
            border-radius: 5px;
            /* Rounded corners */
            background-color: #fff;
            /* White background */
            position: relative;
            /* Position for checkmark */
            cursor: pointer;
            /* Pointer cursor on hover */
            transition: background-color 0.3s, border-color 0.3s;
        }

        /* Class for checked state of checkbox */
        .checkbox-custom:checked {
            background-color: #2dce89;
            /* Green background when checked */
            border-color: #2dce89;
            /* Green border when checked */
        }

        /* Class for custom checkmark style */
        .checkbox-custom:checked::before {
            content: '\2714';
            /* Unicode character for checkmark */
            color: white;
            /* White checkmark color */
            font-size: 16px;
            /* Size of checkmark */
            position: absolute;
            /* Positioning of checkmark */
            top: 50%;
            /* Center checkmark vertically */
            left: 50%;
            /* Center checkmark horizontally */
            transform: translate(-50%, -50%);
            /* Adjust position */
        }

        /* Class for hover state */
        .checkbox-custom:hover {
            border-color: #2dce89;
            /* Slightly darker green border when hovering */
        }

        /* Class for disabled state */
        .checkbox-custom:disabled {
            cursor: not-allowed;
            /* Disable cursor on hover */
            opacity: 0.6;
            /* Make the checkbox appear faded */
            border-color: #2dce89;
            /* Green border for disabled */
            background-color: #ffffff;
            /* White background for disabled (when not checked) */
        }

        /* Class for checkmark style for disabled checkbox */
        .checkbox-custom:disabled:checked {
            cursor: not-allowed;
            /* Disable cursor on hover */
            opacity: 0.6;
            /* Make the checkbox appear faded */
            border-color: #2dce89;
            /* Green border for disabled */
            background-color: #2dce89;
        }

        .checkbox-container {
            display: flex;
            align-items: flex-start;
            /* penting supaya checkbox sejajar dengan baris pertama */
            gap: 8px;
        }

        .label-group {
            display: flex;
            flex-direction: column;
        }

        .main-label {
            cursor: pointer;
        }

        .desc-label {
            color: #6c757d;
            /* optional biar seperti helper text */
            font-size: 14px;
        }
    </style>

    <style lang="scss">
        .v-select,
        .vs__dropdown-toggle {
            height: auto !important;
        }
    </style>


</head>

<body>

    <div id="app" style="user-select: text">
        <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
                class="bi bi-arrow-up-short"></i></a>
    </div>

    <script src="https://apis.google.com/js/platform.js" async="" gapi_processed="true"></script>
    @vite('resources/js/Master/index.js')

    <script src="{{ asset('rukada/plugins/simplebar/js/simplebar.js') }}"></script>
    <script src="{{ asset('rukada/js/waves.js') }}"></script>
    <script src="{{ asset('rukada/js/sidebar-menu.js') }}"></script>
    <script src="{{ asset('rukada/js/app-script.js') }}"></script>
    <!--notification js -->
    <script src="{{ asset('rukada/plugins/notifications/js/lobibox.min.js') }}"></script>
    <script src="{{ asset('rukada/plugins/notifications/js/notifications.min.js') }}"></script>

    <!--Switchery Js-->
    <script src="{{ asset('rukada/plugins/switchery/js/switchery.min.js') }}"></script>

    <!--Bootstrap Switch Buttons-->
    <script src="{{ asset('rukada/plugins/bootstrap-switch/bootstrap-switch.min.js') }}"></script>
</body>

</html>
