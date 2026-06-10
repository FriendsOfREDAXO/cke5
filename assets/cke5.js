(() => {
  (() => {
    (() => {
      (() => {
        let ckeditors = {};
        let ckareas = ".cke5-editor";
        $(document).on("rex:ready", function(e, container) {
          cke5_init_ready(container.find(ckareas));
        });
        $(document).on("ready", function() {
          if (typeof mblock_module === "object") {
            mblock_module.registerCallback("reindex_end", function() {
              if ($(ckareas).length) {
                if (mblock_module.lastAction === "add_item") {
                  cke5_destroy(mblock_module.affectedItem.find(ckareas));
                  cke5_init_all(mblock_module.affectedItem.find(ckareas));
                }
              }
            });
          }
        });
        function cke5_init_ready(cke_areas) {
          $.each(cke_areas, function(key, editor) {
            cke5_init($(editor));
            if (rex.cke5theme != "notheme") {
              if (rex.cke5theme == "dark") {
                if (!$("#ckedark").length) {
                  $("head").append('<link id="ckedark" rel="stylesheet" type="text/css" href="' + rex.cke5darkcss + '">');
                }
              }
              if (rex.cke5theme == "auto" && window.matchMedia) {
                if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
                  $("head").append('<link id="ckedark" rel="stylesheet" type="text/css" href="' + rex.cke5darkcss + '">');
                }
                window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", (event) => {
                  if (event.matches) {
                    $("head").append('<link id="ckedark" rel="stylesheet" type="text/css" href="' + rex.cke5darkcss + '">');
                  } else {
                    $("head").find("#ckedark").remove();
                  }
                });
              }
            }
          });
        }
        function cke5_init_all(elements) {
          elements.each(function() {
            cke5_init($(this));
          });
        }
        function cke5_init(element) {
          let initState = element.attr("data-cke5-init-state");
          if (initState === "pending" || initState === "ready") {
            return;
          }
          if (!element.next().length || !element.next().hasClass("ck")) {
            let unique_id = element.attr("id") || "ck" + Math.random().toString(16).slice(2), options = {}, sub_options = {}, profile_set = element.attr("data-profile"), min_height = element.attr("data-min-height"), max_height = element.attr("data-max-height"), lang = { "ui": "", "content": "" }, ui_lang = element.attr("data-lang"), content_lang = element.attr("data-content-lang"), repeater_cke = element.attr("repeater_cke") === "1";
            if (repeater_cke) {
              unique_id = element.attr("id");
            } else if (!element.attr("id")) {
              element.attr("id", unique_id);
            } else {
              unique_id = element.attr("id");
            }
            if (typeof profile_set === void 0 || !profile_set) {
            } else {
              if (profile_set in cke5profiles) {
                options = cke5profiles[profile_set];
              }
              if (profile_set in cke5suboptions) {
                if (cke5suboptions[profile_set].length > 0) {
                  cke5suboptions[profile_set].forEach(function(value, key) {
                    if (value.hasOwnProperty("min-height")) {
                      sub_options["min-height"] = value["min-height"];
                    }
                    if (value.hasOwnProperty("max-height")) {
                      sub_options["max-height"] = value["max-height"];
                    }
                  });
                }
              }
            }
            if (typeof min_height === void 0 || !min_height) {
            } else {
              sub_options["min-height"] = min_height;
            }
            if (typeof max_height === void 0 || !max_height) {
            } else {
              sub_options["max-height"] = max_height;
            }
            if (typeof ui_lang === void 0 || !ui_lang) {
            } else {
              lang["ui"] = ui_lang;
            }
            if (typeof content_lang === void 0 || !content_lang) {
            } else {
              lang["content"] = content_lang;
            }
            if (lang["ui"] !== "" || lang["content"] !== "") {
              if (options["language"] === void 0) options["language"] = [];
              if (lang["ui"] !== "") options["language"]["ui"] = lang["ui"];
              if (lang["content"] !== "") options["language"]["content"] = lang["content"];
              if (lang["ui"] !== "" && options["placeholder_" + lang["ui"]] !== void 0) {
                options["placeholder"] = options["placeholder_" + lang["ui"]];
              }
            }
            if (typeof options.licenseKey === "undefined" || options.licenseKey === null || options.licenseKey === "") {
              options.licenseKey = "GPL";
            }
            options = cke5_prepare_link_decorator_exclusive_groups(options);
            options = cke5_apply_external_profile_transforms(options, element);
            if (ckeditors[unique_id] === void 0) {
              const editorConstructor = cke5_get_editor_constructor();
              if (!editorConstructor || typeof editorConstructor.create !== "function") {
                console.error("cke5: ClassicEditor constructor not available");
                element.attr("data-cke5-init-state", "none");
                return;
              }
              options = cke5_apply_modern_constructor_fallback(options, editorConstructor);
              options = cke5_ensure_image_link_toolbar(options);
              element.attr("data-cke5-init-state", "pending");
              try {
                editorConstructor.create(document.querySelector("#" + unique_id), options).then((editor) => {
                  ckeditors[unique_id] = editor;
                  element.attr("data-cke5-init-state", "ready");
                  cke5_register_soft_hyphen_postfixer(editor);
                  cke5_init_link_decorator_exclusive_groups(editor, options);
                  cke5_init_external_plugins(editor, unique_id, element, options);
                  cke5_pastinit(editor, sub_options);
                  dispatchCke5Event(editor, unique_id);
                }).catch((error) => {
                  element.attr("data-cke5-init-state", "none");
                  console.error(error);
                });
              } catch (error) {
                element.attr("data-cke5-init-state", "none");
                console.error(error);
              }
            } else {
              element.attr("data-cke5-init-state", "ready");
              console.log("editor already exist: " + unique_id);
            }
          }
        }
        function cke5_get_editor_constructor() {
          if (typeof window.ClassicEditor !== "undefined") {
            return window.ClassicEditor;
          }
          if (typeof window.CKEDITOR === "object" && window.CKEDITOR !== null && typeof window.CKEDITOR.ClassicEditor !== "undefined") {
            return window.CKEDITOR.ClassicEditor;
          }
          return null;
        }
        function cke5_get_modern_default_plugins() {
          if (typeof window.CKEDITOR !== "object" || window.CKEDITOR === null) {
            return [];
          }
          const cke = window.CKEDITOR;
          const pluginNames = [
            "Essentials",
            "Paragraph",
            "Heading",
            "Font",
            "FontSize",
            "FontFamily",
            "FontColor",
            "FontBackgroundColor",
            "Bold",
            "Italic",
            "Underline",
            "Strikethrough",
            "Subscript",
            "Superscript",
            "Link",
            "LinkImage",
            "List",
            "TodoList",
            "Indent",
            "Alignment",
            "BlockQuote",
            "Table",
            "TableToolbar",
            "Image",
            "ImageToolbar",
            "ImageCaption",
            "ImageStyle",
            "ImageResize",
            "ImageUpload",
            "AutoImage",
            "MediaEmbed",
            "Code",
            "CodeBlock",
            "HorizontalLine",
            "PageBreak",
            "FindAndReplace",
            "SelectAll",
            "Mention",
            "TextTransformation",
            "Highlight",
            "Emoji",
            "SpecialCharacters",
            "SpecialCharactersEssentials",
            "PastePlainText",
            "HtmlEmbed",
            "SourceEditing",
            "TextPartLanguage",
            "ShowBlocks",
            "AccessibilityHelp",
            "Style",
            "RemoveFormat",
            "Bookmark"
          ];
            return pluginNames.map((name) => cke[name]).filter((plugin) => typeof plugin === "function");
        }
        function cke5_insert_or_update_link(editor, url, label) {
          if (!editor || typeof url !== "string" || url === "") {
            return;
          }
          const selection = editor.model.document.selection;
          if (!selection || !selection.isCollapsed) {
            editor.execute("link", url);
            return;
          }
          editor.model.change((writer) => {
            const text = typeof label === "string" && label !== "" ? label : url;
            const node = writer.createText(text, { linkHref: url });
            editor.model.insertContent(node, selection.getFirstPosition());
          });
        }
        function cke5_get_link_form() {
          return document.querySelector(".ck.ck-link-form") || document.querySelector(".ck.ck-link-form_layout-vertical");
        }
        function cke5_link_form_set_input_value(input, value) {
          if (!input) {
            return;
          }
          input.focus();
          input.value = value;
          input.dispatchEvent(new Event("input", { bubbles: true }));
          input.dispatchEvent(new Event("change", { bubbles: true }));
        }
        function cke5_get_link_form_inputs(form) {
          if (!form) {
            return { textInput: null, urlInput: null };
          }
          const inputs = Array.from(form.querySelectorAll("input[type='text']"));
          if (inputs.length === 0) {
            return { textInput: null, urlInput: null };
          }
          let textInput = null;
          let urlInput = null;
          inputs.forEach((input) => {
            const aria = ((input.getAttribute("aria-label") || "") + " " + (input.getAttribute("placeholder") || "")).toLowerCase();
            if (!urlInput && (aria.indexOf("link") !== -1 || aria.indexOf("url") !== -1 || aria.indexOf("adresse") !== -1)) {
              urlInput = input;
            }
            if (!textInput && (aria.indexOf("text") !== -1 || aria.indexOf("anzeig") !== -1 || aria.indexOf("display") !== -1)) {
              textInput = input;
            }
          });
          if (!textInput) {
            textInput = inputs[0] || null;
          }
          if (!urlInput) {
            urlInput = inputs[inputs.length - 1] || null;
          }
          return { textInput, urlInput };
        }
        function cke5_apply_link_to_form_or_editor(editor, url, label) {
          const form = cke5_get_link_form();
          if (form) {
            const formInputs = cke5_get_link_form_inputs(form);
            if (formInputs.urlInput) {
              cke5_link_form_set_input_value(formInputs.urlInput, url);
            }
            if (typeof label === "string" && label !== "" && formInputs.textInput && formInputs.textInput.value.trim() === "") {
              cke5_link_form_set_input_value(formInputs.textInput, label);
            }
            return;
          }
          cke5_insert_or_update_link(editor, url, label);
        }
        function cke5_get_link_decorator_attribute_name(name) {
          if (typeof name !== "string" || name.trim() === "") {
            return null;
          }
          const cleanName = name.trim();
          return "link" + cleanName.charAt(0).toUpperCase() + cleanName.slice(1);
        }
        function cke5_prepare_link_decorator_exclusive_groups(options) {
          if (!options || typeof options !== "object") {
            return options;
          }
          if (!options.link || typeof options.link !== "object") {
            return options;
          }
          if (!options.link.decorators || typeof options.link.decorators !== "object") {
            return options;
          }

          const groups = {};
          Object.keys(options.link.decorators).forEach((decoratorName) => {
            const decorator = options.link.decorators[decoratorName];
            if (!decorator || typeof decorator !== "object") {
              return;
            }
            if (decorator.mode !== "manual") {
              return;
            }

            const groupName = typeof decorator.redaxoExclusiveGroup === "string" && decorator.redaxoExclusiveGroup.trim() !== "" ? decorator.redaxoExclusiveGroup.trim() : typeof decorator.exclusiveGroup === "string" && decorator.exclusiveGroup.trim() !== "" ? decorator.exclusiveGroup.trim() : "";
            delete decorator.redaxoExclusiveGroup;
            delete decorator.exclusiveGroup;

            if (groupName === "") {
              return;
            }

            const attrName = cke5_get_link_decorator_attribute_name(decoratorName);
            if (!attrName) {
              return;
            }

            if (!Array.isArray(groups[groupName])) {
              groups[groupName] = [];
            }
            if (!groups[groupName].includes(attrName)) {
              groups[groupName].push(attrName);
            }
          });

          options.redaxoLinkDecoratorExclusiveGroups = Object.values(groups).filter((group) => Array.isArray(group) && group.length > 1);
          return options;
        }
        function cke5_init_link_decorator_exclusive_groups(editor, options) {
          if (!editor || editor._cke5ExclusiveLinkDecoratorsInit) {
            return;
          }
          if (!options || typeof options !== "object") {
            return;
          }

          const groups = Array.isArray(options.redaxoLinkDecoratorExclusiveGroups) ? options.redaxoLinkDecoratorExclusiveGroups : [];
          if (groups.length === 0) {
            return;
          }

          const linkCommand = editor.commands && typeof editor.commands.get === "function" ? editor.commands.get("link") : null;
          if (!linkCommand || typeof linkCommand.on !== "function") {
            return;
          }

          editor._cke5ExclusiveLinkDecoratorsInit = true;
          linkCommand.on("execute", (evt, args) => {
            if (!Array.isArray(args) || args.length < 2) {
              return;
            }
            const commandAttributes = args[1];
            if (!commandAttributes || typeof commandAttributes !== "object") {
              return;
            }

            groups.forEach((group) => {
              if (!Array.isArray(group) || group.length < 2) {
                return;
              }
              const enabled = group.filter((attrName) => commandAttributes[attrName] === true);
              if (enabled.length === 0) {
                return;
              }
              const keep = enabled[enabled.length - 1];
              group.forEach((attrName) => {
                if (attrName !== keep) {
                  commandAttributes[attrName] = false;
                }
              });
            });
          }, { priority: "high" });
        }
        function cke5_get_clang_param() {
          try {
            const params = new URLSearchParams(window.location.search || "");
            const clang = Number(params.get("clang"));
            if (Number.isNaN(clang) || clang < 1) {
              return 1;
            }
            return Math.floor(clang);
          } catch (error) {
            return 1;
          }
        }
        function cke5_open_redaxo_internal_link(editor, linkConfig) {
          if (typeof window.openLinkMap !== "function" || typeof window.jQuery !== "function") {
            return;
          }
          const category = typeof linkConfig.rexlink_category !== "undefined" ? "&category_id=" + linkConfig.rexlink_category : "";
          const popup = window.openLinkMap("", "&clang=" + cke5_get_clang_param() + category);
          window.jQuery(popup).off("rex:selectLink.cke5").on("rex:selectLink.cke5", (event, linkUrl, linkLabel) => {
            event.preventDefault();
            if (popup && typeof popup.close === "function") {
              popup.close();
            }
            cke5_apply_link_to_form_or_editor(editor, linkUrl, linkLabel);
          });
        }
        function cke5_open_redaxo_media_link(editor, linkConfig) {
          if (typeof window.openREXMedia !== "function" || typeof window.jQuery !== "function") {
            return;
          }
          let query = "";
          if (typeof linkConfig.rexmedia_category !== "undefined") {
            query += "&rex_file_category=" + linkConfig.rexmedia_category;
          }
          if (typeof linkConfig.rexmedia_types === "string" && linkConfig.rexmedia_types !== "") {
            query += "&args[types]=" + linkConfig.rexmedia_types;
          }
          const mediaPath = typeof linkConfig.rexmedia_path === "string" && linkConfig.rexmedia_path !== "" ? linkConfig.rexmedia_path : "/media/";
          const popup = window.openREXMedia("cke5_medialink", query);
          window.jQuery(popup).off("rex:selectMedia.cke5").on("rex:selectMedia.cke5", (event, filename) => {
            event.preventDefault();
            if (popup && typeof popup.close === "function") {
              popup.close();
            }
            cke5_apply_link_to_form_or_editor(editor, mediaPath + filename, filename);
          });
        }
        function cke5_safe_prompt(title, defaultValue) {
          try {
            if (typeof window.prompt !== "function") {
              return null;
            }
            return window.prompt(title, defaultValue || "");
          } catch (error) {
            return null;
          }
        }
        function cke5_open_redaxo_prefix_link(editor, prefix, defaultValue) {
          const value = cke5_safe_prompt(prefix, defaultValue || "");
          if (typeof value !== "string") {
            cke5_apply_link_to_form_or_editor(editor, prefix, prefix);
            return;
          }
          const trimmed = value.trim();
          const normalized = trimmed === "" ? prefix : trimmed.indexOf(prefix) === 0 ? trimmed : prefix + trimmed;
          cke5_apply_link_to_form_or_editor(editor, normalized, normalized);
        }
        function cke5_normalize_ytable_entry(entry) {
          if (!entry || typeof entry !== "object") {
            return null;
          }
          const table = typeof entry.table === "string" ? entry.table.trim() : "";
          if (table === "") {
            return null;
          }
          const fieldRaw = typeof entry.column === "string" && entry.column.trim() !== "" ? entry.column : entry.field;
          const field = typeof fieldRaw === "string" && fieldRaw.trim() !== "" ? fieldRaw.trim() : "name";
          const title = typeof entry.title === "string" && entry.title.trim() !== "" ? entry.title.trim() : table;
          const urlPrefix = typeof entry.url === "string" && entry.url.trim() !== "" ? entry.url : table + "://";
          return {
            table: table,
            field: field,
            title: title,
            urlPrefix: urlPrefix
          };
        }
        function cke5_get_ytable_entries(linkConfig) {
          if (!linkConfig || !Array.isArray(linkConfig.ytable)) {
            return [];
          }
          return linkConfig.ytable.map((entry) => cke5_normalize_ytable_entry(entry)).filter((entry) => entry !== null);
        }
        function cke5_open_redaxo_ytable_link(editor, linkConfig, configuredEntry) {
          if (typeof window.newPoolWindow !== "function" || typeof window.jQuery !== "function") {
            return;
          }
          const entries = cke5_get_ytable_entries(linkConfig);
          let selectedEntry = configuredEntry || null;
          if (!selectedEntry) {
            if (entries.length === 0) {
              return;
            }
            selectedEntry = entries[0];
          }
          const popupUrl = "index.php?page=yform/manager/data_edit&table_name=" + encodeURIComponent(selectedEntry.table) + "&rex_yform_manager_opener[id]=1&rex_yform_manager_opener[field]=" + encodeURIComponent(selectedEntry.field) + "&rex_yform_manager_opener[multiple]=0";
          const popup = window.newPoolWindow(popupUrl);
          if (!popup) {
            return;
          }
          window.jQuery(popup).off("rex:YForm_selectData.cke5").on("rex:YForm_selectData.cke5", (event, id, label) => {
            event.preventDefault();
            if (popup && typeof popup.close === "function") {
              popup.close();
            }
            const cleanLabel = typeof label === "string" ? label.replace(new RegExp("(.*?)\\s\\[.*?\\]", "gi"), "$1") : "";
            const linkUrl = selectedEntry.urlPrefix + String(id);
            cke5_apply_link_to_form_or_editor(editor, linkUrl, cleanLabel);
          });
        }
        function cke5_execute_redaxo_link_type(editor, linkConfig, type) {
          if (typeof type === "object" && type !== null && type.kind === "ytable") {
            cke5_open_redaxo_ytable_link(editor, linkConfig, type.entry || null);
            return;
          }
          if (type === "internal") {
            cke5_open_redaxo_internal_link(editor, linkConfig);
            return;
          }
          if (type === "media") {
            cke5_open_redaxo_media_link(editor, linkConfig);
            return;
          }
          if (type === "email") {
            cke5_open_redaxo_prefix_link(editor, "mailto:", "");
            return;
          }
          if (type === "phone") {
            cke5_open_redaxo_prefix_link(editor, "tel:", "+49");
            return;
          }
          if (type === "ytable") {
            cke5_open_redaxo_ytable_link(editor, linkConfig);
            return;
          }
        }
        function cke5_enhance_link_form(editor) {
          if (!editor || editor._cke5RedaxoLinkEnhancerInit) {
            return;
          }
          editor._cke5RedaxoLinkEnhancerInit = true;
          const observer = new MutationObserver(() => {
            const form = document.querySelector(".ck.ck-link-form") || document.querySelector(".ck.ck-link-form_layout-vertical");
            if (!form || form.querySelector(".ck-redaxo-link-buttons")) {
              return;
            }
            const linkConfig = editor.config.get("link") || {};
            const types = Array.isArray(linkConfig.rexlink) && linkConfig.rexlink.length > 0 ? linkConfig.rexlink : ["internal", "media", "email", "phone"];
            const ytableEntries = cke5_get_ytable_entries(linkConfig);
            const row = document.createElement("div");
            row.className = "ck-redaxo-link-buttons";
            types.forEach((type) => {
              if (["internal", "media", "email", "phone", "ytable"].indexOf(type) === -1) {
                return;
              }
              if (type === "ytable") {
                if (ytableEntries.length === 0) {
                  return;
                }
                ytableEntries.forEach((entry) => {
                  const button = document.createElement("button");
                  button.type = "button";
                  button.className = "ck ck-button ck-off ck-button_with-text";
                  button.textContent = entry.title;
                  button.addEventListener("click", (event) => {
                    event.preventDefault();
                    cke5_execute_redaxo_link_type(editor, linkConfig, { kind: "ytable", entry: entry });
                  });
                  row.appendChild(button);
                });
                return;
              }
              const button = document.createElement("button");
              button.type = "button";
              button.className = "ck ck-button ck-off ck-button_with-text";
              button.textContent = type;
              button.addEventListener("click", (event) => {
                event.preventDefault();
                cke5_execute_redaxo_link_type(editor, linkConfig, type);
              });
              row.appendChild(button);
            });
            if (row.childElementCount > 0) {
              const target = form.querySelector(".ck-link-form__provider-list") || form;
              target.insertBefore(row, target.firstChild);
            }
          });
          observer.observe(document.body, { childList: true, subtree: true });
        }

        function cke5_prefer_image_toolbar_over_link_form(editor) {
          if (!editor || editor._cke5PreferImageToolbarInit) {
            return;
          }
          editor._cke5PreferImageToolbarInit = true;

          const imageUtils = editor.plugins && editor.plugins.has("ImageUtils") ? editor.plugins.get("ImageUtils") : null;
          const linkUI = editor.plugins && editor.plugins.has("LinkUI") ? editor.plugins.get("LinkUI") : null;

          if (!linkUI) {
            return;
          }

          const isSelectedImage = () => {
            const selection = editor.model && editor.model.document ? editor.model.document.selection : null;
            if (!selection || typeof selection.getSelectedElement !== "function") {
              return false;
            }

            const selectedElement = selection.getSelectedElement();
            if (!selectedElement) {
              return false;
            }

            if (imageUtils && typeof imageUtils.isImage === "function") {
              return imageUtils.isImage(selectedElement);
            }

            return selectedElement.is && (selectedElement.is("element", "imageBlock") || selectedElement.is("element", "imageInline"));
          };

          const hideLinkUiForImageSelection = () => {
            if (!isSelectedImage()) {
              return;
            }

            if (typeof linkUI._hideUI === "function") {
              linkUI._hideUI();
            }
          };

          if (editor.model && editor.model.document && editor.model.document.selection) {
            editor.model.document.selection.on("change:range", () => {
              window.setTimeout(hideLinkUiForImageSelection, 0);
            });
          }

          // Keep link editing reachable: only suppress the automatic link balloon on
          // selection changes, not on every click afterwards.
        }
        function cke5_register_soft_hyphen_postfixer(editor) {
          if (!editor || editor._cke5SoftHyphenFixerInit) {
            return;
          }
          if (!editor.model || !editor.model.document || typeof editor.model.document.registerPostFixer !== "function") {
            return;
          }

          editor._cke5SoftHyphenFixerInit = true;
          editor.model.document.registerPostFixer((writer) => {
            const root = editor.model.document.getRoot();
            if (!root) {
              return false;
            }

            const toFix = [];
            for (const item of editor.model.createRangeIn(root).getItems()) {
              if (!item || typeof item.is !== "function" || !item.is("$text")) {
                continue;
              }
              if (typeof item.data !== "string" || item.data.indexOf("\u00AD") === -1) {
                continue;
              }
              toFix.push(item);
            }

            if (toFix.length === 0) {
              return false;
            }

            for (let i = toFix.length - 1; i >= 0; i--) {
              const item = toFix[i];
              const clean = item.data.replace(/\u00AD/g, "");
              const attrs = Object.fromEntries(item.getAttributes());
              const pos = writer.createPositionBefore(item);
              writer.remove(item);
              if (clean !== "") {
                writer.insertText(clean, attrs, pos);
              }
            }

            return true;
          });
        }
        window.cke5_enhance_link_form = cke5_enhance_link_form;
        window.cke5_prefer_image_toolbar_over_link_form = cke5_prefer_image_toolbar_over_link_form;
        let cke5RedaxoPluginCache = null;
        function cke5_get_native_redaxo_plugins() {
          if (cke5RedaxoPluginCache !== null) {
            return cke5RedaxoPluginCache;
          }
          if (typeof window.CKEDITOR !== "object" || window.CKEDITOR === null) {
            cke5RedaxoPluginCache = [];
            return cke5RedaxoPluginCache;
          }
          const cke = window.CKEDITOR;
          const registry = typeof window.CKE5_NATIVE_PLUGINS === "object" && window.CKE5_NATIVE_PLUGINS !== null ? window.CKE5_NATIVE_PLUGINS : {};
          const pluginNames = ["RedaxoLinkIntegration", "RedaxoMediaImage", "RedaxoMediaVideo", "RedaxoSnippets", "RedaxoPastePlainTextToggle"];
          const plugins = [];
          pluginNames.forEach((pluginName) => {
            const factory = registry[pluginName];
            if (typeof factory !== "function") {
              console.warn("cke5 native plugin not found:", pluginName);
              return;
            }
            try {
              const plugin = factory({ cke: cke });
              if (typeof plugin === "function") {
                plugins.push(plugin);
              }
            } catch (error) {
              console.error("cke5 native plugin init failed:", pluginName, error);
            }
          });
          cke5RedaxoPluginCache = plugins;
          return cke5RedaxoPluginCache;
        }
        function cke5_apply_native_redaxo_toolbar(options) {
          if (!options || typeof options !== "object") {
            return options;
          }
          if (!options.toolbar || typeof options.toolbar !== "object" || !Array.isArray(options.toolbar.items)) {
            return options;
          }
          options.toolbar.items = options.toolbar.items.map((item) => {
            if (item === "insertTemplate") {
              return "snippets";
            }
            if (item === "rexImage" || item === "insertImage") {
              return "redaxoMedia";
            }
            if (item === "for_toc") {
              return null;
            }
            return item;
          });
          if (options.toolbar.items.includes("insertImage") && !options.toolbar.items.includes("redaxoMedia")) {
            const imageIndex = options.toolbar.items.indexOf("insertImage");
            options.toolbar.items.splice(imageIndex + 1, 0, "redaxoMedia");
          }
          options.toolbar.items = cke5_normalize_toolbar_items(options.toolbar.items);
          return options;
        }
        function cke5_register_native_redaxo_plugins(options) {
          if (!options || typeof options !== "object") {
            return options;
          }
          if (!Array.isArray(options.plugins)) {
            options.plugins = [];
          }
          cke5_get_native_redaxo_plugins().forEach((plugin) => {
            if (!options.plugins.includes(plugin)) {
              options.plugins.push(plugin);
            }
          });
          return options;
        }
        function cke5_apply_resize_handle_mode(options) {
          if (!options || typeof options !== "object") {
            return options;
          }
          if (typeof window.CKEDITOR !== "object" || window.CKEDITOR === null) {
            return options;
          }
          if (!Array.isArray(options.plugins)) {
            options.plugins = [];
          }

          const cke = window.CKEDITOR;
          const handlesDisabled = options.redaxoImageResizeHandles === false;
          const removePluginByName = (name) => {
            const plugin = cke[name];
            if (typeof plugin !== "function") {
              return;
            }
            options.plugins = options.plugins.filter((current) => current !== plugin);
          };
          const addPluginByName = (name) => {
            const plugin = cke[name];
            if (typeof plugin !== "function") {
              return;
            }
            if (!options.plugins.includes(plugin)) {
              options.plugins.push(plugin);
            }
          };

          if (!handlesDisabled) {
            return options;
          }

          // Vendor-konformer Fallback ohne Drag-Handles: Buttons + Commands bleiben aktiv.
          removePluginByName("ImageResize");
          removePluginByName("ImageResizeHandles");
          addPluginByName("ImageResizeEditing");
          addPluginByName("ImageResizeButtons");
          addPluginByName("ImageCustomResizeUI");
          return options;
        }
        function cke5_apply_modern_constructor_fallback(options, editorConstructor) {
          if (!editorConstructor || typeof editorConstructor !== "function") {
            return options;
          }
          if (typeof window.CKEDITOR !== "object" || window.CKEDITOR === null) {
            return options;
          }
          if (editorConstructor !== window.CKEDITOR.ClassicEditor) {
            return options;
          }
          const plugins = cke5_get_modern_default_plugins();
          if (!Array.isArray(options.plugins)) {
            options.plugins = [];
          }
          if (plugins.length > 0) {
            plugins.forEach((plugin) => {
              if (!options.plugins.includes(plugin)) {
                options.plugins.push(plugin);
              }
            });
          }
          if (options.title !== null && typeof options.title === "object" && typeof window.CKEDITOR.Title === "function" && !options.plugins.includes(window.CKEDITOR.Title)) {
            options.plugins.push(window.CKEDITOR.Title);
          }
          options = cke5_register_native_redaxo_plugins(options);
          options = cke5_apply_resize_handle_mode(options);
          options = cke5_apply_native_redaxo_toolbar(options);
          return options;
        }
        function cke5_get_external_registry() {
          if (typeof rex === "undefined" || typeof rex.cke5ExternalPlugins === "undefined") {
            return {};
          }
          if (typeof rex.cke5ExternalPlugins !== "object" || rex.cke5ExternalPlugins === null) {
            return {};
          }
          return rex.cke5ExternalPlugins;
        }
        function cke5_get_enabled_external_plugins(options) {
          const enabled = [];
          if (typeof options !== "object" || options === null) {
            return enabled;
          }
          if (!Object.prototype.hasOwnProperty.call(options, "externalPlugins")) {
          } else if (Array.isArray(options.externalPlugins)) {
            options.externalPlugins.forEach((name) => {
              if (typeof name === "string" && name !== "" && !enabled.includes(name)) {
                enabled.push(name);
              }
            });
          } else if (typeof options.externalPlugins === "string") {
            options.externalPlugins.split(",").map((name) => name.trim()).filter((name) => name !== "").forEach((name) => {
              if (!enabled.includes(name)) {
                enabled.push(name);
              }
            });
          }

          return enabled;
        }
        function cke5_apply_toolbar_aliases(options, aliases) {
          if (!options || typeof options !== "object") {
            return options;
          }
          if (!options.toolbar || typeof options.toolbar !== "object" || !Array.isArray(options.toolbar.items)) {
            return options;
          }
          if (!aliases || typeof aliases !== "object") {
            return options;
          }
          options.toolbar.items = options.toolbar.items.map((item) => {
            if (typeof item !== "string") {
              return item;
            }
            return aliases[item] || item;
          });
          options.toolbar.items = cke5_normalize_toolbar_items(options.toolbar.items);
          return options;
        }
        function cke5_ensure_image_link_toolbar(options) {
          if (!options || typeof options !== "object") {
            return options;
          }
          if (!options.image || typeof options.image !== "object") {
            return options;
          }

          let toolbarItems = [];
          if (Array.isArray(options.image.toolbar)) {
            toolbarItems = options.image.toolbar.slice();
          } else if (typeof options.image.toolbar === "string") {
            toolbarItems = options.image.toolbar.split(",").map((item) => item.trim()).filter((item) => item !== "");
          }

          if (toolbarItems.length === 0) {
            return options;
          }

          if (!toolbarItems.includes("linkImage")) {
            const insertIndex = toolbarItems.includes("toggleImageCaption") ? toolbarItems.indexOf("toggleImageCaption") : toolbarItems.length;
            toolbarItems.splice(insertIndex, 0, "linkImage");
          }

          options.image.toolbar = cke5_normalize_toolbar_items(toolbarItems);
          return options;
        }
        function cke5_normalize_toolbar_items(items) {
          if (!Array.isArray(items)) {
            return [];
          }
          const normalized = [];
          items.forEach((item) => {
            if (typeof item !== "string") {
              return;
            }
            if (item === "|") {
              if (normalized.length === 0 || normalized[normalized.length - 1] === "|") {
                return;
              }
              normalized.push(item);
              return;
            }
            if (normalized[normalized.length - 1] === item) {
              return;
            }
            normalized.push(item);
          });
          while (normalized.length > 0 && normalized[normalized.length - 1] === "|") {
            normalized.pop();
          }
          return normalized;
        }
        function cke5_apply_external_profile_transforms(options, element) {
          const pluginMap = window.CKE5_EXTERNAL_PLUGINS || {};
          const registry = cke5_get_external_registry();
          const enabledPlugins = cke5_get_enabled_external_plugins(options);
          enabledPlugins.forEach((name) => {
            if (typeof name !== "string" || name === "") {
              return;
            }
            const initFn = pluginMap[name];
            const registryEntry = typeof registry[name] === "object" && registry[name] !== null ? registry[name] : {};
            const config = registryEntry.config || {};
            if (config.toolbarAliases && typeof config.toolbarAliases === "object") {
              options = cke5_apply_toolbar_aliases(options, config.toolbarAliases);
            }
            if (typeof initFn === "function" && typeof initFn.transformOptions === "function") {
              try {
                options = initFn.transformOptions({
                  name,
                  options,
                  element: element.get(0),
                  config
                }) || options;
              } catch (error) {
                console.error("cke5 external plugin transform failed:", name, error);
              }
            }
          });
          return options;
        }
        function cke5_init_external_plugins(editor, unique_id, element, options) {
          const pluginMap = window.CKE5_EXTERNAL_PLUGINS || {};
          const registry = cke5_get_external_registry();
          const enabledPlugins = cke5_get_enabled_external_plugins(options);
          enabledPlugins.forEach((name) => {
            if (typeof name !== "string" || name === "") {
              return;
            }
            const initFn = pluginMap[name];
            if (typeof initFn !== "function") {
              console.warn("cke5 external plugin not found:", name);
              return;
            }
            const registryEntry = typeof registry[name] === "object" && registry[name] !== null ? registry[name] : {};
            try {
              initFn({
                name,
                editor,
                editorId: unique_id,
                element: element.get(0),
                profileOptions: options,
                config: registryEntry.config || {}
              });
            } catch (error) {
              console.error("cke5 external plugin init failed:", name, error);
            }
          });
        }
        function cke5_destroy(elements) {
          elements.each(function() {
            let element = $(this), next = element.next();
            delete ckeditors[element.attr("id")];
            element.attr("data-cke5-init-state", "none");
            while (next.length && (next.hasClass("ck-editor") || next.hasClass("ck"))) {
              let current = next;
              next = current.next();
              current.remove();
            }
          });
        }
        function cke5_pastinit(editor, sub_options) {
          editor.editing.view.change((writer) => {
            if ("min-height" in sub_options && sub_options["min-height"] !== "none") {
              writer.setStyle("min-height", sub_options["min-height"] + "px", editor.editing.view.document.getRoot());
            }
            if ("max-height" in sub_options && sub_options["max-height"] !== "none") {
              writer.setStyle("max-height", sub_options["max-height"] + "px", editor.editing.view.document.getRoot());
            }
          });
        }
        function dispatchCke5Event(editor, unique_id) {
          let event = jQuery.Event("rex:cke5IsInit");
          jQuery(window).trigger(event, [editor, unique_id]);
        }
      })();
    })();
  })();
})();
