# cinema-booking-system
A web portal for booking cinema/theatre tickets with **PHP, MySQL, HTML, CSS, and JavaScript**.  
This is a school project demonstrating client-side + server-side web development concepts.

---

## 📂 Project Structure
```text
cinema-booking-system/
├─ assets/
│  ├─ styles.css           # external stylesheet (≥4 styles)
│  ├─ app.js               # vanilla JavaScript for client-side validation
│  └─ images/              # posters, banners
├─ includes/
│  ├─ db.php               # PDO database connection
│  ├─ header.php           # shared header + navigation
│  └─ footer.php           # shared footer
├─ pages/
│  ├─ index.php            # Home page
│  ├─ movies.php           # All movies (server-side generated page, SELECT)
│  ├─ movie.php            # Movie details + showtimes table
│  ├─ book.php             # Booking form (≥4 fields, client validation)
│  └─ account.php          # My bookings (SELECT, table display, inline update form)
├─ actions/
│  ├─ process_booking.php  # Form handler (INSERT booking)
│  └─ update_booking.php   # Update booking (UPDATE)
├─ sql/
│  ├─ schema.sql           # database structure (tables)
│  └─ seed.sql             # initial data (sample movies/showtimes)
├─ .gitignore
├─ LICENSE
└─ README.md

---

## How to run with XAMPP
1. Install [XAMPP]
2. Inside your htdocs folder, create a project folder:
File path:

> C:\xampp\htdocs\cinema-booking-system
3. Copy all project files into that folder.
4. Start **Apache** and **MySQL** from the XAMPP Control Panel.
5. Import the database:
- Go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
- Create a new database called `cinema`.
- Import `sql/schema.sql`, then `sql/seed.sql`.
6. Visit the site at (macOS):
> http://localhost/cinema-booking-system/
7. Or (windowOS):
>http://localhost:8000/cinema-booking-system/

---


---

## ✨ Features
- **Home Page** (`index.php`)  
Intro and featured movies.

- **Server-Side Generated Page** (`movies.php`)  
Fetches all movies dynamically with `SELECT`.

- **Movie Details Page** (`movie.php`)  
Shows description + runtime. Includes a **table of showtimes**.

- **Booking Form** (`book.php`)  
4+ fields (Name, Email, Ticket Type, Quantity).  
- Client-side validation (HTML5 + JavaScript).  
- Server-side validation in PHP.

- **Booking Processing** (`process_booking.php`)  
Validates, inserts into DB (`INSERT`), then redirects to Account.

- **Account Page** (`account.php`)  
Displays user’s bookings (`SELECT`) in a table.  
Inline form allows updating quantity.

- **Update Handler** (`update_booking.php`)  
Updates booking quantity (`UPDATE`).

---

## 📊 Database Overview
- **movies**: id, title, description, runtime, poster  
- **showtimes**: id, movie_id, starts_at  
- **bookings**: id, name, email, showtime_id, ticket_type, qty  

Example seed data is in `sql/seed.sql`.

---

## 📝 Requirements Coverage
- ✅ One (1) home page + four (4) content pages  
- ✅ Each page has text + images + proper titles  
- ✅ One (1) table (`movie.php` showtimes; also in `account.php`)  
- ✅ One (1) form with ≥4 fields (`book.php`)  
- ✅ Server-side processing of form (`process_booking.php`)  
- ✅ SQL: `SELECT`, `INSERT`, `UPDATE`  
- ✅ One server-side generated page (`movies.php`)  
- ✅ Client-side validation (HTML5 + JS)  
- ✅ Server-side validation (PHP + DB)  
- ✅ One external CSS stylesheet (`assets/styles.css`)  

---

## 👥 Team Roles
- **Backend (PHP, MySQL)** – booking logic, DB transactions, server-side validation.  
- **Frontend (HTML, CSS, JS)** – layouts, styling, client-side validation, images/content.  

---

## 🚀 Next Steps (Optional Enhancements)
- Admin panel for managing movies/showtimes/prices.
- Seat selection instead of free seating.
- Email confirmations via PHPMailer/SMTP.
- Better UI with responsive CSS (without Bootstrap, per project rules).

---
