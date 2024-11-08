<!DOCTYPE html>
<html lang="en">
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



</head>
<body class="light-mode">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
        <span>Angemeldeter Benutzer: {{ auth()->user()->name }}</span>
        
        <div>
            @if (auth()->check() && auth()->user()->is_admin)
                <a href="{{ url('/user-reports') }}" class="btn btn-primary me-2">Benutzerberichte</a>
            @endif
            
            <a href="{{ route('workhours') }}" class="btn btn-primary me-2">Arbeitszeiten</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                @if (auth()->check() && auth()->user()->is_admin)
                <a href="{{ url('/ferien') }}" class="btn btn-primary me-2">Urlaub</a>
            @endif
            <a href="{{ url('/spesen') }}" class="btn btn-primary me-2">Spesen</a>
                <button type="submit" class="btn btn-danger">Abmelden</button>
            </form>
        </div>
    </div>

    </br><h1 class="mb-4 text-center">Dashboard</h1></br>

        <!-- Filter Reports Card -->
         <div class="row">
            <div class="col-6">
                <div class="card mb-4">
                    <div class="card-header text-center">
                        <h5 class="card-title">Berichte filtern</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
                            <div class="form-group">
                                <label for="month">Monat auswählen:</label>
                                <input type="month" id="month" name="month" class="form-control" value="{{ $selectedMonth }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Filtern</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card mb-4">
                    <div class="card-header text-center">
                        <h5 class="card-title">Monatsgewinn</h5>
                    </div>
                    <div class="card-body text-left">
                    <p style="font-size:44px;">
                        <strong>Gesamtgewinn:</strong><br/>
                        {{ number_format($totalProductivProfit + $totalInternProductivProfit, 2) }} CHF
                    </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Form Card -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h5 class="card-title">Bericht hinzufügen</h5>
                    </div>
                    <div class="card-body">
                        <form id="report-form" method="POST" action="/reports">
                            @csrf
                            <input type="hidden" name="selectline" value="0">

                            <div class="form-group">
                                <label for="datum">Datum</label>
                                <input type="date" id="datum" name="datum" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Arbeitszeit</label>
                                <div class="input-group">
                                    <label for="vrijeme_pocetak" class="input-group-text">Von:</label>
                                    <input type="time" id="vrijeme_pocetak" name="vrijeme_pocetak" class="form-control">
                                    
                                    <label for="vrijeme_kraj" class="input-group-text">Bis:</label>
                                    <input type="time" id="vrijeme_kraj" name="vrijeme_kraj" class="form-control">
                                </div>
                                <input type="text" id="vrijeme_rada" name="vrijeme_rada" class="form-control" hidden>
                            </div>

                            <div class="form-group">
                                <label for="ime_stranke">Kundenname</label>
                                <input type="text" id="ime_stranke" name="ime_stranke" class="form-control" placeholder="Kunden suchen...">
                                <div id="stranke_list" class="dropdown-list"></div>
                            </div>
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addClientModal">
                                Kunde hinzufügen
                            </button></br></br>

                            <div class="form-group">
                                <label for="opis_rada">Arbeitsbeschreibung</label>
                                <textarea id="opis_rada" name="opis_rada" class="form-control" rows="3" placeholder="Arbeitsbeschreibung eingeben..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="tip_posla">Arbeitsart</label>
                                <select id="tip_posla" name="tip_posla" class="form-control" required>
                                    <option value="produktiv">Produktiv</option>
                                    <option value="neproduktivan">Unproduktiv</option>
                                    <option value="interni posao">Interne Arbeit</option>
                                    <option value="interno produktivan">Intern produktiv</option>
                                    <option value="telefonsko produktivan">Telefonisch produktiv</option>
                                    <option value="telefonsko neproduktivan">Telefonisch unproduktiv</option>
                                    <option value="pauza">Pause</option>
                                    <option value="weiterbildung">Weiterbildung</option>
                                    <option value="anderes">Anderes</option>
                                    <option value="e-mails">E-Mails</option>
                                    <option value="hemutec procesi">Hemutec Prozesse</option>
                                    <option value="fahrt">Fahrt</option>
                                </select>
                            </div>

                            <div class="form-check">
                                <input type="checkbox" id="selectline" name="selectline" class="form-check-input" value="on">
                                <label for="selectline" class="form-check-label">Selectline</label>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Speichern</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Stats Card -->
            <div class="col-md-6">
            <div class="card">
    <div class="card-header text-center">
        <h5 class="card-title">Statistik</h5>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between py-2">
            <span><strong>Gesamtzeit:</strong></span>
            <span>{{ number_format($totalTime, 2) }} h</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span><strong>Produktive Zeit:</strong></span>
            <span>{{ number_format($totalProductiveTime, 2) }} h</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span><strong>Unproduktive Zeit:</strong></span>
            <span>{{ number_format($totalNeProductiveTime, 2) }} h</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span><strong>Interne produktive Zeit:</strong></span>
            <span>{{ number_format($totalInternProductiveTime, 2) }} h</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span><strong>Telefonisch produktive Zeit:</strong></span>
            <span>{{ number_format($totalPhoneProductiveTime, 2) }} h</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span><strong>Telefonisch unproduktive Zeit:</strong></span>
            <span>{{ number_format($totalPhoneInProductiveTime, 2) }} h</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span><strong>Pausenzeit:</strong></span>
            <span>{{ number_format($totalPauseTime, 2) }} h</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span><strong>Weiterbildungszeit:</strong></span>
            <span>{{ number_format($totalWeiterbildungTime, 2) }} h</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span><strong>Andere Zeit:</strong></span>
            <span>{{ number_format($totalAnderesTime, 2) }} h</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span><strong>E-Mail Zeit:</strong></span>
            <span>{{ number_format($totalEmailTime, 2) }} h</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span><strong>Hemutec Prozesszeit:</strong></span>
            <span>{{ number_format($totalHemutecProcesiTime, 2) }} h</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span><strong>Fahrtzeit:</strong></span>
            <span>{{ number_format($totalFahrtTime, 2) }} h</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span><strong>Gewinn (internproduktiv):</strong></span>
            <span>{{ number_format($totalInternProductivProfit, 2) }} CHF</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span><strong>Gewinn (produktiv):</strong></span>
            <span>{{ number_format($totalProductivProfit, 2) }} CHF</span>
        </div>
    </div>
