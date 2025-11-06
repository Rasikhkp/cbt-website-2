import "./bootstrap";
import Alpine from "alpinejs";
import tinymce from 'tinymce';
import wirisPlugin from '@wiris/mathtype-tinymce7/plugin.min.js?url';
import $ from 'jquery';
import jQuery from 'jquery';
import Chart from 'chart.js/auto';
import { createIcons, icons } from 'lucide';

import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/models/dom/model';
import 'tinymce/skins/ui/oxide/skin.css';
import 'tinymce/plugins/link';
import 'tinymce/plugins/table';
import 'tinymce/plugins/charmap';


window.$ = $;
window.jQuery = jQuery;
window.Chart = Chart
window.Alpine = Alpine;
window.tinymce = tinymce
window.addTinyMCE = () => {
    window.tinymce.init({
        selector: '.tinymce-field',
        license_key: 'gpl', // gpl for open source, T8LK:... for commercial
        skin: false, // use imported skin.css
        content_css: false,
        plugins: "link table charmap",
        toolbar: "undo redo | bold italic underline strikethrough | link table | align lineheight | numlist bullist indent | charmap tiny_mce_wiris_formulaEditor | removeformat",
        menubar: false,
        external_plugins: {
            tiny_mce_wiris: wirisPlugin,
        },
        draggable_modal: true,
        content_style: "@import url('/fonts/figtree.css'); body { font-family: Figtree; }",
    })
}
window.fileSizeFormat = (bytes) => {
  const units = ['B', 'KB', 'MB', 'GB', 'TB'];
  let i = 0;

  while (bytes >= 1024 && i < units.length - 1) {
    bytes /= 1024;
    i++;
  }

  return `${bytes.toFixed(2)} ${units[i]}`;
}
window.customConfirm = (message, title = "Are you absolutely sure?") => {
    return new Promise((resolve) => {
        const modal = document.getElementById("customConfirmModal");
        const card = document.getElementById("customConfirmCard");
        const titleEl = document.getElementById("customConfirmTitle");
        const messageEl = document.getElementById("customConfirmMessage");
        const okBtn = document.getElementById("customConfirmOkBtn");
        const cancelBtn = document.getElementById("customConfirmCancelBtn");

        titleEl.textContent = title;
        messageEl.textContent = message;

        // Show modal and play "enter" animation
        modal.classList.remove("hidden");
        requestAnimationFrame(() => {
          card.classList.remove("modal-enter");
          card.classList.add("modal-enter-active");
        });

        const cleanup = () => {
          // Play leave animation
          card.classList.remove("modal-enter-active");
          card.classList.add("modal-leave-active");
          setTimeout(() => {
            modal.classList.add("hidden");
            card.classList.remove("modal-leave-active");
            card.classList.add("modal-enter");
          }, 120);
          okBtn.removeEventListener("click", handleOk);
          cancelBtn.removeEventListener("click", handleCancel);
        };

        const handleOk = () => {
          cleanup();
          resolve(true);
        };

        const handleCancel = () => {
          cleanup();
          resolve(false);
        };

        okBtn.addEventListener("click", handleOk);
        cancelBtn.addEventListener("click", handleCancel);
    });
}
window.showToast = ({
    title = "Heads up!",
    message = "Something happened.",
    type = "info", // 'success' | 'error' | 'warning' | 'info'
    duration = 4000,
} = {}) => {
    const container = document.getElementById("toastContainer");

    const colors = {
        success: "border-green-400 bg-green-50 text-green-800",
        error: "border-red-400 bg-red-50 text-red-800",
        warning: "border-yellow-400 bg-yellow-50 text-yellow-800",
        info: "border-blue-400 bg-blue-50 text-blue-800",
    };

    const icons = {
        success: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>`,
        error: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>`,
        warning: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.22 19h13.56a1 1 0 00.9-1.45L13.9 4.55a1 1 0 00-1.8 0L4.32 17.55A1 1 0 005.22 19z" /></svg>`,
        info: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20 10 10 0 010-20z" /></svg>`,
    };

    const toast = document.createElement("div");
    toast.className = `
        toast-enter
        border rounded-lg px-4 py-3 shadow-sm flex items-start gap-3
        ${colors[type]}
    `;
    toast.innerHTML = `
        ${icons[type]}
        <div>
          <p class="font-semibold">${title}</p>
          <p class="text-sm">${message}</p>
        </div>
    `;

    container.appendChild(toast);

    // Trigger enter animation
    requestAnimationFrame(() => {
        toast.classList.remove("toast-enter");
        toast.classList.add("toast-enter-active");
    });

    setTimeout(() => {
        toast.classList.remove("toast-enter-active");
        toast.classList.add("toast-leave-active");
        setTimeout(() => toast.remove(), 250);
    }, duration);
}

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
    createIcons({ icons });
    addTinyMCE()
})

document.querySelectorAll("form[data-confirm]").forEach((form) => {
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const message = form.dataset.confirm;
        const confirmed = await customConfirm(message);
        if (confirmed) form.submit();
    });
});
