<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>VoiceBoard — โน้ตเสียง (HTML/CSS/JS)</title>
  <style>
    :root{--bg:#f7fafc;--board:#ffffff;--note-shadow:0 6px 12px rgba(0,0,0,0.08);--accent:#ef4444}
    *{box-sizing:border-box}
    body{font-family:Inter,ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial; margin:0; background:var(--bg); color:#111}
    .container{max-width:1100px;margin:20px auto;padding:16px}
    header{display:flex;align-items:center;justify-content:space-between;gap:12px}
    h1{font-size:20px;margin:0}
    .controls{display:flex;gap:8px;align-items:center}
    button, label.control{cursor:pointer;border:0;padding:8px 12px;border-radius:8px;background:#1f2937;color:#fff}
    button.ghost{background:#e5e7eb;color:#111;border:1px solid #d1d5db}
    label.control input{display:none}
    .board{margin-top:12px;height:68vh;background:var(--board);border:2px dashed #e6e6e6;border-radius:8px;position:relative;overflow:hidden}
    .note{width:210px;padding:10px;border-radius:8px;position:absolute;box-shadow:var(--note-shadow);cursor:grab;display:flex;flex-direction:column;gap:8px}
    .note:active{cursor:grabbing}
    .note .meta{display:flex;justify-content:space-between;align-items:center}
    .meta button{background:rgba(255,255,255,0.8);color:#111;padding:6px;border-radius:6px;border:0}
    .note audio{width:100%;outline:none}
    .hint{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#9ca3af;pointer-events:none}
    footer{margin-top:10px;color:#6b7280;font-size:13px}
    /* color variants */
    .yellow{background:#fef3c7}
    .green{background:#dcfce7}
    .blue{background:#dbeafe}
    .pink{background:#fce7f3}
    .orange{background:#ffedd5}
    .purple{background:#ede9fe}
    /* responsive */
    @media (max-width:640px){.note{width:160px}}
  </style>
</head>
<body>
  <div class="container">
    <header>
      <h1>VoiceBoard — โน้ตเสียง</h1>
      <div class="controls">
        <button id="recordBtn">เริ่มบันทึกเสียง</button>
        <button id="stopBtn" style="display:none;background:var(--accent);">หยุด & บันทึก</button>
        <button id="exportBtn" class="ghost">ส่งออก</button>
        <label class="control ghost">นำเข้า<input id="importFile" type="file" accept="application/json"/></label>
        <button id="clearBtn" class="ghost">ล้าง</button>
      </div>
    </header>

    <div id="board" class="board">
      <div class="hint" id="hint">กด "เริ่มบันทึกเสียง" เพื่อสร้างโน้ต แล้วลากเพื่อย้ายตำแหน่ง</div>
    </div>

    <footer>ใช้งานบน localhost หรือ HTTPS เท่านั้น (ต้องการสิทธิ์ไมโครโฟน). ข้อมูลเก็บใน localStorage.</footer>
  </div>

  <script>
    // Simple VoiceBoard - plain JS
    const STORAGE_KEY = 'voiceboard_notes_v1';
    const board = document.getElementById('board');
    const recordBtn = document.getElementById('recordBtn');
    const stopBtn = document.getElementById('stopBtn');
    const exportBtn = document.getElementById('exportBtn');
    const importFile = document.getElementById('importFile');
    const clearBtn = document.getElementById('clearBtn');
    const hint = document.getElementById('hint');

    let notes = [];
    let mediaRecorder = null;
    let chunks = [];
    let streamRef = null;

    function uid(){ return Math.random().toString(36).slice(2,9); }
    function randomColorClass(){ const p=['yellow','green','blue','pink','orange','purple']; return p[Math.floor(Math.random()*p.length)]; }

    function save(){ localStorage.setItem(STORAGE_KEY, JSON.stringify(notes)); }
    function load(){ try{ const raw=localStorage.getItem(STORAGE_KEY); if(raw) notes=JSON.parse(raw); else notes=[]; }catch(e){console.error(e); notes=[];} }

    function render(){ board.querySelectorAll('.note').forEach(n=>n.remove());
      if(notes.length===0) hint.style.display='flex'; else hint.style.display='none';
      notes.forEach(n=>{
        const el = document.createElement('div'); el.className='note '+n.color; el.style.left = n.x+'px'; el.style.top = n.y+'px';
        el.dataset.id = n.id;

        const meta = document.createElement('div'); meta.className='meta';
        const title = document.createElement('strong'); title.textContent='โน้ต'; title.style.fontSize='13px';
        const controls = document.createElement('div');

        const dl = document.createElement('button'); dl.title='ดาวน์โหลด'; dl.textContent='⤓';
        dl.onclick = (ev)=>{ ev.stopPropagation(); downloadNote(n); };
        const del = document.createElement('button'); del.title='ลบ'; del.textContent='✖';
        del.onclick = (ev)=>{ ev.stopPropagation(); deleteNote(n.id); };
        controls.appendChild(dl); controls.appendChild(del);
        meta.appendChild(title); meta.appendChild(controls);

        el.appendChild(meta);

        if(n.audioBase64){
          const audio = document.createElement('audio'); audio.controls=true; audio.src = base64ToUrl(n.audioBase64);
          el.appendChild(audio);
        } else {
          const info = document.createElement('div'); info.textContent='ไม่มีเสียง'; info.style.fontSize='13px'; info.style.color='#6b7280'; el.appendChild(info);
        }

        // dragging
        el.addEventListener('pointerdown', (e)=>{
          el.setPointerCapture(e.pointerId);
          const startX=e.clientX, startY=e.clientY;
          const origX = n.x, origY = n.y;
          const onMove = (ev)=>{
            const dx = ev.clientX - startX, dy = ev.clientY - startY;
            n.x = Math.max(0, Math.min(board.clientWidth - el.clientWidth, origX + dx));
            n.y = Math.max(0, Math.min(board.clientHeight - el.clientHeight, origY + dy));
            el.style.left = n.x + 'px'; el.style.top = n.y + 'px';
          };
          const onUp = ()=>{ save(); window.removeEventListener('pointermove', onMove); window.removeEventListener('pointerup', onUp); };
          window.addEventListener('pointermove', onMove); window.addEventListener('pointerup', onUp);
        });

        board.appendChild(el);
      });
    }

    function base64ToUrl(base64, type='audio/webm'){ const binary = atob(base64); const len=binary.length; const bytes=new Uint8Array(len); for(let i=0;i<len;i++) bytes[i]=binary.charCodeAt(i); const blob=new Blob([bytes],{type}); return URL.createObjectURL(blob); }
    function blobToBase64(blob){ return new Promise((resolve,reject)=>{ const r=new FileReader(); r.onloadend=()=>resolve(r.result.split(',')[1]); r.onerror=reject; r.readAsDataURL(blob); }); }

    async function startRecording(){ try{
        const s = await navigator.mediaDevices.getUserMedia({audio:true}); streamRef = s; mediaRecorder = new MediaRecorder(s); chunks=[];
        mediaRecorder.ondataavailable = e=>{ if(e.data && e.data.size>0) chunks.push(e.data); };
        mediaRecorder.start(); recordBtn.style.display='none'; stopBtn.style.display='inline-block';
      }catch(err){ alert('ไม่สามารถเข้าถึงไมโครโฟน: '+err.message); }
    }

    async function stopRecordingAndSave(){ if(!mediaRecorder) return; mediaRecorder.stop(); stopBtn.style.display='none'; recordBtn.style.display='inline-block';
      if(streamRef){ streamRef.getTracks().forEach(t=>t.stop()); streamRef=null; }
      if(chunks.length===0) return;
      const blob = new Blob(chunks,{type:'audio/webm'}); chunks=[];
      const base64 = await blobToBase64(blob);
      const newNote = { id:uid(), x:40+Math.random()*220, y:40+Math.random()*120, audioBase64:base64, createdAt:Date.now(), color:randomColorClass() };
      notes.push(newNote); save(); render();
    }

    function deleteNote(id){ if(!confirm('ลบโน้ตนี้?')) return; notes = notes.filter(n=>n.id!==id); save(); render(); }
    function downloadNote(note){ if(!note.audioBase64) return; const url = base64ToUrl(note.audioBase64); const a=document.createElement('a'); a.href=url; a.download = 'voice-note-'+note.id+'.webm'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url); }

    function exportBoard(){ const data = JSON.stringify(notes); const blob = new Blob([data],{type:'application/json'}); const url = URL.createObjectURL(blob); const a=document.createElement('a'); a.href=url; a.download='voiceboard-export.json'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url); }
    function importBoard(file){ const r=new FileReader(); r.onload=()=>{ try{ const parsed=JSON.parse(r.result); if(Array.isArray(parsed)){ notes=parsed; save(); render(); } else alert('ไฟล์ไม่ถูกต้อง'); }catch(e){ alert('นำเข้าไม่สำเร็จ: '+e.message); } }; r.readAsText(file); }
    function clearBoard(){ if(confirm('ลบโน้ตทั้งหมด?')){ notes=[]; save(); render(); } }

    // wire events
    recordBtn.addEventListener('click', startRecording);
    stopBtn.addEventListener('click', stopRecordingAndSave);
    exportBtn.addEventListener('click', exportBoard);
    importFile.addEventListener('change', (e)=>{ if(e.target.files.length) importBoard(e.target.files[0]); e.target.value=''; });
    clearBtn.addEventListener('click', clearBoard);

    // init
    load(); render();

    // Tips: revoke generated URLs when necessary (not strictly required here since browser manages them)
  </script>
</body>
</html>
