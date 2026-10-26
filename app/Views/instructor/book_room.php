<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book a Room - CPC RoomFinder | Instructor Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <style>
    .booking-screen-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 8px;
    }
    .booking-title-group h1 {
      font-size: 1.85rem;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 2px;
    }
    .booking-title-group p.subhead {
      font-size: 0.92rem;
      font-style: italic;
      color: #cbd5e1;
    }
    .back-btn-orange {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 38px;
      height: 38px;
      border-radius: 8px;
      background: var(--cpc-orange);
      color: #11153b;
      font-size: 1.4rem;
      font-weight: 800;
      text-decoration: none;
      transition: transform var(--transition-fast), background var(--transition-fast);
    }
    .back-btn-orange:hover {
      background: var(--cpc-orange-hover);
      transform: scale(1.05);
      color: #11153b;
    }
    .booking-divider {
      border: 0;
      border-top: 1px solid rgba(255, 255, 255, 0.15);
      margin: 14px 0 28px;
    }
    .booking-form-box {
      max-width: 820px;
      width: 100%;
    }
    .book-field-label {
      font-size: 0.88rem;
      font-weight: 600;
      color: #cbd5e1;
      margin-bottom: 8px;
      display: block;
    }
    .book-input-control {
      background: #13194a;
      border: 1.5px solid #3b468a;
      border-radius: 8px;
      color: #ffffff;
      padding: 11px 16px;
      font-size: 0.95rem;
      outline: none;
      width: 100%;
      transition: border-color var(--transition-fast);
    }
    .book-input-control:focus {
      border-color: var(--cpc-orange);
    }
    .book-select-control {
      background: #13194a;
      border: 1.5px solid #3b468a;
      border-radius: 8px;
      color: #ffffff;
      padding: 11px 40px 11px 16px;
      font-size: 0.95rem;
      outline: none;
      width: 100%;
      cursor: pointer;
      appearance: none;
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23cbd5e1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 14px center;
      background-size: 16px;
    }
    .time-input-group {
      position: relative;
      display: flex;
      align-items: center;
    }
    .time-input-group input {
      padding-right: 38px;
    }
    .time-input-group .icon-inside {
      position: absolute;
      right: 12px;
      color: #f59e0b;
      font-size: 1.1rem;
      pointer-events: none;
    }
    .time-conflict-warning {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #f87171;
      font-size: 0.85rem;
      font-style: italic;
      padding: 6px 12px;
      border-radius: 6px;
      background: rgba(239, 68, 68, 0.08);
    }
  </style>
