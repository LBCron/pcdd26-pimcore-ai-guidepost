# Demo findings

Notes from building the demos in this repo with the installed agent skills. Beginner-friction style: what a first-time Pimcore developer (human or agent) hits, where the skills helped, where they did not.

## 2026-11-04: Guidepost as CMS pages (AGENTS.md becomes a Pimcore Document)

Task: turn `AGENTS.md` into a real Pimcore Document served at `/`, with the body content in wysiwyg editables so a rich text editor integration can edit exactly this content. Implemented as `GuidepostController` + `templates/guidepost/guidepost.html.twig` + idempotent `app:guidepost:seed` console command that rewrites the Home document (id 1).

### What the skills covered

- CORS `pimcore` skill (`.claude/skills/pimcore/`): correctly framed Documents as one of the three element pillars ("template-based rendering with editables") and showed the `pimcore.documents.default_controller` config key, which explained how the skeleton's homepage resolves its controller. Its Step 10 checklist (`lint:twig`, `lint:yaml`, `cache:clear`) was directly usable for verification.
- `pimcore-onboarding` (this repo's own skill): carried the two rules that prevented real mistakes: run every PHP command through the container, and never declare done without verifying in the running app.
- Official `pimcore/skills` (33 skills): consulted the index, nothing applies; they are all Studio bundle development. This repo's AGENTS.md gap list already says so, and this task confirmed it in practice.

### What the skills did not cover (the actual work)

- Nothing anywhere explains how to BUILD a CMS page, arguably the most beginner task in a CMS: which base class a frontend controller extends (`Pimcore\Controller\FrontendController`), that templates live in `templates/` and are referenced by relative path on the Document, or that the Twig functions `pimcore_input('name')` and `pimcore_wysiwyg('name')` both render the edit UI in editmode and the stored content on the frontend. All of this came from reading the skeleton's `DefaultController` and the vendor source (`vendor/pimcore/pimcore/models/Document/Editable/*`), not from any skill.
- Creating or filling a Document programmatically is undocumented in the skills: the editable-object dance (`new Editable\Wysiwyg()`, `setDocument()`, `setName()`, `setRealName()`, `setDataFromEditmode()`, then `$page->setEditable()`) had to be reverse-engineered from `PageSnippet.php`. A "seed content from code" recipe is a natural fixture/demo need and nobody ships it.
- No skill mentions that the controller string stored on a Document has the exact format `App\Controller\GuidepostController::guidepostAction` (FQCN + `::` + method), even though every Document needs one.

### Surprising Pimcore behavior

- `Wysiwyg::save()` silently sanitizes the HTML through symfony/html-sanitizer (`html_sanitizer.sanitizer.pimcore.wysiwyg_sanitizer`). The allowlist (see `debug:config framework html_sanitizer`) includes tables, lists, links, spans with class/style, but NOT `<code>` or `<pre>`. Seeding developer-flavored content with inline code loses those elements with no warning; this page uses `<span class="code">` instead. Silent data loss on save is a sharp edge for any CMS content with code in it.
- Editables are not fields on the page record: they live in `documents_editables` keyed by `(documentId, name, type)`, and the Twig template's editable names ARE the schema. Nothing validates that seeded editable names match the template; a typo just renders an empty editable.
- The Home document is hardcoded as id 1 with an empty key and path `/`; "make the homepage this page" means mutating document 1, not creating anything.
- The Studio tree picked the change up with no manual reindexing: saving the Document dispatched generic-data-index messages and the supervisord container's messenger consumers processed them (verified with `messenger:stats` at zero and the Studio API `GET /pimcore-studio/api/documents/tree` returning the new title and published state).
- The Studio backend API authenticates with a plain `POST /pimcore-studio/api/login` (JSON username/password) and a session cookie, which made headless verification easy.

### Editable inventory on the page (for tomorrow's editor integration)

| Editable name | Type | Content |
|---|---|---|
| `headline` | input | Page H1 |
| `intro` | wysiwyg | Intro paragraphs + "built from" list |
| `found_resources` | wysiwyg | The "Found" resources table |
| `marked_gaps` | wysiwyg | The gaps checklist |
| `podcast_note` | wysiwyg | Generated podcast note |
| `reference_points` | wysiwyg | Other-ecosystem reference list |
