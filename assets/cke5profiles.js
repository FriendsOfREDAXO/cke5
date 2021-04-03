const cke5profiles = {
    "default": {
        "toolbar": {
            "items": ["heading", "|", "bold", "italic", "underline", "strikethrough", "blockQuote", "|", "bulletedList", "numberedList", "todoList", "|", "outdent", "indent", "alignment", "|", "link", "insertTable", "rexImage", "mediaEmbed", "|", "fontSize", "fontColor", "fontBackgroundColor", "|", "subscript", "superscript", "|", "|", "redo", "undo", "|", "removeFormat", "|", "code", "codeBlock"],
            "shouldNotGroupWhenFull": false
        },
        "removePlugins": ["AutoLink", "FontFamily", "PastePlainText", "Highlight", "SelectAll", "SpecialCharactersCurrency", "SpecialCharactersMathematical", "SpecialCharactersLatin", "SpecialCharactersArrows", "SpecialCharactersText", "SpecialCharactersEssentials", "Emoji", "HorizontalLine", "PageBreak", "PastePlainText"],
        "link": {
            "rexlink": ["internal", "media"],
            "addTargetToExternalLinks": true,
            "decorators": {
                "downloadable": {
                    "mode": "manual",
                    "label": "Downloadable",
                    "attributes": {"download": "download"}
                }
            }
        },
        "image": {
            "resizeOptions": [{
                "name": "imageResize:original",
                "value": "",
                "icon": "original"
            }, {"name": "imageResize:25", "value": "25", "icon": "small"}, {
                "name": "imageResize:50",
                "value": "50",
                "icon": "medium"
            }, {"name": "imageResize:75", "value": "75", "icon": "large"}],
            "toolbar": ["imageTextAlternative", "|", "imageStyle:full", "imageStyle:alignLeft", "imageStyle:alignRight", "imageStyle:alignCenter", "|", "linkImage", "|", "imageResize:original", "imageResize:25", "imageResize:50", "imageResize:75"],
            "styles": ["full", "alignLeft", "alignRight", "alignCenter"]
        },
        "table": {"contentToolbar": ["tableColumn", "tableRow", "mergeTableCells"]},
        "alignment": ["left", "right", "center", "justify"],
        "heading": {
            "options": [{
                "model": "paragraph",
                "title": "Paragraph",
                "class": "ck-heading_paragraph"
            }, {
                "model": "heading1",
                "view": "h1",
                "title": "Heading 1",
                "class": "ck-heading_heading1"
            }, {
                "model": "heading2",
                "view": "h2",
                "title": "Heading 2",
                "class": "ck-heading_heading2"
            }, {
                "model": "heading3",
                "view": "h3",
                "title": "Heading 3",
                "class": "ck-heading_heading3"
            }, {
                "model": "heading4",
                "view": "h4",
                "title": "Heading 4",
                "class": "ck-heading_heading4"
            }, {
                "model": "heading5",
                "view": "h5",
                "title": "Heading 5",
                "class": "ck-heading_heading5"
            }, {"model": "heading6", "view": "h6", "title": "Heading 6", "class": "ck-heading_heading6"}]
        },
        "fontSize": {"options": ["default", "tiny", "small", "big", "huge"]},
        "codeBlock": {
            "languages": [{"language": "css", "label": "CSS", "class": "block_css"}, {
                "language": "html",
                "label": "HTML",
                "class": "block_html"
            }, {"language": "javascript", "label": "JavaScript", "class": "block_java_script"}, {
                "language": "php",
                "label": "PHP",
                "class": "block_php"
            }, {"language": "xml", "label": "XML", "class": "block_xml"}, {
                "language": "typescript",
                "label": "TypeScript",
                "css": "block_type_script"
            }]
        },
        "mediaEmbed": {"removeProviders": ["dailymotion", "spotify", "instagram", "twitter", "googleMaps", "flickr", "facebook"]},
        "rexImage": {"media_path": "\/media\/"},
        "ckfinder": {"uploadUrl": ".\/index.php?cke5upload=1&media_path=media"}
    },
    "light": {
        "toolbar": {
            "items": ["bold", "italic", "bulletedList", "numberedList", "Undo", "Redo"],
            "shouldNotGroupWhenFull": true
        },
        "removePlugins": ["AutoLink", "Alignment", "ListStyle", "Font", "FontFamily", "PastePlainText", "MediaEmbed", "BlockQuote", "Heading", "Alignment", "Highlight", "Strikethrough", "Underline", "Code", "Subscript", "Superscript", "SelectAll", "SpecialCharactersCurrency", "SpecialCharactersMathematical", "SpecialCharactersLatin", "SpecialCharactersArrows", "SpecialCharactersText", "SpecialCharactersEssentials", "CodeBlock", "Emoji", "RemoveFormat", "TodoList", "HorizontalLine", "PageBreak", "PastePlainText"],
        "image": {"resizeUnit": "%"}
    },
    "full_expert": {
        "toolbar": ["heading", "|", "bold", "italic", "underline", "strikethrough", "subscript", "superscript", "|", "alignment", "bulletedList", "numberedList", "todoList", "|", "link", "rexImage", "|", "undo", "redo", "|", "selectAll", "insertTable", "specialCharacters", "removeFormat", "|", "code", "codeBlock", "|", "fontSize", "fontColor", "fontFamily", "fontBackgroundColor", "|", "blockQuote", "|", "outdent", "indent", "|", "highlight", "emoji", "pastePlainText", "|", "horizontalLine", "pageBreak"],
        "removePlugins": ["MediaEmbed"],
        "link": {
            "rexlink": ["internal", "media"],
            "addTargetToExternalLinks": true,
            "decorators": {
                "downloadable": {
                    "mode": "manual",
                    "label": "Downloadable",
                    "attributes": {"download": "download"}
                },
                "openInNewTab": {
                    "mode": "manual",
                    "label": "Open in a new tab",
                    "attributes": {"target": "_blank", "rel": "noopener noreferrer"}
                }
            }
        },
        "image": {
            "toolbar": ["imageTextAlternative", "|", "imageStyle:full", "imageStyle:alignLeft", "imageStyle:alignRight", "imageStyle:alignCenter", "|", "linkImage"],
            "styles": ["full", "alignLeft", "alignRight", "alignCenter"]
        },
        "table": {
            "contentToolbar": ["tableColumn", "tableRow", "mergeTableCells", "tableProperties", "tableCellProperties"],
            "tableProperties": {
                "borderColors": [{
                    "color": "rgb(214, 126, 126)",
                    "label": "red",
                    "hasBorder": "false"
                }, {"color": "rgb(255, 255, 255)", "label": "white", "hasBorder": "true"}, {
                    "color": "rgb(21, 194, 79)",
                    "label": "green",
                    "hasBorder": "false"
                }],
                "backgroundColors": [{
                    "color": "rgb(214, 126, 126)",
                    "label": "red",
                    "hasBorder": "false"
                }, {"color": "rgb(255, 255, 255)", "label": "white", "hasBorder": "true"}, {
                    "color": "rgb(21, 194, 79)",
                    "label": "green",
                    "hasBorder": "false"
                }]
            },
            "tableCellProperties": {
                "borderColors": [{
                    "color": "rgb(214, 126, 126)",
                    "label": "red",
                    "hasBorder": "false"
                }, {"color": "rgb(255, 255, 255)", "label": "white", "hasBorder": "true"}, {
                    "color": "rgb(21, 194, 79)",
                    "label": "green",
                    "hasBorder": "false"
                }],
                "backgroundColors": [{
                    "color": "rgb(214, 126, 126)",
                    "label": "red",
                    "hasBorder": "false"
                }, {"color": "rgb(255, 255, 255)", "label": "white", "hasBorder": "true"}, {
                    "color": "rgb(21, 194, 79)",
                    "label": "green",
                    "hasBorder": "false"
                }]
            }
        },
        "typing": {
            "transformations": {
                "extra": [{"from": ":)", "to": "smile!!"}, {
                    "from": ":+1:",
                    "to": "JE+11++1"
                }, {"from": ":tada:", "to": "tadaaa"}]
            }
        },
        "alignment": ["left", "right", "center", "justify"],
        "heading": {
            "options": [{
                "model": "paragraph",
                "title": "Paragraph",
                "class": "ck-heading_paragraph"
            }, {
                "model": "heading1",
                "view": "h1",
                "title": "Heading 1",
                "class": "ck-heading_heading1"
            }, {
                "model": "heading2",
                "view": "h2",
                "title": "Heading 2",
                "class": "ck-heading_heading2"
            }, {
                "model": "heading3",
                "view": "h3",
                "title": "Heading 3",
                "class": "ck-heading_heading3"
            }, {
                "model": "heading4",
                "view": "h4",
                "title": "Heading 4",
                "class": "ck-heading_heading4"
            }, {
                "model": "heading5",
                "view": "h5",
                "title": "Heading 5",
                "class": "ck-heading_heading5"
            }, {"model": "heading6", "view": "h6", "title": "Heading 6", "class": "ck-heading_heading6"}]
        },
        "highlight": {
            "options": [{
                "model": "yellowMarker",
                "class": "marker-yellow",
                "title": "Yellow Marker",
                "color": "var(--ck-highlight-marker-yellow)",
                "type": "marker"
            }, {
                "model": "greenMarker",
                "class": "marker-green",
                "title": "Green Marker",
                "color": "var(--ck-highlight-marker-green)",
                "type": "marker"
            }, {
                "model": "redPen",
                "class": "pen-red",
                "title": "Red pen",
                "color": "var(--ck-highlight-pen-red)",
                "type": "pen"
            }, {
                "model": "greenPen",
                "class": "pen-green",
                "title": "Green pen",
                "color": "var(--ck-highlight-pen-green)",
                "type": "pen"
            }, {
                "model": "pinkMarker",
                "class": "marker-pink",
                "title": "Pink Marker",
                "color": "var(--ck-highlight-marker-pink)",
                "type": "marker"
            }, {
                "model": "blueMarker",
                "class": "marker-blue",
                "title": "Blue Marker",
                "color": "var(--ck-highlight-marker-blue)",
                "type": "marker"
            }]
        },
        "fontSize": {"options": ["default", "tiny", "small", "big", "huge"]},
        "codeBlock": {
            "languages": [{"language": "c", "label": "C", "class": "block_c"}, {
                "language": "plaintext",
                "label": "Plain Text",
                "class": "block_plain_text"
            }, {"language": "html", "label": "HTML", "class": "block_html"}, {
                "language": "css",
                "label": "CSS",
                "class": "block_css"
            }, {"language": "python", "label": "Python", "class": "block_python"}, {
                "language": "ruby",
                "label": "Ruby",
                "class": "block_ruby"
            }, {"language": "javascript", "label": "JavaScript", "class": "block_java_script"}, {
                "language": "cs",
                "label": "C#",
                "class": "block_cs"
            }, {"language": "cpp", "label": "C++", "class": "block_cpp"}, {
                "language": "diff",
                "label": "Diff",
                "class": "block_diff"
            }, {"language": "java", "label": "Java", "class": "block_java"}, {
                "language": "php",
                "label": "PHP",
                "class": "block_php"
            }, {"language": "typescript", "label": "TypeScript", "css": "block_type_script"}, {
                "language": "xml",
                "label": "XML",
                "class": "block_xml"
            }]
        },
        "rexImage": {"media_path": "\/media\/"},
        "ckfinder": {"uploadUrl": ".\/index.php?cke5upload=1&media_path=media"},
        "placeholder_en": "Placeholder EN",
        "placeholder_de": "Placeholder DE"
    }
};
const cke5suboptions = {"default": [], "light": [], "full_expert": [{"min-height": 100}, {"max-height": 280}]};
