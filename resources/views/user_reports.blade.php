<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzerberichte</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Back Button -->
        <div class="text-right mt-3 mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Zurück</a>
        </div>
        <h2 class="mb-4">Benutzerberichte:</h2>
        
        <div class="card-body">
            <form method="GET" action="{{ route('user-reports') }}" class="mb-4">
                <div class="form-group">
                    <label for="month">Monat auswählen:</label>
                    <input type="month" id="month" name="month" class="form-control" value="{{ $selectedMonth }}" required style="width: calc(100% - 76.33%);">
                </div>
                <button type="submit" class="btn btn-primary">Filtern</button>
            </form>
        </div>

        @if (auth()->check() && auth()->user()->is_admin)
        <div class="row">
            @foreach ($users as $user)
                <div class="col-md-6 mb-4">
                    <div class="card">
                    <div class="card-header text-center">
                    <h5 class="card-title" style="{{ $user->id === $highestProfitUser->id ? 'color: white;' : '' }}">
                    <a href="{{ route('user-profile', $user->id) }}" style="text-decoration: none; color: inherit;" 
                        onmouseover="this.style.color='red'" 
                        onmouseout="this.style.color='inherit'">
                        {{ $user->name }}
                    </a>
                    ({{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }})
                    </h5>

                    </div>

                    <div class="card-body text-left">
                            <?php
                                // Filtriranje izvještaja za izabrani mjesec je urađeno u kontroleru.
                                $totalTime = $user->reports->where('tip_posla', '!=', 'pauza')->sum('vrijeme_rada');
                                $totalProductiveTime = $user->reports->where('tip_posla', 'produktiv')->sum('vrijeme_rada');
                                $totalNeProductiveTime = $user->reports->where('tip_posla', 'neproduktivan')->sum('vrijeme_rada');
                                $totalInternProductiveTime = $user->reports->where('tip_posla', 'interno produktivan')->sum('vrijeme_rada');
                                $totalPhoneProductiveTime = $user->reports->where('tip_posla', 'telefonsko produktivan')->sum('vrijeme_rada');
                                $totalPhoneInProductiveTime = $user->reports->where('tip_posla', 'telefonsko neproduktivan')->sum('vrijeme_rada');
                                $totalPauseTime = $user->reports->where('tip_posla', 'pauza')->sum('vrijeme_rada');
                                $totalWeiterbildungTime = $user->reports->where('tip_posla', 'weiterbildung')->sum('vrijeme_rada');
                                $totalAnderesTime = $user->reports->where('tip_posla', 'anderes')->sum('vrijeme_rada');
                                $totalEmailTime = $user->reports->where('tip_posla', 'e-mails')->sum('vrijeme_rada');
                                $totalHemutecProcesiTime = $user->reports->where('tip_posla', 'hemutec procesi')->sum('vrijeme_rada');
                                $totalFahrtTime = $user->reports->where('tip_posla', 'fahrt')->sum('vrijeme_rada');
                                //$ferien = $user->ferien;
                                //$totalProductivProfit = $user->reports->sum('profit');
                                //$totalOvertimeMinutes = WorkHours::where('user_id', $userId)->sum('overtime_minutes');
                            ?>

                            <p><strong>Gesamtzeit:</strong> {{ number_format($totalTime, 2) }} h</p>
                            <p><strong>Produktive Zeit:</strong> {{ number_format($totalProductiveTime, 2) }} h</p>
                            <p><strong>Unproduktive Zeit:</strong> {{ number_format($totalNeProductiveTime, 2) }} h</p>
                            <p><strong>Interne produktive Zeit:</strong> {{ number_format($totalInternProductiveTime, 2) }} h</p>
                            <p><strong>Telefonisch produktive Zeit:</strong> {{ number_format($totalPhoneProductiveTime, 2) }} h</p>
                            <p><strong>Telefonisch unproduktive Zeit:</strong> {{ number_format($totalPhoneInProductiveTime, 2) }} h</p>
                            <p><strong>Pausenzeit:</strong> {{ number_format($totalPauseTime, 2) }} h</p>
                            <p><strong>Weiterbildungszeit:</strong> {{ number_format($totalWeiterbildungTime, 2) }} h</p>
                            <p><strong>Andere Zeit:</strong> {{ number_format($totalAnderesTime, 2) }} h</p>
                            <p><strong>E-Mail Zeit:</strong> {{ number_format($totalEmailTime, 2) }} h</p>
                            <p><strong>Hemutec Prozesszeit:</strong> {{ number_format($totalHemutecProcesiTime, 2) }} h</p>
                            <p><strong>Fahrtzeit:</strong> {{ number_format($totalFahrtTime, 2) }} h</p>
                            <p><strong>Gewinn (intern produktiv):</strong> {{ number_format($user->totalInternProductivProfit, 2) }} CHF</p>
                            <p><strong>Gewinn (produktiv):</strong> {{ number_format($user->totalProductivProfit, 2) }} CHF</p>
                            <p style="font-size: 30px;"><strong>Gesamtgewinn:</strong> {{ number_format($user->totalProductivProfit + $user->totalInternProductivProfit, 2) }} CHF</p>
                            <p><strong>Urlaub:</strong> {{ $user->ferien }} Tage</p> 
                            <p><strong>Monatliche Fahrtkosten:</strong> {{ number_format($user->totalCost, 2) }} CHF</p>
                            <p><strong>Überstunden SUMME:</strong> 
                                @php
                                    $absoluteMinutes = abs($user->totalOvertimeMinutes);
                                    $totalHours = floor($absoluteMinutes / 60);
                                    $totalMinutes = $absoluteMinutes % 60;
                                    $sign = $user->totalOvertimeMinutes < 0 ? '-' : '';
                                    echo $sign . $totalHours . 'h ' . str_pad($totalMinutes, 2, '0', STR_PAD_LEFT) . 'min';
                                @endphp
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @else
        <p>Sie sind kein Admin, bitte verlassen Sie diese Seite</p>
    @endif
    
    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