</div>

                <!-- Warning Message -->
                <div id="warning" class="alert alert-danger mt-3 flash hidden">
                    <strong>Warnung!</strong> Selectline auswählen!
                </div>
            </div>
        </div>

        <!-- Reports Table Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Berichte</h5>
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
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                        <tr>
                            <td data-label="Datum">{{ \Carbon\Carbon::parse($report->datum)->format('d.m.Y') }}</td>
                            <td data-label="Arbeitszeit">
                                <div class="vrijeme-rada-container" data-toggle="tooltip" data-placement="top" title="{{ $report->vrijeme_rada }}">
                                    <span class="vrijeme-rada vrijeme-rada-text" data-id="{{ $report->id }}">{{ Str::limit($report->vrijeme_rada, 30) }}</span>
                                    <input type="text" class="form-control vrijeme-rada-input" data-id="{{ $report->id }}" value="{{ $report->vrijeme_rada }}" style="display: none;">
                                </div>
                            </td>
                            <td data-label="Kundenname">{{ $report->ime_stranke }}</td>
                            <td data-label="Arbeitsbeschreibung">
                                <div class="opis-rada-container" data-toggle="tooltip" data-placement="top" title="{{ $report->opis_rada }}">
                                    <span class="opis-rada opis-rada-text" data-id="{{ $report->id }}">{{ Str::limit($report->opis_rada, 30) }}</span>
                                    <input type="text" class="form-control opis-rada-input" data-id="{{ $report->id }}" value="{{ $report->opis_rada }}" style="display: none;">
                                </div>
                            </td>
                            <td data-label="Arbeitsart">{{ $report->tip_posla }}</td>
                            <td data-label="Selectline" class="text-center">
                                @if($report->tip_posla == 'produktiv' || $report->tip_posla == 'telefonsko produktivan')
                                    <input type="checkbox" class="styled-checkbox selectline-checkbox" data-id="{{ $report->id }}" {{ $report->selectline ? 'checked' : '' }}>
                                @endif
                            </td>
                            <td data-label="Aktionen">
                                <button class="btn btn-danger" onclick="deleteReport({{ $report->id }})">Löschen</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addClientModalLabel">Kunde hinzufügen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('clients.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="client_name">Kundenname</label>
                        <input type="text" id="client_name" name="name" class="form-control" placeholder="Kundennamen eingeben" required>
                    </div>
                    <div class="form-group">
                        <label for="hourly_rate">Stundensatz</label>
                        <input type="number" id="hourly_rate" name="hourly_rate" class="form-control" placeholder="Stundensatz eingeben" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Kunde hinzufügen</button>
                </form>
            </div>
        </div>
    </div>
