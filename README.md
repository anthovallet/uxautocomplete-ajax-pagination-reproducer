# ux-autocomplete remote multi-select pagination reproducer

Minimal reproducer for a `symfony/ux-autocomplete` issue: symfony/ux#4084


## Versions

- PHP 8.5.11
- Symfony 7.4.20
- `symfony/ux-autocomplete` 3.5.1

## Setup

```bash
composer install
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load -n
symfony server:start
```

SQLite only (`var/data.db`), no Docker needed.

## Steps to reproduce

1. 50 `Item` entities are loaded (`Item 01` … `Item 50`).
2. Open `/`: an `#[AsEntityAutocompleteField]` field with `'multiple' => true`, where `Item 01` to `Item 08` are pre-selected.
3. Open the dropdown without typing anything.

## Expected

The dropdown shows the unselected items (`Item 09`, `Item 10`, `Item 11`, …) and scrolling loads further pages.

## Actual

Only `Item 09` and `Item 10` are shown. The list is too short to scroll, no request for `page=2` is sent, and `Item 11` to `Item 50` cannot be reached without typing a search.

## Why

The autocomplete endpoint paginates (10 results per page) without knowing the current selection, so page 1 (`/autocomplete/item_autocomplete_field?query=&page=1`) returns `Item 01` to `Item 10`, already-selected ones included.
TomSelect then hides the selected options client-side (`hideSelected` defaults to `true` in multi mode), leaving only 2 visible options.
The `virtual_scroll` plugin only loads `next_page` when the user scrolls to the bottom of the menu, which never happens here because the menu has nothing to scroll.
Excluding the already-selected values server-side (or loading the next page when too few options remain visible) would avoid this, and seems the logical way to fill the select options.
