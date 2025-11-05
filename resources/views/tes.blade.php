<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Exam System</title>
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
            user-select: none;
            -webkit-user-select: none;
        }

        /* JavaScript Disabled Warning - Always hidden unless JS is off */
        #jsDisabledWarning {
            display: none;
        }
        
        #container {
            width: 100vw;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }
        
        .header {
            width: 100%;
            max-width: 1200px;
            background: rgba(255,255,255,0.15);
            padding: 20px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(10px);
        }

        .logo {
            font-size: 1.5em;
            font-weight: bold;
        }

        .student-info {
            text-align: right;
            font-size: 0.9em;
        }
        
        .exam-card {
            width: 100%;
            max-width: 900px;
            background: white;
            color: #333;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 2.2em;
        }

        .exam-info {
            color: #666;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .warning-banner {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            color: #856404;
        }

        .danger-banner {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            color: #721c24;
        }

        .rules-box {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .rules-box h3 {
            color: #495057;
            margin-bottom: 15px;
        }

        .rules-box ul {
            margin-left: 20px;
            line-height: 2;
            color: #495057;
        }

        .rules-box li {
            margin-bottom: 8px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 25px 0;
            padding: 15px;
            background: #e7f3ff;
            border-radius: 5px;
        }

        .checkbox-container input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .checkbox-container label {
            font-size: 1em;
            color: #004085;
            cursor: pointer;
        }

        button {
            padding: 15px 40px;
            font-size: 1.1em;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        button:hover:not(:disabled) {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .status-bar {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 0.9em;
            color: #495057;
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #28a745;
        }

        .status-dot.inactive {
            background: #dc3545;
        }

        .violation-counter {
            font-weight: bold;
            color: #dc3545;
        }

        #activityLog {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            max-height: 150px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.85em;
            margin-top: 20px;
            color: #495057;
        }

        .log-entry {
            padding: 3px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .log-entry.warning {
            color: #ff6600;
            font-weight: bold;
        }

        .log-entry.error {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- This will ONLY show if JavaScript is disabled -->
    <noscript>
        <div id="jsDisabledWarning" style="display: block; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; z-index: 999999; color: #fff;">
            <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; text-align: center; padding: 40px;">
                <div style="max-width: 800px; background: #1a1a1a; padding: 50px; border-radius: 10px; border: 3px solid #dc3545;">
                    <h1 style="font-size: 3.5em; margin-bottom: 30px; color: #dc3545;">⚠️ SECURITY VIOLATION</h1>
                    
                    <div style="background: #dc3545; color: white; padding: 20px; border-radius: 5px; margin: 30px 0; font-size: 1.3em;">
                        <strong>JavaScript Has Been Disabled</strong>
                    </div>
                    
                    <p style="font-size: 1.5em; margin-bottom: 25px; line-height: 1.6;">
                        This is a serious violation of exam protocols.
                    </p>
                    
                    <div style="background: #2d2d2d; padding: 25px; border-radius: 5px; margin: 30px 0; text-align: left;">
                        <p style="font-size: 1.2em; margin-bottom: 15px;"><strong style="color: #ffc107;">This incident has been logged:</strong></p>
                        <ul style="list-style: none; padding: 0; font-size: 1.1em; line-height: 2;">
                            <li>✓ Your student ID</li>
                            <li>✓ Computer station number</li>
                            <li>✓ Timestamp of violation</li>
                            <li>✓ IP address and device information</li>
                        </ul>
                    </div>
                    
                    <div style="background: #fff3cd; color: #856404; padding: 25px; border-radius: 5px; margin: 30px 0;">
                        <p style="font-size: 1.2em; font-weight: bold; margin-bottom: 10px;">Consequences:</p>
                        <ul style="list-style: none; padding: 0; font-size: 1.1em; line-height: 1.8; text-align: left;">
                            <li>• Exam supervisor has been notified</li>
                            <li>• This may result in exam disqualification</li>
                            <li>• Academic misconduct report may be filed</li>
                            <li>• Your exam session may be terminated</li>
                        </ul>
                    </div>
                    
                    <p style="font-size: 1.3em; margin-top: 30px; margin-bottom: 20px; color: #28a745;">
                        <strong>To continue:</strong>
                    </p>
                    <ol style="font-size: 1.2em; text-align: left; line-height: 2; margin: 0 auto; max-width: 500px;">
                        <li>Raise your hand and notify the exam supervisor</li>
                        <li>Enable JavaScript in your browser settings</li>
                        <li>Refresh this page</li>
                        <li>Wait for supervisor approval to continue</li>
                    </ol>
                    
                    <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #444; font-size: 0.9em; color: #999;">
                        <p>University Exam System v2.0</p>
                        <p>Contact: examhelp@university.edu</p>
                    </div>
                </div>
                
                <!-- Tracking pixel to log the violation server-side -->
                <img src="/api/log-violation?type=js_disabled&student_id=STUDENT_ID_HERE&timestamp=" style="display: none;" onerror="this.style.display='none'">
                
                <!-- Alternative: Auto-submit form to log violation -->
                <iframe name="logFrame" style="display:none;"></iframe>
                <form id="violationForm" action="/api/log-violation" method="POST" target="logFrame" style="display:none;">
                    <input type="hidden" name="violation_type" value="javascript_disabled">
                    <input type="hidden" name="student_id" value="STUDENT_ID_HERE">
                    <input type="hidden" name="timestamp" value="">
                </form>
                <script type="text/javascript">
                    // This won't run if JS is disabled, but if they re-enable it:
                    document.getElementById('violationForm').timestamp.value = new Date().toISOString();
                    document.getElementById('violationForm').submit();
                </script>
            </div>
        </div>
    </noscript>

    <!-- Main exam interface - only visible when JS is enabled -->
    <div id="container">
        <div class="header">
            <div class="logo">🎓 University Exam System</div>
            <div class="student-info">
                <div><strong>Student ID:</strong> <span id="studentId">202312345</span></div>
                <div><strong>Station:</strong> <span id="station">LAB-A-15</span></div>
            </div>
        </div>

        <div class="exam-card">
            <h1>Computer Science 301 - Final Exam</h1>
            <div class="exam-info">
                <strong>Duration:</strong> 120 minutes | <strong>Questions:</strong> 50 | <strong>Total Points:</strong> 100
            </div>

            <div class="danger-banner">
                <strong>⚠️ SECURITY MONITORING ACTIVE</strong>
                <p style="margin-top: 10px;">This exam is monitored for academic integrity. Any attempt to disable security features, exit fullscreen, or access unauthorized resources will be logged and may result in exam disqualification.</p>
            </div>

            <div class="rules-box">
                <h3>📋 Exam Rules & Regulations</h3>
                <ul>
                    <li>You must remain in fullscreen mode for the entire exam duration</li>
                    <li>Switching tabs, windows, or applications is <strong>strictly prohibited</strong></li>
                    <li>Copy, paste, and right-click functions are disabled</li>
                    <li>All violations are logged with timestamps and reviewed by faculty</li>
                    <li>Multiple violations will result in automatic exam termination</li>
                    <li>Any attempt to disable JavaScript or security features will be reported</li>
                </ul>
            </div>

            <div class="warning-banner">
                <strong>🔒 Security Features:</strong> Fullscreen Lock • Keyboard Monitoring • Tab Switching Detection • Activity Logging • JavaScript Requirement • Server-Side Tracking
            </div>

            <div class="checkbox-container">
                <input type="checkbox" id="agreeCheckbox">
                <label for="agreeCheckbox">
                    <strong>I have read and agree to follow all exam rules. I understand that violations will be reported to the academic integrity office.</strong>
                </label>
            </div>

            <button id="startExamBtn" disabled>🚀 Start Exam (Fullscreen Mode)</button>

            <div class="status-bar">
                <div class="status-item">
                    <div class="status-dot" id="jsStatus"></div>
                    <span>JavaScript: Enabled</span>
                </div>
                <div class="status-item">
                    <div class="status-dot inactive" id="fullscreenStatus"></div>
                    <span id="fullscreenText">Fullscreen: Inactive</span>
                </div>
                <div class="status-item">
                    <span>Violations: <span class="violation-counter" id="violationCount">0</span></span>
                </div>
            </div>

            <div id="activityLog">
                <strong>Activity Monitor:</strong>
                <div class="log-entry">[System] Security monitoring initialized</div>
                <div class="log-entry">[System] JavaScript status: ENABLED</div>
            </div>
        </div>
    </div>

    <script>
        // Configuration
        const STUDENT_ID = '202312345'; // This would come from server
        const STATION = 'LAB-A-15';
        const MAX_VIOLATIONS = 5;
        
        let examStarted = false;
        let violations = 0;
        let sessionStartTime = null;

        // DOM elements
        const agreeCheckbox = document.getElementById('agreeCheckbox');
        const startExamBtn = document.getElementById('startExamBtn');
        const activityLog = document.getElementById('activityLog');
        const fullscreenStatus = document.getElementById('fullscreenStatus');
        const fullscreenText = document.getElementById('fullscreenText');
        const violationCount = document.getElementById('violationCount');

        // Enable start button when checkbox is checked
        agreeCheckbox.addEventListener('change', () => {
            startExamBtn.disabled = !agreeCheckbox.checked;
        });

        // Logging function
        function addLog(message, type = 'info') {
            const entry = document.createElement('div');
            entry.className = `log-entry ${type}`;
            const timestamp = new Date().toLocaleTimeString();
            entry.textContent = `[${timestamp}] ${message}`;
            activityLog.appendChild(entry);
            activityLog.scrollTop = activityLog.scrollHeight;

            // Send to server
            logToServer(message, type);
        }

        // Send violations to server
        function logToServer(message, type) {
            // In production, replace with your actual API endpoint
            const data = {
                student_id: STUDENT_ID,
                station: STATION,
                message: message,
                type: type,
                timestamp: new Date().toISOString(),
                violations: violations
            };

            // Using fetch API
            fetch('/api/log-activity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            }).catch(err => {
                console.error('Failed to log to server:', err);
            });

            console.log('Logging to server:', data);
        }

        // Handle violations
        function recordViolation(message) {
            violations++;
            violationCount.textContent = violations;
            addLog(message, 'error');

            if (violations >= MAX_VIOLATIONS) {
                addLog('CRITICAL: Maximum violations reached. Exam terminated.', 'error');
                alert('⚠️ EXAM TERMINATED\n\nYou have exceeded the maximum number of allowed violations.\n\nYour exam supervisor has been notified.\n\nPlease remain seated and wait for instructions.');
                
                // In production, you would:
                // 1. Lock the exam interface
                // 2. Submit current answers
                // 3. Notify supervisor via real-time notification
                examStarted = false;
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // Fullscreen functions
        function enterFullscreen() {
            const elem = document.documentElement;
            if (elem.requestFullscreen) {
                elem.requestFullscreen().catch(err => {
                    addLog('Failed to enter fullscreen', 'error');
                    alert('Unable to enter fullscreen mode. Please try again or contact the exam supervisor.');
                });
            }
        }

        // Monitor fullscreen
        document.addEventListener('fullscreenchange', () => {
            if (document.fullscreenElement) {
                fullscreenStatus.className = 'status-dot';
                fullscreenText.textContent = 'Fullscreen: Active';
                addLog('Entered fullscreen mode', 'info');
            } else {
                fullscreenStatus.className = 'status-dot inactive';
                fullscreenText.textContent = 'Fullscreen: Inactive';
                
                if (examStarted) {
                    recordViolation('VIOLATION: Exited fullscreen during exam');
                    
                    // Show re-entry prompt
                    const reenter = confirm('⚠️ SECURITY VIOLATION\n\nYou have exited fullscreen mode.\n\nThis violation has been logged.\n\nClick OK to return to fullscreen and continue the exam.');
                    
                    if (reenter) {
                        enterFullscreen();
                    } else {
                        recordViolation('VIOLATION: Refused to re-enter fullscreen');
                    }
                }
            }
        });

        // Start exam
        startExamBtn.addEventListener('click', () => {
            examStarted = true;
            sessionStartTime = new Date();
            violations = 0;
            addLog('Exam session started', 'info');
            enterFullscreen();
            
            // In production, this would load the actual exam questions
            setTimeout(() => {
                alert('✅ Exam Started\n\nYou are now in secure exam mode.\n\nRemember:\n- Stay in fullscreen\n- Do not switch tabs\n- All actions are monitored\n\nGood luck!');
            }, 1000);
        });

        // Monitor tab visibility
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && examStarted) {
                recordViolation('VIOLATION: Tab switched or window hidden');
            }
        });

        // Monitor window blur (switched to another app)
        window.addEventListener('blur', () => {
            if (examStarted) {
                recordViolation('VIOLATION: Window lost focus (switched applications)');
            }
        });

        // Block keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (!examStarted) return;

            // Block Ctrl+C, Ctrl+V, Ctrl+X, Ctrl+A
            if (e.ctrlKey && ['c', 'v', 'x', 'a'].includes(e.key.toLowerCase())) {
                e.preventDefault();
                recordViolation(`VIOLATION: Attempted ${e.ctrlKey ? 'Ctrl' : 'Cmd'}+${e.key.toUpperCase()}`);
                return false;
            }

            // Block Cmd shortcuts (Mac)
            if (e.metaKey && ['c', 'v', 'x', 'a'].includes(e.key.toLowerCase())) {
                e.preventDefault();
                recordViolation(`VIOLATION: Attempted Cmd+${e.key.toUpperCase()}`);
                return false;
            }

            // Block F12, Ctrl+Shift+I/J/C (DevTools)
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && ['i', 'j', 'c'].includes(e.key.toLowerCase()))) {
                e.preventDefault();
                recordViolation('VIOLATION: Attempted to open developer tools');
                return false;
            }

            // Block PrintScreen
            if (e.key === 'PrintScreen') {
                e.preventDefault();
                recordViolation('VIOLATION: Attempted screenshot');
                return false;
            }

            // Block Ctrl+P (Print)
            if (e.ctrlKey && e.key.toLowerCase() === 'p') {
                e.preventDefault();
                recordViolation('VIOLATION: Attempted to print');
                return false;
            }
        });

        // Block right-click
        document.addEventListener('contextmenu', (e) => {
            if (!examStarted) return;
            e.preventDefault();
            recordViolation('VIOLATION: Right-click attempted');
            return false;
        });

        // Block copy/paste events
        document.addEventListener('copy', (e) => {
            if (!examStarted) return;
            e.preventDefault();
            recordViolation('VIOLATION: Copy attempted');
        });

        document.addEventListener('paste', (e) => {
            if (!examStarted) return;
            e.preventDefault();
            recordViolation('VIOLATION: Paste attempted');
        });

        // Disable text selection during exam
        document.addEventListener('selectstart', (e) => {
            if (examStarted) {
                e.preventDefault();
                return false;
            }
        });

        // Periodic JavaScript check (in case they disable it mid-exam)
        setInterval(() => {
            if (examStarted) {
                // This runs every second - if JS is disabled, it stops
                // Server should detect missing heartbeats
                logToServer('Heartbeat', 'heartbeat');
            }
        }, 5000);

        // Log page load
        addLog('Page loaded successfully', 'info');
        addLog('Waiting for student to start exam...', 'info');
    </script>
</body>
</html>
