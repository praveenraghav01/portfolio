# Praveen DevOps Log — install & setup

A classic PHP WordPress theme matching praveenraghav.com's design system,
built for a self-hosted WordPress.org install at a **subdomain**
(e.g. `blog.praveenraghav.com`).

## 1. Install

1. Zip this folder as-is, so the zip's top level contains a single
   `praveen-devops-log/` directory with `style.css` inside it (WordPress
   requires the theme to be nested one folder deep in the zip — don't zip
   the *contents* of the folder directly).
2. In WP admin: **Appearance → Themes → Add New → Upload Theme**, upload
   the zip, then **Activate**.
3. Set your favicon via **Settings → General → Site Icon** (or
   **Appearance → Customize → Site Identity**) — no favicon files are
   bundled in the theme; WordPress's own Site Icon feature handles it.

## 2. Set the essential links (Customizer)

Go to **Appearance → Customize → Praveen DevOps Log — Links** and fill in:

- **Portfolio URL** — `https://praveenraghav.com` (powers the nav "Portfolio ↗" button and the footer link)
- **LinkedIn URL**, **GitHub URL**, **Contact email**
- **Contact page URL** — where "get in touch" style links should point (defaults to the portfolio's `#contact` anchor)

## 3. Menus

Go to **Appearance → Menus** and create/assign:

- A menu for **Primary Menu** — e.g. Log (→ blog home `/`), Topics (→ a
  category or a page listing categories), Portfolio (→ external link to
  your portfolio). If you skip this, the nav falls back to "Log" +
  "Portfolio" automatically.
- Optionally a **Footer Menu** for extra footer links — LinkedIn, GitHub,
  Portfolio, and Email are already hardcoded in the footer from the
  Customizer fields above, so this is only for anything additional.

## 4. Writing posts

- **Reading time** and the cosmetic **commit hash** (`#a1b2c3d`) are
  computed automatically from the post — nothing to fill in.
- **Categories/tags** render as chips and double as `git log --grep`
  filters on the archive pages.
- **Code blocks**: use the core "Code" block (or Markdown code fences, if
  you write in Markdown and paste in). Set the block's language via the
  block's HTML `class="language-xxx"` (the block editor doesn't have a
  language picker natively — see below) so syntax highlighting picks the
  right grammar; if omitted, plain text is fine. Supported out of the box
  via Prism's autoloader: bash, yaml, json, docker, python, php, js/ts,
  and most common languages — it lazy-loads whichever grammar is used.
  - Easiest path: install a small plugin like **"Code Syntax Block"** or
    **"WP-Prism-Syntax-Highlighter"** so the editor gives you a language
    dropdown per code block. The theme's styling (dark terminal chrome,
    copy button, JetBrains Mono) applies automatically to any
    `<pre class="wp-block-code"><code class="language-*">` markup either
    of those produces — no theme changes needed.
- **Table of contents**: appears automatically as a sticky sidebar box
  once a post has 3+ `H2`/`H3` headings. No setup required.
- **Related posts**: automatic, based on shared categories.

## 4a. Activating Google AdSense

