# CKEditor 5 for REDAXO

CKEditor 5 integration for REDAXO with profile-based configuration, REDAXO media/link dialogs, snippets, style management, and import/export workflows.

## Version

Current development version: `7.0.0-dev`

## Requirements

- REDAXO: `>= 5.12`
- PHP: `>= 7.2, < 9`
- Conflicts:
  - `mblock < 3.4.0`
  - `mform < 6.1.0`

## What Is New in 7.0.0-dev

- Switched to the official CKEditor 5 build as addon base
- New native runtime plugins and updated editor dialogs
- Templates replaced by snippets
- Extended profile export/import with bundled dependencies:
  - profiles
  - style groups
  - styles
  - snippets
- New plugin/build documentation in `PLUGIN_DEVELOPMENT.md`
- Cleanup of legacy and orphaned vendor/runtime files

## Feature Overview

### Editor and UX

- Modern CKEditor 5 integration in REDAXO backend
- Theme support (`dark`, `auto`, `notheme`)
- Language-aware placeholders and UI/content language handling
- Height control via data attributes (`data-min-height`, `data-max-height`)
- Stable initialization for repeated/dynamic fields (for example MBlock reindex)

### Profile System

- Profile manager for editor configurations
- Drag and drop/tag-based profile editing
- Expert mode with raw `expert_definition` + `expert_suboption`
- Live preview page for profile output and integration snippets

### Styles and Snippets

- Single style entities with element/classes and optional CSS
- Style groups with JSON configuration and optional CSS
- Snippet entities selectable per profile (replacement for templates)
- Auto-generated backend CSS from configured style/style-group CSS definitions

### Media and Links

- REDAXO media integration (`openREXMedia`) for image insertion/replacement
- REDAXO link integration (`openLinkMap`, media links, `mailto:`, `tel:`, YTable)
- Image upload endpoint for media pool upload workflows
- Image toolbar safeguards for image linking (`linkImage`)

### Plugin Runtime

- Native addon plugins loaded at runtime:
  - `RedaxoLinkIntegration`
  - `RedaxoMediaImage`
  - `RedaxoMediaVideo`
  - `RedaxoSnippets`
  - `RedaxoPastePlainTextToggle`
- External plugin registry support via addon API and JS config
- Toolbar alias transformations for external plugins

## Installation

1. Install addon (Installer or package deployment).
2. Run REDAXO update/install routine.
3. Open `CKEditor 5 > Profiles` and configure at least one profile.
4. Use the profile in your textarea via `data-profile`.

## Basic Usage

### Minimal textarea integration

```php
<textarea
  class="form-control cke5-editor"
  data-profile="default"
  data-lang="<?php echo \Cke5\Utils\Cke5Lang::getUserLang(); ?>"
  data-content-lang="<?php echo \Cke5\Utils\Cke5Lang::getOutputLang(); ?>"
  name="REX_INPUT_VALUE[1]"
>REX_VALUE[1]</textarea>
```

### With height limits

```php
<textarea
  class="form-control cke5-editor"
  data-profile="default"
  data-min-height="220"
  data-max-height="700"
  data-lang="<?php echo \Cke5\Utils\Cke5Lang::getUserLang(); ?>"
  name="REX_INPUT_VALUE[2]"
>REX_VALUE[2]</textarea>
```

### Frontend output

```html
REX_VALUE[id="1" output="html"]
```

## Integration Examples

### MForm

```php
$mform = new MForm();
$mform->addTextAreaField(1, [
    'label' => 'Text',
    'class' => 'cke5-editor',
    'data-profile' => 'default',
    'data-lang' => \Cke5\Utils\Cke5Lang::getUserLang(),
    'data-content-lang' => \Cke5\Utils\Cke5Lang::getOutputLang(),
]);

echo $mform->show();
```

### MBlock

```php
$id = 1;
$mform = new MForm();
$mform->addFieldset('Accordion');
$mform->addTextField("$id.0.title", ['label' => 'Title']);
$mform->addTextAreaField("$id.0.text", [
    'label' => 'Text',
    'class' => 'cke5-editor',
    'data-profile' => 'default',
    'data-lang' => \Cke5\Utils\Cke5Lang::getUserLang(),
    'data-content-lang' => \Cke5\Utils\Cke5Lang::getOutputLang(),
]);

echo MBlock::show($id, $mform->show());
```

### YForm (custom attributes)

```json
{"class":"cke5-editor","data-profile":"default","data-lang":"en","data-content-lang":"en"}
```

## Profiles: Practical Notes

- Toolbar uses CKEditor-style identifiers (`link`, `insertImage`, `snippets`, ...).
- Legacy aliases are migrated/normalized internally where applicable.
- Snippets are selected per profile.
- Style groups and styles are selected per profile and merged for output config.
- In profile edit mode, language placeholders can be configured per REDAXO locale.

## Snippets Instead of Templates

Templates are no longer part of the active workflow.
Use snippets for reusable editor content blocks.

Recommended workflow:

1. Create snippets in `Profiles > Customise > Snippets`.
2. Assign snippets to one or more profiles.
3. Add `snippets` button to profile toolbar.

## Export and Import

### Export

`Profiles > Export` exports selected profiles including linked dependencies.

Export payload includes:

- `profiles`
- `style_groups`
- `styles`
- `snippets`

### Import

`Profiles > Import` supports:

- New bundle format (profiles + dependencies)
- Legacy profile-only format

Import performs ID-based upsert for bundled tables and then profile import.

## Config Page

`CKEditor 5 > Config` provides:

- License key configuration
- Upload/replace of editor runtime files (`.js`, `.js.map`)
- Upload/replace translation files (`.js`)

Default runtime path is modern build under:

- `assets/addons/cke5/vendor/ckeditor5-modern/`

## API Example

Programmatically create an expert profile:

```php
use Cke5\Creator\Cke5ProfilesApi;

$definition = json_encode([
    'toolbar' => [
        'items' => ['heading', '|', 'bold', 'italic', 'link', 'snippets', 'undo', 'redo'],
    ],
], JSON_UNESCAPED_UNICODE);

Cke5ProfilesApi::addProfile(
    'project_expert',
    'Project expert profile',
    $definition,
    null
);
```

## Custom CSS Strategy

You can combine:

- static custom CSS in addon/project assets
- generated CSS from style/style-group definitions
- optional external CSS paths configured per style/style group

The addon regenerates backend CSS artifacts when style/style-group data changes.

## Plugin Development

For build sources, runtime plugin architecture, and external plugin integration, see:

- `PLUGIN_DEVELOPMENT.md`

## Troubleshooting

### Editor does not initialize

- Check that textarea has class `cke5-editor`.
- Verify `data-profile` exists.
- Verify `cke5profiles.js` is generated and loaded.

### Image link button disabled/missing

- Ensure profile image toolbar contains `linkImage`.
- Ensure build contains `LinkImage` plugin.
- Hard-reload backend after JS updates.

### Translations not loaded

- Verify configured translation path and files in Config page.
- Ensure profile/user language maps to available CKEditor translation files.

## License

See `LICENSE.md`.

## Support

- Issues: https://github.com/FriendsOfREDAXO/cke5/issues
