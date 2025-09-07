# 🎨 Frontend Code Rules for Laravel + Tailwind + jQuery

## 1. **Folder & File Structure**

Keep FE assets structured so JS/CSS don’t get cluttered.

```
resources/
  views/              # Blade templates
    layouts/          # Master layouts
    components/       # Small reusable UI pieces
    pages/            # Page-level views (grouped by feature)
  js/
    pages/            # Page-specific JS (match Blade page)
    components/       # Small reusable scripts (e.g., modal.js, table.js)
    utils/            # Helpers (ajax.js, form.js, timer.js)
  css/
    custom.css        # Extra Tailwind overrides if needed
```

* **One JS file per page** (`exam-create.js`, `exam-take.js`).
* Shared scripts go into **components** or **utils**.
* Load scripts **only when needed** (don’t dump everything in `app.js`).

---

## 2. **Blade Conventions**

* Use **layouts** for consistent structure (`layouts/app.blade.php`).
* Extract repeatable UI into **components** (`resources/views/components/`).
* Keep Blade files **thin** → only HTML + Tailwind classes + minimal `@if/@foreach`.
* Never put long `<script>` tags directly in Blade → always move to JS file.

---

## 3. **JavaScript Rules**

* Use **namespaced modules** instead of spaghetti jQuery. Example:

```js
// resources/js/pages/exam-take.js
const ExamTake = {
  init() {
    this.bindEvents();
    this.startTimer();
  },

  bindEvents() {
    $(".next-btn").on("click", this.nextQuestion);
    $(".answer-input").on("change", this.saveAnswer);
  },

  nextQuestion() {
    // handle next question
  },

  saveAnswer() {
    // ajax save answer
  },

  startTimer() {
    // countdown timer
  }
};

$(document).ready(() => ExamTake.init());
```

👉 This keeps logic **grouped by feature** instead of random jQuery everywhere.

---

## 4. **Styling Rules (Tailwind)**

* Use **utility-first** → don’t create too many custom CSS classes.
* For repeatable patterns (like buttons), create **Blade components** (`<x-button>`).
* Use **Tailwind typography plugin** for readable exam text.
* Avoid inline styles → always Tailwind classes.

---

## 5. **AJAX & API Calls**

* Create a **utils/ajax.js** wrapper:

```js
// resources/js/utils/ajax.js
const Ajax = {
  post(url, data) {
    return $.ajax({
      url,
      type: "POST",
      data,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
      }
    });
  },
  get(url) {
    return $.ajax({ url, type: "GET" });
  }
};
```

👉 Always use this wrapper → avoids repeating CSRF token handling & error logging.

---

## 6. **Data Table Recommendation**

For MVP:

* **Use DataTables (jQuery plugin)** → proven, stable, works well with Laravel.

  * Features: search, pagination, sorting.
  * You can later replace with Alpine/Livewire if you want, but for now it’s simple.

Integration idea:

```js
$('#userTable').DataTable({
  responsive: true,
  pageLength: 10,
});
```

---

## 7. **Coding Standards**

* **One function = one responsibility** (don’t write 200 lines in one handler).
* **Comment only for non-obvious logic** (don’t over-comment).
* **Name consistently**:

  * `exam-create.js`, `exam-take.js`.
  * Functions → verbs (`saveAnswer()`, `startTimer()`).
* **Linting**: use ESLint (standard config) to keep formatting consistent.

---

## 8. **Deployment Readiness**

* Compile assets via **Laravel Mix or Vite** → minify CSS/JS for production.
* Version your assets (cache-busting).
* No inline `<script>` → always bundled.
