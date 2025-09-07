## 📌 Phase Plan (Incremental & Testable)

### **Phase 1 – Core Auth & Roles**

🎯 Goal: Users can log in with correct role.

* Backend:

  * Laravel auth (login, logout, role-based access).
  * Roles: Admin, Teacher, Student.
* Frontend:

  * Simple login/register forms (styled with Tailwind).
  * Role-based navigation (different menu items).
* Test: Log in as each role, check access control.

---

### **Phase 2 – Admin User Management**

🎯 Goal: Admin can add/manage users.

* Backend:

  * CRUD for Users (name, email, password, role).
* Frontend:

  * Admin menu → “Users” list.
  * Add/Edit/Delete users (basic forms).
* Test: Admin creates a teacher & student, then log in with them.

---

### **Phase 3 – Question Bank (Shared)**

🎯 Goal: Teachers can create questions in a global bank.

* Backend:

  * Questions table (with type: MCQ, Short, Long).
  * Options table for MCQ answers.
* Frontend:

  * Teacher menu → “Question Bank.”
  * Add questions (form depends on type).
  * List & reuse questions.
* Test: Teacher adds MCQ, short, long questions → check list.

---

### **Phase 4 – Exam Creation & Assignment**

🎯 Goal: Teachers can make exams.

* Backend:

  * Exams table (title, start\_time, end\_time, duration, randomization, etc.).
  * Exam ↔ Questions relation.
* Frontend:

  * Teacher menu → “Exams.”
  * Create exam (choose fixed vs random, select questions from bank).
* Test: Teacher creates exam → exam visible in list.

---

### **Phase 5 – Exam Taking (Basic Flow)**

🎯 Goal: Students can take exams (no autosave yet).

* Backend:

  * Attempts table (links student → exam).
  * Answers table.
* Frontend:

  * Student menu → “Available Exams.”
  * Start attempt → 1 question per page, Next/Prev, submit at end.
* Test: Student takes exam, submit → answers saved.

---

### **Phase 6 – Autosave & Resume**

🎯 Goal: Make exam reliable.

* Backend:

  * Store partial answers + remaining time.
* Frontend:

  * jQuery autosave (on answer change or every few seconds).
  * Resume attempt if disconnected (load saved state + timer).
* Test: Student disconnects mid-exam → log back in → resume with answers intact.

---

### **Phase 7 – Grading & Result Release**

🎯 Goal: Teachers can grade & release results.

* Backend:

  * Auto-grade MCQ & exact short text.
  * Manual grading for long text.
  * “Release” flag to publish score.
* Frontend:

  * Teacher view: grade attempts → mark as released.
  * Student view: after release → see final score.
* Test: Teacher grades exam → release → student sees score.

---

### **Phase 8 – Polishing MVP**

🎯 Goal: Make it smooth but still simple.

* Styling cleanup with Tailwind.
* Question navigation panel (jump to question number).
* Randomization (question order & selection).
* Minor fixes (input validation, error messages).

---

👉 Each phase is **usable** by itself, so you can build, deploy locally, and test it before moving forward.
