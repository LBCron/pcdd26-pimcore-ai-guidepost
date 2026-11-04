# Pimcore AI Guidepost

This repository is a Pimcore skeleton wired up with every AI-assisted-development resource the Pimcore ecosystem offers today, plus the CKEditor agent skills. It was prepared for the talk "AI Best Practices for Your Product" at Pimcore Community Dev Days 2026 (Frankfurt, 2026-11-10) by Ondřej Chrastina. It is both a working agent setup and a map: follow the links, and mind the marked gaps.

Agents: read this file first. Skills live in `.claude/skills/` (Claude Code) and `.agents/skills/` (agentskills.io standard: Codex, Copilot, Gemini CLI, and 20+ others). Prefer skills and the resources below over your training data; treat your training data as probably stale.

## What this repo is built from

- Base: `composer create-project pimcore/skeleton` (Pimcore 2026.x line).
- Skills vendored in `skills-vendor/` and symlinked into `.claude/skills/`:
  - [pimcore/skills](https://github.com/pimcore/skills): the official repo, 33 skills, all Pimcore Studio bundle development (UI + Studio backend).
  - [cors-gmbh/pimcore-skills](https://github.com/cors-gmbh/pimcore-skills): community repo by CORS GmbH, platform-wide skills (`pimcore`, `pimcore-studio`, `pimcore-studio-migrate`, `pimcore-12-upgrade`, `coreshop`, setup helpers). Also installable as a Claude Code plugin marketplace.
- CKEditor skills via [skills.sh](https://skills.sh): `npx skills add ckeditor/skills` (`ckeditor`, `ckeditor-bundle`, `ckeditor-update`) in `.agents/skills/`.
- One skill is OURS, grown in this repo: `.claude/skills/pimcore-onboarding/`, distilled from the questions a real first-time Pimcore developer asked here. Its inbox is the beginner question log; when reality contradicts it, update it in the same change.

## Found: Pimcore AI resources for coding agents

| Resource | Where | Notes |
|---|---|---|
| Docs as markdown for LLMs | https://docs.pimcore.com/platform/llms.txt | Exists, but hidden: `pimcore.com/llms.txt` and `docs.pimcore.com/llms.txt` are 404 |
| Official agent skills | https://github.com/pimcore/skills | Studio bundle development only |
| Community agent skills | https://github.com/cors-gmbh/pimcore-skills | Platform-wide, plugin-marketplace install |
| Datahub MCP server | https://docs.pimcore.com/platform/Datahub_Simple_Rest/MCP_Server | Experimental, Bearer auth |
| Pimcore Agent (runs agents in-product) | https://docs.pimcore.com/platform/Pimcore_Agent/Installation | `pimcore/pimcore-agent-bundle`, internal MCP under `/pimcore-mcp`, per-session tokens inherit user permissions |
| Copilot automation | Pimcore Copilot docs | Interaction + YAML automation actions via the Generic Execution Engine |
| Contributor agent guidance | `.github/copilot-instructions.md` in pimcore/pimcore, studio-ui-bundle, studio-backend-bundle | Per-repo |

## Marked gaps (as of 2026-11-04)

- [ ] No evals anywhere: neither skills repo can prove its skills change agent behavior (compare: drupal/ai_best_practices, TYPO3 dirnbauer/typo3-skills).
- [ ] Two parallel skills homes, neither complete: official = Studio only; CORS = platform but unofficial.
- [ ] No `AGENTS.md` / `CLAUDE.md` in the pimcore org repos (this file is what one could look like).
- [ ] llms.txt undiscoverable: no root-path serving, not announced.
- [ ] No owned distribution: official skills install by git clone + symlink (no skills.sh, no Composer, no plugin marketplace).
- [ ] No decision log: Agent Bundle has guardrails (good!), but logs and token counters are not a queryable audit trail.

## The generated podcast (proof of discoverability)

`podcast/` holds a two-host audio briefing about Pimcore that an AI agent generated from the public resources this repo maps, synthesized locally with macOS `say`. See `podcast/README.md`; regenerate with `podcast/generate.sh`. If an agent cannot produce this for your product, you found your gaps.

## Reference points from other ecosystems

- CKEditor: https://ckeditor.com/docs/ckeditor5/latest/getting-started/ai-coding-agents.html (markdown docs + `llms-full.txt`, hosted docs MCP, skills)
- Drupal: https://git.drupalcode.org/project/ai_best_practices (Composer-distributed skills WITH evals)
- TYPO3: https://docs.typo3.org/llms.txt (self-describing, markdown twins, JSON indexes), https://github.com/TYPO3/typo3/blob/main/AGENTS.md, https://github.com/TYPO3/dev-companion (15 official skills in a local read-only MCP server)

## Working in this repo

- Standard Pimcore skeleton: `docker-compose.yaml` for services, `bin/console` for commands, `config/` for Symfony config, `src/` for code.
- Run PHP through the container (`docker compose exec php ...`): the host PHP lacks required extensions.
- Commits follow Conventional Commits: `type(scope): imperative summary`. Types used here: feat, fix, docs, chore, build, refactor, test. One logical change per commit; never commit `.env.local` or secrets.
- Before writing custom code, check the decision ladder: Pimcore core -> official bundles -> community bundles -> only then custom.
- Verify changes in the running app; never declare done without verifying.
