<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoiceNote Board - บอร์ดโน้ตเสียงแบบโต้ตอบ</title>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --accent: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4cc9f0;
            --warning: #ffd166;
            --danger: #ef476f;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: var(--dark);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        header {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        h1 {
            color: var(--primary);
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        h1 i {
            color: var(--accent);
        }
        
        .room-selector {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .room-selector select {
            padding: 10px 15px;
            border-radius: 8px;
            border: 2px solid var(--primary);
            font-weight: bold;
            background-color: white;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
        }
        
        .btn-success {
            background-color: var(--success);
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .main-content {
            display: flex;
            gap: 20px;
        }
        
        .tools-panel {
            width: 250px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .tools-panel h2 {
            color: var(--primary);
            font-size: 20px;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light);
        }
        
        .tool-item {
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            cursor: move;
            text-align: center;
            transition: all 0.2s ease;
        }
        
        .tool-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .tool-item i {
            font-size: 24px;
            margin-bottom: 8px;
            color: var(--primary);
        }
        
        .board {
            flex: 1;
            min-height: 70vh;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
            background-image: radial-gradient(#ccc 1px, transparent 1px);
            background-size: 20px 20px;
        }
        
        .note {
            position: absolute;
            width: 200px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            cursor: move;
            user-select: none;
        }
        
        .voice-note {
            background-color: #ffecd9;
            border-left: 5px solid var(--warning);
        }
        
        .text-note {
            background-color: #d9edf7;
            border-left: 5px solid var(--primary);
        }
        
        .sticker-note {
            background-color: #e0f0e3;
            border-left: 5px solid var(--success);
            text-align: center;
        }
        
        .note-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .note-controls button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            color: var(--dark);
        }
        
        .audio-player {
            width: 100%;
            margin-top: 10px;
        }
        
        .recorder {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-top: 20px;
            text-align: center;
        }
        
        .recorder h3 {
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .recorder-controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
        }
        
        .rec-timer {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
            color: var(--dark);
        }
        
        .pulse {
            animation: pulse-animation 1.5s infinite;
        }
        
        @keyframes pulse-animation {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        footer {
            text-align: center;
            margin-top: 20px;
            color: white;
            font-size: 14px;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
            }
            
            .tools-panel {
                width: 100%;
            }
            
            header {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-microphone-alt"></i> VoiceNote Board</h1>
            <div class="room-selector">
                <span>ห้อง:</span>
                <select id="roomSelect">
                    <option value="room1">ห้องทำงานหลัก</option>
                    <option value="room2">ห้องประชุม</option>
                    <option value="room3">ห้องส่วนตัว</option>
                    <option value="room4">ใหม่...</option>
                </select>
            </div>
        </header>
        
        <div class="main-content">
            <div class="tools-panel">
                <h2><i class="fas fa-tools"></i> เครื่องมือ</h2>
                
                <div class="tool-item" draggable="true" data-type="voice">
                    <i class="fas fa-microphone"></i>
                    <p>โน้ตเสียง</p>
                </div>
                
                <div class="tool-item" draggable="true" data-type="text">
                    <i class="fas fa-sticky-note"></i>
                    <p>โน้ตข้อความ</p>
                </div>
                
                <div class="tool-item" draggable="true" data-type="sticker">
                    <i class="fas fa-smile"></i>
                    <p>สติ๊กเกอร์</p>
                </div>
                
                <div class="recorder">
                    <h3><i class="fas fa-record-vinyl"></i> อัดเสียง</h3>
                    <button id="recordBtn" class="btn btn-danger">
                        <i class="fas fa-circle"></i> เริ่มบันทึก
                    </button>
                    <div class="rec-timer" id="timer">00:00</div>
                    <div class="recorder-controls">
                        <button id="playBtn" class="btn btn-success" disabled>
                            <i class="fas fa-play"></i> เล่น
                        </button>
                        <button id="saveBtn" class="btn btn-primary" disabled>
                            <i class="fas fa-save"></i> บันทึก
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="board" id="board">
                <!-- Notes will be added here dynamically -->
                <div class="note voice-note" style="top: 50px; left: 100px;">
                    <div class="note-header">
                        <span><i class="fas fa-microphone"></i> โน้ตเสียง</span>
                        <div class="note-controls">
                            <button><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <p>ความคิดเกี่ยวกับโครงการใหม่</p>
                    <audio controls class="audio-player">
                        <source src="#" type="audio/mpeg">
                        Browser ของคุณไม่รองรับการเล่นเสียง
                    </audio>
                </div>
                
                <div class="note text-note" style="top: 200px; left: 300px;">
                    <div class="note-header">
                        <span><i class="fas fa-sticky-note"></i> ข้อความ</span>
                        <div class="note-controls">
                            <button><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <p>นัดประชุมกับทีม marketing วันศุกร์นี้ 10:00 น.</p>
                </div>
                
                <div class="note sticker-note" style="top: 350px; left: 150px;">
                    <div class="note-header">
                        <span><i class="fas fa-smile"></i> สติ๊กเกอร์</span>
                        <div class="note-controls">
                            <button><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <p style="font-size: 32px;">👍</p>
                </div>
            </div>
        </div>
        
        <footer>
            <p>VoiceNote Board &copy; 2023 - เว็บแอปสำหรับบันทึกและแปะโน้ตเสียง</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ตัวแปรสำหรับการบันทึกเสียง
            let mediaRecorder;
            let audioChunks = [];
            let isRecording = false;
            let timerInterval;
            let seconds = 0;
            let recordedAudio = null;
            
            // Elements
            const recordBtn = document.getElementById('recordBtn');
            const playBtn = document.getElementById('playBtn');
            const saveBtn = document.getElementById('saveBtn');
            const timer = document.getElementById('timer');
            const board = document.getElementById('board');
            
            // เริ่มการบันทึกเสียง
            recordBtn.addEventListener('click', toggleRecording);
            
            // เล่นเสียงที่บันทึก
            playBtn.addEventListener('click', playRecordedAudio);
            
            // บันทึกโน้ตเสียง
            saveBtn.addEventListener('click', saveAudioNote);
            
            // ทำให้เครื่องมือสามารถลากได้
            const toolItems = document.querySelectorAll('.tool-item');
            toolItems.forEach(item => {
                item.addEventListener('dragstart', handleDragStart);
            });
            
            // ตั้งค่า board เป็น drop zone
            board.addEventListener('dragover', handleDragOver);
            board.addEventListener('drop', handleDrop);
            
            // ฟังก์ชันจัดการการบันทึกเสียง
            async function toggleRecording() {
                if (!isRecording) {
                    // เริ่มบันทึก
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        mediaRecorder = new MediaRecorder(stream);
                        audioChunks = [];
                        
                        mediaRecorder.ondataavailable = event => {
                            audioChunks.push(event.data);
                        };
                        
                        mediaRecorder.onstop = () => {
                            const audioBlob = new Blob(audioChunks, { type: 'audio/mpeg' });
                            recordedAudio = URL.createObjectURL(audioBlob);
                            playBtn.disabled = false;
                            saveBtn.disabled = false;
                        };
                        
                        mediaRecorder.start();
                        isRecording = true;
                        recordBtn.innerHTML = '<i class="fas fa-stop"></i> หยุดบันทึก';
                        recordBtn.classList.add('pulse');
                        startTimer();
                    } catch (error) {
                        alert('ไม่สามารถเข้าถึงไมโครโฟนได้: ' + error.message);
                    }
                } else {
                    // หยุดบันทึก
                    mediaRecorder.stop();
                    isRecording = false;
                    recordBtn.innerHTML = '<i class="fas fa-circle"></i> เริ่มบันทึก';
                    recordBtn.classList.remove('pulse');
                    stopTimer();
                }
            }
            
            // ฟังก์ชันเล่นเสียงที่บันทึก
            function playRecordedAudio() {
                if (recordedAudio) {
                    const audio = new Audio(recordedAudio);
                    audio.play();
                }
            }
            
            // ฟังก์ชันบันทึกโน้ตเสียง
            function saveAudioNote() {
                if (!recordedAudio) return;
                
                const note = document.createElement('div');
                note.className = 'note voice-note';
                note.style.top = '100px';
                note.style.left = '100px';
                note.innerHTML = `
                    <div class="note-header">
                        <span><i class="fas fa-microphone"></i> โน้ตเสียง</span>
                        <div class="note-controls">
                            <button onclick="this.parentElement.parentElement.parentElement.remove()"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <p>เสียงที่บันทึกใหม่</p>
                    <audio controls class="audio-player">
                        <source src="${recordedAudio}" type="audio/mpeg">
                    </audio>
                `;
                
                makeElementDraggable(note);
                board.appendChild(note);
                
                // รีเซ็ตการบันทึก
                recordedAudio = null;
                playBtn.disabled = true;
                saveBtn.disabled = true;
            }
            
            // ฟังก์ชันจัดการการลากและวาง
            function handleDragStart(e) {
                e.dataTransfer.setData('text/plain', e.target.dataset.type);
            }
            
            function handleDragOver(e) {
                e.preventDefault();
            }
            
            function handleDrop(e) {
                e.preventDefault();
                const type = e.dataTransfer.getData('text/plain');
                
                let note;
                if (type === 'voice') {
                    note = createVoiceNote();
                } else if (type === 'text') {
                    note = createTextNote();
                } else if (type === 'sticker') {
                    note = createStickerNote();
                }
                
                if (note) {
                    const rect = board.getBoundingClientRect();
                    note.style.top = (e.clientY - rect.top - 30) + 'px';
                    note.style.left = (e.clientX - rect.left - 30) + 'px';
                    
                    board.appendChild(note);
                    makeElementDraggable(note);
                }
            }
            
            // ฟังก์ชันสร้างโน้ตต่างๆ
            function createVoiceNote() {
                const note = document.createElement('div');
                note.className = 'note voice-note';
                note.innerHTML = `
                    <div class="note-header">
                        <span><i class="fas fa-microphone"></i> โน้ตเสียง</span>
                        <div class="note-controls">
                            <button onclick="this.parentElement.parentElement.parentElement.remove()"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <p>ลากไอคอนไมโครโฟนเพื่อบันทึกเสียง</p>
                    <button class="btn btn-primary" style="margin-top: 10px;" onclick="alert('เริ่มบันทึกเสียง')">
                        <i class="fas fa-microphone"></i> บันทึกเสียง
                    </button>
                `;
                return note;
            }
            
            function createTextNote() {
                const note = document.createElement('div');
                note.className = 'note text-note';
                note.innerHTML = `
                    <div class="note-header">
                        <span><i class="fas fa-sticky-note"></i> ข้อความ</span>
                        <div class="note-controls">
                            <button onclick="this.parentElement.parentElement.parentElement.remove()"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <p contenteditable="true">คลิกเพื่อแก้ไขข้อความ...</p>
                `;
                return note;
            }
            
            function createStickerNote() {
                const stickers = ['👍', '❤️', '😊', '🚀', '⭐', '🎉'];
                const randomSticker = stickers[Math.floor(Math.random() * stickers.length)];
                
                const note = document.createElement('div');
                note.className = 'note sticker-note';
                note.innerHTML = `
                    <div class="note-header">
                        <span><i class="fas fa-smile"></i> สติ๊กเกอร์</span>
                        <div class="note-controls">
                            <button onclick="this.parentElement.parentElement.parentElement.remove()"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <p style="font-size: 32px;">${randomSticker}</p>
                `;
                return note;
            }
            
            // ทำให้องค์ประกอบลากได้
            function makeElementDraggable(element) {
                let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
                
                element.onmousedown = dragMouseDown;
                
                function dragMouseDown(e) {
                    e.preventDefault();
                    // หาตำแหน่ง cursor
                    pos3 = e.clientX;
                    pos4 = e.clientY;
                    document.onmouseup = closeDragElement;
                    document.onmousemove = elementDrag;
                }
                
                function elementDrag(e) {
                    e.preventDefault();
                    // คำนวณตำแหน่งใหม่
                    pos1 = pos3 - e.clientX;
                    pos2 = pos4 - e.clientY;
                    pos3 = e.clientX;
                    pos4 = e.clientY;
                    // ตั้งค่าตำแหน่งใหม่
                    element.style.top = (element.offsetTop - pos2) + "px";
                    element.style.left = (element.offsetLeft - pos1) + "px";
                }
                
                function closeDragElement() {
                    document.onmouseup = null;
                    document.onmousemove = null;
                }
            }
            
            // ฟังก์ชันจับเวลา
            function startTimer() {
                seconds = 0;
                timerInterval = setInterval(() => {
                    seconds++;
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    timer.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                }, 1000);
            }
            
            function stopTimer() {
                clearInterval(timerInterval);
            }
            
            // ทำให้โน้ตที่มีอยู่แล้วลากได้
            document.querySelectorAll('.note').forEach(note => {
                makeElementDraggable(note);
            });
        });
    </script>
</body>
</html><?php /**PATH D:\xampp\htdocs\voice board\voice-board\resources\views/test.blade.php ENDPATH**/ ?>