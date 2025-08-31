<head>
    <base href="{{ asset('assets') . '/' }}">
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Dashboard - @yield('title') </title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="css/app.min.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/components.css">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="css/custom.css">
    <link rel='shortcut icon' type='image/x-icon' href='img/favicon.ico' />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    @stack('links')
    @stack('styles')
    @notifyCss
    <style>
        .notify {
            z-index: 1001 !important;
        }
    </style>
</head>
