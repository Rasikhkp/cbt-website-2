<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tab/Window Detection Demo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        
        .info-box {
            background: rgba(255,255,255,0.95);
            color: #333;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .status-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        
        .status-card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        
        .status-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }
        
        .dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #28a745;
            transition: background 0.3s;
        }
        
        .dot.inactive {
            background: #dc3545;
        }
        
        .dot.warning {
            background: #ffc107;
        }
        
        .test-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin: 20px 0;
        }
        
        button {
            padding: 12px 25px;
            font-size: 1em;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        button:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        button.danger {
            background: #dc3545;
        }
        
        button.danger:hover {
            background: #c82333;
        }
        
        #eventLog {
            background: #1e1e1e;
            color: #00ff00;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 20px;
        }
        
        .log-entry {
            padding: 5px 0;
            border-bottom: 1px solid #333;
        }
        
        .log-entry.warning {
            color: #ffaa00;
            font-weight: bold;
        }
        
        .log-entry.error {
            color: #ff4444;
            font-weight: bold;
        }
        
        .log-entry.info {
            color: #00ddff;
        }
        
        .method-explanation {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            color: #004085;
        }
        
        .code-block {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            color: #333;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
        }
        
        .timer {
            font-size: 2em;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .violation-counter {
            text-align: center;
            font-size: 1.5em;
            color: #ff4444;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Tab & Window Switching Detection</h1>
        
        <div class="info-box">
            <h2 style="color: #667eea; margin-bottom: 20px;">Live Monitoring Dashboard</h2>
            
            <div class="status-grid">
                <div class="status-card">
                    <h3>🖥️ Page Visibility</h3>
                    <div class="status-indicator">
                        <div class="dot" id="visibilityDot"></div>
                        <span id="visibilityStatus">Visible</span>
                    </div>
                    <div class="method-explanation">
                        <strong>Method:</strong> Page Visibility API<br>
                        <strong>Detects:</strong> Tab switches, minimized windows
                    </div>
                </div>
                
                <div class="status-card">
                    <h3>🎯 Window Focus</h3>
                    <div class="status-indicator">
                        <div class="dot" id="focusDot"></div>
                        <span id="focusStatus">Focused</span>
                    </div>
                    <div class="method-explanation">
                        <strong>Method:</strong> Window focus/blur events<br>
                        <strong>Detects:</strong> Clicked outside browser, Alt+Tab
                    </div>
                </div>
                
                <div class="status-card">
                    <h3>⏱️ User Activity</h3>
                    <div class="status-indicator">
                        <div class="dot" id="activityDot"></div>
                        <span id="activityStatus">Active</span>
                    </div>
                    <div class="method-explanation">
                        <strong>Method:</strong> Mouse/keyboard tracking<br>
                        <strong>Detects:</strong> Idle time, no interaction
                    </div>
                </div>
                
                <div class="status-card">
                    <h3>📱 Mouse Position</h3>
                    <div class="status-indicator">
                        <div class="dot" id="mouseDot"></div>
                        <span id="mouseStatus">Inside window</span>
                    </div>
                    <div class="method-explanation">
                        <strong>Method:</strong> Mouse leave detection<br>
                        <strong>Detects:</strong> Mouse moved outside browser
                    </div>
                </div>
            </div>
            
            <div class="timer">
                ⏱️ Time on Page: <span id="timeOnPage">0</span>s | 
                Away Time: <span id="awayTime">0</span>s
            </div>
            
            <div class="violation-counter">
                🚨 Total Violations: <span id="violationCount">0</span>
            </div>
            
            <div class="test-buttons">
                <button onclick="startMonitoring()">▶️ Start Monitoring</button>
                <button onclick="stopMonitoring()" class="danger">⏹️ Stop Monitoring</button>
                <button onclick="clearLog()">🗑️ Clear Log</button>
                <button onclick="openNewTab()">🔗 Open New Tab (Test)</button>
                <button onclick="simulateViolation()">⚠️ Simulate Violation</button>
            </div>
        </div>
        
        <div class="info-box">
            <h2 style="color: #667eea; margin-bottom: 20px;">📊 Detection Methods Explained</h2>
            
            <h3 style="margin-top: 20px;">1️⃣ Page Visibility API (Most Reliable)</h3>
            <div class="code-block">
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        console.log('User switched tab or minimized window');
    } else {
        console.log('User returned to tab');
    }
});
            </div>
            <p><strong>Detects:</strong> Tab switches, window minimization, browser hidden behind other apps</p>
            
            <h3 style="margin-top: 20px;">2️⃣ Window Focus/Blur Events</h3>
            <div class="code-block">
