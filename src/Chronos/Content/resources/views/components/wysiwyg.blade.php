<script type="text/x-template" id="wysiwyg-template">
    <div class="wysiwyg-editor">
        <div class="preview" v-on:click="startEdit" v-if="!editing">
            <div class="editor-value" v-html="value != '' ? parsedValue : '<em>{{ trans('chronos.content::forms.Click to edit') }}</em>'"></div>
            <div class="preview-buttons">
                <a class="btn-edit"><span class="icon c4icon-pencil-3"></span></a>
            </div>
        </div>
        <div class="editor" v-show="editing">
            <div class="editor-toolbar">
                <a class="editor-btn btn-strong" v-on:click="toggleStrong" data-toggle="tooltip" title="Bold">Bold</a>
                <a class="editor-btn btn-em" v-on:click="toggleEm">Italic</a>
                <div class="editor-dropdown" v-bind:class="{ open: dropdownOpen }" v-click-outside="closeOtherStylesDropdown">
                    <a v-on:click="toggleOtherStylesDropdown">Other styles <span class="caret"></span></a>
                    <ul class="editor-dropdown-menu">
                        <li><a v-on:click="toggleIns">Underline</a></li>
                        <li><a v-on:click="toggleHeading">Heading</a></li>
                        <li><a v-on:click="toggleDel">Strikethrough</a></li>
                        <li><a v-on:click="toggleSup">Superscript</a></li>
                        <li><a v-on:click="toggleSub">Subscript</a></li>
                        <li><a v-on:click="toggleCode">Monospace</a></li>
                        <li><a v-on:click="toggleQuote">Quote</a></li>
                    </ul>
                </div>
                <span class="editor-separator"></span>
                <a class="editor-btn btn-ul" v-on:click="toggleUl">Unordered list</a>
                <a class="editor-btn btn-olno" v-on:click="toggleOlNo">Ordered list number</a>
                <a class="editor-btn btn-olchar" v-on:click="toggleOlChar">Ordered list char</a>
                <a class="editor-btn btn-table" v-on:click="insertTable">Table</a>
                <span class="editor-separator"></span>
                <a class="editor-btn btn-align-left" v-on:click="toggleAlignLeft">Align left</a>
                <a class="editor-btn btn-align-center" v-on:click="toggleAlignCenter">Align center</a>
                <a class="editor-btn btn-align-right" v-on:click="toggleAlignRight">Align right</a>
                <a class="editor-btn btn-align-justify" v-on:click="toggleAlignJustify">Align justify</a>
                <span class="editor-separator"></span>
                <a class="editor-btn btn-link" v-on:click="insertLink">Link</a>
                <a class="editor-btn btn-media" v-on:click="openMediaDialog">Media</a>
                <a class="editor-btn btn-gallery" v-on:click="insertGallery">Gallery</a>
                <a class="editor-btn btn-youtube" v-on:click="insertYoutube">Youtube</a>
            </div>
            <textarea v-bind:class="inputClass" v-bind:id="id" v-bind:name="name" v-bind:rows="rows" v-model="value" />
            <div class="editor-buttons">
                <a class="btn-help" href="#" target="_blank">?</a>
                <a class="btn-save" v-on:click.stop="stopEdit"><span class="icon c4icon-check-2"></span></a>
            </div>
        </div>
    </div>
</script>


