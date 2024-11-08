<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spesen</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    <style>
        .container { max-width: 800px; }
        h1 { margin-top: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-right mt-3 mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Zurück</a>
        </div>
        <h1 class="text-center">Spesen</h1></br>

        <div class="row">
            <!-- Form für neue Route -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header text-center">
                        <h5 class="card-title">Neue Route hinzufügen</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('spesen.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="datum">Datum</label>
                                <input type="date" id="datum" name="datum" placeholder="Fahrtdatum" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="standort">Standort</label>
                                <input type="text" id="standort" name="standort" class="form-control" placeholder="Standort eingeben" required>
                            </div>
                            <div class="form-group">
                                <label for="kilometer">Kilometer</label>
                                <input type="number" id="kilometer" name="kilometer" class="form-control" placeholder="Anzahl der Kilometer eingeben" required>
                            </div>
                            <div class="form-group">
                                <label for="parkgebuehr">Parkgebühr</label>
                                <input type="number" id="parkgebuehr" name="parkgebuehr" class="form-control" placeholder="Parkgebühr">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Route speichern</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Anzeige der Gesamtkilometer und Kosten für den ausgewählten Monat -->
            <div class="col-md-6">
                <!-- Filterformular nach Monat -->
                <div class="card-body">
                    <form method="GET" action="{{ route('spesen') }}" class="mb-4">
                        <div class="form-group">
                            <label for="month">Monat auswählen:</label>
                            <input type="month" id="month" name="month" class="form-control" value="{{ $selectedMonth }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Filtern</button>
                    </form>
                </div>
                <div class="card mb-4">
                    <div class="card-header text-center">
                        <h5 class="card-title">Übersicht für den ausgewählten Monat</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Gesamt Kilometer:</strong> {{ $totalKilometers }} km</p>
                        <p><strong>Gesamtkosten (inkl. Parkgebühr):</strong> {{ number_format($totalCost, 2) }} CHF</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabelle zur Anzeige vorhandener Einträge -->
        <div class="card mt-4">
            <div class="card-header text-center">
                <h5 class="card-title">Routenübersicht</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Datum</th>
                            <th>Standort</th>
                            <th>Kilometer</th>
                            <th>Parkgebühr</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($spesen as $entry)
                            <tr>
                                <td onclick="editValue(this, '{{ $entry->datum }}', 'datum', {{ $entry->id }})">
                                    {{ \Carbon\Carbon::parse($entry->datum)->format('d.m.Y') }}
                                </td>
                                <td onclick="editValue(this, '{{ $entry->standort }}', 'standort', {{ $entry->id }})">
                                    {{ $entry->standort }}
                                </td>
                                <td onclick="editValue(this, '{{ $entry->kilometer }}', 'kilometer', {{ $entry->id }})">
                                    {{ $entry->kilometer }}
                                </td>
                                <td onclick="editValue(this, '{{ $entry->parkgebuehr }}', 'parkgebuehr', {{ $entry->id }})">
                                    {{ $entry->parkgebuehr }} CHF
                                </td>
                                <td>
                                    <button class="btn btn-danger" onclick="deleteSpesen({{ $entry->id }})">Löschen</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editValue(element, currentValue, field, id) {
            const inputField = document.createElement('input');
            inputField.type = 'text';
            inputField.className = 'form-control';
            inputField.value = currentValue;

            inputField.onblur = function() {
                const newValue = this.value;
                updateSpesen(id, field, newValue);
                element.innerText = newValue;
                element.onclick = function() {
                    editValue(element, newValue, field, id);
                };
                inputField.remove();
            };

            inputField.onkeypress = function(event) {
                if (event.key === 'Enter') {
                    this.blur();
                }
            };

            element.innerHTML = '';
            element.appendChild(inputField);
            inputField.focus();
        }

        function updateSpesen(id, field, value) {
            fetch(`/spesen/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ [field]: value })
            })
            .then(response => {
                if (!response.ok) throw new Error('Failed to update.');
                return response.json();
            })
            .then(data => {
                console.log('Updated successfully:', data);
                location.reload(); // Osvježava stranicu nakon uspješne promjene
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        
    function deleteSpesen(id) {
    
            fetch(`/spesen/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            })
            .then(response => {
                if (!response.ok) throw new Error('Failed to delete entry.');
                return response.json();
            })
            .then(data => {
                console.log('Deleted successfully:', data);
                location.reload(); // Refresh the page to reflect the deletion
            })
            .catch(error => {
                console.error('Error:', error);
            });
        
    }



    flatpickr("#datum", { 
    dateFormat: "Y-m-d", // Format koji će se slati serveru
    altInput: true,
    altFormat: "d-m-y"   // Format prikaza za korisnika
});
    </script>
</body>
</html>
