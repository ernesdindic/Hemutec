<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - {{ $user->name }}</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Back Button -->
        <div class="text-right mt-3 mb-3">
            <a href="{{ route('user-reports') }}" class="btn btn-secondary">Zurück</a>
        </div>
        <h2>{{ $user->name }} - Profil</h2>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        
        <!-- Month Filter Form -->
        <div class="card-body">
            <form method="GET" action="{{ route('user-profile', $user->id) }}" class="mb-4" style="width: calc(100% - 76.33%);">
                <div class="form-group">
                    <label for="month">Monat auswählen:</label>
                    <input type="month" id="month" name="month" class="form-control" value="{{ $selectedMonth }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Filtern</button>
            </form>
        </div>

        <!-- Reports Table Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Berichte für den ausgewählten Monat</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Arbeitszeit</th>
                            <th>Kundenname</th>
                            <th>Arbeitsbeschreibung</th>
                            <th>Arbeitsart</th>
                            <th>Selectline</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($report->datum)->format('d.m.Y') }}</td>
                                <td>{{ $report->vrijeme_rada }}</td>
                                <td>{{ $report->ime_stranke }}</td>
                                <td>{{ $report->opis_rada }}</td>
                                <td>{{ $report->tip_posla }}</td>
                                <td class="text-center">
                                    @if($report->tip_posla == 'produktiv' || $report->tip_posla == 'telefonsko produktivan')
                                        <input type="checkbox" class="styled-checkbox selectline-checkbox" data-id="{{ $report->id }}" {{ $report->selectline ? 'checked' : '' }}>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
