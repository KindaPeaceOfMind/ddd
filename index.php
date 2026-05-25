<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Легенды Старого Центра — Квест по Хабаровску</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .header {
            background: rgba(0, 0, 0, 0.8);
            padding: 15px 20px;
            text-align: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .header h1 {
            font-size: 1.5rem;
            color: #f39c12;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .header p {
            font-size: 0.9rem;
            color: #bdc3c7;
            margin-top: 5px;
        }

        .main-container {
            margin-top: 80px;
            padding: 20px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        #map {
            width: 100%;
            height: 60vh;
            min-height: 400px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
        }

        .progress-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }

        .progress-bar {
            width: 100%;
            height: 30px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            overflow: hidden;
            margin: 15px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #f39c12, #e74c3c);
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .locations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .location-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .location-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }

        .location-card.completed {
            border-color: #27ae60;
            background: rgba(39, 174, 96, 0.2);
        }

        .location-card.active {
            border-color: #f39c12;
            background: rgba(243, 156, 18, 0.2);
        }

        .location-card.locked {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .location-title {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 8px;
            color: #f39c12;
        }

        .location-status {
            font-size: 0.85rem;
            color: #bdc3c7;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.9);
            z-index: 2000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: linear-gradient(135deg, #2c3e50 0%, #1a1a2e 100%);
            border-radius: 20px;
            padding: 30px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
        }

        .modal-title {
            font-size: 1.5rem;
            color: #f39c12;
            margin-bottom: 15px;
        }

        .modal-description {
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #ecf0f1;
        }

        .question-section {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }

        .question-text {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #f39c12;
        }

        .options-list {
            list-style: none;
        }

        .option-item {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid transparent;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .option-item:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: #f39c12;
        }

        .option-item.selected {
            background: rgba(243, 156, 18, 0.3);
            border-color: #f39c12;
        }

        .option-item.correct {
            background: rgba(39, 174, 96, 0.3);
            border-color: #27ae60;
        }

        .option-item.wrong {
            background: rgba(231, 76, 60, 0.3);
            border-color: #e74c3c;
        }

        .submit-btn {
            background: linear-gradient(90deg, #f39c12, #e74c3c);
            color: #fff;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: transform 0.3s ease;
        }

        .submit-btn:hover {
            transform: scale(1.05);
        }

        .submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* QR Code Section */
        .qr-section {
            display: none;
            text-align: center;
            padding: 40px 20px;
        }

        .qr-section.active {
            display: block;
        }

        .qr-code {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            display: inline-block;
            margin: 20px 0;
        }

        .qr-message {
            font-size: 1.5rem;
            color: #27ae60;
            margin-bottom: 20px;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-top-color: #f39c12;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .feedback {
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            font-weight: bold;
        }

        .feedback.success {
            background: rgba(39, 174, 96, 0.3);
            color: #27ae60;
        }

        .feedback.error {
            background: rgba(231, 76, 60, 0.3);
            color: #e74c3c;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.2rem;
            }

            .header p {
                font-size: 0.8rem;
            }

            #map {
                height: 50vh;
                min-height: 300px;
            }

            .modal-content {
                padding: 20px;
            }

            .locations-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏛️ Легенды Старого Центра</h1>
        <p>Интерактивный квест по Хабаровску</p>
    </div>

    <div class="main-container">
        <div id="map"></div>

        <div class="progress-section">
            <h2>📊 Ваш прогресс</h2>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 0%">0/10</div>
            </div>
            <p id="progressText">Загрузка данных...</p>
        </div>

        <div class="locations-grid" id="locationsGrid">
            <!-- Location cards will be inserted here -->
        </div>

        <div class="qr-section" id="qrSection">
            <div class="qr-message">🎉 Поздравляем! Вы прошли весь маршрут!</div>
            <div class="qr-code" id="qrCode"></div>
            <p>Покажите этот QR-код организаторам для получения приза</p>
        </div>
    </div>

    <!-- Question Modal -->
    <div class="modal-overlay" id="questionModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <h2 class="modal-title" id="modalTitle"></h2>
            <div class="modal-description" id="modalDescription"></div>
            
            <div class="question-section" id="questionSection">
                <div class="question-text" id="questionText"></div>
                <ul class="options-list" id="optionsList"></ul>
                <div id="feedback"></div>
                <button class="submit-btn" id="submitBtn" onclick="submitAnswer()" disabled>Ответить</button>
            </div>
        </div>
    </div>

    <script>
        // Global state
        let locations = [];
        let progress = {
            completed: [],
            currentLocation: null
        };
        let selectedOption = null;
        let map = null;
        let placemarks = [];

        // Initialize the application
        async function init() {
            loadProgressFromCache();
            await loadLocationsData();
            initMap();
            renderLocations();
            updateProgress();
        }

        // Load progress from localStorage
        function loadProgressFromCache() {
            const saved = localStorage.getItem('questProgress');
            if (saved) {
                try {
                    progress = JSON.parse(saved);
                } catch (e) {
                    console.error('Error loading progress:', e);
                }
            }
        }

        // Save progress to localStorage
        function saveProgress() {
            localStorage.setItem('questProgress', JSON.stringify(progress));
        }

        // Load locations data from PHP backend
        async function loadLocationsData() {
            try {
                const response = await fetch('data.php');
                if (!response.ok) throw new Error('Failed to load data');
                locations = await response.json();
                document.getElementById('progressText').textContent = 
                    `Загружено ${locations.length} мест`;
            } catch (error) {
                console.error('Error loading locations:', error);
                document.getElementById('progressText').textContent = 
                    'Ошибка загрузки данных. Обновите страницу.';
            }
        }

        // Initialize Yandex Map
        function initMap() {
            // Khabarovsk coordinates
            const khabarovsk = [48.4827, 135.0838];

            ymaps.ready(function() {
                map = new ymaps.Map('map', {
                    center: khabarovsk,
                    zoom: 14,
                    controls: ['zoomControl', 'fullscreenControl']
                });

                // Center on first uncompleted location or first location
                if (progress.completed.length > 0 && progress.completed.length < locations.length) {
                    const nextLocation = locations.find(loc => !progress.completed.includes(loc.id));
                    if (nextLocation) {
                        map.setCenter(nextLocation.coords, 16);
                    }
                } else if (locations.length > 0) {
                    map.setCenter(locations[0].coords, 16);
                }

                // Add placemarks for all locations
                locations.forEach((location, index) => {
                    const isCompleted = progress.completed.includes(location.id);
                    const isNext = !isCompleted && 
                        (progress.completed.length === 0 || 
                         progress.completed.length === index);

                    const placemark = new ymaps.Placemark(location.coords, {
                        hintContent: location.title,
                        balloonContent: `<strong>${location.title}</strong><br>${location.description.substring(0, 100)}...`
                    }, {
                        preset: isCompleted ? 'islands#greenCircleIcon' : 
                               (isNext ? 'islands#orangeCircleIcon' : 'islands#grayCircleIcon')
                    });

                    placemark.events.add('click', () => openLocation(location));
                    map.geoObjects.add(placemark);
                    placemarks.push(placemark);
                });
            });
        }

        // Render location cards
        function renderLocations() {
            const grid = document.getElementById('locationsGrid');
            grid.innerHTML = '';

            locations.forEach((location, index) => {
                const isCompleted = progress.completed.includes(location.id);
                const isLocked = index > 0 && !progress.completed.includes(locations[index - 1].id);
                const isActive = !isCompleted && !isLocked && 
                    (progress.completed.length === 0 || 
                     progress.completed.length === index);

                const card = document.createElement('div');
                card.className = `location-card ${isCompleted ? 'completed' : ''} ${isActive ? 'active' : ''} ${isLocked ? 'locked' : ''}`;
                card.onclick = () => {
                    if (!isLocked) openLocation(location);
                };

                card.innerHTML = `
                    <div class="location-title">${isCompleted ? '✅ ' : ''}${location.title}</div>
                    <div class="location-status">
                        ${isCompleted ? 'Пройдено' : (isLocked ? '🔒 Заблокировано' : '📍 Доступно')}
                    </div>
                `;

                grid.appendChild(card);
            });
        }

        // Open location modal
        function openLocation(location) {
            const isCompleted = progress.completed.includes(location.id);
            
            document.getElementById('modalTitle').textContent = location.title;
            document.getElementById('modalDescription').innerHTML = location.fullDescription;
            
            if (isCompleted) {
                document.getElementById('questionSection').style.display = 'none';
            } else {
                document.getElementById('questionSection').style.display = 'block';
                document.getElementById('questionText').textContent = location.question;
                
                const optionsList = document.getElementById('optionsList');
                optionsList.innerHTML = '';
                
                location.options.forEach((option, idx) => {
                    const li = document.createElement('li');
                    li.className = 'option-item';
                    li.textContent = option;
                    li.onclick = () => selectOption(idx, li);
                    optionsList.appendChild(li);
                });

                document.getElementById('feedback').innerHTML = '';
                document.getElementById('submitBtn').disabled = true;
                selectedOption = null;
            }

            document.getElementById('questionModal').classList.add('active');
        }

        // Select answer option
        function selectOption(index, element) {
            document.querySelectorAll('.option-item').forEach(item => {
                item.classList.remove('selected');
            });
            element.classList.add('selected');
            selectedOption = index;
            document.getElementById('submitBtn').disabled = false;
        }

        // Submit answer
        async function submitAnswer() {
            if (selectedOption === null) return;

            const currentLocation = locations.find(loc => 
                !progress.completed.includes(loc.id) &&
                (progress.completed.length === 0 || 
                 progress.completed.length === locations.indexOf(loc))
            );

            if (!currentLocation) return;

            const feedback = document.getElementById('feedback');
            const submitBtn = document.getElementById('submitBtn');
            
            submitBtn.disabled = true;

            try {
                const response = await fetch('process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        locationId: currentLocation.id,
                        selectedOption: selectedOption,
                        correctAnswer: currentLocation.correctAnswer
                    })
                });

                const result = await response.json();

                if (result.correct) {
                    feedback.className = 'feedback success';
                    feedback.textContent = '✅ Правильно! Следующее место разблокировано.';
                    
                    // Mark as completed
                    if (!progress.completed.includes(currentLocation.id)) {
                        progress.completed.push(currentLocation.id);
                        saveProgress();
                    }

                    // Update UI after delay
                    setTimeout(() => {
                        closeModal();
                        updateProgress();
                        renderLocations();
                        updateMapMarkers();
                        
                        // Check if all completed
                        if (progress.completed.length === locations.length) {
                            showQRCode();
                        }
                    }, 1500);
                } else {
                    feedback.className = 'feedback error';
                    feedback.textContent = '❌ Неверно. Попробуйте ещё раз!';
                    
                    document.querySelector('.option-item.selected').classList.add('wrong');
                    submitBtn.disabled = false;
                    selectedOption = null;
                }
            } catch (error) {
                console.error('Error submitting answer:', error);
                feedback.className = 'feedback error';
                feedback.textContent = 'Ошибка соединения. Попробуйте ещё раз.';
                submitBtn.disabled = false;
            }
        }

        // Close modal
        function closeModal() {
            document.getElementById('questionModal').classList.remove('active');
        }

        // Update progress bar
        function updateProgress() {
            const percentage = (progress.completed.length / locations.length) * 100;
            const fill = document.getElementById('progressFill');
            fill.style.width = percentage + '%';
            fill.textContent = `${progress.completed.length}/${locations.length}`;

            const text = document.getElementById('progressText');
            if (progress.completed.length === locations.length) {
                text.textContent = '🎉 Все места пройдены!';
            } else if (progress.completed.length > 0) {
                text.textContent = `Пройдено ${progress.completed.length} из ${locations.length} мест`;
            }
        }

        // Update map markers
        function updateMapMarkers() {
            if (!map) return;

            placemarks.forEach((placemark, index) => {
                const location = locations[index];
                const isCompleted = progress.completed.includes(location.id);
                const isNext = !isCompleted && 
                    (progress.completed.length === 0 || 
                     progress.completed.length === index);

                placemark.options.set('preset', 
                    isCompleted ? 'islands#greenCircleIcon' : 
                    (isNext ? 'islands#orangeCircleIcon' : 'islands#grayCircleIcon'));
            });
        }

        // Show QR code when all locations completed
        function showQRCode() {
            document.getElementById('qrSection').classList.add('active');
            
            // Generate QR code using API
            const qrData = encodeURIComponent(JSON.stringify({
                completed: true,
                timestamp: Date.now(),
                quest: 'legends-old-center-khabarovsk'
            }));
            
            document.getElementById('qrCode').innerHTML = 
                `<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${qrData}" alt="QR Code">`;
            
            // Scroll to QR section
            document.getElementById('qrSection').scrollIntoView({ behavior: 'smooth' });
        }

        // Initialize on page load
        window.addEventListener('DOMContentLoaded', init);
    </script>
    
    <!-- Yandex Maps API -->
    <script src="https://api-maps.yandex.ru/2.1/?apikey=YOUR_API_KEY&lang=ru_RU"></script>
</body>
</html>
