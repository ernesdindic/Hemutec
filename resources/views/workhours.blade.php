<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/app.js') }}" defer></script>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" defer></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    <style>
        .container {
            max-width: 1000px;
        }
        h1 {
            margin-top: 20px;
            margin-bottom: 40px;
        }
    </style>
</head>
<body class="light-mode">
    <div class="container">
        <!-- Back Button -->
        <div class="text-right mt-3 mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Zurück</a>
        </div>
        <h1 class="mb-4 text-center">Arbeitszeiten</h1>

        <!-- Filter Reports Card -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header text-center">
                        <h5 class="card-title">Monat</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('workhours') }}" class="mb-4">
                        <div class="form-group">
                            <label for="month">Monat auswählen:</label>
                            <input type="month" id="month" name="month" class="form-control" value="{{ request('month', date('Y-m')) }}" required>
                        </div>

                            <button type="submit" class="btn btn-primary btn-block">Filtern/Generieren</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
    <div class="card mb-4">
        <div class="card-header text-center">
            <h5 class="card-title">Überstundenübersicht</h5>
        </div>
        <div class="card-body" style="border-bottom: 1px solid black;">
        <p><strong>Überstunden SUMME:</strong> 
    @php
        $absoluteMinutes = abs($totalOvertimeMinutes);
        $totalHours = floor($absoluteMinutes / 60);
        $totalMinutes = $absoluteMinutes % 60;
        $sign = $totalOvertimeMinutes < 0 ? '-' : '';
        echo $sign . $totalHours . 'h ' . str_pad($totalMinutes, 2, '0', STR_PAD_LEFT) . 'min';
    @endphp
</p>
<p><strong>Überstunden in Tagen:</strong> 
    @php
        $hoursInDay = 8.5;
        $totalDays = $totalOvertimeMinutes / ($hoursInDay * 60); // Pretvori u dane
        echo number_format($totalDays, 2) . ' Tage';
    @endphp
</p>

        </div>
        <div class="card-body">
    <p>Urlaub </br>
        {{ auth()->user()->ferien }} Tage</p>
</div>
    </div>
    
</div>

        </div>

<!-- Workhours Table Card -->
<div class="card mt-4">
    <div class="card-header text-center">
        <h5 class="card-title">Arbeitszeiten</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Datum</th>
                        <th>Startzeit</th>
                        <th>Pause</th>
                        <th>Endzeit</th>
                        <th>Überstunden</th>
                        <th>Beschreibung</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workhours as $workhour)
                    <tr>
                    <td>{{ \Carbon\Carbon::parse($workhour->date)->format('d.m.Y') }}</td>
                        <td>
                            <span class="value" onclick="editValue(this, '{{ substr($workhour->start_time, 0, 5) }}', 'start_time', {{ $workhour->id }})">{{ substr($workhour->start_time, 0, 5) }}</span>
                        </td>
                        <td>
                            <span class="value" onclick="editValue(this, '{{ substr($workhour->break_time, 0, 5) }}', 'break_time', {{ $workhour->id }})">{{ substr($workhour->break_time, 0, 5) }}</span>
                        </td>
                        <td>
                            <span class="value" onclick="editValue(this, '{{ substr($workhour->end_time, 0, 5) }}', 'end_time', {{ $workhour->id }})">{{ substr($workhour->end_time, 0, 5) }}</span>
                        </td>
                        <td>
                            <span class="value" onclick="editValue(this, '{{ $workhour->overtime_minutes }}', 'overtime_minutes', {{ $workhour->id }})">
                                @php
                                    $hours = floor(abs($workhour->overtime_minutes) / 60);
                                    $minutes = abs($workhour->overtime_minutes) % 60;
                                    $sign = $workhour->overtime_minutes < 0 ? '-' : ''; // Uzimanje znaka
                                    echo $sign . $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
                                @endphp
                            </span>
                        </td>

                        <td>
                            <span class="value" onclick="editValue(this, '{{ $workhour->description ?: '...' }}', 'description', {{ $workhour->id }})">
                                {{ $workhour->description ?: '...' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
function formatTime(time) {
    // Assuming time is in the format HH:MM:SS
    return time.substring(0, 5); // Truncate to HH:MM
}

function editValue(element, currentValue, field, id) {
    const inputField = document.createElement('input');
    inputField.type = 'text';
    inputField.className = 'form-control';
    inputField.value = currentValue;

    // Spremi vrednost pre promene kako bi se izbeglo nevoljno ažuriranje
    let isUpdated = false;

    // Kada input izgubi fokus, proveri da li treba ažurirati
    inputField.onblur = function() {
        if (!isUpdated) {
            const newValue = this.value;
            if (newValue !== currentValue) {
                updateWorkHour(id, field, newValue);
            }
            element.innerText = newValue;
        }
        isUpdated = true; // Spreči dvostruko ažuriranje
    };

    // Kada korisnik pritisne Enter, ažuriraj i zatvori polje
    inputField.onkeypress = function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Sprečava novi red
            this.blur();
        }
    };

    // Zadrži kursor na mestu kada korisnik klikne unutar polja
    inputField.onclick = function(event) {
        event.stopPropagation();
    };

    // Zameni sadržaj ćelije sa input poljem i zadrži fokus
    element.innerHTML = '';
    element.appendChild(inputField);
    inputField.focus();
}

function updateWorkHour(id, field, value) {
    fetch(`/workhours/${id}`, {
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
        location.reload(); // Osvježava stranicu nakon uspešne promene
    })
    .catch(error => {
        console.error('Error:', error);
    });
}


</script>



</body>
</html>
