# TindaPresyo: Web-Based Price Monitoring and Overpricing Complaint System

Built exactly as described in the capstone paper: **Laravel (PHP) + Blade + HTML/CSS/JavaScript + SQLite**, using the **MVC** pattern.
Everything is plain code you can read in Visual Studio Code. There is no build step and no `npm` needed.

## Group Members
- VINCE ERNEST H. AVENA <br>
- KYLE CHRISTIAN R. RICAMORA 

---------------------------------------------------------------------------

## 1. What you need installed (Windows 10)

| Tool | Version | Notes |
|---|---|---|
| PHP | 8.2 or newer | Needs the `pdo_sqlite`, `sqlite3`, `mbstring`, `fileinfo`, `openssl`, `tokenizer`, `xml`, `ctype` extensions (all are on by default in XAMPP / Laravel Herd) |
| Composer | 2.x | https://getcomposer.org |
| Visual Studio Code | any | Recommended extensions: *PHP Intelephense*, *Laravel Blade Snippets* |

Check them in a terminal: `php -v` and `composer -V`.

## 2. Install (5 minutes)

This folder contains **only the TindaPresyo code** (not Laravel itself). You lay it on top of a fresh Laravel project:

```bash
# 1. Create a fresh Laravel project (downloads Laravel; needs internet)
composer create-project laravel/laravel tindapresyo
cd tindapresyo

# 2. Copy THESE folders from this download into the project, choosing "Replace" when asked:
#       app/   config/   database/   public/   resources/   routes/
#    (In VS Code: open the project folder, then drag the folders in from File Explorer.)

# 3. Build the database and load the demo data
php artisan migrate:fresh --seed

# 4. Start the system
php artisan serve
```

Open **http://127.0.0.1:8000** in a browser.

> Do **not** delete `.env`. Laravel already created it and set SQLite as the database.
> Optional: add `TP_MARKET_NAME="Tuguegarao City Public Market"` (and `TP_VIOLATION_THRESHOLD=3`) to `.env` to rename the market and change the repeat-violation limit.

## 3. Demo accounts (password for all: `password`)

| Role | Email |
|---|---|
| Market Administrator | admin@tindapresyo.test |
| Market Inspector | inspector@tindapresyo.test |
| Vendor (stall M-01) | vendor1@tindapresyo.test (also vendor2 to vendor5; vendor6 has a stall waiting for approval) |
| Shopper | shopper1@tindapresyo.test (also shopper2) |

Stall **V-05** (vendor5) already has 3 confirmed complaints, so it shows as a **flagged repeat violator**.
Delete or change these accounts before real use.

## 4. Feature map: paper to system

| Paper feature | Where in the system |
|---|---|
| Vendor stall registration | `/register` (as vendor) and *My Stalls*; the administrator approves under *Stalls* |
| Daily price posting | *Post Prices* (vendor: own stalls; inspector/administrator: any stall) |
| Price verification by inspectors | *Verify Prices* |
| Public price information for shoppers | *Price Board* and each stall's public page |
| Complaint submission with photo evidence | *Report Overpricing* (shopper) |
| Complaint verification (reported vs. posted price) | Administrator: *Complaints, Review* |
| Resolution tracking | Status flow Pending, Under review, Confirmed, Resolved (or Dismissed) with a full history; shoppers also have the public *Track a Complaint* page |
| Administrator and vendor communication about violations | Messages on a verified complaint |
| Repeat violation flagging | Automatic at 3 confirmed complaints; *Flagged* page |
| Price monitoring and compliance reports | *Reports* (print and CSV download) |
| Fair to sellers *and* shoppers | Vendors: *Trusted stall* badge, posting-consistency score, *market price comparison* on their dashboard, complaints shown only after verification, shopper identity hidden, right to reply. Shoppers: public price board with *Lowest price* tags, *Lowest posted prices today* on their dashboard, price-change arrows on every stall page, complaint tracking with no login needed |
| ISO/IEC 25010 evaluation, IT experts vs. end users | *Evaluate* (all users) and *Evaluation* results (weighted mean, Likert rating, Welch t-test) |

## 5. Folder guide

```
app/Models/               the Model layer: data + rules (Complaint, Stall, Price, ...)
app/Http/Controllers/     the Controller layer: receive a request, call the Models, pick a View
app/Http/Middleware/      EnsureRole = role-based access control
app/Support/Stats.php     weighted mean, Welch t-test, Likert rating
config/tindapresyo.php    market name, flag limit, sections, units, Likert scale
config/iso25010.php       the evaluation statements
database/migrations/      the database tables
database/seeders/         demo data
resources/views/          the View layer (Blade templates)
routes/web.php            every web address and who may open it
public/css, public/js     stylesheet and JavaScript
```

## 6. Common problems

* **"could not find driver"**: enable `pdo_sqlite` and `sqlite3` in `php.ini`.
* **Photos do not show**: they are stored privately and served by the controller; make sure you ran `php artisan migrate:fresh --seed` at least once and that `storage/app` is writable.
* **Changed a file but nothing changes**: run `php artisan view:clear` and `php artisan config:clear`.
* **Start again with clean demo data**: `php artisan migrate:fresh --seed`.
