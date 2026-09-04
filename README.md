# Praveen Kumar — Portfolio

DevOps Team Leader · 9 years turning manual pipelines into automated, observable platforms.

**[Visit the live site →](https://praveenraghav.com)**
**[Read the blog →](https://blog.praveenraghav.com)**

## About this site

A single-page portfolio themed around DevOps/git concepts — career history rendered
as a `git log`, skills declared as `stack.yaml`, metrics as `git diff --stat`, and a
`ping`-style contact section. No framework, no build step — plain HTML/CSS/JS.

The design and front-end engineering are adapted (MIT licensed) from
[Praveen Kumar's portfolio](https://praveenraghav.com);
all content, copy, and data on this site describe Praveen Kumar.

The nav and footer link out to a technical blog at `blog.praveenraghav.com`, a
self-hosted WordPress install running the matching custom theme in this repo —
see [Blog theme](#blog-theme) below.

## Run it locally

```bash
npx serve .
```

Then open the printed `localhost` URL — opening `index.html` directly via `file://`
also works, but some browsers restrict `localStorage`/clipboard access for local
files, so a couple of niceties (remembering your theme choice, one-click email copy)
won't work until it's served over `http://`.

## Structure

```
index.html          Page markup and content
req/css/styles.css  All styles (theme tokens, layout, motion)
req/js/main.js       Nav, reveals, live clock, hero star-chart, stack↔yaml hover-link
req/js/preloader.js  Boot-sequence loader
req/js/cursor.js     Custom cursor
req/fonts/           Self-hosted Space Grotesk + JetBrains Mono
req/img/fav/         Favicon set
assets/              Résumé PDF
wordpress-theme/     Custom WP theme for the blog subdomain (see below)
```

## Blog theme

`wordpress-theme/praveen-devops-log/` is a classic PHP WordPress theme for the
blog at `blog.praveenraghav.com`, built to match this site's design system —
posts read as a `git log`, a single post as a commit, tags as chips, search as
`grep`. It's a separate WordPress install, not part of this static site's
build. `wordpress-theme/praveen-devops-log.zip` is the ready-to-upload package;
see [wordpress-theme/praveen-devops-log/INSTALL.md](wordpress-theme/praveen-devops-log/INSTALL.md)
for setup, Customizer links, ad placements, and AdSense activation.

## License

MIT — see [LICENSE](LICENSE). Original design/code © Praveen Kumar.