window.addEventListener('blur', () => {
    console.log('Window lost focus - user clicked outside');
});

window.addEventListener('focus', () => {
    console.log('Window regained focus');
});
            </div>
            <p><strong>Detects:</strong> Alt+Tab, clicking on desktop, switching to other applications</p>
            
            <h3 style="margin-top: 20px;">3️⃣ Mouse Leave Detection</h3>
            <div class="code-block">
document.addEventListener('mouseleave', () => {
    console.log('Mouse left browser window');
});
            </div>
            <p><strong>Detects:</strong> Mouse moved outside browser boundaries</p>
            
            <h3 style="margin-top: 20px;">4️⃣ Activity Tracking</h3>
            <div class="code-block">
let lastActivity = Date.now();
['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart']
    .forEach(event => {
        document.addEventListener(event, () => {
            lastActivity = Date.now();
        });
    });
            </div>
            <p><strong>Detects:</strong> Inactivity, user not interacting with the page</p>
        </div>
        
        <div class="info-box">
            <h2 style="color: #667eea; margin-bottom: 20px;">📝 Event Log</h2>
            <div id="eventLog">
                <div class="log-entry info">[System] Monitoring initialized. Try switching tabs to test detection!</div>
            </div>
        </div>
    </div>

    <script>
        let monitoringActive = false;
        let violations = 0;
        let pageStartTime = Date.now();
        let totalAwayTime = 0;
        let currentAwayStart = null;
        let lastActivity = Date.now();
        let activityCheckInterval;
        let timeUpdateInterval;

        // DOM elements
        const visibilityDot = document.getElementById('visibilityDot');
        const visibilityStatus = document.getElementById('visibilityStatus');
        const focusDot = document.getElementById('focusDot');
        const focusStatus = document.getElementById('focusStatus');
        const activityDot = document.getElementById('activityDot');
        const activityStatus = document.getElementById('activityStatus');
        const mouseDot = document.getElementById('mouseDot');
        const mouseStatus = document.getElementById('mouseStatus');
        const eventLog = document.getElementById('eventLog');
        const violationCount = document.getElementById('violationCount');
        const timeOnPage = document.getElementById('timeOnPage');
        const awayTime = document.getElementById('awayTime');

        // Logging function
        function addLog(message, type = 'info') {
            const entry = document.createElement('div');
            entry.className = `log-entry ${type}`;
            const timestamp = new Date().toLocaleTimeString();
            entry.textContent = `[${timestamp}] ${message}`;
            eventLog.appendChild(entry);
            eventLog.scrollTop = eventLog.scrollHeight;

            // In production, send to server
            if (type === 'warning' || type === 'error') {
                logToServer(message, type);
            }
        }

        // Record violation
        function recordViolation(message) {
            if (!monitoringActive) return;
            
            violations++;
            violationCount.textContent = violations;
            addLog(message, 'error');
        }

        // Send to server (placeholder)
        function logToServer(message, type) {
            const data = {
                student_id: 'STUDENT_ID',
                message: message,
                type: type,
                timestamp: new Date().toISOString(),
                violations: violations
            };
            console.log('Would send to server:', data);
            
            // In production:
            // fetch('/api/log-activity', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify(data)
            // });
        }

        // 1. PAGE VISIBILITY API (Most reliable for tab switching)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                visibilityDot.className = 'dot inactive';
                visibilityStatus.textContent = 'Hidden';
                currentAwayStart = Date.now();
                recordViolation('🚨 VIOLATION: Tab hidden or switched to another tab');
            } else {
                visibilityDot.className = 'dot';
                visibilityStatus.textContent = 'Visible';
                
                if (currentAwayStart) {
                    const awayDuration = Date.now() - currentAwayStart;
                    totalAwayTime += awayDuration;
                    addLog(`✅ Returned to tab (was away for ${Math.round(awayDuration/1000)}s)`, 'info');
                    currentAwayStart = null;
                }
            }
        });

        // 2. WINDOW FOCUS/BLUR (Detects window switching)
        window.addEventListener('blur', () => {
            focusDot.className = 'dot inactive';
            focusStatus.textContent = 'Lost Focus';
            recordViolation('🚨 VIOLATION: Window lost focus (Alt+Tab or clicked outside)');
        });

        window.addEventListener('focus', () => {
            focusDot.className = 'dot';
            focusStatus.textContent = 'Focused';
            addLog('✅ Window regained focus', 'info');
        });

        // 3. MOUSE LEAVE DETECTION
        document.addEventListener('mouseenter', () => {
            mouseDot.className = 'dot';
            mouseStatus.textContent = 'Inside window';
        });

        document.addEventListener('mouseleave', () => {
            mouseDot.className = 'dot warning';
            mouseStatus.textContent = 'Outside window';
            if (monitoringActive) {
                addLog('⚠️ Mouse left browser window', 'warning');
            }
        });

        // 4. ACTIVITY TRACKING (Detect inactivity)
        const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        activityEvents.forEach(event => {
            document.addEventListener(event, () => {
                lastActivity = Date.now();
                activityDot.className = 'dot';
                activityStatus.textContent = 'Active';
            }, { passive: true });
        });

        // Check for inactivity every 2 seconds
        function checkActivity() {
            const inactiveTime = Date.now() - lastActivity;
            const inactiveSeconds = Math.round(inactiveTime / 1000);
            
            if (inactiveTime > 10000) { // 10 seconds of inactivity
                activityDot.className = 'dot warning';
                activityStatus.textContent = `Inactive (${inactiveSeconds}s)`;
                
                if (monitoringActive && inactiveTime > 30000) { // 30 seconds
                    addLog(`⚠️ WARNING: User inactive for ${inactiveSeconds} seconds`, 'warning');
                }
            } else {
                activityDot.className = 'dot';
                activityStatus.textContent = 'Active';
            }
        }

        // 5. BEFOREUNLOAD - Detects closing tab/window
        window.addEventListener('beforeunload', (e) => {
            if (monitoringActive) {
                recordViolation('🚨 VIOLATION: Attempted to close tab/window');
                
                // Show confirmation dialog
                e.preventDefault();
                e.returnValue = '';
                return 'Are you sure you want to leave? Your exam is still in progress.';
            }
        });

        // 6. KEYBOARD SHORTCUTS DETECTION
        document.addEventListener('keydown', (e) => {
            if (!monitoringActive) return;

            // Detect Ctrl+T (new tab), Ctrl+N (new window)
            if ((e.ctrlKey || e.metaKey) && (e.key === 't' || e.key === 'n')) {
                e.preventDefault();
                recordViolation(`🚨 VIOLATION: Attempted to open new ${e.key === 't' ? 'tab' : 'window'} (Ctrl+${e.key.toUpperCase()})`);
                return false;
            }

            // Detect Ctrl+W (close tab)
            if ((e.ctrlKey || e.metaKey) && e.key === 'w') {
                e.preventDefault();
                recordViolation('🚨 VIOLATION: Attempted to close tab (Ctrl+W)');
                return false;
            }

            // Detect Alt+Tab (Windows) or Cmd+Tab (Mac)
            if ((e.altKey || e.metaKey) && e.key === 'Tab') {
                // Can't fully prevent this, but can detect
                addLog('⚠️ Alt/Cmd+Tab detected', 'warning');
            }
        });

        // Update timers
        function updateTimers() {
            const elapsed = Math.round((Date.now() - pageStartTime) / 1000);
            timeOnPage.textContent = elapsed;
            
            let totalAway = totalAwayTime;
            if (currentAwayStart) {
                totalAway += (Date.now() - currentAwayStart);
            }
            awayTime.textContent = Math.round(totalAway / 1000);
        }

        // Start monitoring
        function startMonitoring() {
            monitoringActive = true;
            violations = 0;
            violationCount.textContent = '0';
            pageStartTime = Date.now();
            totalAwayTime = 0;
            currentAwayStart = null;
            
            activityCheckInterval = setInterval(checkActivity, 2000);
            timeUpdateInterval = setInterval(updateTimers, 1000);
            
            addLog('✅ Monitoring started', 'info');
        }

        // Stop monitoring
        function stopMonitoring() {
            monitoringActive = false;
            clearInterval(activityCheckInterval);
            clearInterval(timeUpdateInterval);
            addLog('⏹️ Monitoring stopped', 'info');
        }

        // Clear log
        function clearLog() {
            eventLog.innerHTML = '<div class="log-entry info">[System] Log cleared</div>';
        }

        // Open new tab (for testing)
        function openNewTab() {
            window.open('about:blank', '_blank');
            addLog('🔗 Opened new tab for testing', 'info');
        }

        // Simulate violation
        function simulateViolation() {
            recordViolation('🚨 SIMULATED: Test violation for demonstration');
        }

        // Initialize
        addLog('✅ Detection system ready. Click "Start Monitoring" to begin.', 'info');
        
        // Start activity checking immediately
        setInterval(checkActivity, 2000);
        setInterval(updateTimers, 1000);
    </script>
</body>
</html>