<script>


    Vue.component('wysiwyg', {
        computed: {
            parsedValue: function() {
                var parsedValue = this.normalizeNewline(this.value);

                // wrap in paragraph
                parsedValue = '<p>' + parsedValue + '</p>';

                // handle lists
                parsedValue = parsedValue.replace(/^\* (.*)<\/p>/gm, "<liul>$1</liul></p>");
                parsedValue = parsedValue.replace(/^\* (.*)(\n|$)/gm, "<liul>$1</liul>");
                parsedValue = parsedValue.replace(/<p>\* (.*)(\n|$)/gm, "<p><liul>$1</liul>");
                parsedValue = parsedValue.replace(/(<liul>.*<\/liul>)(\n)*/g, "</p><ul>$1</ul><p>");
                parsedValue = parsedValue.replace(new RegExp("<liul>", 'g'), "<li>");
                parsedValue = parsedValue.replace(new RegExp("</liul>", 'g'), "</li>");

                // handle order lists with chars
                parsedValue = parsedValue.replace(/^@ (.*)<\/p>/gm, "<liolchar>$1</liolchar></p>");
                parsedValue = parsedValue.replace(/^@ (.*)(\n|$)/gm, "<liolchar>$1</liolchar>");
                parsedValue = parsedValue.replace(/<p>@ (.*)(\n|$)/gm, "<p><liolchar>$1</liolchar>");
                parsedValue = parsedValue.replace(/(<liolchar>.*<\/liolchar>)(\n)*/g, "</p><ol class=\"char\">$1</ol><p>");
                parsedValue = parsedValue.replace(new RegExp("<liolchar>", 'g'), "<li>");
                parsedValue = parsedValue.replace(new RegExp("</liolchar>", 'g'), "</li>");

                // handle order lists with numbers
                parsedValue = parsedValue.replace(/^# (.*)<\/p>/gm, "<liolno>$1</liolno></p>");
                parsedValue = parsedValue.replace(/^# (.*)(\n|$)/gm, "<liolno>$1</liolno>");
                parsedValue = parsedValue.replace(/<p># (.*)(\n|$)/gm, "<p><liolno>$1</liolno>");
                parsedValue = parsedValue.replace(/(<liolno>.*<\/liolno>)(\n)*/g, "</p><ol>$1</ol><p>");
                parsedValue = parsedValue.replace(new RegExp("<liolno>", 'g'), "<li>");
                parsedValue = parsedValue.replace(new RegExp("</liolno>", 'g'), "</li>");

                // handle tables
                var rows = parsedValue.match(/\|(.*)\|(\n|$)/g);
                if (rows) {
                    rows.forEach(function(row) {
                        var replace = row.replace('\n', '');
                        if (row.match(/\|\|(.+)\|\|/)) {
                            replace = replace.replace(/^\|\|/g, '');
                            replace = replace.replace(/\|\|$/g, '');
                            replace = '<th>' + replace + '</th>';
                            replace = replace.replace(/\|\|/g, '</th><th>');
                        } else {
                            replace = replace.replace(/^\|/g, '');
                            replace = replace.replace(/\|$/g, '');
                            replace = '<td>' + replace + '</td>';
                            replace = replace.replace(/\|/g, '</td><td>');
                        }
                        replace = '<tr>' + replace + '</tr>';
                        parsedValue = parsedValue.replace(row, replace);
                    });
                    parsedValue = parsedValue.replace(/(<tr>.*<\/tr>)(\n)*/g, "</p><table class=\"table\">$1</table><p>");
                }

                // handle code
                parsedValue = parsedValue.replace(/(^|\s|[`\-_\+\*~\^>])`([^\s](?:.*?)[^\s])`(\s|[`\-_\+\*~\^<]|$)/g, "$1<code>$2</code>$3");

                // handle del
                parsedValue = parsedValue.replace(/(^|\s|[`\-_\+\*~\^>])-([^\s](?:.*?)[^\s])-(\s|[`\-_\+\*~\^<]|$)/g, "$1<del>$2</del>$3");

                // handle em
                parsedValue = parsedValue.replace(/(^|\s|[`\-_\+\*~\^>])_([^\s](?:.*?)[^\s])_(\s|[`\-_\+\*~\^<]|$)/g, "$1<em>$2</em>$3");

                // handle headings
                parsedValue = parsedValue.replace(/^H\. (.*)<\/p>/gm, "<h3>$1</h3></p>");
                parsedValue = parsedValue.replace(/^H\. (.*)(\n|$)/gm, "<h3>$1</h3>");
                parsedValue = parsedValue.replace(/<p>H\. (.*)(\n|$)/gm, "<p><h3>$1</h3>");

                // handle ins
                parsedValue = parsedValue.replace(/(^|\s|[`\-_\+\*~\^>])\+([^\s](?:.*?)[^\s])\+(\s|[`\-_\+\*~\^<]|$)/g, "$1<ins>$2</ins>$3");

                // handle strong
                parsedValue = parsedValue.replace(/(^|\s|[`\-_\+\*~\^>])\*([^\s](?:.*?)[^\s])\*(\s|[`\-_\+\*~\^<]|$)/g, "$1<strong>$2</strong>$3");

                // handle sub
                parsedValue = parsedValue.replace(/(^|\s|[`\-_\+\*~\^>])~([^\s](?:.*?)[^\s])~(\s|[`\-_\+\*~\^<]|$)/g, "$1<sub>$2</sub>$3");

                // handle sup
                parsedValue = parsedValue.replace(/(^|\s|[`\-_\+\*~\^>])\^([^\s](?:.*?)[^\s])\^(\s|[`\-_\+\*~\^<]|$)/g, "$1<sup>$2</sup>$3");

                // change double newlines in paragraphs
                parsedValue = parsedValue.replace(/(^|[^\n])\n{2}(?!\n)/g, '$1</p><p>');
                // change single newlines in line breaks
                parsedValue = parsedValue.replace(/(^|[^\n])\n(?!\n)/g, '$1<br />');

                // handle alignments
                parsedValue = parsedValue.replace(/<p>{left}(.*){left}<\/p>/g, "<p style=\"text-align: left;\">$1</p>");
                parsedValue = parsedValue.replace(/<br \/>{left}(.*){left}<\/p>/g, "</p><p style=\"text-align: left;\">$1</p>");
                parsedValue = parsedValue.replace(/<p>{left}(.*){left}<br \/>/g, "<p style=\"text-align: left;\">$1</p><p>");
                parsedValue = parsedValue.replace(/<br \/>{left}(.*){left}<br \/>/g, "</p><p style=\"text-align: left;\">$1</p><p>");
                parsedValue = parsedValue.replace(/<p>{center}(.*){center}<\/p>/g, "<p style=\"text-align: center;\">$1</p>");
                parsedValue = parsedValue.replace(/<br \/>{center}(.*){center}<\/p>/g, "</p><p style=\"text-align: center;\">$1</p>");
                parsedValue = parsedValue.replace(/<p>{center}(.*){center}<br \/>/g, "<p style=\"text-align: center;\">$1</p><p>");
                parsedValue = parsedValue.replace(/<br \/>{center}(.*){center}<br \/>/g, "</p><p style=\"text-align: center;\">$1</p><p>");
                parsedValue = parsedValue.replace(/<p>{right}(.*){right}<\/p>/g, "<p style=\"text-align: right;\">$1</p>");
                parsedValue = parsedValue.replace(/<br \/>{right}(.*){right}<\/p>/g, "</p><p style=\"text-align: right;\">$1</p>");
                parsedValue = parsedValue.replace(/<p>{right}(.*){right}<br \/>/g, "<p style=\"text-align: right;\">$1</p><p>");
                parsedValue = parsedValue.replace(/<br \/>{right}(.*){right}<br \/>/g, "</p><p style=\"text-align: right;\">$1</p><p>");
                parsedValue = parsedValue.replace(/<p>{justify}(.*){justify}<\/p>/g, "<p style=\"text-align: justify;\">$1</p>");
                parsedValue = parsedValue.replace(/<br \/>{justify}(.*){justify}<\/p>/g, "</p><p style=\"text-align: justify;\">$1</p>");
                parsedValue = parsedValue.replace(/<p>{justify}(.*){justify}<br \/>/g, "<p style=\"text-align: justify;\">$1</p><p>");
                parsedValue = parsedValue.replace(/<br \/>{justify}(.*){justify}<br \/>/g, "</p><p style=\"text-align: justify;\">$1</p><p>");

                // handle quotes
                parsedValue = parsedValue.replace(/<p>{quote}(.*){quote}<\/p>/g, "<blockquote>$1</blockquote>");
                parsedValue = parsedValue.replace(/<br \/>{quote}(.*){quote}<\/p>/g, "</p><blockquote>$1</blockquote>");
                parsedValue = parsedValue.replace(/<p>{quote}(.*){quote}<br \/>/g, "<blockquote>$1</blockquote><p>");
                parsedValue = parsedValue.replace(/<br \/>{quote}(.*){quote}<br \/>/g, "</p><blockquote>$1</blockquote><p>");

                // remove empty paragraphs
                parsedValue = parsedValue.replace(new RegExp("<p><br /></p>", 'g'), "");
                parsedValue = parsedValue.replace(new RegExp("<p></p>", 'g'), "");

                return parsedValue;
            }
        },
        created: function() {
            if (this.defaultValue)
                this.value = this.defaultValue;

            // add listeners
            mediaEventHub.$on('select-from-media-dialog', this.insertMedia);
        },
        data: function() {
            return {
                dropdownOpen: false,
                editing: false,
                textarea: null,
                value: ''
            }
        },
        methods: {
            closeOtherStylesDropdown: function() {
                this.dropdownOpen = false;
            },
            getCursorPosition: function() {
                return this.textarea.selectionStart;
            },
            getParagraph: function() {
                var cursorPosition = this.getCursorPosition();

                var textarea = this.normalizeNewline(this.value);

                var start = textarea.lastIndexOf("\n", cursorPosition - 1);
                start = start == -1 ? 0 : start + 1;

                var end = textarea.indexOf("\n", cursorPosition);
                if (end == -1)
                    end = textarea.length;

                return {
                    start: start,
                    end: end
                }
            },
            getSelection: function() {
                return {
                    start: this.textarea.selectionStart,
                    end: this.textarea.selectionEnd
                }
            },
            insert: function(insertText) {
                var cursorPosition = this.getCursorPosition();

                this.value = this.value.slice(0, cursorPosition) + insertText + this.value.slice(cursorPosition);

                this.selectRange(cursorPosition.start, cursorPosition.end);
            },
            insertGallery: function() {
                var shortcode = '[gallery id=""]';

                this.insert(shortcode);
            },
            insertLink: function() {
                var shortcode = '[link text="" url=""]';

                this.insert(shortcode);
            },
            insertMedia: function(file, name) {
                if (this.name != name)
                    return;

                var shortcode = file && file.id ? '[media id="' + file.id + '"]' : '[media id=""]';

                this.insert(shortcode);
            },
            insertTable: function() {
                var shortcode = '||heading 1||heading 2||heading 3||\n|col A1|col A2|col A3|\n|col B1|col B2|col B3|';

                this.insert("\n" + shortcode + "\n");
            },
            insertYoutube: function() {
                var shortcode = '[youtube vid=""]';

                this.insert(shortcode);
            },
            normalizeNewline: function(text) {
                text = text.replace(/\r\n/g, "\n"); // Win to *nix
                text = text.replace(/\r/g, "\n"); // OSX to *nix

                return text;
            },
            openMediaDialog: function() {
                mediaEventHub.$emit('open-media-dialog', true, this.name);
            },
            paragraphPreceeded: function(wrapString) {
                var selection = this.getParagraph();

                return this.value.slice(selection.start, selection.start + wrapString.length) == wrapString;
            },
            paragraphSucceeded: function(wrapString) {
                var selection = this.getParagraph();

                return this.value.slice(selection.end - wrapString.length, selection.end) == wrapString;
            },
            prependParagraph: function(wrapString) {
                var selection = this.getParagraph();

                this.value = this.value.slice(0, selection.start) + wrapString + this.value.slice(selection.start);

                this.selectRange(selection.start, selection.end);
            },
            selectionPreceeded: function(wrapString) {
                var selection = this.getSelection();

                return this.value.slice(selection.start - wrapString.length, selection.start) == wrapString;
            },
            selectionSucceeded: function(wrapString) {
                var selection = this.getSelection();

                return this.value.slice(selection.end, selection.end + wrapString.length) == wrapString;
            },
            paragraphWrapped: function(wrapString) {
                return this.paragraphPreceeded(wrapString) && this.paragraphSucceeded(wrapString);
            },
            selectionWrapped: function(wrapString) {
                return this.selectionPreceeded(wrapString) && this.selectionSucceeded(wrapString);
            },
            selectRange: function(start, end) {
                if (this.textarea.setSelectionRange) {
                    this.textarea.focus();
                    this.textarea.setSelectionRange(start, end);
                } else if (this.textarea.createTextRange) {
                    var range = this.textarea.createTextRange();
                    range.collapse(true);
                    range.moveEnd('character', end);
                    range.moveStart('character', start);
                    range.select();
                }
            },
            startEdit: function() {
                this.editing = true;
            },
            stopEdit: function() {
                this.editing = false;
            },
            toggleAlignCenter: function() {
                var wrapString = '{center}';

                if (!this.paragraphWrapped(wrapString))
                    this.wrapParagraph(wrapString);
                else
                    this.unwrapParagraph(wrapString);
            },
            toggleAlignJustify: function() {
                var wrapString = '{justify}';

                if (!this.paragraphWrapped(wrapString))
                    this.wrapParagraph(wrapString);
                else
                    this.unwrapParagraph(wrapString);
            },
            toggleAlignLeft: function() {
                var wrapString = '{left}';

                if (!this.paragraphWrapped(wrapString))
                    this.wrapParagraph(wrapString);
                else
                    this.unwrapParagraph(wrapString);
            },
            toggleAlignRight: function() {
                var wrapString = '{right}';

                if (!this.paragraphWrapped(wrapString))
                    this.wrapParagraph(wrapString);
                else
                    this.unwrapParagraph(wrapString);
            },
            toggleCode: function() {
                var wrapChar = '`';

                if (!this.selectionWrapped(wrapChar))
                    this.wrapSelection(wrapChar);
                else
                    this.unwrapSelection(wrapChar);

                this.closeOtherStylesDropdown();
            },
            toggleDel: function() {
                var wrapChar = '-';

                if (!this.selectionWrapped(wrapChar))
                    this.wrapSelection(wrapChar);
                else
                    this.unwrapSelection(wrapChar);

                this.closeOtherStylesDropdown();
            },
            toggleEm: function() {
                var wrapChar = '_';

                if (!this.selectionWrapped(wrapChar))
                    this.wrapSelection(wrapChar);
                else
                    this.unwrapSelection(wrapChar);
            },
            toggleHeading: function() {
                var wrapString = 'H. ';

                if (!this.paragraphPreceeded(wrapString))
                    this.prependParagraph(wrapString);
                else
                    this.unprependParagraph(wrapString);
            },
            toggleIns: function() {
                var wrapChar = '+';

                if (!this.selectionWrapped(wrapChar))
                    this.wrapSelection(wrapChar);
                else
                    this.unwrapSelection(wrapChar);

                this.closeOtherStylesDropdown();
            },
            toggleOlChar: function() {
                var wrapString = '@ ';

                if (!this.paragraphPreceeded(wrapString))
                    this.prependParagraph(wrapString);
                else
                    this.unprependParagraph(wrapString);
            },
            toggleOlNo: function() {
                var wrapString = '# ';

                if (!this.paragraphPreceeded(wrapString))
                    this.prependParagraph(wrapString);
                else
                    this.unprependParagraph(wrapString);
            },
            toggleOtherStylesDropdown: function() {
                this.dropdownOpen = !this.dropdownOpen;
            },
            toggleQuote: function() {
                var wrapString = '{quote}';

                if (!this.paragraphPreceeded(wrapString))
                    this.wrapParagraph(wrapString);
                else
                    this.unwrapParagraph(wrapString);

                this.closeOtherStylesDropdown();
            },
            toggleStrong: function() {
                var wrapChar = '*';

                if (!this.selectionWrapped(wrapChar))
                    this.wrapSelection(wrapChar);
                else
                    this.unwrapSelection(wrapChar);
            },
            toggleSub: function() {
                var wrapChar = '~';

                if (!this.selectionWrapped(wrapChar))
                    this.wrapSelection(wrapChar);
                else
                    this.unwrapSelection(wrapChar);

                this.closeOtherStylesDropdown();
            },
            toggleSup: function() {
                var wrapChar = '^';

                if (!this.selectionWrapped(wrapChar))
                    this.wrapSelection(wrapChar);
                else
                    this.unwrapSelection(wrapChar);

                this.closeOtherStylesDropdown();
            },
            toggleUl: function() {
                var wrapString = '* ';

                if (!this.paragraphPreceeded(wrapString))
                    this.prependParagraph(wrapString);
                else
                    this.unprependParagraph(wrapString);
            },
            unprependParagraph: function(wrapString) {
                var selection = this.getParagraph();

                this.value = this.value.slice(0, selection.start) + this.value.slice(selection.start + wrapString.length);

                this.selectRange(selection.start, selection.end);
            },
            unwrapParagraph: function(wrapString) {
                var selection = this.getParagraph();

                this.value = this.value.slice(0, selection.start) + this.value.slice(selection.start + wrapString.length, selection.end - wrapString.length) + this.value.slice(selection.end);

                this.selectRange(selection.start, selection.end);
            },
            unwrapSelection: function(wrapString) {
                var selection = this.getSelection();

                this.value = this.value.slice(0, Math.max(selection.start - wrapString.length, 0)) + this.value.slice(selection.start, selection.end) + this.value.slice(selection.end + wrapString.length);

                this.selectRange(selection.start, selection.end);
            },
            wrapParagraph: function(wrapString) {
                var selection = this.getParagraph();

                this.value = this.value.slice(0, selection.start) + wrapString + this.value.slice(selection.start, selection.end) + wrapString + this.value.slice(selection.end);

                this.selectRange(selection.start, selection.end);
            },
            wrapSelection: function(wrapString) {
                var selection = this.getSelection();

                this.value = this.value.slice(0, selection.start) + wrapString + this.value.slice(selection.start, selection.end) + wrapString + this.value.slice(selection.end);

                this.selectRange(selection.start, selection.end);
            }
        },
        mounted: function() {
            this.textarea = document.querySelector('textarea[name="' + this.name + '"]');
        },
        props: {
            defaultValue: {
                default: null
            },
            id: {
                default: '',
                type: String
            },
            inputClass: {
                default: '',
                type: String
            },
            name: {
                required: true,
                type: String
            },
            rows: {
                default: 10,
                type: Number
            }
        },
        template: '#wysiwyg-template',
        watch: {
            defaultValue: function() {
                this.value = this.defaultValue;
            }
        }
    });
</script>