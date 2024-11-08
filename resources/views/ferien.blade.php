<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferien Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <style>
        .container {
            max-width: 700px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-right mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Zurück</a>
        </div>
        <h1 class="text-center">Ferien verwalten</h1>
        @if (auth()->check() && auth()->user()->is_admin)

        <h2 class="mt-4">Benutzer Ferienübersicht</h2>
        <div class="card mt-4">
            <div class="card-body">
            <form method="POST" action="{{ route('update.ferien') }}">
    @csrf
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Name</th>
                <th style="width: calc(100% - 76.33%);">Ferientage</th>
                <th style="width: calc(100% - 76.33%);">Aktion</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>
                    <input type="number" name="ferien[{{ $user->id }}]" value="{{ $user->ferien }}" class="form-control" required>
                </td>
                <td>
                    <button type="submit" class="btn btn-primary">Aktualisieren</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</form>
            </div>
        </div>

        <h2 class="mt-4">Notizen</h2>
<form method="POST" action="{{ route('update.ferien.notes') }}">
    @csrf
    <div class="form-group">
        <textarea class="form-control" name="ferien_notes" rows="15">{{ $ferienNotes->ferien_notes ?? '' }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Notizen speichern</button>
</form>

        @endif
    </div>

    
</body>
</html>
