# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

Devana is a browser-based multiplayer strategy game (PBBG — town building, resource management,
military, alliances). It is a flat, procedural PHP 5/7-style codebase with **no framework, no
build step, no package manager, and no automated tests**. Every `.php` file in the repo root is a
directly-requested web page served by Apache/mysqli — there is no router or MVC layer.

## Running it locally

There is no CLI build/test/lint tooling in this repo. To run the app you need a PHP+MySQL stack
(e.g. Apache + mysqli extension):

1. Create a MySQL database and import the schema: `mysql -u root -p devana < devana.sql`
2. Copy `db-template.php` to `db.php` and fill in real credentials (`db.php` is gitignored —
   never commit real credentials into it or any tracked file).
3. Point the webserver document root at this directory and open `install.php` to create the first
   (owner) account, or `register.php` for a normal account.

There are no `composer.json`, `package.json`, test suite, or linter configured. Verify changes by
exercising the relevant page(s) in a browser against a live DB — there is no automated way to
check correctness.

## Architecture

**One file per page/action, in pairs.** Most game actions follow a `foo.php` (renders an HTML
form/view) + `foo_.php` (processes the POST/GET and redirects) convention — e.g.
`login.php`/`login_.php`, `create.php`/`create_.php`, `a_join.php` has no separate view because
it's invoked directly from a link. Files ending in `_` are handlers, not pages, and always end by
redirecting or calling `msg()`.

**Every page starts the same way:**
```php
<?php include "antet.php"; include "func.php"; ?>
```
- `antet.php` starts the session, loads the active language file into `$lang`, includes `db.php`
  (DB credentials), and defines the page chrome helpers (`logo()`, `menu_up()`, `menu_down()`,
  `about()`) plus the `$system` config array (chat lifetime, tick intervals, etc).
- `func.php` opens the mysqli connection (`$db_id`) and contains essentially the entire
  application/data layer as ~130 top-level functions (game logic, queue processing, battle
  simulation, messaging, alliances, forums, trade...). There are no classes — everything is a
  free function taking/returning arrays or scalars.

**Auth/session model:** `$_SESSION["user"]` is a raw indexed array — the full row returned by
`login()`/`user()` from the `users` MySQL table (see the `users` table in `devana.sql` for the
column order, e.g. index 0=id, 1=name, 4=level/admin rank, 10=faction, 16=lang). There are no
named session properties; every page reads `$_SESSION["user"][N]` positionally. `$_SESSION["user"][4]`
is the privilege level: `>1` hides ads, `>3` shows the admin panel, `==5` is the owner (can change
game config via `config.php`).

**DB access pattern:** Raw `mysqli_query()` calls with string-concatenated SQL scattered through
`func.php` and the page files — there is no query builder or ORM. All user input must be passed
through `clean()` (in `func.php`) before use, which strips tags, escapes HTML, applies
`mysqli_real_escape_string`, and strips a fixed blocklist of characters
(`%20 " ' \ = ; :`). Always sanitize new user input the same way existing code does nearby, and
prefer following existing query patterns exactly rather than introducing new abstractions.

**Game state encoding:** Many DB columns store multiple values packed into one string field,
delimited with `-` or `/`, then `explode()`'d in PHP (e.g. a town's resources, land tiles, and
building levels are each single delimited-string columns). When touching town/building/unit code,
find how the column is `explode()`d elsewhere before changing its shape.

**Queues:** Time-based game actions (building, training, forging, trading, dispatches, upgrades)
are stored in `*_queue` tables (`c_queue`, `t_queue`, `u_queue`, `w_queue`, `uup_queue`, `a_queue`,
`d_queue`) and resolved by `check_*()` functions in `func.php` (`check_c`, `check_t`, `check_u`,
`check_w`, `check_uup`, `check_a`, `check_r`, `check_d`, `check_d_all`), which are called at the
top of the relevant page before rendering so state is always resolved lazily on page load rather
than via a cron/background worker.

**Client side:** `func.js` is a single shared vanilla-JS file (resource-tick countdown timers,
chat polling, small UI helpers) included by pages that need it. No bundler — it's referenced
directly via `<script src="func.js">`.

**Localization:** `language/{de,en,fr,it,nl,ro}.php` each define a `$lang` associative array of UI
strings, selected by `$_SESSION["user"][16]` (or `$_SESSION["lang"]`, default `en.php`) and loaded
by `antet.php`. When adding user-facing strings, add the key to every language file, not just
`en.php`.

**Assets:** `default/` holds the default theme's images/flags (per-faction image sets referenced
via `$imgs`/`$fimgs`, built from `$_SESSION["user"][13]` and the user's faction).

## Conventions to preserve

- New pages should include `antet.php` then `func.php` first, sanitize all `$_GET`/`$_POST` input
  through `clean()`, and check `$_SESSION["user"][0]`/ownership before acting, matching the
  pattern in files like `build.php`, `train.php`, `demolish.php`.
- Match the existing terse procedural style (no classes, minimal whitespace, functions returning
  positional arrays) rather than introducing new patterns/abstractions into this codebase.