**Step 1 — get approved.** Apply at [adsense.google.com](https://adsense.google.com)
with `blog.praveenraghav.com`. Google reviews the site before approving it
(needs enough original content and a privacy policy — add a Page for that
if you don't have one). This can take anywhere from a day to a few weeks;
nothing below works until the account is approved.

**Step 2 — enter your Publisher ID.** Once approved, go to **AdSense →
Account → Account information** and copy your Publisher ID (looks like
`pub-1234567890123456`). Paste it into **Appearance → Customize →
Praveen DevOps Log — Ad Placements → AdSense Publisher ID**. This alone
does two things automatically, before you place a single ad:
- loads Google's script on every page (required for any ad to show)
- serves a valid `/ads.txt` at `blog.praveenraghav.com/ads.txt` — Google
  checks for this and will flag the account/reduce revenue without it,
  and most WP hosting makes uploading a static file there a hassle, so
  the theme serves it virtually instead

Check **AdSense → Sites** — it should show `blog.praveenraghav.com` as
"Ready" within a day of saving the Publisher ID (AdSense polls for the
script + ads.txt to confirm the site is correctly connected).

**Step 3 — choose Auto ads or manual placements (or both):**
- **Auto ads** (easiest): in AdSense, go to **Ads → Overview** and turn
  Auto ads on for the site. Google decides placement and density on its
  own — you don't touch the five slots below at all.
- **Manual placements** (more control, and what the five slots below are
  for): in AdSense, go to **Ads → By ad unit → Display ads**, create a
  unit, and copy the snippet it gives you — just the part starting
  `<ins class="adsbygoogle" ...>` through the closing `</script>` (skip
  the `<script async src=".../adsbygoogle.js?client=...">` line at the
  top of Google's snippet — that part's already handled by the Publisher
  ID field, so including it again is harmless but redundant). Paste that
  into whichever slot below fits.

Google can take a few hours to start actually serving ads on a newly
connected site/unit even once everything above is in place — an empty
space where an ad unit is placed usually just means "still warming up,"
not a misconfiguration.

## 4a-ii. The five placement slots

Independent of AdSense specifically — paste an AdSense ad-unit snippet
(see above), a direct sponsor's HTML, or any other network's code into
one of these, and it appears; leave a slot blank and that placement
simply doesn't render (no empty boxes):

- **Top of post** — right below the title/tags, above the fold
- **Mid-article** — auto-inserted after the middle paragraph on posts with
  6+ paragraphs (no shortcode needed, and it skips short posts so it never
  sits awkwardly close to the top or end)
- **Sidebar rail** — in the sticky column next to the article, alongside
  the table of contents (desktop only, since that column collapses to the
  bottom on mobile)
- **Between posts** — every 4th post in the blog list/category/search results
- **Above footer** — a full-width banner on every page

Each renders inside a "sponsored"-labelled box styled to match the rest of
the site. If you're using something like AdSense that requires raw
`<script>` tags, only a user role with the `unfiltered_html` capability
(Administrator, on a default single-site install) can save script content
in these fields — anyone else's input gets HTML-sanitized on save.

## 4b. If you paste in Markdown instead of using blocks

If a post's text was pasted straight in as Markdown (common when copying
from an AI tool or notes app) instead of using real Gutenberg blocks,
WordPress will store every line as its own plain paragraph and mangle the
leftover syntax — dash bullets become stray en-dashes, and `` ``` `` code
fences get turned into curly-quote artifacts by WordPress's own
typography filter. The theme now includes a rendering safety net
(`inc/markdown-salvage.php`) that automatically repairs this on display:

- runs of `- item` / `* item` / `1. item` paragraphs become real `<ul>`/`<ol>` lists
- mangled `` ``` `` code fences become real, copy-button code blocks
- `![alt](url)` becomes a real image (or a labelled placeholder box if the
  URL is an obvious placeholder like example.com)
- `[text](url)`, `**bold**`, `*em*` become real links/emphasis

**What it can't fix: headings.** Once a Markdown `## Heading` loses its
`##`, there's no reliable way to tell a former heading apart from an
ordinary short sentence, so nothing auto-promotes text to `H2`/`H3` — you
select that line in the block editor and change it to a Heading block by
hand (a few clicks per post). The durable fix is upstream: whatever tool
or pipeline produces these posts should convert Markdown to real
HTML/blocks (or at least keep the `#` markers) before publishing, rather
than pasting flattened text.

## 5. Comments

Comments are on by default per WordPress's usual per-post/global settings
(**Settings → Discussion**). Threaded replies, avatars, and pending-review
states are all styled already.

## 6. DNS / hosting note

This theme assumes the WordPress install lives on its own subdomain
(e.g. `blog.praveenraghav.com`) separate from the static portfolio at
`praveenraghav.com`. Point that subdomain's DNS at wherever you host
WordPress, and add a "Blog" link in the main portfolio's nav
(`index.html`) pointing at it once it's live — that part isn't included
here since it lives in the static site's repo, not this theme.

## What's intentionally not included

- No preloader / animated night-sky hero canvas on blog pages — those are
  homepage-only flourishes on the main site; a blog you reload often stays
  fast without them (by design, see the theme's build notes).
- No sidebar/widget areas — kept single-column and minimal, matching the
  portfolio's aesthetic.
- No screenshot.png — add one later (1200×900) via
  **Appearance → Themes** if you want a custom admin thumbnail; WordPress
  shows a generic placeholder without it, which doesn't affect the site.
