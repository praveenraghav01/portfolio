# Praveen Kumar — Portfolio

DevOps Team Leader · 9 years turning manual pipelines into automated, observable platforms.

[![Portfolio](https://img.shields.io/website?url=https%3A%2F%2Fpraveenraghav.com&label=portfolio&up_color=3ecf8e&down_color=inactive)](https://praveenraghav.com)
[![Blog](https://img.shields.io/website?url=https%3A%2F%2Fblog.praveenraghav.com&label=blog&up_color=3ecf8e&down_color=inactive)](https://blog.praveenraghav.com)
[![License: MIT](https://img.shields.io/badge/license-MIT-3ecf8e.svg)](LICENSE)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white)](https://developer.mozilla.org/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?logo=css3&logoColor=white)](https://developer.mozilla.org/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=black)](https://developer.mozilla.org/docs/Web/JavaScript)
![No build step](https://img.shields.io/badge/build-none-informational)
[![WordPress theme](https://img.shields.io/badge/WordPress-theme-21759B?logo=wordpress&logoColor=white)](wordpress-theme/praveen-devops-log)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)](wordpress-theme/praveen-devops-log)

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

MIT — see [LICENSE](LICENSE) .
