import "./bootstrap";
import Alpine from "alpinejs";
import tinymce from 'tinymce';
import wirisPlugin from '@wiris/mathtype-tinymce7/plugin.min.js?url';

import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/models/dom/model';
import 'tinymce/skins/ui/oxide/skin.css';
import 'tinymce/plugins/link';
import 'tinymce/plugins/table';
import 'tinymce/plugins/charmap';

window.Alpine = Alpine;
Alpine.start();

tinymce.init({
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
    content_style: "@import url('https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap'); body { font-family: Figtree; }"
});
