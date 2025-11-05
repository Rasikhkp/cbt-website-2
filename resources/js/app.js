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

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
    createIcons({ icons });
    addTinyMCE()
})
