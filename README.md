# Dia Hafed — architecture portfolio

A Laravel 12 / PHP 8.2+ architecture portfolio with a database-backed admin dashboard. This starter is personalized for Dia Hafed and uses original architectural illustration placeholders until Dia's CV, project details, and photographs are supplied. Placeholder projects are not claims of completed work.

## Start locally

```sh
composer install
npm ci
npm run build
# For a fresh checkout, copy .env.example to .env and configure the database.
php artisan key:generate # only for a fresh environment without an application key
php artisan migrate --seed
php artisan serve
```

In local development, the seeded developer account can sign in directly at `/admin/login` with **admin@example.test** / **password**. No first-login password reset is required. Set `ADMIN_EMAIL` and `ADMIN_PASSWORD` in `.env` to use different credentials; production must configure both before seeding. The `php artisan portfolio:admin {email}` command can also create an administrator and prompts for its password.

- Portfolio: `/`
- Project detail pages: `/projects/{id}`
- Admin sign-in: `/admin/login`
- Dashboard: `/admin`
- Analytics: `/admin/analytics`

The current workspace uses the MySQL database configured in `.env`. Tests use an isolated in-memory SQLite database.

## Editing the website

Open **Website content** in the dashboard. Each section has its own form for text, display order, and visibility. Lower order numbers appear first. Changes are public immediately after saving.

- **Site settings → Website colors:** color pickers and hex inputs for the accent, backgrounds, text, buttons, borders and focus outline. Includes a live palette preview, contrast feedback, and an original-palette reset. Save changes applies the palette to the homepage and project pages. Existing sites use the original colors until edited; no migration or reseeding is needed.
- **Site settings:** name, professional title, SEO description, footer, and CV/portfolio PDF downloads.
- **Hero:** banner heading, introduction, image, and both call-to-action links.
- **Projects:** add, edit, hide, reorder or delete projects; manage category, year, location, descriptions, cover images, galleries, and optional GLB 3D maquettes. Project images open in an interactive fullscreen viewer with zoom, keyboard arrows, and swipe navigation. Actual orbitable 3D maquettes require a GLB model upload per project. Editorial projects display their uploaded images in the offline page-turning reader; category filters derive from visible projects. The initial six architecture cards are explicitly labeled speculative sample concepts for preview only, not claims about Dia's work.
- **About:** biography, portrait and image description.
- **Services, experience, education, skills:** edit section headings and manage individual entries.
- **Contact:** email, phone, location, social links, form heading and response text.
- **Message inbox:** read and delete enquiries; Reply opens your email application. Messages are saved in the database; automatic email delivery is not configured.
- **Analytics:** review unique browsing sessions, page views, pages per visitor, bounce rate, active visitors, inquiries, daily trends, popular pages, referrers, devices, and browsers over 7, 30, or 90 days. Tracking covers homepage and project-detail views; administrators and obvious bots are excluded. Session IDs are one-way hashed, IP addresses and query strings are not stored, and referrers are reduced to host names.
- **Your account:** update administrator name, email and password, with current-password verification.

Upload JPG, PNG or WebP images (10 MB each), PDF documents (30 MB), and GLB 3D models (50 MB). Uploaded files use generated names under `storage/app/private/uploads` and are served through a constrained `/media/uploads/{filename}` route. Uploaded portfolio media is public. Replacing a file or deleting a project keeps the old physical file so shared references do not break. Remove unused uploads manually only after checking their references.

For galleries, upload multiple images, reorder the displayed path lines, or remove a line to remove an image from the project. No JSON editing is required.

## Seed data and portfolio materials

`database/seeders/PortfolioSeeder.php` reads `database/seeders/data/portfolio.json`. It creates missing sections and their initial entries in a transaction, and does not overwrite dashboard edits or resurrect deleted items on subsequent runs.

The portfolio profile is based on the supplied `Dia Hafed CV.pdf`: biography, education, two three-month internships, software, languages, achievements, and contact details are transcribed from it. Internship dates and language proficiency levels were not specified in the CV and are not inferred. A `/cv` download route serves the supplied PDF. The six project cards and original illustrations remain clearly labeled speculative samples for previewing the layout and categories; replace them with verified project details, images, and GLB models supplied by Dia. No client work or built projects have been invented.

## Verification

```sh
php artisan test
php artisan view:cache
```

Feature tests cover seeded pages, repeatable seeding, authorization, direct administrator sign-in and sign-out, analytics tracking and aggregation, admin forms, section ordering and visibility, project CRUD, hidden project access, image/gallery uploads, unsafe URLs and files, enquiries, and account password changes.

## Deployment

Deploy on a PHP/Laravel host with a database and the web root set to `public/`. Preserve `.env`, database contents and `storage/app/private/uploads` between releases. Set `APP_ENV=production`, `APP_DEBUG=false`, the correct HTTPS `APP_URL`, and `SESSION_SECURE_COOKIE=true`. Configure PHP `upload_max_filesize` to at least `30M` and `post_max_size` sufficiently above the combined gallery upload size (for example `128M`). Back up the database and uploaded files together. Run migrations and cache configuration/views during deployment.

This implementation has not been deployed to external hosting. The source machine's `.env` and Git history are not included in this copy; create a local `.env` from `.env.example` before running the application.
