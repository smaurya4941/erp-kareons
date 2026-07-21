# Brand assets

The application uses a configurable brand logo/favicon.

Resolution order (see `app/Helpers/GlobalHelper.php`):

1. **Admin upload** — Settings → Company → *Company Logo* / *Favicon*
   (stored under `storage/app/public/settings`). This overrides everything.
2. **Bundled default** — `public/images/logo.png` (this folder).

## To ship the KARE ONS HERBALS logo as the default

Save the logo image here as:

    public/images/logo.png

Recommended: a square PNG (e.g. 512×512) with a transparent or white background.

That single file is used as the default logo **and** the default favicon
everywhere (sidebar, navbar, login, browser tab) until an admin uploads a
different one from the Settings page.