</div>


    <script>

document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.selectline-checkbox');

        checkboxes.forEach(checkbox => {
            // Initial check for applying the correct class
            toggleBorder(checkbox);

            // Add event listener for change event
            checkbox.addEventListener('change', function () {
                toggleBorder(checkbox);
            });
        });

        function toggleBorder(checkbox) {
            const row = checkbox.closest('tr');

            if (checkbox.checked) {
                row.classList.add('border-checked');
                row.classList.remove('border-unchecked');
            } else {
                row.classList.add('border-unchecked');
                row.classList.remove('border-checked');
            }
        }
    });



        //dodavanje vremena
        document.getElementById('vrijeme_pocetak').addEventListener('change', calculateWorkingTime);
document.getElementById('vrijeme_kraj').addEventListener('change', calculateWorkingTime);

function calculateWorkingTime() {
    const start = document.getElementById('vrijeme_pocetak').value;
    const end = document.getElementById('vrijeme_kraj').value;

    if (start && end) {
        const startTime = new Date(`1970-01-01T${start}:00`);
        const endTime = new Date(`1970-01-01T${end}:00`);
        
        // Izračunaj razliku u minutama
        const diff = (endTime - startTime) / (1000 * 60);
        
        // Prikaži razliku kao vrijeme
        if (diff >= 0) {
            const hours = Math.floor(diff / 60);
            const minutes = diff % 60;
            document.getElementById('vrijeme_rada').value = `${hours}.${(minutes / 60).toFixed(2).split('.')[1]}`; // Prikazuje vrijeme u satima
        } else {
            alert('Vrijeme završetka mora biti poslije vremena početka.');
            document.getElementById('vrijeme_kraj').value = ''; // Resetuj završno vrijeme
            document.getElementById('vrijeme_rada').value = ''; // Resetuj ukupno vrijeme
        }
    }
}





    function deleteReport(reportId) {
        fetch(`/reports/${reportId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload(); // Osveži stranicu nakon brisanja
            } else {
                alert('Error deleting report');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while trying to delete the report.');
        });
    }

  

    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip(); // Inicijalizuje Bootstrap tooltip
    });

    // Ažuriranje opisa rada
    document.querySelectorAll('.opis-rada-text').forEach(element => {
        element.addEventListener('click', function() {
            const reportId = this.getAttribute('data-id');
            const opisInput = this.nextElementSibling;

            // Prikaži polje za unos i sakrij tekst
            this.style.display = 'none';
            opisInput.style.display = 'block';
            opisInput.focus();

            // Sačuvaj promene kada se izgubi fokus ili pritisne enter
            opisInput.addEventListener('blur', () => {
                updateOpis(reportId, opisInput.value);
                resetOpisInput(opisInput); // Resetuj prikaz ulaza nakon čuvanja
            });
            opisInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    updateOpis(reportId, opisInput.value);
                    resetOpisInput(opisInput); // Resetuj prikaz ulaza nakon čuvanja
                }
            });
        });
    });

    // Funkcija za resetovanje prikaza opisa
    function resetOpisInput(opisInput) {
        opisInput.style.display = 'none';
        const opisText = opisInput.previousElementSibling;
        opisText.style.display = 'block';
        opisText.textContent = opisInput.value; // Ažuriraj sadržaj teksta novom vrednošću
    }

    function updateOpis(reportId, newOpis) {
        fetch(`/reports/${reportId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ opis_rada: newOpis })
        })
        .then(response => {
            if (response.ok) {
                console.log('Opis rada updated successfully');
                location.reload();
            } else {
                alert('Error updating report: ' + response.statusText);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Error updating report: ' + error.message);
        });
    }

    // Ažuriranje selectline checkbox-a
    document.querySelectorAll('.selectline-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const reportId = this.getAttribute('data-id');
            const newSelectlineValue = this.checked; // true ili false

            // Pošaljite AJAX zahtev za ažuriranje
            fetch(`/reports/${reportId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ selectline: newSelectlineValue })
            })
            .then(response => {
                if (response.ok) {
                    console.log('Selectline updated successfully');
                } else {
                    alert('Error updating selectline: ' + response.statusText);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Error updating selectline: ' + error.message);
            });
        });
    });

    // Ažuriranje vremena rada
document.querySelectorAll('.vrijeme-rada-text').forEach(element => {
    element.addEventListener('click', function() {
        const reportId = this.getAttribute('data-id');
        const vrijemeInput = this.nextElementSibling;

        // Prikaži polje za unos i sakrij tekst
        this.style.display = 'none';
        vrijemeInput.style.display = 'block';
        vrijemeInput.focus();

        // Sačuvaj promene kada se izgubi fokus ili pritisne enter
        vrijemeInput.addEventListener('blur', () => {
            updateVrijeme(reportId, vrijemeInput.value);
            resetVrijemeInput(vrijemeInput); // Resetuj prikaz ulaza nakon čuvanja
        });
        vrijemeInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                updateVrijeme(reportId, vrijemeInput.value);
                resetVrijemeInput(vrijemeInput); // Resetuj prikaz ulaza nakon čuvanja
            }
        });
    });
});

// Funkcija za resetovanje prikaza vremena
function resetVrijemeInput(vrijemeInput) {
    vrijemeInput.style.display = 'none';
    const vrijemeText = vrijemeInput.previousElementSibling;
    vrijemeText.style.display = 'block';
    vrijemeText.textContent = vrijemeInput.value; // Ažuriraj sadržaj teksta novom vrednošću
}

function updateVrijeme(reportId, newVrijeme) {
    fetch(`/reports/${reportId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ vrijeme_rada: newVrijeme })
    })
    .then(response => {
        if (response.ok) {
            console.log('Vrijeme rada updated successfully');
            location.reload();
        } else {
            alert('Error updating vrijeme rada: ' + response.statusText);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Error updating vrijeme rada: ' + error.message);
    });
}


       // Example array of selected dates, this should come from your actual logic
       const selectedDates = []; // Populate this array with selected dates

       function checkLastThreeDays(selectedMonth) {
        const today = new Date();
        const currentMonth = today.getMonth() + 1; // Months are zero-based
        const currentYear = today.getFullYear();

        if (selectedMonth) {
            const [year, month] = selectedMonth.split('-').map(Number);
            const lastDayOfMonth = new Date(year, month, 0).getDate();

            const lastThreeDays = [
                `${year}-${String(month).padStart(2, '0')}-${String(lastDayOfMonth - 2).padStart(2, '0')}`,
                `${year}-${String(month).padStart(2, '0')}-${String(lastDayOfMonth - 1).padStart(2, '0')}`,
                `${year}-${String(month).padStart(2, '0')}-${String(lastDayOfMonth).padStart(2, '0')}`
            ]; // Last three days in YYYY-MM-DD format

            // Check if all last three days are in the selectedDates
            const containsLastThreeDays = lastThreeDays.every(day => selectedDates.includes(day));

            // Show/hide warning based on selection
            const warningElement = document.getElementById('selectline-warning');
            if (containsLastThreeDays) {
                warningElement.style.display = 'block'; // Show the warning
                warningElement.classList.add('flash'); // Add flash class
            } else {
                warningElement.style.display = 'none'; // Hide the warning
                warningElement.classList.remove('flash'); // Remove flash class
            }
        }
    }

    // Example usage: Call this function when the month is selected or reports are added
    /* document.addEventListener('DOMContentLoaded', function() {
        const monthSelect = document.getElementById('month'); // Ensure you have the correct ID
        monthSelect.addEventListener('change', function() {
            checkLastThreeDays(monthSelect.value);
        });
    }); */

    document.addEventListener('DOMContentLoaded', function() {
    var today = new Date();
    var lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();
    var dayOfMonth = today.getDate();

    // Ako je danas jedan od zadnja 3 dana u mjesecu, prikazi upozorenje
    if (dayOfMonth >= lastDayOfMonth - 2) {
        document.getElementById('warning').classList.remove('hidden');
    }
});

