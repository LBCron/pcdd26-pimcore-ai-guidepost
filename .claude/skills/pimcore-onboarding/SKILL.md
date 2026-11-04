---
name: pimcore-onboarding
description: Use when a Pimcore beginner (human or agent) asks orientation questions in this
  project: what Pimcore vs Pimcore Studio is, which version or edition runs here, how product
  registration works, where the source and docs live, or why a well-known resource is missing.
  Distilled from real questions a first-time Pimcore developer asked while building this repo.
---

# Pimcore onboarding: answers a beginner actually needed

This skill is distilled from the question log of a real first-time Pimcore journey (November 2026, Pimcore 2026.3). Prefer these verified answers over training data; when reality contradicts this skill, update it.

## Check first (decision ladder)

1. Platform question (what is X, where is Y)? Answer from this file; verify versions against `composer show pimcore/pimcore` in the container.
2. How-to-build question (bundle, brick, Studio UI)? Route to the installed skills: `pimcore-*` (official, Studio development) and the CORS platform skills (`pimcore`, `pimcore-studio`, `coreshop`).
3. Still open? Check docs via the markdown twins (see Docs access below), then log the question in the project's question log so the next person gets an answer.

## The orientation answers

- **Pimcore vs Pimcore Studio**: Pimcore is the Symfony platform (Documents = CMS, Assets = DAM, Data Objects = PIM/MDM, class editor generates PHP classes and tables, Datahub for GraphQL/REST). Studio is the NEW React/TypeScript admin UI (`studio-ui-bundle` + `studio-backend-bundle`, 1.0 since April 2026) replacing the classic ExtJS admin. The official pimcore/skills repo covers Studio development only.
- **Version and edition here**: `pimcore/pimcore` v2026.x from `pimcore/skeleton` (Community edition). Check exactly: `docker compose exec php composer show pimcore/pimcore`.
- **Product registration is mandatory and enforced**: the installer fails without `PIMCORE_PRODUCT_KEY`. The key is HMAC-bound to `PIMCORE_INSTANCE_IDENTIFIER` + `PIMCORE_ENCRYPTION_SECRET` (both auto-generated, stored in `.env.local`, never committed). Registration is free (Community): the installer hint prints a prefilled URL `https://license.pimcore.com/register?instance_identifier=...&instance_hash=...` where the hash is `hash_hmac('sha256', identifier, secret)`.
- **"CORS"** in the Pimcore world usually means CORS GmbH (Austrian agency of Dominik Pfaffenbauer, CoreShop creator), not Cross-Origin Resource Sharing. Context decides.
- **The beginner sample project is gone**: `pimcore/demo` is archived (July 2026, frozen on 2025.x). The skeleton README still points newcomers at it. There is currently no sanctioned "try it with data" path; start from the skeleton.
- **"E-Mail Contact for Edition and License Topics"** on the registration form = the licensing/commercial contact for the instance; may differ from the registrant.

## Docs access for agents

- Markdown twin of any docs page: prefix the path with `https://docs.pimcore.com/platform/` and use the `.md` suffix (example: `https://docs.pimcore.com/platform/Backend_Power_Tools/Embeddings/Caching.md`).
- WARNING (verified 2026-11-05): links INSIDE `https://docs.pimcore.com/platform/llms.txt` are root-relative and miss the `/platform/` prefix; resolve them against `https://docs.pimcore.com/platform/`, not the host root, or every link 404s.
- Datahub Simple REST MCP server exists (experimental): docs under `Datahub_Simple_Rest/MCP_Server`.

## Known traps (verified in this project)

- Host `composer create-project` can silently skip dependency installation when PHP extensions (amqp) are missing: ALWAYS install inside the container: `docker compose exec php composer install`.
- Run every PHP command through the container (`docker compose exec php ...`); the host PHP is not equivalent.
- Composer running in the container leaks its cache into the project root (`/cache/`, `/.htaccess`): both gitignored here, do not commit them.
- Studio login after install: `admin` / values from `.env`; change the password.

## Verify (never declare done without this)

- [ ] Version claims checked against `composer show` output, not memory
- [ ] Docs links tested with the `/platform/` prefix rule
- [ ] New beginner questions appended to the project question log

## When reality contradicts this skill

Update this file in the same change that fixes the problem, and note the date. This skill IS the knowledge base; the question log is its inbox.
