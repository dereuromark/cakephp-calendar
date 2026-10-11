# Contributing

Run PHP checks from the repository root after installing Composer dependencies:

```sh
composer test
composer cs-check
composer stan-setup
composer stan
```

Build the documentation from `docs`:

```sh
npm ci
npm run docs:build
```

Use `npm run docs:dev` for a local preview. Update examples when changing behavior, and check them against the supported CakePHP APIs. See the [CI workflow](https://github.com/dereuromark/cakephp-calendar/blob/cake6/.github/workflows/ci.yml) for the tested runtime matrix.