</head>
<body class="portal-layout">

  <!-- Instructor Sidebar Component -->
  <?= view('partials/instructor_sidebar', ['activeNav' => 'book']) ?>

  <!-- Main View Content -->
  <main class="portal-main">
    
    <!-- Top Header Bar matching Bottom Panel Screenshot -->
    <div class="booking-screen-header">
      <div class="booking-title-group">
        <h1>Book a Room</h1>
        <p class="subhead">Fill up the following details to request a booking.</p>
      </div>

      <!-- Orange Back Button Icon -->
      <a href="<?= base_url('instructor/book') ?>" class="back-btn-orange" title="Back to Room List" aria-label="Back">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
      </a>
    </div>

    <hr class="booking-divider">

    <!-- Booking Form Container -->
    <div class="booking-form-box">
      <form 
        id="instructorBookingRequestForm" 
        onsubmit="event.preventDefault(); showToast('Booking request submitted! Awaiting administrator approval.', 'success'); setTimeout(() => { window.location.href = '<?= base_url('instructor/bookings') ?>'; }, 1600);"
      >
        
        <!-- Row 1: Room No. / name & Capacity -->
        <div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 30px; margin-bottom: 22px; align-items: start;">
          <div>
            <label class="book-field-label" for="roomSelectField">Room No. / name</label>
            <select class="book-select-control" id="roomSelectField" name="room_selection" onchange="updateRoomCapacity(this)">
              <option value="Room 224" data-cap="24" <?= (isset($selectedRoom) && $selectedRoom === 'Room 224') ? 'selected' : '' ?>>Room 224 - CET department</option>
              <option value="Room 223" data-cap="30" <?= (isset($selectedRoom) && $selectedRoom === 'Room 223') ? 'selected' : '' ?>>Room 223 - CET department</option>
              <option value="Room 101" data-cap="36" <?= (isset($selectedRoom) && $selectedRoom === 'Room 101') ? 'selected' : '' ?>>Room 101 - Psychology department</option>
              <option value="DABBA" data-cap="80" <?= (isset($selectedRoom) && $selectedRoom === 'DABBA') ? 'selected' : '' ?>>DABBA - Marine department</option>
              <option value="Lab D" data-cap="20" <?= (isset($selectedRoom) && $selectedRoom === 'Lab D') ? 'selected' : '' ?>>Lab D - IT department</option>
            </select>
          </div>

          <div>
            <span class="book-field-label">Capacity</span>
            <div style="display: flex; align-items: center; gap: 8px; padding-top: 10px; font-size: 1.05rem; font-weight: 700; color: #ffffff;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="color: #cbd5e1;">
                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path>
              </svg>
              <span id="displayCapacityCount">24</span>
            </div>
          </div>
        </div>

        <!-- Row 2: Date -->
        <div style="max-width: 320px; margin-bottom: 22px;">
          <label class="book-field-label" for="bookingDateField">Date</label>
          <div class="time-input-group">
            <input 
              type="date" 
              class="book-input-control" 
              id="bookingDateField" 
              name="booking_date"
              value="<?= date('Y-m-d') ?>"
              required
            >
          </div>
        </div>

        <!-- Row 3: Start Time & End Time with Conflict Note -->
        <div style="display: grid; grid-template-columns: 140px 140px 1fr; gap: 20px; margin-bottom: 24px; align-items: flex-end;">
          <div>
            <label class="book-field-label" for="startTimeInput">Start Time:</label>
            <div class="time-input-group">
              <input 
                type="text" 
                class="book-input-control" 
                id="startTimeInput" 
                name="start_time" 
                value="7:00 AM"
                placeholder="7:00 AM"
                required
              >
              <span class="icon-inside">🕒</span>
            </div>
          </div>

          <div>
            <label class="book-field-label" for="endTimeInput">End Time:</label>
            <div class="time-input-group">
              <input 
                type="text" 
                class="book-input-control" 
                id="endTimeInput" 
                name="end_time" 
                value="10:00 AM"
                placeholder="10:00 AM"
                required
              >
              <span class="icon-inside">🕒</span>
            </div>
          </div>

          <!-- Conflict Warning Banner matching Screenshot -->
          <div class="time-conflict-warning" id="timeConflictAlert">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink: 0;">
              <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>
            </svg>
            <span>This room is already booked 8:00–9:00 AM. Pick a different time.</span>
          </div>
        </div>

        <!-- Row 4: Subject / purpose -->
        <div style="max-width: 320px; margin-bottom: 34px;">
          <label class="book-field-label" for="subjectPurposeInput">Subject / purpose</label>
          <input 
            type="text" 
            class="book-input-control" 
            id="subjectPurposeInput" 
            name="subject_purpose" 
            placeholder="GEC 8: Ethics"
            value="GEC 8: Ethics"
            required
          >
        </div>

        <!-- Row 5: Submit Button (Lime Green Pill) -->
        <div>
          <button 
            type="submit" 
            class="btn btn-lime" 
            id="btnSubmitBookingRequest"
            style="padding: 12px 34px; font-size: 0.95rem; font-weight: 700; border-radius: var(--radius-pill);"
          >
            Submit booking request
          </button>
        </div>

      </form>
    </div>

  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    function updateRoomCapacity(selectEl) {
      const selectedOption = selectEl.options[selectEl.selectedIndex];
      const cap = selectedOption.dataset.cap || '24';
      document.getElementById('displayCapacityCount').textContent = cap;
    }

    // Interactive time change check
    const startInp = document.getElementById('startTimeInput');
    const endInp = document.getElementById('endTimeInput');
    const warningBox = document.getElementById('timeConflictAlert');

    function checkTimeConflict() {
      const s = startInp.value.toUpperCase();
      // If time overlaps 8:00 AM or 9:00 AM, show conflict note as in screenshot
      if (s.includes('7') || s.includes('8')) {
        warningBox.style.display = 'flex';
      } else {
        warningBox.style.display = 'none';
      }
    }

    startInp.addEventListener('input', checkTimeConflict);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
