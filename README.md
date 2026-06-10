# CKEditor 5 for REDAXO

CKEditor 5 integration for REDAXO with profile-based configuration, REDAXO media/link dialogs, snippets, style management, and import/export workflows.

## Version

Current development version: `7.0.0-dev`

## Requirements

- REDAXO: `>= 5.19`
- PHP: `>= 8.1, < 9`
- Conflicts:
  - `mblock < 4.3.0`
  - `mform < 7.0.0`

## What Is New in 7.0.0-dev

- Switched to the official CKEditor 5 build as addon base
- New native runtime plugins and updated editor dialogs
- Templates replaced by snippets
- Extended profile export/import with bundled dependencies:
  - profiles
  - style groups
  - styles
  - snippets
- New developer documentation in `dev.md`
- Cleanup of legacy and orphaned vendor/runtime files
- New global defaults page (`Profiles > Defaults > Global settings`) for mentions, Sprog replacements, yTables, media defaults, and font defaults
- New editor type `classic_balloon` and configurable balloon toolbar in profile manager
- Improved merge/fallback behavior between profile settings and global defaults
- UX fixes in profile/default widgets (mentions examples, stable placeholders, robust toggle/collapse init)

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
  - `RedaxoSnippets`
  - `RedaxoPastePlainTextToggle`
  - `RedaxoMarkdownPasteToggle`
  - `RedaxoMinimapToggle`
  - `RedaxoVideoWidgetTest`
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

## JSON Configuration Cookbook (Profile Fields)

Several profile fields expect JSON input. This section gives working starter examples.

### 1) `link_decorators_definition`

Use this to add manual link decorators. A practical use case is Bootstrap-like link buttons.

```json
[
  {
    "btnPrimary": {
      "mode": "manual",
      "label": "Button Primary",
      "attributes": {
        "class": "btn btn-primary",
        "role": "button"
      }
    }
  },
  {
    "btnOutline": {
      "mode": "manual",
      "label": "Button Outline",
      "attributes": {
        "class": "btn btn-outline-secondary",
        "role": "button"
      }
    }
  },
  {
    "nofollow": {
      "mode": "manual",
      "label": "Add nofollow",
      "attributes": {
        "rel": "nofollow"
      }
    }
  }
]
```

Tip: this JSON is merged into `link.decorators` of the generated CKEditor profile.

Exclusive decorator groups (only one active at a time):

If multiple manual decorators should be mutually exclusive (for example button variants, color variants, badge variants), set the same `redaxoExclusiveGroup` on those decorators.

Example:

```json
[
  {
    "btnPrimary": {
      "mode": "manual",
      "label": "Button Primary",
      "classes": "btn btn-primary",
      "redaxoExclusiveGroup": "linkButtonStyle"
    }
  },
  {
    "btnSuccess": {
      "mode": "manual",
      "label": "Button Success",
      "classes": "btn btn-success",
      "redaxoExclusiveGroup": "linkButtonStyle"
    }
  },
  {
    "nofollow": {
      "mode": "manual",
      "label": "Add nofollow",
      "attributes": {
        "rel": "nofollow"
      }
    }
  }
]
```

Result: In the link dialog, only one decorator from `linkButtonStyle` can be active at the same time, while unrelated decorators (like `nofollow`) remain independent.

### 2) `mentions_definition`

Defines custom mention feeds.

```json
[
  {
    "marker": "@",
    "minimumCharacters": 1,
    "feed": ["@support", "@sales", "@redaktion", "@admin"]
  },
  {
    "marker": "#",
    "minimumCharacters": 1,
    "feed": ["#news", "#release", "#event", "#faq"]
  }
]
```

### 3) `sprog_mention_definition`

Sprog replacements are JSON-based and are exposed via `{` mention marker.

```json
[
  { "id": "{{company}}", "text": "Friends Of REDAXO" },
  { "id": "{{support_mail}}", "text": "support@example.org" },
  { "id": "{{hotline}}", "text": "+49 000 123456" }
]
```

### 4) `image_resize_options_definition`

Defines explicit image resize options used in image toolbar.

```json
[
  { "name": "resizeImage:original", "label": "Original", "value": null },
  { "name": "resizeImage:25", "label": "25%", "value": "25" },
  { "name": "resizeImage:50", "label": "50%", "value": "50" },
  { "name": "resizeImage:75", "label": "75%", "value": "75" }
]
```

Note: the addon normalizes names internally for profile output.

### 5) `transformation_extra`

Adds additional typing transformations.

```json
[
  { "from": "->", "to": "→" },
  { "from": "<-", "to": "←" },
  { "from": "(c)", "to": "©" },
  { "from": "(r)", "to": "®" }
]
```

### 6) `html_support_allow`

Allow additional elements/attributes/classes/styles.

```json
[
  {
    "name": "regex(/^(section|article|div)$/)",
    "attributes": true,
    "classes": true,
    "styles": true
  },
  {
    "name": "a",
    "attributes": ["target", "rel", "data-bs-toggle", "data-bs-target"],
    "classes": ["btn", "btn-primary", "btn-outline-secondary"],
    "styles": false
  }
]
```

### 7) `html_support_disallow`

Disallow specific patterns even if allowed elsewhere.

```json
[
  {
    "name": "script",
    "attributes": true,
    "classes": true,
    "styles": true
  },
  {
    "name": "*",
    "attributes": ["on.*"]
  }
]
```

### 8) `extra_definition`

Advanced raw merge into generated profile JSON. Use with care.

```json
{
  "removePlugins": ["Autoformat"],
  "heading": {
    "options": [
      { "model": "paragraph", "title": "Paragraph", "class": "ck-heading_paragraph" },
      { "model": "heading2", "view": "h2", "title": "H2", "class": "ck-heading_heading2" }
    ]
  }
}
```

Tip: if you define `removePlugins` here, it is merged with existing remove list.

### Validation Tips

- Always use valid JSON (double quotes, no trailing commas).
- Start with small JSON snippets and test in one profile first.
- If a profile fails to behave as expected, open profile preview and inspect generated JSON.

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

## Credits

Demo content image: [Frankfurt am Main Skyline](https://pixabay.com/photos/building-horizon-city-skyscraper-7092747/) by [Leonhard_Niederwimmer](https://pixabay.com/users/leonhard_niederwimmer-1131094/) on [Pixabay](https://pixabay.com/) — free to use under the [Pixabay Content License](https://pixabay.com/service/license-summary/).

Friends of REDAXO logo: [friendsofredaxo.github.io](https://friendsofredaxo.github.io) — © Friends of REDAXO.

## Support

- Issues: https://github.com/FriendsOfREDAXO/cke5/issues
