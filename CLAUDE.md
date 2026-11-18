# CLAUDE.md

## What this project is

**`remi-san/minipavi-service-skeleton`**: the Minitel service to start from (`composer
create-project`), built on the MiniPavi service framework,
[remi-san/minipavi-service](https://github.com/remi-san/minipavi-service). Its README
(`README.md`) says how to create a service and what is in it. The framework's README
(`vendor/remi-san/minipavi-service/README.md`) documents its API and the page XML format.

What `composer create-project` copies is the service someone starts from: keep it small,
working and commented, and keep anything else (this file, the README, CI) out of it through
`.gitattributes` (`export-ignore`).

It requires the framework's `dev-main`, so a framework change can break it without any change
here: CI also runs every week.

## Workflow for a change

Work is tracked in GitHub issues on this repository. For each issue:

1. **Read the issue** (`gh issue view <n>`) and the code it points to. If the proposal no longer
   matches the code, or the issue is ambiguous, say so before starting.
2. **Create a branch from an up-to-date `main`**. Never commit to `main` directly.
   ```sh
   git switch main && git pull --ff-only
   git switch -c <type>/<issue-number>-<short-slug>
   ```
   Fix one issue per branch, and open its PR against `main` (don't stack PRs).
3. **Make small commits** that follow the Karma convention (below). Each commit does one
   thing and leaves the code working. A refactoring, a behaviour change, and the docs update are
   separate commits.
4. **Verify** before pushing (see [Checking a change](#checking-a-change)).
5. **Push and open a PR** against `main`. The PR body states what changed and why, how it was
   checked, and any follow-up, and ends with `Closes #<n>`. Don't merge the PR: the user
   reviews and merges it.

## Commit messages: Karma convention

```
<type>(<scope>): <subject>

<body>

<footer>
```

- **type**: `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`, `chore`.
- **scope** (optional): the part touched, e.g. `controller`, `pages`, `messages`, `container`.
- **subject**: imperative, present tense, lowercase, no final period, about 70 characters at
  most.
- **body** (optional): why the change was made and how it differs from before, wrapped at 72
  columns.
- **footer**: `Refs #<n>` on intermediate commits, `Closes #<n>` on the one that resolves the
  issue.

## Checking a change

Check it as its users get it: create a service from this checkout, then run its checks there
(what CI does):

```sh
composer create-project remi-san/minipavi-service-skeleton /tmp/Created \
  --repository='{"type": "path", "url": ".", "options": {"symlink": false}}' --stability=dev
cd /tmp/Created && composer cs && composer analyse && composer lint && composer test
```

Add or update tests for the changed behaviour (`tests/`, with the framework's
`ServiceTester`). To try it in the local MiniPavi stack, create it in minipavi-stack's
`services/` instead (see the README). Say in the PR which checks were run.

## Code conventions

- PHP 8.4+ (lazy objects). Prefer `final` and `readonly` classes, enums over string constants,
  and typed parameters and returns.
- The framework's style: Mago's formatter (PER Coding Style), with `declare(strict_types=1)`,
  and its linter and analyzer (`mago.toml`). Don't turn off or ignore a Mago rule to make an
  issue go away: fix the code.
- Document classes and methods with PHPDoc (`/** ... */`, typed `@param` / `@return`).