//PRETRAZIVANJE STRANKI //////////////////////////////////

const input = document.getElementById('ime_stranke');
const list = document.getElementById('stranke_list');

// Kreiraj listu stranki iz PHP varijable
const clients = @json($clients); // Uzimamo sve klijente iz PHP-a
let filteredClients = [];

// Funkcija za filtriranje stranki
function filterClients() {
    const query = input.value.toLowerCase();
    filteredClients = clients.filter(client => client.name.toLowerCase().includes(query));
    updateDropdown();
}

// Funkcija za ažuriranje dropdown liste
function updateDropdown() {
    list.innerHTML = '';
    if (filteredClients.length > 0 && input.value !== '') {
        list.style.display = 'block'; // Prikaži listu
        filteredClients.forEach(client => {
            const item = document.createElement('div');
            item.textContent = client.name;
            item.onclick = () => selectClient(client.name);
            list.appendChild(item);
        });
    } else {
        list.style.display = 'none'; // Sakrij listu ako nema rezultata
    }
}

// Funkcija za selektovanje klijenta
function selectClient(name) {
    input.value = name;
    list.style.display = 'none'; // Sakrij listu
}

// Event listener za pretraživanje
input.addEventListener('input', filterClients);

// Sakrij dropdown kada se klikne van
document.addEventListener('click', (event) => {
    if (!input.contains(event.target) && !list.contains(event.target)) {
        list.style.display = 'none';
    }
});

//PRETRAZIVANJE STRANKI /////////////////////////////////////////////////////


</script>

<style>
    

.dropdown-list {
    border: 1px solid #ccc;
    max-height: 150px; /* Ograniči visinu */
    overflow-y: auto; /* Omogući skrolanje */
    position: absolute; /* Pozicioniraj listu ispod inputa */
    background-color: white; /* Pozadina */
    z-index: 1000; /* Osiguraj da je iznad ostalog sadržaja */
    display: none; /* Početno sakrij listu */
}

.dropdown-list div {
    padding: 8px 12px;
    cursor: pointer;
}

.dropdown-list div:hover {
    background-color: #f0f0redf0; /* Hover efekat */
}


    .border-checked {
        color: green !important;
    }

    .border-unchecked {
        color: red !important; 
    }



</style>
</body>
</html>
