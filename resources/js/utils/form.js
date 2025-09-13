// Form utility functions
const FormUtils = {
	// Auto-save functionality for long forms
	setupAutoSave(formSelector, saveUrl, interval = 30000) {
		const form = document.querySelector(formSelector);
		if (!form) return;

		setInterval(() => {
			const formData = new FormData(form);
			// Auto-save implementation (for future use)
		}, interval);
	},

	// Image preview functionality
	setupImagePreview(inputSelector, previewSelector) {
		const input = document.querySelector(inputSelector);
		const preview = document.querySelector(previewSelector);

		if (!input || !preview) return;

		input.addEventListener("change", function (e) {
			const file = e.target.files[0];
			if (file) {
				const reader = new FileReader();
				reader.onload = function (e) {
					preview.src = e.target.result;
					preview.style.display = "block";
				};
				reader.readAsDataURL(file);
			}
		});
	},
};

// Export for use in other files
window.FormUtils = FormUtils;
