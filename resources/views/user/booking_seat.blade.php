@extends('layouts.app')

@section('content')
<div class="container text-white">
    <h3 class="mb-4 text-center">🎬 Select Seat for {{ $movie->title }}</h3>

    <form action="{{ route('booking.store', $movie->id) }}" method="POST">
        @csrf

        
        <div class="mb-3 text-center">
            <label for="theatre_id" class="form-label">🏛️ Theatre</label>
            <select name="theatre_id" id="theatre_id" class="form-select w-50 mx-auto" required>
                <option value="">-- Select Theatre --</option>
                @foreach($theatres as $theatre)
                    <option value="{{ $theatre->id }}">{{ $theatre->name }}</option>
                @endforeach
            </select>
        </div>

        
        <div class="mb-3 text-center">
            <label for="showtime_id" class="form-label">🕒 Showtime</label>
            <select name="showtime_id" id="showtime_id" class="form-select w-50 mx-auto" required disabled>
                <option value="">-- Select Showtime --</option>
            </select>
        </div>

        
        <div class="text-center bg-warning text-dark py-2 fw-bold mb-4 rounded">SCREEN</div>

       
        <div id="seat-map" class="d-flex flex-column align-items-center gap-1 mb-3">
            @php
                $rows = ['A','B','C','D','E','F','G','H','I','J'];
                $cols = range(1,10);
            @endphp

            @foreach($rows as $row)
                <div class="d-flex gap-1">
                    @foreach($cols as $col)
                        @php $seat = $row.$col; @endphp
                        <button type="button"
                            class="seat btn btn-sm
                                @if(in_array($row,['A','B'])) honeymoon
                                @elseif(in_array($row,['C','D','E'])) normal
                                @elseif(in_array($row,['F','G','H'])) opera
                                @else normal
                                @endif"
                            data-seat="{{ $seat }}">
                            {{ $seat }}
                        </button>
                    @endforeach
                </div>
            @endforeach
        </div>

        
        <input type="hidden" name="seat_numbers" id="seat_numbers">
        <input type="hidden" name="seat_type" id="seat_type">

       
        <div class="text-center mt-3">
            <p>🪑 Selected Seats: <span id="selected-seats">None</span></p>
            <p>💰 Total Price: <span id="total-price">0</span> ฿</p>
            <button type="submit" class="btn btn-success px-4">Confirm & Pay</button>
        </div>

        
        <div class="d-flex justify-content-center align-items-center gap-4 mt-4">
            <div><span class="legend normal"></span> Normal (99฿)</div>
            <div><span class="legend honeymoon"></span> Honeymoon (119฿)</div>
            <div><span class="legend opera"></span> Opera Chair (400฿)</div>
            <div><span class="legend booked"></span> จองแล้ว</div>
        </div>
    </form>
</div>

<style>
    body { background-color: #1b1f23; }
    .seat {
        width: 40px; height: 40px;
        border-radius: 6px;
        border: none;
        color: white;
        font-size: 12px;
        text-align: center;
        cursor: pointer;
        transition: 0.2s;
    }
    .seat.normal { background-color: #b33b3b; }
    .seat.honeymoon { background-color: #5d3b8c; }
    .seat.opera { background-color: #b38f00; }
    .seat.booked { background-color: #555; cursor: not-allowed; }
    .seat.selected { outline: 3px solid #00ff99; }
    .legend {
        display: inline-block;
        width: 25px;
        height: 25px;
        border-radius: 4px;
        margin-right: 6px;
    }
    .legend.normal { background-color: #b33b3b; }
    .legend.honeymoon { background-color: #5d3b8c; }
    .legend.opera { background-color: #b38f00; }
    .legend.booked { background-color: #555; }
</style>

<script>
    const theatreSelect = document.getElementById('theatre_id');
    const showtimeSelect = document.getElementById('showtime_id');
    const seatNumbersInput = document.getElementById('seat_numbers');
    const seatTypeInput = document.getElementById('seat_type');
    const selectedSeatsEl = document.getElementById('selected-seats');
    const totalPriceEl = document.getElementById('total-price');
    let selectedSeats = [];

    
    theatreSelect.addEventListener('change', function() {
        const theatreId = this.value;
        const movieId = "{{ $movie->id }}";
        showtimeSelect.innerHTML = '<option value="">-- Select Showtime --</option>';
        showtimeSelect.disabled = true;
        resetSeatSelection();

        if (!theatreId) return;

        fetch(`/api/movies/${movieId}/theatres/${theatreId}/showtimes`)
            .then(res => res.json())
            .then(showtimes => {
                showtimes.forEach(st => {
                    const opt = document.createElement('option');
                    opt.value = st.id;
                    opt.textContent = `${st.show_date} | ${st.start_time} - ${st.end_time}`;
                    showtimeSelect.appendChild(opt);
                });
                showtimeSelect.disabled = false;
            })
            .catch(() => alert('❌ ไม่สามารถโหลดรอบฉายได้'));
    });

    
    showtimeSelect.addEventListener('change', function() {
        const showtimeId = this.value;
        resetSeatSelection();

        if (!showtimeId) return;

        fetch(`/api/showtime/${showtimeId}/booked-seats`)
            .then(res => res.json())
            .then(bookedSeats => {
                document.querySelectorAll('.seat').forEach(btn => {
                    btn.classList.remove('booked');
                    btn.disabled = false;
                });
                bookedSeats.forEach(seat => {
                    const btn = document.querySelector(`[data-seat="${seat}"]`);
                    if (btn) {
                        btn.classList.add('booked');
                        btn.disabled = true;
                    }
                });
            })
            .catch(() => alert('❌ ไม่สามารถโหลดข้อมูลที่นั่งได้'));
    });

    
    document.querySelectorAll('.seat').forEach(btn => {
        btn.addEventListener('click', () => {
            if (btn.classList.contains('booked')) return;

            const seat = btn.dataset.seat;
            const type = btn.classList.contains('honeymoon')
                ? 'honeymoon'
                : btn.classList.contains('opera')
                ? 'opera'
                : 'normal';

            if (selectedSeats.includes(seat)) {
                selectedSeats = selectedSeats.filter(s => s !== seat);
                btn.classList.remove('selected');
            } else {
                selectedSeats.push(seat);
                btn.classList.add('selected');
            }

            updateSeatSummary();
        });
    });

    function getPriceByType(type) {
        if (type === 'honeymoon') return 119;
        if (type === 'opera') return 400;
        return 99;
    }

    function updateSeatSummary() {
        seatNumbersInput.value = selectedSeats.join(',');
        const total = selectedSeats.reduce((sum, s) => {
            const seatBtn = document.querySelector(`[data-seat="${s}"]`);
            const seatType = seatBtn.classList.contains('honeymoon')
                ? 'honeymoon'
                : seatBtn.classList.contains('opera')
                ? 'opera'
                : 'normal';
            return sum + getPriceByType(seatType);
        }, 0);

        selectedSeatsEl.textContent = selectedSeats.length ? selectedSeats.join(', ') : 'None';
        totalPriceEl.textContent = total;
    }

    function resetSeatSelection() {
        selectedSeats = [];
        seatNumbersInput.value = '';
        selectedSeatsEl.textContent = 'None';
        totalPriceEl.textContent = '0';
        document.querySelectorAll('.seat').forEach(btn => btn.classList.remove('selected'));
    }
</script>
@endsection
