const ExamUtils = {
	// Auto-save functionality
	autoSave: {
		timeout: null,
		delay: 2000, // 2 seconds

		schedule(callback) {
			clearTimeout(this.timeout);
			this.timeout = setTimeout(callback, this.delay);
		},
	},

	// Timer functionality
	timer: {
		interval: null,
		callbacks: [],

		start(initialSeconds) {
			this.stop(); // Clear any existing timer
			let seconds = initialSeconds;

			this.interval = setInterval(() => {
				seconds--;
				this.callbacks.forEach((callback) => callback(seconds));

				if (seconds <= 0) {
					this.stop();
				}
			}, 1000);
		},

		stop() {
			if (this.interval) {
				clearInterval(this.interval);
				this.interval = null;
			}
		},

		addCallback(callback) {
			this.callbacks.push(callback);
		},

		formatTime(seconds) {
			const hours = Math.floor(seconds / 3600);
			const minutes = Math.floor((seconds % 3600) / 60);
			const secs = seconds % 60;

			if (hours > 0) {
				return `${hours.toString().padStart(2, "0")}:${minutes.toString().padStart(2, "0")}:${secs.toString().padStart(2, "0")}`;
			}
			return `${minutes.toString().padStart(2, "0")}:${secs.toString().padStart(2, "0")}`;
		},
	},

	// Progress tracking
	updateProgress(current, total) {
		const percentage = Math.round((current / total) * 100);
		const progressElement = document.getElementById("progress");
		if (progressElement) {
			progressElement.textContent = percentage;
		}
		return percentage;
	},

	// Confirmation dialogs
	confirmSubmit() {
		return confirm(
			"Are you sure you want to submit your exam? You cannot make changes after submission.",
		);
	},

	confirmNavigation() {
		return confirm(
			"You have unsaved changes. Are you sure you want to leave this page?",
		);
	},
};

// Export for global use
window.ExamUtils = ExamUtils;
